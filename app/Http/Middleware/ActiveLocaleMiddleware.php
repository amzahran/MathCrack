<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Language;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ActiveLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // الحصول على اللغة الحالية من مكتبة mcamara
        $locale = LaravelLocalization::getCurrentLocale();

        // التحقق من وجود اللغة وأنها مفعلة
        $active = Language::where('code', $locale)->where('is_active', true)->exists();
        if (!$active) {
            // جلب اللغة الافتراضية من الإعدادات
            $defaultLanguage = \App\Models\Setting::where('option', 'default_language')->first()->value ?? 'en';
            // إذا كانت اللغة الافتراضية نفسها غير مفعلة، أظهر 404
            $defaultActive = Language::where('code', $defaultLanguage)->where('is_active', true)->exists();
            if ($defaultActive && $locale !== $defaultLanguage) {
                // إعادة التوجيه لنفس الرابط مع استبدال الكود في الرابط
                $segments = $request->segments();
                if (count($segments) > 0) {
                    $segments[0] = $defaultLanguage;
                    $newUrl = url(implode('/', $segments));
                    return redirect($newUrl);
                } else {
                    // إذا لم يوجد segments، فقط أعد التوجيه للرئيسية الافتراضية
                    return redirect(url($defaultLanguage));
                }
            }
            abort(404);
        }

        return $next($request);
    }
}