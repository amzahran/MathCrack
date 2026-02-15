@extends('themes.default.layouts.back.master')
@section('title', __('l.Tasks Calendar'))

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <style>
        .fc-event {
            cursor: pointer;
        }

        .badge-status {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
            border-radius: 50rem;
        }

        .task-details {
            margin-top: 15px;
        }

        .fc-today-button {
            text-transform: capitalize;
        }

        .fc-header-toolbar {
            margin-bottom: 1.5rem !important;
        }

        .fc-toolbar-chunk .btn-group {
            box-shadow: none !important;
        }

        .fc-theme-standard .fc-scrollgrid {
            border-radius: 0.5rem;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #f0f0f0;
        }

        .fc-day-today {
            background-color: rgba(105, 108, 255, .1) !important;
        }

        .fc-daygrid-day-number,
        .fc-col-header-cell-cushion {
            color: #566a7f;
            text-decoration: none !important;
        }

        .fc-event-time {
            display: none;
        }

        #task-modal .modal-footer {
            justify-content: space-between;
        }

        .btn-action {
            margin-right: 5px;
        }

        .fc-toolbar-title {
            text-transform: capitalize;
        }

        .calendar-filter-wrapper {
            margin-bottom: 1.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card">
            <div class="card-body">
                <div class="calendar-filter-wrapper">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="filter-status" class="form-label">{{ __('l.Status Filter') }}</label>
                                <select id="filter-status" class="form-select">
                                    <option value="all" selected>{{ __('l.All') }}</option>
                                    <option value="new">{{ __('l.New') }}</option>
                                    <option value="in_progress">{{ __('l.In Progress') }}</option>
                                    <option value="completed">{{ __('l.Completed') }}</option>
                                    <option value="delayed">{{ __('l.Delayed') }}</option>
                                    <option value="cancelled">{{ __('l.Cancelled') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="filter-view" class="form-label">{{ __('l.Calendar View') }}</label>
                                <select id="filter-view" class="form-select">
                                    <option value="dayGridMonth" selected>{{ __('l.Month') }}</option>
                                    <option value="timeGridWeek">{{ __('l.Week') }}</option>
                                    <option value="timeGridDay">{{ __('l.Day') }}</option>
                                    <option value="listWeek">{{ __('l.List') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Task Details Modal -->
        <div class="modal fade" id="task-modal" tabindex="-1" aria-labelledby="task-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="task-modal-label">{{ __('l.Task Details') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <h5 id="task-title" class="mb-0"></h5>
                            <span id="task-status" class="badge badge-status"></span>
                        </div>
                        <p id="task-description" class="mb-3"></p>
                        <div class="row task-details">
                            <div class="col-md-12 mb-2">
                                <strong><i class="fa-regular fa-calendar me-1"></i>{{ __('l.Due Date') }}:</strong>
                                <span id="task-due-date"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong><i class="fa-regular fa-user me-1"></i>{{ __('l.Assigned To') }}:</strong>
                                <span id="task-assigned-to"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong><i class="fa-solid fa-user-plus me-1"></i>{{ __('l.Created By') }}:</strong>
                                <span id="task-created-by"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="current-task-id" value="">
                        <div>
                            @can('edit tasks')
                                <button type="button" class="btn btn-primary btn-action edit-task">
                                    <i class="fa-regular fa-pen-to-square me-1"></i>{{ __('l.Edit') }}
                                </button>
                            @endcan
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('l.Close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const statusFilter = document.getElementById('filter-status');
            const viewFilter = document.getElementById('filter-view');
            const taskModal = new bootstrap.Modal(document.getElementById('task-modal'));
            const quickStatusUpdate = document.getElementById('quick-status-update');

            // تكوين الألوان حسب الحالة
            const statusColors = {
                'new': '#3788d8',
                'in_progress': '#f39c12',
                'completed': '#28a745',
                'delayed': '#dc3545',
                'cancelled': '#6c757d'
            };

            // تكوين نص الحالة باللغة الحالية
            const statusText = {
                'new': "{{ __('l.New') }}",
                'in_progress': "{{ __('l.In Progress') }}",
                'completed': "{{ __('l.Completed') }}",
                'delayed': "{{ __('l.Delayed') }}",
                'cancelled': "{{ __('l.Cancelled') }}"
            };

            // جميع الأحداث من البيانات المرسلة
            const allEvents = @json($calendarEvents);
            console.log('Calendar events:', allEvents); // للتصحيح: طباعة البيانات للتحقق
            let filteredEvents = [...allEvents]; // نسخة للبيانات المفلترة

            // تطبيق الفلتر على الأحداث
            function applyStatusFilter() {
                const filter = statusFilter.value;
                if (filter === 'all') {
                    filteredEvents = [...allEvents];
                } else {
                    filteredEvents = allEvents.filter(event => event.extendedProps.status === filter);
                }
                calendar.removeAllEvents();
                calendar.addEventSource(filteredEvents);
            }

            // تطبيق تغيير العرض
            function applyViewChange() {
                const view = viewFilter.value;
                calendar.changeView(view);
            }

            // التقويم
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: filteredEvents,
                locale: "{{ app()->getLocale() }}",
                direction: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}",
                firstDay: 0, // الأحد كأول يوم في الأسبوع
                timeZone: 'local',
                height: 'auto',
                aspectRatio: 2,
                eventClick: function(info) {
                    console.log('Event clicked:', info.event); // للتصحيح: طباعة بيانات الحدث عند النقر عليه

                    // عرض تفاصيل المهمة
                    document.getElementById('task-title').innerText = info.event.title;
                    document.getElementById('task-description').innerText = info.event.extendedProps.description || '';

                    const status = info.event.extendedProps.status;
                    const statusBadge = document.getElementById('task-status');
                    statusBadge.innerText = statusText[status];
                    statusBadge.style.backgroundColor = statusColors[status];

                    const dueDate = new Date(info.event.start);
                    document.getElementById('task-due-date').innerText = dueDate.toLocaleString();

                    // التأكد من وجود بيانات المستخدمين
                    document.getElementById('task-assigned-to').innerText = info.event.extendedProps.assigned_to || '{{ __("l.Not Assigned") }}';
                    document.getElementById('task-created-by').innerText = info.event.extendedProps.created_by || '{{ __("l.Unknown") }}';

                    // تخزين معرف المهمة
                    document.getElementById('current-task-id').value = info.event.extendedProps.taskId;

                    // فتح النافذة المنبثقة
                    taskModal.show();
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
                noEventsContent: "{{ __('l.No tasks to display') }}"
            });

            calendar.render();

            // تحديث عند تغيير الفلتر
            statusFilter.addEventListener('change', applyStatusFilter);
            viewFilter.addEventListener('change', applyViewChange);

            document.querySelector('.edit-task')?.addEventListener('click', function() {
                const taskId = document.getElementById('current-task-id').value;
                window.location.href = "{{ route('dashboard.admins.tasks-edit') }}?id=" + taskId;
            });

            // تحقق من المهام المتأخرة
            fetch("{{ route('dashboard.admins.tasks-check-overdue') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.count > 0) {
                        toastr.info(
                            `{{ __('l.Updated status of') }} ${data.count} {{ __('l.overdue tasks') }}`);
                    }
                });
        });
    </script>
@endsection
