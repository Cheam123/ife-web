@extends('layouts.master')

@section('title') Form Creation @endsection

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center mt-4">
            <a href="{{ route('form.index') }}" class="btn btn-secondary btn-sm me-3"><i class="mdi mdi-arrow-left"></i> Back</a>
            <h4 class="mb-0">Create Form</h4>
        </div>
      </div>
    </div>
  </div>

  @include('page.form.partials._builder-wizard', [
      'form'        => null,
      'users'       => $users,
      'formElement' => $formElement,
      'action'      => route('form.store'),
  ])
@endsection

@section('script')
  <script src="{{ asset('assets/libs/sortablejs/sortablejs.min.js') }}"></script>
  <script>
    window.CSRF_TOKEN = "{{ csrf_token() }}";
    window.FORM_GROUP_STORE_URL = "{{ route('form.groups.store') }}";
    window.FormBuilderBoot = {
      schema:   { schema_version: 2, groups: [], elements: [] },
      process:  { process_version: 1, nodes: [] },
      settings: { access: { submit_scope: 'everyone', user_ids: [], user_types: [] } },
      users:    @json($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values()),
      types:    @json($types)
    };
  </script>
  <script src="{{ asset('js/forms/form-select2.js') }}"></script>
  <script src="{{ asset('js/forms/form-process-designer.js') }}"></script>
  <script src="{{ asset('js/forms/form-builder.js') }}"></script>
@endsection
