@extends('themes.default.layouts.back.master')

@section('title')
    {{ $user->firstname }} {{ $user->lastname }}
@endsection

@section('css')
    <style>
        .error-message {
            color: red;
        }

        .iti {
            width: 100%;
        }

        .iti__country {
            direction: ltr;
        }

        .iti__country-list {
            left: 0;
        }

        #phone {
            text-align: left;
        }

        .iti__selected-flag {
            direction: ltr;
        }

        /* Select2 styles for modal */
        .modal .select2-container {
            width: 100% !important;
        }

        .modal .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .modal .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
        }

        .modal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .modal .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            z-index: 9999;
        }

        .modal .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd;
        }

        .modal .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #e9ecef;
        }

        /* Floating Action Button */
        .floating-action-btn {
            transition: all 0.3s ease;
        }

        .floating-action-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3) !important;
        }

        .floating-action-btn:active {
            transform: scale(0.95);
        }

    </style>
    <!-- تضمين ملفات CSS اللازمة -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
@endsection


@section('content')
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @can('show students')
            <div class="row">
                <div class="col-md-12">
                    <div id="page1">
                        <div class="card mb-4">
                            <h5 class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fa fa-user me-2"></i>@lang('l.Profile Details')
                                </div>
                                @can('add invoices')
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                                        <i class="fa fa-plus me-1"></i>@lang('l.Add Invoice')
                                    </button>
                                @endcan
                            </h5>
                            <!-- Account -->
                            <div class="card-body">
                                <form class="d-flex align-items-start align-items-sm-center gap-4" method="post" action="#"
                                    enctype="multipart/form-data"> @csrf
                                    <img src="{{ $user->photo }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded"
                                        id="uploadedAvatar" style="max-width: 100px; margin: 10px;" />
                                </form>
                            </div>
                            <hr class="my-0" />
                            <div class="card-body">
                                <form id="formAccountSettings" method="POST">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label for="firstName" class="form-label">@lang('l.First Name')</label>
                                            <input class="form-control" type="text" id="firstName" name="firstname"
                                                value="{{ $user->firstname }}" autofocus readonly />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="lastName" class="form-label">@lang('l.Last Name')</label>
                                            <input class="form-control" type="text" name="lastname" id="lastName"
                                                value="{{ $user->lastname }}" readonly />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="email" class="form-label">@lang('l.E-mail')</label>
                                            <input class="form-control" type="text" id="email" name="email"
                                                value="{{ $user->email }}" readonly />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="phone">@lang('l.Phone Number')</label><br>
                                            <input type="tel" id="phone" name="phone" value="{{ $user->phone }}"
                                                class="form-control" readonly>
                                            <input type="hidden" id="phone_code" name="phone_code" required>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="country">@lang('l.Country')</label>
                                            <select id="country" class="select2 form-control" name="country" readonly>
                                                <option value="">{{ $user->country }}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="city" class="form-label">@lang('l.City')</label>
                                            <input type="text" class="form-control" id="city" name="city"
                                                value="{{ $user->city }}" readonly/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="address" class="form-label">@lang('l.Address')</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                value="{{ $user->address }}" readonly/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="level_id">@lang('l.Level')</label>
                                            <input type="text" class="form-control" id="level_id" name="level_id"
                                                value="{{ $user->level ? $user->level->name : __('l.No Level') }}" readonly/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="state" class="form-label">@lang('l.State')</label>
                                            <input class="form-control" type="text" id="state" name="state"
                                                value="{{ $user->state }}" readonly/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="zipCode" class="form-label">@lang('l.Zip Code')</label>
                                            <input type="text" class="form-control" id="zipCode" name="zip_code"
                                                value="{{ $user->zip_code }}" maxlength="8"readonly />
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">@lang('l.Back')</a>
                                    </div>
                                </form>
                            </div>
                            <!-- /Account -->
                        </div>

                        <!-- Student Invoices Section -->
                        @can('show invoices')
                        <div class="card mb-4">
                            <h5 class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fa fa-file-invoice me-2"></i>@lang('l.Student Invoices')
                                </div>
                                <div class="d-flex gap-2">
                                    @can('add invoices')
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                                            <i class="fa fa-plus me-1"></i>@lang('l.Add Invoice')
                                        </button>
                                    @endcan
                                    <a href="{{ route('dashboard.admins.invoices') }}?student_id={{ $user->id }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-external-link me-1"></i>@lang('l.View All Invoices')
                                    </a>
                                </div>
                            </h5>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="studentInvoicesTable" class="table table-striped table-bordered dt-responsive nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('l.Course')</th>
                                                <th>@lang('l.Category')</th>
                                                <th>@lang('l.Type')</th>
                                                <th>@lang('l.Item')</th>
                                                <th>@lang('l.Amount')</th>
                                                <th>@lang('l.Status')</th>
                                                <th>@lang('l.Created At')</th>
                                                <th>@lang('l.Actions')</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <!-- Floating Action Button for Add Invoice -->
    @can('add invoices')
        <div class="position-fixed" style="bottom: 30px; right: 30px; z-index: 1000;">
            <button type="button" class="btn btn-primary btn-lg rounded-circle shadow-lg floating-action-btn" data-bs-toggle="modal" data-bs-target="#addInvoiceModal" title="@lang('l.Add Invoice')">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    @endcan

    <!-- Add Invoice Modal -->
    @can('add invoices')
        <div class="modal fade" id="addInvoiceModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @lang('l.Add Invoice for') {{ $user->firstname }} {{ $user->lastname }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('dashboard.admins.invoices-store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="from_student_page" value="1">
                        <div class="modal-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">
                                        @lang('l.Category') <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">@lang('l.Select Category')</option>
                                        <option value="quiz" {{ old('category') == 'quiz' ? 'selected' : '' }}>@lang('Test')</option>
                                        <option value="lecture" {{ old('category') == 'lecture' ? 'selected' : '' }}>@lang('Lecture')</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">
                                        @lang('l.Type') <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">@lang('l.Select Type')</option>
                                        <option value="single" {{ old('type') == 'single' ? 'selected' : '' }}>@lang('l.Single')</option>

                                        <option value="course" {{ old('type') == 'course' ? 'selected' : '' }}>@lang('l.Course')</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="course_selection_wrapper">
                                    <label for="course_id" class="form-label">
                                        @lang('l.Course') <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value="">@lang('l.Select Course')</option>
                                        @foreach(\App\Models\Course::all() as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="type_value" class="form-label">
                                        @lang('l.Item') <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select select2-ajax" id="type_value" name="type_value" required>
                                        <option value="">@lang('l.Select type and category first...')</option>
                                        @if(old('type_value'))
                                            <option value="{{ old('type_value') }}" selected>{{ old('type_value') }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label">
                                        @lang('l.Amount') <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                @lang('l.Close')
                            </button>
                            <button type="submit" class="btn btn-primary">
                                @lang('l.Save')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection


@section('js')
    <!-- تضمين ملفات JS اللازمة -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

    <!-- تهيئة الحقل -->
    <script>
        $(document).ready(function() {
            // إنشاء حقل إدخال رقم الهاتف بشكل دولي
            var input = document.querySelector("#phone");
            var iti = window.intlTelInput(input, {
                initialCountry: "gb",
                geoIpLookup: function(callback) {
                    $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                        var countryCode = (resp && resp.country) ? resp.country : "";
                        callback(countryCode);
                    });
                },
                nationalMode: false,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                separateDialCode: true,
                formatOnDisplay: true,
                preferredCountries: ["us", "ca", "gb"], // يمكن تعديل الدول المفضلة هنا
            });

            // تحديث حقل الخفي "phone_code" بشكل تلقائي عند فتح الصفحة
            var phone_code = document.querySelector("#phone_code");
            var currentDialCode = iti.getSelectedCountryData().dialCode;
            phone_code.value = currentDialCode;

            // تحديث حقل الخفي "phone_code" بشكل تلقائي عند تغيير الكود الدولي فقط
            input.addEventListener("countrychange", function() {
                var currentDialCode = iti.getSelectedCountryData().dialCode;
                phone_code.value = currentDialCode;
            });

            // إعادة فتح المودل في حالة وجود أخطاء
            @if($errors->any())
                $('#addInvoiceModal').modal('show');
            @endif

            // فتح المودل تلقائياً إذا كان هناك #addInvoiceModal في الرابط
            if (window.location.hash === '#addInvoiceModal') {
                $('#addInvoiceModal').modal('show');
            }

            // Initialize Select2 for items with AJAX
            function initializeTypeValueSelect() {
                // Destroy existing Select2 if it exists
                if ($('#type_value').hasClass('select2-hidden-accessible')) {
                    $('#type_value').select2('destroy');
                }

                // Clear the select element
                $('#type_value').empty().append('<option value="">@lang('l.Select type and category first...')</option>');

                // Initialize new Select2
                $('#type_value').select2({
                    placeholder: '@lang('l.Select type and category first...')',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#addInvoiceModal'),
                    ajax: {
                        url: '{{ route('dashboard.admins.invoices-get-items') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                type: $('#type').val(),
                                category: $('#category').val(),
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0
                });
            }

            // Initialize Select2 when modal is shown
            $('#addInvoiceModal').on('shown.bs.modal', function() {
                setTimeout(function() {
                    initializeTypeValueSelect();
                }, 200);
            });

            // Reset form when modal is hidden
            $('#addInvoiceModal').on('hidden.bs.modal', function() {
                $('#addInvoiceModal form')[0].reset();
                if ($('#type_value').hasClass('select2-hidden-accessible')) {
                    $('#type_value').select2('destroy');
                }
                $('#type_value').empty().append('<option value="">@lang('l.Select type and category first...')</option>');
            });

                        // Function to update type_value select
            function updateTypeValueSelect() {
                var type = $('#type').val();
                var category = $('#category').val();
                var courseId = $('#course_id').val();

                // Clear current selection
                $('#type_value').empty().append('<option value="">@lang('l.Select Item')</option>');

                // للنوع الفردي، نحتاج اختيار الكورس أولاً
                if (type === 'single' && !courseId) {
                    $('#type_value').empty().append('<option value="">يرجى اختيار الكورس أولاً...</option>');
                    return;
                }

                // للنوع course، نستخدم course_id كـ type_value
                if (type === 'course' && courseId) {
                    $('#type_value').empty().append(`<option value="${courseId}" selected>${$('#course_id option:selected').text()}</option>`);
                    return;
                }

                if (type && category) {
                    // Destroy and recreate select2
                    if ($('#type_value').hasClass('select2-hidden-accessible')) {
                        $('#type_value').select2('destroy');
                    }

                    $('#type_value').select2({
                        placeholder: '@lang('l.Select Item')',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#addInvoiceModal'),
                        ajax: {
                            url: '{{ route('dashboard.admins.invoices-get-items') }}',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    type: type,
                                    category: category,
                                    course_id: $('#course_id').val(),
                                    search: params.term
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 0
                    });
                }
            }

                        // Update items when type, category, or course changes
            $(document).on('change', '#type, #category, #course_id', function() {
                updateTypeValueSelect();
            });

            // Initialize type_value select if type and category are already selected (for old input)
            if ($('#type').val() && $('#category').val()) {
                updateTypeValueSelect();
            }

            // Alternative way to initialize Select2 when modal is ready
            $('#addInvoiceModal').on('show.bs.modal', function() {
                // Ensure the modal is fully loaded before initializing Select2
                setTimeout(function() {
                    if (!$('#type_value').hasClass('select2-hidden-accessible')) {
                        initializeTypeValueSelect();
                    }
                }, 300);
            });

            // Force reinitialize Select2 when modal is fully shown
            $('#addInvoiceModal').on('shown.bs.modal', function() {
                // Double check if Select2 is working
                if (!$('#type_value').hasClass('select2-hidden-accessible')) {
                    console.log('Reinitializing Select2...');
                    initializeTypeValueSelect();
                }
            });

            // Manual trigger for Select2 initialization
            $(document).on('click', '[data-bs-target="#addInvoiceModal"]', function() {
                setTimeout(function() {
                    initializeTypeValueSelect();
                }, 500);
            });

            // Student Invoices DataTable
            @can('show invoices')
            var studentInvoicesTable = $('#studentInvoicesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('dashboard.admins.invoices') }}',
                    data: function(d) {
                        d.student_id = '{{ $user->id }}';
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'course_name',
                    name: 'course_name'
                }, {
                    data: 'category_badge',
                    name: 'category'
                }, {
                    data: 'type_badge',
                    name: 'type'
                }, {
                    data: 'type_value_display',
                    name: 'type_value'
                }, {
                    data: 'amount_formatted',
                    name: 'amount'
                }, {
                    data: 'status_badge',
                    name: 'status'
                }, {
                    data: 'created_at',
                    name: 'created_at'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                order: [
                    [7, 'desc']
                ]
            });
            @endcan
        });
    </script>
@endsection