<div>
    <!-- Name -->
    <label for="name">Name:</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('name'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('name') }}</span>
    @if($type == 'add')
        <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="name" id="name" value="{{ old('name') }}" autocomplete="off">
    @else
        <input type="text" maxlength="255" class="form-control form-control-sm custom-font-small" name="name" id="name" value="{{ $user->name }}" {{ $type == 'view' ? 'readonly' : '' }} autocomplete="off">
    @endif
    <br />

    <!-- Username -->
    @if($type != 'add')
        <label for="username">@lang('translation.username'):</label>
        <input type="text" class="form-control form-control-sm custom-font-small" name="username" id="username" value="{{ $user->username }}" readonly autocomplete="off">
        <br />
    @endif

    <div class="row">
        <!-- Team -->
        <div class="col-md-6 col-xl-6">
            <label for="team">Team:</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('team'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('team') }}</span>
            @if($type == 'view')
                <input type="text" class="form-control custom-font-small" name="team2" id="team2" value="{{ \App\Helpers\Helper::getTeam($user->team) }}" readonly autocomplete="off">
            @else
                <div>
                    <select class="form-control form-select custom-font-small" name="team" id="team">
                        <option value="">-- @lang('translation.select_your_choice') --</option>
                        @foreach(\App\Helpers\Helper::getTeamListing() as $key => $team)
                            <option value="{{ $key }}" {{ old('team', $user->team ?? null) == $key ? 'selected' : '' }}>{{ $team }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        <!-- User Type -->
        <div class="col-md-6 col-xl-6">
            <label for="type">User Type:</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('type'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('type') }}</span>
            @if($type == 'view')
                <input type="text" class="form-control custom-font-small" name="type2" id="type2" value="{{ \App\Models\User::getUserType($user->type) }}" readonly autocomplete="off">
            @else
                <div>
                    <select class="form-control form-select custom-font-small" name="type" id="type">
                        <option value="">-- @lang('translation.select_your_choice') --</option>
                        @foreach(\App\Models\User::getUserTypeListing() as $key => $userType)
                            <option value="{{ $key }}" {{ (string) old('type', $user->type ?? '') === (string) $key ? 'selected' : '' }}>{{ $userType }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>
    <br/>

    <div class="row">
        <!-- Chat ID -->
        <div class="col-md-6 col-xl-6">
            <label for="telegram_chat_id">@lang('translation.chat_id'):</label>
            @if($type == 'add')
                <input type="text" maxlength="50" class="form-control form-control-sm custom-font-small" name="telegram_chat_id" id="telegram_chat_id" value="{{ old('telegram_chat_id') }}" autocomplete="off">
            @else
                <input type="text" maxlength="50" class="form-control form-control-sm custom-font-small" name="telegram_chat_id" id="telegram_chat_id" value="{{ $user->telegram_chat_id }}" {{ $type == 'view' ? 'readonly' : '' }} autocomplete="off">
            @endif
        </div>
        <!-- Gender -->
        <div class="col-md-6 col-xl-6">
            <label for="gender">@lang('translation.gender'):</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('gender'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('gender') }}</span>
            @if($type == 'view')
                <input type="text" class="form-control custom-font-small" name="gender2" id="gender2" value="{{ \App\Helpers\Helper::getGender($user->gender) }}" readonly autocomplete="off">
            @else
                <div>
                    <select class="form-control form-select custom-font-small" id="gender" name="gender" >
                        <option value="">-- Select Gender --</option>
                        <option value="M" {{ old('gender', $user->gender ?? null) == 'M' ? 'selected' : '' }}>@lang('translation.male')</option>
                        <option value="F" {{ old('gender', $user->gender ?? null) == 'F' ? 'selected' : '' }}>@lang('translation.female')</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
    <br/>

    <div class="row">
        <!-- Email -->
        <div class="col-md-6 col-xl-6">
            <label for="email">@lang('translation.email'):</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('email'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('email') }}</span>
            @if($type == 'add')
                <input type="email" maxlength="255" class="form-control form-control-sm custom-font-small" name="email" id="email" value="{{ old('email') }}" autocomplete="off">
            @else
                <input type="email" maxlength="255" class="form-control form-control-sm custom-font-small" name="email" id="email" value="{{ $user->email }}" {{ $type == 'view' ? 'readonly' : '' }} autocomplete="off">
            @endif
        </div>
        <!-- Mobile -->
        <div class="col-md-6 col-xl-6">
            <label for="mobile">@lang('translation.mobile'):</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('mobile'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('mobile') }}</span>
            @if($type == 'add')
                <input type="tel" maxlength="15" onkeypress="return onlyNumberKey(event)" class="form-control form-control-sm custom-font-small" name="mobile" id="mobile" value="{{ old('mobile') }}" autocomplete="off">
            @else
                <input type="tel" maxlength="15" onkeypress="return onlyNumberKey(event)" class="form-control form-control-sm custom-font-small" name="mobile" id="mobile" value="{{ $user->mobile }}" {{ $type == 'view' ? 'readonly' : '' }} autocomplete="off">
            @endif
        </div>
    </div>
    <br/>

    <div class="row">
        <!-- Status -->
        <div class="col-md-6 col-xl-6">
            <label for="status">@lang('translation.status'):</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('statuss'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('statuss') }}</span>
            @if($type == 'view')
                <input type="text" class="form-control custom-font-small"  id="statuss2" name="statuss2"  value="{{ $user->status == '1' ? trans('translation.active') : trans('translation.inactive') }}" readonly autocomplete="off">
            @else
                <div>
                    <select class="form-control form-select custom-font-small" id="statuss" name="statuss">
                        <option value="">-- @lang('translation.select_status') --</option>
                        <option value="1" {{ old('statuss', $user->status ?? null) == '1' ? 'selected' : '' }}>@lang('translation.active')</option>
                        <option value="2" {{ old('statuss', $user->status ?? null) == '2' ? 'selected' : '' }}>@lang('translation.inactive')</option>
                    </select>
                </div>
            @endif
        </div>

        <!-- Enable Notification -->
        <div class="col-md-6 col-xl-6">
            <label for="enable_notification">Enable Notification:</label><label style="padding: 0 5px 0 5px; color:red">*</label><span class="text-danger">@if (!empty($errors->first('enable_notification'))) <i class="fas fa-exclamation-triangle"></i> @endif {{ $errors->first('enable_notification') }}</span>
            @if($type == 'view')
                <input type="text" class="form-control custom-font-small" id="enable_notification2" name="enable_notification2" value="{{ $user->enable_notification == '1' ? 'Yes' : 'No' }}" readonly autocomplete="off">
            @else
                <div>
                    <select class="form-control form-select custom-font-small" id="enable_notification" name="enable_notification">
                        <option value="">-- @lang('translation.select_status') --</option>
                        <option value="1" {{ old('enable_notification', $user->enable_notification ?? null) == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ (string) old('enable_notification', $user->enable_notification ?? '') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
    <br/>
</div>

@section('script')
<script type="text/javascript">
    $("#statuss").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });
    $("#gender").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });
    $("#type").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });
    $("#team").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });
    $("#enable_notification").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });

    setTimeout(() => {
        const box1 = document.getElementById('error_box');
        const box2 = document.getElementById('success_box');
        if (box1) {
            box1.style.display = 'none';
        }
        if (box2) {
            box2.style.display = 'none';
        }
    }, 3000);

    $('form').submit(function(){
        $(this).find(':submit').attr( 'disabled','disabled' );
        //the rest of your code
        setTimeout(() => {
            $(this).find(':submit').attr( 'disabled',false );
        }, 3000)
    });

    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }
</script>
@endsection
