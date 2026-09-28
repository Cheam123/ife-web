@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') IFE Report @endsection

@section('content')
    <div class="card">
        
        <div class="form-body">
            @include('page.ifereport.form.ife-form')                        
            <div class="form-actions">
                <div class="custom_float">
                    <a href="{{route('ifereport.index',$param)}}" class="btn btn-primary waves-effect waves-light custom-button-shadow" style="width:100px">@lang('translation.back')</a>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
@endsection
