@extends('themes.default.layouts.back.master')

@section('title')
    {{ __('l.Edit Task') }}
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/themes/default/vendor/libs/flatpickr/flatpickr.css') }}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('l.Edit Task') }}</h5>
                        <a href="{{ route('dashboard.admins.tasks') }}" class="btn btn-secondary">
                            <i class="icon-base bx bx-arrow-back"></i>
                            <span>{{ __('l.Back to List') }}</span>
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('dashboard.admins.tasks-update', ['id' => request()->id]) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="title">{{ __('l.Task Title') }}</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ $task->title }}" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="flatpickr-datetime">{{ __('l.Due Date') }}</label>
                                    <input type="datetime-local" class="form-control flatpickr" id="flatpickr-datetime" name="due_date" value="{{ date('Y-m-d H:i', strtotime($task->due_date ?? now())) }}" required />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="status">{{ __('l.Task Status') }}</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="new" {{ $task->status == 'new' ? 'selected' : '' }}>{{ __('l.New') }}</option>
                                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>{{ __('l.In Progress') }}</option>
                                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>{{ __('l.Completed') }}</option>
                                        <option value="delayed" {{ $task->status == 'delayed' ? 'selected' : '' }}>{{ __('l.Delayed') }}</option>
                                        <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>{{ __('l.Cancelled') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="assigned_to">{{ __('l.Assign To') }}</label>
                                    <select class="form-select select2" id="assigned_to" name="assigned_to" required>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $task->assigned_to == $user->id ? 'selected' : '' }}>
                                                {{ $user->firstname.' '.$user->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="description">{{ __('l.Task Description') }}</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required>{{ $task->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-primary me-3">
                                            <i class="icon-base bx bx-save"></i>
                                            <span>{{ __('l.Save Changes') }}</span>
                                        </button>
                                        <a href="{{ route('dashboard.admins.tasks') }}" class="btn btn-secondary">
                                            <i class="icon-base bx bx-x"></i>
                                            <span>{{ __('l.Cancel') }}</span>
                                        </a>
                                    </div>
                                    <button type="button" id="deleteTaskBtn" class="btn btn-danger"
                                        data-task-id="{{ request()->id }}">
                                        <i class="icon-base bx bx-trash"></i>
                                        <span>{{ __('l.Delete Task') }}</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('l.Task Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm me-3">
                                <img src="{{ asset($task->creator->photo) }}" alt="{{ $task->creator->firstname.' '.$task->creator->lastname ?? 'غير معروف' }}" class="img-fluid rounded-circle">
                            </div>
                            <div>
                                <h6 class="mb-0">{{ __('l.Created By') }}: {{ $task->creator->firstname.' '.$task->creator->lastname ?? 'غير معروف' }}</h6>
                                <small class="text-muted">{{ $task->created_at->format('Y/m/d H:i') }}</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <img src="{{ asset($task->assignedUser->photo) }}" alt="{{ $task->assignedUser->firstname.' '.$task->assignedUser->lastname ?? 'غير معين' }}" class="img-fluid rounded-circle">
                            </div>
                            <div>
                                <h6 class="mb-0">{{ __('l.Assigned To') }}: {{ $task->assignedUser->firstname.' '.$task->assignedUser->lastname ?? 'غير معين' }}</h6>
                                <small class="text-muted">{{ $task->updated_at->format('Y/m/d H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('l.Task Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @php
                                $statusClass = [
                                    'new' => 'bg-label-primary',
                                    'in_progress' => 'bg-label-warning',
                                    'completed' => 'bg-label-success',
                                    'delayed' => 'bg-label-danger',
                                    'cancelled' => 'bg-label-secondary'
                                ];

                                $statusText = [
                                    'new' => __('l.New'),
                                    'in_progress' => __('l.In Progress'),
                                    'completed' => __('l.Completed'),
                                    'delayed' => __('l.Delayed'),
                                    'cancelled' => __('l.Cancelled')
                                ];
                            @endphp

                            <span class="badge {{ $statusClass[$task->status] }} fs-6">
                                {{ $statusText[$task->status] }}
                            </span>
                        </div>

                        <div>
                            <h6>{{ __('l.Due Date') }}:</h6>
                            <p class="mb-0 {{ \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'completed' ? 'text-danger' : '' }}">
                                {{ \Carbon\Carbon::parse($task->due_date)->format('Y/m/d') }}
                                @if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'completed')
                                    <span class="text-danger ms-2">{{ __('l.Delayed') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('assets/themes/default/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/themes/default/vendor/libs/flatpickr/flatpickr.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة اختيار التاريخ
        flatpickr('.flatpickr', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        // معالجة زر حذف المهمة
        const deleteTaskBtn = document.getElementById('deleteTaskBtn');
        if (deleteTaskBtn) {
            deleteTaskBtn.addEventListener('click', function(e) {
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
        }
    });
</script>
@endsection
