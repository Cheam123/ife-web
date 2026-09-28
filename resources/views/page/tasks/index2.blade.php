@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.manage_cases') @endsection

@section('content')
    <div class="card card3 custom-font-small">

        <div class="card-body">
            <div>

                @if($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{$message}}</p>
                    </div>
                @endif

                @if($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <p>{{$message}}</p>
                    </div>
                @endif

                <?php
                    if ( empty($request->input('status')) )
                    {
                        $request->status = '1';
                    }
                ?>

                <div class="form-body">
                    @include('page.tasks.form.form-index2')
                </div>

            </div>
        </div>

    </div>
@endsection

@section('script')

    <script>

        $(function(){
            $('#btnExpendRow').click(function() {
                var cookieValue = document.cookie.replace(/(?:(?:^|.*;\s*)expendRowTwo\s*\=\s*([^;]*).*$)|^.*$/, "$1");
                if (cookieValue == 0) {
                    document.cookie = "expendRowTwo=1; max-age=" + 5*60;
                } else {
                    document.cookie = "expendRowTwo=0; max-age=" + 5*60;
                }
            });
        });

        document.body.addEventListener("click", function(e) {
            if(e.target && e.target.nodeName) {
                var cookieValue = document.cookie.replace(/(?:(?:^|.*;\s*)expendRowTwo\s*\=\s*([^;]*).*$)|^.*$/, "$1");
                if (cookieValue == 1) {
                    document.cookie = "expendRowTwo=1; max-age=" + 5*60;
                }
            }
        });

    </script>

@endsection
