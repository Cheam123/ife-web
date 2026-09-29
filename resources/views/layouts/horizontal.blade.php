<?php 
    use Jenssegers\Agent\Agent;
$agent = new Agent();
?>

<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box" style="margin-left:15px;">
                <a href="{{url('index')}}" class="logo logo-dark">
                    <span class="logo-sm"><img src="{{ URL::asset('/assets/brand/ronda-logo.svg') }}" alt="Ronda" height="26"></span>
                    <span class="logo-lg"><img src="{{ URL::asset('/assets/brand/ronda-logo.svg') }}" alt="Ronda" height="26"></span>
                </a>
                <a href="{{url('index')}}" class="logo logo-light">
                    <span class="logo-sm"><img src="{{ URL::asset('/assets/brand/ronda-logo.svg') }}" alt="Ronda" height="26"></span>
                    <span class="logo-lg"><img src="{{ URL::asset('/assets/brand/ronda-logo.svg') }}" alt="Ronda" height="26"></span>
                </a>
            </div>

            <button type="button" class="btn btn-sm pt-4 px-3 font-size-16 d-lg-none header-item waves-effect waves-light" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>
            
            @if (!$agent->isMobile())
                <div class="d-flex float-start align-items-center justify-content-center" style="font-size:10pt; width:100%; padding-left:30px;">            
                    <div class="dropdown d-inline-block language-switch">
                        
                        @if($tmenu_part1 == 'create-task')
                            <a href="{{ url('index') }}">@lang('translation.Home')</a> 
                            <i class="fas fa-arrow-alt-circle-right"></i> New Task
                        @elseif($tmenu_part1 == 'manage-task')
                            <a href="{{ url('index') }}">@lang('translation.Home')</a> 
                            <i class="fas fa-arrow-alt-circle-right"></i> Manage Task
                        @elseif($tmenu_part1 == 'edit-task')
                            <a href="{{ url('index') }}">@lang('translation.Home')</a> 
                            <i class="fas fa-arrow-alt-circle-right"></i> Manage Task
                            <i class="fas fa-arrow-alt-circle-right"></i> Edit Task ( {{ $task->task_reference}} )
                        @elseif($tmenu_part1 == 'view-task')
                            <a href="{{ url('index') }}">@lang('translation.Home')</a> 
                            <i class="fas fa-arrow-alt-circle-right"></i> Manage Task
                            <i class="fas fa-arrow-alt-circle-right"></i> View Task ( {{ $task->task_reference}} )

                        @else
                            @if($tmenu_part1 != '')
                                <a href="{{ url('index') }}">@lang('translation.Home')</a> 
                            @endif

                            @if($tmenu_part2 != '') 
                                <i class="fas fa-arrow-alt-circle-right"></i> {{ $tmenu_part2 ?? '' }} 
                            @endif

                            @if($tmenu_part3 != '') 
                                <i class="fas fa-arrow-alt-circle-right"></i> {{ $tmenu_part3 }}
                            @endif
                        @endif

                    </div>
                </div>
            @endif
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block" style="width:100%">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @if (Auth::guard('web')->user()->gender == 'F')
                        <img class="rounded-circle header-profile-user" src="{{ URL::asset('/assets/images/users/cartoon-girl-profile.png') }}" height="22" alt="Header Avatar">
                    @else
                        <img class="rounded-circle header-profile-user" src="{{ URL::asset('/assets/images/users/cartoon-boy-profile.png') }}" height="22" alt="Header Avatar">
                    @endif
                    <span class="d-none d-xl-inline-block ms-1 fw-medium custom-font-xsmall" style="white-space: nowrap;">{{Str::ucfirst(Auth::user()->name)}}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('users.profile') }}"><i class="uil uil-user-circle align-middle text-muted me-1"></i> <span class="align-middle custom-font-small">@lang('translation.View_Profile')</span></a>
                    <a class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="cursor: pointer;"><i class="uil uil-sign-out-alt align-middle me-1 text-muted"></i> <span class="align-middle custom-font-small">@lang('translation.Sign_out')</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="topnav">
            <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
                <div class="collapse navbar-collapse" id="topnav-menu-content">
                    <ul class="navbar-nav">
                        <!-- Dashboard -->
                        @if(Auth::guard('web')->user()->can('dashboard'))
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Helpers\Helper::setActive('index', 'mm-active') }}" href="{{ url('index') }}">
                                <i class="uil-home-alt me-2"><span class="menu-font-style">@lang('translation.Dashboard')</span></i>
                            </a>
                        </li>
                        @endif

                        <!-- Outlets (leads) -->
                        @if(Auth::guard('web')->user()->can('view_lead') || Auth::guard('web')->user()->can('edit_lead') || Auth::guard('web')->user()->can('create_lead'))
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Helpers\Helper::setActive('v1/lead*', 'mm-active') }}" href="{{ route('lead.index') }}">
                                <i class="mdi mdi-account-group-outline"><span class="menu-font-style">Outlets</span></i>
                            </a>
                        </li>
                        @endif

                        <!-- Task -->
                        @if(Auth::guard('web')->user()->can('manage_task'))
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Helpers\Helper::setActive('v1/task/manage/index2', 'mm-active') }}
                                               {{ \App\Helpers\Helper::setActive('v1/task/manage/create*', 'mm-active') }}
                                               {{ \App\Helpers\Helper::setActive('v1/task/manage/edit/*', 'mm-active') }}
                                               {{ \App\Helpers\Helper::setActive('v1/task/manage/view/*', 'mm-active') }}"
                                                                        href="{{ route('tasks.index2', ['status' => '1']) }}">
                                <i class="uil-graph-bar me-2">
                                    <span class="menu-font-style">Task 
                                        <!-- <span style="cursor: pointer; padding-top: 3px; padding-bottom: 3px; padding-left: 3px; padding-right: 3px; border-radius: 25px; background:rgb(7, 111, 36); color: white; width: fit-content; border-width: thin; border-style: dashed; border-color: yellow;">
                                            {{ Auth::guard('web')->user()->total_active_task }}
                                        </span> -->
                                    </span>
                                </i>
                            </a>
                        </li>
                        @endif
                        
                        {{-- <!-- Task (New Design) -->
                        @if(Auth::guard('web')->user()->can('manage_task'))
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Helpers\Helper::setActive('v1/task/manage/index2', 'mm-active') }}"
                                                                        href="{{ route('tasks.index2', ['status' => '1']) }}">
                                <i class="uil-graph-bar me-2">
                                    <span class="menu-font-style">Task (New)</span>
                                </i>
                            </a>
                        </li>
                        @endif --}}

                        <!-- Visits (IFE reports) -->
                        @if(Auth::guard('web')->user()->can('ife_report'))
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Helpers\Helper::setActive('v1/ifereport*', 'mm-active') }}"
                                                                        href="{{ route('ifereport.index') }}">
                                <i class="uil-clipboard-notes me-2">
                                    <span class="menu-font-style">Visits</span>
                                </i>
                            </a>
                        </li>
                        @endif

                        <!-- Form -->
                        {{-- Everyone gets the menu: My Records and My Tasks are scoped to
                             the caller by the controller, so there is nothing to hide. The
                             items inside carry their own gates. --}}
                        @php $formTaskCount = \App\Services\FormApprovalService::pendingCountFor(Auth::guard('web')->id()); @endphp
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none {{ \App\Helpers\Helper::setActive('v1/form*', 'mm-active') }}" 
                                                            href="#" id="topnav-form" role="button">
                                <i class="uil-clipboard-notes me-2"><span class="menu-font-style">Form</span><div class="arrow-down"></div></i>
                            </a>

                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                @if(Auth::guard('web')->user()->can('form_creation') || Auth::guard('web')->user()->can('form_admin'))
                                <a href="{{ route('form.index') }}" class="dropdown-item {{ \App\Helpers\Helper::setActive('v1/form/index*', 'mm-active') }}
                                                                                          {{ \App\Helpers\Helper::setActive('v1/form/create*', 'mm-active') }}
                                                                                          {{ \App\Helpers\Helper::setActive('v1/form/edit*', 'mm-active') }}">Form Creation</a>
                                @endif
                                {{-- No permission gate: the page lists only forms the user
                                     may submit, decided per form by its own "Who can submit"
                                     setting. A second global gate would just shadow it. --}}
                                <a href="{{ route('form.entry') }}" class="dropdown-item {{ \App\Helpers\Helper::setActive('v1/form/entry*', 'mm-active') }}">Available Forms</a>
                                <a href="{{ route('form.records.index') }}" class="dropdown-item {{ \App\Helpers\Helper::setActive('v1/form/records', 'mm-active') }}">My Records</a>
                                <a href="{{ route('form.tasks') }}" class="dropdown-item {{ \App\Helpers\Helper::setActive('v1/form/tasks*', 'mm-active') }}">
                                    My Tasks
                                    @if($formTaskCount > 0)
                                        <span class="badge bg-danger rounded-pill ms-1">{{ $formTaskCount }}</span>
                                    @endif
                                </a>
                                @if(Auth::guard('web')->user()->can('form_admin'))
                                <a href="{{ route('form.records.all') }}" class="dropdown-item {{ \App\Helpers\Helper::setActive('v1/form/records/all*', 'mm-active') }}">All Records</a>
                                @endif
                            </div>
                        </li>

                        <!-- Admin: users, product catalogue, IFE areas -->
                        @if(Auth::guard('web')->user()->can('manage_user') || Auth::guard('web')->user()->can('manage_product') || Auth::guard('web')->user()->can('manage_area'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none {{ \App\Helpers\Helper::setActive('v1/users*', 'mm-active') }}
                                                                          {{ \App\Helpers\Helper::setActive('v1/product*', 'mm-active') }}
                                                                          {{ \App\Helpers\Helper::setActive('v1/area*', 'mm-active') }}"
                                                            href="#" id="topnav-admin" role="button">
                                <i class="uil-users-alt me-2"><span class="menu-font-style">Admin</span><div class="arrow-down"></div></i>
                            </a>

                            <div class="dropdown-menu" aria-labelledby="topnav-admin">
                                @if(Auth::guard('web')->user()->can('manage_user'))
                                <a href="{{ route('users.index') }}" class="dropdown-item {{ \App\Helpers\Helper::setActive('v1/users*', 'mm-active') }}">@lang('translation.Users')</a>
                                @endif
                                @if(Auth::guard('web')->user()->can('manage_product'))
                                <a href="{{ route('product.index') }}" class="dropdown-item {{ \App\Helpers\Helper::setActive('v1/product*', 'mm-active') }}">Products</a>
                                @endif
                                @if(Auth::guard('web')->user()->can('manage_area'))
                                <a href="{{ route('area.index') }}" class="dropdown-item {{ \App\Helpers\Helper::setActive('v1/area*', 'mm-active') }}">IFE Areas</a>
                                @endif
                                @if(Auth::guard('web')->user()->isAdmin())
                                <a href="{{ route('admin.app-errors') }}" class="dropdown-item {{ \App\Helpers\Helper::setActive('app-errors*', 'mm-active') }}">App errors</a>
                                @endif
                            </div>
                        </li>
                        @endif
                        
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</header>

<style>
.menu-font-style {
    display:inline-block; 
    text-indent: 5px; 
    font-style: normal; 
    font-size:13px;
}
.topnav .navbar-nav .nav-link {
    font-size: 13px;
    position: relative;
    padding: 1rem 1.3rem;
    /* color: white; */
}
.topnav .dropdown .dropdown-menu {
    border-radius: 0 0 0.25rem 0.25rem;
    margin-top: 0;
    background-color: rgba(#fbf2e9,0.9);
    box-shadow: 0 2px 4px rgba(15,34,58,0.7);
}
.topnav .navbar-nav .dropdown-item {
    color: #000000;
}
.mm-active, .mm-active {
    color: #f78b17!important;
}
.mm-active, .mm-active>a {
    color: #f78b17!important;
}
.mm-active .active i, .mm-active>i {
    color: #f78b17!important;
}
</style>