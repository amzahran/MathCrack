@extends('themes.default.layouts.back.student-master')

@section('title')
    {{ $lecture->name }}
@endsection

@section('css')
    <style>
        .lecture-hero {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .lecture-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .lecture-content {
            position: relative;
            z-index: 2;
        }

        .video-container {
            position: relative;
            width: 100%;
            background: #000;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .video-container iframe,
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .lecture-info-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(30, 64, 175, 0.08);
            margin-bottom: 1.5rem;
            border: 1px solid #e3f2fd;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 1rem;
            font-size: 1.1rem;
        }

        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .file-card {
            background: white;
            border: 2px solid #e3f2fd;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #1e40af;
        }

        .file-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .assignment-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #4caf50;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .assignment-card:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .assignment-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-not-started {
            background: #ffebee;
            color: #c62828;
        }

        .status-in-progress {
            background: #fff3e0;
            color: #ef6c00;
        }

        .status-completed {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .progress-bar {
            height: 8px;
            background: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
            margin: 1rem 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4caf50, #8bc34a);
            transition: width 0.3s ease;
        }

        .btn-custom {
            border-radius: 25px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border: none;
            color: white
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 64, 175, 0.4);

        }

        .btn-success-custom {
            background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
            border: none;

        }

        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);

        }

        .breadcrumb-custom {
            background: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-custom .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            color: #1e40af;
            font-weight: bold;
        }

        .lecture-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #e3f2fd;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(30, 64, 175, 0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #1e40af;
            display: block;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .lecture-hero {
                padding: 1rem;
            }

            .files-grid {
                grid-template-columns: 1fr;
            }

            .lecture-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
        <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

        <style>
        /* منع النقر بزر الماوس الأيمن */
        .plyr {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .plyr__video-wrapper {
            pointer-events: none;
        }

        .plyr__controls {
            pointer-events: auto;
        }

        /* إخفاء شعار YouTube وعناصر التحكم العلوية */
        .plyr--youtube .plyr__video-wrapper iframe {
            top: -50px;
            height: calc(100% + 50px);
        }

        /* إخفاء واجهة يوتيوب الافتراضية */
        .plyr--youtube .plyr__video-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #000;
            z-index: 1;
        }

        .plyr--playing .plyr__video-wrapper::before {
            display: none;
        }

        /* تعديل موضع الفيديو */
        .plyr--youtube .plyr__video-wrapper iframe {
            top: -50px;
            height: calc(100% + 100px);
        }
    </style>
    <script>
        // منع استخدام زر الماوس الأيمن
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // منع استخدام مفاتيح الاختصار للـ Inspect Element
        document.addEventListener('keydown', function(e) {
            // منع F12
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                return false;
            }

            // منع Ctrl+Shift+I / Cmd+Shift+I
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.keyCode === 73)) {
                e.preventDefault();
                return false;
            }

            // منع Ctrl+Shift+C / Cmd+Shift+C
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'C' || e.key === 'c' || e.keyCode === 67)) {
                e.preventDefault();
                return false;
            }

            // منع Ctrl+U / Cmd+U (View Source)
            if ((e.ctrlKey || e.metaKey) && (e.key === 'U' || e.key === 'u' || e.keyCode === 85)) {
                e.preventDefault();
                return false;
            }
        });

        // منع السحب والإفلات للصور والنصوص
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
        });
    </script>
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <!-- Lecture Hero Section -->
            <div class="lecture-hero">
                <div class="lecture-content">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="mb-3">{{ $lecture->name }}</h1>
                            <p class="mb-3 opacity-90">{{ $lecture->description ?? __('l.no_description_available') }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i class="fas fa-book me-1"></i>{{ $lecture->course->name }}
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-4 text-end">
                            @if ($lecture->image)
                                <img src="{{ asset($lecture->image) }}" alt="{{ $lecture->name }}"
                                    class="img-fluid rounded shadow" style="max-height: 200px;">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Lecture Statistics -->
            <div class="lecture-stats">
                <div class="stat-card">
                    <span class="stat-number">{{ $lecture->assignments->count() }}</span>
                    <span class="stat-label">@lang('l.assignments')</span>
                </div>
                <div class="stat-card">
                    <span
                        class="stat-number">{{ $lecture->files ? 1 : 0 }}</span>
                    <span class="stat-label">@lang('l.files')</span>
                </div>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Video Section -->
                    @if ($lecture->video_url)
                        <div class="lecture-info-card">
                            <h3 class="mb-3">
                                <i class="fas fa-play-circle text-primary me-2"></i>@lang('l.lecture_video')
                            </h3>
                            <div class="video-container">
                                <div id="plyr-video-player" data-plyr-provider="youtube"
                                    data-plyr-embed-id="{{ \Illuminate\Support\Str::after($lecture->video_url, 'v=') }}"
                                    data-plyr-config='{
                                        "controls": ["play-large", "play", "progress", "current-time", "mute", "volume", "fullscreen", "settings"],
                                        "settings": ["speed"],
                                        "youtube": {"noCookie": true, "rel": 0, "showinfo": 0, "modestbranding": 1}
                                    }'>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Lecture Description -->
                    @if ($lecture->description)
                        <div class="lecture-info-card">
                            <h3 class="mb-3">
                                <i class="fas fa-info-circle text-info me-2"></i>@lang('l.lecture_description')
                            </h3>
                            <div class="text-muted">
                                {!! nl2br(e($lecture->description)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Files Section -->
                    @if ($lecture->files)
                        <div class="lecture-info-card">
                            <h3 class="mb-3">
                                <i class="fas fa-file-download text-success me-2"></i>@lang('l.lecture_files')
                            </h3>
                            <div class="files-grid">
                                <a href="{{ asset($lecture->files) }}" target="_blank"
                                    class="file-card">
                                    <div class="file-icon">
                                        <i class="fas fa-file-alt text-primary"></i>
                                    </div>
                                    <div class="file-name">{{ $lecture->files }}</div>
                                    <div class="file-size text-muted">
                                        @if (file_exists(public_path($lecture->files)))
                                            {{ round(filesize(public_path($lecture->files)) / 1024, 2) }} KB
                                        @else
                                            @lang('l.file_not_found')
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Assignments Section -->
                    @if ($lecture->assignments && $lecture->assignments->count() > 0)
                        <div class="lecture-info-card" id="Assignments">
                            <h3 class="mb-3">
                                <i class="fas fa-tasks text-warning me-2"></i>@lang('l.assignments')
                                <span class="badge bg-secondary ms-2">{{ $lecture->assignments->count() }}</span>
                            </h3>

                            @foreach ($lecture->assignments as $assignment)
                                @php
                                    $userAssignment = $assignment->studentAssignments
                                        ->where('student_id', auth()->id())
                                        ->first();
                                    $status = 'not-started';
                                    $statusText = __('l.not_started');
                                    $progress = 0;

                                    if ($userAssignment) {
                                        if ($userAssignment->submitted_at) {
                                            $status = 'completed';
                                            $statusText = __('l.completed');
                                            $progress = 100;
                                        } elseif ($userAssignment->started_at) {
                                            $status = 'in-progress';
                                            $statusText = __('l.in_progress');
                                            $progress = 50;
                                        }
                                    }
                                @endphp

                                <div class="assignment-card">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="mb-1">{{ $assignment->title }}</h5>
                                            <p class="text-muted mb-2">{{ $assignment->description }}</p>
                                        </div>
                                        <span
                                            class="assignment-status status-{{ $status }}">{{ $statusText }}</span>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <div class="info-icon bg-light text-primary">
                                                    <i class="fas fa-question-circle"></i>
                                                </div>
                                                <div>
                                                    <strong>@lang('l.questions_count'): </strong>
                                                    {{ $assignment->questions->count() }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <div class="info-icon bg-light text-warning">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div>
                                                    <strong>@lang('l.time_limit'): </strong>
                                                    {{ $assignment->time_limit ? $assignment->time_limit . ' ' . __('l.minutes') : __('l.unlimited') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                    </div>

                                    @if ($userAssignment && $userAssignment->submitted_at)
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <strong
                                                        class="text-primary">{{ $userAssignment->score ?? 0 }}</strong>
                                                    <br><small>@lang('l.score')</small>
                                                </div>
                                                <div class="col-4">
                                                    <strong
                                                        class="text-info">{{ $userAssignment->total_points ?? 0 }}</strong>
                                                    <br><small>@lang('l.total_points')</small>
                                                </div>
                                                <div class="col-4">
                                                    <strong
                                                        class="text-success">{{ $userAssignment->percentage ?? 0 }}%</strong>
                                                    <br><small>@lang('l.percentage')</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-3 text-end">
                                        @if (!$userAssignment || !$userAssignment->submitted_at)
                                            <a href="{{ route('dashboard.users.assignments-start', ['id' => encrypt($assignment->id)]) }}" class="btn btn-primary-custom btn-custom">
                                                <i class="fas fa-play me-2"></i>
                                                {{ $userAssignment && $userAssignment->started_at ? __('l.continue_assignment') : __('l.start_assignment') }}
                                            </a>
                                        @else
                                            <a href="{{ route('dashboard.users.assignments-results', ['id' => encrypt($userAssignment->id)]) }}" class="btn btn-success-custom btn-custom">
                                                <i class="fas fa-chart-bar me-2"></i>@lang('l.view_results')
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Course Info -->
                    <div class="lecture-info-card">
                        <h5 class="mb-3">
                            <i class="fas fa-book text-primary me-2"></i>@lang('l.course_info')
                        </h5>
                        <div class="info-item">
                            <div class="info-icon bg-primary text-white">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div>
                                <strong>@lang('l.course_name'): </strong>
                                <a href="{{ route('dashboard.users.courses-lectures', ['id' => encrypt($lecture->course->id)]) }}"
                                    class="text-decoration-none">
                                    {{ $lecture->course->name }}
                                </a>
                            </div>
                        </div>

                        @if ($lecture->course->level)
                            <div class="info-item">
                                <div class="info-icon bg-info text-white">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div>
                                    <strong>@lang('l.Level'): </strong>
                                    {{ $lecture->course->level->name }}
                                </div>
                            </div>
                        @endif

                        <div class="info-item">
                            <div class="info-icon bg-success text-white">
                                <i class="fas fa-video"></i>
                            </div>
                            <div>
                                <strong>@lang('l.lectures_count'): </strong>
                                {{ $lecture->course->lectures->count() }}
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('dashboard.users.courses-lectures', ['id' => encrypt($lecture->course->id)]) }}"
                                class="btn btn-outline-primary btn-custom w-100">
                                <i class="fas fa-arrow-left me-2"></i>@lang('l.back_to_course')
                            </a>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="lecture-info-card">
                        <h5 class="mb-3">
                            <i class="fas fa-bolt text-warning me-2"></i>@lang('l.quick_actions')
                        </h5>

                        @if ($lecture->assignments->count() > 0)
                            <a class="btn btn-warning btn-custom w-100 mb-2" href="#Assignments">
                                <i class="fas fa-play-circle me-2"></i>@lang('l.start_all_assignments')
                            </a>
                        @endif

                        @if ($lecture->files)
                            <a class="btn btn-info btn-custom w-100 mb-2" href="{{ asset($lecture->files) }}" target="_blank">
                                <i class="fas fa-download me-2"></i>@lang('l.download_all_files')
                            </a>
                        @endif

                        {{-- <button class="btn btn-secondary btn-custom w-100 mb-2" onclick="markAsCompleted()">
                            <i class="fas fa-check-circle me-2"></i>@lang('l.mark_completed')
                        </button> --}}

                        <button class="btn btn-outline-primary btn-custom w-100" onclick="shareLecture()">
                            <i class="fas fa-share-alt me-2"></i>@lang('l.share')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const player = new Plyr('#plyr-video-player');
        });
    </script>

    <script>
        // مشاركة المحاضرة
        function shareLecture() {
            const url = window.location.href;
            const title = '{{ $lecture->name }}';

            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                });
            } else {
                // نسخ الرابط للحافظة
                navigator.clipboard.writeText(url).then(() => {
                    Swal.fire({
                        title: '@lang('l.course_name')',
                        text: '@lang('l.level')',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            }
        }

        // تأثيرات الحركة عند التحميل
        $(document).ready(function() {
            // تأثير ظهور البطاقات
            $('.lecture-info-card, .assignment-card, .stat-card').each(function(index) {
                $(this).css('opacity', '0').css('transform', 'translateY(20px)');
                $(this).delay(index * 100).animate({
                    opacity: 1
                }, 500).animate({
                    transform: 'translateY(0)'
                }, 500);
            });

            // تأثير hover على الملفات
            $('.file-card').hover(
                function() {
                    $(this).addClass('shadow-lg');
                },
                function() {
                    $(this).removeClass('shadow-lg');
                }
            );

            // تحديث دائرة التقدم
            const progressRing = document.querySelector('.progress-ring circle:last-child');
            if (progressRing) {
                const circumference = 2 * Math.PI * 50;
                const progress = {{ $overallProgress ?? 0 }};
                const strokeDashoffset = circumference - (progress / 100) * circumference;

                setTimeout(() => {
                    progressRing.style.strokeDashoffset = strokeDashoffset;
                    progressRing.style.transition = 'stroke-dashoffset 2s ease-in-out';
                }, 500);
            }
        });

        // إضافة تأثيرات عند النقر على الأزرار
        $('.btn-custom').click(function() {
            $(this).addClass('animate__animated animate__pulse');
            setTimeout(() => {
                $(this).removeClass('animate__animated animate__pulse');
            }, 600);
        });
    </script>
@endsection
