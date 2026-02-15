<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task;

    /**
     * إنشاء مثيل جديد من الإشعار.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * الحصول على قنوات التسليم للإشعار.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * الحصول على تمثيل البريد الإلكتروني للإشعار.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('dashboard.admins.tasks');

        return (new MailMessage)
            ->subject('New Task Assigned: ' . $this->task->title)
            ->greeting('Hello ' . $notifiable->firstname . '،')
            ->line('A new task has been assigned to you with the title: ' . $this->task->title)
            ->line('Task Description: ' . $this->task->description)
            ->line('Due Date: ' . \Carbon\Carbon::parse($this->task->due_date)->format('Y/m/d H:i'))
            ->action('View Task', $url)
            ->line('Thank you for using our application!');
    }

    /**
     * الحصول على مصفوفة بيانات الإشعار.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $url = route('dashboard.admins.tasks');
        return [
            'title' => 'New Task Assigned',
            'details' => 'A new task has been assigned to you with the title: ' . $this->task->title,
            'link' => $url,
            'icon' => 'fas fa-tasks',
        ];
    }
}
