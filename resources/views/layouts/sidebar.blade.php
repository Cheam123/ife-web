<div class="vertical-menu">
    <!-- BUTTON EXPEND/HIDE SIDE MENU -->
    <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
    </button>

    <!-- SIDE MENU LIST -->
    <div data-simplebar class="sidebar-menu-scroll ">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <!-- FIRST SECTION -->
                <li class="menu-title">@lang('translation.Menu')</li>

                <!-- Dashboard -->
                @if(Auth::guard('web')->user()->can('dashboard'))
                <li>
                    <a href="{{url('index')}}">
                        <i class="uil-home-alt"></i>
                        <span class="custom-font-small">@lang('translation.Dashboard')</span>
                    </a>
                </li>
                @endif

                <!--  Customer -->
                @if(Auth::guard('web')->user()->can('view_lead') || Auth::guard('web')->user()->can('edit_lead') || Auth::guard('web')->user()->can('create_lead'))
                <li class="{{ \App\Helpers\Helper::setActive('v1/leads/index*','mm-active') }}
                           {{ \App\Helpers\Helper::setActive('v1/leads/edit*','mm-active') }}
                           {{ \App\Helpers\Helper::setActive('v1/leads/view*','mm-active') }}">
                    <a href="{{ route('lead.index') }}" class="{{ \App\Helpers\Helper::setActive('v1/leads/index*','mm-active') }}
                                                                    {{ \App\Helpers\Helper::setActive('v1/leads/edit*','mm-active') }}
                                                                    {{ \App\Helpers\Helper::setActive('v1/leads/view*','mm-active') }}">
                        <i class="mdi mdi-account-group-outline"></i>
                        <span class="custom-font-small">Outlets</span>
                    </a>
                </li>
                @endif

                <!--  Task -->
                @if(Auth::guard('web')->user()->can('manage_task'))
                <li class="{{ \App\Helpers\Helper::setActive('v1/task/manage*','mm-active') }}
                           {{ \App\Helpers\Helper::setActive('v1/task/manage/index*','mm-active') }}
                           {{ \App\Helpers\Helper::setActive('v1/task/manage/create*','mm-active') }} 
                           {{ \App\Helpers\Helper::setActive('v1/task/manage/edit*','mm-active') }}
                           {{ \App\Helpers\Helper::setActive('v1/task/manage/view*','mm-active') }}">
                    <a href="{{ route('tasks.index',['status'=>'1']) }}" class="{{ \App\Helpers\Helper::setActive('v1/task/manage*','mm-active') }}
                                                                                {{ \App\Helpers\Helper::setActive('v1/task/manage/index*','mm-active') }}
                                                                                {{ \App\Helpers\Helper::setActive('v1/task/manage/create*','mm-active') }} 
                                                                                {{ \App\Helpers\Helper::setActive('v1/task/manage/edit*','mm-active') }}
                                                                                {{ \App\Helpers\Helper::setActive('v1/task/manage/view*','mm-active') }}">
                        <i class="mdi mdi-book-outline"></i>
                        <span class="custom-font-small">Task</span>
                    </a>
                </li>
                @endif

                <!-- Visits (IFE reports) -->
                @if(Auth::guard('web')->user()->can('ife_report'))
                <li class="{{ \App\Helpers\Helper::setActive('v1/ifereport*','mm-active') }}">
                    <a href="{{ route('ifereport.index') }}" class="{{ \App\Helpers\Helper::setActive('v1/ifereport*','mm-active') }}">
                        <i class="uil-clipboard-notes"></i>
                        <span class="custom-font-small">Visits</span>
                    </a>
                </li>
                @endif

                <!-- Forms -->
                {{-- No permission gate on the menu itself: My Records and My Tasks are
                     scoped to the caller by the controller (records the user submitted or
                     took part in; stages awaiting the user), so there is nothing to hide.
                     Gating the menu instead made it appear when a task arrived and vanish
                     when it was done, taking the user's own history with it. --}}
                @php $formTaskCount = \App\Services\FormApprovalService::pendingCountFor(Auth::guard('web')->id()); @endphp
                <li class="{{ \App\Helpers\Helper::setActive('form/entry*','mm-active') }}
                           {{ \App\Helpers\Helper::setActive('form/records*','mm-active') }}">
                    <a href="javascript: void(0);" class="has-arrow {{ \App\Helpers\Helper::setActive('form/entry*','mm-active') }}
                                                                      {{ \App\Helpers\Helper::setActive('form/records*','mm-active') }}">
                        <i class="mdi mdi-file-document-edit-outline"></i>
                        <span class="custom-font-small">Forms</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        {{-- Same gate as the top menu's Form Creation item --}}
                        @if(Auth::guard('web')->user()->can('form_creation') || Auth::guard('web')->user()->can('form_admin'))
                        <li>
                            <a href="{{ route('form.index') }}" class="{{ \App\Helpers\Helper::setActive('form/index*','mm-active') }}">
                                <i class="fas fa-arrow-circle-right"></i>
                                <span class="custom-font-small">Form Creation</span>
                            </a>
                        </li>
                        @endif
                        {{-- No permission gate: the page lists only forms the user may
                             submit, decided per form by its own "Who can submit" setting. --}}
                        <li>
                            <a href="{{ route('form.entry') }}" class="{{ \App\Helpers\Helper::setActive('form/entry*','mm-active') }}">
                                <i class="fas fa-arrow-circle-right"></i>
                                <span class="custom-font-small">Available Forms</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('form.records.index') }}" class="{{ \App\Helpers\Helper::setActive('form/records','mm-active') }}">
                                <i class="fas fa-arrow-circle-right"></i>
                                <span class="custom-font-small">My Records</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('form.tasks') }}" class="{{ \App\Helpers\Helper::setActive('form/tasks*','mm-active') }}">
                                <i class="fas fa-arrow-circle-right"></i>
                                <span class="custom-font-small">
                                    My Tasks
                                    @if($formTaskCount > 0)
                                        <span class="badge bg-danger rounded-pill ms-1">{{ $formTaskCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        @if(Auth::guard('web')->user()->can('form_admin'))
                        <li>
                            <a href="{{ route('form.records.all') }}" class="{{ \App\Helpers\Helper::setActive('form/records/all*','mm-active') }}">
                                <i class="fas fa-arrow-circle-right"></i>
                                <span class="custom-font-small">All Records</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                <!-- Users -->
                @if(Auth::guard('web')->user()->can('manage_user'))
                <li class="{{ \App\Helpers\Helper::setActive('v1/users','mm-active') }}
                           {{ \App\Helpers\Helper::setActive('v1/users/view*','mm-active') }}
                           {{ \App\Helpers\Helper::setActive('v1/users/create*','mm-active') }}
                           {{ \App\Helpers\Helper::setActive('v1/users/edit*','mm-active') }}">
                    <a href="{{ route('users.index') }}" class="{{ \App\Helpers\Helper::setActive('v1/users','mm-active') }}
                                                               {{ \App\Helpers\Helper::setActive('v1/users/view*','mm-active') }}
                                                               {{ \App\Helpers\Helper::setActive('v1/users/create*','mm-active') }}
                                                               {{ \App\Helpers\Helper::setActive('v1/users/edit*','mm-active') }}">
                        <i class="uil-users-alt"></i>
                        <span class="custom-font-small">@lang('translation.Users')</span>
                    </a>
                </li>
                @endif

                <!-- Products -->
                @if(Auth::guard('web')->user()->can('manage_product'))
                <li class="{{ \App\Helpers\Helper::setActive('v1/product*','mm-active') }}">
                    <a href="{{ route('product.index') }}" class="{{ \App\Helpers\Helper::setActive('v1/product*','mm-active') }}">
                        <i class="mdi mdi-package-variant-closed"></i>
                        <span class="custom-font-small">Products</span>
                    </a>
                </li>
                @endif

                <!-- IFE Areas -->
                @if(Auth::guard('web')->user()->can('manage_area'))
                <li class="{{ \App\Helpers\Helper::setActive('v1/area*','mm-active') }}">
                    <a href="{{ route('area.index') }}" class="{{ \App\Helpers\Helper::setActive('v1/area*','mm-active') }}">
                        <i class="mdi mdi-map-marker-radius-outline"></i>
                        <span class="custom-font-small">IFE Areas</span>
                    </a>
                </li>
                @endif

                <!-- App errors (reported by the mobile app) -->
                @if(Auth::guard('web')->user()->isAdmin())
                <li class="{{ \App\Helpers\Helper::setActive('app-errors*','mm-active') }}">
                    <a href="{{ route('admin.app-errors') }}" class="{{ \App\Helpers\Helper::setActive('app-errors*','mm-active') }}">
                        <i class="mdi mdi-alert-circle-outline"></i>
                        <span class="custom-font-small">App errors</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>