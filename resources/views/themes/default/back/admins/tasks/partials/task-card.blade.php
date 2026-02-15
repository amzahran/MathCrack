@php
    $dueDate = \Carbon\Carbon::parse($task->due_date);
    $now = \Carbon\Carbon::now();
    $createdAt = \Carbon\Carbon::parse($task->created_at);

    // التحقق من حالة المهمة وتحديد ما إذا كانت متأخرة (للعرض فقط، التغيير الفعلي في قاعدة البيانات يتم من خلال checkOverdueTasks)
    $isOverdue = $dueDate->isPast() && !in_array($task->status, ['completed', 'delayed', 'cancelled']);

    // إذا كانت المهمة متأخرة، نعرضها كمتأخرة حتى إذا لم يتم تحديثها في قاعدة البيانات بعد
    $displayStatus = $isOverdue ? 'delayed' : $task->status;

    // حساب المدة الكلية من تاريخ الإنشاء إلى موعد التسليم
    $totalDuration = $createdAt->diffInSeconds($dueDate);

    // حساب المدة المنقضية من تاريخ الإنشاء إلى الآن
    $elapsedDuration = $createdAt->diffInSeconds($now);

    // حساب النسبة المئوية للوقت المستهلك
    $elapsedPercentage = ($totalDuration > 0) ? min(100, max(0, ($elapsedDuration / $totalDuration) * 100)) : 0;

    // حساب النسبة المئوية للوقت المتبقي
    $remainingPercentage = 100 - $elapsedPercentage;

    // تحديد الألوان والأيقونات حسب حالة المهمة
    $statusConfig = [
        'new' => [
            'color' => 'primary',
            'icon' => 'bx-bell',
            'text' => __('l.New')
        ],
        'in_progress' => [
            'color' => 'info',
            'icon' => 'bx-loader-circle',
            'text' => __('l.In Progress')
        ],
        'completed' => [
            'color' => 'success',
            'icon' => 'bx-check-circle',
            'text' => __('l.Completed')
        ],
        'delayed' => [
            'color' => 'danger',
            'icon' => 'bx-time-five',
            'text' => __('l.Delayed')
        ],
        'cancelled' => [
            'color' => 'secondary',
            'icon' => 'bx-x-circle',
            'text' => __('l.Cancelled')
        ]
    ];

    // استخراج التكوين الخاص بالمهمة الحالية (نستخدم حالة العرض بدلاً من حالة المهمة الفعلية)
    $currentStatus = $statusConfig[$displayStatus];

    // تحديد لون شريط الوقت المتبقي
    $timeBarColor = 'success';
    if ($remainingPercentage < 25 || $dueDate->isPast()) {
        $timeBarColor = 'danger';
    } else if ($remainingPercentage < 50) {
        $timeBarColor = 'warning';
    }

    // إذا كانت المهمة مكتملة، نعرض شريط أخضر كامل
    if ($displayStatus == 'completed') {
        $remainingPercentage = 100;
        $timeBarColor = 'success';
    }

    // إذا كان موعد التسليم قد مر ولم تكتمل المهمة
    if ($dueDate->isPast() && $displayStatus != 'completed') {
        $remainingPercentage = 0;
        $timeBarColor = 'danger';
    }

    // إعداد نص الوقت المتبقي
    if ($displayStatus == 'completed') {
        $timeText = '<span class="text-success"><i class="bx bx-check-circle me-1"></i>' . __('l.Completed') . '</span>';
    } elseif ($dueDate->isPast()) {
        $timeText = '<span class="text-danger"><i class="bx bx-time-five me-1"></i>' . __('l.Delayed') . ' ' . $now->diffForHumans($dueDate, true) . '</span>';
    } else {
        $timeText = '<span class="text-' . $timeBarColor . '"><i class="bx bx-time me-1"></i>' . __('l.Remaining') . ' ' . $now->diffForHumans($dueDate, true) . '</span>';
    }

    // الحصول على المسار الافتراضي للصورة الشخصية
    $defaultAvatar = 'assets/themes/default/img/avatars/user-avatar.png';
    $creatorPhoto = isset($task->creator->photo) && !empty($task->creator->photo) ? $task->creator->photo : $defaultAvatar;
    $assignedPhoto = isset($task->assignedUser->photo) && !empty($task->assignedUser->photo) ? $task->assignedUser->photo : $defaultAvatar;
