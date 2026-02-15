<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Blog;
use Illuminate\Support\Facades\Log;

class NewBlogNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * بيانات المقال
     *
     * @var \App\Models\Blog
     */
    protected $blog;

    /**
     * عدد المحاولات القصوى للإرسال
     *
     * @var int
     */
    public $tries = 3;

    /**
     * الوقت بالثواني قبل اعتبار المهمة معلقة
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * إنشاء إشعار جديد
     *
     * @param  \App\Models\Blog  $blog
     * @return void
     */
    public function __construct(Blog $blog)
    {
        $this->blog = $blog;

        // تحديد اسم الطابور الذي سيتم استخدامه
        $this->queue = 'notifications';

        // حفظ البلوج فقط بالحقول الضرورية للإشعار لتقليل استهلاك الذاكرة
        $this->afterCommit();
    }

    /**
     * الحصول على قنوات الإشعار
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * الحصول على رسالة البريد الإلكتروني
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        try {
            // استخدام الوصول المباشر لجدول الإعدادات بدلاً من الكاش
            $defaultLanguage = \App\Models\Setting::where('option', 'default_language')->first()->value ?? 'en';
            $title = $this->blog->getTranslation('title', $defaultLanguage);
            $siteTitle = \App\Models\Setting::where('option', 'site_name')->first()->value ?? config('app.name');
            $url = route('blog.show', $this->blog->slug);

            return (new MailMessage)
                        ->subject(__('l.New Article Published') . ': ' . $title)
                        ->markdown('emails.blog.new_article', [
                            'blog' => $this->blog,
                            'url' => $url,
                            'title' => $title,
                            'siteTitle' => $siteTitle,
                            'defaultLanguage' => $defaultLanguage,
                            'unsubscribeUrl' => $notifiable->unsubscribe_url,
                        ]);
        } catch (\Exception $e) {
            Log::error('Error creating email notification: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * الحصول على التمثيل المصفوفي للإشعار
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $defaultLanguage = \App\Models\Setting::where('option', 'default_language')->first()->value ?? 'en';
        return [
            'blog_id' => $this->blog->id,
            'title' => $this->blog->getTranslation('title', $defaultLanguage),
            'slug' => $this->blog->slug,
        ];
    }

    /**
     * معالجة فشل الإشعار
     *
     * @param  mixed  $notifiable
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed($notifiable, \Throwable $exception)
    {
        Log::error('Failed to send blog notification to: ' . $notifiable->email . '. Error: ' . $exception->getMessage());
    }
}