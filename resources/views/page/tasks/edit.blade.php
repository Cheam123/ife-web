@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.edit_case') @endsection

@section('content')
    <div class="card">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-body">

            <form action="" method="post" id="view_case_form" name="view_case_form" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="custom-task dropdown">
                    <a href="{{ route('tasks.index2',$request->all()) }}" style="width:100px;" class="btn btn-dark custom-button-shadow">@lang('translation.back')</a>
                    
                    <input type="hidden" name="task_id" id="task_id"  value="{{ $task->id }}">
                    <input type="hidden" value="" id="special_remark" name="special_remark">

                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="page" id="page" value="{{ $request->page }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="status" id="status" value="{{ $request->status }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="sortby" id="sortby" value="{{ $request->sortby }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_title" id="filter_title" value="{{ $request->filter_title }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_alert" id="filter_alert" value="{{ $request->filter_alert }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_reference_no" id="filter_reference_no" value="{{ $request->filter_reference_no }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_subscriber" id="filter_subscriber" value="{{ $request->filter_subscriber }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_subsubscriber" id="filter_subsubscriber" value="{{ $request->filter_subsubscriber }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_source" id="filter_source" value="{{ $request->filter_source }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_creator" id="filter_creator" value="{{ $request->filter_creator }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_checker" id="filter_checker" value="{{ $request->filter_checker }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_owner" id="filter_owner" value="{{ $request->filter_owner }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_viewer" id="filter_viewer" value="{{ $request->filter_viewer }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="filter_business_category" id="filter_business_category" value="{{ $request->filter_business_category }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="start" id="start" value="{{ $request->start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="end" id="end" value="{{ $request->end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="complete_date_start" id="filter_sucomplete_date_startbscriber" value="{{ $request->complete_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="complete_date_end" id="complete_date_end" value="{{ $request->complete_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="inprogress_date_start" id="inprogress_date_start" value="{{ $request->inprogress_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="inprogress_date_end" id="inprogress_date_end" value="{{ $request->inprogress_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="done_date_start" id="done_date_start" value="{{ $request->done_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="done_date_end" id="done_date_end" value="{{ $request->done_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="verify_date_start" id="verify_date_start" value="{{ $request->verify_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="verify_date_end" id="verify_date_end" value="{{ $request->verify_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="reject_date_start" id="reject_date_start" value="{{ $request->reject_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="reject_date_end" id="reject_date_end" value="{{ $request->reject_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="kiv_date_start" id="kiv_date_start" value="{{ $request->kiv_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="kiv_date_end" id="kiv_date_end" value="{{ $request->kiv_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="onhold_date_start" id="kiv_date_start" value="{{ $request->onhold_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="onhold_date_end" id="kiv_date_end" value="{{ $request->onhold_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="updated_date_start" id="updated_date_start" value="{{ $request->updated_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="updated_date_end" id="updated_date_end" value="{{ $request->updated_date_end }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="appointment_date_start" id="appointment_date_start" value="{{ $request->appointment_date_start }}">
                    <input type="hidden" class="form-control form-control-sm custom-font-xsmall" name="appointment_date_end" id="appointment_date_end" value="{{ $request->appointment_date_end }}">

                    @if(
                        ($task->status == 1 && $task->users->whereIn('role',[2,6])->where('user_id',Auth::guard('web')->user()->id)->count() > 0) ||
                        ($task->status == 2 && $task->users->whereIn('role',[2,6])->where('user_id',Auth::guard('web')->user()->id)->count() > 0) ||
                        ($task->status == 3 && $task->users->whereIn('role',[3,4])->where('user_id',Auth::guard('web')->user()->id)->count() > 0) ||
                        ($task->status == 4 && $task->users->where('role',4)->where('user_id',Auth::guard('web')->user()->id)->count() > 0) ||
                        ($task->status == 2 && $task->users->whereIn('role',[4])->where('user_id',Auth::guard('web')->user()->id)->count() > 0)
                    )
                    <button type="button" class="btn btn-light dropdown-toggle waves-effect waves-light custom-button-shadow" style="width: 40px" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    @endif

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="background-color: rgb(237, 235, 188); max-width: 100px; min-width: 200px; box-shadow: 0px 2px 2px 1px rgb(183, 183, 183);"> 
                        <!-- MARKED AS VERIFIED OR FALLBACK -->
                        @if($task->status == 3 && $task->users->whereIn('role',[3,4])->where('user_id',Auth::guard('web')->user()->id)->count() > 0)
                            <button class="dropdown-item" formaction="{{ route('tasks.verified') }}" type="submit" id="submit_verify_main" style="display:none;">Verified</button>
                            <button class="dropdown-item" type="button" id="submit_verify">Verified</button>

                            <button class="dropdown-item" formaction="{{ route('tasks.fallback') }}" type="submit" id="submit_fallback_main" style="display:none;">Fallback</button>
                            <button class="dropdown-item" type="button" id="submit_fallback">Fallback</button>
                        @endif

                        <!-- MARKED AS COMPLETE -->
                        @if($task->status == 4 && $task->users->where('role',4)->where('user_id',Auth::guard('web')->user()->id)->count() > 0)
                            <button class="dropdown-item" formaction="{{ route('tasks.completed') }}" type="submit" id="submit_complete_main" style="display:none;">Complete</button>
                            <button class="dropdown-item" type="button" id="submit_complete">Complete</button>
                        @endif

                        <!-- MARKED AS IN PROGRESS, KIV OR REJECTED (MOVED FROM ON HOLD) -->
                        @if($task->status == 2 && $task->users->whereIn('role',[4])->where('user_id',Auth::guard('web')->user()->id)->count() > 0)
                            <button class="dropdown-item" formaction="{{ route('tasks.fallback') }}" type="submit" id="submit_fallback_main" style="display:none;">Fallback</button>
                            <button class="dropdown-item" type="button" id="submit_fallback">Fallback</button>

                            <button class="dropdown-item" formaction="{{ route('tasks.rejected') }}" type="submit" id="submit_reject_main" style="display:none;">Reject</button>
                            <button class="dropdown-item" type="button" id="submit_reject">Reject</button>

                            <button class="dropdown-item" formaction="{{ route('tasks.kiv') }}" type="submit" id="submit_kiv_main" style="display:none;">KIV</button>
                            <button class="dropdown-item" type="button" id="submit_kiv">Keep In View</button>

                            <button class="dropdown-item" formaction="{{ route('tasks.done') }}" type="submit" id="submit_done_main" style="display:none;">Done</button>
                            <button class="dropdown-item" type="button" id="submit_done">Done</button>
                        @endif

                        {{-- <!-- MARKED AS ONHOLD, LET MANAGER SET AS KIV OR REJECTED -->
                        @if(($task->status == 1 || $task->status == 2) && $task->users->whereIn('role',[2,3,4,6])->where('user_id',Auth::guard('web')->user()->id)->count() > 0)
                            <button class="dropdown-item" formaction="{{ route('tasks.onhold') }}" type="submit" id="submit_onhold_main" style="display:none;">On Hold</button>
                            <button class="dropdown-item" type="button" id="submit_onhold">On Hold</button>
                        @endif --}}

                    </div>
                </div>
            </form>
            @include('page.tasks.form.form-edit', ['type' => 'edit'])
        </div>
        
    </div>
@endsection

@section('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\TaskSaveRequest') !!}
@endsection
