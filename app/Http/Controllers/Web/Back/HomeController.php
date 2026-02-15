<?php

namespace App\Http\Controllers\Web\Back;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Course;
use App\Models\Test;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->roles()->exists()) {
            // Get real platform statistics
            $stats = $this->getPlatformStatistics();

            return view('themes.default.back.index', compact('stats'));
        } else {
            return redirect()->route('dashboard.users.courses');
        }
    }

    private function getPlatformStatistics()
    {
        return [
            'levels' => Level::count(),
            'courses' => Course::count(),
            'lectures' => Lecture::count(),
            'tests' => Test::count()
        ];
    }
}
