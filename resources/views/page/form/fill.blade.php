@extends('layouts.master')

@section('title') Fill Form @endsection

@section('content')
  <div class="row">
      <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
              <h4 class="mb-0 mt-4">{{ $form->name }}</h4>
              <div class="page-title-right mt-4">
                  <a href="{{ route('form.entry') }}" class="btn btn-secondary">
                      <i class="mdi mdi-arrow-left me-1"></i> Back to Start
                  </a>
              </div>
          </div>
      </div>
  </div>

  @if($errors->any())
      <div class="alert alert-danger">
          <ul class="mb-0">
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif

  <div class="row">
      <div class="col-12">
          <div class="card">
              <div class="card-body p-3 p-md-4">
                  <p class="text-muted mb-4">{{ $form->description }}</p>

                  @if(!empty($deferredIds))
                      <div class="alert alert-info py-2 px-3 small">
                          <i class="mdi mdi-account-multiple-outline me-1"></i>
                          Some sections of this form are completed by other participants after you submit.
                      </div>
                  @endif

                  {{-- novalidate: hidden conditional/deferred fields carry "required";
                       native validation would silently block submit. JS validates instead. --}}
                  <form id="form-submission" action="{{ route('form.submit') }}" method="POST"
                        enctype="multipart/form-data" novalidate>
                      @csrf
                      <input type="hidden" name="form_id" value="{{ $form->id }}">
                      <input type="hidden" name="form_data" id="form_data_json">

                      @include('page.form.partials._record-header', [
                          'parentOptions' => $parentOptions,
                          'parentCase'    => $parentCase,
                          'recordTitle'   => null,
                      ])

                      @include('page.form.partials._form-render', [
                          'tree'       => $tree,
                          'answers'    => $answers,
                          'visibility' => $visibility,
                          'form'       => $form,
                          'disabled'   => false,
                      ])

                      <div class="text-center mt-4">
                          <button type="button" onclick="window.history.back()" class="btn btn-secondary me-2 mb-2 mb-sm-0">Cancel</button>
                          <button type="submit" class="btn btn-primary mb-2 mb-sm-0" id="submit-btn">Submit Form</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>

  </div>

  @include('page.form.partials._assignee-picker')
@endsection

