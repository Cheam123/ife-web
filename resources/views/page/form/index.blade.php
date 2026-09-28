@extends('layouts.master')

@section('title') Form List @endsection

@section('content')
<style>
    /* ===== Form list — grouped, drag-to-arrange ===== */
    .fl-toolbar { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:20px; }
    .fl-search { max-width:340px; flex:1 1 240px; }

    .fl-group { background:#fff; border:1px solid #e3e8f0; border-radius:12px; margin-bottom:18px; overflow:hidden;
                box-shadow:0 1px 2px rgba(16,24,40,0.04); }
    .fl-group-head { display:flex; align-items:center; gap:10px; padding:13px 18px; background:#f7f8fb; border-bottom:1px solid #eceff5; }
    .fl-group-name { font-size:14.5px; font-weight:700; color:#16203a; flex:1; }
    .fl-group-count { font-size:12px; color:#8a93a8; font-weight:600; }
    .fl-group-actions { display:flex; align-items:center; gap:4px; }

    .fl-row { display:flex; align-items:center; gap:14px; padding:13px 18px; border-bottom:1px solid #f1f4f9; background:#fff; }
    .fl-row:last-child { border-bottom:0; }
    .fl-row:hover { background:#fafbfe; }
    .fl-handle { cursor:grab; color:#c3cad8; font-size:17px; flex-shrink:0; }
    .fl-handle:active { cursor:grabbing; }
    .fl-name { font-weight:600; color:#16203a; font-size:13.5px; }
    .fl-desc { font-size:12px; color:#8a93a8; margin-top:2px; }
    .fl-main { flex:1; min-width:0; }
    .fl-meta { font-size:12px; color:#8a93a8; white-space:nowrap; }
    .fl-actions { display:flex; gap:5px; flex-shrink:0; }

    .fl-empty-group { padding:18px; text-align:center; color:#b6bec7; font-size:12.5px; font-style:italic; }
    /* Ungrouped is noise when it holds nothing, but it is also the only place
       to drop a form you want out of every group — so it reappears mid-drag. */
    .fl-hidden { display:none; }
    body.fl-dragging .fl-hidden { display:block; }
    body.fl-dragging .fl-group--ungrouped { border-style:dashed; }
    .fl-drop-hint { outline:2px dashed #b9c6f0; outline-offset:-6px; }
    .sortable-ghost { opacity:.35; }
    .fl-saving { position:fixed; right:22px; bottom:22px; background:#16203a; color:#fff; font-size:12.5px;
                 padding:9px 16px; border-radius:8px; opacity:0; transition:opacity .2s; pointer-events:none; z-index:1080; }
    .fl-saving.show { opacity:1; }
</style>

  <div class="row">
      <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
              <h4 class="mb-0 mt-4">Form List</h4>
              <div class="page-title-right mt-4">
                  @can('form_creation')
                  <button type="button" class="btn btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                      <i class="mdi mdi-folder-plus-outline me-1"></i> Create Group
                  </button>
                  <a href="{{ route('form.create') }}" class="btn btn-primary">
                      <i class="mdi mdi-plus me-1"></i> Create New Form
                  </a>
                  @endcan
              </div>
          </div>
      </div>
  </div>

  <div class="row">
      <div class="col-12">
          <div class="fl-toolbar">
              <form action="{{ route('form.index') }}" method="GET" class="fl-search">
                  <div class="input-group">
                      <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                      <input type="text" class="form-control" name="search" placeholder="Search forms..." value="{{ $search }}">
                      @if($search)
                          <a href="{{ route('form.index') }}" class="btn btn-outline-secondary" title="Clear">
                              <i class="mdi mdi-close"></i>
                          </a>
                      @endif
                  </div>
              </form>
              <div class="text-muted small">
                  {{ trans_choice('{0} No forms|{1} 1 form|[2,*] :count forms', $formCount, ['count' => $formCount]) }}
                  @if($search) matching &ldquo;{{ $search }}&rdquo; @endif
                  @can('form_creation')
                      @unless($search)
                          &middot; drag <i class="mdi mdi-drag-horizontal-variant"></i> to arrange
                      @endunless
                  @endcan
              </div>
          </div>

          @if($search)
              {{-- Arranging while filtered would persist an order built from a
                   partial list, so drag is disabled until the search is cleared. --}}
              <div class="alert alert-light border small py-2 px-3">
                  <i class="mdi mdi-information-outline me-1"></i>Clear the search to rearrange forms.
              </div>
          @endif

          <div id="fl-groups">
              @foreach($groups as $group)
                  @php $groupForms = $grouped[$group->id] ?? collect(); @endphp
                  <div class="fl-group" data-group-id="{{ $group->id }}">
                      <div class="fl-group-head">
                          @can('form_creation')
                              <i class="mdi mdi-drag-horizontal-variant fl-handle fl-group-handle"></i>
                          @endcan
                          <span class="fl-group-name">{{ $group->name }}</span>
                          <span class="fl-group-count">{{ trans_choice('{0} empty|{1} 1 form|[2,*] :count forms', $groupForms->count(), ['count' => $groupForms->count()]) }}</span>
                          @can('form_creation')
                          <div class="fl-group-actions">
                              <button type="button" class="btn btn-sm btn-outline-secondary"
                                      title="Rename group"
                                      onclick="renameGroup({{ $group->id }}, @js($group->name))">
                                  <i class="mdi mdi-pencil-outline"></i>
                              </button>
                              <button type="button" class="btn btn-sm btn-outline-danger"
                                      title="Delete group"
                                      onclick="deleteGroup({{ $group->id }}, @js($group->name), {{ $groupForms->count() }})">
                                  <i class="mdi mdi-trash-can-outline"></i>
                              </button>
                          </div>
                          @endcan
                      </div>
                      <div class="fl-list" data-group-id="{{ $group->id }}">
                          @foreach($groupForms as $form)
                              @include('page.form.partials._form-row', ['form' => $form])
                          @endforeach
                          {{-- Always rendered, hidden while the group holds forms: a
                               group can be emptied by dragging, and the hint has to
                               appear then without a page reload. --}}
                          <div class="fl-empty-group {{ $groupForms->count() ? 'd-none' : '' }}">Drag forms here.</div>
                      </div>
                  </div>
              @endforeach

              {{-- Ungrouped is the landing place for forms whose group was
                   deleted. Hidden while empty, revealed during a drag so a form
                   can still be pulled out of every group. --}}
              <div class="fl-group fl-group--ungrouped {{ $ungrouped->count() ? '' : 'fl-hidden' }}" data-group-id="">
                  <div class="fl-group-head">
                      <span class="fl-group-name text-muted">Ungrouped</span>
                      <span class="fl-group-count">{{ trans_choice('{0} empty|{1} 1 form|[2,*] :count forms', $ungrouped->count(), ['count' => $ungrouped->count()]) }}</span>
                  </div>
                  <div class="fl-list" data-group-id="">
                      @foreach($ungrouped as $form)
                          @include('page.form.partials._form-row', ['form' => $form])
                      @endforeach
                      <div class="fl-empty-group {{ $ungrouped->count() ? 'd-none' : '' }}">
                          @if($formCount === 0)
                              No forms yet.
                          @else
                              Every form is filed in a group.
                          @endif
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="fl-saving" id="fl-saving">Saving order&hellip;</div>

  @can('form_creation')
  {{-- Create group --}}
  <div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <form action="{{ route('form.groups.store') }}" method="POST" id="createGroupForm" onsubmit="return false;">
                  @csrf
                  <div class="modal-header">
                      <h5 class="modal-title">Create group</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                      <label class="form-label small fw-semibold">Group name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="name" id="createGroupName"
                             placeholder="e.g. Reports, Sales, Claims" maxlength="120">
                      <div class="invalid-feedback">A group name is required.</div>
                      <div class="form-text">Groups only organise the list. Who may submit a form is still set on the form itself.</div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                      <button type="button" class="btn btn-primary" id="btn-create-group">Create group</button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  {{-- Rename group --}}
  <div class="modal fade" id="renameGroupModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <form method="POST" id="renameGroupForm" onsubmit="return false;">
                  @csrf
                  <div class="modal-header">
                      <h5 class="modal-title">Rename group</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                      <label class="form-label small fw-semibold">Group name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="name" id="renameGroupName" maxlength="120">
                      <div class="invalid-feedback">A group name is required.</div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                      <button type="button" class="btn btn-primary" id="btn-rename-group">Save</button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <form method="POST" id="deleteGroupForm" class="d-none">@csrf</form>
  @endcan
@endsection

@section('script')
<script src="{{ asset('assets/libs/sortablejs/sortablejs.min.js') }}"></script>
<script>
    const FL_CAN_ARRANGE = @json(auth()->user()->can('form_creation') && !$search);

    $(document).ready(function () {
        if (FL_CAN_ARRANGE) { initArranging(); }

        // A toast cannot survive the reload that follows a successful change,
        // so it is handed across in sessionStorage.
        const pending = sessionStorage.getItem('fl-toast');
        if (pending) {
            sessionStorage.removeItem('fl-toast');
            try { const t = JSON.parse(pending); showToast(t.msg, t.type); } catch (e) {}
        }

        $('#createGroupName, #renameGroupName').on('input', function () { $(this).removeClass('is-invalid'); });

        $('#btn-create-group').on('click', function () {
            submitGroup($(this), "{{ route('form.groups.store') }}", $('#createGroupName'));
        });
        $('#btn-rename-group').on('click', function () {
            submitGroup($(this), $('#renameGroupForm').attr('action'), $('#renameGroupName'));
        });
    });

    function initArranging() {
        // Forms move within and between groups; one shared group name is what
        // makes cross-group drops possible.
        document.querySelectorAll('.fl-list').forEach(function (list) {
            Sortable.create(list, {
                group: 'forms',
                handle: '.fl-form-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onStart: function () {
                    $('body').addClass('fl-dragging');
                    $('.fl-list').addClass('fl-drop-hint');
                },
                onEnd: function () {
                    $('body').removeClass('fl-dragging');
                    $('.fl-list').removeClass('fl-drop-hint');
                    syncEmptyPlaceholders();
                    persistOrder();
                }
            });
        });

        // Groups themselves reorder; Ungrouped is pinned last by filtering it out.
        Sortable.create(document.getElementById('fl-groups'), {
            handle: '.fl-group-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: persistOrder
        });
    }

    /**
     * "Drag forms here" belongs to an empty group only. Dragging changes that
     * without a reload, so the hint is toggled from the live row count.
     */
    function syncEmptyPlaceholders() {
        $('.fl-list').each(function () {
            const empty = $(this).find('.fl-row').length === 0;
            $(this).find('.fl-empty-group').toggleClass('d-none', !empty);
        });

        // An emptied Ungrouped section disappears again once the drag ends.
        const $ungrouped = $('.fl-group--ungrouped');
        $ungrouped.toggleClass('fl-hidden', $ungrouped.find('.fl-row').length === 0);
    }

    function submitGroup($btn, url, $input) {
        const name = $input.val().trim();
        if (!name) {
            $input.addClass('is-invalid').focus();
            return;
        }

        $btn.prop('disabled', true);

        $.ajax({
            type: 'post',
            url: url,
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: { _token: "{{ csrf_token() }}", name: name }
        }).done(function (res) {
            sessionStorage.setItem('fl-toast', JSON.stringify({ msg: res.message, type: 'success' }));
            location.reload();
        }).fail(function (xhr) {
            // A duplicate name keeps the modal open with the text intact, so
            // the admin can edit it rather than retype it.
            const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not save the group.';
            $input.addClass('is-invalid').focus();
            showToast(msg, 'error');
        }).always(function () {
            $btn.prop('disabled', false);
        });
    }

    function persistOrder() {
        const forms = [];
        $('.fl-list').each(function () {
            const groupId = $(this).data('group-id');
            $(this).find('.fl-row').each(function (index) {
                forms.push({
                    id: $(this).data('form-id'),
                    group_id: groupId === '' ? null : groupId,
                    position: index
                });
            });
        });

        const groups = [];
        $('#fl-groups .fl-group').each(function (index) {
            const id = $(this).data('group-id');
            if (id !== '') { groups.push({ id: id, position: index }); }
        });

        const $toast = $('#fl-saving').addClass('show').text('Saving order…');

        $.ajax({
            type: 'post',
            url: "{{ route('form.groups.reorder') }}",
            data: { _token: "{{ csrf_token() }}", forms: forms, groups: groups },
            success: function () {
                $toast.text('Order saved');
                setTimeout(function () { $toast.removeClass('show'); }, 900);
            },
            error: function () {
                $toast.text('Could not save the order — reloading');
                setTimeout(function () { location.reload(); }, 1200);
            }
        });
    }

    function renameGroup(id, name) {
        $('#renameGroupForm').attr('action', "{{ url('v1/form/groups') }}/" + id + "/rename");
        $('#renameGroupName').val(name).removeClass('is-invalid');
        new bootstrap.Modal(document.getElementById('renameGroupModal')).show();
    }

    function deleteGroup(id, name, count) {
        new swal({
            title: 'Delete "' + name + '"?',
            text: count === 0
                ? 'This empty group will be removed.'
                : count + ' form(s) will move to Ungrouped. No form is deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Delete group'
        }).then(function (result) {
            if (result.isConfirmed) {
                const $form = $('#deleteGroupForm');
                $form.attr('action', "{{ url('v1/form/groups') }}/" + id + "/delete").appendTo('body').trigger('submit');
            }
        });
    }

    function deleteForm(id) {
        new swal({
            title: 'Please confirm to proceed on the deletion!',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "{{ route('form.delete') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'id': id
                    },
                    cache: false,
                    success: function(response) {
                        if(response.success) {
                            new swal(
                                'Deleted!',
                                'Your form has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            new swal(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        new swal(
                            'Error!',
                            'Something went wrong.',
                            'error'
                        );
                    }
                });
            }
        })
    }

    function toggleForm(id, isActive) {
        const action = isActive ? 'disable' : 'enable';
        const actionTitle = isActive ? 'Disable' : 'Enable';

        new swal({
            title: `${actionTitle} this form?`,
            text: `Are you sure you want to ${action} this form?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "{{ route('form.toggle') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'id': id
                    },
                    cache: false,
                    success: function(response) {
                        if(response.success) {
                            new swal(
                                'Success!',
                                `Form has been ${action}d.`,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            new swal(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        new swal(
                            'Error!',
                            'Something went wrong.',
                            'error'
                        );
                    }
                });
            }
        })
    }
</script>
@endsection
