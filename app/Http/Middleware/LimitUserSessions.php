<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class LimitUserSessions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $maxSessions = Setting::where('option', 'max_sessions')->first()->value;
            $sessionTimeout = Setting::where('option', 'session_timeout')->first()->value*60*60;

            // التحقق من آخر جلسة للمستخدم
            $lastSession = DB::table('sessions')
                ->where('user_id', $userId)
                ->orderBy('last_activity', 'desc')
                ->first();

            if ($lastSession && (time() - $lastSession->last_activity) > $sessionTimeout) {
                Auth::logout();
                return redirect('/login')->withErrors(['limit' => __('l.Your session has expired.')]);
            }

            // عد الجلسات النشطة للمستخدم (خلال الساعتين الأخيرتين)
            $activeSessions = DB::table('sessions')
                ->where('user_id', $userId)
                ->where('last_activity', '>', time() - $sessionTimeout)
                ->count();

            if ($activeSessions > $maxSessions) {
                Auth::logout();
                return redirect('/login')->withErrors(['limit' => __('l.You have exceeded the allowed number of devices.')]);
            }
        }

        return $next($request);
    }
}
