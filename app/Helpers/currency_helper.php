<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

if (!function_exists('convertCurrency') && !function_exists('updateCurrencyRates')) {
    /**
     * Convert amount between two currencies using their codes.
     *
     * @param float $amount - Amount to be converted
     * @param string $fromCurrencyCode - Currency code of the source
     * @param string $toCurrencyCode - Currency code of the target
     * @return float - Converted amount
     */
    function convertCurrency(float $amount, string $fromCurrencyCode, string $toCurrencyCode): float
    {
        // Fetch rates from the database
        $fromRate = DB::table('currencies')
            ->where('code', $fromCurrencyCode)
            ->value('rate');

        $toRate = DB::table('currencies')
            ->where('code', $toCurrencyCode)
            ->value('rate');

        // Ensure rates are valid
        if (!$fromRate || !$toRate) {
            throw new \InvalidArgumentException('Invalid currency code provided.');
        }

        if ($fromRate == 0) {
            throw new \InvalidArgumentException('From rate cannot be zero.');
        }

        // Perform conversion
        return ($amount / $fromRate) * $toRate;
    }

    function updateCurrencyRates(): bool
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post(config('app.exchangeCurrencyUrl'), [
                'form_params' => [
                    'domain' => request()->getHost(),
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            // التحقق من صحة الاستجابة وأنها تحتوي على معدلات الصرف
            if (!$data || !isset($data['rates']) || !is_array($data['rates'])) {
                throw new \Exception('Invalid response format or missing rates data');
            }

            // تحديث الأسعار في قاعدة البيانات باستخدام معدلات الصرف من المفتاح 'rates'
            foreach ($data['rates'] as $code => $rate) {
                DB::table('currencies')
                    ->where('code', $code)
                    ->where('is_manual', false)
                    ->update(['rate' => $rate, 'last_updated_at' => now()]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Currency rates update failed: ' . $e->getMessage());
            return false;
        }
    }

}
