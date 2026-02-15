<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the site is in maintenance mode
        if (config('app.maintenance.MAINTENANCE_MODE')) {
            // First check if the route is for login
            if ($request->is('login') || $request->is('logout')) {
                return $next($request);
            }

            // Then check if user is authenticated and has maintenance access
            if (Auth::check() && Gate::allows('access maintenance')) {
                return $next($request);
            }

            // Otherwise, show maintenance page
            return response()->view('errors.maintenance');
        }

        return $next($request);
    }
}