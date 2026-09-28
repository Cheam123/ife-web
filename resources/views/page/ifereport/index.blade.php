@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') IFE Report @endsection

@section('content')

<style>
    /* The Modal (background) */
    .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;

    }

    .modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
    }

    /* The Close Button */
    .close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    }

    .close:hover,
    .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
    }


    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    /* Modal Content (image) */
    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }

    /* Caption of Modal Image */
    #caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 10px 0;
        height: 150px;
    }

    /* Add Animation */
    .modal-content, #caption {  
        -webkit-animation-name: zoom;
        -webkit-animation-duration: 0.6s;
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @-webkit-keyframes zoom {
        from {-webkit-transform:scale(0)} 
        to {-webkit-transform:scale(1)}
    }

    @keyframes zoom {
        from {transform:scale(0)} 
        to {transform:scale(1)}
    }

    /* The Close Button */
    .close {
        position: absolute;
        top: 25px;
        right: 35px;
        color:rgb(0, 0, 0);
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 700px){
        .modal-content {
            width: 100%;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <form class="form" id="filter" action="{{ route('ifereport.index') }}" method="GET">
            <div class="row">
                <div class="col-md-3 col-xl-3" style="padding-top: 5px; padding-bottom: 2px">
                    <!-- Creation date -->
                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="start"  id="start" placeholder="Created (from)" value="{{ old('start',$request->start) }}">
                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="end" id="end" placeholder="Created (to)" value="{{ old('end',$request->end) }}">
                    </div>
                </div>

                <div class="col-md-3 col-xl-3" style="padding-top: 5px; padding-bottom: 2px">
                    <select class="form-control custom-font-small" name="ifearea" id="ifearea" style="width:100%;">
                        <option value="">-- Select IFE Area --</option>
                        @foreach($ifeareas as $c)
                        <option value="{{ $c->id }}" {{ old('ifearea',$request->ifearea) == $c->id ? 'selected' : '' }}>{{ $c->area }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-xl-3" style="padding-top: 5px; padding-bottom: 2px">
                    <select class="form-control custom-font-small" name="salesperson" id="salesperson" style="width:100%;">
                        <option value="">-- Select Salesperson --</option>
                        @foreach($salesperson as $c)
                        <option value="{{ $c->id }}" {{ old('salesperson',$request->salesperson) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-xl-3" style="padding-top: 2px; padding-bottom: 2px">
                    <input type="text" class="form-control form-control-sm custom-font-small" name="company_name" id="company_name" maxlength="255" placeholder="Company Name" value="{{ old('company_name',$request->company_name) }}">
                </div>

                <div class="col-md-3 col-xl-3" style="padding-top: 2px; padding-bottom: 2px">
                    <input type="text" class="form-control form-control-sm custom-font-small" name="cafe_name" id="cafe_name" maxlength="255" placeholder="Cafe Name" value="{{ old('cafe_name',$request->cafe_name) }}">
                </div>

                <div class="col-md-3 col-xl-3" style="padding-top: 2px; padding-bottom: 2px">
                    <input type="text" class="form-control form-control-sm custom-font-small" name="mobile" id="mobile" maxlength="14" onkeypress="return onlyNumberKey(event)" placeholder="Mobile" value="{{ old('mobile',$request->mobile) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-xl-12" style="padding-top: 8px;">
                    <button type="reset" class="btn btn-sm custom-button-shadow" id="querystring" style="margin-right: 4px; width:100px">@lang('translation.reset')</button>
                    <button type="submit" class="btn btn-sm btn-primary custom-button-shadow" id="filter" style="margin-right: 4px; width:100px">@lang('translation.search')</button>
                    <button type="button" class="btn btn-sm btn-success custom-button-shadow" id="export" style="margin-right: 4px; width:100px">Export Excel</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body">
        {{ $ifereports->withQueryString()->links() }}
        <div style="overflow-x:auto; white-space: nowrap;">
            <table class="table table-sm custom-font-small" style="width:100%; border-collapse: collapse; box-shadow: 1px 1px 3px 1px rgb(183, 183, 183);">
                <thead class="table-dark">
                    <tr>
                        <td scope="col">#</td>
                        <td scope="col">IFE Area</td>
                        <td scope="col">Salesperson</td>
                        <td scope="col">Company Name</td>
                        <td scope="col">Cafe Name</td>
                        <td scope="col">{{ trans('translation.created_at') }}</td>
                        <td scope="col" style="text-align: center; width:100px"></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ifereports as $s)
                        <tr>
                            <td style="border-bottom: 1px solid #acacac;" scope="row" >
                                {{ ( ($ifereports->links()->paginator->currentPage()-1) * $ifereports->links()->paginator->perPage() ) + $loop->iteration }}
                            </td>
                            <td style="border-bottom: 1px solid #acacac;">{{ $s->area ? $s->area->area : '' }}</td>
                            <td style="border-bottom: 1px solid #acacac;">{{ $s->createdBy->name }}</td>
                            <td style="border-bottom: 1px solid #acacac;">{{ $s->company_name }}</td>
                            <td style="border-bottom: 1px solid #acacac;">{{ $s->shop_name }}</td>
                            <td style="border-bottom: 1px solid #acacac;" class="custom-font-xxsmall">
                                <span style="color: blue;">{{ \Carbon\Carbon::parse($s->created_at)->format('Y-m-d, h:i:s A') }}</span><br>
                                {{ $s->location }}
                            </td>
                            <td style="border-bottom: 1px solid #acacac; display: flex; align-items: center; justify-content: flex-end;">
                                <div>
                                    @if(!$s->task_id && Auth::guard('web')->user()->can('manage_ife_report'))
                                        <button type="button" onclick="convertTask({{ $s->id }})" class="btn btn-sm btn-danger custom-button-shadow">+ Task</button>
                                    @endif
                                    @php
                                        $param['id'] = $s->id
                                    @endphp
                                    <a href="{{ route('ifereport.view', $param) }}" class="btn btn-sm btn-primary custom-button-shadow" title="View">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $("#ifearea").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $("#salesperson").select2({ dropdownCssClass: "small", containerCssClass: "small" });

    $("#querystring").click(function() {
        window.location.href = window.location.href.split('?')[0];
    });

    $("#export").click(function() {
        var form = $("form#filter");
        var originalAction = form.attr('action');
        form.attr('action', "{{ route('ifereport.export') }}");
        form.submit();
        form.attr('action', originalAction);
    });

    $("#start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });

    $("#end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });

    function deleteReport(id) {
        new swal({
            title: 'Please confirm to proceed on the deletion!',
            customClass: {
                container: 'text-class align-middle',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "{{ route('ifereport.delete') }}",
                    data: {"_token": "{{ csrf_token() }}",'id': id },
                    cache: false,
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        })
    }
    
    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }

    function convertTask(id) {
        new swal({
            title: 'Please confirm to convert this report to sales task!',
            customClass: {
                container: 'text-class align-middle',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "{{ route('ifereport.convert') }}",
                    data: {"_token": "{{ csrf_token() }}",'id': id },
                    cache: false,
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        })
    }

    function deleteReport(id) {
        new swal({
            title: 'Please confirm to proceed on the deletion!',
            customClass: {
                container: 'text-class align-middle',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "{{ route('ifereport.delete') }}",
                    data: {"_token": "{{ csrf_token() }}",'id': id },
                    cache: false,
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        })
    }

    function freezeReport(id) {
        new swal({
            title: 'Please confirm to proceed!',
            customClass: {
                container: 'text-class align-middle',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "{{ route('ifereport.freeze') }}",
                    data: {"_token": "{{ csrf_token() }}",'id': id },
                    cache: false,
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        })
    }

    function unfreezeReport(id) {
        new swal({
            title: 'Please confirm to proceed!',
            customClass: {
                container: 'text-class align-middle',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "{{ route('ifereport.unfreeze') }}",
                    data: {"_token": "{{ csrf_token() }}",'id': id },
                    cache: false,
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        })
    }
</script>
@endsection
