@extends('themes.default.layouts.back.master')

@section('title')
    {{ __('l.Edit Page') }} - {{ __('l.' . $page->title) }}
@endsection

@section('css')
<style>
    .page-card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .page-card .card-header {
        background-color: #696cff;
        padding: 1.5rem;
        color: white;
    }
    .page-form {
        padding: 1.5rem;
    }
    .back-btn {
        transition: all 0.3s ease;
    }
    .back-btn:hover {
        transform: translateX(-5px);
    }
    .form-label {
        font-weight: 500;
    }
    .editor-container {
        border: 1px solid #ddd;
        border-radius: 0.375rem;
    }
    .tox-tinymce {
        border-radius: 0.375rem !important;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard.admins.pages', ['page' => $page->title]) }}" class="btn btn-icon btn-outline-primary back-btn me-3">
                    <i class="bx bx-arrow-back"></i>
                </a>
                <h4 class="fw-bold py-3 mb-0">
                    <i class="bx bx-edit-alt text-primary me-1"></i>
                    {{ __('l.Edit Page') }} - {{ __('l.' . $page->title) }}
                </h4>
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

    <div class="row">
        <div class="col-12">
            <div class="card page-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="bx bx-file-blank me-2 fs-4"></i>
                        {{ __('l.Page Content') }}
                        <i class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i>
                    </h5>

                    <a href="{{ route('dashboard.admins.pages-get-translations')}}?id={{ encrypt($page->id) }}" class="btn btn-dark">
                        <i class="bx bx-globe me-1"></i> {{ __('l.Manage Translations') }}
                    </a>
                </div>

                <form action="{{ route('dashboard.admins.pages-update') }}" method="POST" class="page-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" value="{{ encrypt($page->id) }}">

                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="editor-container">
                                <textarea id="content-editor" name="content" class="form-control">{{ $page->getTranslation('content', $defaultLanguage->code) }}</textarea>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end align-items-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> {{ __('l.Save Changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
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

        tinymce.init({
            selector: '#content-editor',
            height: 500,
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            toolbar_sticky: true,
            directionality: '{{ app()->getLocale() == "ar" ? "rtl" : "ltr" }}',
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

            templates: [
                { title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
                { title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
                { title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
            ],
            template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
            template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_class: 'mceNonEditable',
            toolbar_mode: 'sliding',
            contextmenu: 'link image table',

            // Dark mode support
            skin: isDarkMode ? 'oxide-dark' : 'oxide',
            content_css: isDarkMode ? 'dark' : 'default',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
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