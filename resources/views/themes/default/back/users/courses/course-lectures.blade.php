@extends('themes.default.layouts.back.student-master')

@section('title')
    {{ $course->name }} - @lang('l.lectures')
@endsection

@section('css')
    <style>
        .course-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            border-radius: 15px;
        }

        .course-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .filters-section {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .filters-section h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .filters-section .form-select,
        .filters-section .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        .filters-section .form-select:focus,
        .filters-section .form-control:focus {
            border-color: #1e40af;
            box-shadow: 0 0 0 0.25rem rgba(30, 64, 175, 0.25);
        }

        .btn-filter {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-filter i {
            margin-right: 8px;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .table th {
            background-color: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 15px 12px;
        }

        .table td {
            padding: 15px 12px;
            vertical-align: middle;
            border-color: #f1f3f4;
        }

        .lecture-image {
            border-radius: 8px;
            object-fit: cover;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .stats-number {
            font-size: 1.5rem;
            font-weight: 600;
            display: block;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
        }

        .purchase-btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 25px;
            border: none;
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #333;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .purchase-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
            background: linear-gradient(45deg, #ffed4e, #ffd700);
            color: #333;
        }

        .purchase-btn:active {
            transform: translateY(-1px);
        }

        .free-course-alert {
            background: rgba(40, 167, 69, 0.2) !important;
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: white !important;
            border-radius: 10px;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }

        .purchase-modal .modal-content {
            border-radius: 15px;
            border: none;
        }

        .purchase-modal .modal-header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .price-display {
            font-size: 2rem;
            font-weight: 700;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <!-- Course Header -->
        <div class="course-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-3">
                            <a href="{{ route('dashboard.users.courses') }}" class="back-btn me-3">
                                <i class="fas fa-arrow-right me-2"></i>@lang('l.back_to_courses')
                            </a>
                        </div>
                        <h1 class="mb-2">{{ $course->name }}</h1>
                        <p class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>{{ $course->level->name ?? '-' }}
                            @if($course->price && $course->price > 0)
                                <span class="ms-3"><i class="fas fa-tag me-2"></i>{{ $course->price }} @lang('l.currency')</span>
                            @else
                                <span class="ms-3 text-success"><i class="fas fa-gift me-2"></i>@lang('l.Free')</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="stats-card">
                                    <span class="stats-number">{{ $course->lectures->count() }}</span>
                                    <div class="stats-label">@lang('l.lectures')</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card">
                                    <span class="stats-number">{{ $course->lectures->sum(function($lecture) { return $lecture->assignments->count(); }) }}</span>
                                    <div class="stats-label">@lang('l.assignments')</div>
                                </div>
                            </div>
                        </div>

                        <!-- Purchase Button -->
                        @if($course->price && $course->price > 0 && !auth()->user()->hasPurchasedCourseLectures($course->id))
                            <div class="mt-3 text-center">
                                <button type="button" class="btn btn-warning btn-lg purchase-btn w-100" onclick="purchaseCourse('{{ encrypt($course->id) }}')">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    @lang('l.purchase_course')
                                    <br>
                                </button>
                            </div>
                        @elseif($course->price && $course->price > 0 && auth()->user()->hasPurchasedCourseLectures($course->id))
                            <div class="mt-3 text-center">
                                <div class="alert alert-success mb-0 purchased-course-alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    @lang('l.already_purchased')
                                </div>
                            </div>
                        @else
                            <div class="mt-3 text-center">
                                <div class="alert alert-info mb-0 free-course-alert">
                                    <i class="fas fa-gift me-2"></i>
                                    @lang('l.free_course_enjoy')
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <h6>
                <i class="fas fa-filter me-2"></i>@lang('l.filter_lectures')
            </h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label small text-muted">@lang('l.lecture_type')</label>
                    <select class="form-select" id="filter_type">
                        <option value="">@lang('l.all_types')</option>
                        <option value="free">@lang('l.Free')</option>
                        <option value="price">@lang('l.Paid')</option>
                        <option value="month">@lang('l.Monthly')</option>
                        <option value="course">@lang('l.Course')</option>
                    </select>
                </div>
                {{-- <div class="col-md-3 mb-3">
                    <label class="form-label small text-muted">@lang('l.price_range')</label>
                    <select class="form-select" id="filter_price">
                        <option value="">@lang('l.all_prices')</option>
                        <option value="free">@lang('l.Free')</option>
                        <option value="paid">@lang('l.paid_lectures')</option>
                    </select>
                </div> --}}
                <div class="col-md-4 mb-3">
                    <label class="form-label small text-muted">@lang('l.from_date')</label>
                    <input type="date" class="form-control" id="filter_date_from">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small text-muted">@lang('l.to_date')</label>
                    <input type="date" class="form-control" id="filter_date_to">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary btn-filter d-none" id="apply_filters">
                        <i class="fas fa-filter"></i>@lang('l.apply_filters')
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-filter" id="clear_filters">
                        <i class="fas fa-times"></i>@lang('l.clear_filters')
                    </button>
                </div>
            </div>
        </div>

        <!-- Lectures Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover" id="lecturesTable">
                    <thead class="table-dark">
                        <tr>
                            <th style="color: white;">#</th>
                            <th style="color: white;">@lang('l.image')</th>
                            <th style="color: white;">@lang('l.lecture_name')</th>
                            <th style="color: white;">@lang('l.Lecture Type')</th>
                            <th style="color: white;">@lang('l.Price')</th>
                            <th style="color: white;">@lang('l.assignments')</th>
                            <th style="color: white;">@lang('l.created_at')</th>
                            <th style="color: white;">@lang('l.Action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Purchase Modal -->
    <div class="modal fade purchase-modal" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseModalLabel">
                        <i class="fas fa-shopping-cart me-2"></i>@lang('l.purchase_course')
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="course-info-purchase">
                            <h4>{{ $course->name }}</h4>
                            <p class="text-muted">{{ $course->level->name ?? '-' }}</p>

                            @if($course->price && $course->price > 0)
                                <div class="price-display">
                                    {{ $course->price }} @lang('l.currency')
                                </div>
                            @endif

                            <div class="course-benefits mt-4">
                                <h6>@lang('l.what_you_get'):</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>{{ $course->lectures->count() }} @lang('l.lectures')</li>
                                    <li><i class="fas fa-check text-success me-2"></i>{{ $course->lectures->sum(function($lecture) { return $lecture->assignments->count(); }) }} @lang('l.assignments')</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('l.Cancel')</button>
                    <a type="button" class="btn btn-success btn-lg" href="{{route('dashboard.users.courses-purchase')}}?id={{ encrypt($course->id) }}">
                        <i class="fas fa-credit-card me-2"></i>@lang('l.proceed_to_payment')
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            var table = $('#lecturesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard.users.courses-lectures') }}?id={{ encrypt($course->id) }}",
                    data: function (d) {
                        d.type = $('#filter_type').val();
                        d.price_range = $('#filter_price').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'image', name: 'image', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'type', name: 'type'},
                    {data: 'price', name: 'price'},
                    {data: 'assignments_count', name: 'assignments_count'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[6, 'desc']],
                language: {
                    url: "{{ asset('assets/back/js/datatables-ar.json') }}"
                },
                pageLength: 10,
                responsive: false
            });

            // Apply filters
            $('#apply_filters').click(function() {
                updateFiltersStatus();
                table.draw();
            });

            // Clear filters
            $('#clear_filters').click(function() {
                $('#filter_type').val('');
                $('#filter_price').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');
                updateFiltersStatus();
                table.draw();
            });

            // Update filters status
            function updateFiltersStatus() {
                var hasActiveFilters = $('#filter_type').val() || $('#filter_price').val() ||
                                     $('#filter_date_from').val() || $('#filter_date_to').val();

                if (hasActiveFilters) {
                    $('#apply_filters').removeClass('btn-primary').addClass('btn-success');
                    $('#apply_filters i').removeClass('fa-filter').addClass('fa-check');
                } else {
                    $('#apply_filters').removeClass('btn-success').addClass('btn-primary');
                    $('#apply_filters i').removeClass('fa-check').addClass('fa-filter');
                }
            }

            // Auto apply filters on change
            $('#filter_type, #filter_price, #filter_date_from, #filter_date_to').change(function() {
                updateFiltersStatus();
                table.draw();
            });

            // Initialize filters status
            updateFiltersStatus();
        });

        function purchaseCourse(courseId) {
            // التوجه مباشرة لصفحة شراء الكورس
            window.location.href = "{{ route('dashboard.users.courses-purchase') }}?course_id=" + courseId;
        }

        function confirmPurchase() {
            // يمكنك هنا إضافة منطق الدفع الفعلي
            // مثل التوجيه لبوابة الدفع أو إرسال طلب AJAX

            Swal.fire({
                title: '@lang("l.confirm_purchase")',
                text: '@lang("l.purchase_confirmation_text")',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: '@lang("l.yes_purchase")',
                cancelButtonText: '@lang("l.cancel")'
            }).then((result) => {
                if (result.isConfirmed) {
                    // إرسال طلب الشراء
                    processPurchase();
                }
            });
        }

        function processPurchase() {
            // إضافة منطق معالجة الشراء هنا
            Swal.fire({
                title: '@lang("l.processing")',
                text: '@lang("l.please_wait")',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // محاكاة معالجة الدفع
            setTimeout(() => {
                Swal.fire({
                    title: '@lang("l.success")',
                    text: '@lang("l.purchase_successful")',
                    icon: 'success',
                    confirmButtonText: '@lang("l.ok")'
                }).then(() => {
                    // إعادة تحميل الصفحة لإظهار التحديثات
                    location.reload();
                });
            }, 2000);
        }
    </script>
@endsection
