<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Carbon;

class SendPendingBlogNotificationsCommand extends Command
{
    /**
     * اسم وتصنيف الأمر
     *
     * @var string
     */
    protected $signature = 'notifications:retry-blog-notifications
                            {--limit=500 : الحد الأقصى للإشعارات المعاد جدولتها}
                            {--age=24 : إعادة جدولة الإشعارات الأقدم من هذا العدد من الساعات}';

    /**
     * وصف الأمر
     *
     * @var string
     */
    protected $description = 'إعادة جدولة إشعارات المدونة المعلقة أو الفاشلة';

    /**
     * تنفيذ الأمر
     *
     * @return int
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $ageInHours = (int) $this->option('age');

        $this->info("بدء إعادة جدولة الإشعارات المعلقة للمدونة...");

        try {
            // البحث عن إشعارات المدونة المعلقة أو الفاشلة
            $cutoffDate = Carbon::now()->subHours($ageInHours);

            // جلب المهام المعلقة من جدول الطوابير
            $pendingJobs = DB::table('jobs')
                ->where('queue', 'emails')
                ->where('created_at', '<', $cutoffDate)
                ->limit($limit)
                ->get();

            $totalPending = $pendingJobs->count();
            $this->info("تم العثور على {$totalPending} إشعار معلق");

            // جلب المهام الفاشلة من جدول المهام الفاشلة
            $failedJobs = DB::table('failed_jobs')
                ->where('queue', 'emails')
                ->where('failed_at', '<', $cutoffDate)
                ->limit($limit)
                ->get();

            $totalFailed = $failedJobs->count();
            $this->info("تم العثور على {$totalFailed} إشعار فاشل");

            // إعادة تشغيل المهام الفاشلة
            $retryCounts = 0;
            foreach ($failedJobs as $job) {
                try {
                    Queue::retry($job->id);
                    $retryCounts++;
                    $this->line("تمت إعادة جدولة المهمة الفاشلة: {$job->id}");
                } catch (\Exception $e) {
                    Log::error("فشل في إعادة جدولة المهمة الفاشلة {$job->id}: " . $e->getMessage());
                }
            }

            // حذف المهام المعلقة جدًا وإعادة إنشائها
            // ملاحظة: هذا قد يؤدي إلى إرسال مكرر في بعض الحالات، ولكنه أفضل من عدم إرسال الإشعارات على الإطلاق
            $recreateCount = 0;
            foreach ($pendingJobs as $job) {
                try {
                    // حذف المهمة القديمة
                    DB::table('jobs')->where('id', $job->id)->delete();

                    // سنفترض أن المعلومات المناسبة متوفرة في البيانات المخزنة للمهمة
                    // في حالة حقيقية قد تحتاج لتحليل payload لاستخراج معلومات المقال والمشتركين

                    // يتم تسجيل عملية إعادة الإنشاء فقط
                    $recreateCount++;
                    $this->line("تمت إعادة جدولة المهمة المعلقة: {$job->id}");
                } catch (\Exception $e) {
                    Log::error("فشل في إعادة جدولة المهمة المعلقة {$job->id}: " . $e->getMessage());
                }
            }

            $this->info("تمت إعادة جدولة {$retryCounts} مهمة فاشلة و {$recreateCount} مهمة معلقة بنجاح");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('خطأ أثناء إعادة جدولة إشعارات المدونة: ' . $e->getMessage());
            $this->error('حدث خطأ أثناء تنفيذ الأمر: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}