<div class="row">
    <form action="{{route('tasks.store')}}" method="post" id="add_form" name="add_form" enctype="multipart/form-data">
        {{csrf_field()}}

        <div>
            <!-- #1 Task Detail -->
            <div class="mt-1">
                <div style="padding:5px; background-color:rgb(13, 114, 158); color: white; border-color:white;">
                    Task Detail
                </div>
                <div class="m-2">
                    {{-- Only users who assign tasks to others pick the people;
                         anyone else's task is their own (TaskController@store). --}}
                    @cannot('create_task')
                    <div class="alert alert-info py-2 px-3 custom-font-small mb-2">
                        <i class="fas fa-user-check me-1"></i> You will start this task.
                    </div>
                    @else
                    <div class="row">
                        <!-- Subscriber -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="subscriber" class="custom-font-xsmall"><b>Subscriber</b> :</label>
                            <span class="pl-3" style="padding-left: 5px; color:red;">*</span>
                            <span class="text-danger">@if (!empty($errors->first('subscriber'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('subscriber') }}</span>
                            <select class="form-control form-select custom-font-small js-subscriber" name="subscriber" id="subscriber" style="width:100%;">
                                <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($subscriber as $c)
                                    <option value="{{ $c->id }}" {{ old('subscriber') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                            </select>
                        </div>

                        <!-- Sub-Subscriber -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="sub_subscriber" class="custom-font-xsmall"><b>Sub-Subscriber(s)</b> :</label>
                            <select class="form-control form-select custom-font-small js-subsubscriber-multiple" name="sub_subscriber[]" id="sub_subscriber" multiple style="width:100%;">
                                @foreach($subscriber as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
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
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Viewer -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="viewer" class="custom-font-xsmall"><b>Viewer(s)</b> :</label>
                            <select class="form-control form-select custom-font-small js-viewer-multiple" name="viewer[]" id="viewer" multiple style="width:100%;">
                                @foreach($viewer as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endcannot

                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                            <label for="title" class="custom-font-xsmall"><b>Title</b> :</label><span class="pl-3" style="padding-left: 5px; color:red;">*</span>
                            <input class="form-control form-control-sm custom-font-small" type="text" value="{{ old('title') }}" name="title" id="title" autocomplete="off">
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
                                    <input class="form-control form-control-sm custom-font-small" type="date" value="{{ old('task_start_date',date('Y-m-d')) }}" name="task_start_date" id="task_start_date" autocomplete="off">
                                </div>
                                <?php
                                    $timestamp = strtotime("9am");
                                ?>
                                <div class="col-md-6 col-xl-6">
                                    <input class="form-control form-control-sm custom-font-small" type="time" value="{{ old('task_start_time',date('H:i', $timestamp)) }}" id="task_start_time" name="task_start_time" autocomplete="off">
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
                                    <input class="form-control form-control-sm custom-font-small" type="date" value="{{ old('task_due_date',date('Y-m-d')) }}" name="task_due_date" id="task_due_date" autocomplete="off">
                                </div>
                                <?php
                                    $timestamp = strtotime("9am");
                                ?>
                                <div class="col-md-6 col-xl-6">
                                    <input class="form-control form-control-sm custom-font-small" type="time" value="{{ old('task_due_time',date('H:i', $timestamp)) }}" id="task_due_time" name="task_due_time" autocomplete="off">
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
                                    <input class="form-control form-control-sm custom-font-small" type="date" value="{{ old('task_appointment_date','') }}" name="task_appointment_date" id="task_appointment_date" autocomplete="off">
                                </div>
                                <?php
                                    $timestamp = strtotime("9am");
                                ?>
                                <div class="col-md-6 col-xl-6">
                                    <input class="form-control form-control-sm custom-font-small" type="time" value="{{ old('task_appointment_time','') }}" id="task_appointment_time" name="task_appointment_time" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Remark -->
                        <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                            <label for="remark" class="custom-font-xsmall"><b>Remark</b> :</label>
                            <textarea style="height:100px;" name="remark" id="remark" class="ckeditor form-control form-control-sm small chat-input small" autocomplete="off">{{ old('remark') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- #2 Lead Detail -->
            <div class="mt-1">
                <div style="padding:5px; background-color:rgb(13, 114, 158); color: white; border-color:white;">
                    Lead/Customer Detail<span style="font-size:10pt; margin-left:5px; color: rgb(252, 245, 155);">(view only)</span>
                </div>
                <div class="m-2">
                    <div class="row">
                        <!-- Company Name -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="leadname" class="custom-font-xsmall"><b>Company Name</b> :</label>
                            <input type="hidden" name="lead_id" id="lead_id" value="{{ $lead->id }}" readonly>
                            <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="leadname" id="leadname" value="{{ $lead->name }}" readonly>
                        </div>

                        <!-- Shop Name -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="leadname" class="custom-font-xsmall"><b>Shop Name</b> :</label>
                            <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="businessname" id="businessname" value="{{ $lead->business_name }}" readonly>
                        </div>

                        <!-- Customer ID -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="leadname" class="custom-font-xsmall"><b>Customer ID</b> :</label>
                            <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="customerid" id="customerid" value="{{ $lead->customer_id }}" readonly>
                        </div>

                        <!-- Created By -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="createdby" class="custom-font-xsmall"><b>Lead/Customer Created By</b> :</label>
                            <input type="hidden" name="belong_to" id="belong_to" value="{{ $lead->belong_to }}" readonly>
                            <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="createdby" id="createdby" value="{{ $lead->createdBy->name }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Mobile -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="mobile" class="custom-font-xsmall"><b>@lang('translation.mobile')</b> :</label>
                            <input type="tel" class="form-control form-control-sm custom-font-small" name="mobile" id="mobile" value="{{ $lead->mobile }}" readonly>
                        </div>

                        <!-- Email -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="email" class="custom-font-xsmall"><b>@lang('translation.email')</b> :</label>
                            <input type="email" class="form-control form-control-sm custom-font-small" parsley-type="email" name="email" id="email" value="{{ $lead->email }}" readonly>
                        </div>

                        <!-- Source -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="leadsource_v" class="custom-font-xsmall"><b>Source of Lead/Customer</b> :</label><br/>
                            <input class="form-control form-control-sm custom-font-small" type="text" value="{{ \App\Helpers\Helper::getLeadSource($lead->source) }}" name="leadsource_v" id="leadsource_v" readonly>
                            <input class="form-control form-control-sm custom-font-small" type="hidden" value="{{ $lead->source }}" name="leadsource" id="leadsource" readonly>
                        </div>

                        <!-- Business Category -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="businesscat_v" class="custom-font-xsmall"><b>Business Category</b> :</label><br/>
                            <input class="form-control form-control-sm custom-font-small" type="text" value="{{ \App\Helpers\Helper::getBusinessCategory($lead->business_category) }}" name="businesscat_v" id="businesscat_v" readonly>
                            <input class="form-control form-control-sm custom-font-small" type="hidden" value="{{ $lead->business_category }}" name="businesscat" id="businesscat" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Address -->
                        <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                            <label for="address" class="custom-font-xsmall"><b>@lang('translation.address')</b> :</label>
                            <textarea type="text" rows="3" maxlength="500" class="form-control form-control-sm custom-font-small" name="address" id="address" readonly>{{ $lead->address }}</textarea>
                        </div>

                        <!-- States -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="leadstate" class="custom-font-xsmall"><b>@lang('translation.state')</b> :</label>
                            <input class="form-control form-control-sm custom-font-small" type="text" value="{{ \App\Helpers\Helper::getState($lead->state_id) }}" name="leadstate" id="leadstate" readonly>
                        </div>

                        <!-- Cities -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="leadcity" class="custom-font-xsmall"><b>@lang('translation.city')</b> :</label>
                            <input class="form-control form-control-sm custom-font-small" type="text" value="{{ \App\Helpers\Helper::getCity($lead->city_id) }}" name="leadcity" id="leadcity" readonly>
                        </div>

                        <!-- Postcode -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="leadpostcode" class="custom-font-xsmall"><b>@lang('translation.postcode')</b> :</label>
                            <input class="form-control form-control-sm custom-font-small" type="text" value="{{ $lead->postcode }}" name="leadpostcode" id="leadpostcode" readonly>
                        </div>

                        <!-- IFE Area -->
                        <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                            <label for="ifearea" class="custom-font-xsmall"><b>IFE Area</b> :</label><span class="text-danger">@if (!empty($errors->first('ifearea'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('city_id') }}</span>
                            <select class="js-select2-area_city form-control custom-font-small" name="ifearea" id="ifearea" style="width:100%;" disabled>
                                <option value="">-- Select IFE Area --</option>
                                @foreach($ifeareas as $c)
                                    <option value="{{ $c->id }}" {{ $lead->ife_area_id == $c->id ? 'selected' : '' }}>{{ $c->area }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Remark -->
                        <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                            <label for="leadremark" class="custom-font-xsmall"><b>Note on Lead/Customer</b> :</label>
                            <textarea type="text" rows="10" readonly class="form-control form-control-sm custom-font-small" name="leadremark" id="leadremark">{{ $lead->remark }}</textarea>
                        </div>
                    </div> 

                    <div class="row">
                        <div><b>Document</b></div>

                        <div class="row">
                            <div>
                                <label class="custom-font-xxsmall">Supported File: png, jpg, pdf, word. excel, wav, mp4 & other audio format</label>
                                <div class="form-group">
                                    <input type="file" onchange="fileValidation('file')" name="file[]" id="file" accept=".jpeg, .jpg, .png, .pdf, audio/*, .ppt, .pptx,  .xls, .xlsx, application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword" multiple>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 10px; overflow-x:auto; white-space: nowrap;">
                            <table class="table table-sm table-hover table-bordered custom-font-small custom-shadow" style="width:100%">
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
                                    @if(count($lead->documentUploads) >0)
                                        @foreach($lead->documentUploads as $item)
                                        <tr>
                                            <td scope="row">{{ $item->filename }}</td>
                                            <td scope="row">{{ $item->created_at }}</td>
                                            <td scope="row">{{ $item->uploadBy->name }}</td>
                                            <td scope="row">{{ $item->size }} KB</td>
                                            <td style="border-bottom: 1px solid #acacac; border-bottom: 1px solid #acacac; text-align:center;">
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
                                                    <div class="float-end" style="margin-top: 3px; margin-bottom: 3px; height: 150px; overflow-y: scroll; width: 320px;">
                                                        <img id="{{ 'img'.$item->id }}" src="{{ $item->file_full_path }}" style="width:300px;"/>
                                                    </div>
                                                    @endif
                                                    @if($isPdf !== false)
                                                    <object class="float-end " data="{{ $item->file_full_path }}"
                                                            width="300px"
                                                            height="150px">
                                                    </object>
                                                    @endif
                                                </td>
                                            <td style="text-align:center;">
                                                <a href="{{ route('lead.file.download',['doc_id'=>$item->id]) }}"><i class="fas fa-cloud-download-alt fa-lg"></i></a>&NonBreakingSpace;
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <div class="custom_float">
                <a href="{{ route('lead.index') }}" class="btn btn-light custom-button-shadow" style="width:100px">@lang('translation.back')</a>
                <button class="btn btn-primary custom-button-shadow" style="width:130px" id="buttonOne" type="submit">@lang('translation.submit')</button>
            </div>
        </div>
    </form> 
</div>

@section('script')
<script>
    CKEDITOR.config.allowedContent = true;
    CKEDITOR.config.extraPlugins   = 'colorbutton';
    CKEDITOR.config.removeButtons  = 'Image,Cut,Copy,Paste,PasteText,Source,Blockquote';
    CKEDITOR.config.removePlugins  = 'exportpdf';
    CKEDITOR.config.versionCheck   = false;
    CKEDITOR.config.toolbar        = [
                                        [ 'Undo', 'Redo' ],['TextColor', 'BGColor'],
                                        { name: 'links',       items : [ 'Link','Unlink' ] },
                                        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'FontSize' ] }
                                    ];
    CKEDITOR.config.enterMode      = CKEDITOR.ENTER_DIV;
    CKEDITOR.config.shiftEnterMode = CKEDITOR.ENTER_DIV;

    $(".js-subscriber").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $(".js-owner-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $(".js-viewer-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $(".js-subsubscriber-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $("#ifearea").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });

    $('form').submit(function(){
        $(this).find(':submit').attr( 'disabled','disabled' );
        setTimeout(() => {
            $(this).find(':submit').attr( 'disabled',false );
        }, 3000)
    });

    $("#add_form").bind("keypress", function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });
    
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
</script>
@endsection