@extends('themes.default.layouts.back.master')

@section('title')
    {{ __('l.Frequently Asked Questions') }}
@endsection

@section('css')
<style>
    .question-card {
        border-left: 4px solid #696cff;
        transition: all 0.3s ease;
    }
    .question-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    .question-header {
        cursor: pointer;
        padding: 1rem;
    }
    .question-content {
        border-top: 1px solid rgba(0,0,0,.1);
        padding: 1rem;
        background-color: rgba(105, 108, 255, 0.05);
    }
    .question-actions {
        padding: 0.5rem 1rem;
        border-top: 1px solid rgba(0,0,0,.1);
    }
    .add-question-btn {
        transition: all 0.3s ease;
    }
    .add-question-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(105, 108, 255, 0.4);
    }
    .expand-icon {
        transition: transform 0.3s ease;
    }
    .collapsed .expand-icon {
        transform: rotate(-90deg);
    }
    .question-number {
        display: inline-flex;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: #696cff;
        color: white;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    [dir="ltr"] .question-number {
        margin-right: 10px;
    }
    [dir="rtl"] .question-number {
        margin-left: 10px;
    }
    .lang-flag {
        width: 20px;
        height: 15px;
        margin-right: 5px;
        border-radius: 2px;
    }
    .action-buttons .btn {
        margin-left: 5px;
        transition: all 0.2s;
    }
    .action-buttons .btn:hover {
        transform: translateY(-2px);
    }
    .delete-selected {
        transition: all 0.3s;
    }
    .delete-selected:hover {
        background-color: #e05260;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="fw-bold py-3 mb-0">
                    <i class="bx bx-help-circle text-primary me-1"></i>
                    {{ __('l.Frequently Asked Questions') }}
                </h4>

                <div class="action-buttons">
                    @can('delete questions')
                    <button id="deleteSelected" class="btn btn-outline-danger delete-selected d-none">
                        <i class="bx bx-trash me-1"></i> {{ __('l.Delete Selected') }}
                    </button>
                    @endcan

                    @can('add questions')
                    <button type="button" class="btn btn-primary add-question-btn" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="bx bx-plus-circle me-1"></i> {{ __('l.Add New Question') }}
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

    </div>

    <div class="row">
        <div class="col-12">
            @forelse($questions as $index => $question)
            <div class="card mb-3 question-card">
                <div class="card-header question-header p-0" data-bs-toggle="collapse" data-bs-target="#question{{ $question->id }}" aria-expanded="false">
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div class="d-flex align-items-center">
                            <span class="question-number">{{ $index + 1 }}</span>
                            <span class="fw-semibold">{{ is_array($question->question) ? ($question->question[$defaultLanguage] ?? reset($question->question)) : $question->question }}</span>
                            <div class="ms-2">
                                @foreach(is_array($question->question) ? array_keys($question->question) : [] as $langCode)
                                    <i class="fi fi-{{ strtolower($langCode) }} fis"></i>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="form-check me-3">
                                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $question->id }}" id="check{{ $question->id }}">
                            </div>
                            <i class="bx bx-chevron-down fs-4 expand-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="collapse" id="question{{ $question->id }}">
                    <div class="question-content">
                        <p class="mb-0">{{ is_array($question->answer) ? ($question->answer[$defaultLanguage] ?? reset($question->answer)) : $question->answer }}</p>
                    </div>

                    <div class="question-actions d-flex justify-content-end">
                        @can('edit questions')
                        <a href="{{ route('dashboard.admins.questions-get-translations', ['id' => encrypt($question->id)]) }}" class="btn btn-sm btn-dark me-2">
                            <i class="bx bx-globe me-1"></i> {{ __('l.Translations') }}
                        </a>

                        <a href="{{ route('dashboard.admins.questions-edit', ['id' => encrypt($question->id)]) }}" class="btn btn-sm btn-outline-primary me-2">
                            <i class="bx bx-edit me-1"></i> {{ __('l.Edit') }}
                        </a>
                        @endcan

                        @can('delete questions')
                        <button type="button" class="btn btn-sm btn-outline-danger delete-question" data-id="{{ encrypt($question->id) }}">
                            <i class="bx bx-trash me-1"></i> {{ __('l.Delete') }}
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" alt="لا توجد أسئلة" class="mb-3" style="max-width: 200px;">
                    <h5>{{ __('l.No Questions Added Yet') }}</h5>
                    <p class="text-muted">{{ __('l.Start by adding your first question') }}</p>
                    @can('add questions')
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="bx bx-plus-circle me-1"></i> {{ __('l.Add First Question') }}
                    </button>
                    @endcan
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Question Modal -->
@can('add questions')
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('l.Add New Question') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.admins.questions-store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label d-flex align-items-center">
                                {{ __('l.Question') }} <span class="text-danger mx-1">*</span>
                                <i class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i>
                            </label>
                            <textarea class="form-control" name="question" rows="3" required placeholder="{{ __('l.Enter your question here') }}"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label d-flex align-items-center">
                                {{ __('l.Answer') }} <span class="text-danger mx-1">*</span>
                                <i class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i>
                            </label>
                            <textarea class="form-control" name="answer" rows="5" required placeholder="{{ __('l.Enter the answer here') }}"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input type="hidden" name="auto_translate" value="0">
                                <input class="form-check-input" type="checkbox" id="auto_translate" name="auto_translate" value="1">
                                <label class="form-check-label" for="auto_translate">{{ __('l.Auto Translate') }}</label>
                            </div>
                            <small class="text-muted">{{ __('l.Please note that automatic translation for large content is not efficient and may take some time, so we do not recommend using it for large content') }}</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('l.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('l.Save Question') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // تبديل الأيقونة عند فتح وإغلاق التفاصيل
        $('.question-header').on('click', function() {
            $(this).toggleClass('collapsed');
        });

        // حدث تحديد/إلغاء تحديد الكل
        $('#select-all').on('change', function() {
            $('.row-checkbox').prop('checked', $(this).prop('checked'));
            updateDeleteButton();
        });

        // تحديث حالة زر الحذف عند تغيير أي صندوق تحديد
        $(document).on('change', '.row-checkbox', function() {
            updateDeleteButton();
        });

        function updateDeleteButton() {
            let checkedCount = $('.row-checkbox:checked').length;
            if (checkedCount > 0) {
                $('#deleteSelected').removeClass('d-none');
            } else {
                $('#deleteSelected').addClass('d-none');
            }
        }

        // حذف الأسئلة المحددة
        $('#deleteSelected').on('click', function() {
            let selectedIds = $('.row-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedIds.length > 0) {
                Swal.fire({
                    title: "{{ __('l.Are you sure?') }}",
                    text: "{{ __('l.Selected questions will be deleted!') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('l.Yes, delete them!') }}",
                    cancelButtonText: "{{ __('l.Cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('dashboard.admins.questions-deleteSelected') }}?ids=" + selectedIds.join(',');
                    }
                });
            }
        });

        // حذف سؤال
        $('.delete-question').on('click', function() {
            var questionId = $(this).data('id');

            Swal.fire({
                title: "{{ __('l.Are you sure?') }}",
                text: "{{ __('l.You will be delete this forever!') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('l.Yes, delete it!') }}",
                cancelButtonText: "{{ __('l.Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('dashboard.admins.questions-delete') }}?id=" + questionId;
                }
            });
        });
    });
</script>
@endsection
