<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Services\AdminDashboardService;

class UsersController extends Controller
{
    /** ?focus= filters from the admin Overview's attention list. */
    public const FOCUS = [
        'quiet'  => 'Field reps who have not signed in for ' . AdminDashboardService::QUIET_DAYS . ' days',
        'no_app' => 'People who cannot get app notifications (not signed in to the app on a phone, or notifications off)',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (!Auth::guard('web')->user()->can('manage_user')) {
            return $this->accessError();
        }

        $users = User::whereNotIn('id',[1]);

        if (!empty($request->get('active'))) {
            $users = $users->where('status', $request->get('active'));
        } else {
            $users = $users->where('status', 1);
            $request->merge(['active'=>1]);
        }
        if ($request->get('user_type') !== NULL) {
            $users = $users->where('type', $request->get('user_type'));
        }
        if (!empty($request->get('name'))) {
            $users = $users->where('name', 'like' ,'%'.$request->get('name').'%');
        }
        if (!empty($request->get('mobile'))) {
            $users = $users->where('mobile', 'like' ,'%'.$request->get('mobile').'%');
        }
        if (!empty($request->get('email'))) {
            $users = $users->where('email', 'like' ,'%'.$request->get('email').'%');
        }

        // People links on the admin Overview: quiet reps, and people the app cannot notify.
        $focusLabel = self::FOCUS[$request->get('focus')] ?? null;
        if ($request->get('focus') === 'quiet') {
            $users = $users->where('type', User::TYPE_USER)
                           ->where(fn ($q) => $q->whereNull('last_login_date')
                                                ->orWhere('last_login_date', '<', now()->subDays(AdminDashboardService::QUIET_DAYS)));
        } elseif ($request->get('focus') === 'no_app') {
            $users = $users->where('type', '!=', User::TYPE_ADMIN)
                           ->where(fn ($q) => $q->whereNull('fcm_token')->orWhere('fcm_token', ''));
        }

        $users       = $users->orderBy('name','asc')->get();
        $total       = $users->count();
        $tmenu_part1 = trans('translation.Users');
        $tmenu_part2 = trans('translation.Users');
        $tmenu_part3 = trans('translation.total').':'.$total;

        return view('page.users.index', compact('users','request','total','focusLabel','tmenu_part1','tmenu_part2','tmenu_part3'));
    }

    public function view($id)
    {
        if (!Auth::guard('web')->user()->can('manage_user')) {
            return $this->accessError();
        }

        $user        = User::findOrFail($id);
        $tmenu_part1 = trans('translation.Users');
        $tmenu_part2 = trans('translation.Users');
        $tmenu_part3 = trans('translation.view') . ' (' . trans('translation.id').':'.$user->id . ')';

        return view('page.users.view', compact('user','tmenu_part1','tmenu_part2','tmenu_part3'));
    }

    public function create()
    {
        if (!Auth::guard('web')->user()->can('manage_user')) {
            return $this->accessError();
        }

        $tmenu_part1 = trans('translation.Users');
        $tmenu_part2 = trans('translation.Users');
        $tmenu_part3 = trans('translation.create');

        return view('page.users.create', compact('tmenu_part1','tmenu_part2','tmenu_part3'));
    }

