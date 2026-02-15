@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Import Users')
@endsection

@section('css')
    <style>
        .drop-zone {
            width: 100%;
            height: 200px;
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 20px;
            font-weight: 500;
            cursor: pointer;
            color: #777;
            border: 4px dashed #ddd;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .drop-zone--over {
            border-style: solid;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .drop-zone__input {
            display: none;
        }

        .drop-zone__thumb {
            width: 100%;
            height: 100%;
            border-radius: 10px;
            overflow: hidden;
            background-color: #cccccc;
            background-size: cover;
            position: relative;
        }

        .drop-zone__thumb::after {
            content: attr(data-label);
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 5px 0;
            color: #ffffff;
            background: rgba(0, 0, 0, 0.75);
            font-size: 14px;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">@lang('l.Import Users')</h5>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="alert alert-warning">
                                {!! session('warning') !!}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
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
                            <div class="col-md-6">
                                <form action="{{ route('dashboard.admins.users-import-post') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="drop-zone">
                                        <span class="drop-zone__prompt">@lang('l.Drop CSV or Excel file here or click to upload')</span>
                                        <input type="file" name="file" class="drop-zone__input" accept=".csv,.txt,.xlsx,.xls">
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">@lang('l.Import')</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">@lang('l.File Format Instructions')</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>@lang('l.The file should have the following columns in order:')</p>
                                        <ol>
                                            <li>@lang('l.First Name')</li>
                                            <li>@lang('l.Last Name')</li>
                                            <li>@lang('l.Email')</li>
                                            <li>@lang('l.Phone')</li>
                                            <li>@lang('l.Role') (@lang('l.Available Roles'): {{ implode(', ', $roles->where('name', '!=', 'root')->pluck('name')->toArray()) }})</li>
                                            <li>@lang('l.Password') (@lang('l.optional'))</li>
                                            <li>@lang('l.Address') (@lang('l.optional'))</li>
                                            <li>@lang('l.State') (@lang('l.optional'))</li>
                                            <li>@lang('l.Zip Code') (@lang('l.optional'))</li>
                                            <li>@lang('l.Country') (@lang('l.optional'))</li>
                                            <li>@lang('l.City') (@lang('l.optional'))</li>
                                        </ol>
                                        <p class="text-muted">@lang('l.If password is not provided, a default password will be set.')</p>
                                        <div class="d-flex gap-2">
                                            <a href="{{ asset('templates/users-import-template.csv') }}" target="_blank" class="btn btn-outline-primary">
                                                <i class="bx bx-download me-1"></i>@lang('l.Download CSV Template')
                                            </a>
                                            <a href="{{ asset('templates/users-import-template.xlsx') }}" target="_blank" class="btn btn-outline-primary">
                                                <i class="bx bx-download me-1"></i>@lang('l.Download Excel Template')
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.querySelectorAll(".drop-zone").forEach(dropZone => {
            const input = dropZone.querySelector(".drop-zone__input");
            const prompt = dropZone.querySelector(".drop-zone__prompt");

            dropZone.addEventListener("click", e => {
                input.click();
            });

            input.addEventListener("change", e => {
                if (input.files.length) {
                    updateThumbnail(dropZone, input.files[0]);
                } else {
                    prompt.textContent = "@lang('l.Drop CSV or Excel file here or click to upload')";
                }
            });

            dropZone.addEventListener("dragover", e => {
                e.preventDefault();
                dropZone.classList.add("drop-zone--over");
            });

            ["dragleave", "dragend"].forEach(type => {
                dropZone.addEventListener(type, e => {
                    dropZone.classList.remove("drop-zone--over");
                });
            });

            dropZone.addEventListener("drop", e => {
                e.preventDefault();

                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    updateThumbnail(dropZone, e.dataTransfer.files[0]);
                }

                dropZone.classList.remove("drop-zone--over");
            });
        });

        function updateThumbnail(dropZone, file) {
            let thumbnailElement = dropZone.querySelector(".drop-zone__thumb");

            if (thumbnailElement) {
                thumbnailElement.remove();
            }

            thumbnailElement = document.createElement("div");
            thumbnailElement.classList.add("drop-zone__thumb");
            thumbnailElement.dataset.label = file.name;

            dropZone.querySelector(".drop-zone__prompt").remove();
            dropZone.appendChild(thumbnailElement);
        }
    </script>
@endsection