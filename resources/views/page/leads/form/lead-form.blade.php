<div class="row">
    <div class="mt-1">
        <!-- Lead Information -->
        <div>
            <div style="padding:5px; background-color:rgb(13, 114, 158); color: white; border-color:white;">
                Lead/Customer Detail
            </div>
            <div class="m-2">
                <div class="row">
                    <!-- Company name -->
                    <div class="col-md-9 col-xl-9" style="padding-bottom:10px;">
                        <label for="name" class="custom-font-xsmall"><b>Company Name</b> :</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('name'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('name') }}</span>
                        @if($type == 'add')
                            <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="name" id="name" value="{{ old('name') }}" autocomplete="off">
                        @else
                            <input type="hidden" name="belong_to" id="belong_to" value="{{ $customerDetail->belong_to }}" autocomplete="off">
                            <input type="hidden" name="assign_to" id="assign_to" value="{{ $customerDetail->assign_to }}" autocomplete="off">
                            <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="name" id="name" value="{{ old('name',$customerDetail->name) }}" autocomplete="off" {{ $type == 'view' ? 'readonly' : '' }}>
                        @endif
                    </div>

                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="receive_date" class="custom-font-xsmall"><b>Receiving Date</b> :</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('receive_date'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('receive_date') }}</span>
                        <span class="text-danger">@if (!empty($errors->first('task_start_date'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('task_start_date') }}</span>
                        <input class="form-control form-control-sm custom-font-small" type="date" value="{{ old('task_start_date',date('Y-m-d')) }}" name="receive_date" id="receive_date" autocomplete="off">
                    </div>
                </div>

                <div class="row">
                    <!-- Shop name -->
                    <div class="col-md-9 col-xl-9" style="padding-bottom:10px;">
                        <label for="name" class="custom-font-xsmall"><b>Shop Name</b> :</label>
                        @if($type == 'add')
                            <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="business_name" id="business_name" value="{{ old('business_name') }}" autocomplete="off">
                        @else
                            <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="business_name" id="business_name" value="{{ old('business_name',$customerDetail->business_name) }}" autocomplete="off" {{ $type == 'view' ? 'readonly' : '' }}>
                        @endif
                    </div>

                    <!-- customer id -->
                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="name" class="custom-font-xsmall"><b>Customer ID</b> :</label>
                        @if($type == 'add')
                            <input type="text" maxlength="20" class="form-control form-control-sm custom-font-small" name="customer_id" id="customer_id" value="{{ old('customer_id') }}" autocomplete="off">
                        @else
                            <input type="text" maxlength="20" class="form-control form-control-sm custom-font-small" name="customer_id" id="customer_id" value="{{ old('customer_id',$customerDetail->customer_id) }}" autocomplete="off" {{ $type == 'view' ? 'readonly' : '' }}>
                        @endif
                    </div>

                </div>

                <div class="row">
                    <!-- Mobile -->
                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="mobile" class="custom-font-xsmall"><b>@lang('translation.mobile')</b> :</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('mobile'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('mobile') }}</span>
                        @if($type == 'add')
                            <input type="tel" maxlength="12" onkeypress="return onlyNumberKey(event)" class="form-control form-control-sm custom-font-small" name="mobile" id="mobile" value="{{ old('mobile') }}" autocomplete="off">
                        @else
                            <input type="tel" maxlength="12" onkeypress="return onlyNumberKey(event)" class="form-control form-control-sm custom-font-small" name="mobile" id="mobile" value="{{ old('mobile',$customerDetail->mobile) }}" autocomplete="off" {{ $type == 'view' ? 'readonly' : '' }}>
                        @endif
                    </div>

                    <!-- Email -->
                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="email" class="custom-font-xsmall"><b>@lang('translation.email')</b> :</label><span class="text-danger">@if (!empty($errors->first('email'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('email') }}</span>
                        @if($type == 'add')
                            <input type="email" maxlength="255" class="form-control form-control-sm custom-font-small" parsley-type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="off">
                        @else
                            <input type="email" maxlength="255" class="form-control form-control-sm custom-font-small" parsley-type="email" name="email" id="email" value="{{ old('email',$customerDetail->email) }}" autocomplete="off" {{ $type == 'view' ? 'readonly' : '' }}>
                        @endif
                    </div>

                    <!-- Source -->
                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="state_id" class="custom-font-xsmall"><b>Source of Lead/Customer</b> :</label><span class="text-danger">@if (!empty($errors->first('state_id'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('state_id') }}</span>
                        @if($type == 'add')
                            <select class="form-control custom-font-small" name="leadsource" id="leadsource" onchange="getCities()" style="width:100%;">
                                <option value="">-- Select Source --</option>
                                @foreach(\App\Helpers\Helper::getLeadSourceListing() as $key => $leadsource)
                                    <option value="{{ $key }}" {{ old('leadsource') == $key ? 'selected' : '' }}>{{ $leadsource }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="form-control custom-font-small" name="leadsource" id="leadsource" onchange="getCities()" style="width:100%;" {{ $type == 'view' ? 'disabled' : '' }}>
                                <option value="">-- Select Source --</option>
                                @foreach(\App\Helpers\Helper::getLeadSourceListing() as $key => $leadsource)
                                    <option value="{{ $key }}" {{ old('leadsource',$customerDetail->source) == $key ? 'selected' : '' }}>{{ $leadsource }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <!-- Business Category -->
                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="state_id" class="custom-font-xsmall"><b>Business Category</b> :</label><span class="text-danger">@if (!empty($errors->first('state_id'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('state_id') }}</span>
                        @if($type == 'add')
                            <select class="form-control custom-font-small" name="businesscat" id="businesscat" onchange="getCities()" style="width:100%;">
                                <option value="">-- Select Cateogry --</option>
                                @foreach(\App\Helpers\Helper::getBusinessCategoryListing() as $key => $businesscat)
                                    <option value="{{ $key }}" {{ old('businesscat') == $key ? 'selected' : '' }}>{{ $businesscat }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="form-control custom-font-small" name="businesscat" id="businesscat" onchange="getCities()" style="width:100%;" {{ $type == 'view' ? 'disabled' : '' }}>
                                <option value="">-- Select Category --</option>
                                @foreach(\App\Helpers\Helper::getBusinessCategoryListing() as $key => $businesscat)
                                    <option value="{{ $key }}" {{ old('businesscat',$customerDetail->business_category) == $key ? 'selected' : '' }}>{{ $businesscat }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <!-- Address -->
                    <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                        <label for="address" class="custom-font-xsmall"><b>@lang('translation.address')</b> :</label><br/>
                        <span class="text-danger">@if (!empty($errors->first('address'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('address') }}</span>
                        @if($type == 'add')
                        <textarea type="text" rows="3" maxlength="500" {{ $type == 'view' ? 'readonly' : '' }} class="form-control form-control-sm custom-font-small" name="address" id="address" autocomplete="off">{{ old('address') }}</textarea>
                        @else
                        <textarea type="text" rows="3" maxlength="500" {{ $type == 'view' ? 'readonly' : '' }} class="form-control form-control-sm custom-font-small" name="address" id="address" autocomplete="off">{{ old('mobile',$customerDetail->address) }}</textarea>
                        @endif
                    </div>

                    <!-- States -->
                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="state_id" class="custom-font-xsmall"><b>@lang('translation.state')</b> :</label><span class="text-danger">@if (!empty($errors->first('state_id'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('state_id') }}</span>
                        @if($type == 'add')
                            <select class="js-select2-state form-control custom-font-small" name="state_id" id="state_id" onchange="getCities()" style="width:100%;">
                                <option value="">-- @lang('translation.select_state') --</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="js-select2-state form-control custom-font-small" name="state_id" id="state_id" onchange="getCities()" style="width:100%;" {{ $type == 'view' ? 'disabled' : '' }}>
                                <option value="">-- @lang('translation.select_state') --</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('area_state',$customerDetail->state_id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <!-- Cities -->
                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="area_city" class="custom-font-xsmall"><b>@lang('translation.city')</b> :</label><span class="text-danger">@if (!empty($errors->first('city_id'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('city_id') }}</span>
                        @if($type == 'add')
                            <select class="js-select2-city form-control custom-font-small" name="city_id" id="city_id" style="width:100%;">
                                <option value="">-- @lang('translation.select_city') --</option>
                                @if (null !== old('state_id'))
                                    @foreach($cities as $c)
                                        @if($c->state_id == old('state_id'))
                                            <option value="{{ $c->id }}" {{ old('city_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        @else
                            <select class="js-select2-city form-control custom-font-small" name="city_id" id="city_id" style="width:100%;" {{ $type == 'view' ? 'disabled' : '' }}>
                                <option value="">-- @lang('translation.select_city') --</option>
                                @if (null !== old('state_id',$customerDetail->state_id))
                                    @foreach($cities as $c)
                                        @if($c->state_id == old('state_id',$customerDetail->state_id))
                                            <option value="{{ $c->id }}" {{ old('city_id',$customerDetail->city_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        @endif
                    </div>

                    <!-- Postcode -->
                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="postcode" class="custom-font-xsmall"><b>@lang('translation.postcode')</b> :</label><span class="text-danger">@if (!empty($errors->first('postcode'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('postcode') }}</span>
                        @if($type == 'add')
                            <input type="tel" maxlength="5" onkeypress="return onlyNumberKey(event)" class="form-control form-control-sm custom-font-small" name="postcode" id="postcode" value="{{ old('postcode') }}" autocomplete="off">
                        @else
                            <input type="tel" maxlength="5" onkeypress="return onlyNumberKey(event)" class="form-control form-control-sm custom-font-small" name="postcode" id="postcode" value="{{ old('postcode',$customerDetail->postcode) }}" autocomplete="off" {{ $type == 'view' ? 'readonly' : '' }}>
                        @endif
                    </div>

                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="ifearea" class="custom-font-xsmall"><b>IFE Area</b> :</label><span class="text-danger">@if (!empty($errors->first('ifearea'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('city_id') }}</span>
                        @if($type == 'add')
                            <select class="js-select2-area_city form-control custom-font-small" name="ifearea" id="ifearea" style="width:100%;">
                                <option value="">-- Select IFE Area --</option>
                                @foreach($ifeareas as $c)
                                    <option value="{{ $c->id }}" {{ old('ifearea') == $c->id ? 'selected' : '' }}>{{ $c->area }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="js-select2-area_city form-control custom-font-small" name="ifearea" id="ifearea" style="width:100%;" {{ $type == 'view' ? 'disabled' : '' }}>
                                <option value="">-- Select IFE Area --</option>
                                @foreach($ifeareas as $c)
                                    <option value="{{ $c->id }}" {{ old('ifearea',$customerDetail->ife_area_id) == $c->id ? 'selected' : '' }}>{{ $c->area }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                {{-- Outlet profile: what the recommendation engine compares outlets
                     on, and where the outlet is. The location is captured on site
                     with the same helper as a GPS Stamp form field. Manual only:
                     editing at the office must not stamp the office. --}}
                @php
                    $profile   = $type == 'add' ? null : $customerDetail;
                    $readonly  = $type == 'view';
                    $gpsStamp  = old('gps', $profile ? json_encode($profile->locationStamp()) : '');
                    $gpsStamp  = $gpsStamp === 'null' ? '' : $gpsStamp;
                @endphp
                <div class="row">
                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="size_band" class="custom-font-xsmall"><b>Outlet Size</b> :</label><span class="text-danger">{{ $errors->first('size_band') }}</span>
                        <select class="form-control custom-font-small" name="size_band" id="size_band" style="width:100%;" {{ $readonly ? 'disabled' : '' }}>
                            <option value="">-- Select Size --</option>
                            @foreach(\App\Models\Leads::SIZE_BANDS as $key => $label)
                                <option value="{{ $key }}" {{ old('size_band', optional($profile)->size_band) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="seats" class="custom-font-xsmall"><b>Seats</b> :</label><span class="text-danger">{{ $errors->first('seats') }}</span>
                        <input type="number" min="0" max="5000" class="form-control form-control-sm custom-font-small" name="seats" id="seats"
                               value="{{ old('seats', optional($profile)->seats) }}" autocomplete="off" {{ $readonly ? 'readonly' : '' }}>
                    </div>

                    <div class="col-md-3 col-xl-3" style="padding-bottom:10px;">
                        <label for="segment" class="custom-font-xsmall"><b>Segment</b> :</label><span class="text-danger">{{ $errors->first('segment') }}</span>
                        <select class="form-control custom-font-small" name="segment" id="segment" style="width:100%;" {{ $readonly ? 'disabled' : '' }}>
                            <option value="">-- Select Segment --</option>
                            @foreach(\App\Models\Leads::SEGMENTS as $key => $label)
                                <option value="{{ $key }}" {{ old('segment', optional($profile)->segment) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                        <label class="custom-font-xsmall"><b>Outlet Location</b> :</label>
                        <span class="text-danger">@if (!empty($errors->first('gps'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('gps') }}</span>
                        <div class="gps-field border rounded p-2">
                            <input type="hidden" class="gps-input" name="gps" data-el-id="lead_gps" data-capture-mode="manual"
                                   value="{{ $gpsStamp }}" {{ $readonly ? 'disabled' : '' }}>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <div class="gps-readout flex-grow-1 small text-muted">
                                    <i class="mdi mdi-map-marker-off-outline me-1"></i>Location not stamped yet.
                                </div>
                                @unless($readonly)
                                    <button type="button" class="btn btn-sm btn-outline-primary gps-capture-btn">
                                        <i class="mdi mdi-crosshairs-gps me-1"></i><span class="gps-btn-label">Stamp location</span>
                                    </button>
                                @endunless
                            </div>
                            <div class="gps-status small mt-1"></div>
                        </div>
                        @unless($readonly)
                            <div class="custom-font-xxsmall text-muted mt-1">Stamp this while you are at the outlet.</div>
                        @endunless
                    </div>
                </div>
                <script src="{{ asset('js/forms/form-gps.js') }}"></script>

                <div class="row">
                    <!-- Remark -->
                    <div class="col-md-12 col-xl-12" style="padding-bottom:10px;">
                        <label for="remark" class="custom-font-xsmall"><b>@lang('translation.remark')</b> :</label><br/>
                        <span class="text-danger">@if (!empty($errors->first('remark'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('remark') }}</span>
                        @if($type == 'add')
                        <textarea type="text" rows="10" maxlength="255" {{ $type == 'view' ? 'readonly' : '' }} class="form-control form-control-sm custom-font-small" name="remark" id="remark" autocomplete="off">{{ old('remark') }}</textarea>
                        @else
                        <textarea type="text" rows="10" maxlength="255" {{ $type == 'view' ? 'readonly' : '' }} class="form-control form-control-sm custom-font-small" name="remark" id="remark" autocomplete="off">{{ old('postcode',$customerDetail->remark) }}</textarea>
                        @endif
                    </div>
                </div>

                @if($type == 'view')
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
                                @if(count($customerDetail->documentUploads) > 0)
                                    @foreach($customerDetail->documentUploads as $item)
                                    <tr>
                                        <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->filename }}</td>
                                        <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->created_at }}</td>
                                        <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->uploadBy->name }}</td>
                                        <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->size }} KB</td>
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
                                            <audio controls class="custom-shadow">
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
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @elseif($type == 'add')
                <div class="row">
                    <div>
                        <label class="custom-font-xxsmall">Supported File: png, jpg, pdf, excel, word, wav, mp4 & other audio format</label>
                        <div class="form-group">
                            <input type="file" onchange="fileValidation('file')" name="file[]" id="file" accept=".jpeg, .jpg, .png, .pdf, .ppt, .pptx,  audio/*" multiple>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Handler Information -->
        <div style="display:none;">
            <div style="padding:5px; background-color:rgb(13, 114, 158); color: white; border-color:white;">
                Handler Detail
            </div>
            <div class="m-2">
                <div class="row">
                    <!-- handler name -->
                    <div class="col-md-6 col-xl-6" style="padding-bottom:20px;">
                        <label for="name" class="custom-font-xsmall">Handler Name :</label>
                        @if($type == 'add')
                            <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="handler_name" id="handler_name" value="{{ old('handler_name') }}" autocomplete="off">
                        @else
                            <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="handler_name" id="handler_name" value="{{ old('handler_name',$customerDetail->handler_name) }}" autocomplete="off" {{ $type == 'view' ? 'readonly' : '' }}>
                        @endif
                    </div>

                    <!-- handler id -->
                    <div class="col-md-6 col-xl-6" style="padding-bottom:20px;">
                        <label for="name" class="custom-font-xsmall">Handler ID :</label>
                        @if($type == 'add')
                            <input type="text" maxlength="12" class="form-control form-control-sm custom-font-small" name="handler_id" id="handler_id" value="{{ old('handler_id') }}" onkeypress="return onlyNumberKey(event)" autocomplete="off">
                        @else
                            <input type="text" maxlength="12" class="form-control form-control-sm custom-font-small" name="handler_id" id="handler_id" value="{{ old('handler_id',$customerDetail->handler_id) }}" onkeypress="return onlyNumberKey(event)" autocomplete="off" {{ $type == 'view' ? 'readonly' : '' }}>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        
        <!-- Task Histories -->
        @if($type == 'view')
        <div>
            <div style="padding:5px; background-color:rgb(13, 114, 158); color: white; border-color:white;">
                Task Histories
            </div>
            <div class="m-2">
                <div class="row">
                    <div style="margin-top: 10px; overflow-x:auto; white-space: nowrap;">
                        <table class="table table-sm custom-font-small" style="width:100%; border-collapse: collapse; box-shadow: 1px 1px 3px 1px rgb(183, 183, 183);">
                            <thead class="table-dark">
                                <tr>
                                    <td scope="col">#</td>
                                    <td scope="col">Lead/Customer Information</td>
                                    <td scope="col">Task Information</td>
                                    <td scope="col">Manage By</td>
                                    <td scope="col">Status</td>
                                    <td scope="col" style="text-align: center; width:30px"></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerDetail->tasks as $index => $s)
                                <tr>
                                    <td style="border-bottom: 1px solid #acacac;" scope="row">
                                        {{ $index+1 }}
                                    </td>
                                    <td style="border-bottom: 1px solid #acacac;" class="custom-font-xsmall">
                                        <span style="text-transform: uppercase;">
                                            @if ($s->lead->customer_id)
                                                <img class="rounded-circle header-profile-user" src="{{ URL::asset('/assets/images/users/cartoon-boy-profile.png') }}" height="10" alt="Header Avatar">
                                            @endif
                                            {{ $s->lead->name }}
                                        </span>
                                        <table>
                                            <tr>
                                                <td>Shop Name</td>
                                                <td style="width:1px;">:</td>
                                                <td><font class="custom-table-tr-td-font-color">{{ $s->lead->business_name }}</font></td>
                                            </tr>
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
                                                <td>@lang('translation.email')</td>
                                                <td style="width:1px;">:</td>
                                                <td><font class="custom-table-tr-td-font-color">{{ $s->lead->email }}</font></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="border-bottom: 1px solid #acacac;" class="custom-font-xsmall">
                                        <table>
                                            <tr>
                                                <td>@lang('translation.reference_no')</td>
                                                <td style="width:1px;">:</td>
                                                <td><font class="custom-table-tr-td-font-color">{{ $s->task_reference }}</font></td>
                                            </tr>
                                            <tr>
                                                <td>Lead/Customer Source</td>
                                                <td style="width:1px;">:</td>
                                                <td><font class="custom-table-tr-td-font-color">{{ \App\Helpers\Helper::getLeadSource($s->lead->source) }}</font></td>
                                            </tr>
                                            <tr>
                                                <td>Business Category</td>
                                                <td style="width:1px;">:</td>
                                                <td><font class="custom-table-tr-td-font-color">{{ \App\Helpers\Helper::getBusinessCategory($s->lead->business_category) }}</font></td>
                                            </tr>
                                            <tr>
                                                <td>Appointment Date</td>
                                                <td style="width:1px;">:</td>
                                                <td>
                                                    <font class="custom-table-tr-td-font-color" style="color: red;">
                                                    <div style="padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(18, 51, 158); color: white; width: fit-content;">
                                                        {{ $s->appointment_date ? date('Y M d h:i A', strtotime($s->appointment_date)) : '' }}
                                                    </div>
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="border-bottom: 1px solid #acacac;" class="custom-font-xsmall">
                                        <table>
                                            <tr>
                                                <td>Subscriber</td>
                                                <td style="width:1px;">:</td>
                                                <td>
                                                    <font class="custom-table-tr-td-font-color">
                                                    <div style="cursor: pointer; padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(7, 111, 36); color: white; width: fit-content;">
                                                        {{ $s->users->where('role',2)->first() ? $s->users->where('role',2)->first()->user->name : '' }}
                                                    </div>
                                                    </font>
                                                </td>
                                            </tr>
                                            <?php
                                                $cnt = 0;
                                            ?>
                                            @foreach($s->users->where('role',6) as $u)
                                            <?php
                                                $cnt = $cnt+1;
                                            ?>
                                            <tr>
                                                <td>{{ $cnt == 1 ? 'Sub-Subscriber(s)' : '' }}</td>
                                                <td>{{ $cnt == 1 ? ':' : '' }}</td>
                                                <td><font class="custom-table-tr-td-font-color">{{ $u->user->name }}</font></td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td>Checked By</td>
                                                <td style="width:1px;">:</td>
                                                <td><font class="custom-table-tr-td-font-color">{{ $s->users->where('role',3)->first() ? $s->users->where('role',3)->first()->user->name : '' }}</font></td>
                                            </tr>
                                            <tr>
                                                <td>Created By</td>
                                                <td style="width:1px;">:</td>
                                                <td><font class="custom-table-tr-td-font-color">{{ $s->users->where('role',1)->first() ? $s->users->where('role',1)->first()->user->name : '' }}</font></td>
                                            </tr>
                                            <?php
                                                $cnt = 0;
                                            ?>
                                            @foreach($s->users->where('role',4) as $u)
                                            <?php
                                                $cnt = $cnt+1;
                                            ?>
                                            <tr>
                                                <td>{{ $cnt == 1 ? 'Owner(s)' : '' }}</td>
                                                <td>{{ $cnt == 1 ? ':' : '' }}</td>
                                                <td><font class="custom-table-tr-td-font-color">{{ $u->user->name }}</font></td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                    <td style="border-bottom: 1px solid #acacac;" class="custom-font-xsmall">
                                        <div style="cursor: pointer; padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(30, 7, 111); color: white; width: fit-content;">{{$s->getTaskStatus($s->status)}}</div>
                                        @if($s->status == 1)
                                            <span style="color:blue;">Created</span> on <br/>
                                            {{ date('Y M d h:i A', strtotime($s->created_at)) }} <br/>

                                        @elseif($s->status == 2)
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

                                        @endif

                                        @if($s->comments->where('submit_by',$s->users->whereIn('role',[2,6])->first()->user->id)->count() > 0)
                                        <div style="margin-top: 10px; color:red;">Last Updated on</div>
                                        <div style="padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(18, 51, 158); color: white; width: fit-content;">{{ date('Y M d h:i A', strtotime($s->comments->where('submit_by',$s->users->whereIn('role',[2,6])->first()->user->id)->sortBy('created_at')->first()->created_at)) }}</div>
                                        @endif
                                    </td>
                                    <td style="border-bottom: 1px solid #acacac;">
                                        <div style="padding-right: 8px;">
                                            <a href="{{ config('app.url') }}/v1/task/manage/view/{{ $s->id }}?mode=comment" target="_blank"><i class="mdi mdi-forum-outline" style="margin-right:3px; font-size: 22px;"></i></a>
                                            <a href="{{ config('app.url') }}/v1/task/manage/view/{{ $s->id }}" target="_blank"><i class="mdi mdi-clipboard-outline" style="margin-right:3px; font-size: 22px;"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@section('script')
<script>
    $(".js-select2-state").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });
    $(".js-select2-city").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });
    $("#leadsource").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });
    $("#businesscat").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });
    $("#ifearea").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });
    $("#size_band").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white", minimumResultsForSearch: Infinity });
    $("#segment").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white", minimumResultsForSearch: Infinity });

    var uploadedDocumentMap = {}
    
    Dropzone.autoDiscover = true; 

    Dropzone.options.documentDropzone = {
        url: "{{ route('lead.file.store') }}",
        paramName: "file",
        maxFiles: 25,
        maxFilesize: 10, // MB
        parallelUploads: 25,
        uploadMultiple: true,
        addRemoveLinks: true,
        autoProcessQueue: true, //set true if want direct call api upload, in this case remove the button
        acceptedFiles: 'image/png, image/jpg, image/jpeg, application/pdf, audio/*, .doc, .docx, .pdf, .xls, .xlsx, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function (file, response) {
            $('#frmUploadDocument').append('<input type="hidden" name="document[]" value="' + response.name + '">')
            uploadedDocumentMap[file.name] = response.name
            location.reload();
        },
        removedfile: function (file) {
            file.previewElement.remove()
            var name = ''

            if (typeof file.filename !== 'undefined') {
                name = file.filename
            } else {
                name = uploadedDocumentMap[file.name]
            }

            $('#frmUploadDocument').find('input[name="document[]"][value="' + name + '"]').remove()
        },
        init: function () {
            this.on('sending', function(file, xhr, formData) {
                formData.append("leadid",$('#leadid').val());
            });
        }
    }

    $('form').submit(function(){
        $(this).find(':submit').attr( 'disabled','disabled' );
        setTimeout(() => {
            $(this).find(':submit').attr( 'disabled',false );
        }, 3000)
    });

    $("#frmUploadDocument").bind("keypress", function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });

    function getCities() {
        var x       = document.getElementById("state_id").value;
        var select  = $('form select[name=city_id]');
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