<style>
    #myImg:hover {
        opacity: 0.7;
    }

    .chat-input {
        border-radius: 30px;
        background-color: #f5f6f8 !important;
        border-color: #cfcfcfff !important;
    }

    /* The Modal (background) */
    .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    padding-top: 180px; /* Location of the box */
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
    top: 125px;
    right: 135px;
    color: #f1f1f1;
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

<div class="card2" style="padding:15px;">
    <?php 
        $status = '';
        switch ($task->status) {
            case '1':
                $status = 'New Task';
                break;

            case '2':
                $status = 'In Progress';
                break;

            case '3':
                $status = 'Done';
                break;

            case '4':
                $status = 'Verified';
                break;

            case '5':
                $status = 'Completed';
                break;
            
            case '6':
                $status = 'KIV';
                break;

            case '7':
                $status = 'Rejected';
                break;

            case '8':
                $status = 'On Hold';
                break;
        }
    ?>

    <div class="row">
        <div class="col-md-3 col-xl-3" style="padding-bottom:4px;">
            <div><span style="vertical-align: bottom;">Task Reference</span></div>
            <div><span style="font-size:11pt; vertical-align: top;"><b>{{ $task->task_reference }}</b></span><span style="color:rgb(214, 11, 0);"> ({{ $status }})</span></div>
        </div>

        <div class="col-md-3 col-xl-3" style="padding-bottom:4px;">
            <div><span style="vertical-align: bottom;">Lead/Customer Name</span></div>
            <div><span style="font-size:11pt; vertical-align: top;"><b>{{ $task->lead->name }}</b>{{ $task->lead->customer_id ? ' (' . $task->lead->customer_id . ')' : '' }}</span></div>
            @if($task->lead->business_name)
            <div><span style="font-size:10pt; vertical-align: top;">{{ $task->lead->business_name }}</span></div>
            @endif
        </div>

        <div class="col-md-3 col-xl-3" style="padding-bottom:4px;">
            <div><span style="vertical-align: bottom;">Lead/Customer Mobile</span></div>
            <div><span style="font-size:11pt; vertical-align: top;"><b>{{ $task->lead->mobile }}</b></span></div>
        </div>

        <div class="col-md-3 col-xl-3" style="padding-bottom:4px;">
            <div><span style="vertical-align: bottom;">Subscriber</span></div>
            <div><span style="font-size:11pt; vertical-align: top;"><b>{{ $task->users->where('role',2)->first() ? $task->users->where('role',2)->first()->user->name : '' }}</b></span></div>
        </div>
    </div>
</div>

