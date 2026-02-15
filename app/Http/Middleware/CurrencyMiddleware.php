<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Currency;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class CurrencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // فحص الكوكيز الحالية
        $currencyCode = $_COOKIE['currency'] ?? null;

        // تعيين العملة الافتراضية إذا لم تكن موجودة
        if ($currencyCode == null) {
            $defaultCode = Setting::where('option', 'default_currency')->first()->value;
            $defaultCurrency = Currency::where('code', $defaultCode)->firstOrFail();

            setcookie('currency', $defaultCurrency->code, [
                'expires' => time() + (86400 * 30),
                'path' => '/',
                'secure' => true,
                'samesite' => 'Lax'
            ]);

            setcookie('currency_symbol', $defaultCurrency->symbol, [
                'expires' => time() + (86400 * 30),
                'path' => '/',
                'secure' => true,
                'samesite' => 'Lax'
            ]);

            $currencyCode = $defaultCurrency->code;

            if (!$request->has('currency_set')) {
                return redirect($request->fullUrlWithQuery(['currency_set' => 'true']));
            }
        }

        // تغيير العملة عند الطلب
        if ($request->currency && $currencyCode !== $request->currency) {
            $requestedCurrency = Currency::where('code', $request->currency)->first();
            if ($requestedCurrency) {
                setcookie('currency', $requestedCurrency->code, [
                    'expires' => time() + (86400 * 30),
                    'path' => '/',
                    'secure' => true,
                    'samesite' => 'Lax'
                ]);

                setcookie('currency_symbol', $requestedCurrency->symbol, [
                    'expires' => time() + (86400 * 30),
                    'path' => '/',
                    'secure' => true,
                    'samesite' => 'Lax'
                ]);

                if (!$request->has('currency_set')) {
                    return redirect($request->fullUrlWithQuery(['currency_set' => 'true']));
                }
            }
        }

        return $next($request);
    }
}
