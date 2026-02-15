@extends('themes.default.layouts.back.master')

@section('title')
    {{ __('l.Translate') }} - {{ $language->name }}
@endsection

@section('css')
<style>
    .translation-table {
        width: 100%;
    }
    .translation-key {
        font-weight: bold;
    }
    .nav-pills .nav-link.active {
        color: #fff;
        background-color: {{$settings['primary_color'] ?? '#007bff'}};
    }
    .nav-pills .nav-link {
        border-radius: 0.375rem;
        color: #566a7f;
    }
    .search-container {
        position: relative;
        margin-bottom: 20px;
    }
    .search-container .fas {
        position: absolute;
        top: 12px;
        left: 12px;
        color: #566a7f;
    }
    #searchTranslation {
        padding-left: 35px;
    }
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }
    .reset-translation {
        width: 40px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .original-value {
        background-color: #f8f9fa;
        border: 1px dashed #dee2e6;
        border-radius: 4px;
        padding: 5px 8px;
        font-size: 0.8rem;
        color: #6c757d;
    }
    .translation-features {
        background-color: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .feature-item {
        display: flex;
        align-items: start;
        margin-bottom: 10px;
    }
    .feature-icon {
        margin-right: 10px;
        margin-top: 2px;
        color: {{$settings['primary_color'] ?? '#007bff'}};
    }
    .empty-translation {
        background-color: #fff8e8;
    }
</style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @can('show settings')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('l.Translate') }} - {{ $language->name }} ({{ $language->native }})</h5>
                    <a href="{{ route('dashboard.admins.languages') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('l.Back to Languages') }}
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <ul class="nav nav-pills mb-4" id="translationType" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $type == 'front' ? 'active' : '' }}" id="frontend-tab" data-bs-toggle="pill"
                                data-bs-target="#frontend" type="button" role="tab" aria-controls="frontend"
                                aria-selected="{{ $type == 'front' ? 'true' : 'false' }}"
                                onclick="window.location.href='{{ route('dashboard.admins.languages-translate') }}?id={{ encrypt($language->id) }}&type=front'">
                                <i class="fas fa-desktop me-1"></i>{{ __('l.Main Website Translations') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $type == 'l' ? 'active' : '' }}" id="backend-tab" data-bs-toggle="pill"
                                data-bs-target="#backend" type="button" role="tab" aria-controls="backend"
                                aria-selected="{{ $type == 'l' ? 'true' : 'false' }}"
                                onclick="window.location.href='{{ route('dashboard.admins.languages-translate') }}?id={{ encrypt($language->id) }}&type=l'">
                                <i class="fas fa-tachometer-alt me-1"></i>{{ __('l.Dashboard Translations') }}
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active">

                            <div class="search-container">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchTranslation" class="form-control" placeholder="{{ __('l.Search translations') }}...">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <span class="badge bg-label-info mb-2">{{ __('l.Total Keys') }}: <span id="totalKeys">{{ count($translations) }}</span></span>
                                    <span class="badge bg-label-primary mb-2">{{ __('l.Showing') }}: <span id="showingKeys">{{ count($translations) }}</span></span>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-warning btn-sm mb-2" id="copyEmptyBtn">
                                        <i class="fas fa-undo me-1"></i>{{ __('l.Reset to Default') }}
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm mb-2" id="showEmptyBtn">
                                        <i class="fas fa-filter me-1"></i>{{ __('l.Show Empty') }}
                                    </button>
                                </div>
                            </div>

                            <form action="{{ route('dashboard.admins.languages-translate-store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ encrypt($language->id) }}">
                                <input type="hidden" name="type" value="{{ $type }}">
                                <input type="hidden" name="reset_all" value="0" id="resetAllField">

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover translation-table">
                                        <thead>
                                            <tr>
                                                <th width="5%">{{ __('l.Key') }}</th>
                                                <th width="45%">{{ __('l.Default Value') }}</th>
                                                <th width="50%">{{ __('l.Translation') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($translations) > 0)
                                                @foreach($translations as $key => $value)
                                                    <tr class="translation-row">
                                                        <td class="translation-key">
                                                            {!! $loop->iteration !!}
                                                            {{-- {!! $key !!} --}}
                                                        </td>
                                                        <td class="translation-default">
                                                            <textarea class="form-control" rows="2" readonly>{!! $originalTranslations[$key] ?? '' !!}</textarea>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <textarea name="translations[{{ $key }}]" rows="2"
                                                                    class="form-control translation-value {{ empty($value) ? 'empty-translation' : '' }}">{!! $value !!}</textarea>
                                                                @if(isset($originalTranslations[$key]) && $originalTranslations[$key] != $value)
                                                                <button type="button" class="btn btn-sm btn-outline-warning reset-translation"
                                                                        data-key="{{ $key }}"
                                                                        data-original="{!! $originalTranslations[$key] !!}"
                                                                        data-bs-toggle="tooltip"
                                                                        title="{{ __('l.Reset to original value') }}">
                                                                    <i class="fas fa-history"></i>
                                                                </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="3" class="text-center">{{ __('l.No translation keys found') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>{{ __('l.Save Translations') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // إضافة حقول مخفية لإعادة ضبط الترجمات
        var resetItems = [];

        // دالة إضافة عنصر إلى قائمة إعادة الضبط
        function addResetItem(key) {
            // التحقق إذا كان المفتاح موجود بالفعل
            if (resetItems.indexOf(key) === -1) {
                resetItems.push(key);
                // إضافة حقل مخفي للنموذج
                $('form').append('<input type="hidden" name="reset_items[]" value="' + key + '">');
            }
        }

        // عند النقر على زر إعادة ضبط الترجمة
        $(document).on('click', '.reset-translation', function() {
            var key = $(this).data('key');
            var originalValue = $(this).data('original');
            var textareaField = $(this).closest('td').find('textarea');

            // استبدال القيمة بالقيمة الأصلية
            textareaField.val(originalValue);

            // إضافة المفتاح إلى قائمة العناصر المعاد ضبطها
            addResetItem(key);

            // إخفاء زر إعادة الضبط بعد النقر عليه
            $(this).fadeOut();
        });

        // البحث في الترجمات
        $('#searchTranslation').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            let rows = $('.translation-row');
            let visibleCount = 0;

            rows.each(function() {
                let key = $(this).find('.translation-key').text().toLowerCase();
                let defaultText = $(this).find('.translation-default').text().toLowerCase();
                let translation = $(this).find('.translation-value').val().toLowerCase();

                if (key.includes(value) || defaultText.includes(value) || translation.includes(value)) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            $('#showingKeys').text(visibleCount);
        });

        // فلترة الحقول الفارغة
        let showingEmpty = false;
        $('#showEmptyBtn').on('click', function() {
            showingEmpty = !showingEmpty;
            let rows = $('.translation-row');
            let visibleCount = 0;

            if (showingEmpty) {
                $(this).html('<i class="fas fa-list me-1"></i>{{ __("l.Show All") }}');

                rows.each(function() {
                    let translationValue = $(this).find('.translation-value').val().trim();
                    if (translationValue === '') {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                $(this).html('<i class="fas fa-filter me-1"></i>{{ __("l.Show Empty") }}');

                rows.each(function() {
                    $(this).show();
                });
                visibleCount = rows.length;
            }

            $('#showingKeys').text(visibleCount);
        });

        // نسخ القيم الفارغة من الترجمة الافتراضية
        $('#copyEmptyBtn').on('click', function() {
            Swal.fire({
                title: '{{ __("l.Reset All Translations") }}',
                text: '{{ __("l.Are you sure you want to reset all translations to their original values?") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __("l.Yes, reset all!") }}',
                cancelButtonText: '{{ __("l.Cancel") }}',
                customClass: {
                    confirmButton: 'btn btn-danger ms-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // تفعيل خيار إعادة ضبط جميع الترجمات
                    $('#resetAllField').val('1');

                    // تقديم النموذج
                    $('form').submit();
                }
            });
        });
    });
</script>
@endsection
