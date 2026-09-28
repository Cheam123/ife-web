@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.Users') @endsection

@section('content')
    <div class="card card3 custom-font-small" style="border-radius: 5px;">

        <div class="card-body">
            <div class="form-body">
                @include('page.users.form.users-form', ['type' => 'view'])
                <div class="form-actions">
                    <div class="custom_float">
                        <a href="{{ route('users.index') }}" class="btn btn-primary custom-button-shadow" style="width:100px">@lang('translation.back')</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
