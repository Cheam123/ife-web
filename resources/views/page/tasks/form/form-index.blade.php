<?php 
    use Jenssegers\Agent\Agent;
    $agent = new Agent();
?>

<style>
    .select2-selection--multiple {
        border: 1px solid #d3d3d3ff!important;
        box-shadow: #e6e6e6ff 1px 1px!important;
    }

    .overflowText {
        overflow            : hidden;
        text-overflow       : ellipsis;
        display             : -webkit-box;
        -webkit-line-clamp  : 2;
                line-clamp  : 2; 
        -webkit-box-orient  : vertical;
    }
    
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

    .center {
        margin: auto;
        background-color: white;
        padding: 70px 0;
        border: 3px solid green;
        height: 100%;
        padding-left: 40%;
        text-align: left;
    }
    .nav-pills .nav-link.inactive {
        padding: 10px;
    }
    .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        padding: 10px;
    }
    .card-body {
        flex: 1 1 auto;
        padding: 0.75rem 0.75rem;
    }
    hr {
        margin: 0.5rem 0;
        color: rgba(0, 0, 0, 0.1);
        background-color: currentColor;
        border: 0;
        opacity: 0.5;
    }
    .nav-pills .nav-link.inactive {
        color: #000000;
        background-color: #bedbe8;
    }
    .swal2-container {
        z-index: 500;
    }
    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        vertical-align: middle;
        margin: auto;
        width: 90%!important;
    }

    #myImg:hover {
        opacity: 0.7;
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

    /* The Tutup Button */
    .tutup {
        position: absolute;
        top: 100px;
        right: 35px;
        color:rgb(0, 0, 0);
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
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

    /* Compact task list table */
    .task-table-compact,
    .task-table-compact td,
    .task-table-compact th,
    .task-table-compact .custom-font-xsmall,
    .task-table-compact .custom-table-tr-td-font-color,
    .task-table-compact table {
        font-size: 12px;
        line-height: 1.3;
    }
    .task-table-compact td {
        padding: 0.25rem 0.4rem;
    }
</style>

<div>
    <form class="form" id="filter" action="{{ route('tasks.index') }}" method="GET">
        <div class="custom-font-small">
            <div id="statusRow1" name="statusRow1" class="mb-2">
                <div class="container-fluid">
                    <div class="row">
                        @if(!$agent->isMobile())
                            <!-- title -->
                            <div class="col-lg-3 col-sm-12">
                                <div class="mb-3">
                                    <label for="filter_title" class="custom-font-xsmall">Task Title:</label><br/>
                                    <input type="text" maxlength="500" class="form-control form-control-sm custom-font-xsmall" name="filter_title" id="filter_title" value="{{ old('filter_title',$request->filter_title) }}" placeholder="">
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-12">
                                <!-- reference no -->
                                <div class="mb-3">
                                    <label for="filter_reference_no" class="custom-font-xsmall">@lang('translation.reference_no'):</label><br/>
                                    <input type="text" maxlength="21" class="form-control form-control-sm custom-font-xsmall" name="filter_reference_no" id="filter_reference_no" value="{{ old('filter_reference_no',$request->filter_reference_no) }}" placeholder="">
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-12">
                                <!-- subscriber -->
                                <div class="mb-3">
                                    <label for="filter_subscriber" class="custom-font-xsmall">Subscriber:</label><br/>
                                    <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_subscriber" id="filter_subscriber" style="width:100%">
                                        <option value="">-- @lang('translation.select_your_choice') --</option>
                                        @foreach($users as $key => $u)
                                            <option value="{{ $u->id }}" @if (old('filter_subscriber',$request->filter_subscriber) == $u->id) selected @endif>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-12">
                                <!-- sub-subscriber -->
                                <div class="mb-3">
                                    <label for="filter_subsubscriber" class="custom-font-xsmall">Sub-Subscriber:</label><br/>
                                    <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_subsubscriber" id="filter_subsubscriber" style="width:100%">
                                        <option value="">-- @lang('translation.select_your_choice') --</option>
                                        @foreach($users as $key => $u)
                                            <option value="{{ $u->id }}" @if (old('filter_subsubscriber',$request->filter_subsubscriber) == $u->id) selected @endif>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="col-lg-3 col-sm-12">
                            <div class="d-flex flex-wrap justify-content-end align-items-center gap-2">
                                <button id="btnIfeArea" class="btn btn-sm btn-primary custom-button-shadow d-inline-flex align-items-center justify-content-center" type="button" style="height:32px; min-width:120px;">IFE Area Code</button>
                                <a class="btn btn-sm custom-button-shadow d-inline-flex align-items-center justify-content-center" id="btnExpendRow" name="btnExpendRow" data-bs-toggle="collapse" data-bs-target="#statusRow2" aria-expanded="true" aria-controls="statusRow2" style="height:32px; width:36px; padding:0;"><i class="material-icons" style="font-size:18px;">unfold_more</i></a>
                                <button id="btnSearch" class="btn btn-sm custom-button-shadow d-inline-flex align-items-center justify-content-center" type="submit" style="height:32px; width:36px; padding:0;"><i class="material-icons" style="font-size:18px;">search</i></button>
                                <button class="btn btn-sm custom-button-shadow d-inline-flex align-items-center justify-content-center" type="reset" id="querystring" style="height:32px; width:36px; padding:0;"><i class="material-icons" style="font-size:18px;">delete_sweep</i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="statusRow2" name="statusRow2" class="collapse {{ isset($_COOKIE['expendRowTwo']) ? ($_COOKIE['expendRowTwo'] == '1' ? 'show' : '') : '' }}">
                <div class="container-fluid" style="padding-top:10px;">
                    @if($agent->isMobile())
                    <div class="row">
                        <!-- title -->
                        <div class="col-lg-3 col-sm-12">
                            <div class="mb-3">
                                <label for="filter_title" class="custom-font-xsmall">Task Title:</label><br/>
                                <input type="text" maxlength="500" class="form-control form-control-sm custom-font-xsmall" name="filter_title" id="filter_title" value="{{ old('filter_title',$request->filter_title) }}" placeholder="">
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-12">
                            <!-- reference no -->
                            <div class="mb-3">
                                <label for="filter_reference_no" class="custom-font-xsmall">Task Reference:</label><br/>
                                <input type="text" maxlength="20" class="form-control form-control-sm custom-font-xsmall" name="filter_reference_no" id="filter_reference_no" value="{{ old('filter_reference_no',$request->filter_reference_no) }}" placeholder="">
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-12">
                            <!-- subscriber -->
                            <div class="mb-3">
                                <label for="filter_subscriber" class="custom-font-xsmall">Subscriber:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_subscriber" id="filter_subscriber" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_subscriber',$request->filter_subscriber) == $u->id) selected @endif>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-12">
                            <!-- sub-subscriber -->
                            <div class="mb-3">
                                <label for="filter_subsubscriber" class="custom-font-xsmall">Sub-Subscriber:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_subsubscriber" id="filter_subsubscriber" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_subsubscriber',$request->filter_subsubscriber) == $u->id) selected @endif>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row">
                        <!-- With Sales -->
                        <div class="col-lg-3 col-sm-12">
                            <div class="mb-3">
                                <label for="filter_withsales" class="custom-font-xsmall">With Sales:</label><br/>
                                <select class="js-select2-with-sales form-control form-control-sm custom-font-xsmall" name="filter_withsales" id="filter_withsales" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    <option value="Y" @if (old('filter_withsales',$request->filter_withsales) == 'Y') selected @endif>Yes</option>
                                    <option value="N" @if (old('filter_withsales',$request->filter_withsales) == 'Y') selected @endif>No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- name -->
                            <div class="mb-3">
                                <label for="filter_name" class="custom-font-xsmall">Lead/Customer Name:</label><br/>
                                <select class="form-control form-select custom-font-small js-leadname-multiple" name="filter_name[]" id="filter_name" multiple style="width:100%;">
                                    @if(null !== $request->filter_name)
                                        @foreach($leadNameOptions as $c)
                                        <option value="{{ $c }}" {{ array_search($c, $request->filter_name) === false ? '' : 'selected' }}>{{ $c }}</option>
                                        @endforeach
                                    @else
                                        @foreach($leadNameOptions as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- Appointment date -->
                            <div class="mb-3">
                                <label for="appointment_date_start" class="custom-font-xsmall">Appointment Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker7" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker7">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="appointment_date_start" id="appointment_date_start" placeholder="Start Date" value="{{ old('appointment_date_start',$request->appointment_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="appointment_date_end" id="appointment_date_end" placeholder="End Date" value="{{ old('appointment_date_end',$request->appointment_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- alert -->
                            <div class="mb-3">
                                <label for="filter_alert" class="custom-font-xsmall">Alert Indicator ON:</label><br/>
                                <select class="form-control form-select custom-font-small js-alertind" name="filter_alert" id="filter_alert" style="width:100%;">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    <option value="1" {{ old('filter_alert',$request->filter_alert) == 1 ? 'selected' : '' }}>NO</option>
                                    <option value="2" {{ old('filter_alert',$request->filter_alert) == 2 ? 'selected' : '' }}>YES</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Customer ID -->
                        <div class="col-lg-3 col-sm-12">
                            <div class="mb-3">
                                <label for="filter_cid" class="custom-font-xsmall">Customer ID:</label><br/>
                                <input type="text" maxlength="24" class="form-control form-control-sm custom-font-xsmall" name="filter_cid" id="filter_cid" value="{{ old('filter_cid',$request->filter_cid) }}" placeholder="">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <label for="filter_ifearea" class="custom-font-xsmall">IFE Area Code:</label><br/>
                            <select class="js-select2-area_city form-control custom-font-small" name="filter_ifearea" id="filter_ifearea" style="width:100%;">
                                <option value="">-- Select IFE Area --</option>
                                @foreach($ifeareas as $c)
                                    <option value="{{ $c->id }}" {{ old('filter_ifearea', $request->filter_ifearea) == $c->id ? 'selected' : '' }}>{{ $c->area }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <!-- source -->
                            <div class="mb-3">
                                <label for="filter_source" class="custom-font-xsmall">Source:</label><br/>
                                <select class="js-select2-source form-control form-control-sm custom-font-xsmall" name="filter_source" id="filter_source" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach(\App\Helpers\Helper::getLeadSourceListing() as $key => $leadsource)
                                    <option value="{{ $key }}" {{ old('filter_source',$request->filter_source) == $key ? 'selected' : '' }}>{{ $leadsource }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- creator -->
                            <div class="mb-3">
                                <label for="filter_creator" class="custom-font-xsmall">Creator:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_creator" id="filter_creator" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_creator',$request->filter_creator) == $u->id) selected @endif>{{ $u->name }}</option>                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- checker -->
                            <div class="mb-3">
                                <label for="filter_checker" class="custom-font-xsmall">Checker:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_checker" id="filter_checker" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_checker',$request->filter_checker) == $u->id) selected @endif>{{ $u->name }}</option>                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- owner -->
                            <div class="mb-3">
                                <label for="filter_owner" class="custom-font-xsmall">Owner:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_owner" id="filter_owner" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_owner',$request->filter_owner) == $u->id) selected @endif>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- viewer -->
                            <div class="mb-3">
                                <label for="filter_viewer" class="custom-font-xsmall">Viewer:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_viewer" id="filter_viewer" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_viewer',$request->filter_viewer) == $u->id) selected @endif>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- busienss category -->
                        <div class="col-lg-3 col-sm-12">
                            <div class="mb-3">
                                <label for="filter_business_category" class="custom-font-xsmall">Business Category:</label><br/>
                                <select class="js-select2-business-category form-control form-control form-control-sm custom-font-xsmall" name="filter_business_category" id="filter_business_category" style="width:100%">
                                    <option value="">-- @lang('translation.select_company_type') --</option>
                                    <option value="1" {{ old('filter_business_category' , $request->filter_business_category) == '1' ? 'selected' : '' }}>Bar</option>
                                    <option value="2" {{ old('filter_business_category' , $request->filter_business_category) == '2' ? 'selected' : '' }}>Cafe</option>
                                    <option value="3" {{ old('filter_business_category' , $request->filter_business_category) == '3' ? 'selected' : '' }}>Hotel</option>
                                    <option value="4" {{ old('filter_business_category' , $request->filter_business_category) == '4' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- last updated date -->
                        <div class="col-lg-3 col-sm-12">
                            <div class="mb-3">
                                <label for="updated_date_start" class="custom-font-xsmall">Last Updated Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="updated_date_start" id="updated_date_start" placeholder="Start Date" value="{{ old('updated_date_start',$request->updated_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="updated_date_end" id="updated_date_end" placeholder="End Date" value="{{ old('updated_date_end',$request->updated_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <!-- submission date -->
                            <div class="mb-3">
                                <label for="start" class="custom-font-xsmall">@lang('translation.submission_date'):</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6" style="z-index:1000;">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="start" id="start" placeholder="Start Date" value="{{ old('start',$request->start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="end" id="end" placeholder="End Date" value="{{ old('end',$request->end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- complete date -->
                            <div class="mb-3">
                                <label for="complete_date_start" class="custom-font-xsmall">@lang('translation.complete_date'):</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="complete_date_start" id="complete_date_start" placeholder="Start Date" value="{{ old('complete_date_start',$request->complete_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="complete_date_end" id="complete_date_end" placeholder="End Date" value="{{ old('complete_date_end',$request->complete_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- inprogress date -->
                            <div class="mb-3">
                                <label for="inprogress_date_start" class="custom-font-xsmall">In Progress Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="inprogress_date_start" id="inprogress_date_start" placeholder="Start Date" value="{{ old('inprogress_date_start',$request->inprogress_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="inprogress_date_end" id="inprogress_date_end" placeholder="End Date" value="{{ old('inprogress_date_end',$request->inprogress_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- done date -->
                            <div class="mb-3">
                                <label for="done_date_start" class="custom-font-xsmall">Done Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="done_date_start" id="done_date_start" placeholder="Start Date" value="{{ old('done_date_start',$request->done_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="done_date_end" id="done_date_end" placeholder="End Date" value="{{ old('done_date_end',$request->done_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- verify date -->
                            <div class="mb-3">
                                <label for="verify_date_start" class="custom-font-xsmall">Verified Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="verify_date_start" id="verify_date_start" placeholder="Start Date" value="{{ old('verify_date_start',$request->verify_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="verify_date_end" id="verify_date_end" placeholder="End Date" value="{{ old('verify_date_end',$request->verify_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- reject date -->
                            <div class="mb-3">
                                <label for="reject_date_start" class="custom-font-xsmall">Rejected Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="reject_date_start" id="reject_date_start" placeholder="Start Date" value="{{ old('reject_date_start',$request->reject_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="reject_date_end" id="reject_date_end" placeholder="End Date" value="{{ old('reject_date_end',$request->reject_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- KIV date -->
                            <div class="mb-3">
                                <label for="kiv_date_start" class="custom-font-xsmall">KIV Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="kiv_date_start" id="kiv_date_start" placeholder="Start Date" value="{{ old('kiv_date_start',$request->kiv_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="kiv_date_end" id="kiv_date_end" placeholder="End Date" value="{{ old('kiv_date_end',$request->kiv_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- on hold date -->
                            <div class="mb-3">
                                <label for="onhold_date_start" class="custom-font-xsmall">On Hold Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="onhold_date_start" id="onhold_date_start" placeholder="Start Date" value="{{ old('onhold_date_start',$request->onhold_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="onhold_date_end" id="onhold_date_end" placeholder="End Date" value="{{ old('onhold_date_end',$request->onhold_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
            </div>
        </div>

        <hr />

        <input type="hidden" name="status" id="status" value="{{ old('status',$request->status) }}">

        <div class="d-flex justify-content-between nav-pills" style="overflow-x:auto; white-space: nowrap;">
            <div>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 1 ? 'active' : 'inactive' }}" onclick="filter_data(1)">
                        <span>New Task{{ ($all->where('status','1')->first() !== NULL && $all->where('status','1')->first()->cnt > 0)  ? ' ( '.$all->where('status','1')->first()->cnt.' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 2 ? 'active' : 'inactive' }}" onclick="filter_data(2)">
                        <span>Inprogress{{ ($all->where('status','2')->first() !== NULL && $all->where('status','2')->first()->cnt > 0)  ? ' ( '.$all->where('status','2')->first()->cnt.' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 8 ? 'active' : 'inactive' }}" onclick="filter_data(8)">
                        <span>On Hold{{ ($all->where('status','8')->first() !== NULL && $all->where('status','8')->first()->cnt > 0)  ? ' ( '.$all->where('status','8')->first()->cnt.' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 3 ? 'active' : 'inactive' }}" onclick="filter_data(3)">
                        <span>Done{{ ($all->where('status','3')->first() !== NULL && $all->where('status','3')->first()->cnt > 0)  ? ' ( '.$all->where('status','3')->first()->cnt.' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 4 ? 'active' : 'inactive' }}" onclick="filter_data(4)">
                        <span>Verified{{ ($all->where('status','4')->first() !== NULL && $all->where('status','4')->first()->cnt > 0)  ? ' ( '.$all->where('status','4')->first()->cnt.' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 5 ? 'active' : 'inactive' }}" onclick="filter_data(5)">
                        <span>Completed{{ ($all->where('status','5')->first() !== NULL && $all->where('status','5')->first()->cnt > 0)  ? ' ( '.$all->where('status','5')->first()->cnt.' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 6 ? 'active' : 'inactive' }}" onclick="filter_data(6)">
                        <span>Keep In View{{ ($all->where('status','6')->first() !== NULL && $all->where('status','6')->first()->cnt > 0)  ? ' ( '.$all->where('status','6')->first()->cnt.' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 7 ? 'active' : 'inactive' }}" onclick="filter_data(7)">
                        <span>Rejected{{ ($all->where('status','7')->first() !== NULL && $all->where('status','7')->first()->cnt > 0)  ? ' ( '.$all->where('status','7')->first()->cnt.' )' : '' }}</span>
                    </a>
                </span>
            </div>
            <div class="d-flex">
                <div style="padding-top: 10px; width: 250px;">
                    <select class="js-sortby form-control form-control-sm custom-font-xsmall" name="sortby" id="sortby" onchange="refreshPage()">
                        <option value="last_follow_up" @if (old('sortby',$request->sortby) == 'last_follow_up') selected @endif>Sort By Last Updated Date</option>
                        <option value="reminder_date" @if (old('sortby',$request->sortby) == 'reminder_date') selected @endif>Sort By Reminder Date</option>
                        <option value="due_date" @if (old('sortby',$request->sortby) == 'due_date') selected @endif>Sort By Due Date</option>
                        <option value="created_at" @if (old('sortby',$request->sortby) == 'created_at') selected @endif>Sort By Created Date</option>
                        <option value="inprogress_date" @if (old('sortby',$request->sortby) == 'inprogress_date') selected @endif>Sort By In Progress Date</option>
                        <option value="complete_date" @if (old('sortby',$request->sortby) == 'complete_date') selected @endif>Sort By In Completed Date</option>
                        <option value="kiv_date" @if (old('sortby',$request->sortby) == 'kiv_date') selected @endif>Sort By KIV Date</option>
                        <option value="reject_date" @if (old('sortby',$request->sortby) == 'reject_date') selected @endif>Sort By Rejected Date</option>
                        <option value="onhold_date" @if (old('sortby',$request->sortby) == 'onhold_date') selected @endif>Sort By On Hold Date</option>
                    </select>
                </div>
                <div style="padding-top: 10px; width: 80px;">
                    <select class="js-sortmode form-control form-control-sm custom-font-xsmall" name="sortmode" id="sortmode" onchange="refreshPage()">
                        <option value="desc" @if (old('sortmode',$request->sortmode) == 'desc') selected @endif>🔽 Desc</option>
                        <option value="asc" @if (old('sortmode',$request->sortmode) == 'asc') selected @endif>🔼 Asc</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <div style="overflow-x:auto; white-space: nowrap;">
        <table class="task-table-compact table" style="width:100%; border-collapse: collapse; box-shadow: 1px 1px 3px 1px rgb(183, 183, 183);">
            <thead class="table-dark">
                <tr>
                    <td scope="col">#</td>
                    <td scope="col">Lead/Customer Information</td>
                    <td scope="col">Task Information</td>
                    <td scope="col">Manage By</td>
                    <td scope="col">Status</td>
                    <td scope="col" style="text-align: center; width:100px"></td>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $index => $s)
                    <tr>
                        <td style="background-color:{{ $s->alert == 2 ? '#f4c7c7ff' : 'white' }}; border-bottom: 1px solid #acacac;" scope="row">
                            {{ ( ($tasks->links()->paginator->currentPage()-1) * $tasks->links()->paginator->perPage() ) + $loop->iteration }}
                        </td>
                        <td style="background-color:{{ $s->alert == 2 ? '#f4c7c7ff' : 'white' }}; border-bottom: 1px solid #acacac;" class="custom-font-xsmall">
                            <span style="text-transform: uppercase;">
                                @if ($s->lead->customer_id)
                                    <img class="rounded-circle header-profile-user" src="{{ URL::asset('/assets/images/users/cartoon-boy-profile.png') }}" style="height:25px; width:auto;" alt="Header Avatar">
                                @endif
                                {{ $s->lead->name }}
                            </span>
                            <table>
                                <tr>
                                    <td>Shop Name</td>
                                    <td style="width:1px;">:</td>
                                    <td><font class="custom-table-tr-td-font-color">{{ $s->lead->business_name }}</font></td>
                                </tr>
                                @if($s->lead->ife_area_id)
                                <tr>
                                    <td>IFE Area Code</td>
                                    <td style="width:1px;">:</td>
                                    <td><font class="custom-table-tr-td-font-color">{{ $s->lead->ifearea->area }}</font></td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Customer ID</td>
                                    <td style="width:1px;">:</td>
                                    <td><font class="custom-table-tr-td-font-color">{{ $s->lead->customer_id }}</font></td>
                                </tr>
                                <tr>
                                    <td>@lang('translation.mobile')</td>
                                    <td style="width:1px;">:</td>
                                    <td><font class="custom-table-tr-td-font-color">{{ $s->lead->mobile }}</font></td>
                                </tr>
                                <tr>
                                    <td>Lead/Customer Source</td>
                                    <td style="width:1px;">:</td>
                                    <td><font class="custom-table-tr-td-font-color">{{ \App\Helpers\Helper::getLeadSource($s->lead->source) }}</font></td>
                                </tr>
                                <tr>
                                    <td>Sales Amount</td>
                                    <td style="width:1px;">:</td>
                                    <td>
                                        @if($s->sales && $s->sales > 0)
                                        <div style="cursor: pointer; padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(7, 111, 36); color: white; width: fit-content; border-width: thin; border-style: dashed; border-color: yellow;">
                                            {{ $s->sales ? 'RM ' . number_format($s->sales,2) : '' }}
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="background-color:{{ $s->alert == 2 ? '#f4c7c7ff' : 'white' }}; border-bottom: 1px solid #acacac;" class="custom-font-xsmall">
                            <table>
                                <tr>
                                    <td>Title</td>
                                    <td style="width:1px;">:</td>
                                    <td><div style="width:20vw;" class="overflowText custom-table-tr-td-font-color">{{ $s->title }}</div></td>
                                </tr>
                                <tr>
                                    <td>@lang('translation.reference_no')</td>
                                    <td style="width:1px;">:</td>
                                    <td><font class="custom-table-tr-td-font-color">{{ $s->task_reference }}</font></td>
                                </tr>
                                <tr>
                                    <td>Due Date</td>
                                    <td style="width:1px;">:</td>
                                    <td>
                                        <font class="custom-table-tr-td-font-color" style="color: red;">
                                        <div style="padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(158, 18, 37); color: white; width: fit-content;">
                                            {{ $s->due_date ? date('Y-m-d, h:i A', strtotime($s->due_date . ' ' . $s->due_time)) : '' }}
                                        </div>
                                        </font>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Appointment Date</td>
                                    <td style="width:1px;">:</td>
                                    <td>
                                        <font class="custom-table-tr-td-font-color" style="color: red;">
                                        <div style="padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(18, 51, 158); color: white; width: fit-content;">
                                            {{ $s->appointment_date ? date('Y-m-d, h:i A', strtotime($s->appointment_date)) : '' }}
                                        </div>
                                        </font>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reminder Date</td>
                                    <td style="width:1px;">:</td>
                                    <td>
                                        @php
                                            $r = $s->reminder->where('user_id',Auth::guard('web')->user()->id);
                                        @endphp
                                        <font class="custom-table-tr-td-font-color" style="color: red;">
                                        <div style="padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(198, 134, 7); color: white; width: fit-content;">
                                            {{ $r->count() > 0 ? date('Y-m-d, h:i A', strtotime(Carbon\Carbon::parse($r->first()->reminder_date . ' ' . $r->first()->reminder_time))) : '' }}
                                        </div>
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="background-color:{{ $s->alert == 2 ? '#f4c7c7ff' : 'white' }}; border-bottom: 1px solid #acacac;" class="custom-font-xsmall">
                            <table>
                                <tr>
                                    <td>Subscriber</td>
                                    <td style="width:1px;">:</td>
                                    <td>
                                        @php 
                                            $find = $s->rating->where('user_id',$s->users->where('role',2)->first()->user_id)->first();
                                            $u    = $s->users->where('role',2)->first();
                                        @endphp
                                        <font class="custom-table-tr-td-font-color">
                                        <div onclick="appointmentList('{{ $u->user->name }}','{{ $s->id }}','{{ $u->user_id }}','{{ $u->role }}')" style="cursor: pointer; padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(7, 111, 36); color: white; width: fit-content; border-width: thin; border-style: dashed; border-color: yellow;">
                                            {{ $s->users->where('role',2)->first()->user->name }} <span style="color:rgb(248, 244, 3);">{{ $find ? '('.$find->rate.')' : '' }}</span>
                                        </div>
                                        </font>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top">Sub-Subscriber(s)</td>
                                    <td valign="top">:</td>
                                    <td>
                                        <font class="custom-table-tr-td-font-color">
                                        @foreach($s->users->where('role',6) as $u)
                                        @php 
                                            $find = $s->rating->where('user_id',$u->user_id)->first();
                                        @endphp
                                        <div onclick="appointmentList('{{ $u->user->name }}','{{ $s->id }}','{{ $u->user_id }}','{{ $u->role }}')" style="cursor: pointer; padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(7, 111, 36); color: white; width: fit-content; border-width: thin; border-style: dashed; border-color: yellow;">
                                            {{ $u->user->name }} <span style="color:rgb(248, 244, 3);">{{ $find ? '('.$find->rate.')' : '' }}</span>
                                        </div>
                                        @endforeach
                                        </font>
                                    </td>
                                </tr>
                                <?php 
                                    $name = [];
                                    foreach($s->users->where('role',4) as $u) {
                                        array_push($name, $u->user->name);
                                    }
                                    $name = implode(', ',$name);
                                ?>
                                <tr>
                                    <td>Owner(s)</td>
                                    <td>:</td>
                                    <td><font class="custom-table-tr-td-font-color">{{ $name }}</font></td>
                                </tr>
                            </table>
                        </td>
                        <td style="background-color:{{ $s->alert == 2 ? '#f4c7c7ff' : 'white' }}; border-bottom: 1px solid #acacac;" class="custom-font-xsmall">
                            
                            <span style="color:blue;">Created</span> on <br/>
                            {{ date('Y M d h:i A', strtotime($s->created_at)) }} <br/><br/>
                            
                            @if($s->status == 2)
                                <span style="color:blue;">{{ $s->getTaskStatus($s->status) }}</span> on <br/>
                                {{ date('Y M d h:i A', strtotime($s->inprogress_date)) }} <br/>

                            @elseif($s->status == 3)
                                <span style="color:blue;">{{ $s->getTaskStatus($s->status) }}</span> on <br/>
                                {{ date('Y M d h:i A', strtotime($s->done_date)) }} <br/>

                            @elseif($s->status == 4)
                                <span style="color:blue;">{{ $s->getTaskStatus($s->status) }}</span> on <br/>
                                {{ date('Y M d h:i A', strtotime($s->verify_date)) }} <br/>

                            @elseif($s->status == 5)
                                <span style="color:blue;">{{ $s->getTaskStatus($s->status) }}</span> on <br/>
                                {{ date('Y M d h:i A', strtotime($s->complete_date)) }} <br/>

                            @elseif($s->status == 6)
                                <span style="color:blue;">{{ $s->getTaskStatus($s->status) }}</span> on <br/>
                                {{ date('Y M d h:i A', strtotime($s->kiv_date)) }} <br/>

                            @elseif($s->status == 7)
                                <span style="color:blue;">{{ $s->getTaskStatus($s->status) }}</span> on <br/>
                                {{ date('Y M d h:i A', strtotime($s->reject_date)) }} <br/>

                            @elseif($s->status == 8)
                                <span style="color:blue;">{{ $s->getTaskStatus($s->status) }}</span> on <br/>
                                {{ date('Y M d h:i A', strtotime($s->onhold_date)) }} <br/>

                            @endif

                            <div style="margin-top: 10px; color:red;">Last Updated on</div>
                            <div style="margin-top:5px; padding-left: 10px; padding-right: 10px; border-radius: 25px; border-width: thin; border-style: dashed; border-color: rgb(248, 188, 110); background:rgb(18, 51, 158); color: white; width: fit-content;">
                                {{ date('Y-m-d, h:i A', strtotime($s->last_follow_up)) }}
                            </div>
                            @if($s->comments->count() > 0)
                            <div style="margin-top:5px; padding-left: 10px; padding-right: 10px; border-radius: 25px; border-width: thin; border-style: dashed; border-color: rgb(248, 188, 110); background:rgb(3, 60, 246); color: white; width: fit-content;">
                                {{ $s->comments->count() > 0 ? 'By ' . $s->comments->sortByDesc('created_at')->first()->submitBy->name : '' }}<br>    
                            </div>
                            @endif

                            @if($s->has_unread_notification->count() > 0)
                            <div style="margin-top:5px; padding-left: 10px; padding-right: 10px; border-radius: 25px; border-width: thin; border-style: dashed; border-color: black; background:rgb(255, 221, 0); color: black; width: fit-content;">unread message : {{ $s->has_unread_notification->count() }}</div>
                            @endif
                        </td>
                        <td style="background-color:{{ $s->alert == 2 ? '#f4c7c7ff' : 'white' }}; border-bottom: 1px solid #acacac; vertical-align: top;">
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px; padding: 4px 6px;">
                                <div style="display: flex; flex-wrap: wrap; justify-content: flex-end; align-items: center; gap: 6px;">
                                    @if($s->status == 1 && $s->users->whereIn('role',[1,4])->where('user_id',Auth::guard('web')->user()->id)->count() > 0)
                                    <button type="button" style="border: none; background-color: transparent; padding: 0; line-height: 1;" onclick="deleteTask({{ $s->id }})"><i class="mdi mdi-trash-can" style="color:red; font-size: 22px;"></i></button>
                                    @endif

                                    @if($s->status == 2 && Auth::guard('web')->user()->type == 1)
                                    <button type="button" style="border: none; background-color: transparent; padding: 0; line-height: 1;" onclick="deleteTask({{ $s->id }})"><i class="mdi mdi-trash-can" style="color:red; font-size: 22px;"></i></button>
                                    @endif

                                    <a href="view/{{ $s->id }}?mode=comment&{{ $queryString }}" style="line-height: 1;"><i class="mdi mdi-forum-outline" style="font-size: 22px;"></i></a>

                                    @if($s->status == 1 || $s->status == 2 || $s->status == 3 || $s->status == 4 || $s->status == 8)
                                        @if($s->users->whereIn('role',[1,3,4])->where('user_id',Auth::guard('web')->user()->id)->count() > 0 || Auth::guard('web')->user()->type == 0)
                                            <a href="edit/{{ $s->id }}?{{ $queryString }}" style="line-height: 1;"><i class="mdi mdi-square-edit-outline" style="font-size: 22px;"></i></a>
                                        @endif
                                        <a href="view/{{ $s->id }}?{{ $queryString }}" style="line-height: 1;"><i class="mdi mdi-clipboard-outline" style="font-size: 22px;"></i></a>
                                    @else
                                        <a href="view/{{ $s->id }}?{{ $queryString }}" style="line-height: 1;"><i class="mdi mdi-clipboard-outline" style="font-size: 22px;"></i></a>
                                    @endif

                                    @if( ($s->status == 6 && $s->users->whereIn('role',[4])->where('user_id',Auth::guard('web')->user()->id)->count() > 0) ||
                                         ($s->status == 6 && Auth::guard('web')->user()->type == 1) ||
                                         ($s->status == 6 && Auth::guard('web')->user()->type == 2)
                                    )
                                    <a href="edit/{{ $s->id }}?{{ $queryString }}" style="line-height: 1;"><i class="mdi mdi-recycle-variant" style="color:red; font-size: 22px;"></i></a>
                                    @endif

                                    @if( ($s->status == 8 && $s->users->whereIn('role',[2])->where('user_id',Auth::guard('web')->user()->id)->count() > 0) ||
                                         ($s->status == 8 && Auth::guard('web')->user()->type == 1) ||
                                         ($s->status == 8 && Auth::guard('web')->user()->type == 2)
                                    )
                                    <button type="button" style="border: none; background-color: transparent; padding: 0; line-height: 1;" onclick="reactivateTask({{ $s->id }})"><i class="mdi mdi-recycle-variant" style="color:red; font-size: 22px;"></i></button>
                                    @endif
                                </div>

                                <a download href="{{ route('tasks.export_single_task',['id'=>$s->id]) }}" style="width:70px; font-size: 8pt;" class="btn btn-sm btn-success custom-button-shadow">Excel</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $tasks->withQueryString()->links() }}
</div>

<!-- The Modal -->
<div id="myifearea" class="modal mt-3" style="z-index:50;">
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

<!-- The Modal -->
<div id="myModal" class="modal mt-3" style="z-index:50;">
    <span class="tutup" style="padding-right:30px;">&times;</span>
    <p id="content"></p>
</div>

@section('script')
<script>
    $(".js-select2-source").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-select2-users").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-sortby").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-sortmode").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-select2-business-category").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-select2-with-sales").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-leadname-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-leadbusiness-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-alertind").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $("#filter_ifearea").select2({ dropdownCssClass: "small", containerCssClass: "small" });

    $("#querystring").click(function(){
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
    $("#complete_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#complete_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#inprogress_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#inprogress_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#reject_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#reject_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#kiv_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#kiv_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#onhold_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#onhold_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#appointment_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#appointment_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#done_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#done_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#verify_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#verify_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });

    $("#start").change(function(){
        $('#status').val(1);

        // $("#start").val('');
        // $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#end").change(function(){
        $('#status').val(1);

        // $("#start").val('');
        // $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#done_date_start").change(function(){
        $('#status').val(3);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        // $("#done_date_start").val('');
        // $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#done_date_end").change(function(){
        $('#status').val(3);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        // $("#done_date_start").val('');
        // $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#reject_date_start").change(function(){
        $('#status').val(7);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        // $("#reject_date_start").val('');
        // $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#reject_date_end").change(function(){
        $('#status').val(7);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        // $("#reject_date_start").val('');
        // $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#inprogress_date_start").change(function(){
        $('#status').val(2);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        // $("#inprogress_date_start").val('');
        // $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#inprogress_date_end").change(function(){
        $('#status').val(2);
        
        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        // $("#inprogress_date_start").val('');
        // $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#complete_date_start").change(function(){
        $('#status').val(5);

        $("#start").val('');
        $("#end").val('');

        // $("#complete_date_start").val('');
        // $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#complete_date_end").change(function(){
        $('#status').val(5);

        $("#start").val('');
        $("#end").val('');

        // $("#complete_date_start").val('');
        // $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#kiv_date_start").change(function(){
        $('#status').val(6);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        // $("#kiv_date_start").val('');
        // $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#kiv_date_end").change(function(){
        $('#status').val(6);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        // $("#kiv_date_start").val('');
        // $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#verify_date_start").change(function(){
        $('#status').val(4);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        // $("#verify_date_start").val('');
        // $("#verify_date_end").val('');
    });
    $("#verify_date_end").change(function(){
        $('#status').val(4);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        // $("#verify_date_start").val('');
        // $("#verify_date_end").val('');
    });
 
    $('#sortby').change(function() {
        if ($( "#sortby option:selected" ).text() == 'Sort By Due Date') {
            $("select#sortmode").prop('selectedIndex', 1);
        }
    });

    $( document ).ready(function() {
        // setInterval(function() {
        //     location.reload();
        // }, 60 * 1000);
    });

    function deleteTask(id) {
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
                    url: "{{ route('tasks.delete') }}",
                    data: {"_token": "{{ csrf_token() }}",'id': id },
                    cache: false,
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        })
    }

    function reactivateTask(id) {
        new swal({
            title: 'Please confirm to reactivate this task!',
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
                    url: "{{ route('tasks.reactivate') }}",
                    data: {"_token": "{{ csrf_token() }}",'id': id },
                    cache: false,
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        })
    }

    function filter_data(status) {
        $('#status').val(status);
        document.forms['filter'].submit();
    }

    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }

    const tasks = ({!! json_encode($tasks->toArray()['data']) !!})

    // json_encode($u->user->active_appointment 

    function appointmentList(subscriber, t, u, r) {
        let text = '<div class="center">';
        
        text += "<span><b><u>Appointment List of " + subscriber + "</u></b></span><br><br>";

        let entries = Object.values(tasks);
        let selectedTask = entries.filter(task => task['id'] == t)[0];
        let selectedUser = selectedTask.users.filter(user => user.user_id == u && user.role == r)[0];
        let appointments = selectedUser.user.active_appointment;

        // console.log(selectedTask.users);
        // console.log(selectedUser.user.active_appointment);

        for (let i = 0; i < appointments.length; i++) {
            text += (i+1) + '. <span style="color:red;">' + appointments[i][0] + "</span> (" + appointments[i][1] + ")<br>";
        }
        
        text += "</div>";

        document.getElementById("content").innerHTML = text;

        var modal = document.getElementById("myModal");
        modal.style.display  = "block";
        
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("tutup")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() { 
            modal.style.display = "none";
        }
    }

    function refreshPage() {
        document.getElementById("btnSearch").click();
    }

    
    // Get the modal
    var modal = document.getElementById("myifearea");

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