@endphp

<div class="task-item" data-task-id="{{ $task->id }}">
    <div class="card shadow-sm border-0 h-100 task-card-fancy">
        <div class="card-body p-0">
            <!-- رأس البطاقة مع الحالة والقائمة -->
            <div class="task-header d-flex align-items-center p-3 border-start border-5 border-{{ $currentStatus['color'] }} bg-label-{{ $currentStatus['color'] }} bg-opacity-10">
                <div class="d-flex align-items-center flex-grow-1">
                    @can('delete tasks')
                    <div class="form-check me-2">
                        <input type="checkbox" class="form-check-input task-checkbox" id="task-checkbox-{{ $task->id }}" value="{{ $task->id }}">
                    </div>
                    @endcan
                    <div class="task-badge me-2">
                        <span class="badge bg-{{ $currentStatus['color'] }} rounded-pill">
                            <i class="bx {{ $currentStatus['icon'] }} me-1"></i>
                            {{ $currentStatus['text'] }}
                        </span>
                    </div>
                    <h5 class="mb-0 fw-semibold task-title">{{ $task->title }}</h5>
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-icon btn-sm text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a href="#" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#taskDetails{{ $task->id }}" aria-controls="taskDetails{{ $task->id }}">
                                <i class="bx bx-show me-2"></i>{{ __('l.View Details') }}
                            </a>
                        </li>
                        @can('edit tasks')
                        <li>
                            <a href="{{ route('dashboard.admins.tasks-edit', ['id' => encrypt($task->id)]) }}" class="dropdown-item">
                                <i class="bx bx-edit me-2"></i>{{ __('l.Edit') }}
                            </a>
                        </li>
                        @endcan
                        @can('delete tasks')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="javascript:void(0)" class="dropdown-item text-danger delete-task-btn" data-task-id="{{ encrypt($task->id) }}">
                                <i class="bx bx-trash me-2"></i>{{ __('l.Delete') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </div>

            <!-- محتوى البطاقة -->
            <div class="p-3">
                <!-- وصف المهمة -->
                <p class="mb-3 task-description">{{ Str::limit($task->description, 100) }}</p>

                <!-- شريط الوقت المتبقي -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-medium text-dark">{{ __('l.Remaining Time') }}</span>
                        {!! $timeText !!}
                    </div>
                    <div class="progress rounded-pill" style="height: 8px; background-color: rgba(0,0,0,.09);">
                        <div class="progress-bar bg-{{ $timeBarColor }}"
                            role="progressbar"
                            style="width: {{ $remainingPercentage }}%;"
                            aria-valuenow="{{ $remainingPercentage }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <!-- بيانات المهمة -->
                <div class="task-info mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                    <!-- المسؤول عن المهمة -->
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                            <img src="{{ asset($assignedPhoto) }}" alt="{{ $task->assignedUser->firstname ?? 'مستخدم' }}" class="img-fluid rounded-circle">
                        </div>
                        <div>
                            <span class="fw-medium d-block text-truncate" style="max-width: 120px;">
                                {{ $task->assignedUser->firstname ?? '' }} {{ $task->assignedUser->lastname ?? '' }}
                            </span>
                            <small class="text-muted">{{ __('l.Executor') }}</small>
                        </div>
                    </div>

                    <!-- تاريخ الانشاء -->
                    <div class="text-start">
                        <small class="text-muted">{{ __('l.Creation Time') }}</small>
                        <span class="badge bg-label-dark d-flex align-items-center px-2 py-1">
                            <i class="bx bx-calendar me-1"></i>
                            {{ $createdAt->format('Y/m/d') }}
                        </span>
                    </div>
                    <!-- تاريخ الاستحقاق -->
                    <div class="text-start">
                        <small class="text-muted">{{ __('l.Due Date') }}</small>
                        <span class="badge bg-label-{{ $timeBarColor }} d-flex align-items-center px-2 py-1">
                            <i class="bx bx-calendar me-1"></i>
                            {{ $dueDate->format('Y/m/d') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة عرض تفاصيل المهمة -->
<div class="offcanvas offcanvas-end offcanvas-task-details" tabindex="-1" id="taskDetails{{ $task->id }}" aria-labelledby="taskDetailsLabel{{ $task->id }}">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="taskDetailsLabel{{ $task->id }}">{{ __('l.Task Details') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <!-- عنوان ووصف المهمة -->
        <div class="task-details-section bg-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-{{ $currentStatus['color'] }} p-2">
                    <i class="bx {{ $currentStatus['icon'] }} me-1"></i>
                    {{ $currentStatus['text'] }}
                </span>
                <h5 class="mb-0 fw-bold">{{ $task->title }}</h5>
            </div>
            <p>{{ $task->description }}</p>
        </div>

        <!-- معلومات الوقت والتواريخ -->
        <div class="task-details-section bg-body-tertiary">
            <h6 class="mb-3 fw-semibold">{{ __('l.Task Information') }}</h6>
            <ul class="list-group list-group-flush bg-transparent mb-3">
                <li class="list-group-item bg-transparent d-flex justify-content-between px-0">
                    <span>{{ __('l.Creation Date') }}</span>
                    <span>{{ $createdAt->format('Y/m/d H:i') }}</span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between px-0">
                    <span>{{ __('l.Due Date') }}</span>
                    <span class="{{ $dueDate->isPast() && $displayStatus != 'completed' ? 'text-danger fw-semibold' : '' }}">
                        {{ $dueDate->format('Y/m/d H:i') }}
                    </span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between px-0">
                    <span>{{ __('l.Remaining Time') }}</span>
                    <span class="fw-semibold text-{{ $timeBarColor }}">{!! strip_tags($timeText) !!}</span>
                </li>
            </ul>

            <!-- شريط الوقت المتبقي -->
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ __('l.Remaining Time Percentage') }}</span>
                    <span class="fw-semibold">{{ round($remainingPercentage) }}%</span>
                </div>
                <div class="progress" style="height: 10px; background-color: rgba(0,0,0,.09);">
                    <div class="progress-bar bg-{{ $timeBarColor }}" role="progressbar" style="width: {{ $remainingPercentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- معلومات المسؤولين -->
        <div class="task-details-section bg-body">
            <h6 class="mb-4 fw-semibold">{{ __('l.Task Assignees') }}</h6>

            <div class="row g-3">
                <!-- منشئ المهمة -->
                <div class="col-12">
                    <div class="card border shadow-none">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <img src="{{ asset($creatorPhoto) }}" alt="{{ $task->creator->firstname ?? 'منشئ' }}" class="img-fluid rounded-circle">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $task->creator->firstname ?? '' }} {{ $task->creator->lastname ?? '' }}</h6>
                                    <span class="text-muted small">{{ __('l.Task Creator') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المسؤول عن التنفيذ -->
                <div class="col-12">
                    <div class="card border shadow-none">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <img src="{{ asset($assignedPhoto) }}" alt="{{ $task->assignedUser->firstname ?? 'مسؤول' }}" class="img-fluid rounded-circle">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $task->assignedUser->firstname ?? '' }} {{ $task->assignedUser->lastname ?? '' }}</h6>
                                    <span class="text-muted small">{{ __('l.Task Executor') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- أزرار الإجراءات -->
        <div class="task-details-section bg-body-tertiary">
            <div class="row g-2">
                @can('edit tasks')
                <div class="col-8">
                    <a href="{{ route('dashboard.admins.tasks-edit', ['id' => encrypt($task->id)]) }}" class="btn btn-primary d-block">
                        <i class="bx bx-edit me-1"></i>{{ __('l.Edit') }}
                    </a>
                </div>
                @endcan
                @can('delete tasks')
                <div class="col{{ Gate::allows('edit tasks') ? '-4' : '-12' }}">
                    <button type="button" class="btn btn-danger d-block delete-task-btn" data-task-id="{{ encrypt($task->id) }}">
                        <i class="bx bx-trash me-1"></i>{{ __('l.Delete') }}
                    </button>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // تفعيل أزرار حذف المهام
        const deleteButtons = document.querySelectorAll('.delete-task-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const taskId = this.getAttribute('data-task-id');

                Swal.fire({
                    title: '{{ __('l.Are you sure?') }}',
                    text: "{{ __('l.You will not be able to undo this action!') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('l.Yes, delete it!') }}',
                    cancelButtonText: '{{ __('l.Cancel') }}',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ route('dashboard.admins.tasks-delete', ['id' => '']) }}/${taskId}`;
                    }
                });
            });
        });
    });
</script>