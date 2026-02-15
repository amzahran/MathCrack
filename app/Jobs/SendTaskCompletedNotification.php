<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTaskCompletedNotification implements ShouldQueue
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
        // التأكد من وجود منشئ للمهمة
        if ($this->task->created_by) {
            $creator = User::find($this->task->created_by);

            if ($creator) {
                // إرسال إشعار إلى منشئ المهمة
                $creator->notify(new TaskCompleted($this->task));
            }
        }
    }
}
