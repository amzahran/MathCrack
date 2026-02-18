<?php

namespace App\Http\Controllers\Web\Back;

use App\Http\Controllers\Controller;
use App\Services\PlatformStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request, PlatformStatsService $statsService)
    {
        if (Auth::user()->roles()->exists()) {
            $stats = $statsService->dashboard();
            return view('themes.default.back.index', compact('stats'));
        }

        return redirect()->route('dashboard.users.courses');
    }
}