@section('script')
<script src="{{ asset('js/forms/form-conditions.js') }}"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $(document).ready(function() {
        const formSchema = @json($schema);
        const deferredIds = @json($deferredIds ?? []);
        const fileUrls = {};

        @if(!empty($answers))
            // Clone prefill: carry over already-uploaded file URLs.
            const prefillAnswers = @json($answers);
            formSchema.elements.forEach(function (el) {
                if (el.type === 'file' && prefillAnswers[el.id]) fileUrls[el.id] = prefillAnswers[el.id];
            });
        @endif

        /* ---------- live answers + conditional visibility ---------- */

        function collectAnswers() {
            const answers = {};
            formSchema.elements.forEach(function (el) {
                if (el.kind !== 'field') return;
                answers[el.id] = readValue(el);
            });
            return answers;
        }

        function readValue(el) {
            switch (el.type) {
                case 'multi-choice': {
                    const vals = [];
                    $(`.multi-choice-input[data-parent="${el.id}"]:checked`).each(function () { vals.push($(this).val()); });
                    return vals;
                }
                case 'multi-select': {
                    const vals = [];
                    $(`.multi-select-input[data-parent="${el.id}"]:checked`).each(function () { vals.push($(this).val()); });
                    return vals;
                }
                case 'checkbox': {
                    const vals = [];
                    $(`.checkbox-input[data-parent="${el.id}"]:checked`).each(function () { vals.push($(this).val()); });
                    return vals;
                }
                case 'file': {
                    const $input = $(document).find(`.file-input[data-el-id="${el.id}"]`);
                    const picked = ($input[0] && $input[0].files) ? $input[0].files.length : 0;
                    if (picked > 0) return '__files_attached__';
                    // Untouched: send back what is already there, or the answer
                    // would be blanked on save.
                    const current = $input.attr('data-current');
                    return current ? JSON.parse(current) : null;
                }
                case 'gps': {
                    // {lat, lng, accuracy, captured_at}, written by form-gps.js.
                    const raw = $(`.gps-input[data-el-id="${el.id}"]`).val();
                    try { return raw ? JSON.parse(raw) : null; } catch (e) { return null; }
                }
                default:
                    return $(`.form-input[data-el-id="${el.id}"]`).val() ?? null;
            }
        }

        function applyVisibility() {
            const visibility = FormConditions.resolveVisibility(formSchema, collectAnswers());
            // Fields owned by later fill phases are never shown to the submitter.
            deferredIds.forEach(function (id) { visibility[id] = false; });
            formSchema.elements.forEach(function (el) {
                $(`.form-element-wrapper[data-el-id="${el.id}"]`).toggleClass('d-none', visibility[el.id] === false);
            });
            // Hide group wrappers whose members are all hidden (or that fail their own condition).
            (formSchema.groups || []).forEach(function (group) {
                const members = formSchema.elements.filter(function (el) { return el.group_id === group.id; });
                const anyVisible = members.some(function (el) { return visibility[el.id] !== false; });
                $(`.form-group-wrapper[data-group-id="${group.id}"]`).toggleClass('d-none', members.length > 0 && !anyVisible);
            });
            return visibility;
        }

        $(document).on('change input', '.form-input, .multi-choice-input, .multi-select-input, .checkbox-input, .file-input, .gps-input', function () {
            applyVisibility();
        });
        applyVisibility();

        /* ---------- attachments ---------- */
        // Files post with the form as files[el_id][], the way the IFE and Task
        // pages do it. Nothing leaves the browser until submit, so this only
        // reports what is queued and flags anything over the size limit.
        $(document).on('change', '.file-input', function () {
            const elId  = $(this).data('el-id');
            const files = Array.from(this.files || []);
            const $out  = $(`#upload_status_${elId}`);

            $(`#error_${elId}`).text('').removeClass('d-block');

            if (!files.length) { $out.html(''); applyVisibility(); return; }

            const tooBig = files.filter(f => f.size > 10240 * 1024);
            const list   = files.map(f =>
                `<span class="badge rounded-pill px-3 py-2 me-1 mb-1" style="background:#e8eeff;color:#3a5bd9;font-size:0.8rem;">`
                + `<i class="mdi mdi-paperclip me-1"></i>${$('<div>').text(f.name).html()}</span>`).join('');

            $out.html(`<div class="mt-1">${list}</div>`
                + (tooBig.length
                    ? `<div class="text-danger small mt-1"><i class="mdi mdi-alert-circle me-1"></i>`
                      + `${tooBig.length} file(s) are over the 10 MB limit and will be rejected.</div>`
                    : ''));

            applyVisibility();
        });

        /* ---------- submit ---------- */

        $('#form-submission').submit(function(e) {
            e.preventDefault();

            $('.invalid-feedback').removeClass('d-block').text('');
            $('.border').removeClass('border-danger');

            const visibility = applyVisibility();
            const formData = [];
            let isValid = true;
            let firstErrorId = null;

            formSchema.elements.forEach(function (el) {
                if (el.kind !== 'field') return;
                const visible = visibility[el.id] !== false;
                const value = visible ? readValue(el) : null;

                if (visible) {
                    if ((el.mandatory ?? false) && (value === null || value === '' || (Array.isArray(value) && value.length === 0))) {
                        $(`#error_${el.id}`).text('This field is required.').addClass('d-block');
                        if (firstErrorId === null) firstErrorId = el.id;
                        isValid = false;
                    }
                    if (['multi-choice', 'multi-select'].includes(el.type)) {
                        const min = parseInt(el.min) || 0;
                        const max = parseInt(el.max) || 0;
                        const count = Array.isArray(value) ? value.length : 0;
                        const $cont = $(`[data-el-id="${el.id}"][data-type="${el.type}"]`);
                        if (min && count < min) {
                            $(`#error_${el.id}`).text(`Please select at least ${min} option(s)`).addClass('d-block');
                            $cont.addClass('border-danger');
                            if (firstErrorId === null) firstErrorId = el.id;
                            isValid = false;
                        }
                        if (max && count > max) {
                            $(`#error_${el.id}`).text(`Please select no more than ${max} option(s)`).addClass('d-block');
                            $cont.addClass('border-danger');
                            if (firstErrorId === null) firstErrorId = el.id;
                            isValid = false;
                        }
                    }
                }

                formData.push({ id: el.id, type: el.type, label: el.label, value: value, hidden: !visible });
            });

            if (!isValid) {
                if (firstErrorId !== null) {
                    $('html, body').animate({ scrollTop: $(`#error_${firstErrorId}`).offset().top - 100 }, 300);
                }
                return;
            }

            $('#form_data_json').val(JSON.stringify(formData));
            this.submit();
        });
    });
</script>
@endsection
