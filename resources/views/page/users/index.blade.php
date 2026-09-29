@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.Users') @endsection

@section('content')

    <div class="card card3 custom-font-small">
        <div class="card-body">

            <div class="ibox">
                <a class="collapse-link" style="cursor: pointer;">
                    <div>
                        @lang('translation.filter')&nbsp;&nbsp;<i class="fa fa-caret-down fa-lg"></i>
                        <div class="float-end">
                            <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary custom-button-shadow" style="width:100px">
                                @lang('translation.add')
                            </a>
                        </div>
                    </div>
                </a>
                <div class="ibox-content" style="padding-top:10px; display:none;">
                    <form class="form" id="filter" action="{{ route('users.index') }}" method="GET">
                        <input type="hidden" name="active" id="active" value="{{ old('active',$request->active) }}">
                        {{-- Keeps an attention filter (from the admin Overview) while searching --}}
                        @if($focusLabel)
                            <input type="hidden" name="focus" value="{{ $request->focus }}">
                        @endif
                        <div class="row">
                            <div class="col-md-3 col-xl-3" style="padding-top: 3px; padding-bottom: 3px">
                                <input type="text" class="form-control form-control-sm" name="name" placeholder="Name" value="{{ old('name',$request->name) }}">
                            </div>
                            <div class="col-md-3 col-xl-3" style="padding-top: 3px; padding-bottom: 3px">
                                <select class="form-control form-select-sm" name="user_type" id="user_type" style="width:100%;">
                                    <option value="">-- Select User Type --</option>
                                    @foreach(\App\Models\User::getUserTypeListing() as $key => $userType)
                                        <option value="{{ $key }}" {{ (string) old('user_type',$request->user_type) === (string) $key ? 'selected' : '' }}>{{ $userType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-xl-3" style="padding-top: 3px; padding-bottom: 3px">
                                <input type="text" class="form-control form-control-sm" onkeypress="return onlyNumberKey(event)" maxlength="15" name="mobile" placeholder="Mobile" value="{{ old('mobile',$request->mobile) }}">
                            </div>
                            <div class="col-md-3 col-xl-3" style="padding-top: 3px; padding-bottom: 3px">
                                <input type="email" class="form-control form-control-sm" maxlength="255" name="email" placeholder="Email" value="{{ old('email',$request->email) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div style="padding-top: 8px;">
                                <button type="reset" class="btn btn-sm custom-button-shadow" id="querystring" style="width:100px">@lang('translation.reset')</button>
                                <button type="submit" class="btn btn-sm btn-primary custom-button-shadow" id="filter" style="width:100px">@lang('translation.search')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <hr />

            @if($focusLabel)
                <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center gap-2 py-2" role="status">
                    <span>Showing: {{ $focusLabel }} ({{ number_format($total) }})</span>
                    <a href="{{ route('users.index', $request->except(['focus'])) }}" class="fw-semibold">Show everyone</a>
                </div>
            @endif

            <div class="breadcrumb-wrapper col-s-12">
                <div style="padding-top : 10px; padding-bottom : 5px; font-weight:600;">
                    <form class="form" id="shortlist" action="{{ route('users.index') }}" method="GET">
                        <select class="form-control form-select-sm" name="choose" id="choose">
                            <option value="">-- @lang('translation.select_status') --</option>
                            <option value="1" @if ($request->active == '1') selected @endif>@lang('translation.active')</option>
                            <option value="2" @if ($request->active == '2') selected @endif>@lang('translation.inactive')</option>
                        </select>
                    </form>
                </div>
            </div>

            <div style="overflow-x:auto; white-space: nowrap;">
                <table class="table table-sm custom-font-small" style="width:100%; border-collapse: collapse; box-shadow: 1px 1px 3px 1px rgb(183, 183, 183);">
                    <thead class="table-dark">
                        <tr>
                            <td scope="col">#</td>
                            <td scope="col" style="width:30%">Name</td>
                            <td scope="col">User Type</td>
                            <td scope="col">Team</td>
                            <td scope="col" style="text-align: center; width:100px"></td>
                            <td scope="col" style="text-align: center; width:100px"></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $s)
                            <tr>
                                <td style="border-bottom: 1px solid #acacac;" scope="row">
                                    {{ $index + 1 }}
                                </td>
                                <td style="border-bottom: 1px solid #acacac;">{{ $s->name }}</td>
                                <td style="border-bottom: 1px solid #acacac;">{{ \App\Models\User::getUserType($s->type) }}</td>
                                <td style="border-bottom: 1px solid #acacac;">{{ \App\Helpers\Helper::getTeam($s->team) }}</td>
                                <td style="border-bottom: 1px solid #acacac; text-align: center;">
                                    <a class="btn btn-sm btn-warning" onclick="resetPassword({{ $s->id }})">Reset Password</a>
                                </td>
                                <td style="border-bottom: 1px solid #acacac;">
                                    <div>
                                        <button type="button" onclick="deleteUser({{ $s->id }})" class="btn btn-sm btn-danger custom-button-shadow" style="margin-right: 15px;">@lang('translation.delete')</button>
                                        <a class="btn btn-sm btn-primary custom-button-shadow" href="{{ route('users.view', $s->id) }}">@lang('translation.view')</a>
                                        <a class="btn btn-sm btn-primary custom-button-shadow" href="{{ route('users.edit', $s->id) }}">@lang('translation.edit')</a>
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
    $("#user_type").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });
    $("#choose").select2({ dropdownCssClass: "small", containerCssClass: "small bg-white" });

    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }

    function resetPassword(id) {

        new swal({
            title: 'Reset Password !',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off',
                minlength: 6,
                maxlength: 12,
            },
            showCancelButton: true,
            confirmButtonText: 'Send',
            showLoaderOnConfirm: false,
        }).then((result) => {
            if (result.isConfirmed && result.value !== '') {
                $.ajax({
                    type: "post",
                    url: "{{ route('users.resetpassword') }}",
                    data: {"_token": "{{ csrf_token() }}", 'id': id, 'pwd': result.value},
                    cache: false,
                    success: function(response) {
                        new swal(
                            "Sccess!",
                            "Password has been reset!",
                            "success"
                        )
                    }
                });
            } else if (result.isConfirmed) {
                new swal(
                    'Oops...',
                    'Please enter new password to proceed!',
                    'error'
                )
            }
        })
    }

    // Collapse ibox function
    $('.collapse-link').click(function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.find('div.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-caret-up').toggleClass('fa-caret-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    $("#querystring").click(function(){
        window.location.href = window.location.href.split('?')[0];
    });

    $(document).ready(function() {
        $('#choose').on('change', function() {
            $("#active").val($("#choose").val());
            document.forms['filter'].submit();
        });
    });

    function deleteUser(id) {
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
                    url: "{{ route('users.delete') }}",
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
