<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// cronjob for webuzo
//usr/bin/php /home/superadmin/addondomains/smsacontrol.com/artisan schedule:run >> /dev/null 2>&1
// cronjob for cyberpanel
//usr/local/lsws/lsphp83/bin/php /home/smsacontrol.com/public_html/artisan schedule:run >> /dev/null 2>&1
// cronjob for hostinger
//domains/5damah.com/public_html/artisan schedule:run >> /dev/null 2>&1

// Task to run every minute, including queue and any other commands
Schedule::command('queue:work  --queue=default,emails,notifications --stop-when-empty')->everyMinute()->withoutOverlapping();
// Add any other commands here to run every minute

// Task to run every day (Updating currency rates)
Schedule::command('currency:update')->daily();

// Register any additional commands here as needed


// Run artisan schedule:run to execute all scheduled commands
Artisan::command('custom:schedule', function () {
    Artisan::call('schedule:run');
})->purpose('Run all scheduled tasks');