@extends('layouts.master')

@section('title') Available Forms @endsection

@section('content')
  <div class="row">
      <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
              <h4 class="mb-0 mt-4">Available Forms</h4>
              <div class="page-title-right mt-4">
                  <a href="{{ route('form.records.index') }}" class="btn btn-outline-primary">
                      <i class="mdi mdi-clipboard-list-outline me-1"></i>My Records
                  </a>
              </div>
          </div>
      </div>
  </div>

  <div class="row mb-3">
      <div class="col-md-5">
          <div class="input-group">
              <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
              <input type="text" class="form-control" id="search-forms" placeholder="Search forms...">
          </div>
          <small class="text-muted ms-1 mt-1 d-block" id="search-results-count"></small>
      </div>
  </div>

  <div id="forms-container">
      {{-- Grouped when an admin has created groups; a single unnamed section
           otherwise, so a small deployment sees exactly what it saw before. --}}
      @forelse($sections as $section)
          <div class="form-section mb-2" data-section="{{ $section['name'] ?? '' }}">
              @if($section['name'])
                  <div class="d-flex align-items-center gap-2 mb-2 mt-1">
                      <h6 class="mb-0 fw-bold text-muted text-uppercase" style="font-size:11.5px; letter-spacing:.08em;">
                          {{ $section['name'] }}
                      </h6>
                      <div class="flex-grow-1" style="height:1px; background:#e3e8f0;"></div>
                      <small class="text-muted section-count" style="font-size:11.5px;">{{ $section['forms']->count() }}</small>
                  </div>
              @endif
              <div class="row">
                  @foreach($section['forms'] as $form)
                      <div class="col-md-4 form-card mb-4"
                           data-name="{{ strtolower($form->name) }}"
                           data-description="{{ strtolower($form->description) }}">
                          <div class="card h-100 mb-0" style="box-shadow: 1px 1px 3px 1px rgb(183,183,183);">
                              <div class="card-body d-flex flex-column">
                                  <h6 class="fw-bold mb-1">{{ $form->name }}</h6>
                                  <p class="text-muted mb-3 flex-grow-1" style="font-size: 0.82rem;">{{ Str::limit($form->description, 80) }}</p>

                                  <div class="d-flex justify-content-between align-items-center mt-auto">
                                      <small class="text-muted"><i class="mdi mdi-format-list-bulleted"></i> {{ count($form->form_elements['elements'] ?? $form->form_elements ?? []) }} field(s)</small>
                                      <a href="{{ route('form.fill', $form->id) }}" class="btn btn-primary btn-sm">
                                          Start
                                      </a>
                                  </div>
                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>
          </div>
      @empty
          <div id="no-forms-message">
              <div class="card" style="box-shadow: 1px 1px 3px 1px rgb(183,183,183);">
                  <div class="card-body text-center py-5">
                      <i class="mdi mdi-file-document-outline" style="font-size: 64px; color: #ccc;"></i>
                      <h5 class="mt-3 text-muted">No Forms Available</h5>
                      <p class="text-muted mb-0">There are no forms you can submit right now.</p>
                  </div>
              </div>
          </div>
      @endforelse

      <div id="no-results-message" style="display: none;">
          <div class="card" style="box-shadow: 1px 1px 3px 1px rgb(183,183,183);">
              <div class="card-body text-center py-5">
                  <i class="mdi mdi-magnify" style="font-size: 64px; color: #ccc;"></i>
                  <h5 class="mt-3 text-muted">No Forms Found</h5>
                  <p class="text-muted mb-0">No forms match your search criteria.</p>
              </div>
          </div>
      </div>
  </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        const totalForms = $('.form-card').length;

        updateResultsCount(totalForms);

        $('#search-forms').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase().trim();
            let visibleCount = 0;

            if (searchTerm === '') {
                $('.form-card').show();
                $('.form-section').show();
                $('#no-results-message').hide();
                visibleCount = totalForms;
                $('.form-section').each(function () { syncSectionCount($(this)); });
            } else {
                $('.form-card').each(function() {
                    const name = $(this).data('name');
                    const description = $(this).data('description');

                    if (name.includes(searchTerm) || description.includes(searchTerm)) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                // Search reaches across every group; a group heading with no
                // surviving cards is noise, so it goes with them.
                $('.form-section').each(function () {
                    const $section = $(this);
                    const shown = $section.find('.form-card:visible').length;
                    $section.toggle(shown > 0);
                    syncSectionCount($section, shown);
                });

                $('#no-results-message').toggle(visibleCount === 0);
            }

            updateResultsCount(visibleCount);
        });

        function syncSectionCount($section, shown) {
            if (shown === undefined) { shown = $section.find('.form-card').length; }
            $section.find('.section-count').text(shown);
        }

        function updateResultsCount(count) {
            if (count === 0 || count === totalForms) {
                $('#search-results-count').text(count === 0 ? '' : `Showing all ${count} form(s)`);
            } else {
                $('#search-results-count').text(`Showing ${count} of ${totalForms} form(s)`);
            }
        }
    });
</script>
@endsection