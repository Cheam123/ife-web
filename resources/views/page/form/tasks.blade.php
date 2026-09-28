@extends('layouts.master')

@section('title') Form Tasks @endsection

@section('css')
@include('page.form.partials._table-style')
@endsection

@section('content')
  <div class="row">
      <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
              <h4 class="mb-0 mt-4">Form Tasks</h4>
          </div>
      </div>
  </div>

  <div class="row">
      <div class="col-12">
          <div class="card">
              <div class="card-body">
                  <p class="text-muted mb-4">
                      Forms waiting for <strong>your</strong> action — sections assigned to you and approvals on your desk.
                  </p>

                  @if($tasks->isEmpty())
                      <div class="text-center text-muted py-5">
                          <i class="mdi mdi-check-all display-4" style="opacity:0.4;"></i>
                          <p class="mt-2 mb-0">All caught up — nothing is waiting on you.</p>
                      </div>
                  @else
                      <div class="table-responsive">
                          <table class="table table-hover form-table align-middle mb-0">
                              <thead class="table-light">
                                  <tr>
                                      <th style="width:40px;">#</th>
                                      <th>Record</th>
                                      <th>Submitted By</th>
                                      <th>Submitted At</th>
                                      <th>Waiting For</th>
                                      <th class="text-center" style="width:120px;">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach($tasks as $task)
                                      <tr>
                                          <td>{{ $loop->iteration }}</td>
                                          <td>
                                              <a href="{{ route('form.records.show', ['id' => $task['record_id'], 'entry' => $task['submission_id']]) }}"
                                                 class="fw-semibold text-body d-block">
                                                  {{ $task['record_title'] }}
                                              </a>
                                              <div class="text-muted" style="font-size:0.75rem;">
                                                  {{ $task['form_name'] }} &middot;
                                                  <span class="badge bg-light text-dark border">{{ $task['record_ref'] }}</span>
                                              </div>
                                          </td>
                                          <td>{{ $task['submitted_by'] }}</td>
                                          <td>
                                              {{ $task['submitted_at']->format('d M Y, h:i A') }}
                                              <div class="text-muted small">{{ $task['submitted_at']->diffForHumans() }}</div>
                                          </td>
                                          <td>
                                              @if($task['stage_type'] === 'fill')
                                                  <span class="badge" style="background-color:#7a56d1;">
                                                      <i class="mdi mdi-account-edit-outline me-1"></i>Handler: {{ $task['stage_name'] }}
                                                  </span>
                                              @else
                                                  <span class="badge" style="background-color:#e8871e;">
                                                      <i class="mdi mdi-account-check-outline me-1"></i>Approve: {{ $task['stage_name'] }}
                                                  </span>
                                              @endif
                                          </td>
                                          <td class="text-center">
                                              <a href="{{ route('form.records.show', ['id' => $task['record_id'], 'entry' => $task['submission_id']]) }}" class="btn btn-sm btn-primary">
                                                  {{ $task['stage_type'] === 'fill' ? 'Fill Section' : 'Review' }}
                                              </a>
                                          </td>
                                      </tr>
                                  @endforeach
                              </tbody>
                          </table>
                      </div>
                  @endif
              </div>
          </div>
      </div>
  </div>
@endsection
