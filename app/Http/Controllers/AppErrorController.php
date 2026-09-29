<?php

namespace App\Http\Controllers;

use App\Models\Error as AppError;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Errors the mobile app reported (POST /api/mobile/error/log), for admins.
 * The admin Overview links here from "Needs your attention" and System
 * status. The raw request input is not shown: it can hold people's data.
 */
class AppErrorController extends Controller
{
    /** Window key => label; the key is the ?since= value. */
    public const WINDOWS = [
        '24h' => 'Last 24 hours',
        '7d'  => 'Last 7 days',
        '30d' => 'Last 30 days',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (!Auth::guard('web')->user()->isAdmin()) {
            abort(403);
        }

        $since = array_key_exists($request->query('since'), self::WINDOWS) ? $request->query('since') : '7d';
        $from  = match ($since) {
            '24h'   => Carbon::now()->subDay(),
            '30d'   => Carbon::now()->subDays(30),
            default => Carbon::now()->subDays(7),
        };

        // Not $errors: views already share that name for validation messages.
        $reports = AppError::where('created_at', '>=', $from)
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $screens = AppError::where('created_at', '>=', $from)
            ->selectRaw('page_name, count(*) as total')
            ->groupBy('page_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $windows = self::WINDOWS;

        return view('page.app-errors', compact('reports', 'screens', 'since', 'windows'));
    }
}
