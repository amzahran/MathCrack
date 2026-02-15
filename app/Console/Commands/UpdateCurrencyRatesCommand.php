<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class UpdateCurrencyRatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update';

    /**
     * Description of the command
     *
     * @var string
     */
    protected $description = 'Update Currency Rates Using API';

    /**
     * Execute the command
     */
    public function handle()
    {
        $this->info('Starting Currency Rates Update...');

        try {
            if (\updateCurrencyRates()) {
                // Clear the cache and update it after the update
                Cache::forget('app_cached_data');
                $this->info('Currency Rates Updated Successfully.');
                return 0;
            }

            $this->error('Failed to Update Currency Rates.');
            return 1;
        } catch (\Exception $e) {
            $this->error('An error occurred while updating currency rates: ' . $e->getMessage());
            return 1;
        }
    }
}