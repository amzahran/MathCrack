@extends('themes.default.layouts.back.master')

@section('title')
    {{ __('l.Translate Page') }} - {{ __('l.' . $page->title) }}
@endsection

@section('css')
<style>
    .nav-tabs .nav-link {
        position: relative;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #696cff;
    }
    .nav-tabs .nav-link:hover:not(.active) {
        background-color: rgba(105, 108, 255, 0.05);
        border-color: transparent;
    }
    .back-btn {
        transition: all 0.3s ease;
    }
    .back-btn:hover {
        transform: translateX(-5px);
    }
    .textarea-counter {
        position: absolute;
        bottom: 10px;
        right: 10px;
        font-size: 0.75rem;
        color: #697a8d;
        background: rgba(255,255,255,0.9);
        padding: 0 5px;
        border-radius: 4px;
    }
    .textarea-wrapper {
        position: relative;
    }
    .auto-translate-btn {
        transition: all 0.3s ease;
    }
    .auto-translate-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(33, 37, 41, 0.25);
    }
    .editor-container {
        border: 1px solid #ddd;
        border-radius: 0.375rem;
        margin-bottom: 20px;
    }
    .tox-tinymce {
        border-radius: 0.375rem !important;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard.admins.pages', ['page' => $page->title]) }}" class="btn btn-icon btn-outline-primary back-btn me-3">
                <i class="bx bx-arrow-back"></i>
            </a>
            <h4 class="fw-bold py-3 mb-0">
                <i class="bx bx-globe text-primary me-1"></i>
                {{ __('l.Translate Page') }} - {{ __('l.' . $page->title) }}
            </h4>
        </div>

        <a href="{{ route('dashboard.admins.pages-auto-translate', ['id' => encrypt($page->id)]) }}" class="btn btn-dark auto-translate-btn">
            <i class="bx bx-bulb me-1"></i> {{ __('l.Auto Translate') }}
        </a>
    </div>

    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bx bx-info-circle me-1"></i>
        {{ __('l.Please note that automatic translation for large content is not efficient and may take some time, so we do not recommend using it for large content') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
    
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">{{ __('l.Translate Page Content') }}</h5>
        </div>

        <div class="card-body">
            <form id="translateForm" method="post" action="{{ route('dashboard.admins.pages-translate') }}">
                @csrf @method('PATCH')
                <input type="hidden" name="id" value="{{ encrypt($page->id) }}">

                <ul class="nav nav-tabs mb-4" role="tablist">
                    @foreach ($languages as $language)
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link d-flex align-items-center {{ $loop->first ? 'active' : '' }}"
                                role="tab" data-bs-toggle="tab" data-bs-target="#lang-{{ $language->code }}"
                                aria-controls="lang-{{ $language->code }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <i class="fi fi-{{ strtolower($language->flag) }} me-2"></i>
                                {{ $language->native }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach ($languages as $language)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="lang-{{ $language->code }}" role="tabpanel">

                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label d-flex align-items-center">
                                        <i class="bx bx-file-blank text-primary me-2"></i>
                                        {{ __('l.Page Content') }} ({{ $language->native }})
                                        <span class="text-danger ms-1">*</span>
                                    </label>

                                    @if ($language->code === $defaultLanguage)
                                        <div class="alert alert-info mb-3">
                                            <i class="bx bx-info-circle me-1"></i>
                                            {{ __('l.This is the default language. You can edit it on the edit page.') }}
                                        </div>
                                        <div class="p-3 bg-light rounded mb-4">
                                            {!! $page->getTranslation('content', $language->code) !!}
                                        </div>
                                    @else
                                        <div class="editor-container" id="editor-wrapper-{{ $language->code }}">
                                            <textarea
                                                id="content-editor-{{ $language->code }}"
                                                name="content-{{ $language->code }}"
                                                class="form-control content-editor"
                                                data-language="{{ $language->code }}"
                                                rows="10"
                                                required
                                            >{{ $page->getTranslation('content', $language->code) }}</textarea>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('dashboard.admins.pages', ['page' => $page->title]) }}" class="btn btn-outline-secondary me-2">
                        <i class="bx bx-x me-1"></i> {{ __('l.Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> {{ __('l.Save Translations') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.4.2/tinymce.min.js"></script>
<script>
    $(document).ready(function() {
        // Define dark mode based on user preference or system setting
        const prefersDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const userPrefersDark = localStorage.getItem('darkMode') === 'true';
        const isDarkMode = userPrefersDark || prefersDarkMode;

        // initialize TinyMCE for all editors
        $('.content-editor').each(function() {
            const languageCode = $(this).data('language');
            const isRtl = ['ar', 'he', 'fa', 'ur'].includes(languageCode);

            tinymce.init({
                selector: `#content-editor-${languageCode}`,
                height: 500,
                plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
                menubar: 'file edit view insert format tools table help',
                toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
                toolbar_sticky: true,
                directionality: isRtl ? 'rtl' : 'ltr',
                autosave_ask_before_unload: true,
                autosave_interval: '30s',
                autosave_prefix: '{path}{query}-{id}-',
                autosave_restore_when_empty: false,
                autosave_retention: '2m',
                image_advtab: true,
                importcss_append: true,

                // Base64 inline image handling - no server storage
                paste_data_images: true,
                automatic_uploads: false,
                images_dataimg_filter: function(img) {
                    return true; // Keep all pasted images as data URLs
                },

                // Base64 file picker for images and media
                file_picker_types: 'image media',
                file_picker_callback: function (cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');

                    if (meta.filetype === 'image') {
                        input.setAttribute('accept', 'image/*');
                    }

                    if (meta.filetype === 'media') {
                        input.setAttribute('accept', 'video/*,audio/*');
                    }

                    input.onchange = function () {
                        var file = this.files[0];
                        var reader = new FileReader();

                        reader.onload = function () {
                            var id = 'blobid' + (new Date()).getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);

                            // Insert the blob directly
                            cb(blobInfo.blobUri(), { title: file.name });
                        };

                        reader.readAsDataURL(file);
                    };

                    input.click();
                },

                // Dark mode support
                skin: isDarkMode ? 'oxide-dark' : 'oxide',
                content_css: isDarkMode ? 'dark' : 'default',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
            });
        });

        // Handle tab switching to make sure TinyMCE renders correctly
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const targetTab = $(e.target).attr('data-bs-target');
            const languageCode = targetTab.replace('#lang-', '');

            // Trigger window resize to fix any TinyMCE rendering issues
            setTimeout(function() {
                window.dispatchEvent(new Event('resize'));
            }, 100);
        });

        // Toggle dark mode in TinyMCE when the system or user preference changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            const newMode = e.matches;
            localStorage.setItem('darkMode', newMode);
            location.reload();
        });
    });
</script>
@endsection