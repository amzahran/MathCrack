@extends('themes.default.layouts.back.master')

@section('title')
    {{ __('l.Notes') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y animate__animated animate__fadeIn">
        @if ($notes->count() > 0)
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden animate__animated animate__slideInUp">
                        <div class="card-header bg-secondary bg-opacity-10 border-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <div class="avatar avatar-lg bg-primary bg-opacity-20 rounded-circle me-3 animate__animated animate__bounceIn">
                                        <i class="fas fa-note-sticky fs-2 fa-bounce text-dark"></i>
                                    </div>
                                    <h4 class="card-title mb-0 fw-bold">@lang('l.Today\'s Notes')</h4>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-primary rounded-pill animate__animated animate__fadeInRight">
                                        {{ $notes->count() }}
                                        <i class="fas fa-clipboard-list ms-1"></i>
                                    </div>
                                    <a href="{{ route('dashboard.users.notes') }}"
                                       class="btn btn-secondary rounded-pill ms-3 hover-scale">
                                        <i class="fas fa-plus me-1"></i> @lang('l.Add new notes to start')
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="notes-grid">
                                @foreach ($notes as $note)
                                    <div class="note-item animate__animated animate__fadeInUp"
                                         style="animation-delay: {{ $loop->iteration * 0.1 }}s">
                                        <div class="note-content-wrapper">
                                            <div class="note-header">
                                                <div class="note-icon">
                                                    <div class="avatar avatar-md bg-success bg-opacity-10 rounded-circle">
                                                        <i class="fas fa-comment text-success"></i>
                                                    </div>
                                                </div>
                                                <div class="note-meta">
                                                    <div class="d-flex align-items-center text-muted">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        <span>{{ \Carbon\Carbon::parse($note->date)->format('d M Y') }}</span>
                                                        @if($note->is_still_active)
                                                            <span class="badge bg-warning bg-opacity-10 text-warning ms-2">
                                                                <i class="fas fa-thumbtack me-1"></i>@lang('l.Pinned')
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="note-body">
                                                <p class="note-text">{{ $note->note }}</p>
                                            </div>

                                            <div class="note-footer">
                                                <a href="{{ route('dashboard.users.notes-edit') }}?id={{ encrypt($note->id) }}"
                                                   class="btn btn-warning*/989+btn-sm rounded-pill hover-scale">
                                                    <i class="fas fa-edit me-1"></i> @lang('l.Edit')
                                                </a>
                                                <button type="button"
                                                        class="btn btn-soft-danger btn-sm rounded-pill delete-note hover-scale"
                                                        data-note-id="{{ encrypt($note->id) }}">
                                                    <i class="fas fa-trash-alt me-1"></i> @lang('l.Delete')
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5 animate__animated animate__fadeIn">
                <div class="avatar avatar-xl bg-warning bg-opacity-10 rounded-circle mb-3 mx-auto animate__animated animate__bounce">
                    <i class="fas fa-sticky-note-o fs-1 text-warning"></i>
                </div>
                <h4 class="animate__animated animate__fadeInUp" style="animation-delay: 0.2s">@lang('l.No notes for today')</h4>
                <p class="text-muted animate__animated animate__fadeInUp" style="animation-delay: 0.3s">@lang('l.Add new notes to start')</p>
                <a href="{{ route('dashboard.users.notes') }}" class="btn btn-primary rounded-pill animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                    <i class="fas fa-plus me-1"></i> @lang('l.Add new notes to start')
                </a>
            </div>
        @endif
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* Cards Grid Layout */
        .notes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 0.5rem;
        }

        /* Note Item Styling */
        .note-item {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            transition: all 0.3s ease;
        }

        .note-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.1);
        }

        .note-content-wrapper {
            padding: 1.5rem;
        }

        /* Note Header */
        .note-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .note-icon {
            flex-shrink: 0;
        }

        .note-meta {
            flex-grow: 1;
        }

        /* Note Body */
        .note-body {
            margin-bottom: 1.5rem;
        }

        .note-text {
            font-size: 1rem;
            line-height: 1.6;
            color: #2c3e50;
            margin: 0;
        }

        /* Note Footer */
        .note-footer {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }
        /* Hover Effects */
        .hover-scale {
            transition: transform 0.2s ease;
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        /* Avatar */
        .avatar {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-lg {
            width: 48px;
            height: 48px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .notes-grid {
                grid-template-columns: 1fr;
            }

            .note-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .note-footer {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .note-item {
                background: linear-gradient(145deg, #2d3748 0%, #1a202c 100%);
            }

            .note-text {
                color: #e2e8f0;
            }

            .btn-soft-danger {
                background-color: rgba(220, 53, 69, 0.1);
                border-color: rgba(220, 53, 69, 0.2);
            }
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).on('click', '.delete-note', function() {
            let noteId = $(this).data('note-id');

            Swal.fire({
                title: '@lang('l.Are you sure?')',
                text: '@lang('l.You will delete this note forever!')',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '@lang('l.Yes, delete it!')',
                cancelButtonText: '@lang('l.Cancel')',
                customClass: {
                    popup: 'animate__animated animate__fadeInDown',
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary ms-2'
                },
                buttonsStyling: false,
                reverseButtons: true,
                background: '#fff',
                showClass: {
                    popup: 'animate__animated animate__fadeIn'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOut'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('dashboard.users.notes-delete') }}?id=' + noteId;
                }
            });
        });
    </script>
@endsection
