@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Blog Articles')
@endsection


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @can('add blog')
            <div class="card-action-element mb-2" style="text-align: end;">
                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                    data-bs-target="#addTicketModal">
                    <i class="fa fa-plus fa-xs me-1"></i> @lang('l.Add New Article')
                </button>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <!-- Modal -->
            <div class="modal fade" id="addTicketModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content p-3 p-md-5">
                        <div class="modal-header">
                            <h3 class="modal-title text-center">@lang('l.Add New Article')</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addArticleForm" class="row g-3" method="post"
                                action="{{ route('dashboard.admins.blogs.articles-store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="col-12 mb-2">
                                    <label class="form-label" for="meta_keywords">@lang('l.Meta Keywords')<i
                                            class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i></label>
                                    <textarea id="meta_keywords" name="meta_keywords" class="form-control">tag1, tag2, tag3</textarea>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label" for="meta_description">@lang('l.Meta Description')<i
                                            class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i></label>
                                    <textarea id="meta_description" name="meta_description" class="form-control" placeholder="@lang('l.Enter a meta description')"></textarea>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label" for="title">@lang('l.Title')<i
                                            class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i></label>
                                    <input type="text" id="title" name="title" class="form-control"
                                        placeholder="@lang('l.Enter a title')" required />
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label" for="blog_category_id">@lang('l.Category')</label>
                                    <select id="blog_category_id" name="blog_category_id" class="form-select select2">
                                        <option value="">@lang('l.Select a category')</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label" for="content">@lang('l.Content')
                                        <i class="fi fi-{{ strtolower($defaultLanguage->flag) }} fs-8 me-2 ms-2"></i>
                                    </label>
                                    <div id="content"></div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label" for="image">@lang('l.Image')</label>
                                    <div class="theme-upload-area" id="image-upload-area">
                                        <input type="file" id="image" name="image" class="theme-upload-input"
                                            accept="image/*">
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
                                                <img src="" alt="" class="preview-image"
                                                    style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                                <span class="filename"></span>
                                            </div>
                                            <button type="button" class="btn-remove-file">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <label class="form-label" for="status">
                                        @lang('l.Status')
                                        <i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-offset="0,8"
                                            data-bs-placement="top" data-bs-custom-class="tooltip-dark"
                                            data-bs-original-title="@lang('l.Published'): @lang('l.The article will be available to view for visitors.') @lang('l.Draft'): @lang('l.The article will be hidden from visitors and saved as a draft.')"></i>
                                    </label>
                                    <select name="status" class="form-select select2">
                                        <option value="published">@lang('l.Published')</option>
                                        <option value="draft">@lang('l.Draft')</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-check form-switch mb-2">
                                        <input type="hidden" name="auto_translate" value="0">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault"
                                            name="auto_translate" value="1" />
                                        <label class="form-check-label"
                                            for="flexSwitchCheckDefault">@lang('l.Auto Translate')</label>
                                    </div>
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <button type="button" class="btn btn-label-secondary"
                                        data-bs-dismiss="modal">@lang('l.Cancel')</button>
                                    <button type="submit" class="btn btn-primary">@lang('l.Create')</button>
                                </div>

                                <!-- إضافة حقل مخفي للمحتوى -->
                                <input type="hidden" name="content" id="content-input">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        <div class="card" style="padding: 15px;">
            <div class="card-datatable table-responsive">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif
                <div class="mb-3">
                    @can('delete blog')
                        <button id="deleteSelected" class="btn btn-danger d-none">
                            <i class="fa fa-trash ti-xs me-1"></i>@lang('l.Delete Selected')
                        </button>
                    @endcan
                </div>
                <table class="table" id="articles-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all" class="form-check-input">
                            </th>
                            <th>#</th>
                            <th>@lang('l.Title')</th>
                            <th>@lang('l.Category')</th>
                            <th>@lang('l.Views')</th>
                            <th>@lang('l.Author')</th>
                            <th>@lang('l.Status')</th>
                            <th>@lang('l.Created At')</th>
                            <th>@lang('l.Updated At')</th>
                            <th>@lang('l.Action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            // إضافة حقل مخفي للمحتوى
            $('#addTicketForm').append('<input type="hidden" name="content">');

            // معالجة إرسال النموذج
            $('#addTicketForm').on('submit', function() {
                // الحصول على محتوى المحرر
                var content = fullEditor.root.innerHTML;
                // تحديث قيمة الحقل المخفي
                $('input[name="content"]').val(content);
            });

            let table = $('#articles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ request()->fullUrl() }}',
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<input type="checkbox" class="form-check-input row-checkbox" value="${data.id}">`;
                        }
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'title',
                        name: 'title',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'category',
                        name: 'category',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'views',
                        name: 'views',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'author',
                        name: 'author',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [7, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "@lang('l.All')"]
                ],
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rtip',
                language: {
                    search: "@lang('l.Search'):",
                    lengthMenu: "@lang('l.Show') _MENU_ @lang('l.entries')",
                    paginate: {
                        next: '@lang('l.Next')',
                        previous: '@lang('l.Previous')'
                    },
                    info: "@lang('l.Showing') _START_ @lang('l.to') _END_ @lang('l.of') _TOTAL_ @lang('l.entries')",
                    infoEmpty: "@lang('l.Showing') 0 @lang('l.To') 0 @lang('l.Of') 0 @lang('l.entries')",
                    infoFiltered: "@lang('l.Showing') 1 @lang('l.Of') 1 @lang('l.entries')",
                    zeroRecords: "@lang('l.No matching records found')",
                    loadingRecords: "@lang('l.Loading...')",
                    processing: "@lang('l.Processing...')",
                    emptyTable: "@lang('l.No data available in table')",
                }
            });

            // حدث تحديد/إلغاء تحديد الكل
            $('#select-all').on('change', function() {
                $('.row-checkbox').prop('checked', $(this).prop('checked'));
                updateDeleteButton();
            });

            // تحديث حالة زر الحذف عند تغيير أي صندوق تحديد
            $(document).on('change', '.row-checkbox', function() {
                updateDeleteButton();
                let allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
                $('#select-all').prop('checked', allChecked);
            });

            function updateDeleteButton() {
                let checkedCount = $('.row-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('#deleteSelected').removeClass('d-none');
                } else {
                    $('#deleteSelected').addClass('d-none');
                }
            }

            // حذف الملاحظات المحددة
            $('#deleteSelected').on('click', function() {
                let selectedIds = $('.row-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length > 0) {
                    Swal.fire({
                        title: '@lang('l.Are you sure?')',
                        text: '@lang('l.Selected articles will be deleted!')',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '@lang('l.Yes, delete them!')',
                        cancelButtonText: '@lang('l.Cancel')',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href =
                                '{{ route('dashboard.admins.blogs.articles-deleteSelected') }}?ids=' +
                                selectedIds.join(',');
                        }
                    });
                }
            });

            // إضافة معالج حدث الحذف للأزرار التي يتم إنشاؤها ديناميكياً
            $(document).on('click', '.delete-article', function() {
                let articleId = $(this).data('id');

                Swal.fire({
                    title: '@lang('l.Are you sure?')',
                    text: '@lang('l.You will be delete this forever!')',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '@lang('l.Yes, delete it!')',
                    cancelButtonText: '@lang('l.Cancel')',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href =
                            '{{ route('dashboard.admins.blogs.articles-delete') }}?id=' +
                            articleId;
                    }
                });
            });
        });

        const fullEditor = new Quill('#content', {
            bounds: '#content',
            modules: {
                formula: true,
                toolbar: fullToolbar
            },
            theme: 'snow'
        });


        // حفظ المحتوى في الحقل المخفي عند التقديم
        document.getElementById('addArticleForm').addEventListener('submit', function() {
            document.getElementById('content-input').value = fullEditor.root.innerHTML;
        });

        // إضافة كود معالجة تحميل الصورة
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            const uploadArea = document.getElementById('image-upload-area');
            const uploadPreview = uploadArea.querySelector('.upload-preview');
            const previewImage = uploadArea.querySelector('.preview-image');
            const filename = uploadArea.querySelector('.filename');
            const uploadLabel = uploadArea.querySelector('.theme-upload-label');
            const removeButton = uploadArea.querySelector('.btn-remove-file');

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
                        alert('@lang('l.Please upload an image file')');
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
            });
        });
    </script>
@endsection
