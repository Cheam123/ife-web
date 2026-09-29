@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.customer') @endsection

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
        <form class="form" id="filter" action="{{ route('lead.index') }}" method="GET">
            {{-- Keeps a Data health filter (from the admin Overview) while searching --}}
            @if($gapLabel)
                <input type="hidden" name="gap" value="{{ $request->gap }}">
            @endif
            <div class="row">
                <div class="col-md-3 col-xl-3" style="padding-top: 5px; padding-bottom: 2px">
                    <!-- Creation date -->
                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="start"  id="start" placeholder="Created (from)" value="{{ old('start',$request->start) }}">
                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="end" id="end" placeholder="Created (to)" value="{{ old('end',$request->end) }}">
                    </div>
                </div>

                <div class="col-md-3 col-xl-3" style="padding-top: 5px; padding-bottom: 2px">
                    <select class="js-select2-area_state form-control custom-font-small" name="has_customerid" id="has_customerid" style="width:100%;">
                        <option value="">All Leads/Customers</option>
                        <option value="Y" {{ old('has_customerid',$request->has_customerid) == 2 ? 'selected' : '' }}>With Customer ID</option>
                        <option value="N" {{ old('has_customerid',$request->has_customerid) == 3 ? 'selected' : '' }}>Without Customer ID</option>
                    </select>
                </div>

                <div class="col-md-3 col-xl-3" style="padding-top: 5px; padding-bottom: 2px">
                    <select class="js-select2-area_city form-control custom-font-small" name="ifearea" id="ifearea" style="width:100%;">
                        <option value="">-- Select IFE Area --</option>
                        @foreach($ifeareas as $c)
                            <option value="{{ $c->id }}" {{ old('ifearea',$request->ifearea) == $c->id ? 'selected' : '' }}>{{ $c->area }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-xl-3" style="padding-top: 2px; padding-bottom: 2px">
                    <input type="text" class="form-control form-control-sm custom-font-small" name="lead_name" id="lead_name" maxlength="255" placeholder="Company Name" value="{{ old('lead_name',$request->lead_name) }}">
                </div>

                <div class="col-md-3 col-xl-3" style="padding-top: 2px; padding-bottom: 2px">
                    <input type="text" class="form-control form-control-sm custom-font-small" name="business_name" id="business_name" maxlength="255" placeholder="Shop Name" value="{{ old('business_name',$request->business_name) }}">
                </div>

                <div class="col-md-3 col-xl-3" style="padding-top: 2px; padding-bottom: 2px">
                    <input type="text" class="form-control form-control-sm custom-font-small" name="mobile" id="mobile" maxlength="14" onkeypress="return onlyNumberKey(event)" placeholder="Lead/Customer Mobile" value="{{ old('mobile',$request->mobile) }}">
                </div>

                <div class="col-md-3 col-xl-3" style="padding-top: 2px; padding-bottom: 2px">
                    <input type="text" class="form-control form-control-sm custom-font-small" name="customer_id" id="customer_id" maxlength="12" onkeypress="return onlyNumberKey(event)" placeholder="Customer ID" value="{{ old('customer_id',$request->customer_id) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-xl-12" style="padding-top: 8px;">
                    <button type="reset" class="btn btn-sm custom-button-shadow" id="querystring" style="margin-right: 4px; width:100px">@lang('translation.reset')</button>
                    <button type="submit" class="btn btn-sm btn-primary custom-button-shadow" id="filter" style="margin-right: 4px; width:100px">@lang('translation.search')</button>
                    @if(Auth::guard('web')->user()->can('create_lead'))
                        <a class="btn btn-sm btn-primary custom-button-shadow float-right" style="margin-right: 4px; width:100px" href="{{ route('lead.create') }}">@lang('translation.add')</a>
                    @endif
                    <button type="button" class="btn btn-sm btn-primary custom-button-shadow" id="btnIfeArea" style="margin-right: 4px; width:120px">IFE Area Listing</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body">
        @if($gapLabel)
            <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center gap-2 py-2" role="status">
                <span>Showing: {{ $gapLabel }} ({{ number_format($lead_detail->total()) }})</span>
                <a href="{{ route('lead.index', $request->except(['gap', 'page'])) }}" class="fw-semibold">Show all outlets</a>
            </div>
        @endif
        {{ $lead_detail->withQueryString()->links() }}
        <div style="overflow-x:auto; white-space: nowrap;">
            <table class="table table-sm custom-font-small" style="width:100%; border-collapse: collapse; box-shadow: 1px 1px 3px 1px rgb(183, 183, 183);">
                <thead class="table-dark">
                    <tr>
                        <td scope="col">#</td>
                        <td scope="col" style="width:20%">Name</td>
                        <td scope="col">Area</td>
                        <td scope="col">Handler Information</td>
                        <td scope="col">Lead/Customer Information</td>
                        <td scope="col">{{ trans('translation.created_at') }}</td>
                        <td scope="col" style="text-align: center; width:100px"></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lead_detail as $s)
                        <tr>
                            <td style="border-bottom: 1px solid #acacac;" scope="row" >
                                {{ ( ($lead_detail->links()->paginator->currentPage()-1) * $lead_detail->links()->paginator->perPage() ) + $loop->iteration }}
                            </td>
                            <td style="border-bottom: 1px solid #acacac;">
                                @if ($s->customer_id)
                                    <img class="rounded-circle header-profile-user" src="{{ URL::asset('/assets/images/users/cartoon-boy-profile.png') }}" height="10" alt="Header Avatar">
                                @endif
                                {{ $s->name }}<br/>
                                <table>
                                    <tr>
                                        <td class="custom-font-xxsmall" style="width:1%;color:rgb(61, 61, 149);">Customer ID</td>
                                        <td class="custom-font-xxsmall" style="width:1%;">:</td>
                                        <td class="custom-font-xxsmall">{{ $s->customer_id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="custom-font-xxsmall" style="width:1%;color:rgb(61, 61, 149);">Shop Name</td>
                                        <td class="custom-font-xxsmall" style="width:1%;">:</td>
                                        <td class="custom-font-xxsmall">{{ $s->business_name }}</td>
                                    <tr>
                                        <td class="custom-font-xxsmall" style="width:1%;color:rgb(61, 61, 149);">Mobile No.</td>
                                        <td class="custom-font-xxsmall" style="width:1%;">:</td>
                                        <td class="custom-font-xxsmall">{{ $s->mobile }}</td>
                                    </tr>
                                    <tr>
                                        <td class="custom-font-xxsmall" style="width:1%;color:rgb(61, 61, 149);">Email Address</td>
                                        <td class="custom-font-xxsmall" style="width:1%;">:</td>
                                        <td class="custom-font-xxsmall">{{ $s->email }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="border-bottom: 1px solid #acacac;">{{ (isset($s->city_id) ? \App\Helpers\Helper::getCity($s->city_id) : '') . ( (isset($s->city_id) && isset($s->state_id)) ? ', ' : '') . (isset($s->state_id) ? \App\Helpers\Helper::getState($s->state_id) : '') }}</td>
                            <td style="border-bottom: 1px solid #acacac;">
                                <table>
                                    <tr>
                                        <td class="custom-font-xxsmall" style="width:1%;color:rgb(61, 61, 149);">Handler</td>
                                        <td class="custom-font-xxsmall" style="width:1%;">:</td>
                                        <td class="custom-font-xxsmall">{{ $s->upline_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="custom-font-xxsmall" style="width:1%;color:rgb(61, 61, 149);">Pre-Sales</td>
                                        <td class="custom-font-xxsmall" style="width:1%;">:</td>
                                        <td class="custom-font-xxsmall">{{ $s->presales_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="custom-font-xxsmall" style="width:1%;color:rgb(61, 61, 149);">Closing Sales</td>
                                        <td class="custom-font-xxsmall" style="width:1%;">:</td>
                                        <td class="custom-font-xxsmall">{{ $s->closing_sales_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="custom-font-xxsmall" style="width:1%;color:rgb(61, 61, 149);">IFE Area</td>
                                        <td class="custom-font-xxsmall" style="width:1%;">:</td>
                                        <td class="custom-font-xxsmall">{{ $s->ife_area_id ? $s->ifearea->area : '' }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="border-bottom: 1px solid #acacac;">
                                <table>
                                    <tr>
                                        <td class="custom-font-xxsmall" style="width:1%;color:rgb(61, 61, 149);">Creator</td>
                                        <td class="custom-font-xxsmall" style="width:1%;">:</td>
                                        <td class="custom-font-xxsmall">{{ $s->createdBy->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="custom-font-xxsmall" style="width:1%;color:rgb(61, 61, 149);">Subscriber</td>
                                        <td class="custom-font-xxsmall" style="width:1%;">:</td>
                                        <td class="custom-font-xxsmall">{{ $s->assign_to ? $s->assignee->name : '' }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td  style="border-bottom: 1px solid #acacac;" class="custom-font-xxsmall">
                                {{ \Carbon\Carbon::parse($s->created_at)->format('Y-m-d') }}<br/>
                                {{ \Carbon\Carbon::parse($s->created_at)->format('h:i:s A') }}
                            </td>
                            <td style="border-bottom: 1px solid #acacac;">
                                <div>
                                    @if(Auth::guard('web')->user()->can('edit_lead') && $s->tasks->whereIn('status',[1,2,3,4,5,6])->count() == 0)
                                        <button type="button" onclick="deleteLead({{ $s->id }})" class="btn btn-sm btn-danger custom-button-shadow">@lang('translation.delete')</button>
                                    @endif
                                    @if(Auth::guard('web')->user()->can('view_lead'))
                                        <a class="btn btn-sm btn-primary custom-button-shadow" href="lead/view/{{ $s->id }}">@lang('translation.view')</a>
                                    @endif
                                    @if(Auth::guard('web')->user()->can('edit_lead'))
                                        <a class="btn btn-sm btn-primary custom-button-shadow" href="lead/edit/{{ $s->id }}">@lang('translation.edit')</a>
                                    @endif
                                    @if(Auth::guard('web')->user()->can('add_task') &&
                                        (
                                            (empty($s->customer_id) && $s->runningTasks->count() == 0) || ($s->customer_id)
                                        )
                                    )
                                        <a class="btn btn-sm btn-primary custom-button-shadow" href="{{ route('tasks.create',['id'=>$s->id]) }}">+ Task</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal mt-3" style="z-index:50;">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close" style="z-index:50;">&times;</span>
            <p>IFE Area Listing</p>

            <div class="modal-body">
                <table class="table table-sm custom-font-small" style="width:100%; border-collapse: collapse; box-shadow: 1px 1px 3px 1px rgb(183, 183, 183);">
                    <tr>
                        <td style="background-color: #18016cff; color: #ffffff; border: 1px solid;">Area Code</td>
                        <td style="background-color: #18016cff; color: #ffffff; border: 1px solid;">Description</td>
                    </tr>
                    @foreach($ifeareas as $c)
                    <tr>
                        <td style="border: 1px solid;">{{ $c->area }}</td>
                        <td style="border: 1px solid;">{{ $c->description }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $("#ifearea").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $("#has_customerid").select2({ dropdownCssClass: "small", containerCssClass: "small" });

    $("#querystring").click(function() {
        window.location.href = window.location.href.split('?')[0];
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

    function deleteLead(id) {
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
                    url: "{{ route('lead.delete') }}",
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

    function getCities() {

        var x       = document.getElementById("area_state").value;
        var select  = $('form select[name=area_city]');
        var url     = '/v1/state/' + x + '/cities/';
        select.empty();

        $.get(url, function(data) {
            select.append(
                '<option value="" disabled selected hidden>-- @lang('translation.select_city') --</option>');
            $.each(data, function(key, value) {
                select.append('<option value=' + data[key].id + '>' + value.name + '</option>');
            });
        });
    }

    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("btnIfeArea");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
@endsection
