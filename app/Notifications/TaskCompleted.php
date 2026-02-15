<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCompleted extends Notification implements ShouldQueue
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
        $assignedUser = $this->task->assignedUser ? $this->task->assignedUser->firstname . ' ' . $this->task->assignedUser->lastname : 'غير معروف';

        return (new MailMessage)
            ->subject('Task Completed: ' . $this->task->title)
            ->greeting('Hello ' . $notifiable->firstname . '،')
            ->line('The task "' . $this->task->title . '" has been completed by ' . $assignedUser)
            ->line('Task Description: ' . $this->task->description)
            ->line('Completion Date: ' . \Carbon\Carbon::parse($this->task->completed_at)->format('Y/m/d H:i'))
            ->action('View Tasks', $url)
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
        $assignedUser = $this->task->assignedUser ? $this->task->assignedUser->firstname . ' ' . $this->task->assignedUser->lastname : 'غير معروف';
        return [
            'title' => 'Task Completed',
            'details' => 'The task "' . $this->task->title . '" has been completed by ' . $assignedUser,
            'link' => $url,
            'icon' => 'fas fa-tasks',
        ];
    }
}
