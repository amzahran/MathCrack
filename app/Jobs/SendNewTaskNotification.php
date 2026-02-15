<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Notifications\NewTaskAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewTaskNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;

    /**
     * إنشاء مثيل جديد من الوظيفة.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * تنفيذ الوظيفة.
     */
    public function handle(): void
    {
        // التأكد من وجود مستخدم معين للمهمة
        if ($this->task->assigned_to) {
            $user = User::find($this->task->assigned_to);

            if ($user) {
                // إرسال إشعار للمستخدم المعين
                $user->notify(new NewTaskAssigned($this->task));
            }
        }
    }
}
