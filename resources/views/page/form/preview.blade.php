@extends('layouts.master')

@section('title') Form Preview @endsection

@section('content')
  <div class="row">
      <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
              <h4 class="mb-0 mt-4">Form Preview: {{ $form->name }}</h4>
              <div class="page-title-right mt-4">
                  <a href="{{ route('form.index') }}" class="btn btn-secondary">
                      <i class="mdi mdi-arrow-left me-1"></i> Back to List
                  </a>
              </div>
          </div>
      </div>
  </div>

  <div class="row">
      <div class="col-lg-8">
          <div class="card">
              <div class="card-body">
                  <h5 class="card-title">{{ $form->name }}</h5>
                  <p class="text-muted">{{ $form->description }}</p>

                  <hr>

                  @if(count($tree) > 0)
                      <form id="preview-form">
                          @include('page.form.partials._form-render', [
                              'tree'       => $tree,
                              'answers'    => [],
                              'visibility' => [],
                              'form'       => $form,
                              'disabled'   => true,
                          ])

                          <div class="alert alert-warning mt-4">
                              <i class="mdi mdi-information-outline"></i> This is a preview only. The form is not functional.
                              Fields with display conditions are all shown here regardless of their conditions.
                          </div>
                      </form>
                  @else
                      <div class="alert alert-info">
                          <i class="mdi mdi-information-outline"></i> No form elements have been added to this form yet.
                      </div>
                  @endif
              </div>
          </div>
      </div>

      <div class="col-lg-4">
          <div class="card">
              <div class="card-body">
                  <h5 class="card-title mb-3"><i class="mdi mdi-sitemap me-1"></i>Approval Process</h5>

                  @if(empty($process['nodes']))
                      <div class="text-muted small">No approval steps — submissions are approved automatically.</div>
                  @else
                      <div class="text-center mb-2"><span class="badge bg-dark px-3 py-2">Submit</span></div>
                      @foreach($process['nodes'] as $node)
                          @include('page.form.partials._process-preview-node', ['node' => $node, 'approverNames' => $approverNames])
                      @endforeach
                      <div class="text-center mt-2"><span class="badge bg-dark px-3 py-2">End</span></div>
                  @endif
              </div>
          </div>
      </div>
  </div>
@endsection
