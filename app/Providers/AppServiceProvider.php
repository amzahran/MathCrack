<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use App\Models\Language;
use App\Models\Currency;

class AppServiceProvider extends ServiceProvider
{
    /**
     * تسجيل الخدمات في التطبيق
     */
    public function register(): void
    {
        if (!$this->app->runningInConsole()) {
            // تخزين جميع البيانات المطلوبة في الكاش مرة واحدة
            $this->registerCachedData();

            // تسجيل خدمة theme
            $this->app->singleton('theme', function () {
                $cachedData = $this->app->make('cached_data');
                return $cachedData['settings']['theme'] ?? 'default';
            });
        }
    }

    /**
     * تهيئة الخدمات في التطبيق
     */
    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            $this->shareGlobalVariables();
            $this->shareCurrencyData();
        }
    }

    /**
     * تخزين البيانات في الكاش
     */
    private function registerCachedData(): void
    {
        $this->app->singleton('cached_data', function () {
            $cacheKey = 'app_cached_data';
            $cacheTtl = config('cache.ttl.app_data', now()->addDay());

            return Cache::remember($cacheKey, $cacheTtl, function () {
                // تجميع كل الإعدادات المطلوبة في استعلام واحد
                $settings = $this->getSettings();

                // تجميع اللغات والعملات في استعلامين فقط مع eager loading
                $languages = Language::where('is_active', true)->get();
                $currencies = Currency::where('is_active', true)->get();

                // العثور على اللغة والعملة الافتراضية
                $defaultLanguage = $languages->where('code', $settings['default_language'] ?? '')->first();
                $defaultCurrency = $currencies->where('code', $settings['default_currency'] ?? '')->first();

                return [
                    'settings' => $settings,
                    'theme' => $settings['theme'] ?? 'default',
                    'languages' => $languages,
                    'default_language' => $defaultLanguage,
                    'currencies' => $currencies,
                    'default_currency' => $defaultCurrency,
                ];
            });
        });
    }

    /**
     * الحصول على الإعدادات
     *
     * @return array
     */
    private function getSettings(): array
    {
        return Setting::pluck('value', 'option')->toArray();
    }

    /**
     * مشاركة المتغيرات العامة في جميع صفحات التطبيق
     */
    private function shareGlobalVariables(): void
    {
        $cachedData = $this->app->make('cached_data');

        View::share([
            'settings' => $cachedData['settings'],
            'headerLanguages' => $cachedData['languages'],
            'headerCurrencies' => $cachedData['currencies'],
            'defaultLanguage' => $cachedData['default_language'],
            'defaultCurrency' => $cachedData['default_currency'],
        ]);
    }

    /**
     * مشاركة بيانات العملة في جميع الصفحات
     */
    private function shareCurrencyData(): void
    {
        View::composer('*', function ($view) {
            // استخدام الجلسة لتخزين العملة المحددة
            if (!session()->has('currency_data')) {
                session()->put('currency_data', [
                    'currency' => $_COOKIE['currency'] ?? null,
                    'symbol' => $_COOKIE['currency_symbol'] ?? null
                ]);
            }

            $currencyData = session()->get('currency_data');
            $view->with([
                'currentCurrency' => $currencyData['currency'],
                'currentSymbol' => $currencyData['symbol']
            ]);
        });
    }

}