<div style="margin-top:5px; margin-bottom:40px;">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tabTask" data-bs-toggle="tab" href="#task" role="tab" aria-selected="true">
                <span class="d-block d-sm-none">Task Information</span>
                <span class="d-none d-sm-block">Task Information</span>
            </a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link" id="tabReminder" data-bs-toggle="tab" href="#reminder" role="tab" aria-selected="false">
                <span class="d-block d-sm-none">Reminder</span>
                <span class="d-none d-sm-block">Reminder</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tabHistory" data-bs-toggle="tab" href="#history" role="tab" aria-selected="false">
                <span class="d-block d-sm-none">History</span>
                <span class="d-none d-sm-block">History</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tabComment" data-bs-toggle="tab" href="#comment" role="tab" aria-selected="false" onclick="scrollToBottom()">
                <span class="d-block d-sm-none">Comment</span>
                <span class="d-none d-sm-block">Comment</span>
            </a>
        </li> --}}
    </ul>

    <!-- Tab panes -->
    <div class="tab-content text-muted" style="margin-top:2px;">
        <div class="tab-pane active" id="task" role="tabpanel">
            <form action="" method="post" id="edit_case_form" name="edit_case_form" enctype="multipart/form-data" autocomplete="off">
                <div>
                    {{csrf_field()}}
                    <input type="hidden" name="special_remark" id="special_remark">
                    <input type="hidden" name="task_id" id="task_id"  value="{{ $task->id }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="page" id="page" value="{{ $request->page }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="status" id="status" value="{{ $request->status }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="sortby" id="sortby" value="{{ $request->sortby }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_title" id="filter_title" value="{{ $request->filter_title }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_reference_no" id="filter_reference_no" value="{{ $request->filter_reference_no }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_subscriber" id="filter_subscriber" value="{{ $request->filter_subscriber }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_alert" id="filter_alert" value="{{ $request->filter_alert }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_subsubscriber" id="filter_subsubscriber" value="{{ $request->filter_subsubscriber }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_source" id="filter_source" value="{{ $request->filter_source }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_creator" id="filter_creator" value="{{ $request->filter_creator }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_checker" id="filter_checker" value="{{ $request->filter_checker }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_owner" id="filter_owner" value="{{ $request->filter_owner }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_viewer" id="filter_viewer" value="{{ $request->filter_viewer }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_business_category" id="filter_business_category" value="{{ $request->filter_business_category }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="start" id="start" value="{{ $request->start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="end" id="end" value="{{ $request->end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="complete_date_start" id="filter_sucomplete_date_startbscriber" value="{{ $request->complete_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="complete_date_end" id="complete_date_end" value="{{ $request->complete_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="inprogress_date_start" id="inprogress_date_start" value="{{ $request->inprogress_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="inprogress_date_end" id="inprogress_date_end" value="{{ $request->inprogress_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="done_date_start" id="done_date_start" value="{{ $request->done_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="done_date_end" id="done_date_end" value="{{ $request->done_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="verify_date_start" id="verify_date_start" value="{{ $request->verify_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="verify_date_end" id="verify_date_end" value="{{ $request->verify_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="reject_date_start" id="reject_date_start" value="{{ $request->reject_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="reject_date_end" id="reject_date_end" value="{{ $request->reject_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="kiv_date_start" id="kiv_date_start" value="{{ $request->kiv_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="kiv_date_end" id="kiv_date_end" value="{{ $request->kiv_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="onhold_date_start" id="onhold_date_start" value="{{ $request->onhold_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="onhold_date_end" id="onhold_date_end" value="{{ $request->onhold_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="appointment_date_start" id="appointment_date_start" value="{{ $request->appointment_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="appointment_date_end" id="appointment_date_end" value="{{ $request->appointment_date_end }}">

                    <!-- #1 Task Detail -->
                    <div>
                        <div style="padding:5px; background-color:rgb(13, 114, 158); color: white; border-color:white;">
                            Task Detail
                        </div>
                        <div class="m-2">
                            <div class="row">
                                <!-- Salesperson -->
                                @if($task->users->where('role',4)->where('user_id',Auth::guard('web')->user()->id)->first() ||
                                    Auth::guard('web')->user()->type == 0
                                )
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="subscriber" class="custom-font-xsmall"><b>Subscriber</b> :</label>
                                    <span class="pl-3" style="padding-left: 5px; color:red;">*</span>
                                    <span class="text-danger">@if (!empty($errors->first('subscriber'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('subscriber') }}</span>
                                    <select class="form-control form-select custom-font-small js-subscriber" name="subscriber" id="subscriber" style="width:100%;">
                                        <option value="">-- @lang('translation.select_your_choice') --</option>
                                            @foreach($subscriber as $c)
                                            <option value="{{ $c->id }}" {{ old('subscriber', $task->users->where('role',2)->first()->user_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                            @endforeach
                                    </select>
                                </div>
                                @else
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="subscriber" class="custom-font-xsmall"><b>Subscriber</b> :</label>
                                    <input type="hidden" class="form-control form-control-sm custom-font-small" name="subscriber" id="subscriber" value="{{ $task->users->where('role',2)->first() ? $task->users->where('role',2)->first()->user->id : '' }}" readonly>
                                    <input type="text" class="form-control form-control-sm custom-font-small" value="{{ $task->users->where('role',2)->first() ? $task->users->where('role',2)->first()->user->name : '' }}" readonly>
                                </div>
                                @endif

                                <!-- Sub-Subscriber -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="sub_subscriber" class="custom-font-xsmall"><b>Sub-Subscriber(s)</b> :</label>
                                    <select class="form-control form-select custom-font-small js-subsubscriber-multiple" name="sub_subscriber[]" id="sub_subscriber" multiple style="width:100%;">
                                        @foreach($subscriber as $c)
                                        <option value="{{ $c->id }}" {{ $task->users->where('role',6)->where('user_id',$c->id)->first() !== null ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Owner -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="owner" class="custom-font-xsmall"><b>Owner(s)</b> :</label>
                                    <span class="pl-3" style="padding-left: 5px; color:red;">*</span>
                                    <span class="text-danger">@if (!empty($errors->first('owner'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('owner') }}</span>
                                    <select class="form-control form-select custom-font-small js-owner-multiple" name="owner[]" id="owner" multiple style="width:100%;">
                                        @foreach($owner as $c)
                                        <option value="{{ $c->id }}" {{ $task->users->where('role',4)->where('user_id',$c->id)->first() !== null ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Viewer -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="viewer" class="custom-font-xsmall"><b>Viewer(s)</b> :</label>
                                    <select class="form-control form-select custom-font-small js-viewer-multiple" name="viewer[]" id="viewer" multiple style="width:100%;">
                                        @foreach($viewer as $c)
                                        <option value="{{ $c->id }}" {{ $task->users->where('role',5)->where('user_id',$c->id)->first() !== null ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Creator -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="creator" class="custom-font-xsmall"><b>Creator</b> :</label>
                                    <input type="text" class="form-control form-control-sm custom-font-small" name="creator" id="creator" value="{{ $task->users->where('role',1)->first() ? $task->users->where('role',1)->first()->user->name : '' }}" readonly>
                                </div>

                                <!-- Checker -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="checker" class="custom-font-xsmall"><b>Checker</b> :</label>
                                    <input type="text" class="form-control form-control-sm custom-font-small" name="checker" id="checker" value="{{ $task->users->where('role',3)->first() ? $task->users->where('role',3)->first()->user->name : '' }}" readonly>
                                </div>

                                <!-- alert -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="alertind" class="custom-font-xsmall"><b>ALERT</b> :</label>
                                    <span class="text-danger">@if (!empty($errors->first('alertind'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('alertind') }}</span>
                                    <select class="form-control form-select custom-font-small js-alertind" name="alertind" id="alertind" style="width:100%;">
                                        <option value="">-- @lang('translation.select_your_choice') --</option>
                                            <option value="1" {{ old('alertind', $task->alert) == 1 ? 'selected' : '' }}>NO</option>
                                            <option value="2" {{ old('alertind', $task->alert) == 2 ? 'selected' : '' }}>YES</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Title -->
                                <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                                    <label for="title" class="custom-font-xsmall"><b>Title</b> :</label><span class="pl-3" style="padding-left: 5px; color:red;">*</span>
                                    <input class="form-control form-control-sm custom-font-small" type="text" value="{{ old('title',$task->title) }}" name="title" id="title" autocomplete="off">
                                </div>
                            </div>

                            <div class="row">
                                <!-- Start Date Time -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="task_start_date" class="custom-font-xsmall"><b>Task Start</b> :</label><span class="pl-3" style="padding-left: 5px; color:red;">*</span>
                                    <span class="text-danger">@if (!empty($errors->first('task_start_date'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('task_start_date') }}</span>
                                    <span class="text-danger">@if (!empty($errors->first('task_start_time'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('task_start_time') }}</span>
                                    <div class="row">
                                        <div class="col-md-6 col-xl-6">
                                            <input class="form-control form-control-sm custom-font-small" type="date" value="{{ old('task_start_date', $task->start_date) }}" name="task_start_date" id="task_start_date" autocomplete="off">
                                        </div>
                                        <?php
                                            $timestamp = time() + 0*60;
                                        ?>
                                        <div class="col-md-6 col-xl-6">
                                            <input class="form-control form-control-sm custom-font-small" type="time" value="{{ old('task_start_time', $task->start_time) }}" id="task_start_time" name="task_start_time" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <!-- Due Date Time -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="task_due_date" class="custom-font-xsmall"><b>Task Due</b> :</label><span class="pl-3" style="padding-left: 5px; color:red;">*</span>
                                    <span class="text-danger">@if (!empty($errors->first('task_due_date'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('task_due_date') }}</span>
                                    <span class="text-danger">@if (!empty($errors->first('task_due_time'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('task_due_time') }}</span>
                                    <div class="row">
                                        <div class="col-md-6 col-xl-6">
                                            <input class="form-control form-control-sm custom-font-small" type="date" value="{{ old('task_due_date', $task->due_date) }}" name="task_due_date" id="task_due_date" autocomplete="off">
                                        </div>
                                        <?php
                                            $timestamp = time() + 60*60;
                                        ?>
                                        <div class="col-md-6 col-xl-6">
                                            <input class="form-control form-control-sm custom-font-small" type="time" value="{{ old('task_due_time', $task->due_time) }}" id="task_due_time" name="task_due_time" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <!-- Appointment Date Time -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="task_appointment_date" class="custom-font-xsmall"><b>Apointment Date Time</b> :</label>
                                    <span class="text-danger">@if (!empty($errors->first('task_appointment_date'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('task_appointment_date') }}</span>
                                    <span class="text-danger">@if (!empty($errors->first('task_appointment_time'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('task_appointment_time') }}</span>
                                    <div class="row">
                                        <div class="col-md-6 col-xl-6">
                                            <input class="form-control form-control-sm custom-font-small" type="date"value="{{ $task->appointment_date ? Carbon\Carbon::parse($task->appointment_date)->format('Y-m-d') : '' }}" name="task_appointment_date" id="task_appointment_date" autocomplete="off">
                                        </div>
                                        <?php
                                            $timestamp = time() + 0*60;
                                        ?>
                                        <div class="col-md-6 col-xl-6">
                                            <input class="form-control form-control-sm custom-font-small" type="time" value="{{ $task->appointment_date ? Carbon\Carbon::parse($task->appointment_date)->format('H:i:s') : '' }}" id="task_appointment_time" name="task_appointment_time" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Invoice No -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="creator" class="custom-font-xsmall"><b>Invoice No</b> :</label>
                                    <input type="text" class="form-control form-control-sm custom-font-small" name="invoice_no" id="invoice_no" maxlength="100" value="{{ old('invoice_no', $task->invoice_no) }}">
                                </div>

                                <!-- Sales Figure -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="checker" class="custom-font-xsmall"><b>Sales Amount</b> :</label>
                                    <input type="number" step="any" class="form-control form-control-sm custom-font-small" name="sales" id="sales" maxlength="10" value="{{ old('sales', $task->sales) }}">
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Remark -->
                                <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                                    <label for="remark" class="custom-font-xsmall"><b>Remark</b> :</label>
                                    <textarea style="height:100px;" name="remark" id="remark" class="ckeditor form-control form-control-sm small chat-input small" autocomplete="off">{{ old('remark', $task->remark) }}</textarea>
                                </div>
                            </div> 
                        </div>
                    </div>

                    <!-- #2 Lead Detail -->
                    <div>
                        <div style="padding:5px; background-color:rgb(13, 114, 158); color: white; border-color:white;">
                            Lead/Customer Detail <span style="margin-left:5px; color: rgb(252, 245, 155);">(view only)</span>
                        </div>
                        <div class="m-2">
                            <div class="row">
                                <!-- Company Name -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="leadname" class="custom-font-xsmall"><b>Company Name</b> :</label>
                                    <input type="hidden" name="lead_id" id="lead_id" value="{{ $task->lead->id }}" readonly>
                                    <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="leadname" id="leadname" value="{{ $task->lead->name }}" readonly>
                                </div>

                                <!-- Shop Name -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="leadname" class="custom-font-xsmall"><b>Shop Name</b> :</label>
                                    <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="businessname" id="businessname" value="{{ $task->lead->business_name }}" readonly>
                                </div>

                                <!-- Customer ID -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="leadname" class="custom-font-xsmall"><b>Customer ID</b> :</label>
                                    <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="customerid" id="customerid" value="{{ $task->lead->customer_id }}" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Mobile -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="mobile" class="custom-font-xsmall"><b>@lang('translation.mobile')</b> :</label>
                                    <input type="tel" class="form-control form-control-sm custom-font-small" name="mobile" id="mobile" value="{{ $task->lead->mobile }}" readonly>
                                </div>

                                <!-- Email -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="email" class="custom-font-xsmall"><b>@lang('translation.email')</b> :</label>
                                    <input type="email" class="form-control form-control-sm custom-font-small" parsley-type="email" name="email" id="email" value="{{ $task->lead->email }}" readonly>
                                </div>

                                <!-- Source -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="leadsource_v" class="custom-font-xsmall"><b>Source of Lead/Customer</b> :</label><br/>
                                    <input class="form-control form-control-sm custom-font-small" type="text" value="{{ \App\Helpers\Helper::getLeadSource($task->lead->source) }}" name="leadsource_v" id="leadsource_v" readonly>
                                    <input class="form-control form-control-sm custom-font-small" type="hidden" value="{{ $task->lead->source }}" name="leadsource" id="leadsource" readonly>
                                </div>

                                <!-- Business Category -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="businesscat_v" class="custom-font-xsmall"><b>Business Category</b> :</label><br/>
                                    <input class="form-control form-control-sm custom-font-small" type="text" value="{{ \App\Helpers\Helper::getBusinessCategory($task->lead->business_category) }}" name="businesscat_v" id="businesscat_v" readonly>
                                    <input class="form-control form-control-sm custom-font-small" type="hidden" value="{{ $task->lead->business_category }}" name="businesscat" id="businesscat" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Address -->
                                <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                                    <label for="address" class="custom-font-xsmall"><b>@lang('translation.address')</b> :</label>
                                    <textarea type="text" rows="3" maxlength="500" class="form-control form-control-sm custom-font-small" name="address" id="address" readonly>{{ $task->lead->address }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <!-- States -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="leadstate" class="custom-font-xsmall"><b>@lang('translation.state')</b> :</label>
                                    <input class="form-control form-control-sm custom-font-small" type="text" value="{{ \App\Helpers\Helper::getState($task->lead->state_id) }}" name="leadstate" id="leadstate" readonly>
                                </div>

                                <!-- Cities -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="leadcity" class="custom-font-xsmall"><b>@lang('translation.city')</b> :</label>
                                    <input class="form-control form-control-sm custom-font-small" type="text" value="{{ \App\Helpers\Helper::getCity($task->lead->city_id) }}" name="leadcity" id="leadcity" readonly>
                                </div>

                                <!-- Postcode -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="leadpostcode" class="custom-font-xsmall"><b>@lang('translation.postcode')</b> :</label>
                                    <input class="form-control form-control-sm custom-font-small" type="text" value="{{ $task->lead->postcode }}" name="leadpostcode" id="leadpostcode" readonly>
                                </div>

                                <!-- IFE Area -->
                                <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                                    <label for="ifearea" class="custom-font-xsmall"><b>IFE Area</b> :</label><span class="text-danger">@if (!empty($errors->first('ifearea'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('city_id') }}</span>
                                    <select class="js-select2-area_city form-control custom-font-small" name="ifearea" id="ifearea" style="width:100%;" disabled>
                                        <option value="">-- Select IFE Area --</option>
                                        @foreach($ifeareas as $c)
                                            <option value="{{ $c->id }}" {{ $task->lead->ife_area_id == $c->id ? 'selected' : '' }}>{{ $c->area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @if($task->lead->remark)
                            <div class="row">
                                <!-- Remark -->
                                <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                                    <label for="leadremark" class="custom-font-xsmall"><b>Note on Lead</b> :</label>
                                    <textarea type="text" rows="10" readonly class="form-control form-control-sm custom-font-small" name="leadremark" id="leadremark">{{ $task->lead->remark }}</textarea>
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <div class="custom-font-xsmall"><b>Document(s)</b></div>

                                <div style="margin-top: 10px; overflow-x:auto; white-space: nowrap;">
                                    <table class="table table-sm custom-font-small" style="width:100%; border-collapse: collapse; box-shadow: 1px 1px 3px 1px rgb(183, 183, 183);">
                                        <thead class="table-dark">
                                            <tr>
                                                <td scope="col">{{ trans('translation.name') }}</td>
                                                <td scope="col">{{ trans('translation.upload_date') }}</td>
                                                <td scope="col">{{ trans('translation.upload_by') }}</td>
                                                <td scope="col">{{ trans('translation.file_size') }}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($task->lead->documentUploads as $item)
                                            <tr>
                                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->filename }}</td>
                                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->created_at }}</td>
                                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->uploadBy->name }}</td>
                                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->size }} KB</td>
                                                <td style="border-bottom: 1px solid #acacac; border-bottom: 1px solid #acacac; text-align:right;">
                                                    <?php
                                                        $audioFormat = [
                                                            '3gp','aa','aac','aax','act','aiff','alac','amr','au','awb','dvf','flac','gsm','iklax','ivs','m4a','m4b','m4p','mmf','movpkg','mp3','mpc','msv','nmf','ogg','oga','mogg','opus','ra','rm','raw','rf64','sln','tta','voc','vox','wav','wma','wv','8svx','cda'
                                                        ];

                                                        $imageFormat = [
                                                            'png','jpg','jpeg','webp'
                                                        ];

                                                        $pdfFormat = [
                                                            'pdf'
                                                        ];

                                                        $fileExtensionArr = explode('.',$item->filename);
                                                        $fileExtension    = $fileExtensionArr[count($fileExtensionArr)-1];
                                                        $isAudio          = array_search($fileExtension, $audioFormat);
                                                        $isImage          = array_search($fileExtension, $imageFormat);
                                                        $isPdf            = array_search($fileExtension, $pdfFormat);
                                                    ?>
                                                    @if($isAudio !== false)
                                                    <audio controls class="float-end custom-shadow">
                                                        <source src="{{ $item->file_full_path }}" type="audio/ogg">
                                                        <source src="{{ $item->file_full_path }}" type="audio/mp3">
                                                        <source src="{{ $item->file_full_path }}" type="audio/wav">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                    @endif
                                                    @if($isImage !== false)
                                                    <input type=submit onclick="window.open('{{ $item->file_full_path }}','_blank'); event.preventDefault(); return true;" value="preview photo">
                                                    @endif
                                                    @if($isPdf !== false)
                                                    <input type=submit onclick="window.open('{{ $item->file_full_path }}','_blank'); event.preventDefault(); return true;" value="preview pdf">
                                                    @endif
                                                </td>
                                                <td style="border-bottom: 1px solid #acacac; text-align:center;">
                                                    <a href="{{ route('lead.file.download',['doc_id'=>$item->id]) }}"><i class="fas fa-cloud-download-alt fa-lg"></i></a>&NonBreakingSpace;
                                                </td>
                                            </tr>
                                            @endforeach
                                            @foreach($task->documentUploads as $item)
                                            <tr>
                                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->filename }}</td>
                                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->created_at }}</td>
                                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->uploadBy->name }}</td>
                                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->size }} KB</td>
                                                <td style="border-bottom: 1px solid #acacac; border-bottom: 1px solid #acacac; text-align:right;">
                                                    <?php
                                                        $audioFormat = [
                                                            '3gp','aa','aac','aax','act','aiff','alac','amr','au','awb','dvf','flac','gsm','iklax','ivs','m4a','m4b','m4p','mmf','movpkg','mp3','mpc','msv','nmf','ogg','oga','mogg','opus','ra','rm','raw','rf64','sln','tta','voc','vox','wav','wma','wv','8svx','cda'
                                                        ];

                                                        $imageFormat = [
                                                            'png','jpg','jpeg','webp'
                                                        ];

                                                        $pdfFormat = [
                                                            'pdf'
                                                        ];

                                                        $fileExtensionArr = explode('.',$item->filename);
                                                        $fileExtension    = $fileExtensionArr[count($fileExtensionArr)-1];
                                                        $isAudio          = array_search($fileExtension, $audioFormat);
                                                        $isImage          = array_search($fileExtension, $imageFormat);
                                                        $isPdf            = array_search($fileExtension, $pdfFormat);
                                                    ?>
                                                    @if($isAudio !== false)
                                                    <audio controls class="float-end custom-shadow">
                                                        <source src="{{ $item->file_full_path }}" type="audio/ogg">
                                                        <source src="{{ $item->file_full_path }}" type="audio/mp3">
                                                        <source src="{{ $item->file_full_path }}" type="audio/wav">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                    @endif
                                                    @if($isImage !== false)
                                                    <input type=submit onclick="window.open('{{ $item->file_full_path }}','_blank'); event.preventDefault(); return true;" value="preview photo">
                                                    @endif
                                                    @if($isPdf !== false)
                                                    <input type=submit onclick="window.open('{{ $item->file_full_path }}','_blank'); event.preventDefault(); return true;" value="preview pdf">
                                                    @endif
                                                </td>
                                                <td style="border-bottom: 1px solid #acacac; text-align:center;">
                                                    <a href="{{ route('lead.file.download',['doc_id'=>$item->id]) }}"><i class="fas fa-cloud-download-alt fa-lg"></i></a>&NonBreakingSpace;
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>                    

                    <div class="form-actions">
                        <div class="dropdown custom_float">
                            <button class="btn btn-dark custom-button-shadow" formaction="{{ route('tasks.update') }}" type="submit" id="submit_main" style="display:none;"></button>
                            <button class="btn btn-primary custom-button-shadow" type="button" id="submit">@lang('translation.save')</button>
                        </div>
                    </div>
                </div>

                <div style="visibility:hidden; width:100%; height:0px;">
                    <select class="form-control form-select custom-font-small js-leadname-multiple" name="filter_name[]" id="filter_name" multiple style="visibility:hidden; width:100%;">
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
                    <select class="form-control form-select custom-font-small js-leadbusiness-multiple" name="filter_businessname[]" id="filter_businessname" multiple style="visibility:hidden; width:100%;">
                        @if(null !== $request->filter_businessname)
                            @foreach($leadBusinessOptions as $c)
                            <option value="{{ $c }}" {{ array_search($c, $request->filter_businessname) === false ? '' : 'selected' }}>{{ $c }}</option>
                            @endforeach
                        @else
                            @foreach($leadBusinessOptions as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </form>            
        </div>

        <div class="tab-pane" id="reminder" role="tabpanel" style="padding-right:5px; padding-left:5px;">
            <div style="padding:5px; background-color:rgb(13, 114, 158); color: white; border-color:white;">
                Reminder Detail
            </div>
            <div class="m-2">
                <div class="row">
                    @php
                        $u      = $task->users->where('user_id',Auth::guard('web')->user()->id)->first();
                        $uid    = $u ? $u->user_id : 0;
                        $record = $reminders->where('user_id', $uid)->first();

                        $d = $record ? $record->reminder_date : '';
                        $t = $record ? $record->reminder_time : '';
                        $m = $record ? $record->message : '';
                    @endphp
                    @if($uid != 0)
                    <div style="padding-bottom:20px;">

                        <input type="hidden" value="{{ $task->id }}" name="reminder_tid" id="reminder_tid">
                        <input type="hidden" value="{{ $uid }}" name="reminder_uid" id="reminder_uid">

                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="task_start_date" class="custom-font-xsmall"><b>Reminder</b> :</label>
                            <div class="row">
                                <div class="col-md-6 col-xl-6">
                                    <input class="form-control form-control-sm custom-font-small" type="date" value="{{ old('reminder_date',$d) }}" name="reminder_date" id="reminder_date">
                                </div>
                                <div class="col-md-6 col-xl-6">
                                    <input class="form-control form-control-sm custom-font-small" type="time" value="{{ old('reminder_time',$t) }}" id="reminder_time" name="reminder_time">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                            <label for="title" class="custom-font-xsmall"><b>Message</b> :</label>
                            <input class="form-control form-control-sm custom-font-small" type="text" maxlength="500" value="{{ old('reminder_msg',$m) }}" name="reminder_msg" id="reminder_msg">
                        </div>

                        <hr/>
                    </div>
                    @endif
                </div>

                <div class="form-actions">
                    <div class="dropdown custom_float">
                        @if($uid != 0)
                        <a class="btn btn-danger custom-button-shadow" id="btnSetReminder" name="btnSetReminder" style="width:150px;" onclick="setReminder()">Set Reminder</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="history" role="tabpanel" style="padding-right:5px; padding-left:5px;">
            @if(!$history->isEmpty())
                <div style="padding-top:3px; overflow-x:auto; white-space: nowrap; min-height: 300px;">
                    <table class="table table-sm table-striped custom-font-small custom-shadow" style="width:100%;">
                        <thead class="table-dark">
                            <tr>
                                <td scope="col" style="width:200px;">{{ trans('translation.updated_by') }}</td>
                                <td scope="col" style="width:180px;">{{ trans('translation.updated_at') }}</td>
                                <td scope="col" style="width:160px;">{{ trans('translation.before_status') }}</td>
                                <td scope="col" style="width:160px;">{{ trans('translation.after_status') }}</td>
                                <td scope="col">{{ trans('translation.remark') }}</td>
                                <td scope="col" style="width:50px;"></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $h)
                                <tr>
                                    <td class="custom-font-xsmall">
                                        {{ $h->updatedBy->name }}<br/>
                                        <span class="small">{{ \App\Models\User::getUserType($h->updatedBy->type) }}</span>
                                    </td>
                                    <td class="custom-font-xsmall">
                                        {{ $h->created_at}}
                                    </td>
                                    <td class="custom-font-xsmall">
                                        {{ \App\Helpers\Helper::getStatus($h->before_status) }}
                                    </td>
                                    <td class="custom-font-xsmall">
                                        {{ \App\Helpers\Helper::getStatus($h->after_status) }}
                                    </td>
                                    <td>
                                        <pre style="font-size: 12px; font-family: Arial, Helvetica, sans-serif">{{ $h->remark }}</pre>
                                    </td>
                                    <td>
                                        @if($h->after_data)
                                        <a class="btn btn-sm btn-danger custom-font-xsmall custom-cursor" data-bs-toggle="collapse" data-bs-target="#info{{$h->id}}" aria-expanded="true" aria-controls="info{{$h->id}}">
                                            @lang('translation.detail')
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <div id="info{{$h->id}}" class="collapse" style="padding-left:50px; padding-right:50px;">
                                            <table class="table-primary table-bordered table-striped" style="width:100%; border-color:black; word-break:break-all;">
                                                <thead>
                                                    <tr>
                                                        <td style="padding-left:10px; background-color:rgb(64, 70, 157); color:white; width: 20%;">@lang('translation.field')</td>
                                                        <td style="padding-left:10px; background-color:rgb(64, 70, 157); color:white; width: 40%;">@lang('translation.before')</td>
                                                        <td style="padding-left:10px; background-color:rgb(64, 70, 157); color:white; width: 40%;">@lang('translation.after')</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($h->after_data as $k => $v)
                                                        @if(!is_array($v))
                                                            <tr>
                                                                <td style="background-color:white; padding-left:10px;">{{ \App\Helpers\Helper::getFormatDisplay($k) }}</td>
                                                                <td style="background-color:white; padding-left:10px;">{{ isset($h->before_data[$k]) ? $h->before_data[$k] : '' }}</td>
                                                                <td style="background-color:white; padding-left:10px;">{{ isset($v) ? $v : '' }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach                    
                                                </tbody>
                                            </table><br/>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="tab-pane" id="comment" role="tabpanel" style="padding-right:5px; padding-left:5px;">
            <div class="row custom-font-small m-3" style="height: 65vh;">
                <div class="col-sm-8 col-md-8 col-md-8 col-xl-8">
                    <div class="card shadow mt-1" style="background-color: rgb(251, 248, 242); border-width: thin; border-color:rgb(227, 227, 227); border-radius: 5px;">
                        <div class="px-lg-2 small">
                            <div id="comment{{ $task->id }}" class="chat-conversation p-3" style="background-color: rgb(251, 248, 242);">
                                @php
                                    $chat_date       = 0;
                                    $unreadScrollIdx = 0;
                                    $comment_listing = $task->comments->groupBy(function($item) {
                                                                    return Carbon\Carbon::parse($item->submit_date)->format('Y-m-d');
                                                                })
                                                                ->groupBy('created_at')
                                                                ->sortBy('created_at');
                                @endphp
                                <ul id="chatroom_container{{ $task->id }}" class="list-unstyled mb-0" style="min-height: 60vh;">
                                    <div class="simplebar-wrapper" style="margin: 0px;">
                                        <div class="simplebar-height-auto-observer-wrapper">
                                            <div class="simplebar-height-auto-observer"></div>
                                        </div>
                                        <div class="simplebar-mask">
                                            <div class="simplebar-offset" style="right: -15px;">
                                                <div class="simplebar-content-wrapper">
                                                    <div class="scroller" id="scroller{{ $task->id }}" class="simplebar-content" style="padding: 0px; height:65vh; overflow: hidden scroll;">
                                                        @if($comment_listing->count() == 0)
                                                            <li class="chat-day-title" style="border-bottom: 1px solid #dfdfdf;">
                                                                <div class="title" style="background-color: white; box-shadow: 0px 2px 2px 1px rgb(183, 183, 183);">Today</div>
                                                            </li>
                                                        @else
                                                            <!-- master collection (alway one item) -->
                                                            @foreach($comment_listing as $chat_msg_group_idx => $chat_msg_group)
                                                                <!-- date group -->
                                                                @foreach($chat_msg_group as $chat_msg_group_idx => $chat_msg_group)
                                                                    <!-- msg group -->
                                                                    @foreach($chat_msg_group as $chat_msg_idx => $chat_msg)
                                                                        
                                                                        @if($chat_date != $chat_msg->submit_date)
                                                                            @php $chat_date = $chat_msg->submit_date @endphp
                                                                            <!-- Date -->
                                                                            <li class="chat-day-title" style="border-bottom: 1px solid #dfdfdf;">
                                                                                @if( Carbon\Carbon::parse($chat_msg->submit_date)->eq(Carbon\Carbon::today()) )
                                                                                    <div class="title" style="background-color: white; box-shadow: 0px 2px 2px 1px rgb(183, 183, 183);">Today</div>
                                                                                @else
                                                                                    <div class="title" style="background-color: white; box-shadow: 0px 2px 2px 1px rgb(183, 183, 183);">{{ Carbon\Carbon::parse($chat_msg->submit_date)->format('Y-m-d') }}</div>
                                                                                @endif
                                                                            </li>
                                                                        @endif

                                                                        @if($chat_msg->documentUploads->count() > 0)
                                                                            @foreach($chat_msg->documentUploads as $doc_idx => $doc)
                                                                            <li class="{{ Auth::guard('web')->user()->id == $chat_msg->submit_by ? 'right' : '' }}" style="width: 60%; {{ Auth::guard('web')->user()->id == $chat_msg->submit_by ? ' float: right;' : '' }}" id="chatroom_msg{{ $chat_msg->id }}">
                                                                                <div class="conversation-list">
                                                                                    <div class="ctext-wrap">
                                                                                        <div class="ctext-wrap-content" style="background-color: white; box-shadow: 0px 2px 2px 1px rgb(183, 183, 183);">
                                                                                            <h5 class="small conversation-name">
                                                                                                <div class="d-flex">
                                                                                                    {{ $chat_msg->submitBy->name }}
                                                                                                    <span class="d-inline-block text-muted ms-2">{{ Carbon\Carbon::parse($chat_msg->created_at)->format('h:i A') }}</span>
                                                                                                    @if(!$chat_msg->is_read && Auth::guard('web')->user()->id != $chat_msg->submit_by)
                                                                                                        @php $unreadScrollIdx = $unreadScrollIdx + 1 @endphp
                                                                                                        <div id="unread{{ $unreadScrollIdx }}" class="rounded-circle" style="margin-left: 5px; width: 7px; height: 7px; background-color: rgb(255, 6, 6);">&nbsp;</div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </h5>
                                                                                            <!-- FOR LOOP SHOW THE DOCUMENT HERE -->
                                                                                            <div style="width: 300px;">
                                                                                                <?php
                                                                                                    $chatVideoFormat = [
                                                                                                        'mp4'
                                                                                                    ];

                                                                                                    $chatAudioFormat = [
                                                                                                        '3gp','aa','aac','aax','act','aiff','alac','amr','au','awb','dvf','flac','gsm','iklax','ivs','m4a','m4b','m4p','mmf','movpkg','mp3','mpc','msv','nmf','ogg','oga','mogg','opus','ra','rm','raw','rf64','sln','tta','voc','vox','wav','wma','wv','8svx','cda'
                                                                                                    ];

                                                                                                    $chatImageFormat = [
                                                                                                        'png','jpg','jpeg','webp'
                                                                                                    ];

                                                                                                    $chatPdfFormat = [
                                                                                                        'pdf'
                                                                                                    ];

                                                                                                    $chatFileExtensionArr = explode('.',$doc->filename);
                                                                                                    $chatFileExtension    = $chatFileExtensionArr[count($chatFileExtensionArr)-1];
                                                                                                    $isChatVideo          = array_search($chatFileExtension, $chatVideoFormat);
                                                                                                    $isChatAudio          = array_search($chatFileExtension, $chatAudioFormat);
                                                                                                    $isChatImage          = array_search($chatFileExtension, $chatImageFormat);
                                                                                                    $isChatPdf            = array_search($chatFileExtension, $chatPdfFormat);
                                                                                                ?>
                                                                                                @if($isChatAudio !== false)
                                                                                                <audio controls class="custom-shadow">
                                                                                                    <source src="{{ $doc->file_full_path }}" type="audio/ogg">
                                                                                                    <source src="{{ $doc->file_full_path }}" type="audio/mp3">
                                                                                                    <source src="{{ $doc->file_full_path }}" type="audio/wav">
                                                                                                    Your browser does not support the audio element.
                                                                                                </audio>
                                                                                                @elseif($isChatImage !== false)
                                                                                                <img id="{{ 'img'.$doc->id }}" src="{{ $doc->file_full_path }}" style="width: 100px; height: auto; object-fit: cover; cursor: pointer;" onclick="imageClick('{{ $doc->file_full_path }}')"/>
                                                                                                @elseif($isChatPdf !== false)
                                                                                                <input type=submit onclick="window.open('{{ $doc->file_full_path }}','_blank'); event.preventDefault(); return true;" value="preview">
                                                                                                <object data="{{ $doc->file_full_path }}"
                                                                                                        width="300px"
                                                                                                        height="150px">
                                                                                                </object>
                                                                                                @elseif($isChatVideo !== false)
                                                                                                <video width="320" controls>
                                                                                                    <source src="{{ $doc->file_full_path }}" >
                                                                                                    Your browser does not support the video tag.
                                                                                                </video>
                                                                                                @else
                                                                                                <a href="{{ $doc->file_full_path }}"><i class="fas fa-cloud-download-alt fa-lg"></i> {{ $doc->filename }}</a>&NonBreakingSpace;
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                        @if( (Auth::guard('web')->user()->id == $chat_msg->submit_by) )
                                                                                        <div class="dropdown dropleft float-right align-self-start {{ Auth::guard('web')->user()->id == $chat_msg->submit_by ? '' : 'pe-5' }}">
                                                                                            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                                                                                aria-haspopup="true" aria-expanded="false">
                                                                                                <i class="uil uil-ellipsis-v"></i>
                                                                                            </a>
                                                                                            <div class="dropdown-menu" style="max-width:15px;">
                                                                                                @if(strtotime(Carbon\Carbon::parse($chat_msg->created_at)->addMinutes(15)) > strtotime(Carbon\Carbon::now()))
                                                                                                <a class="dropdown-item small" onclick="deleteChat({{ $chat_msg->id }},{{ $doc->id }})"><i class='fas fa-trash-alt'></i>&NonBreakingSpace;Delete</a>
                                                                                                @endif
                                                                                                <a class="dropdown-item small" href="{{ route('lead.file.download',['doc_id'=>$doc->id]) }}"><i class="fas fa-cloud-download-alt fa-lg"></i>&NonBreakingSpace;Download</a>
                                                                                            </div>
                                                                                        </div>
                                                                                        @else
                                                                                        <div class="dropdown dropleft float-right align-self-start {{ Auth::guard('web')->user()->id == $chat_msg->submit_by ? '' : 'pe-5' }}">
                                                                                            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                                                                                aria-haspopup="true" aria-expanded="false">
                                                                                                <i class="uil uil-ellipsis-v"></i>
                                                                                            </a>
                                                                                            <div class="dropdown-menu" style="max-width:15px;">
                                                                                                <a class="dropdown-item small" href="{{ route('lead.file.download',['doc_id'=>$doc->id]) }}"><i class="fas fa-cloud-download-alt fa-lg"></i>&NonBreakingSpace;Download</a>
                                                                                            </div>
                                                                                        </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                            @endforeach
                                                                        @endif

                                                                        @if($chat_msg->message)
                                                                        <li class="{{ Auth::guard('web')->user()->id == $chat_msg->submit_by ? 'right' : '' }}" id="chatroom_msg{{ $chat_msg->id }}" style="width: 60%; {{ Auth::guard('web')->user()->id == $chat_msg->submit_by ? ' float: right;' : '' }}">
                                                                            <div class="conversation-list">
                                                                                <div class="ctext-wrap">
                                                                                    <div class="ctext-wrap-content" style="background-color: white; box-shadow: 0px 2px 2px 1px rgb(183, 183, 183);">
                                                                                        <h5 class="small conversation-name">
                                                                                            <div class="d-flex">
                                                                                                {{ $chat_msg->submitBy->name }}
                                                                                                <span class="d-inline-block text-muted ms-2">{{ Carbon\Carbon::parse($chat_msg->created_at)->format('h:i A') }}</span>
                                                                                                @if(!$chat_msg->is_read && Auth::guard('web')->user()->id != $chat_msg->submit_by)
                                                                                                    @php $unreadScrollIdx = $unreadScrollIdx + 1 @endphp
                                                                                                    <div id="unread{{ $unreadScrollIdx }}" class="rounded-circle" style="margin-left: 5px; width: 7px; height: 7px; background-color: rgb(255, 6, 6);">&nbsp;</div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </h5>
                                                                                        <div style="max-width: 1024px; font-size: 14px; ">
                                                                                            <p class="mb-0" style="text-align:left; white-space: pre-wrap;">{!! $chat_msg->formated_message !!}</p>
                                                                                            @if($chat_msg->ifeReport)
                                                                                                @foreach($chat_msg->ifeReport->documentUploads as $doc_idx => $doc)
                                                                                                    <!-- FOR LOOP SHOW THE DOCUMENT HERE -->
                                                                                                    <div style="width: 300px;">
                                                                                                        <?php
                                                                                                            $chatVideoFormat = [
                                                                                                                'mp4'
                                                                                                            ];

                                                                                                            $chatAudioFormat = [
                                                                                                                '3gp','aa','aac','aax','act','aiff','alac','amr','au','awb','dvf','flac','gsm','iklax','ivs','m4a','m4b','m4p','mmf','movpkg','mp3','mpc','msv','nmf','ogg','oga','mogg','opus','ra','rm','raw','rf64','sln','tta','voc','vox','wav','wma','wv','8svx','cda'
                                                                                                            ];

                                                                                                            $chatImageFormat = [
                                                                                                                'png','jpg','jpeg','webp'
                                                                                                            ];

                                                                                                            $chatPdfFormat = [
                                                                                                                'pdf'
                                                                                                            ];

                                                                                                            $chatFileExtensionArr = explode('.',$doc->filename);
                                                                                                            $chatFileExtension    = $chatFileExtensionArr[count($chatFileExtensionArr)-1];
                                                                                                            $isChatVideo          = array_search($chatFileExtension, $chatVideoFormat);
                                                                                                            $isChatAudio          = array_search($chatFileExtension, $chatAudioFormat);
                                                                                                            $isChatImage          = array_search($chatFileExtension, $chatImageFormat);
                                                                                                            $isChatPdf            = array_search($chatFileExtension, $chatPdfFormat);
                                                                                                        ?>
                                                                                                        @if($isChatAudio !== false)
                                                                                                        <audio controls class="custom-shadow">
                                                                                                            <source src="{{ $doc->file_full_path }}" type="audio/ogg">
                                                                                                            <source src="{{ $doc->file_full_path }}" type="audio/mp3">
                                                                                                            <source src="{{ $doc->file_full_path }}" type="audio/wav">
                                                                                                            Your browser does not support the audio element.
                                                                                                        </audio>
                                                                                                        @elseif($isChatImage !== false)
                                                                                                        <img id="{{ 'ifeimg'.$doc->id }}" src="{{ $doc->file_full_path }}" style="width: 100px; height: auto; object-fit: cover; cursor: pointer;" onclick="imageClick('{{ $doc->file_full_path }}')"/>
                                                                                                        @elseif($isChatPdf !== false)
                                                                                                        <input type=submit onclick="window.open('{{ $doc->file_full_path }}','_blank'); event.preventDefault(); return true;" value="preview">
                                                                                                        <object data="{{ $doc->file_full_path }}"
                                                                                                                width="300px"
                                                                                                                height="150px">
                                                                                                        </object>
                                                                                                        @elseif($isChatVideo !== false)
                                                                                                        <video width="320" controls>
                                                                                                            <source src="{{ $doc->file_full_path }}" >
                                                                                                            Your browser does not support the video tag.
                                                                                                        </video>
                                                                                                        @else
                                                                                                        <a href="{{ $doc->file_full_path }}"><i class="fas fa-cloud-download-alt fa-lg"></i> {{ $doc->filename }}</a>&NonBreakingSpace;
                                                                                                        @endif
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                    @if( (Auth::guard('web')->user()->id == $chat_msg->submit_by) && (strtotime(Carbon\Carbon::parse($chat_msg->created_at)->addMinutes(15)) > strtotime(Carbon\Carbon::now())) )
                                                                                    <div class="dropdown dropleft float-right align-self-start {{ Auth::guard('web')->user()->id == $chat_msg->submit_by ? '' : 'pe-5' }}">
                                                                                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                                                                            aria-haspopup="true" aria-expanded="false">
                                                                                            <i class="uil uil-ellipsis-v"></i>
                                                                                        </a>
                                                                                        <div class="dropdown-menu" style="max-width:15px;">
                                                                                            <a class="dropdown-item small" onclick="deleteChat({{ $chat_msg->id }},0)"><i class='fas fa-trash-alt'></i>&NonBreakingSpace;Delete</a>
                                                                                        </div>
                                                                                    </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                        @endif

                                                                    @endforeach
                                                                @endforeach
                                                            @endforeach
                                                        @endif
                                                        <li class="anchor" id="anchor{{ $task->id }}"></li>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="simplebar-placeholder" style="width: auto; height: 200px;"></div>
                                    </div>

                                    <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                        <div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: none;"></div>
                                    </div>
                                    <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                                        <div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: block; height: 384px;"></div>
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-4 col-md-4 col-xl-4">
                    <div class="row chat-input-section">
                        <div>
                            <textarea id="chat_message{{ $task->id }}" name="chat_message" rows="15" class="form-control form-control-sm small chat-input small" style="border-radius: 2px;"></textarea>
                        </div>
                        <div class="mb-1">
                            <input type="file" onchange="fileValidation('file')" name="file[]" id="file" class="mt-2" accept="video/* .jpeg, .jpg, .png, .pdf, audio/*, .ppt, .pptx,  .xls, .xlsx, application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword" multiple>
                            <button type="button" onclick="createChat({{ $task->id }});" class="float-end mt-2 btn btn-sm btn-primary chat-send w-md waves-effect waves-light custom-shadow"><span
                                class="d-none d-sm-inline-block me-2">Send</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div id="myModal" class="modal">
  <span class="close">&times;</span>
  <img class="modal-content" id="img01">
</div>

@section('script')
<script>
    CKEDITOR.config.allowedContent = true;
    CKEDITOR.config.extraPlugins   = 'colorbutton,blockimagepaste';
    CKEDITOR.config.removeButtons  = 'Image,Cut,Copy,Paste,PasteText,Source,Blockquote';
    CKEDITOR.config.removePlugins  = 'exportpdf';
    CKEDITOR.config.versionCheck   = false;
    CKEDITOR.config.height         = 330;
    CKEDITOR.config.toolbar        = [
                                        [ 'Undo', 'Redo' ],['TextColor', 'BGColor'],
                                        { name: 'links',       items : [ 'Link','Unlink' ] },
                                        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'FontSize' ] }
                                    ];
    CKEDITOR.config.enterMode      = CKEDITOR.ENTER_DIV;
    CKEDITOR.config.shiftEnterMode = CKEDITOR.ENTER_DIV;
    CKEDITOR.plugins.add('blockimagepaste', {
    
        init : function(editor) {
    
            function replaceImgText(html) {
    
                var ret = html.replace(/<img[^>]*src="data:image\/(bmp|dds|gif|jpg|jpeg|png|psd|pspimage|tga|thm|tif|tiff|yuv|ai|eps|ps|svg);base64,.*?"[^>]*>/gi, function(img) {
                    alert("Pasting image is not allowed. Please use the upload file method.");
                    return '';
                });
    
                return ret;
            }
    
            function chkImg() {
                // don't execute code if the editor is readOnly
                if (editor.readOnly)
                    return;
    
                setTimeout(function() {
                    editor.document.$.body.innerHTML = replaceImgText(editor.document.$.body.innerHTML);
                }, 100);
            }
    
            editor.on('contentDom', function() {
                // For Firefox
                editor.document.on('drop', chkImg);
                // For IE
                editor.document.getBody().on('drop', chkImg);
            });
    
            editor.on('paste', function(e) {
    
                var html = e.data.dataValue;
                if (!html) {
                    return;
                }
    
                e.data.dataValue = replaceImgText(html);
            });
    
        } // Init
    });

    @if($task->users->where('role',4)->where('user_id',Auth::guard('web')->user()->id)->first() ||
        Auth::guard('web')->user()->type == 0
    )
        $(".js-subscriber").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    @endif

    $(".js-owner-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $(".js-viewer-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $(".js-subsubscriber-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $(".js-leadname-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-leadbusiness-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-alertind").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $("#ifearea").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });

    $("#edit_case_form").bind("keypress", function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });

    $('#submit').click(function(e){

        new swal({
            title: 'Please enter the reason of updating this changes.',
            input: 'textarea',
            showCancelButton: true,
            confirmButtonText: 'Send',

        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value) {
                    $('#special_remark').val(result.value);
                    document.getElementById("submit_main").click();
                } else {
                    new swal(
                        'Oops...',
                        'Please enter your reason to proceed the update.',
                        'error'
                    )
                }
            }
        })
    })

    $('form').submit(function(){
        $(this).find(':submit').attr( 'disabled','disabled' );

        setTimeout(() => {
            $(this).find(':submit').attr( 'disabled',false );
        }, 3000)
    });

    $( document ).ready(function() {
        let scrollableUnreadMsg = document.getElementById('unread1');
        if (scrollableUnreadMsg != null) {
            scrollableUnreadMsg.scrollIntoView();
        } else {
            let scrollableDiv = document.getElementById('scroller'+'{{ $task->id }}');
            scrollableDiv.scrollTop = scrollableDiv.scrollHeight;
        }

        let scrollableDiv = document.getElementById('scroller'+'{{ $task->id }}');
        scrollableDiv.addEventListener("scroll", () => {
            if (scrollableDiv.offsetHeight + scrollableDiv.scrollTop >= scrollableDiv.scrollHeight - 150) {
                $.ajax({
                    type: "post",
                    url: "{{ route('chat.markedasread') }}",
                    data: {"_token": "{{ csrf_token() }}","id": '{{ $task->id }}'},
                    cache: false,
                    success: function (response) {
                    }
                });
            }
        });
    });

    function scrollToBottom() {
        let cnt = {{ $task->comments->count() }};
        if (parseInt(cnt) < 4) {
            $.ajax({
                type: "post",
                url: "{{ route('chat.markedasread') }}",
                data: {"_token": "{{ csrf_token() }}","id": '{{ $task->id }}'},
                cache: false,
                success: function (response) {
                }
            });
        }

        let scrollableUnreadMsg = document.getElementById('unread1');
        if (scrollableUnreadMsg != null) {
            scrollableUnreadMsg.scrollIntoView();
        } else {
            let scrollableDiv = document.getElementById('scroller'+'{{ $task->id }}');
            scrollableDiv.scrollTop = scrollableDiv.scrollHeight;
        }
    }

    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }

    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }

    function onlyAlphaNumberSpaceKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if ( !(ASCIICode == 32 || (ASCIICode > 47 && ASCIICode < 58) || (ASCIICode > 96 && ASCIICode < 123) || (ASCIICode > 64 && ASCIICode < 91)) )
            return false;
        return true;
    }

    function onlyAlphaNumberKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if ( !((ASCIICode > 47 && ASCIICode < 58) || (ASCIICode > 96 && ASCIICode < 123) || (ASCIICode > 64 && ASCIICode < 91)) )
            return false;
        return true;
    }

    function imageClick(url) {
        // Get the modal
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("img01");
    
        modal.style.display = "block";
        modalImg.src = url;

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() { 
            modal.style.display = "none";
        }
    }

    function fileValidation(type) { 
        fi = document.getElementById(type); 
        // Check if any file is selected. 
        if (fi.files.length > 0) { 
            for (i = 0; i <= fi.files.length - 1; i++) { 
    
                fsize = fi.files.item(i).size; 
                file = Math.round((fsize / 1024)); 
                // The size of the file.
                if(type == 'file'){
                    if (file >= 51200) { 
                        alert("File is too large,  > 50MB!");
                        document.getElementById(type).value='';
                    }
                }
            } 
        } 
    }

    function createChat(id) {
        $.ajax({
            url  : "{{ route('chat.store') }}",
            type : "POST",
            xhr  : function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            data : function(){
                var data = new FormData();
                jQuery.each(jQuery('#file')[0].files, function(i, file) {
                    data.append('file-'+i, file);
                });
                data.append('_token', "{{ csrf_token() }}");
                data.append('task_id', $('#task_id').val());
                data.append('chat_message', $('#chat_message'+id).val());
                return data;
            }(),
            cache      : false,
            contentType: false,
            processData: false,
            success : function(response) {
                if (response.type == 'error') {
                    new swal ({
                        title: "@lang('translation.error')",
                        text: response.message,
                        icon: 'warning',
                        confirmButtonColor: '#3085d6'
                    })

                } else {
                    
                    $('#chat_message'+id).val('');

                    obj = JSON.parse(response.data);

                    if (obj['hasdoc'] == 1) {
                        let i = 1;
                        obj['doc'].forEach(element => {
                            html = '';
                            html = html + '    <div class="conversation-list">';
                            html = html + '        <div class="ctext-wrap">';
                            html = html + '            <div class="ctext-wrap-content" style="background-color: white; box-shadow: 0px 2px 2px 1px rgb(183, 183, 183);">';
                            html = html + '                <h5 class="small conversation-name">' + element['name'] + '<span class="d-inline-block text-muted ms-2">' + element['time'] + '</span></h5>';
                            html = html + '                <p class="mb-0" style="white-space: pre-wrap;">';
                            if(element['isChatAudio'] == 1) {
                                html = html + '              <audio controls class="custom-shadow">';
                                html = html + '                 <source src="' +  element['fullpath']  + '" type="audio/ogg">';
                                html = html + '                 <source src="' +  element['fullpath']  + '" type="audio/mp3">';
                                html = html + '                 <source src="' +  element['fullpath']  + '" type="audio/wav">';
                                html = html + '                 Your browser does not support the audio element.';
                                html = html + '              </audio>';
                            }
                            else if(element['isChatImage'] == 1) {
                                html = html + '              <img id="img' + element['docid'] + '" src="' + element['fullpath'] + '" onclick="imageClick(' + element['fullpath'] + ')" style="width: 100px; height: auto; object-fit: cover; cursor: pointer;"/>';
                            }
                            else if(element['isChatPdf'] == 1) {
                                html = html + '              <object data="' + element['fullpath'] + '" width="300px" height="150px">';
                                html = html + '              </object>';
                            }
                            else if(element['isChatVideo'] == 1) {
                                html = html + '              <video width="320px" controls>';
                                html = html + '                 <source src="' + element['fullpath'] + '">';
                                html = html + '                 Your browser does not support the video tag.';
                                html = html + '              </video>';
                            }
                            else {
                                html = html + '              <a href="' + element['fullpath'] + '"><i class="fas fa-cloud-download-alt fa-lg"></i></a>';
                            }

                            html = html + '                </p>';
                            html = html + '            </div>';
                            html = html + '            <div class="dropdown dropleft float-right align-self-start">';
                            html = html + '                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';       
                            html = html + '                    <i class="uil uil-ellipsis-v"></i>';
                            html = html + '                </a>';
                            html = html + '                <div class="dropdown-menu" style="max-width:15px;">';
                            html = html + '                    <a class="dropdown-item small" href="#"  onclick="deleteChat(' + element['chatid'] + ',' + element['docid'] + ')"><i class="fas fa-trash-alt"></i></a>';
                            html = html + '                </div>';
                            html = html + '            </div>';
                            html = html + '        </div>';
                            html = html + '    </div>';

                            li               = document.createElement("li");
                            li.className     = "right";
                            li.style.cssText = "width: 60%; float: right;";
                            li.id            = "chatroom_msg" + obj['chatid'];
                            li.innerHTML     = html;
                            
                            let scroller  = document.getElementById("scroller"+id);
                            let li_anchor = document.getElementById("anchor"+id);
                            
                            scroller.insertBefore(li, li_anchor);
                            i = i + 1;
                        });
                        $('#file').val('');
                    }

                    if (obj['message']) {
                        html = '';
                        html = html + '    <div class="conversation-list">';
                        html = html + '        <div class="ctext-wrap">';
                        html = html + '            <div class="ctext-wrap-content" style="background-color: white; box-shadow: 0px 2px 2px 1px rgb(183, 183, 183);">';
                        html = html + '                <h5 class="small conversation-name">' + obj['name'] + '<span class="d-inline-block text-muted ms-2">' + obj['time'] + '</span></h5>';
                        html = html + '                <div style="max-width: 1024px; font-size: 14px; ">';
                        html = html + '                   <p class="mb-0" style="white-space: pre-wrap;">' + obj['message'] + '</p>';
                        html = html + '                </div>';
                        html = html + '            </div>';
                        html = html + '            <div class="dropdown dropleft float-right align-self-start">';
                        html = html + '                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';       
                        html = html + '                    <i class="uil uil-ellipsis-v"></i>';
                        html = html + '                </a>';
                        html = html + '                <div class="dropdown-menu" style="max-width:15px;">';
                        html = html + '                    <a class="dropdown-item small" href="#"  onclick="deleteChat(' + obj['chatid'] + ',0)"><i class="fas fa-trash-alt"></i></a>';
                        html = html + '                </div>';
                        html = html + '            </div>';
                        html = html + '        </div>';
                        html = html + '    </div>';

                        li               = document.createElement("li");
                        li.className     = "right";
                        li.style.cssText = "width: 60%; float: right;";
                        li.id            = "chatroom_msg" + obj['chatid'];
                        li.innerHTML     = html;
                        
                        let scroller  = document.getElementById("scroller"+id);
                        let li_anchor = document.getElementById("anchor"+id);
                        
                        scroller.insertBefore(li, li_anchor);
                    }

                    var elem = document.getElementById('scroller{{ $task->id }}');
                    elem.scrollTop = elem.scrollHeight;
                }
            }
        });
    }

    function deleteChat(id, docId) {
        new swal({
            text: "Please confirm to proceed delete.",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            confirmButtonColor: '#3085d6',
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "{{ route('chat.delete') }}",
                    data: {"_token": "{{ csrf_token() }}",'id': id, "docId": docId},
                    cache: false,
                    success: function (response) {
                        if (response.type == 'error') {
                            new swal ({
                                title: "@lang('translation.error')",
                                text: response.message,
                                icon: 'warning',
                                confirmButtonColor: '#3085d6'
                            })

                        } else {
                            new swal ({
                                title: "@lang('translation.success')",
                                text: response.message,
                                icon: 'success',
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let li = document.getElementById("chatroom_msg"+id);
                                    li.remove();
                                }
                            })
                        }
                    }
                });
            }
        })
    }

    function setReminder() {
        let mode = 'task';
        let uid  = $('#reminder_uid').val();
        let tid  = $('#reminder_tid').val();
        let d    = $('#reminder_date').val();
        let t    = $('#reminder_time').val();
        let m    = $('#reminder_msg').val();

        if (d == '' || t == '' || m == '') {
            new swal({
                title: "@lang('translation.error')",
                text: "Please fill all the fields.",
                icon: 'warning',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }

        new swal({
            text: "Please confirm to proceed set reminder.",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            confirmButtonColor: '#3085d6',
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "{{ route('reminder.update') }}",
                    data: {"_token": "{{ csrf_token() }}",'mode':mode, 'tid':tid, 'uid': uid, "d": d, "t": t, "m": m},
                    cache: false,
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        })
    }

    $('#submit_onhold').click(function(e){
        new swal({
            title: 'Please enter your comment to mark this task as on hold.',
            input: 'textarea',
            inputAttributes: {
                autocapitalize: 'off',
                maxlength: 500,
            },
            customClass: {
                container: 'text-class',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Send',
            showLoaderOnConfirm: false,
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value) {
                    $('#special_remark').val(result.value);
                    document.getElementById("submit_onhold_main").click();
                } else {
                    new swal(
                        'Oops...',
                        'Please enter your comment to proceed!',
                        'error'
                    )
                }
            }
        })
    })

    $('#submit_fallback').click(function(e){
        new swal({
            title: 'Please enter your comment to mark this task as fallback.',
            input: 'textarea',
            inputAttributes: {
                autocapitalize: 'off',
                maxlength: 500,
            },
            customClass: {
                container: 'text-class',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Send',
            showLoaderOnConfirm: false,
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value) {
                    $('#special_remark').val(result.value);
                    document.getElementById("submit_fallback_main").click();
                } else {
                    new swal(
                        'Oops...',
                        'Please enter your comment to proceed!',
                        'error'
                    )
                }
            }
        })
    })

    $('#submit_verify').click(function(e){
        new swal({
            title: 'Please enter your comment to mark this task as verified.',
            input: 'textarea',
            inputAttributes: {
                autocapitalize: 'off',
                maxlength: 500,
            },
            customClass: {
                container: 'text-class',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Send',
            showLoaderOnConfirm: false,
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value) {
                    $('#special_remark').val(result.value);
                    document.getElementById("submit_verify_main").click();
                } else {
                    new swal(
                        'Oops...',
                        'Please enter your comment to proceed!',
                        'error'
                    )
                }
            }
        })
    })

    $('#submit_complete').click(function(e){
        new swal({
            title: 'Please enter your comment to mark this task as complete.',
            input: 'textarea',
            inputAttributes: {
                autocapitalize: 'off',
                maxlength: 500,
            },
            customClass: {
                container: 'text-class',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Send',
            showLoaderOnConfirm: false,
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value) {
                    $('#special_remark').val(result.value);
                    document.getElementById("submit_complete_main").click();
                } else {
                    new swal(
                        'Oops...',
                        'Please enter your comment to proceed!',
                        'error'
                    )
                }
            }
        })
    })
    
    $('#submit_kiv').click(function(e){
        new swal({
            title: 'Please enter your reason to mark this task as KIV.',
            input: 'textarea',
            inputAttributes: {
                autocapitalize: 'off',
                maxlength: 500,
            },
            customClass: {
                container: 'text-class',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Send',
            showLoaderOnConfirm: false,
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value) {
                    $('#special_remark').val(result.value);
                    document.getElementById("submit_kiv_main").click();
                } else {
                    new swal(
                        'Oops...',
                        'Please enter your comment to proceed!',
                        'error'
                    )
                }
            }
        })
    })

    $('#submit_reject').click(function(e){
        new swal({
            title: 'Please enter your reason to mark this task as rejected.',
            input: 'textarea',
            inputAttributes: {
                autocapitalize: 'off',
                maxlength: 500,
            },
            customClass: {
                container: 'text-class',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Send',
            showLoaderOnConfirm: false,
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value) {
                    $('#special_remark').val(result.value);
                    document.getElementById("submit_reject_main").click();
                } else {
                    new swal(
                        'Oops...',
                        'Please enter your comment to proceed!',
                        'error'
                    )
                }
            }
        })
    })

</script>
@endsection