@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Chats')
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/themes/default/vendor/css/pages/app-chat.css') }}">
<link rel="stylesheet" href="{{ asset('assets/themes/default/css/chat-responsive.css') }}">

<style>
    .chat-list {
        height: calc(100vh - 340px);
        overflow-y: auto;
    }
    .chat-item {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    .chat-item:hover {
        background-color: #f5f5f5;
        cursor: pointer;
    }
    .chat-item.active {
        background-color: #e8f3ff;
        border-left: 3px solid {{$settings['primary_color']}};
    }
    .chat-item .unread-badge {
        min-width: 22px;
        padding: 0 6px;
        height: 22px;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }
    .avatar-group {
        display: flex;
    }
    .avatar-group .avatar {
        margin-right: -8px;
        border: 2px solid #fff;
    }
    .search-input {
        position: relative;
    }
    .search-input .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 10;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 0.375rem;
        max-height: 300px;
        overflow-y: auto;
        display: none;
    }
    .search-input .search-results.show {
        display: block;
    }
    .search-result-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }
    .search-result-item:hover {
        background-color: #f5f5f5;
    }
    .search-result-item:last-child {
        border-bottom: none;
    }
    .avatar-warning {
        position: relative;
    }
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(13, 202, 240, 0.7);
            transform: scale(1);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(13, 202, 240, 0);
            transform: scale(1.05);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(13, 202, 240, 0);
            transform: scale(1);
        }
    }
    .animate-pulse {
        animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    .blink {
        animation: blink 1.5s ease-in-out infinite alternate;
    }
    @keyframes blink {
        from {
            opacity: 1;
        }
        to {
            opacity: 0.5;
        }
    }

    /* Voice Message Styles */
    .voice-recorder {
        display: inline-flex;
        align-items: center;
    }

    .voice-recorder-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: {{$settings['primary_color']}};
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
    }

    .voice-recorder-btn.recording {
        background-color: #ff4d4f;
        animation: pulse 1.5s infinite;
    }

    .voice-recorder-timer {
        margin-left: 10px;
        font-size: 14px;
        color: #666;
        min-width: 60px;
    }

    .voice-actions {
        display: none;
        margin-left: 10px;
    }

    .voice-actions.show {
        display: flex;
        gap: 8px;
    }

    .voice-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #f0f0f0;
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
    }

    .voice-action-btn:hover {
        background-color: #e0e0e0;
    }

    .voice-action-btn.send {
        background-color: #52c41a;
        color: white;
    }

    .voice-action-btn.cancel {
        background-color: #ff4d4f;
        color: white;
    }

    .voice-message {
        display: flex;
        align-items: center;
        background-color: #f0f2f5;
        border-radius: 18px;
        padding: 8px 12px;
        max-width: 300px;
        margin: 5px 0;
    }

    .voice-message-play {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: {{$settings['primary_color']}};
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin-right: 8px;
        flex-shrink: 0;
    }

    .voice-message-wave {
        flex-grow: 1;
        height: 24px;
        display: flex;
        align-items: center;
    }

    .voice-message-duration {
        margin-left: 8px;
        font-size: 12px;
        color: #666;
    }

    .wave-container {
        height: 24px;
        width: 100%;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .wave-bar {
        width: 2px;
        background-color: {{$settings['primary_color']}};
        border-radius: 5px;
        margin: 0 1px;
        height: 20%;
        transition: height 0.2s ease;
        display: inline-block;
    }

    /* أنماط حالة قراءة الرسائل */
    .read-status .message-read {
        color: {{$settings['primary_color']}};
    }

    .read-status .message-unread {
        color: #6c757d;
    }

    .read-status {
        transition: color 0.3s ease;
    }

    .emoji-picker-container {
        position: relative;
        display: inline-block;
    }

    .emoji-picker {
        position: absolute;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 1000;
        width: 300px;
        max-height: 300px;
        overflow-y: auto;
        bottom: 100%;
        right: 0;
        left: auto;
        direction: ltr;
    }

    [dir="rtl"] .emoji-picker {
        left: 0;
        right: auto;
    }

    .emoji-category {
        margin-bottom: 15px;
    }

    .emoji-category-title {
        font-size: 14px;
        color: #666;
        margin-bottom: 8px;
        padding: 5px 10px;
        background: #f5f5f5;
        border-radius: 3px;
        text-align: right;
    }

    .emoji-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(35px, 1fr));
        gap: 5px;
        padding: 5px;
    }

    .emoji {
        cursor: pointer;
        padding: 5px;
        text-align: center;
        font-size: 20px;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 35px;
        height: 35px;
    }

    .emoji:hover {
        background: #f0f0f0;
        border-radius: 5px;
    }

    .chat-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6c757d;
    }

    .chat-empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #adb5bd;
    }

    @media (max-width: 768px) {
        .emoji-picker {
            width: 250px;
            position: fixed;
            bottom: 70px;
            left: 50%;
            transform: translateX(-50%);
        }

        .emoji-list {
            grid-template-columns: repeat(auto-fill, minmax(30px, 1fr));
        }

        .emoji {
            font-size: 18px;
            min-width: 30px;
            height: 30px;
        }

        .voice-message {
            max-width: 200px;
        }

        .voice-message-wave {
            height: 20px;
        }
    }

    /* تصحيح مشكلة العرض في تاريخ المحادثة */
    .chat-history-body {
        overflow-y: auto !important;
        height: calc(100vh - 22rem) !important;
    }

    /* تنسيق الرسائل مثل صفحة التيكت */
    .chat-message .chat-message-text {
        border-radius: 0.375rem;
        background-color: #f8f9fa;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 0.543rem 1rem;
    }

    .chat-message-right .chat-message-text {
        background-color: {{$settings['primary_color']}};
        border-top-right-radius: 0 !important;
        color: white;
    }

    .chat-message:not(.chat-message-right) .chat-message-text {
        border-top-left-radius: 0 !important;
    }

    .chat-message:not(:last-child) {
        margin-bottom: 1.5rem;
    }

    /* تنسيق عنصر تاريخ اليوم */
    .chat-date-divider {
        text-align: center;
        position: relative;
        margin: 2rem 0;
        z-index: 1;
    }

    .chat-date-divider::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background-color: #dee2e6;
        z-index: -1;
    }

    .chat-date-divider span {
        background-color: var(--bs-chat-bg, #f7f8f8);
        padding: 0.5rem 1rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        color: #6c757d;
        display: inline-block;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* أنماط زر الإرسال المعطل */
    .send-msg-btn.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .send-msg-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* أنماط القائمة الجانبية والغطاء في وضع الهاتف */
    .app-chat-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9;
        display: none;
    }

    @media (max-width: 991.98px) {



        .app-chat-contacts.show {
            left: 0;
        }

        /* .app-chat-overlay {
            display: block;
        } */

        .chat-sidebar-opened {
            overflow: hidden;
            width: 90%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- عناصر الصوت للتنبيه (مخفية) -->
    <audio id="notificationSound" preload="auto" style="display: none;">
        <source src="{{ asset('assets/themes/default/sounds/notification.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- عنصر صوت احتياطي (مخفي) -->
    <audio id="backupSound" preload="auto" style="display: none;">
        <source src="{{ asset('assets/themes/default/sounds/notification.mp3') }}" type="audio/mpeg">
    </audio>

    <div class="app-chat card overflow-hidden">
        <div class="row g-0">
            <!-- Chat & Contacts -->
            <div class="col app-chat-contacts app-sidebar flex-grow-0 overflow-hidden border-end" id="app-chat-contacts">
                <div class="sidebar-header px-6 border-bottom d-flex align-items-center">
                    <div class="d-flex align-items-center me-6 me-lg-0">
                        <div class="flex-shrink-0 avatar avatar-online me-4">
                            <img class="user-avatar rounded-circle cursor-pointer" src="{{ asset(auth()->user()->photo) }}" alt="Avatar">
                        </div>
                        <div class="flex-grow-1 input-group input-group-merge rounded-pill">
                            <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search bx-sm"></i></span>
                            <input type="text" class="form-control chat-search-input" placeholder="@lang('l.Search')..." aria-label="@lang('l.Search')..." aria-describedby="basic-addon-search31" id="chatSearchInput">
                        </div>
                    </div>
                    @can('add chats')
                    <button type="button" class="btn btn-primary btn-icon rounded-circle ms-2" data-bs-toggle="modal" data-bs-target="#newChatModal">
                        <i class="bx bx-plus"></i>
                    </button>
                    @endcan
                </div>
                <div class="sidebar-body ps ps--active-y">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs nav-fill w-100 p-2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="direct-tab" data-bs-toggle="tab" href="#direct-messages" role="tab" aria-controls="direct-messages" aria-selected="true">
                                <i class="bx bx-message me-1"></i> @lang('l.Chats')
                                @if($totalDirectUnreplied > 0)
                                <span class="badge bg-danger rounded-pill ms-1">{{ $totalDirectUnreplied }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="group-tab" data-bs-toggle="tab" href="#group-chats" role="tab" aria-controls="group-chats" aria-selected="false">
                                <i class="bx bx-group me-1"></i> @lang('l.Groups')
                                @if($totalGroupUnreplied > 0)
                                <span class="badge bg-danger rounded-pill ms-1">{{ $totalGroupUnreplied }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content p-0">
                        <!-- Direct Messages -->
                        <div class="tab-pane fade show active" id="direct-messages" role="tabpanel" aria-labelledby="direct-tab">
                            <ul class="list-unstyled chat-contact-list py-2 mb-0" id="chat-list-direct">
                                {{-- <li class="chat-contact-list-item chat-contact-list-item-title">
                                    <h5 class="text-primary mb-0">@lang('l.Chats')</h5>
                                </li> --}}
                                @foreach ($chats as $chat)
                                    @if (!$chat->is_group)
                                        @php
                                            $otherParticipant = $chat->participants->where('user_id', '!=', auth()->id())->first();
                                            $otherUser = $otherParticipant ? $otherParticipant->user : null;
                                        @endphp
                                        <li class="chat-contact-list-item mb-1">
                                            <a href="javascript:void(0);" class="d-flex align-items-center text-secondary chat-link" data-chat-id="{{ encrypt($chat->id) }}">
                                                <div class="flex-shrink-0 avatar {{ $otherUser && $otherUser->isOnline() ? 'avatar-online' : 'avatar-offline' }}">
                                                    <img src="{{ $otherUser ? $otherUser->photo : asset('images/default-avatar.png') }}" alt="Avatar" class="rounded-circle">
                                                </div>
                                                <div class="chat-contact-info flex-grow-1 ms-4">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="chat-contact-name text-truncate m-0 fw-normal">
                                                            {{ $otherUser ? $otherUser->firstname . ' ' . $otherUser->lastname : __('l.Unknown User') }}
                                                        </h6>
                                                        <small class="text-muted">{{ $chat->messages->isNotEmpty() ? $chat->messages->first()->created_at->diffForHumans() : '' }}</small>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <small class="chat-contact-status text-truncate">
                                                            @if ($chat->messages->isNotEmpty())
                                                                @if ($chat->messages->first()->user_id == auth()->id())
                                                                    <span class="text-muted">@lang('l.You'):</span>
                                                                @else
                                                                    <span class="text-muted">{{ $chat->messages->first()->user->firstname }}:</span>
                                                                @endif
                                                                {{ Str::limit($chat->messages->first()->content, 20) }}
                                                            @endif
                                                        </small>
                                                        @if ($chat->unread_count > 0)
                                                            <span class="badge bg-danger rounded-pill unread-badge">{{ $chat->unread_count }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        <!-- Group Chats -->
                        <div class="tab-pane fade" id="group-chats" role="tabpanel" aria-labelledby="group-tab">
                            <ul class="list-unstyled chat-contact-list py-2 mb-0" id="chat-list-group">
                                {{-- <li class="chat-contact-list-item chat-contact-list-item-title">
                                    <h5 class="text-primary mb-0">@lang('l.Group Chats')</h5>
                                </li> --}}
                                @foreach ($chats as $chat)
                                    @if ($chat->is_group)
                                        <li class="chat-contact-list-item mb-1">
                                            <a href="javascript:void(0);" class="d-flex align-items-center text-secondary chat-link" data-chat-id="{{ encrypt($chat->id) }}">
                                                <div class="flex-shrink-0 avatar avatar-warning">
                                                    <span class="avatar-initial rounded-circle bg-primary text-white">
                                                        <i class="bx bx-group"></i>
                                                    </span>
                                                </div>
                                                <div class="chat-contact-info flex-grow-1 ms-4">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="chat-contact-name text-truncate m-0 fw-normal">
                                                            {{ $chat->name }}
                                                        </h6>
                                                        <small class="text-muted">{{ $chat->messages->isNotEmpty() ? $chat->messages->first()->created_at->diffForHumans() : '' }}</small>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <small class="chat-contact-status text-truncate">
                                                            @if ($chat->messages->isNotEmpty())
                                                                @if ($chat->messages->first()->user_id == auth()->id())
                                                                    <span class="text-muted">@lang('l.You'):</span>
                                                                @else
                                                                    <span class="text-muted">{{ $chat->messages->first()->user->firstname }}:</span>
                                                                @endif
                                                                {{ Str::limit($chat->messages->first()->content, 20) }}
                                                            @endif
                                                        </small>
                                                        @if ($chat->unread_count > 0)
                                                            <span class="badge bg-danger rounded-pill unread-badge">{{ $chat->unread_count }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Chat contacts -->

            <!-- Main content -->
            <div class="col app-chat-history" id="chat-content">
                <div class="chat-empty-state">
                    <div class="mb-3">
                        <i class="bx bx-message-square-dots text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <h4>@lang('l.Select a chat to start messaging')</h4>
                    <p class="text-muted mb-4">@lang('l.Choose from your existing chats or start a new conversation')</p>
                    <div class="d-flex gap-2">
                        <!-- زر جديد لإظهار قائمة المحادثات في الشاشات الصغيرة -->
                        <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2 d-lg-none" style="height: 30px; line-height: 1;" id="showChatsBtn">
                            @lang('l.Show Chats')
                        </button>
                        @can('add chats')
                        <button type="button" class="btn btn-primary btn-sm py-1 px-2" style="height: 30px; line-height: 1;" data-bs-toggle="modal" data-bs-target="#newChatModal">
                            @lang('l.New Chat')
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
@can('add chats')
<div class="modal fade" id="newChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('dashboard.admins.chats.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">@lang('l.New Chat')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_group" id="inlineRadio1" value="0" checked>
                            <label class="form-check-label" for="inlineRadio1">@lang('l.Direct Message')</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_group" id="inlineRadio2" value="1">
                            <label class="form-check-label" for="inlineRadio2">@lang('l.Group Chat')</label>
                        </div>
                    </div>

                    <div id="groupFields" class="d-none">
                        <div class="mb-3">
                            <label for="name" class="form-label">@lang('l.Group Name')</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">@lang('l.Description')</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="participants" class="form-label">@lang('l.Select Participants')</label>
                        <select class="select2 form-select" id="participants" name="participants[]" multiple>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->firstname }} {{ $user->lastname }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">@lang('l.Cancel')</button>
                    <button type="submit" class="btn btn-primary">@lang('l.Create')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Search Results -->
<div class="search-results-container" id="chatSearchResults" style="display: none;"></div>

<!-- موديل إدارة المشاركين -->
<div class="modal fade" id="participantsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="participantsModalTitle">@lang('l.Manage Participants')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="participants-list mb-4">
                    <h6>@lang('l.Current Participants')</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('l.Name')</th>
                                    <th>@lang('l.Email')</th>
                                    <th>@lang('l.Role')</th>
                                    <th>@lang('l.Status')</th>
                                    <th>@lang('l.Actions')</th>
                                </tr>
                            </thead>
                            <tbody id="currentParticipantsList">
                                <!-- سيتم ملء هذا الجزء عبر JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <form action="{{ route('dashboard.admins.chats.add-users-to-group') }}" method="POST" id="addParticipantsForm">
                    @csrf
                    <input type="hidden" name="chat_id" id="participantsModalChatId">

                    <div class="mb-3">
                        <h6>@lang('l.Add New Participants')</h6>
                        <select class="select2 form-select" id="new_participants" name="users[]" multiple>
                            <!-- سيتم ملء هذا الجزء عبر JavaScript -->
                        </select>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">@lang('l.Cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('l.Add Participants')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- موديل عرض المشاركين (للمستخدمين العاديين فقط) -->
<div class="modal fade" id="viewParticipantsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('l.Group Participants')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="participants-list">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('l.Name')</th>
                                    <th>@lang('l.Email')</th>
                                    <th>@lang('l.Role')</th>
                                    <th>@lang('l.Status')</th>
                                </tr>
                            </thead>
                            <tbody id="viewParticipantsList">
                                <!-- سيتم ملء هذا الجزء عبر JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('l.Close')</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- موديل تأكيد حذف المستخدم -->
<div class="modal fade" id="removeParticipantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('l.Remove Participant')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>@lang('l.Are you sure you want to remove this participant from the group?')</p>
                <p class="text-danger">@lang('l.This action cannot be undone.')</p>
            </div>
            <div class="modal-footer">
                <form action="{{ route('dashboard.admins.chats.remove-user-from-group') }}" method="POST" id="removeParticipantForm">
                    @csrf
                    <input type="hidden" name="chat_id" id="removeModalChatId">
                    <input type="hidden" name="user_id" id="removeModalUserId">

                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('l.Cancel')</button>
                    <button type="submit" class="btn btn-danger">@lang('l.Remove')</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/themes/default/js/app-chat.js') }}"></script>
<script>
    $(function() {
        // تهيئة السيليكت2
        $('.select2').select2({
            dropdownParent: $('#newChatModal')
        });

        // دالة للتأكد من إخفاء البريلودر بعد طلبات AJAX
        function hideGlobalPreloader() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        }

        // إضافة معالج للتأكد من إخفاء البريلودر بعد كل طلب AJAX
        $(document).ajaxComplete(function() {
            setTimeout(hideGlobalPreloader, 100);
        });

        // إظهار/إخفاء حقول المجموعة
        $('input[name="is_group"]').on('change', function() {
            if ($(this).val() == '1') {
                $('#groupFields').removeClass('d-none');
                $('#name').prop('required', true);
            } else {
                $('#groupFields').addClass('d-none');
                $('#name').prop('required', false);
            }
        });

        // تحديد نوع المحادثة عند اختيار المشاركين
        $('#participants').on('change', function() {
            const count = $(this).val() ? $(this).val().length : 0;
            if (count > 1) {
                $('#inlineRadio2').prop('checked', true).trigger('change');
            }
        });

        // معالجة زر القائمة (الهامبرغر) في الشاشات الصغيرة
        $(document).on('click', '[data-bs-toggle="sidebar"]', function() {
            const targetId = $(this).data('target');
            const target = $(targetId);

            // عكس حالة العرض للقائمة الجانبية
            if (target.hasClass('show')) {
                target.removeClass('show');
                $('body').removeClass('chat-sidebar-opened');
                // إزالة الغطاء (overlay) إذا كان موجوداً
                $('.app-chat-overlay').remove();
            } else {
                target.addClass('show');
                $('body').addClass('chat-sidebar-opened');
                // إضافة غطاء (overlay) عند فتح القائمة الجانبية
                $('<div class="app-chat-overlay"></div>').insertAfter('.app-chat').on('click', function() {
                    target.removeClass('show');
                    $('body').removeClass('chat-sidebar-opened');
                    $(this).remove();
                });
            }
        });

        // تحميل محتوى المحادثة عند النقر على محادثة
        $(document).on('click', '.chat-link', function(e) {
            e.preventDefault();
            const chatId = $(this).data('chat-id');

            // إضافة مؤشر التحميل
            $('#chat-content').html(`
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">@lang('l.Loading')...</span>
                    </div>
                </div>
            `);

            // تحديث الحالة النشطة في قائمة المحادثات
            $('.chat-contact-list-item').removeClass('active');
            $(this).closest('.chat-contact-list-item').addClass('active');

            // إغلاق القائمة الجانبية بعد اختيار محادثة على الشاشات الصغيرة
            if (window.innerWidth < 992) {
                $('#app-chat-contacts').removeClass('show');
                $('.app-chat-overlay').remove();
                $('body').removeClass('chat-sidebar-opened');
            }

            loadChat(chatId);
        });

        // التحقق من وجود معرف محادثة في عنوان URL وفتحها تلقائيًا
        function checkForChatIdInURL() {
            const urlParams = new URLSearchParams(window.location.search);
            const chatId = urlParams.get('id');

            if (chatId) {
                // البحث عن المحادثة في القائمة
                const chatLink = $(`.chat-link[data-chat-id="${chatId}"]`);

                if (chatLink.length) {
                    // تحديد المحادثة كنشطة وفتحها
                    chatLink.closest('.chat-contact-list-item').addClass('active');
                    loadChat(chatId);
                } else {
                    // إذا لم يتم العثور على المحادثة في القائمة، نحاول تحميلها مباشرة
                    loadChat(chatId);
                }

                // إزالة معرف المحادثة من عنوان URL
                const url = new URL(window.location);
                url.searchParams.delete('id');
                window.history.replaceState({}, '', url);
            }
        }

        // تشغيل فحص معرف المحادثة عند تحميل الصفحة
        $(document).ready(function() {
            checkForChatIdInURL();
        });

        function loadChat(chatId) {
            // إظهار حالة التحميل
            $('#chat-content').html(`
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">@lang('l.Loading')...</span>
                    </div>
                </div>
            `);

            $.ajax({
                url: '{{ route('dashboard.admins.chats.show') }}',
                type: 'GET',
                data: {
                    id: chatId
                },
                dataType: 'json',
                success: function(response) {
                    // إخفاء البريلودر العام بعد تحميل المحادثة
                    hideGlobalPreloader();

                    console.log('Chat response:', response);

                    // التحقق من وجود خطأ
                    if (response.error) {
                        showError(response.error);
                        return;
                    }

                    // التحقق من وجود البيانات المطلوبة
                    if (!response.success || !response.chat || !response.messages) {
                        console.error('Invalid response format:', response);
                        showError('@lang('l.Invalid response format')');
                        return;
                    }

                    renderChat(response);

                    // تحديث عداد الرسائل غير المقروءة
                    const chatItem = $(`.chat-link[data-chat-id="${chatId}"]`).closest('.chat-contact-list-item');
                    chatItem.find('.unread-badge').remove();
                },
                error: function(xhr, status, error) {
                    // إخفاء البريلودر العام في حالة الخطأ
                    hideGlobalPreloader();

                    console.error('Chat error:', xhr, status, error);
                    let errorMessage = '@lang('l.An error occurred while loading the chat')';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    showError(errorMessage);
                }
            });
        }

        function showError(message) {
            console.log('Showing error:', message);
            $('#chat-content').html(`
                <div class="chat-empty-state">
                    <div class="mb-3">
                        <i class="bx bx-error-circle bx-lg text-danger"></i>
                    </div>
                    <h4>@lang('l.Error')</h4>
                    <p class="text-muted mb-4">${message}</p>
                    <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                        <i class="bx bx-refresh me-1"></i> @lang('l.Refresh')
                    </button>
                </div>
            `);
        }

        function renderChat(data) {
            console.log('Rendering chat:', data);
            const chat = data.chat;
            const messages = data.messages;

            // حفظ بيانات المحادثة الحالية للاستخدام اللاحق
            window.currentChatData = chat;

            // إنشاء هيكل المحادثة
            let chatHtml = `
                <div class="chat-history-wrapper">
                    <div class="chat-history-header border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex overflow-hidden align-items-center">
                                <i class="bx bx-menu bx-lg cursor-pointer d-lg-none d-block me-4" data-bs-toggle="sidebar" data-overlay="" data-target="#app-chat-contacts"></i>
                                ${renderChatHeader(chat)}
                            </div>
                            ${renderChatActions(chat)}
                        </div>
                    </div>
                    <div class="chat-history-body" style="overflow-y: auto; height: calc(100vh - 22rem);">
                        <ul class="list-unstyled chat-history">
                            ${renderMessages(messages)}
                        </ul>
                    </div>
                    ${renderChatInput(chat)}
                </div>
            `;

            $('#chat-content').html(chatHtml);
            initializeChat();
            scrollToBottom();
        }

        function renderChatHeader(chat) {
            let otherParticipant = null;
            let isOnline = false;
            let photoUrl = '{{ asset("images/default-avatar.png") }}';
            let displayName = chat.name || '@lang("l.Chat")';

            if (!chat.is_group && chat.participants && chat.participants.length > 0) {
                // Find the other user in the chat (not the current logged-in user)
                otherParticipant = chat.participants.find(p => p.id !== {{ auth()->id() }});

                if (otherParticipant) {
                    isOnline = otherParticipant.is_online || false;
                    photoUrl = otherParticipant.photo || '{{ asset("images/default-avatar.png") }}';
                    displayName = otherParticipant.name || '@lang("l.Unknown User")';
                }
            } else if (chat.is_group) {
                // For group chats
                photoUrl = null; // We'll use an icon instead
                displayName = chat.name || '@lang("l.Group Chat")';
            }

            return `
                <div class="flex-shrink-0 avatar ${chat.is_group ? 'avatar-warning' : (isOnline ? 'avatar-online' : 'avatar-offline')}">
                    ${chat.is_group ? `
                        <span class="avatar-initial rounded-circle bg-primary text-white">
                            <i class="bx bx-group"></i>
                        </span>
                    ` : `
                        <img src="${photoUrl}" alt="Avatar" class="rounded-circle">
                    `}
                </div>
                <div class="chat-contact-info flex-grow-1 ms-4">
                    <h6 class="m-0 fw-normal">${displayName}</h6>
                    <small class="user-status text-muted">
                        ${chat.is_group ?
                            `${chat.participants ? chat.participants.length : 0} @lang('l.participants')` :
                            (isOnline ? '@lang('l.Online')' : '@lang('l.Offline')')
                        }
                    </small>
                </div>
            `;
        }

        function renderChatActions(chat) {
            if (!chat.is_group) return '';

            // التحقق مما إذا كان المستخدم الحالي مدير المجموعة
            const currentUser = chat.participants.find(p => p.id === {{ auth()->id() }});
            const isAdmin = currentUser && currentUser.is_admin;

            // التحقق من صلاحية إدارة المجموعة
            const canManageGroup = isAdmin && {{ auth()->user()->can('add chats') ? 'true' : 'false' }};

            // تغيير عنوان الموديل حسب صلاحيات المستخدم
            if (isAdmin) {
                $('#participantsModalTitle').text('@lang("l.Manage Participants")');
            } else {
                $('#participantsModalTitle').text('@lang("l.Group Participants")');
            }

            return `
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-icon text-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="true">
                            <i class="bx bx-dots-vertical-rounded bx-md"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            ${canManageGroup ?
                                `<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#participantsModal">
                                    <i class="bx bx-user-plus me-1"></i> @lang('l.Manage Participants')
                                </a>` :
                                `<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewParticipantsModal">
                                    <i class="bx bx-group me-1"></i> @lang('l.View Participants')
                                </a>`
                            }
                            <a class="dropdown-item" href="{{ route('dashboard.admins.chats.leave-group') }}?chat_id=${chat.encrypted_id}" onclick="return confirm('@lang('l.Are you sure you want to leave this group?')')">
                                <i class="bx bx-log-out me-1"></i> @lang('l.Leave Group')
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderMessages(messages) {
            if (!Array.isArray(messages) || messages.length === 0) {
                return `
                    <li class="text-center p-4">
                        <p class="text-muted">@lang('l.No messages yet')</p>
                    </li>
                `;
            }

            // فرز الرسائل من الأقدم إلى الأحدث أولاً
            const sortedMessages = [...messages].sort((a, b) => {
                // استخدام الطابع الزمني إذا كان متاحًا
                if (a.created_at_timestamp && b.created_at_timestamp) {
                    return new Date(a.created_at_timestamp) - new Date(b.created_at_timestamp);
                }

                // محاولة استخدام created_at إذا كان بتنسيق "2023-06-15 12:34:56"
                if (a.created_at && b.created_at && a.created_at.includes('-') && b.created_at.includes('-')) {
                    return new Date(a.created_at) - new Date(b.created_at);
                }

                // استخدام معرف الرسالة كبديل
                return a.id - b.id;
            });

            // تجميع الرسائل حسب التاريخ
            const messagesByDate = {};
            sortedMessages.forEach(message => {
                if (!message) return;

                // استخراج التاريخ فقط (بدون الوقت) من created_at
                let dateString;

                try {
                    // محاولة استخراج التاريخ من الطابع الزمني إذا كان متاحًا
                    if (message.created_at_timestamp) {
                        const date = new Date(message.created_at_timestamp);
                        dateString = date.toISOString().split('T')[0];
                    }
                    // محاولة تحليل النص إذا كان بتنسيق "2023-06-15 12:34:56"
                    else if (message.created_at && message.created_at.includes('-')) {
                        dateString = message.created_at.split(' ')[0];
                    }
                    // إذا كان لدينا نص "منذ كذا"، نستخرج التاريخ الفعلي بدلاً من الاعتماد على الفترة الزمنية
                    else if (message.created_at && (message.created_at.includes('منذ') || message.created_at.includes('ago'))) {
                        // نحصل على تاريخ اليوم
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);

                        // نحسب تاريخ الرسالة بناءً على تاريخ اليوم (وليس على عدد الساعات)
                        const messageDate = new Date(today);

                        // نستخرج عدد الأيام من النص، ونضبط التاريخ وفقاً لذلك
                        if (message.created_at.includes('منذ')) {
                            if (message.created_at.includes('يوم')) {
                                const matches = message.created_at.match(/منذ (\d+) (يوم|أيام|يومين)/);
                                if (matches && matches[1]) {
                                    // نستخدم العدد المستخرج من النص
                                    messageDate.setDate(today.getDate() - parseInt(matches[1]));
                                } else if (message.created_at.includes('يومين')) {
                                    messageDate.setDate(today.getDate() - 2);
                                } else if (message.created_at.includes('يوم')) {
                                    messageDate.setDate(today.getDate() - 1);
                                }
                            } else {
                                // بالنسبة للساعات والدقائق والثواني، نعتبرها في نفس اليوم
                                // لا نقوم بتغيير التاريخ
                            }
                        } else if (message.created_at.includes('ago')) {
                            // للغة الإنجليزية
                            if (message.created_at.includes('day')) {
                                const matches = message.created_at.match(/(\d+) days? ago/);
                                if (matches && matches[1]) {
                                    messageDate.setDate(today.getDate() - parseInt(matches[1]));
                                } else if (message.created_at.includes('a day ago')) {
                                    messageDate.setDate(today.getDate() - 1);
                                }
                            } else {
                                // نفس اليوم للساعات والدقائق والثواني
                            }
                        }

                        // نستخرج التاريخ بتنسيق ISO
                        dateString = messageDate.toISOString().split('T')[0];
                    }
                    // إذا لم نتمكن من استخراج التاريخ، استخدم اليوم كقيمة افتراضية
                    else {
                        console.log("تنسيق تاريخ غير معروف:", message.created_at);
                        const date = new Date();
                        dateString = date.toISOString().split('T')[0];
                    }
                } catch (e) {
                    console.error("خطأ في معالجة التاريخ:", e);
                    // استخدم اليوم في حالة حدوث خطأ
                    const date = new Date();
                    dateString = date.toISOString().split('T')[0];
                }

                if (!messagesByDate[dateString]) {
                    messagesByDate[dateString] = [];
                }
                messagesByDate[dateString].push(message);
            });

            // بناء عناصر الرسائل مع فواصل التاريخ
            let html = '';
            // ترتيب التواريخ من الأقدم إلى الأحدث
            const sortedDates = Object.keys(messagesByDate).sort();

            sortedDates.forEach(dateString => {
                // إضافة عنصر فاصل التاريخ
                const date = new Date(dateString);
                const today = new Date();
                const yesterday = new Date();
                yesterday.setDate(yesterday.getDate() - 1);

                // تنسيق التواريخ للمقارنة (المقارنة ستكون بالتاريخ فقط، وليس بالوقت)
                today.setHours(0, 0, 0, 0);
                yesterday.setHours(0, 0, 0, 0);
                date.setHours(0, 0, 0, 0);

                let displayDate;
                if (date.getTime() === today.getTime()) {
                    displayDate = '@lang("l.Today")';
                } else if (date.getTime() === yesterday.getTime()) {
                    displayDate = '@lang("l.Yesterday")';
                } else {
                    displayDate = date.toLocaleDateString('{{ app()->getLocale() }}', { year: 'numeric', month: 'long', day: 'numeric' });
                }

                html += `
                    <li class="chat-date-divider">
                        <span>${displayDate}</span>
                    </li>
                `;

                // إضافة رسائل اليوم
                messagesByDate[dateString].forEach(message => {
                    const userPhoto = message.user && message.user.photo ?
                        (message.user.photo.startsWith('http') ? message.user.photo : '{{ asset('/') }}' + message.user.photo)
                        : '{{ asset("images/default-avatar.png") }}';
                    const userName = message.user && message.user.name ? message.user.name : '@lang("l.Unknown User")';

                    html += `
                        <li class="chat-message ${message.is_self ? 'chat-message-right' : ''}" data-message-id="${message.id}">
                            <div class="d-flex overflow-hidden">
                                ${!message.is_self ? `
                                    <div class="user-avatar flex-shrink-0 me-4">
                                        <div class="avatar avatar-sm">
                                            <img src="${userPhoto}" alt="Avatar" class="rounded-circle">
                                        </div>
                                    </div>
                                ` : ''}
                                <div class="chat-message-wrapper flex-grow-1">
                                    <div class="chat-message-text">
                                        ${renderMessageContent(message)}
                                    </div>
                                    ${message.attachment ? renderMessageAttachment(message) : ''}
                                    <div class="text-end text-muted mt-1">
                                        <small class="message-sender-name ${message.is_self ? 'text-end' : 'text-start'}" style="display: block; margin-${message.is_self ? 'right' : 'left'}: 2px; font-size: 0.75rem; opacity: 0.8;">
                                            ${message.is_self ? '@lang("l.You")' : userName}
                                        </small>
                                        <small>${formatMessageTime(message)}</small>
                                        ${message.is_self ? `
                                        <span class="read-status" data-message-id="${message.id}">
                                            ${message.is_read ? '<i class="bx bx-check-double bx-16px text-primary ms-1"></i>' : '<i class="bx bx-check-double bx-16px text-muted ms-1"></i>'}
                                        </span>` : ''}
                                    </div>
                                </div>
                                ${message.is_self ? `
                                    <div class="user-avatar flex-shrink-0 ms-4">
                                        <div class="avatar avatar-sm">
                                            <img src="${userPhoto}" alt="Avatar" class="rounded-circle">
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </li>
                    `;
                });
            });

            return html;
        }

        // دالة لتنسيق الوقت من الرسالة
        function formatMessageTime(message) {
            try {
                // إذا كان لدينا طابع زمني استخدمه
                if (message.created_at_timestamp) {
                    const date = new Date(message.created_at_timestamp);
                    return date.toLocaleTimeString('{{ app()->getLocale() }}', { hour: '2-digit', minute: '2-digit' });
                }

                // إذا كان لدينا تاريخ بتنسيق "2023-06-15 12:34:56"
                else if (message.created_at && message.created_at.includes('-') && message.created_at.includes(':')) {
                    const timePart = message.created_at.split(' ')[1];
                    const [hours, minutes] = timePart.split(':');
                    return `${hours}:${minutes}`;
                }

                // إذا كان لدينا نص "منذ كذا"، نحاول تقدير الوقت
                else if (message.created_at && (message.created_at.includes('منذ') || message.created_at.includes('ago'))) {
                    // إنشاء وقت تقريبي بناءً على النص
                    const now = new Date();
                    let estimatedTime = new Date();

                    if (message.created_at.includes('منذ')) {
                        if (message.created_at.includes('يوم')) {
                            // استخراج عدد الأيام
                            const matches = message.created_at.match(/منذ (\d+) (يوم|أيام|يومين)/);
                            if (matches && matches[1]) {
                                estimatedTime.setDate(now.getDate() - parseInt(matches[1]));
                            } else if (message.created_at.includes('يومين')) {
                                estimatedTime.setDate(now.getDate() - 2);
                            } else if (message.created_at.includes('يوم')) {
                                estimatedTime.setDate(now.getDate() - 1);
                            }
                        } else if (message.created_at.includes('ساع')) {
                            const matches = message.created_at.match(/منذ (\d+) (ساعة|ساعات)/);
                            if (matches && matches[1]) {
                                estimatedTime.setHours(now.getHours() - parseInt(matches[1]));
                        } else {
                                estimatedTime.setHours(now.getHours() - 1);
                            }
                        } else if (message.created_at.includes('دقيق')) {
                            const matches = message.created_at.match(/منذ (\d+) (دقيقة|دقائق)/);
                            if (matches && matches[1]) {
                                estimatedTime.setMinutes(now.getMinutes() - parseInt(matches[1]));
                            } else {
                                estimatedTime.setMinutes(now.getMinutes() - 1);
                            }
                        }
                    } else if (message.created_at.includes('ago')) {
                        if (message.created_at.includes('day')) {
                            const matches = message.created_at.match(/(\d+) days? ago/);
                            if (matches && matches[1]) {
                                estimatedTime.setDate(now.getDate() - parseInt(matches[1]));
                            } else if (message.created_at.includes('a day ago')) {
                                estimatedTime.setDate(now.getDate() - 1);
                            }
                        } else if (message.created_at.includes('hour')) {
                            const matches = message.created_at.match(/(\d+) hours? ago/);
                            if (matches && matches[1]) {
                                estimatedTime.setHours(now.getHours() - parseInt(matches[1]));
                            } else {
                                estimatedTime.setHours(now.getHours() - 1);
                            }
                        } else if (message.created_at.includes('minute')) {
                            const matches = message.created_at.match(/(\d+) minutes? ago/);
                            if (matches && matches[1]) {
                                estimatedTime.setMinutes(now.getMinutes() - parseInt(matches[1]));
                            } else {
                                estimatedTime.setMinutes(now.getMinutes() - 1);
                            }
                        }
                    }

                    return estimatedTime.toLocaleTimeString('{{ app()->getLocale() }}', { hour: '2-digit', minute: '2-digit' });
                }

                // أي تنسيق آخر أو إذا كان لدينا قيمة غير معروفة
                else {
                    // إذا تعذر الحصول على الوقت، أرجع الآن
                    const now = new Date();
                    return now.toLocaleTimeString('{{ app()->getLocale() }}', { hour: '2-digit', minute: '2-digit' });
                }
            } catch (e) {
                console.error("خطأ في تنسيق الوقت:", e);
                // في حالة الخطأ، أرجع الوقت الحالي
                const now = new Date();
                return now.toLocaleTimeString('{{ app()->getLocale() }}', { hour: '2-digit', minute: '2-digit' });
            }
        }

        function renderMessageContent(message) {
            if (message.content && message.content.startsWith('data:audio/')) {
                return `
                    <div class="voice-message" data-audio="${message.content}">
                        <div class="voice-message-play" data-voice-id="vm-${message.id}">
                            <i class="bx bx-play"></i>
                        </div>
                        <div class="voice-message-wave">
                            <div class="wave-container" id="vm-${message.id}">
                                ${Array(20).fill().map(() =>
                                    `<div class="wave-bar" style="height: ${Math.floor(Math.random() * 80) + 10}%;"></div>`
                                ).join('')}
                            </div>
                        </div>
                        <div class="voice-message-duration">00:00</div>
                    </div>
                `;
            }
            return `<p class="mb-0">${message.content ? message.content.replace(/\n/g, '<br>') : ''}</p>`;
        }

        function renderMessageAttachment(message) {
            if (!message.attachment) return '';

            if (message.attachment_type === 'image') {
                return `
                    <div class="message-attachment">
                        <a href="{{ asset('/') }}${message.attachment}" target="_blank">
                            <img src="{{ asset('/') }}${message.attachment}" alt="Attachment" class="img-fluid">
                        </a>
                    </div>
                `;
            }

            const fileName = message.attachment.split('/').pop();
            return `
                <div class="message-attachment">
                    <div class="attachment-icon">
                        <i class="bx bx-file"></i>
                        <div>${fileName}</div>
                        <a href="{{ asset('/') }}${message.attachment}" class="btn btn-sm btn-primary mt-2" download>
                            <i class="bx bx-download"></i> @lang('l.Download')
                        </a>
                    </div>
                </div>
            `;
        }

        function renderChatInput(chat) {
            return `
                <div class="chat-history-footer shadow-xs">
                    <form class="d-flex justify-content-between align-items-center" id="chatForm">
                        <input type="hidden" name="chat_id" value="${chat.encrypted_id}">
                        <input type="hidden" name="audio_message" id="audio_message">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <textarea name="content" class="form-control message-input border-0 me-4 shadow-none" required placeholder="@lang('l.Type your message here...')" id="messageInput"></textarea>
                        <div class="message-actions d-flex align-items-center">
                            <div class="voice-recorder mx-1">
                                <button type="button" class="voice-recorder-btn" id="voiceRecorderBtn">
                                    <i class="bx bx-microphone"></i>
                                </button>
                                <span class="voice-recorder-timer" id="voiceTimer" style="display: none;">00:00</span>
                                <div class="voice-actions" id="voiceActions">
                                    <button type="button" class="voice-action-btn send" id="sendVoiceBtn">
                                        <i class="bx bx-check"></i>
                                    </button>
                                    <button type="button" class="voice-action-btn cancel" id="cancelVoiceBtn">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="emoji-picker-container mx-1">
                                <div class="btn btn-icon rounded-circle btn-outline-primary mb-0" id="emoji-button">
                                    <i class="bx bx-smile bx-sm"></i>
                                </div>
                                <div class="emoji-picker" id="emoji-picker" style="display: none;">
                                    <!-- emoji picker content -->
                                </div>
                            </div>
                            <div class="attachment-container mx-1">
                                <label for="attach-doc" class="btn btn-icon rounded-circle btn-outline-primary mb-0">
                                    <i class="bx bx-paperclip bx-sm"></i>
                                    <input type="file" id="attach-doc" name="attachment" hidden>
                                </label>
                            </div>
                            <button class="btn btn-primary d-flex send-msg-btn mx-1" type="submit">
                                <span class="align-middle d-md-inline-block d-none">@lang('l.Send')</span>
                                <i class="bx bx-paper-plane bx-sm ms-md-2 ms-0"></i>
                            </button>
                        </div>
                    </form>
                </div>
            `;
        }

        function initializeChat() {
            initializeEmojiPicker();
            initializeVoiceRecorder();
            initializeAttachments();
            initializeMessageForm();
            initializeVoiceMessagePlayers();
            scrollToBottom();
            setupNotifications();
        }

        function initializeEmojiPicker() {
            const emojis = {
                '@lang('l.Smiling')': ['😀', '😃', '😄', '😁', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳'],
                '@lang('l.Emotions')': ['😏', '😒', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠', '😡', '🤬', '🤯', '😳', '🥵', '🥶', '😱', '😨', '😰', '😥', '😓', '🤗', '🤔'],
                '@lang('l.Hands')': ['👍', '👎', '👊', '✊', '🤛', '🤜', '🤞', '🤟', '🤘', '🤙', '👌', '👈', '👉', '👆', '👇', '☝️', '✋', '🤚', '🖐', '🖖', '👋'],
                '@lang('l.People')': ['👶', '👦', '👧', '👨', '👩', '👱', '👴', '👵', '👲', '👳', '👰', '👱', '👲', '👳', '👰', '👴', '👵', '👶', '👦', '👧', '👨', '👩', '👱', '👴', '👵', '👲', '👳', '👰', '👱', '👲', '👳', '👰', '👴', '👵'],
                '@lang('l.Objects')': ['🎈', '🎉', '🎊', '🎎', '🎏', '🎐', '🎑', '🎃', '🎄', '🎆', '🎇', '🎐', '🎑', '🎃', '🎄', '🎆', '🎇', '🎈', '🎉', '🎊', '🎎', '🎏', '🎐', '🎑', '🎃', '🎄', '🎆', '🎇', '🎈', '🎉', '🎊', '🎎', '🎏', '🎐', '🎑', '🎃', '🎄', '🎆', '🎇'],
                '@lang('l.Nature')': ['🌲', '🌳', '🌴', '🌵', '🌷', '🌹', '🌸', '🌼', '🌻', '🌞', '🌝', '🌛', '🌜', '🌙', '🌌', '🌄', '🌅', '🌆', '🌇', '🌉', '🌊', '🌋', '🌌'],
                '@lang('l.Animals')': ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🐔', '🐧', '🐦', '🐤', '🦆', '🦅', '🦉', '🦇', '🐺', '🐗', '🐴', '🦄', '🐝', '🐛', '🦋'],
                '@lang('l.Food')': ['🍎', '🍐', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝', '🍅', '🍆', '🥑', '🥦', '🥬', '🥒', '🌶', '🌽', '🥕', '🥔', '🍠', '🥐', '🥯', '🍞', '🥖'],
                '@lang('l.Activities')': ['⚽️', '🏀', '🏈', '⚾️', '🥎', '🎾', '🏐', '🏉', '🎱', '🏓', '🏸', '🏒', '🏑', '🥍', '🏏', '🥅', '⛳️', '🎣', '🏹', '🎯', '🥊', '🥋', '🎽', '🛹', '🛷', '⛸', '🥌', '🎿', '⛷', '🏂'],
                '@lang('l.Travel')': ['✈️', '🚗', '🚕', '🚙', '🚌', '🚎', '🏎', '🚓', '🚑', '🚒', '🚐', '🚚', '🚛', '🚜', '🛴', '🚲', '🛵', '🏍', '🚨', '🚔', '🚍', '🚘', '🚖', '🚡', '🚠', '🚟', '🚃', '🚋', '🚞', '🚝'],
                '@lang('l.Symbols')': ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐']
            };

            const emojiPicker = document.getElementById('emoji-picker');
            const emojiButton = document.getElementById('emoji-button');
            const messageInput = document.getElementById('messageInput');

            // إنشاء محتوى picker الايموجي
            let pickerHTML = '';
            for (let category in emojis) {
                pickerHTML += `
                    <div class="emoji-category">
                        <div class="emoji-category-title">${category}</div>
                        <div class="emoji-list">
                            ${emojis[category].map(emoji => `<span class="emoji" data-emoji="${emoji}">${emoji}</span>`).join('')}
                        </div>
                    </div>
                `;
            }
            emojiPicker.innerHTML = pickerHTML;

            // إضافة الأحداث
            emojiButton.addEventListener('click', function(e) {
                e.stopPropagation();
                emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
            });

            document.querySelectorAll('.emoji').forEach(emoji => {
                emoji.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const emojiChar = this.getAttribute('data-emoji');
                    const cursorPos = messageInput.selectionStart;
                    const textBefore = messageInput.value.substring(0, cursorPos);
                    const textAfter = messageInput.value.substring(cursorPos);
                    messageInput.value = textBefore + emojiChar + textAfter;
                    messageInput.focus();
                });
            });

            document.addEventListener('click', function(e) {
                if (!emojiPicker.contains(e.target) && e.target !== emojiButton) {
                    emojiPicker.style.display = 'none';
                }
            });
        }

        function initializeVoiceRecorder() {
            const voiceRecorderBtn = document.getElementById('voiceRecorderBtn');
            const voiceTimer = document.getElementById('voiceTimer');
            const voiceActions = document.getElementById('voiceActions');
            const sendVoiceBtn = document.getElementById('sendVoiceBtn');
            const cancelVoiceBtn = document.getElementById('cancelVoiceBtn');
            const messageInput = document.getElementById('messageInput');
            const audioMessageInput = document.getElementById('audio_message');
            const chatForm = document.getElementById('chatForm');
            const sendMsgBtn = document.querySelector('.send-msg-btn');
            const attachmentInput = document.getElementById('attach-doc');
            const emojiButton = document.getElementById('emoji-button');

            let mediaRecorder;
            let audioChunks = [];
            let startTime;
            let timerInterval;
            let isRecording = false;

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                voiceRecorderBtn.style.display = 'none';
                return;
            }

            voiceRecorderBtn.addEventListener('click', function() {
                if (!isRecording) {
                    startRecording();
                } else {
                    stopRecording();
                    voiceActions.classList.add('show');
                }
            });

            sendVoiceBtn.addEventListener('click', function() {
                if (audioMessageInput.value) {
                    // بدلاً من تقديم النموذج كاملاً، نستخدم AJAX لإرسال الرسالة الصوتية
                    sendVoiceMessage();
                }
            });

            cancelVoiceBtn.addEventListener('click', function() {
                resetRecording();
            });

            // دالة جديدة لإرسال الرسالة الصوتية عبر AJAX
            function sendVoiceMessage() {
                const formData = new FormData();
                formData.append('chat_id', document.querySelector('input[name="chat_id"]').value);
                formData.append('audio_message', audioMessageInput.value);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                // تعطيل أزرار التحكم أثناء الإرسال
                sendVoiceBtn.disabled = true;
                cancelVoiceBtn.disabled = true;

                $.ajax({
                    url: '{{ route('dashboard.admins.chats.send-message') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // إخفاء البريلودر العام
                        hideGlobalPreloader();

                        if (response.success) {
                            appendMessage(response.message);
                            resetRecording();
                            scrollToBottom();

                            // إعادة ضبط عداد الرسائل غير المرد عليها للمحادثة الحالية
                            const chatId = document.querySelector('input[name="chat_id"]').value;
                            if (chatId) {
                                // إزالة العداد من عنصر المحادثة في القائمة
                                const chatLink = $(`.chat-link[data-chat-id="${chatId}"]`);
                                chatLink.find('.unread-badge').remove();

                                // تحديث العداد الكلي في تبويب المحادثات
                                const isGroup = window.currentChatData && window.currentChatData.is_group;
                                const tabBadge = isGroup ? $('#group-tab .badge') : $('#direct-tab .badge');

                                if (tabBadge.length) {
                                    const unrepliedCount = parseInt(chatLink.find('.unread-badge').text() || '0');
                                    const currentTotal = parseInt(tabBadge.text());
                                    const newTotal = currentTotal - unrepliedCount;

                                    if (newTotal <= 0) {
                                        tabBadge.remove();
                                    } else {
                                        tabBadge.text(newTotal);
                                    }
                                }
                            }
                        } else {
                            console.error('فشل في إرسال الرسالة الصوتية:', response);
                            alert(response.error || '@lang('l.Error sending voice message. Please try again.')');
                        }
                        // إعادة تفعيل الأزرار
                        sendVoiceBtn.disabled = false;
                        cancelVoiceBtn.disabled = false;
                    },
                    error: function(xhr, status, error) {
                        // إخفاء البريلودر العام
                        hideGlobalPreloader();

                        console.error('خطأ في إرسال الرسالة الصوتية:', xhr.responseText);
                        alert('@lang('l.Error sending voice message. Please try again.')');
                        // إعادة تفعيل الأزرار
                        sendVoiceBtn.disabled = false;
                        cancelVoiceBtn.disabled = false;
                    }
                });
            }

            function startRecording() {
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then(function(stream) {
                        isRecording = true;
                        voiceRecorderBtn.classList.add('recording');
                        voiceTimer.style.display = 'inline-block';

                        // تعطيل عناصر الواجهة أثناء التسجيل
                        messageInput.setAttribute('disabled', 'disabled');
                        if (sendMsgBtn) {
                            sendMsgBtn.setAttribute('disabled', 'disabled');
                            sendMsgBtn.classList.add('disabled');
                        }
                        if (attachmentInput) {
                            attachmentInput.setAttribute('disabled', 'disabled');
                        }
                        if (emojiButton) {
                            emojiButton.style.pointerEvents = 'none';
                            emojiButton.style.opacity = '0.6';
                        }

                        audioChunks = [];

                        mediaRecorder = new MediaRecorder(stream);

                        mediaRecorder.ondataavailable = function(event) {
                            if (event.data.size > 0) {
                                audioChunks.push(event.data);
                            }
                        };

                        mediaRecorder.onstop = function() {
                            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                            const reader = new FileReader();
                            reader.readAsDataURL(audioBlob);
                            reader.onloadend = function() {
                                audioMessageInput.value = reader.result;
                            };

                            stream.getTracks().forEach(track => track.stop());
                        };

                        mediaRecorder.start();
                        startTime = new Date();
                        updateTimer();
                        timerInterval = setInterval(updateTimer, 1000);
                    })
                    .catch(function(err) {
                        console.error('@lang('l.Error accessing microphone'): ', err);
                        alert('@lang('l.Could not access microphone. Please check permissions.')');
                    });
            }

            // باقي الكود كما هو...
            function stopRecording() {
                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    isRecording = false;
                    voiceRecorderBtn.classList.remove('recording');
                    clearInterval(timerInterval);
                    mediaRecorder.stop();
                }
            }

            function resetRecording() {
                isRecording = false;
                voiceRecorderBtn.classList.remove('recording');
                voiceTimer.style.display = 'none';
                voiceActions.classList.remove('show');

                // إعادة تفعيل العناصر بعد انتهاء التسجيل
                messageInput.removeAttribute('disabled');
                audioMessageInput.value = '';
                clearInterval(timerInterval);

                if (sendMsgBtn) {
                    sendMsgBtn.removeAttribute('disabled');
                    sendMsgBtn.classList.remove('disabled');
                }
                if (attachmentInput) {
                    attachmentInput.removeAttribute('disabled');
                }
                if (emojiButton) {
                    emojiButton.style.pointerEvents = 'auto';
                    emojiButton.style.opacity = '1';
                }

                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    mediaRecorder.stop();
                }
            }

            function updateTimer() {
                const now = new Date();
                const diff = (now - startTime) / 1000;
                const minutes = Math.floor(diff / 60).toString().padStart(2, '0');
                const seconds = Math.floor(diff % 60).toString().padStart(2, '0');
                voiceTimer.textContent = `${minutes}:${seconds}`;

                if (diff >= 180) {
                    stopRecording();
                    voiceActions.classList.add('show');
                }
            }
        }

        function initializeAttachments() {
            const attachInput = document.getElementById('attach-doc');

            attachInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    if (this.files[0].size > maxSize) {
                        alert('@lang('l.File size should not exceed 10MB')');
                        this.value = '';
                        return;
                    }
                }
            });
        }

        function initializeMessageForm() {
            const form = document.getElementById('chatForm');
            const messageInput = document.getElementById('messageInput');
            const audioMessageInput = document.getElementById('audio_message');
            const attachInput = document.getElementById('attach-doc');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!messageInput.value.trim() && !audioMessageInput.value && !attachInput.files.length) {
                    alert('@lang('l.Please enter a message or attach a file')');
                    return;
                }

                // تعطيل زر الإرسال أثناء معالجة الطلب لمنع النقرات المتعددة
                const submitButton = form.querySelector('.send-msg-btn');
                submitButton.disabled = true;
                submitButton.classList.add('disabled');

                const formData = new FormData(this);

                // إضافة رمز CSRF إذا لم يكن موجودًا بالفعل
                if (!formData.has('_token')) {
                    formData.append('_token', '{{ csrf_token() }}');
                }

                console.log('تجهيز إرسال الرسالة...');

                $.ajax({
                    url: '{{ route('dashboard.admins.chats.send-message') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // التأكد من إخفاء البريلودر العام
                        hideGlobalPreloader();

                        if (response.success) {
                            appendMessage(response.message);
                            form.reset();
                            scrollToBottom();

                            // إعادة ضبط عداد الرسائل غير المرد عليها للمحادثة الحالية
                            const chatId = $('input[name="chat_id"]').val();
                            if (chatId) {
                                // إزالة العداد من عنصر المحادثة في القائمة
                                const chatLink = $(`.chat-link[data-chat-id="${chatId}"]`);
                                chatLink.find('.unread-badge').remove();

                                // تحديث العداد الكلي في تبويب المحادثات
                                const isGroup = window.currentChatData && window.currentChatData.is_group;
                                const tabBadge = isGroup ? $('#group-tab .badge') : $('#direct-tab .badge');

                                if (tabBadge.length) {
                                    const unrepliedCount = parseInt(chatLink.find('.unread-badge').text() || '0');
                                    const currentTotal = parseInt(tabBadge.text());
                                    const newTotal = currentTotal - unrepliedCount;

                                    if (newTotal <= 0) {
                                        tabBadge.remove();
                                    } else {
                                        tabBadge.text(newTotal);
                                    }
                                }
                            }
                        } else {
                            console.error('استجابة غير ناجحة:', response);
                            alert(response.error || '@lang('l.Error sending message. Please try again.')');
                        }
                        // إعادة تفعيل زر الإرسال
                        submitButton.disabled = false;
                        submitButton.classList.remove('disabled');
                    },
                    error: function(xhr, status, error) {
                        // التأكد من إخفاء البريلودر العام
                        hideGlobalPreloader();

                        console.error('خطأ في إرسال الرسالة:', xhr.responseText);

                        let errorMessage = '@lang('l.Error sending message. Please try again.')';

                        // محاولة الحصول على رسالة الخطأ من الاستجابة
                        try {
                            const responseJson = JSON.parse(xhr.responseText);
                            if (responseJson && responseJson.error) {
                                errorMessage = responseJson.error;
                            }
                        } catch (e) {
                            // تجاهل الخطأ في تحليل JSON
                        }

                        alert(errorMessage);

                        // إعادة تفعيل زر الإرسال
                        submitButton.disabled = false;
                        submitButton.classList.remove('disabled');
                    }
                });
            });
        }

        function initializeVoiceMessagePlayers() {
            document.querySelectorAll('.voice-message-play').forEach(playButton => {
                if (!playButton.hasAttribute('data-initialized')) {
                    playButton.setAttribute('data-initialized', 'true');

                    playButton.addEventListener('click', function() {
                        const voiceId = this.getAttribute('data-voice-id');
                        const voiceMessage = this.closest('.voice-message');
                        const audioData = voiceMessage.getAttribute('data-audio');
                        const durationEl = voiceMessage.querySelector('.voice-message-duration');
                        const waveContainer = document.getElementById(voiceId);
                        const waveBars = waveContainer.querySelectorAll('.wave-bar');

                        let audioElement = document.getElementById('audio-' + voiceId);
                        if (!audioElement) {
                            audioElement = document.createElement('audio');
                            audioElement.id = 'audio-' + voiceId;
                            audioElement.src = audioData;
                            audioElement.style.display = 'none';
                            document.body.appendChild(audioElement);

                            audioElement.addEventListener('loadedmetadata', function() {
                                const minutes = Math.floor(audioElement.duration / 60).toString().padStart(2, '0');
                                const seconds = Math.floor(audioElement.duration % 60).toString().padStart(2, '0');
                                durationEl.textContent = `${minutes}:${seconds}`;
                            });

                            audioElement.addEventListener('timeupdate', function() {
                                const progressPercent = (audioElement.currentTime / audioElement.duration);
                                waveBars.forEach((bar, index) => {
                                    const barIndex = index / waveBars.length;
                                    if (barIndex < progressPercent) {
                                        bar.style.backgroundColor = '#52c41a';
                                    } else {
                                        bar.style.backgroundColor = '{{$settings['primary_color']}}';
                                    }
                                });

                                const currentMinutes = Math.floor(audioElement.currentTime / 60).toString().padStart(2, '0');
                                const currentSeconds = Math.floor(audioElement.currentTime % 60).toString().padStart(2, '0');
                                durationEl.textContent = `${currentMinutes}:${currentSeconds}`;
                            });

                            audioElement.addEventListener('ended', function() {
                                waveBars.forEach(bar => {
                                    bar.style.backgroundColor = '{{$settings['primary_color']}}';
                                });
                                playButton.innerHTML = '<i class="bx bx-play"></i>';
                                const minutes = Math.floor(audioElement.duration / 60).toString().padStart(2, '0');
                                const seconds = Math.floor(audioElement.duration % 60).toString().padStart(2, '0');
                                durationEl.textContent = `${minutes}:${seconds}`;
                            });
                        }

                        if (audioElement.paused) {
                            document.querySelectorAll('audio').forEach(audio => {
                                if (audio.id !== 'audio-' + voiceId && !audio.paused) {
                                    audio.pause();
                                    const otherId = audio.id.replace('audio-', '');
                                    const otherPlayButton = document.querySelector(`[data-voice-id="${otherId}"]`);
                                    if (otherPlayButton) {
                                        otherPlayButton.innerHTML = '<i class="bx bx-play"></i>';
                                    }
                                }
                            });

                            audioElement.play();
                            this.innerHTML = '<i class="bx bx-pause"></i>';
                        } else {
                            audioElement.pause();
                            this.innerHTML = '<i class="bx bx-play"></i>';
                        }
                    });
                }
            });
        }

        function setupNotifications() {
            let lastMessageId = 0;
            const chatMessages = document.querySelector('.chat-history');
            if (chatMessages && chatMessages.lastElementChild) {
                const lastMessage = chatMessages.querySelector('.chat-message:last-child');
                if (lastMessage) {
                    lastMessageId = lastMessage.getAttribute('data-message-id');
                }
            }

            // تنظيف أي مؤقتات سابقة للتحقق من الرسائل الجديدة
            if (window.checkNewMessagesInterval) {
                clearInterval(window.checkNewMessagesInterval);
            }

            // تنظيف مؤقت تحديث حالة القراءة
            if (window.checkReadStatusInterval) {
                clearInterval(window.checkReadStatusInterval);
            }

            function checkNewMessages() {
                const chatId = document.querySelector('input[name="chat_id"]')?.value;
                if (!chatId) return;

                $.ajax({
                    url: '{{ route('dashboard.admins.chats.get-new-messages') }}',
                    type: 'GET',
                    data: {
                        chat_id: chatId,
                        last_message_id: lastMessageId
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            response.forEach(message => {
                                // تحقق من عدم وجود الرسالة بالفعل
                                const existingMessage = document.querySelector(`.chat-message[data-message-id="${message.id}"]`);
                                if (!existingMessage) {
                                    appendMessage(message);

                                    // إذا لم تكن الرسالة من المستخدم الحالي، زيادة عداد الرسائل غير المرد عليها
                                    if (!message.is_self) {
                                        const chatLink = $(`.chat-link[data-chat-id="${chatId}"]`);
                                        const badgeElement = chatLink.find('.unread-badge');

                                        if (badgeElement.length) {
                                            // تحديث العداد الموجود
                                            const currentCount = parseInt(badgeElement.text());
                                            badgeElement.text(currentCount + 1);
                                        } else {
                                            // إضافة عداد جديد
                                            const countElement = $('<span class="badge bg-danger rounded-pill unread-badge">1</span>');
                                            chatLink.find('.d-flex.justify-content-between.align-items-center.mt-1').append(countElement);
                                        }

                                        // تحديث العداد الكلي في تبويب المحادثات
                                        const isGroup = window.currentChatData && window.currentChatData.is_group;
                                        const tabBadge = isGroup ? $('#group-tab .badge') : $('#direct-tab .badge');

                                        if (tabBadge.length) {
                                            // تحديث العداد الموجود
                                            const currentTotal = parseInt(tabBadge.text());
                                            tabBadge.text(currentTotal + 1);
                                        } else {
                                            // إضافة عداد جديد
                                            const countElement = $('<span class="badge bg-danger rounded-pill ms-1">1</span>');
                                            if (isGroup) {
                                                $('#group-tab').append(countElement);
                                            } else {
                                                $('#direct-tab').append(countElement);
                                            }
                                        }
                                    }

                                    if (parseInt(message.id) > parseInt(lastMessageId)) {
                                        lastMessageId = message.id;
                                        if (!document.hasFocus()) {
                                            playNotificationSound();
                                            showNotification(message);
                                        }
                                    }
                                }
                            });
                            scrollToBottom();
                        }
                    }
                });
            }

            window.checkNewMessagesInterval = setInterval(checkNewMessages, 5000);

            // إضافة فاصل زمني لتحديث حالة القراءة
            window.checkReadStatusInterval = setInterval(updateReadStatus, 5000);
        }

        // إضافة دالة لتحديث حالة قراءة الرسائل
        function updateReadStatus() {
            const chatId = document.querySelector('input[name="chat_id"]')?.value;
            if (!chatId) return;

            // الحصول على جميع رسائل المستخدم الحالي
            const myMessages = document.querySelectorAll('.chat-message-right');
            if (myMessages.length === 0) return;

            // إرسال طلب AJAX للتحقق من حالة قراءة الرسائل
            $.ajax({
                url: '{{ route('dashboard.admins.chats.check-read-status') }}',
                type: 'GET',
                data: {
                    chat_id: chatId
                },
                success: function(response) {
                    if (response.read_message_ids && response.read_message_ids.length > 0) {
                        // تحديث حالة القراءة للرسائل المقروءة
                        myMessages.forEach(messageEl => {
                            const messageId = messageEl.getAttribute('data-message-id');
                            const readStatusEl = messageEl.querySelector('.read-status');

                            if (readStatusEl && response.read_message_ids.includes(parseInt(messageId))) {
                                readStatusEl.innerHTML = '<i class="bx bx-check-double bx-16px text-primary ms-1"></i>';
                            }
                        });
                    }
                }
            });
        }

        // تهيئة زر إظهار المحادثات في الشاشات الصغيرة
        $(document).on('click', '#showChatsBtn', function() {
            const contactsList = $('#app-chat-contacts');

            // إظهار قائمة المحادثات
            contactsList.addClass('show');

            // إضافة فئة للجسم لمنع التمرير
            $('body').addClass('chat-sidebar-opened');
        });

        // إغلاق القائمة الجانبية عند النقر خارجها
        $(document).on('click', function(e) {
            const contactsList = $('#app-chat-contacts');

            // إذا كانت القائمة مفتوحة وتم النقر خارجها
            if (contactsList.hasClass('show') &&
                !contactsList.is(e.target) &&
                contactsList.has(e.target).length === 0 &&
                !$(e.target).hasClass('bx-menu') &&
                !$(e.target).closest('#showChatsBtn').length) {

                contactsList.removeClass('show');
                $('body').removeClass('chat-sidebar-opened');
            }
        });

        // وظيفة البحث في الشاتات
        $('#chatSearchInput').on('input', function() {
            const searchTerm = $(this).val().toLowerCase().trim();

            if (searchTerm === '') {
                // إذا كان حقل البحث فارغًا، أظهر جميع المحادثات
                $('.chat-contact-list-item').show();
                return;
            }

            // البحث في المحادثات المباشرة والمجموعات
            $('.chat-contact-list-item').each(function() {
                const chatName = $(this).find('.chat-contact-name').text().toLowerCase();
                const lastMessage = $(this).find('.chat-contact-status').text().toLowerCase();

                if (chatName.includes(searchTerm) || lastMessage.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // تنفيذ البحث فوري مع تأخير (debounce)
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        // تطبيق التنفيذ الفوري مع تأخير على وظيفة البحث
        const debouncedSearch = debounce(function() {
            const searchTerm = $('#chatSearchInput').val().toLowerCase().trim();

            if (searchTerm === '') {
                $('.chat-contact-list-item').show();
                return;
            }

            $('.chat-contact-list-item').each(function() {
                const chatName = $(this).find('.chat-contact-name').text().toLowerCase();
                const lastMessage = $(this).find('.chat-contact-status').text().toLowerCase();

                if (chatName.includes(searchTerm) || lastMessage.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }, 300);

        // تسجيل حدث البحث مع التنفيذ الفوري المؤجل
        $('#chatSearchInput').on('input', debouncedSearch);

        function appendMessage(message) {
            const chatHistory = document.querySelector('.chat-history');
            if (!chatHistory) return; // تأكد من وجود عنصر المحادثة قبل المتابعة

            // استخراج التاريخ من الرسالة
            let dateString;
            let messageDate;

            try {
                // محاولة استخراج التاريخ من الطابع الزمني إذا كان متاحًا
                if (message.created_at_timestamp) {
                    messageDate = new Date(message.created_at_timestamp);
                    dateString = messageDate.toISOString().split('T')[0];
                }
                // محاولة تحليل النص إذا كان بتنسيق "2023-06-15 12:34:56"
                else if (message.created_at && message.created_at.includes('-')) {
                    dateString = message.created_at.split(' ')[0];
                    messageDate = new Date(dateString);
                }
                // إذا كان لدينا تنسيق آخر مثل "منذ يومين"
                else {
                    // استخدم تاريخ اليوم للرسائل الجديدة
                    messageDate = new Date();
                    dateString = messageDate.toISOString().split('T')[0];
                }
            } catch (e) {
                console.error("خطأ في معالجة التاريخ:", e);
                messageDate = new Date();
                dateString = messageDate.toISOString().split('T')[0];
            }

            // التحقق مما إذا كان يجب إضافة فاصل تاريخ جديد
            const today = new Date();
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);

            // تنسيق التواريخ للمقارنة (المقارنة ستكون بالتاريخ فقط، وليس بالوقت)
            today.setHours(0, 0, 0, 0);
            yesterday.setHours(0, 0, 0, 0);
            messageDate.setHours(0, 0, 0, 0);

            // تحديد عرض التاريخ بناءً على مقارنة التاريخ الفعلي وليس الفترة الزمنية
            let displayDate;
            if (messageDate.getTime() === today.getTime()) {
                displayDate = '@lang("l.Today")';
            } else if (messageDate.getTime() === yesterday.getTime()) {
                displayDate = '@lang("l.Yesterday")';
            } else {
                displayDate = messageDate.toLocaleDateString('{{ app()->getLocale() }}', { year: 'numeric', month: 'long', day: 'numeric' });
            }

            // التحقق من وجود رسالة بنفس المعرف (لتجنب التكرار)
            const existingMessage = chatHistory.querySelector(`[data-message-id="${message.id}"]`);
            if (existingMessage) return;

            // البحث عن فاصل التاريخ المناسب
            let dateGroup = null;
            const dateDividers = chatHistory.querySelectorAll('.chat-date-divider');

            // ابحث عن فاصل التاريخ المطابق
            for (let i = 0; i < dateDividers.length; i++) {
                if (dateDividers[i].querySelector('span').textContent === displayDate) {
                    dateGroup = dateDividers[i];
                    break;
                }
            }

            // إذا لم يكن هناك فاصل التاريخ المناسب، أضف واحدًا جديدًا
            if (!dateGroup) {
                const dateDividerHtml = `
                    <li class="chat-date-divider">
                        <span>${displayDate}</span>
                    </li>
                `;

                chatHistory.insertAdjacentHTML('beforeend', dateDividerHtml);
                dateGroup = chatHistory.lastElementChild;
            }

            // إيجاد آخر رسالة تحت فاصل التاريخ لإضافة الرسالة الجديدة بعدها
            let lastMessageInGroup = dateGroup;
            let nextElement = dateGroup.nextElementSibling;

            while (nextElement && !nextElement.classList.contains('chat-date-divider')) {
                lastMessageInGroup = nextElement;
                nextElement = nextElement.nextElementSibling;
            }

            // إنشاء عنصر الرسالة
            const messageHtml = `
                <li class="chat-message ${message.is_self ? 'chat-message-right' : ''}" data-message-id="${message.id}">
                    <div class="d-flex overflow-hidden">
                        ${!message.is_self ? `
                            <div class="user-avatar flex-shrink-0 me-4">
                                <div class="avatar avatar-sm">
                                    <img src="${message.user.photo.startsWith('http') ? message.user.photo : '{{ asset('/') }}' + message.user.photo}" alt="Avatar" class="rounded-circle">
                                </div>
                            </div>
                        ` : ''}
                        <div class="chat-message-wrapper flex-grow-1">
                            <div class="chat-message-text">
                                ${renderMessageContent(message)}
                            </div>
                            ${message.attachment ? renderMessageAttachment(message) : ''}
                            <div class="text-end text-muted mt-1">
                                <small class="message-sender-name ${message.is_self ? 'text-end' : 'text-start'}" style="display: block; margin-${message.is_self ? 'right' : 'left'}: 2px; font-size: 0.75rem; opacity: 0.8;">
                                    ${message.is_self ? '@lang("l.You")' : message.user.name}
                                </small>
                                <small>${formatMessageTime(message)}</small>
                                ${message.is_self ? `
                                <span class="read-status" data-message-id="${message.id}">
                                    ${message.is_read ? '<i class="bx bx-check-double bx-16px text-primary ms-1"></i>' : '<i class="bx bx-check-double bx-16px text-muted ms-1"></i>'}
                                </span>` : ''}
                            </div>
                        </div>
                        ${message.is_self ? `
                            <div class="user-avatar flex-shrink-0 ms-4">
                                <div class="avatar avatar-sm">
                                    <img src="${message.user.photo.startsWith('http') ? message.user.photo : '{{ asset('/') }}' + message.user.photo}" alt="Avatar" class="rounded-circle">
                                </div>
                            </div>
                        ` : ''}
                    </div>
                </li>
            `;

            // إضافة الرسالة بعد آخر رسالة في مجموعة التاريخ
            lastMessageInGroup.insertAdjacentHTML('afterend', messageHtml);
            initializeVoiceMessagePlayers();
        }

        function scrollToBottom() {
            const chatHistory = document.querySelector('.chat-history-body');
            if (chatHistory) {
                chatHistory.scrollTop = chatHistory.scrollHeight;
            }
        }

        function playNotificationSound() {
            const audio = document.getElementById('notificationSound');
            if (audio) {
                audio.play().catch(() => {
                    const backup = document.getElementById('backupSound');
                    if (backup) {
                        backup.play().catch(() => {});
                    }
                });
            }
        }

        function showNotification(message) {
            if ('Notification' in window && Notification.permission === 'granted') {
                const notification = new Notification('@lang('l.New Message')', {
                    body: message.is_self ? '@lang('l.You'): ' + message.content : message.user.name + ': ' + message.content,
                    icon: '{{ asset('assets/themes/default/img/logo.png') }}'
                });

                notification.onclick = function() {
                    window.focus();
                    this.close();
                };

                setTimeout(() => notification.close(), 5000);
            }
        }

        // طلب إذن الإشعارات
        if ('Notification' in window && Notification.permission !== 'granted' && Notification.permission !== 'denied') {
            Notification.requestPermission();
        }

        // دالة لتهيئة وفتح موديل إدارة المشاركين
        function openParticipantsModal(chatId, chat) {
            // تعيين معرف المحادثة في النموذج
            $('#participantsModalChatId').val(chatId);

            // تفريغ القوائم
            $('#currentParticipantsList').empty();
            $('#new_participants').empty();

            // التحقق من صلاحيات المستخدم (هل هو مدير المجموعة)
            const currentUser = chat.participants.find(p => p.id === {{ auth()->id() }});
            const isAdmin = currentUser && currentUser.is_admin;

            // تغيير عنوان الموديل حسب صلاحيات المستخدم
            if (isAdmin) {
                $('#participantsModalTitle').text('@lang("l.Manage Participants")');
            } else {
                $('#participantsModalTitle').text('@lang("l.Group Participants")');
            }

            // تعبئة قائمة المشاركين الحاليين
            if (chat && chat.participants) {
                chat.participants.forEach(function(participant) {
                    const row = `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <img src="${participant.photo}" alt="Avatar" class="rounded-circle">
                                    </div>
                                    <span>${participant.name}</span>
                                </div>
                            </td>
                            <td>${participant.email}</td>
                            <td>${participant.is_admin ? '@lang("l.Admin")' : '@lang("l.Member")'}</td>
                            <td>${participant.is_online ? '<span class="badge bg-success">@lang("l.Online")</span>' : '<span class="badge bg-secondary">@lang("l.Offline")</span>'}</td>
                            <td>
                                ${isAdmin && participant.id !== {{ auth()->id() }} ?
                                    `<button type="button" class="btn btn-sm btn-danger remove-participant-btn" data-user-id="${participant.id}">
                                        <i class="bx bx-trash"></i>
                                    </button>` :
                                    (participant.id === {{ auth()->id() }} ? '<span class="badge bg-secondary">@lang("l.You")</span>' : '')}
                            </td>
                        </tr>
                    `;
                    $('#currentParticipantsList').append(row);
                });
            }

            // إظهار أو إخفاء قسم إضافة مشاركين جدد بناءً على صلاحيات المستخدم
            if (isAdmin) {
                $('#addParticipantsForm').show();

                // تعبئة قائمة المستخدمين المتاحين للإضافة
                if (chat && chat.users_to_add) {
                    chat.users_to_add.forEach(function(user) {
                        const option = new Option(user.name + ' (' + user.email + ')', user.id, false, false);
                        $('#new_participants').append(option);
                    });

                    // تهيئة select2 مع التحديثات
                    $('#new_participants').select2({
                        dropdownParent: $('#participantsModal')
                    });
                }
            } else {
                $('#addParticipantsForm').hide();
            }

            // فتح الموديل
            $('#participantsModal').modal('show');
        }

        // تسجيل حدث النقر على زر إدارة المشاركين
        $(document).on('click', '[data-bs-target="#participantsModal"]', function(e) {
            e.preventDefault();

            // الحصول على بيانات المحادثة الحالية
            if (window.currentChatData) {
                openParticipantsModal(window.currentChatData.encrypted_id, window.currentChatData);
            } else {
                console.error('بيانات المحادثة غير متوفرة');
            }
        });

        // تسجيل حدث النقر على زر عرض المشاركين (للمستخدمين العاديين)
        $(document).on('click', '[data-bs-target="#viewParticipantsModal"]', function(e) {
            e.preventDefault();

            // الحصول على بيانات المحادثة الحالية
            if (window.currentChatData) {
                openViewParticipantsModal(window.currentChatData);
            } else {
                console.error('بيانات المحادثة غير متوفرة');
            }
        });

        // دالة لفتح موديل عرض المشاركين فقط (للمستخدمين العاديين)
        function openViewParticipantsModal(chat) {
            // تفريغ قائمة المشاركين
            $('#viewParticipantsList').empty();

            // تعبئة قائمة المشاركين
            if (chat && chat.participants) {
                chat.participants.forEach(function(participant) {
                    const row = `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <img src="${participant.photo}" alt="Avatar" class="rounded-circle">
                                    </div>
                                    <span>${participant.name}</span>
                                </div>
                            </td>
                            <td>${participant.email}</td>
                            <td>${participant.is_admin ? '@lang("l.Admin")' : '@lang("l.Member")'}</td>
                            <td>${participant.is_online ? '<span class="badge bg-success">@lang("l.Online")</span>' : '<span class="badge bg-secondary">@lang("l.Offline")</span>'}</td>
                        </tr>
                    `;
                    $('#viewParticipantsList').append(row);
                });
            }

            // فتح الموديل
            $('#viewParticipantsModal').modal('show');
        }

        // تسجيل حدث النقر على زر حذف المستخدم
        $(document).on('click', '.remove-participant-btn', function() {
            const userId = $(this).data('user-id');
            $('#removeModalUserId').val(userId);
            $('#removeModalChatId').val($('#participantsModalChatId').val());
            $('#removeParticipantModal').modal('show');
        });

        // تنفيذ عملية حذف المستخدم
        $('#removeParticipantForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route('dashboard.admins.chats.remove-user-from-group') }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#participantsModal').modal('hide');
                        $('#currentParticipantsList').empty();
                        $('#new_participants').empty();
                        alert(response.message);
                    } else {
                        alert(response.error || '@lang('l.Error removing participant. Please try again.')');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr, status, error);
                    alert('@lang('l.Error removing participant. Please try again.')');
                }
            });
        });
    });
</script>
@endsection