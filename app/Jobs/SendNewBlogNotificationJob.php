<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Blog;
use App\Models\Subscriber;
use App\Notifications\NewBlogNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendNewBlogNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $blog;
    protected $subscriberIds;

    /**
     * عدد المحاولات القصوى
     *
     * @var int
     */
    public $tries = 3;

    /**
     * الوقت بالثواني قبل إعادة المحاولة
     *
     * @var int
     */
    public $backoff = [30, 60, 120];

    /**
     * الوقت بالثواني قبل اعتبار المهمة معلقة
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * إنشاء مهمة جديدة
     *
     * @param  \App\Models\Blog  $blog
     * @param  array  $subscriberIds
     * @return void
     */
    public function __construct(Blog $blog, array $subscriberIds)
    {
        $this->blog = $blog;
        $this->subscriberIds = $subscriberIds;

        // التأكد من عدم تأخير المهام الأخرى في نفس السلسلة
        $this->onQueue('emails');
    }

    /**
     * تنفيذ المهمة
     *
     * @return void
     */
    public function handle()
    {
        try {
            // تسجيل بداية تنفيذ المهمة
            Log::info('Starting SendNewBlogNotificationJob for blog ID: ' . $this->blog->id . ', batch subscribers: ' . count($this->subscriberIds));

            // التحقق من وجود البلوج
            if (!$this->blog || !Blog::find($this->blog->id)) {
                Log::error('Blog not found for notification, ID: ' . $this->blog->id);
                return;
            }

            // جلب المشتركين من قاعدة البيانات (سيكون لدينا الـIDs فقط، نجلب الكائنات الكاملة هنا)
            $subscribers = Subscriber::whereIn('id', $this->subscriberIds)
                                    ->where('is_active', true)
                                    ->get();

            if ($subscribers->isEmpty()) {
                Log::info('No active subscribers found for blog notification batch');
                return;
            }

            // إرسال الإشعارات
            Log::info('Sending notifications to ' . count($subscribers) . ' subscribers for blog ID: ' . $this->blog->id);

            // استخدام Notification::send بدلاً من تعديل NewBlogNotification
            foreach ($subscribers as $subscriber) {
                try {
                    // إرسال كل إشعار بشكل منفصل لتجنب توقف العملية بالكامل إذا فشل إشعار واحد
                    $subscriber->notify(new NewBlogNotification($this->blog));
                } catch (\Exception $e) {
                    Log::error('Failed to send notification to subscriber ' . $subscriber->id . ': ' . $e->getMessage());
                }
            }

            Log::info('Blog notification batch completed for ' . count($subscribers) . ' subscribers');
        } catch (\Exception $e) {
            Log::error('Error in SendNewBlogNotificationJob: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

            // التحقق من عدد المحاولات المتبقية ورمي الاستثناء مرة أخرى إذا لم تكن هذه المحاولة الأخيرة
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }

    /**
     * معالجة فشل المهمة
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('SendNewBlogNotificationJob failed: ' . $exception->getMessage());
    }
}