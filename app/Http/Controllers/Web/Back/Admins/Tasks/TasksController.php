<?php

namespace App\Http\Controllers\Web\Back\Admins\Tasks;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Jobs\SendNewTaskNotification;
use App\Jobs\SendTaskCompletedNotification;

class TasksController extends Controller
{
    public function index()
    {
        if (!Gate::allows('show tasks')) {
            return view('themes.default.back.permission-denied');
        }

        // if the user has access to all tasks, show all tasks
        if (Gate::allows('access all tasks')) {
            $tasks = Task::with(['assignedUser', 'creator'])
            ->orderBy('due_date', 'asc')
            ->get();
        } else {
            if (Gate::allows('add tasks')) {
                // if the user has access to add tasks, show all tasks created by the user or assigned to the user
                $tasks = Task::where('created_by', auth()->user()->id)
                             ->orWhere('assigned_to', auth()->user()->id)
                             ->with(['assignedUser', 'creator'])
                             ->orderBy('due_date', 'asc')
                             ->get();

            } else {
                // if the user does not have access to add tasks, show only the tasks assigned to the user
                $tasks = Task::where('assigned_to', auth()->user()->id)
                             ->with(['assignedUser', 'creator'])
                             ->orderBy('due_date', 'asc')
                             ->get();
            }
        }

        $users = User::whereHas('roles')
        ->whereNotIn('id', [auth()->user()->id, 1])
        ->get();

        $tasksByStatus = [
            'new' => $tasks->where('status', 'new'),
            'in_progress' => $tasks->where('status', 'in_progress'),
            'completed' => $tasks->where('status', 'completed'),
            'delayed' => $tasks->where('status', 'delayed'),
            'cancelled' => $tasks->where('status', 'cancelled')
        ];

        return view('themes.default.back.admins.tasks.tasks-list', compact('tasks', 'tasksByStatus', 'users'));
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add tasks')) {
            return view('themes.default.back.permission-denied');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $task = new Task();
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->due_date = $request->input('due_date');
        $task->assigned_to = $request->input('assigned_to');
        $task->created_by = auth()->user()->id;
        $task->save();

        // إرسال إشعار للمستخدم المعيّن
        SendNewTaskNotification::dispatch($task);

        return redirect()->back()->with('success', __('l.Task added successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit tasks')) {
            return view('themes.default.back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $task = Task::findOrFail($id);
        $users = User::whereHas('roles')
        ->whereNotIn('id', [auth()->user()->id, 1])
        ->get();

        return view('themes.default.back.admins.tasks.tasks-edit', ['task' => $task, 'users' => $users]);
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit tasks')) {
            return view('themes.default.back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:new,in_progress,completed,delayed,cancelled',
            'due_date' => 'required|date',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $task = Task::findOrFail($id);
        $oldStatus = $task->status;
        $oldAssignedTo = $task->assigned_to;

        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->status = $request->input('status');
        $task->due_date = $request->input('due_date');
        $task->assigned_to = $request->input('assigned_to');

        // إذا تم تغيير حالة المهمة إلى "مكتملة"، قم بتعيين تاريخ الإكمال
        if ($request->input('status') == 'completed' && $oldStatus != 'completed') {
            $task->completed_at = now();

            // إرسال إشعار إلى منشئ المهمة
            SendTaskCompletedNotification::dispatch($task);
        } else if ($oldStatus == 'completed' && $request->input('status') != 'completed') {
            // إذا تم تغيير الحالة من "مكتملة" إلى حالة أخرى، نصفر تاريخ الإكمال
            $task->completed_at = null;
        }

        // إذا تم تغيير المستخدم المعين، نرسل له إشعارًا
        if ($oldAssignedTo != $request->input('assigned_to')) {
            $task->save(); // نحفظ المهمة أولاً للحصول على بياناتها المحدثة
            SendNewTaskNotification::dispatch($task);
        } else {
            $task->save();
        }

        return redirect()->back()->with('success', __('l.Task updated successfully'));
    }

    public function updateStatus(Request $request)
    {
        // التحقق من الصلاحيات
        if (!Gate::allows('edit tasks')) {
            // إذا لم يكن لديه صلاحية تعديل المهام، نتحقق ما إذا كانت المهمة مسندة إليه
            $task = Task::findOrFail($request->task_id);

            // التحقق ما إذا كان المستخدم هو الشخص المسند إليه المهمة
            if ($task->assigned_to != auth()->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => __('l.You do not have permission to edit this task because it is not assigned to you')
                ], 403);
            }

            // التحقق ما إذا كان يحاول نقل المهمة إلى حالة غير مسموح بها
            if (!in_array($request->status, ['new', 'in_progress', 'completed'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('l.You do not have permission to edit this task because it is not assigned to you')
                ], 403);
            }
        }

        $request->validate([
            'task_id' => 'required',
            'status' => 'required|in:new,in_progress,completed,delayed,cancelled',
        ]);

        try {
            $task = Task::findOrFail($request->task_id);
            $oldStatus = $task->status;
            $task->status = $request->status;

            // إذا تم نقل المهمة من حالة "مكتملة" إلى حالة أخرى، نصفر تاريخ الإكمال
            if ($oldStatus == 'completed' && $request->status != 'completed' && $request->has('reset_completed_at')) {
                $task->completed_at = null;
            }

            // إذا تم تغيير حالة المهمة إلى "مكتملة"، قم بتعيين تاريخ الإكمال
            if ($request->status == 'completed' && $oldStatus != 'completed') {
                $task->completed_at = now();

                // إرسال إشعار إلى منشئ المهمة عندما تتغير الحالة إلى مكتملة
                SendTaskCompletedNotification::dispatch($task);
            }

            $task->save();

            // إرفاق العلاقات للاستخدام في العرض
            $task->load(['assignedUser', 'creator']);

            return response()->json([
                'success' => true,
                'message' => __('l.Task status updated successfully'),
                'task' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('l.An error occurred while updating the task status: ') . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete tasks')) {
            return view('themes.default.back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $task = Task::findOrFail($id);

        $task->delete();

        return redirect()->back()->with('success', __('l.Task deleted successfully'));
    }

    public function deleteSelected(Request $request)
    {
        if (!Gate::allows('delete tasks')) {
            return view('themes.default.back.permission-denied');
        }

        $ids = explode(',', $request->ids);
        Task::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', __('l.Tasks deleted successfully'));
    }

    /**
     * تحديث حالة المهام المتأخرة تلقائيًا
     */
    public function checkOverdueTasks(Request $request)
    {
        try {
            // الحصول على المهام التي تجاوزت موعد استحقاقها ولم تكتمل بعد ولم تكن في حالة متأخرة أو ملغاة
            $overdueTasks = Task::where('due_date', '<', now())
                ->whereNotIn('status', ['completed', 'delayed', 'cancelled'])
                ->get();

            $count = 0;
            foreach ($overdueTasks as $task) {
                $task->status = 'delayed';
                $task->save();
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => __('l.Overdue tasks status updated successfully'),
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('l.An error occurred while updating the overdue tasks status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض تقويم المهام
     */
    public function calendar()
    {
        if (!Gate::allows('show tasks')) {
            return view('themes.default.back.permission-denied');
        }

        // إذا كان المستخدم لديه صلاحية الوصول إلى جميع المهام، اعرض جميع المهام
        if (Gate::allows('access all tasks')) {
            $tasks = Task::with(['assignedUser', 'creator'])
                ->orderBy('due_date', 'asc')
                ->get();
        } else {
            if (Gate::allows('add tasks')) {
                // إذا كان المستخدم لديه صلاحية إضافة المهام، اعرض جميع المهام التي أنشأها المستخدم أو المسندة إليه
                $tasks = Task::where('created_by', auth()->user()->id)
                    ->orWhere('assigned_to', auth()->user()->id)
                    ->with(['assignedUser', 'creator'])
                    ->orderBy('due_date', 'asc')
                    ->get();
            } else {
                // إذا كان المستخدم ليس لديه صلاحية إضافة المهام، اعرض فقط المهام المسندة إليه
                $tasks = Task::where('assigned_to', auth()->user()->id)
                    ->with(['assignedUser', 'creator'])
                    ->orderBy('due_date', 'asc')
                    ->get();
            }
        }

        // تجهيز بيانات المهام للتقويم
        $calendarEvents = [];

        foreach ($tasks as $task) {
            // تحديد لون المهمة حسب حالتها
            $color = $this->getStatusColor($task->status);

            $calendarEvents[] = [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->due_date,
                'end' => $task->due_date,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#fff',
                'extendedProps' => [
                    'status' => $task->status,
                    'description' => $task->description,
                    'assigned_to' => $task->assignedUser->firstname . ' ' . $task->assignedUser->lastname ?? '',
                    'created_by' => $task->creator->firstname . ' ' . $task->creator->lastname ?? '',
                    'taskId' => encrypt($task->id)
                ]
            ];
        }

        $users = User::whereHas('roles')
            ->whereNotIn('id', [auth()->user()->id, 1])
            ->get();

        return view('themes.default.back.admins.tasks.tasks-calendar', compact('calendarEvents', 'users'));
    }

    /**
     * الحصول على لون حالة المهمة
     */
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'new':
                return '#f39c12'; // أزرق
            case 'in_progress':
                return '#3788d8'; // برتقالي
            case 'completed':
                return '#28a745'; // أخضر
            case 'delayed':
                return '#dc3545'; // أحمر
            case 'cancelled':
                return '#6c757d'; // رمادي
            default:
                return '#3788d8'; // أزرق افتراضي
        }
    }
}
