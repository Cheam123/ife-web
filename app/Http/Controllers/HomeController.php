<?php

namespace App\Http\Controllers;

use App\Models\DailyDigest;
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
     * Admins and Managers get the team dashboard (every task, the team
     * activity table, the AI digest); a Field Rep gets the same tiles and
     * chart for their own work.
     */
    private function dashboard(Request $request)
    {
        $user   = Auth::guard('web')->user();
        $isTeam = $user->seesAllRecords();

        $summary = app(DashboardService::class)->summary($isTeam ? null : $user);
        $digest  = $isTeam ? DailyDigest::latestDigest() : null;

        return view('page.dashboard', compact('request', 'summary', 'digest', 'isTeam'));
    }

    /** "Regenerate" on the digest card: rewrite today's digest now. */
    public function regenerateDigest(DailyDigestService $digests)
    {
        if (!Auth::guard('web')->user()->seesAllRecords()) {
            abort(403);
        }

        $digest = $digests->generate();

        $message = $digest->source === 'bedrock'
            ? 'Morning Round-Up rewritten by Claude.'
            : 'Morning Round-Up rewritten from the template' . ($digest->error ? ' (Bedrock error: ' . $digest->error . ')' : ' (Bedrock not configured)') . '.';

        return redirect()->to('/index')->with('digest_status', $message);
    }
}
