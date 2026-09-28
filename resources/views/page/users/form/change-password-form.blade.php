@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.user_profile') @endsection

@section('content')

    <div class="card card3 custom-font-small">

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger" id="error_box">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-body">

                <div class="row">
                    <form action="{{route('users.updatepassword')}}" method="post" id="edit_form" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="mb-3">
                            <div>
                                <p class="mb-1">@lang('translation.current_password') :</p>
                                <input type="password" minlength="6" maxlength="12" id="oldpass" name="oldpass" class="form-control custom-font-small" value="{{ old('oldpass') }}"  placeholder="Current Password">
                            </div>
                            <div class="mt-2">
                                <p class="mb-1">@lang('translation.new_password') :</p>
                                <input type="password" minlength="6" maxlength="12" id="pass1" name="pass1" class="form-control custom-font-small" value="{{ old('pass1') }}" placeholder="6-12 characters">
                            </div>
                            <div class="mt-2">
                                <p class="mb-1">@lang('translation.reenter_password') :</p>
                                <input type="password" minlength="6" maxlength="12" id="pass2" name="pass2" class="form-control custom-font-small" value="{{ old('pass2') }}" placeholder="6-12 characters">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary custom-button-shadow" style="width:100px">@lang('translation.save')</button>
                        <button type="reset" class="btn btn-secondary waves-effect">@lang('translation.reset')</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\ChangePasswordRequest') !!} 
@endsection