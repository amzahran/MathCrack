@extends('themes.default.layouts.back.student-master')

@section('title')
    {{ $lecture->name }} - Lesson
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

    <style>
        html {
            scroll-behavior: smooth;
        }

        .lesson-hero {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            color: #fff;
        }

        .lesson-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.16), transparent 26%),
                radial-gradient(circle at 85% 15%, rgba(255,255,255,0.12), transparent 22%);
            pointer-events: none;
        }

        .lesson-hero-content {
            position: relative;
            z-index: 2;
        }

        .lesson-hero h1 {
            color: #fff !important;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
            line-height: 1.35;
        }

        .lesson-hero p {
            color: rgba(255,255,255,0.92) !important;
            margin-bottom: 0;
        }

        .hero-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.24);
            border-radius: 999px;
            padding: 8px 14px;
            color: #fff;
            font-weight: 700;
            backdrop-filter: blur(10px);
            text-decoration: none;
            cursor: pointer;
        }

        .hero-badge:hover {
            color: #fff;
            background: rgba(255,255,255,0.26);
            transform: translateY(-2px);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.28);
            border-radius: 10px;
            padding: 9px 15px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.25s ease;
            margin-bottom: 18px;
        }

        .back-btn:hover {
            color: #fff;
            background: rgba(255,255,255,0.26);
            transform: translateY(-2px);
        }

        .lesson-cover {
            max-height: 190px;
            border-radius: 14px;
            object-fit: cover;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.24);
        }

        .lesson-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 18px;
            text-align: center;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        }

        .stat-number {
            display: block;
            color: #1e40af;
            font-size: 1.7rem;
            font-weight: 900;
            margin-bottom: 4px;
        }

        .stat-label {
            color: #64748b;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .lesson-card {
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 22px;
        }

        .lesson-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .lesson-card-title i {
            color: #2563eb;
        }

        .video-container {
            position: relative;
            width: 100%;
            height: 600px;
            background: #000;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.22);
        }

        .video-container .plyr,
        .video-container .plyr__video-wrapper,
        .video-container video,
        .video-container iframe,
        #plyr-video-player {
            width: 100% !important;
            height: 100% !important;
            min-height: 480px !important;
            max-height: none !important;
            background: #000;
        }

        .video-container .plyr__video-embed {
            height: 100% !important;
            padding-bottom: 0 !important;
        }

        .video-container .plyr__video-embed iframe {
            height: 100% !important;
        }


        .lesson-navigation-card {
            position: sticky;
            top: 95px;
        }

        .course-progress-small {
            margin-bottom: 16px;
        }

        .course-progress-label {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            color: #0f172a;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .course-progress-track {
            height: 9px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
        }

        .course-progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 999px;
        }

        .lesson-list {
            display: grid;
            gap: 8px;
            max-height: 520px;
            overflow-y: auto;
            padding-inline-end: 4px;
        }

        .lesson-list-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 12px;
            padding: 11px;
            color: #334155;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .lesson-list-item:hover {
            color: #1e40af;
            border-color: #bfdbfe;
            background: #eff6ff;
            transform: translateY(-1px);
        }

        .lesson-list-item.active {
            border-color: #2563eb;
            background: #dbeafe;
            color: #1e40af;
        }

        .lesson-list-status {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #e2e8f0;
            color: #475569;
            flex: 0 0 auto;
            font-size: 0.8rem;
        }

        .lesson-list-item.active .lesson-list-status {
            background: #2563eb;
            color: #fff;
        }

        .lesson-list-item.completed .lesson-list-status {
            background: #dcfce7;
            color: #166534;
        }

        .lesson-list-title {
            font-weight: 800;
            line-height: 1.35;
            margin-bottom: 4px;
        }

        .lesson-list-meta {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .lesson-next-prev {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 22px;
        }

        .lesson-nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 12px;
            padding: 12px 14px;
            font-weight: 800;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            background: #fff;
            transition: all 0.25s ease;
        }

        .lesson-nav-btn:hover {
            color: #1e40af;
            background: #eff6ff;
            border-color: #bfdbfe;
            transform: translateY(-2px);
        }

        .lesson-nav-btn.disabled {
            opacity: 0.45;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .video-container {
                height: 260px;
            }

            .video-container .plyr,
            .video-container .plyr__video-wrapper,
            .video-container video,
            .video-container iframe,
            #plyr-video-player {
                min-height: 260px !important;
            }
        }

        .material-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            text-decoration: none;
            color: #0f172a;
            transition: all 0.25s ease;
        }

        .material-card:hover {
            color: #1e40af;
            border-color: #bfdbfe;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.08);
        }

        .material-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #dbeafe;
            color: #1e40af;
            font-size: 1.5rem;
            flex: 0 0 auto;
        }

        .material-title {
            font-weight: 800;
            margin-bottom: 3px;
        }

        .material-meta {
            color: #64748b;
            font-size: 0.9rem;
        }

        .assignment-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-left: 5px solid #2563eb;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 16px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            transition: all 0.25s ease;
        }

        .assignment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.10);
        }

        .assignment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 12px;
        }

        .assignment-title {
            color: #0f172a;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .assignment-description {
            color: #64748b;
            margin-bottom: 0;
        }

        .assignment-status {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .status-not-started {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-in-progress {
            background: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
        }

        .assignment-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin: 14px 0;
        }

        .assignment-meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px;
            color: #334155;
            font-weight: 700;
        }

        .assignment-meta-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #e0f2fe;
            color: #0369a1;
            flex: 0 0 auto;
        }

        .progress-line {
            height: 8px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
            margin: 12px 0 16px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #22c55e, #16a34a);
            transition: width 0.3s ease;
        }

        .score-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 14px;
            margin-top: 14px;
        }

        .btn-custom {
            border-radius: 12px;
            padding: 11px 18px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.24);
        }

        .btn-success-custom {
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            border: none;
            color: white;
        }

        .btn-success-custom:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.24);
        }

        .sidebar-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 22px;
        }

        .sidebar-title {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #0f172a;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #dbeafe;
            color: #1e40af;
            flex: 0 0 auto;
        }

        .empty-card {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            color: #64748b;
        }

        .quick-action {
            width: 100%;
            margin-bottom: 10px;
        }

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

        @media (max-width: 768px) {
            html {
            scroll-behavior: smooth;
        }

        .lesson-hero {
                padding: 20px;
            }

            .lesson-hero h1 {
                font-size: 1.55rem;
            }

            .assignment-header {
                flex-direction: column;
            }

            .lesson-navigation-card {
                position: static;
            }

            .lesson-next-prev {
                grid-template-columns: 1fr;
            }
        }

        /* Assignments section polish, visually aligned with student tests cards */
        #Assignments.lesson-card {
            padding: clamp(20px, 3vw, 30px);
        }

        #Assignments .lesson-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        #Assignments .lesson-card-title .badge {
            border-radius: 999px;
            padding: 7px 11px;
            background: #e0ecff !important;
            color: #1e40af;
        }

        #Assignments .assignment-card {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            border-left: 0;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        #Assignments .assignment-card:hover {
            transform: translateY(-4px);
            border-color: #bfdbfe;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.14);
        }

        #Assignments .assignment-header {
            margin-bottom: 0;
            padding: 22px;
            gap: 18px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        #Assignments .assignment-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: -70px;
            width: 150px;
            height: 100%;
            background: rgba(255, 255, 255, 0.12);
            transform: skewX(-15deg);
        }

        #Assignments .assignment-header > * {
            position: relative;
            z-index: 1;
        }

        #Assignments .assignment-title {
            color: #ffffff;
            font-size: clamp(1.12rem, 2vw, 1.38rem);
            font-weight: 900;
            line-height: 1.25;
            margin-bottom: 6px;
            text-wrap: balance;
        }

        #Assignments .assignment-description {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
        }

        #Assignments .assignment-status {
            justify-content: center;
            gap: 8px;
            min-height: 38px;
            padding: 9px 14px;
            font-size: 0.86rem;
            font-weight: 900;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22);
        }

        #Assignments .status-not-started {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #475569;
        }

        #Assignments .status-in-progress {
            background: linear-gradient(135deg, #fef3c7 0%, #f59e0b 100%);
            color: #78350f;
        }

        #Assignments .status-completed {
            background: linear-gradient(135deg, #dcfce7 0%, #10b981 100%);
            color: #064e3b;
        }

        #Assignments .assignment-meta {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin: 0;
            padding: 18px 22px 0;
        }

        #Assignments .assignment-meta-item {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            font-weight: 800;
            min-width: 0;
        }

        #Assignments .assignment-meta-icon {
            border-radius: 12px;
            background: #e0ecff;
            color: #1e40af;
        }

        #Assignments .progress-line {
            margin: 16px 22px 18px;
        }

        #Assignments .progress-fill {
            background: linear-gradient(90deg, #3b82f6, #10b981);
        }

        #Assignments .score-box {
            margin: 0 22px 18px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: linear-gradient(135deg, #f8fafc 0%, #eef6ff 100%);
        }

        #Assignments .score-box strong {
            display: inline-block;
            font-size: 1.2rem;
            margin-bottom: 3px;
        }

        #Assignments .score-box small {
            color: #64748b;
            font-weight: 700;
        }

        .assignment-actions {
            display: flex;
            justify-content: flex-end;
            padding: 0 22px 22px;
        }

        .assignment-action-btn {
            min-height: 46px;
            min-width: 190px;
            border-radius: 12px !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 900 !important;
            line-height: 1.15;
            text-decoration: none !important;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .assignment-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.16);
            text-decoration: none !important;
        }

        .empty-card {
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
            padding: clamp(34px, 6vw, 58px) 20px;
        }

        .empty-card i {
            width: 82px;
            height: 82px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 24px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 2.5rem !important;
        }

        .empty-card h5 {
            color: #0f172a;
            font-weight: 900;
            margin-top: 16px;
        }

        .empty-card p {
            color: #64748b;
            line-height: 1.65;
        }

        [dir="rtl"] #Assignments .assignment-header::before {
            right: auto;
            left: -70px;
            transform: skewX(15deg);
        }

        [dir="rtl"] .assignment-actions {
            justify-content: flex-start;
        }

        @media (max-width: 767.98px) {
            #Assignments .assignment-header {
                align-items: stretch;
                padding: 20px;
            }

            #Assignments .assignment-status {
                width: 100%;
            }

            #Assignments .assignment-meta {
                grid-template-columns: 1fr;
                padding: 16px 18px 0;
            }

            #Assignments .progress-line {
                margin-left: 18px;
                margin-right: 18px;
            }

            #Assignments .score-box {
                margin-left: 18px;
                margin-right: 18px;
            }

            #Assignments .score-box .row {
                row-gap: 12px;
            }

            #Assignments .score-box .col-4 {
                width: 100%;
                flex: 0 0 100%;
            }

            .assignment-actions {
                justify-content: stretch;
                padding: 0 18px 18px;
            }

            .assignment-action-btn {
                width: 100%;
                min-width: 0;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $courseBackUrl = route('dashboard.users.courses-lectures', ['id' => encrypt($lecture->course->id)]);

        $displayCourseName = $lecture->course->name;

        if (($lecture->course->track_slug ?? null) === 'digital-sat') {
            $displayCourseName = __('l.digital_sat_course');
        }

        $youtubeId = null;

        if (!empty($lecture->video_url)) {
            $videoUrl = $lecture->video_url;

            if (str_contains($videoUrl, 'youtu.be/')) {
                $youtubeId = \Illuminate\Support\Str::after($videoUrl, 'youtu.be/');
                $youtubeId = \Illuminate\Support\Str::before($youtubeId, '?');
            } elseif (str_contains($videoUrl, 'v=')) {
                $youtubeId = \Illuminate\Support\Str::after($videoUrl, 'v=');
                $youtubeId = \Illuminate\Support\Str::before($youtubeId, '&');
            } elseif (str_contains($videoUrl, '/embed/')) {
                $youtubeId = \Illuminate\Support\Str::after($videoUrl, '/embed/');
                $youtubeId = \Illuminate\Support\Str::before($youtubeId, '?');
            }
        }
    @endphp

    <div class="main-content">
        <div class="container-fluid">
            <div class="lesson-hero">
                <div class="lesson-hero-content">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <a href="{{ $courseBackUrl }}" class="back-btn">
                                <i class="fas fa-arrow-left"></i>
                                @lang('l.back_to_course')
                            </a>

                            <h1>{{ $lecture->name }}</h1>

                            @if($lecture->description)
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($lecture->description), 180) }}</p>
                            @else
                                <p>@lang('l.video'), materials, and assignments for this lesson.</p>
                            @endif

                            <div class="hero-badges">
                                <a href="{{ $courseBackUrl }}" class="hero-badge">
                                    <i class="fas fa-book-open"></i>
                                    {{ $displayCourseName }}
                                </a>

                                @if($lecture->assignments->count() > 0)
                                    <a href="#Assignments" class="hero-badge">
                                        <i class="fas fa-tasks"></i>
                                        {{ $lecture->assignments->count() }} @lang('l.assignments')
                                    </a>
                                @endif

                                @if($lecture->files)
                                    <a href="#PDFMaterial" class="hero-badge">
                                        <i class="fas fa-file-alt"></i>
                                        @lang('l.pdf_material')
                                    </a>
                                @endif

                                @if($lecture->video_url)
                                    <a href="#@lang('l.video')Lesson" class="hero-badge">
                                        <i class="fas fa-play-circle"></i>
                                        @lang('l.video_lesson')
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4 text-end mt-4 mt-lg-0">
                            @if ($lecture->image)
                                <img src="{{ asset($lecture->image) }}" alt="{{ $lecture->name }}" class="img-fluid lesson-cover">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="lesson-stats">
                <div class="stat-card">
                    <span class="stat-number">{{ $lecture->assignments->count() }}</span>
                    <span class="stat-label">@lang('l.assignments')</span>
                </div>

                <div class="stat-card">
                    <span class="stat-number">{{ $lecture->files ? 1 : 0 }}</span>
                    <span class="stat-label">@lang('l.materials')</span>
                </div>

                <div class="stat-card">
                    <span class="stat-number">{{ $lecture->video_url ? 1 : 0 }}</span>
                    <span class="stat-label">@lang('l.video')</span>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="lesson-next-prev">
                        @if($previousLecture)
                            <a
                                href="{{ route('dashboard.users.courses-lectures-show', ['id' => encrypt($previousLecture->id)]) }}"
                                class="lesson-nav-btn"
                            >
                                <i class="fas fa-arrow-left"></i>
                                Previous Lesson
                            </a>
                        @else
                            <span class="lesson-nav-btn disabled">
                                <i class="fas fa-arrow-left"></i>
                                Previous Lesson
                            </span>
                        @endif

                        @if($nextLecture)
                            <a
                                href="{{ route('dashboard.users.courses-lectures-show', ['id' => encrypt($nextLecture->id)]) }}"
                                class="lesson-nav-btn"
                            >
                                Next Lesson
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        @else
                            <span class="lesson-nav-btn disabled">
                                Next Lesson
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        @endif
                    </div>

                    @if ($lecture->video_url)
                        <div class="lesson-card" id="@lang('l.video')Lesson">
                            <h3 class="lesson-card-title">
                                <i class="fas fa-play-circle"></i>
                                @lang('l.video_lesson')
                            </h3>

                            <div class="video-container">
                                @if($youtubeId)
                                    <div id="plyr-video-player"
                                        data-plyr-provider="youtube"
                                        data-plyr-embed-id="{{ $youtubeId }}"
                                        data-plyr-config='{
                                            "controls": ["play-large", "play", "progress", "current-time", "mute", "volume", "fullscreen", "settings"],
                                            "settings": ["speed"],
                                            "youtube": {"noCookie": true, "rel": 0, "showinfo": 0, "modestbranding": 1}
                                        }'>
                                    </div>
                                @else
                                    <video id="plyr-video-player" controls>
                                        <source src="{{ asset($lecture->video_url) }}" type="video/mp4">
                                    </video>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($lecture->description)
                        <div class="lesson-card">
                            <h3 class="lesson-card-title">
                                <i class="fas fa-align-left"></i>
                                @lang('l.lesson_description')
                            </h3>

                            <div class="text-muted">
                                {!! nl2br(e($lecture->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if ($lecture->files)
                        <div class="lesson-card" id="PDFMaterial">
                            <h3 class="lesson-card-title">
                                <i class="fas fa-file-download"></i>
                                @lang('l.pdf_material')
                            </h3>

                            <a href="{{ asset($lecture->files) }}" target="_blank" class="material-card">
                                <div class="material-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>

                                <div>
                                    <div class="material-title">@lang('l.open_lesson_material')</div>
                                    <div class="material-meta">
                                        @if (file_exists(public_path($lecture->files)))
                                            {{ round(filesize(public_path($lecture->files)) / 1024, 2) }} KB
                                        @else
                                            File attached
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    @if ($lecture->assignments && $lecture->assignments->count() > 0)
                        <div class="lesson-card" id="Assignments">
                            <h3 class="lesson-card-title">
                                <i class="fas fa-tasks"></i>
                                @lang('l.assignments')
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
                                    <div class="assignment-header">
                                        <div>
                                            <h5 class="assignment-title">{{ $assignment->title }}</h5>
                                            @if($assignment->description)
                                                <p class="assignment-description">{{ $assignment->description }}</p>
                                            @endif
                                        </div>

                                        <span class="assignment-status status-{{ $status }}">{{ $statusText }}</span>
                                    </div>

                                    <div class="assignment-meta">
                                        <div class="assignment-meta-item">
                                            <span class="assignment-meta-icon">
                                                <i class="fas fa-question-circle"></i>
                                            </span>
                                            <span>
                                                {{ $assignment->questions->count() }} @lang('l.questions')
                                            </span>
                                        </div>

                                        <div class="assignment-meta-item">
                                            <span class="assignment-meta-icon">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                            <span>
                                                {{ $assignment->time_limit ? $assignment->time_limit . ' ' . __('l.minutes') : __('l.unlimited') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="progress-line">
                                        <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                    </div>

                                    @if ($userAssignment && $userAssignment->submitted_at)
                                        <div class="score-box">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <strong class="text-primary">{{ $userAssignment->score ?? 0 }}</strong>
                                                    <br><small>@lang('l.score')</small>
                                                </div>

                                                <div class="col-4">
                                                    <strong class="text-info">{{ $userAssignment->total_points ?? 0 }}</strong>
                                                    <br><small>@lang('l.total_points')</small>
                                                </div>

                                                <div class="col-4">
                                                    <strong class="text-success">{{ $userAssignment->percentage ?? 0 }}%</strong>
                                                    <br><small>@lang('l.percentage')</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-3 text-end assignment-actions">
                                        @if (!$userAssignment || !$userAssignment->submitted_at)
                                            <a href="{{ route('dashboard.users.assignments-start', ['id' => encrypt($assignment->id)]) }}" class="btn-custom btn-primary-custom assignment-action-btn">
                                                <i class="fas fa-play"></i>
                                                {{ $userAssignment && $userAssignment->started_at ? __('l.continue_assignment') : __('l.start_assignment') }}
                                            </a>
                                        @else
                                            <a href="{{ route('dashboard.users.assignments-results', ['id' => encrypt($userAssignment->id)]) }}" class="btn-custom btn-success-custom assignment-action-btn">
                                                <i class="fas fa-chart-bar"></i>
                                                @lang('l.view_results')
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="lesson-card">
                            <div class="empty-card">
                                <i class="fas fa-tasks fa-2x mb-3"></i>
                                <h5>@lang('l.no_assignments_for_this_lesson')</h5>
                                <p class="mb-0">@lang('l.assignments_will_appear_here_when_available')</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="sidebar-card lesson-navigation-card">
                        <h5 class="sidebar-title">
                            <i class="fas fa-list-ul"></i>
                            Course Lessons
                        </h5>

                        <div class="course-progress-small">
                            <div class="course-progress-label">
                                <span>Course Progress</span>
                                <span>{{ $courseProgress ?? 0 }}%</span>
                            </div>

                            <div class="course-progress-track">
                                <div class="course-progress-fill" style="width: {{ $courseProgress ?? 0 }}%;"></div>
                            </div>
                        </div>

                        <div class="lesson-list">
                            @foreach($courseLectures ?? collect() as $index => $courseLecture)
                                @php
                                    $isCurrentLecture = $courseLecture->id === $lecture->id;

                                    $isCompletedLecture = false;

                                    if ($courseLecture->assignments && $courseLecture->assignments->count() > 0) {
                                        $isCompletedLecture = true;

                                        foreach ($courseLecture->assignments as $navAssignment) {
                                            $navStudentAssignment = $navAssignment->studentAssignments
                                                ->where('student_id', auth()->id())
                                                ->first();

                                            if (!$navStudentAssignment || !$navStudentAssignment->submitted_at) {
                                                $isCompletedLecture = false;
                                                break;
                                            }
                                        }
                                    }

                                    $lessonItemClass = $isCurrentLecture ? 'active' : ($isCompletedLecture ? 'completed' : '');
                                @endphp

                                <a
                                    href="{{ route('dashboard.users.courses-lectures-show', ['id' => encrypt($courseLecture->id)]) }}"
                                    class="lesson-list-item {{ $lessonItemClass }}"
                                >
                                    <span class="lesson-list-status">
                                        @if($isCurrentLecture)
                                            <i class="fas fa-play"></i>
                                        @elseif($isCompletedLecture)
                                            <i class="fas fa-check"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </span>

                                    <span>
                                        <span class="lesson-list-title">
                                            {{ \Illuminate\Support\Str::limit($courseLecture->name, 55) }}
                                        </span>

                                        <span class="lesson-list-meta">
                                            {{ $courseLecture->assignments->count() }} @lang('l.assignments')
                                        </span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <h5 class="sidebar-title">
                            <i class="fas fa-book"></i>
                            @lang('l.course_info')
                        </h5>

                        <div class="info-item">
                            <span class="info-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </span>

                            <div>
                                <strong>@lang('l.course')</strong>
                                <br>
                                <a href="{{ $courseBackUrl }}" class="text-decoration-none">
                                    {{ $displayCourseName }}
                                </a>
                            </div>
                        </div>

                        @if ($lecture->course->level)
                            <div class="info-item">
                                <span class="info-icon">
                                    <i class="fas fa-layer-group"></i>
                                </span>

                                <div>
                                    <strong>@lang('l.Level')</strong>
                                    <br>
                                    {{ $lecture->course->level->name }}
                                </div>
                            </div>
                        @endif

                        <div class="info-item">
                            <span class="info-icon">
                                <i class="fas fa-book-open"></i>
                            </span>

                            <div>
                                <strong>@lang('l.lessons')</strong>
                                <br>
                                {{ $lecture->course->lectures->count() }}
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ $courseBackUrl }}" class="btn btn-outline-primary btn-custom w-100">
                                <i class="fas fa-arrow-left"></i>
                                @lang('l.back_to_course')
                            </a>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <h5 class="sidebar-title">
                            <i class="fas fa-bolt"></i>
                            @lang('l.quick_actions')
                        </h5>

                        @if ($lecture->assignments->count() > 0)
                            <a class="btn btn-warning btn-custom quick-action" href="#Assignments">
                                <i class="fas fa-tasks"></i>
                                @lang('l.go_to_assignments')
                            </a>
                        @endif

                        @if ($lecture->files)
                            <a class="btn btn-info btn-custom quick-action" href="{{ asset($lecture->files) }}" target="_blank">
                                <i class="fas fa-download"></i>
                                @lang('l.download_material')
                            </a>
                        @endif

                        <button class="btn btn-outline-primary btn-custom quick-action" onclick="shareLesson()">
                            <i class="fas fa-share-alt"></i>
                            @lang('l.share_lesson')
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
            const videoElement = document.getElementById('plyr-video-player');

            if (videoElement) {
                new Plyr(videoElement);
            }
        });

        document.addEventListener('contextmenu', function(e) {
            if (e.target.closest('.video-container')) {
                e.preventDefault();
            }
        });

        function shareLesson() {
            const url = window.location.href;
            const title = @json($lecture->name);

            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                });
            } else if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    Swal.fire({
                        title: @json(__('l.copied')),
                        text: '@lang('l.lesson_link_copied_to_clipboard')',
                        icon: 'success',
                        timer: 1800,
                        showConfirmButton: false
                    });
                });
            }
        }
    </script>
@endsection
