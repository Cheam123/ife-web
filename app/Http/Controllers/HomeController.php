<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tasks;
use App\Models\GeneralSetting;
use App\Helpers\Helper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        // $tz = '2025-08-27 00:00:00'; // UTC Time
        // $date = Carbon::createFromFormat('Y-m-d H:i:s', $tz, 'UTC');
        // $date->setTimezone('Asia/Kuala_Lumpur');
        // dd( Carbon::parse($date)->format('Y-m-d H:i:s'));
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('page.dashboard', compact('request'));
    }

    public function root()
    { 
        $request = new Request();
        return view('page.dashboard', compact('request'));
    }
}
