@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.Users') @endsection

@section('content')
    <div class="card card3 custom-font-small" style="border-radius: 5px;">

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

            <form action="{{route('users.update')}}" method="post" id="edit_users_form" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-body">
                    @include('page.users.form.users-form', ['type' => 'edit'])
                    <input type="hidden" name="id" id="id" value="{{ $user->id }}">
                </div>

                <div class="form-actions">
                    <div class="custom_float">
                        <a href="{{ route('users.index') }}" class="btn btn-light custom-button-shadow" style="width:100px">@lang('translation.back')</a>
                        <button type="submit" class="btn btn-primary custom-button-shadow" style="width:100px">@lang('translation.save')</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\UserRequest') !!}
@endsection
