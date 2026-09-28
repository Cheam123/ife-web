<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.title-meta')
    @include('layouts.head')
</head>

<style>
    @media (max-width: 500px) {
        body {
            margin: 0;
            font-family: "IBM Plex Sans", sans-serif;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 2.4!important;
            color: #495057;
            background-color: #f5f6f8;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
    }
    .datepicker { 
        z-index: 99999 !important;
    }
    .container, .container-fluid, .container-xxl, .container-xl, .container-lg, .container-md, .container-sm {
        width: 98%;
        padding-right: var(--bs-gutter-x, calc($grid-gutter-width / 2));
        padding-left: var(--bs-gutter-x, calc($grid-gutter-width / 2));
        margin-right: auto;
        margin-left: auto;
    }

    .table {
        --bs-table-hover-bg: #d5f5d5;
    }

    #loading {
        position: fixed;
        display: block;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        text-align: center;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
    }

    #loading-image {
        position: absolute;
        top: 35%;
        left: 43%;
        z-index: 100;
    }
    .select2-results {
        background-color: #ffffff;
        font-size: 13px;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #04c;
        color: #ffffff
    }
    .select2-container--default .select2-selection--single {
        display: block;
        width: 100%;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        box-shadow: 0 2px 4px rgba(15, 34, 58, 0.12) !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    /* datatable css */
    .pagination .page-item .page-link {
        font-size: 11px;
        height: 30px;
    }
    .page-item:first-child .page-link {
        font-size: 13px;
    }
    .page-item:last-child .page-link {
        font-size: 13px;
    }
    .dataTables_info {
        font-size: 13px;
    }
</style>

@section('body')

    <?php 
        use Jenssegers\Agent\Agent;
        $agent = new Agent();
    ?>
    <body data-topbar="light" data-layout-size="" data-layout="horizontal">
    
    @show

    <!-- Begin page -->
    <div id="layout-wrapper">
        <div class="d-lg-block d-xl-block">
            @include('layouts.horizontal',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')])
        </div>
        

        <div class="d-lg-none d-xl-none">
            @include('layouts.topbar',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')])
            @include('layouts.sidebar')
        </div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content" style="background-color: white !important;">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                    <section class="ldld light full">
                        <div style="margin: auto;">
                            <img src="{{ URL::asset('/assets/brand/ronda-spinner.svg') }}" alt="Loading..." width="96" height="96">
                        </div>
                    </section>
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <!-- @include('layouts.footer') -->
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    
    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
    @include('sweetalert::alert')

    <!-- Reusable bottom-right toast notifications: call showToast('msg', 'success') -->
    <x-toast />

</body>

</html>