<?php

namespace App\Http\Controllers\Web\Back\Admins\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Shetabit\Visitor\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show statistics')) {
            return view('themes/default/back.permission-denied');
        }
    }
}