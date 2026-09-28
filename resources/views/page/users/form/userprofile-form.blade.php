<div class="row">

    <div class="card-body">
        <div class="text-center">
            <div class="clearfix"></div>
            <div>
                @if (Auth::guard('web')->user()->gender == 'F')
                <img src="{{ URL::asset('/assets/images/users/cartoon-girl-profile.png') }}" alt="" class="avatar-lg rounded-circle img-thumbnail">
                @else
                <img src="{{ URL::asset('/assets/images/users/cartoon-boy-profile.png') }}" alt="" class="avatar-lg rounded-circle img-thumbnail">
                @endif
            </div>
            <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
            <p class="text-muted">{{ $user->getUserType($user->type) }}</p>
            <!-- <a href="{{ route('users.changepassword') }}" class="btn btn-light btn-sm"><i class="fas fa-cog"></i> @lang('translation.change_password')</a> -->
        </div>

        <hr class="my-4">

        <div class="col-md-6 col-xl-6" style="margin-left: auto;margin-right: auto;">
            <div class="mt-4" style="text-align:center;">
                <div class="mt-4">
                    <p style="background-color: coral;
                    padding: 4px 10px 4px 10px;
                    color:white;
                    border-radius: 6px;
                    box-shadow: 3px 3px 10px 3px rgb(183, 183, 183);">@lang('translation.name') :</p>
                    <h5 class="font-size-16">{{ isset($user->name) ? $user->name : '-' }}</h5>
                </div>
                <div class="mt-4">
                    <p style="background-color: coral;
                    padding: 4px 10px 4px 10px;
                    color:white;
                    border-radius: 6px;
                    box-shadow: 3px 3px 10px 3px rgb(183, 183, 183);">@lang('translation.mobile') :</p>
                    <h5 class="font-size-16">{{ isset($user->mobile) ? $user->mobile : '-' }}</h5>
                </div>
                <div class="mt-4">
                    <p style="background-color: coral;
                    padding: 4px 10px 4px 10px;
                    color:white;
                    border-radius: 6px;
                    box-shadow: 3px 3px 10px 3px rgb(183, 183, 183);">@lang('translation.email') :</p>
                    <h5 class="font-size-16">{{ isset($user->email) ? $user->email : '-' }}</h5>
                </div>
                <div class="mt-4">
                    <p style="background-color: coral;
                    padding: 4px 10px 4px 10px;
                    color:white;
                    border-radius: 6px;
                    box-shadow: 3px 3px 10px 3px rgb(183, 183, 183);">Team :</p>
                    <h5 class="font-size-16">{{ \App\Helpers\Helper::getTeam($user->team) ?: '-' }}</h5>
                </div>
            </div>
        </div>
    </div>

</div>