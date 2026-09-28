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
                @include('page.users.form.userprofile-form', ['type' => ''])
            </div>
        </div>
    </div>

@endsection