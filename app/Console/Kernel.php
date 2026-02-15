<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // ... existing schedules ...

        // جدولة إعادة محاولة إرسال إشعارات المدونة المعلقة أو الفاشلة كل 6 ساعات
        $schedule->command('notifications:retry-blog-notifications')
                ->everySixHours()
                ->appendOutputTo(storage_path('logs/notification-retries.log'))
                ->emailOutputOnFailure(config('mail.admin_email'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}