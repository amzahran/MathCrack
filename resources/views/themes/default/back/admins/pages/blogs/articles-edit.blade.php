@extends('themes.default.layouts.back.master')

@section('title')
    {{ Str::title($blog->title) }}
@endsection


@section('content')
    @can('edit blog')
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('l.Edit Article')</h3>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card-body">
                    <form id="editForm" class="row g-3" method="post"
                        action="{{ route('dashboard.admins.blogs.articles-update') }}"
                        enctype="multipart/form-data">
                        @csrf @method('patch')
                        <input type="hidden" name="id" value="{{ encrypt($blog->id) }}">

                        <div class="tab-content">
                            <div class="col-12 mb-2">
                                <label class="form-label" for="meta_keywords">@lang('l.Meta Keywords')<i
                                        class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i></label>
                                <textarea id="meta_keywords" name="meta_keywords" class="form-control">{{ $blog->meta_keywords }}</textarea>
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label">@lang('l.Meta Description')</label>
                                <textarea name="meta_description" class="form-control"
                                    placeholder="@lang('l.Enter a meta description')">{{ $blog->meta_description }}</textarea>
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label">@lang('l.Title')<i
                                        class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i></label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ $blog->getTranslation('title', $defaultLanguage->code) }}"
                                    placeholder="@lang('l.Enter a title')" required />
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label">@lang('l.Category')</label>
                                <select name="blog_category_id" class="form-select select2">
                                    <option value="">@lang('l.No category')</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $blog->blog_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label">@lang('l.Content')<i
                                        class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i></label>
                                <div id="content" style="height: 300px; overflow-y: auto;">
                                    {!! $blog->getTranslation('content', $defaultLanguage->code) ?? '' !!}
                                </div>
                                <input type="hidden" name="content" id="content-input">
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label">@lang('l.Views')</label>
                                <input type="number" name="views" class="form-control" value="{{ $blog->views }}"
                                    placeholder="@lang('l.Enter views')" required />
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label">@lang('l.Change Image')</label>
                                <div class="theme-upload-area" id="image-upload-area">
                                    <input type="file" id="image" name="image" class="theme-upload-input" accept="image/*">
                                    <label for="image" class="theme-upload-label">
                                        <div class="upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <div class="upload-text">
                                            <h5>@lang('l.Drop image here or click to upload')</h5>
                                            <p class="text-muted">@lang('l.Supported formats: JPG, PNG, GIF, WebP')</p>
                                        </div>
                                    </label>
                                    <div class="upload-preview d-none">
                                        <div class="preview-content">
                                            <img src="" alt="" class="preview-image" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                            <span class="filename"></span>
                                        </div>
                                        <button type="button" class="btn-remove-file">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="current-image mt-3">
                                        <p class="text-muted mb-2">@lang('l.Current Image'):</p>
                                        <img src="{{ asset($blog->image) }}" alt="@lang('l.Current Image')" class="img-fluid" style="max-width: 200px; max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label">@lang('l.Status')
                                    <i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-dark" data-bs-original-title="@lang('l.Published'): @lang('l.The article will be available to view for visitors.') @lang('l.Draft'): @lang('l.The article will be hidden from visitors and saved as a draft.')"></i>
                                </label>
                                <select name="status" class="form-select select2">
                                    <option value="published" {{ $blog->status == 'published' ? 'selected' : '' }}>
                                        @lang('l.Published')</option>
                                    <option value="draft" {{ $blog->status == 'draft' ? 'selected' : '' }}>@lang('l.Draft')
                                    </option>
                                </select>
                            </div>

                            <div class="col-12 text-center mt-4">
                                <a href="{{ route('dashboard.admins.blogs.articles') }}"
                                    class="btn btn-label-secondary">@lang('l.Cancel')</a>
                                <button type="submit" class="btn btn-primary">@lang('l.Update')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    <!-- / Content -->
@endsection


@section('js')
    <script>
        const fullEditor = new Quill('#content', {
            bounds: '#content',
            modules: {
                formula: true,
                toolbar: fullToolbar
            },
            placeholder: '@lang('l.Write content here')...',
            theme: 'snow',
            height: '300px'
        });

        // حفظ المحتوى في الحقل المخفي عند التقديم
        document.getElementById('editForm').addEventListener('submit', function() {
            document.getElementById('content-input').value = fullEditor.root.innerHTML;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            const uploadArea = document.getElementById('image-upload-area');
            const uploadPreview = uploadArea.querySelector('.upload-preview');
            const previewImage = uploadArea.querySelector('.preview-image');
            const filename = uploadArea.querySelector('.filename');
            const uploadLabel = uploadArea.querySelector('.theme-upload-label');
            const removeButton = uploadArea.querySelector('.btn-remove-file');
            const currentImage = uploadArea.querySelector('.current-image');

            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        imageInput.files = files;
                        showImagePreview(file);
                    } else {
                        alert('@lang("l.Please upload an image file")');
                    }
                }
            });

            function showImagePreview(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    filename.textContent = file.name;
                    uploadPreview.classList.remove('d-none');
                    uploadLabel.classList.add('d-none');
                    currentImage.style.opacity = '0.5';
                }
                reader.readAsDataURL(file);
            }

            imageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    showImagePreview(this.files[0]);
                }
            });

            removeButton.addEventListener('click', function() {
                imageInput.value = '';
                uploadPreview.classList.add('d-none');
                uploadLabel.classList.remove('d-none');
                previewImage.src = '';
                filename.textContent = '';
                currentImage.style.opacity = '1';
            });
        });
    </script>
@endsection

<style>
.theme-upload-area {
    border: 2px dashed var(--bs-primary);
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
}

.theme-upload-input {
    display: none;
}

.theme-upload-label {
    cursor: pointer;
    display: block;
    padding: 20px;
}

.upload-icon {
    font-size: 3rem;
    color: var(--bs-primary);
    margin-bottom: 15px;
}

.upload-text h5 {
    margin-bottom: 10px;
    color: var(--bs-primary);
}

.theme-upload-area:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

.theme-upload-area.dragover {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    border-style: solid;
}

.upload-preview {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.preview-content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-remove-file {
    border: none;
    background: none;
    color: #dc3545;
    cursor: pointer;
    padding: 5px;
    transition: transform 0.2s ease;
}

.btn-remove-file:hover {
    transform: scale(1.1);
}

.current-image {
    border-top: 1px solid #dee2e6;
    padding-top: 15px;
}

.current-image img {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* إضافة تنسيقات لمحرر Quill */
.ql-container {
    height: 250px !important;
    overflow-y: auto !important;
}

.ql-toolbar {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: var(--bs-card-bg);
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}
</style>
