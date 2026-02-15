<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnhancedVisitorTracker
{
    public function handle(Request $request, Closure $next): Response
    {
        $accept = app('cached_data')['settings']['allow_cookies'] ?? null;

        if ($accept == 1) {
            // تسجيل الزيارة
            visitor()->visit();
        }

        return $next($request);
    }
}
