<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box" style="margin-left:15px;">
                <a href="{{url('index')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('/assets/images/logo-eciatto.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('/assets/images/logo-eciatto.png') }}" alt="" height="20">
                    </span>
                </a>
                <a href="{{url('index')}}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('/assets/images/logo-eciatto.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('/assets/images/logo-eciatto.png') }}" alt="" height="20">
                    </span>
                </a>
            </div>

            <button type="button" class="pt-4 btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <?php 
            use Jenssegers\Agent\Agent;
            $agent = new Agent();
        ?>
        
        @if (!$agent->isMobile())
            <div class="d-flex float-start align-items-center" style="width:100%; padding-left:30px;">            
                <div class="dropdown d-inline-block language-switch custom-font-xsmall">
                    @if($tmenu_part1 == 'create-task')
                        <a href="{{ url('index') }}">@lang('translation.Home')</a> 
                        <i class="fas fa-arrow-alt-circle-right"></i> New Task 
                    @elseif($tmenu_part1 == 'manage-task')
                        <a href="{{ url('index') }}">@lang('translation.Home')</a> 
                        <i class="fas fa-arrow-alt-circle-right"></i> Manage Task
                    @elseif($tmenu_part1 == 'edit-task')
                        <a href="{{ url('index') }}">@lang('translation.Home')</a> 
                        <i class="fas fa-arrow-alt-circle-right"></i>Manage Task
                        <i class="fas fa-arrow-alt-circle-right"></i> Edit ( {{ $task->task_reference}} )
                    @elseif($tmenu_part1 == 'view-task')
                        <a href="{{ url('index') }}">@lang('translation.Home')</a> 
                        <i class="fas fa-arrow-alt-circle-right"></i> Manage Task
                        <i class="fas fa-arrow-alt-circle-right"></i>View ( {{ $task->task_reference}} )
                    @else
                        @if( $tmenu_part1 != '' )
                            <a href="{{ url('index') }}">@lang('translation.Home')</a> 
                        @endif

                        @if( $tmenu_part2 != '' ) 
                            <i class="fas fa-arrow-alt-circle-right"></i> {{ $tmenu_part2 ?? '' }} 
                        @endif

                        @if( $tmenu_part3 != '' ) 
                            <i class="fas fa-arrow-alt-circle-right"></i> {{ $tmenu_part3 }}
                        @endif
                    @endif
                </div>
            </div>
        @endif

        <div class="d-flex">
            <div class="dropdown d-inline-block" style="width:100%">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ Auth::guard('web')->user()->gender == 'F' ? URL::asset('/assets/images/users/cartoon-girl-profile.png') : URL::asset('/assets/images/users/cartoon-boy-profile.png') }}" alt="Header Avatar">
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
</header>