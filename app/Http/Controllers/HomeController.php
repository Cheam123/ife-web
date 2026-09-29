<?php

namespace App\Http\Controllers;

use App\Models\DailyDigest;
use App\Services\AdminDashboardService;
use App\Services\DailyDigestService;
use App\Services\DashboardService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return $this->dashboard($request);
    }

    public function root()
    {
        return $this->dashboard(new Request());
    }

    /**
     * Admins land on the Overview: setup, adoption and system status.
     * ?tab=team shows them the team dashboard, which is what Managers get
     * (every task, the team activity table, the AI digest). A Field Rep gets
     * the same tiles and chart for their own work.
     */
    private function dashboard(Request $request)
    {
        $user    = Auth::guard('web')->user();
        $isAdmin = $user->isAdmin();

        if ($isAdmin && $request->query('tab') !== 'team') {
            $overview = app(AdminDashboardService::class)->overview($user);

            return view('page.dashboard-admin', compact('request', 'overview', 'user'));
        }

        $isTeam  = $user->seesAllRecords();
        $summary = app(DashboardService::class)->summary($isTeam ? null : $user);
        $digest  = $isTeam ? DailyDigest::latestDigest() : null;

        return view('page.dashboard', compact('request', 'summary', 'digest', 'isTeam', 'isAdmin'));
    }

    /** "Regenerate" on the digest card: rewrite today's digest now. */
    public function regenerateDigest(DailyDigestService $digests)
    {
        $user = Auth::guard('web')->user();

        if (!$user->seesAllRecords()) {
            abort(403);
        }

        $digest = $digests->generate();

        $message = $digest->source === 'bedrock'
            ? 'Morning Round-Up rewritten by Claude.'
            : 'Morning Round-Up rewritten from the template' . ($digest->error ? ' (Bedrock error: ' . $digest->error . ')' : ' (Bedrock not configured)') . '.';

        // The Round-Up card lives on the team dashboard, which is a tab for admins.
        return redirect()->to($user->isAdmin() ? '/index?tab=team' : '/index')->with('digest_status', $message);
    }
}