    public function store(UserRequest $request)
    {
        if (!Auth::guard('web')->user()->can('manage_user')) {
            return $this->accessError();
        }

        if ($this->isDuplicate($request)) {
            alert()->error('Oops...', trans('translation.duplicate_record'))->showConfirmButton()->focusConfirm(true);
            return redirect()->back()->with('error', trans('translation.duplicate_record'))->withInput();
        }

        DB::beginTransaction();
        try {
            $validatedData = $request->validated();

            $user = User::create([
                'name'                => $validatedData['name'],
                'telegram_chat_id'    => $validatedData['telegram_chat_id'] ?? NULL,
                'email'               => $validatedData['email'],
                'team'                => $validatedData['team'],
                'gender'              => $validatedData['gender'],
                'type'                => $validatedData['type'],
                'mobile'              => Helper::reformat_mobile($validatedData['mobile']),
                'status'              => $validatedData['statuss'],
                'enable_notification' => $request['enable_notification'] ?? 0,
            ]);

            // Staff ID, e.g. U00012. Login is by email; this is for display.
            $user->username = sprintf('U%05d', $user->id);
            $user->save();

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();
            alert()->error('Oops...', $e->getMessage())->showConfirmButton()->focusConfirm(true);
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        alert()->success(trans('translation.success'), trans('translation.successfully_create'))->iconHtml('<i class="far fa-thumbs-up"></i>')->showConfirmButton()->focusConfirm(true);
        return redirect()->route('users.index')->with('success', trans('translation.create_success'));
    }

    public function edit($id)
    {
        if (!Auth::guard('web')->user()->can('manage_user')) {
            return $this->accessError();
        }

        $user        = User::findOrFail($id);
        $tmenu_part1 = trans('translation.Users');
        $tmenu_part2 = trans('translation.Users');
        $tmenu_part3 = trans('translation.edit') . ' (' . trans('translation.id').':'.$user->id . ')';

        return view('page.users.edit', compact('user','tmenu_part1','tmenu_part2','tmenu_part3'));
    }

    public function update(UserRequest $request)
    {
        if (!Auth::guard('web')->user()->can('manage_user')) {
            return $this->accessError();
        }

        if ($this->isDuplicate($request, $request->id)) {
            alert()->error('Oops...', trans('translation.duplicate_record'))->showConfirmButton()->focusConfirm(true);
            return redirect()->back()->with('error', trans('translation.duplicate_record'))->withInput();
        }

        $validatedData = $request->validated();

        $user = User::findOrFail($request->id);
        $user->update([
            'name'                => $validatedData['name'],
            'telegram_chat_id'    => $validatedData['telegram_chat_id'] ?? NULL,
            'email'               => $validatedData['email'],
            'team'                => $validatedData['team'],
            'gender'              => $validatedData['gender'],
            'type'                => $validatedData['type'],
            'mobile'              => Helper::reformat_mobile($validatedData['mobile']),
            'status'              => $validatedData['statuss'],
            'enable_notification' => $request['enable_notification'] ?? 0,
        ]);

        alert()->success(trans('translation.success'), trans('translation.successfully_update'))->iconHtml('<i class="far fa-thumbs-up"></i>')->showConfirmButton()->focusConfirm(true);
        return redirect()->route('users.index')->with('success', trans('translation.update_success'));
    }

    public function delete(Request $request)
    {
        if (!Auth::guard('web')->user()->can('manage_user')) {
            return $this->accessError();
        }

        $user = User::findOrFail($request->id);

        // Free the unique email/mobile so they can be reused by a new account.
        $user->update([
            'email'  => $user->email.'@deleted'.$user->id,
            'mobile' => $user->mobile.'@deleted'.$user->id
        ]);
        $user->delete();

        alert()->success(trans('translation.success'), trans('translation.successfully_delete'))->iconHtml('<i class="far fa-thumbs-up"></i>')->showConfirmButton()->focusConfirm(true);
        return redirect()->route('users.index')->with('success', trans('translation.delete_success'));
    }

    public function profile()
    {
        $user = Auth::guard('web')->user();
        return view('page.users.profile', compact('user'));
    }

    public function change_password()
    {
        $user = Auth::guard('web')->user();
        return view('page.users.form.change-password-form', compact('user'));
    }

    public function reset_password(Request $request)
    {
        if (!Auth::guard('web')->user()->can('manage_user')) {
            return $this->accessError();
        }

        $data     = $request->all();
        $password = $data['pwd'];
        $hashed   = Hash::make($password);

        $usr = User::findOrFail($data['id']);
        $usr->password = $hashed;
        $usr->update();

        alert()->success(trans('translation.success'), trans('translation.successfully_update'))->iconHtml('<i class="far fa-thumbs-up"></i>')->showConfirmButton()->focusConfirm(true);
        return redirect()->back()->withInput();
    }

    public function update_password(ChangePasswordRequest $request)
    {
        $validatedData = $request->validated();

        $user = Auth::guard('web')->user();
        if (Hash::check($request->oldpass, $user->password)) {

            $password = $validatedData['pass1'];
            $hashed = Hash::make($password);

            $usr = User::findOrFail($user->id);
            $usr->password = $hashed;
            $usr->update();

            alert()->success(trans('translation.success'), trans('translation.successfully_update'))->iconHtml('<i class="far fa-thumbs-up"></i>')->showConfirmButton()->focusConfirm(true);
            return redirect()->route('users.profile');

        } else {
            alert()->error('Oops...', trans('translation.update_failed'))->showConfirmButton()->focusConfirm(true);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Another account already uses this email or mobile.
     */
    private function isDuplicate(Request $request, $ignoreId = null)
    {
        return User::where(function ($query) use ($request) {
                        $query->where('email', $request->email)
                              ->orWhere('mobile', $request->mobile);
                    })
                    ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                    ->exists();
    }

    private function accessError()
    {
        $response['title']      = trans('translation.access_error');
        $response['message'][0] = trans('translation.access_error_msg');
        $response['message'][1] = trans('translation.check_with_ur_superior');
        return view('errors.custom-error', compact('response'));
    }
}
