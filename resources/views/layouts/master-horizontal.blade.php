<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.title-meta')
    @include('layouts.head')
</head>

@section('body')

    <body data-topbar="light" data-layout-size="boxed" data-layout="horizontal">
    <div id="preloader"> <div id="status"> <div class="spinner"> <i class="uil-shutter-alt spin-icon"></i> </div> </div> </div>
    @show

    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.horizontal',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')])
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <!-- Start content -->
                <div class="container-fluid">
                    @yield('content')
                </div> <!-- content -->
            </div>
            @include('layouts.footer')
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->


    @include('layouts.vendor-scripts')
    @include('sweetalert::alert')
</body>


</html>
