@extends('layouts.master')

@section('title') {{ $isAdmin ? 'All Records' : 'My Records' }} @endsection

@section('css')
@include('page.form.partials._table-style')
@include('page.form.partials._filter-style')
@endsection

@php
    // Pills read left to right the way a record travels: still moving, then
    // the ways it can end. "All" carries the total, so the row always says how
    // much is in scope.
    $pills = [
        ''          => 'All',
        'pending'   => 'Pending',
        'approved'  => 'Approved',
        'rejected'  => 'Rejected',
        'cancelled' => 'Cancelled',
        'closed'    => 'Closed',
    ];
    $pillCounts = [
        ''          => (int) $statusCounts->sum(),
        'pending'   => (int) ($statusCounts['pending']   ?? 0),
        'approved'  => (int) ($statusCounts['approved']  ?? 0),
        'rejected'  => (int) ($statusCounts['rejected']  ?? 0),
        'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
        'closed'    => (int) ($statusCounts['closed']    ?? 0),
    ];
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 mt-4">{{ $isAdmin ? 'All Records' : 'My Records' }}</h4>
            <div class="page-title-right mt-4 d-flex gap-2">
                @if($isAdmin)
                    <a href="{{ route('form.records.export', request()->query()) }}" class="btn btn-success">
                        <i class="mdi mdi-file-excel me-1"></i> Export
                    </a>
                @endif
                <a href="{{ route('form.entry') }}" class="btn btn-primary">
                    <i class="mdi mdi-plus me-1"></i> Start a form
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card card3 custom-font-small">
    <div class="card-body">
        <div class="form-filter">
            <form class="form" id="filter" action="{{ url()->current() }}" method="GET">
                <div class="custom-font-small">
                    <div id="statusRow1" class="mb-2">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="mb-3">
                                        <label for="search" class="custom-font-xsmall">Search:</label><br/>
                                        <input type="text" maxlength="255" class="form-control form-control-sm custom-font-xsmall"
                                               name="search" id="search" value="{{ request('search') }}"
                                               placeholder="Record title or form name...">
                                    </div>
                                </div>

                                <div class="col-lg-4 col-sm-12">
                                    <div class="mb-3">
                                        <label for="form_id" class="custom-font-xsmall">Form:</label><br/>
                                        <select class="js-select2 form-control form-control-sm custom-font-xsmall"
                                                name="form_id" id="form_id" data-placeholder="All forms" style="width:100%;">
                                            <option value=""></option>
                                            @foreach($forms as $formOption)
                                                <option value="{{ $formOption->id }}" {{ (string) request('form_id') === (string) $formOption->id ? 'selected' : '' }}>
                                                    {{ $formOption->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-sm-12">
                                    <div class="d-flex flex-wrap justify-content-end align-items-center gap-2" style="padding-top:22px;">
                                        <a class="btn btn-sm custom-button-shadow d-inline-flex align-items-center justify-content-center"
                                           id="btnExpendRow" name="btnExpendRow" data-bs-toggle="collapse" data-bs-target="#statusRow2"
                                           aria-expanded="false" aria-controls="statusRow2" title="More filters"
                                           style="height:32px; width:36px; padding:0;"><i class="material-icons" style="font-size:18px;">unfold_more</i></a>
                                        <button id="btnSearch" class="btn btn-sm custom-button-shadow d-inline-flex align-items-center justify-content-center"
                                                type="submit" title="Search"
                                                style="height:32px; width:36px; padding:0;"><i class="material-icons" style="font-size:18px;">search</i></button>
                                        <button class="btn btn-sm custom-button-shadow d-inline-flex align-items-center justify-content-center"
                                                type="reset" id="querystring" title="Clear filters"
                                                style="height:32px; width:36px; padding:0;"><i class="material-icons" style="font-size:18px;">delete_sweep</i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="statusRow2" name="statusRow2" class="collapse {{ isset($_COOKIE['expendRowTwo']) ? ($_COOKIE['expendRowTwo'] == '1' ? 'show' : '') : '' }}">
                        <div class="container-fluid" style="padding-top:10px;">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="mb-3">
                                        <label for="submitted_by" class="custom-font-xsmall">Submitted By:</label><br/>
                                        <select class="js-select2 form-control form-control-sm custom-font-xsmall"
                                                name="submitted_by" id="submitted_by" data-placeholder="Anyone" style="width:100%;">
                                            <option value=""></option>
                                            @foreach($users as $u)
                                                <option value="{{ $u->id }}" {{ (string) request('submitted_by') === (string) $u->id ? 'selected' : '' }}>
                                                    {{ $u->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-sm-12">
                                    <div class="mb-3">
                                        <label for="date_start" class="custom-font-xsmall">Submission Date:</label><br/>
                                        <div class="input-daterange input-group" id="datepicker-records"
                                             data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                                             data-provide="datepicker" data-date-container="#datepicker-records">
                                            <input type="text" class="form-control form-control-sm custom-font-xsmall"
                                                   name="date_start" id="date_start" placeholder="Start Date" value="{{ request('date_start') }}">
                                            <input type="text" class="form-control form-control-sm custom-font-xsmall"
                                                   name="date_end" id="date_end" placeholder="End Date" value="{{ request('date_end') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr />

                <input type="hidden" name="status" id="status" value="{{ request('status') }}">

                <div class="d-flex justify-content-between nav-pills" style="overflow-x:auto; white-space: nowrap;">
                    @include('page.form.partials._status-pills', [
                        'pills'  => $pills,
                        'counts' => $pillCounts,
                        'active' => (string) request('status', ''),
                    ])
                    <div class="d-flex">
                        <div style="padding-top: 10px; width: 220px;">
                            <select class="form-select form-select-sm custom-font-xsmall" name="sortby" id="sortby" onchange="refreshPage()">
                                <option value="created_at" {{ request('sortby', 'created_at') === 'created_at' ? 'selected' : '' }}>Sort By Submission Date</option>
                                <option value="updated_at" {{ request('sortby') === 'updated_at' ? 'selected' : '' }}>Sort By Last Activity</option>
                                <option value="record_title" {{ request('sortby') === 'record_title' ? 'selected' : '' }}>Sort By Record Title</option>
                            </select>
                        </div>
                        <div class="ms-2" style="padding-top: 10px; width: 90px;">
                            <select class="form-select form-select-sm custom-font-xsmall" name="sortmode" id="sortmode" onchange="refreshPage()">
                                <option value="desc" {{ request('sortmode', 'desc') === 'desc' ? 'selected' : '' }}>&#128317; Desc</option>
                                <option value="asc" {{ request('sortmode') === 'asc' ? 'selected' : '' }}>&#128316; Asc</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if($rows->isEmpty())
            <div class="text-center py-5">
                <i class="mdi mdi-file-document-outline" style="font-size:64px;color:#ccc;"></i>
                <h5 class="mt-3 text-muted">
                    {{ request()->hasAny(['search', 'form_id', 'status', 'submitted_by', 'date_start', 'date_end']) ? 'No records match this filter' : 'No records yet' }}
                </h5>
                <p class="text-muted mb-0">Anything you submit or take part in appears here.</p>
            </div>
        @else
            <div class="table-responsive mt-2">
                <table class="table table-hover form-table form-table-compact align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:110px;">Reference</th>
                            <th>Record</th>
                            <th style="width:190px;">Waiting on</th>
                            <th class="text-center" style="width:130px;">Status</th>
                            <th style="width:150px;">Last activity</th>
                            <th class="text-center" style="width:90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            @php $root = $row['root']; @endphp
                            <tr>
                                <td class="small fw-semibold">{{ $row['reference'] }}</td>
                                <td>
                                    <a href="{{ route('form.records.show', $root->id) }}" class="fw-semibold text-body">
                                        {{ $row['title'] }}
                                    </a>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        {{ optional($root->form)->name ?? 'N/A' }}
                                        &middot; {{ optional($root->submittedBy)->name ?? 'Unknown' }}
                                        &middot; {{ $root->created_at->format('d M Y') }}
                                        @if(!$root->isRecordOpen())
                                            <span class="badge bg-secondary ms-1">Closed</span>
                                        @endif
                                        @if($root->parent)
                                            {{-- Says at a glance that this is a return
                                                 visit, and onto which case. --}}
                                            <span class="badge bg-light text-dark border ms-1"
                                                  title="Follows up on {{ $root->parent->recordReference() }}">
                                                <i class="mdi mdi-subdirectory-arrow-right"></i>
                                                {{ $root->parent->recordReference() }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($row['stage_name'])
                                        <div class="small fw-semibold">{{ $row['stage_name'] }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">
                                            {{ count($row['waiting_on']) ? implode(', ', $row['waiting_on']) : 'pending' }}
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @include('page.form._status-badge', ['status' => $row['status']])
                                </td>
                                <td class="small text-muted">
                                    {{ optional($row['updated_at'])->format('d M Y H:i') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('form.records.show', $root->id) }}" class="btn btn-sm btn-outline-primary">
                                        Open
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $records->links() }}</div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/forms/form-select2.js') }}"></script>
<script>
    // A pill is just the status filter: write it into the form and submit, so
    // it travels with the search, the form and the sort like everything else.
    function filter_data(status) {
        $('#status').val(status);
        document.forms['filter'].submit();
    }

    function refreshPage() {
        document.getElementById('btnSearch').click();
    }

    $(function () {
        // type="reset" only repaints the inputs — the page still carries the
        // old query string, so clear that instead.
        $('#querystring').click(function () {
            window.location.href = window.location.href.split('?')[0];
        });

        // Remember whether the extra filters are open, with the same
        // short-lived cookie the Task screen uses.
        $('#btnExpendRow').click(function () {
            var open = document.cookie.replace(/(?:(?:^|.*;\s*)expendRowTwo\s*\=\s*([^;]*).*$)|^.*$/, "$1");
            document.cookie = 'expendRowTwo=' + (open == 1 ? 0 : 1) + '; max-age=' + 5 * 60;
        });

        $('#date_start, #date_end').datepicker({
            format:    'yyyy-mm-dd',
            autoclose: true,
        });
    });
</script>
@endsection
