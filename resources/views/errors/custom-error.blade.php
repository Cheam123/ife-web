@extends('layouts.master')
@section('title') @lang('translation.error') @endsection

@section('content')

    <div class="card card3">
        <div class="card-body">
            <div class="blink_me" style="padding-bottom:15px">
                <h5><i class="fa fa-exclamation-triangle" style="color:red;"></i> {{ $response['title'] }}</h5>
            </div>
            @if (!empty($response['message']))
                @foreach ($response['message'] as $key => $value)

                    <p>{{ $value }}</p>

                @endforeach
            @endif
        </div>
    </div>

@endsection