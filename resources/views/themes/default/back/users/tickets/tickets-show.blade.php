@extends('themes.default.layouts.back.master')


@section('title')
    @lang('l.Ticket Show')
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/themes/default/vendor/css/pages/app-chat.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/themes/default/css/chat-responsive.css') }}">

    <style>
        .avatar-warning,
        .avatar-info {
            position: relative;
        }

        .avatar-warning::after {
            background-color: #ffc107;
            content: "";
            position: absolute;
            bottom: 0;
            right: 3px;
            width: 8px;
            height: 8px;
            border-radius: 100%;
            box-shadow: 0 0 0 2px #2b2c40;
        }

        .avatar-info::after {
            background-color: #0dcaf0;
            content: "";
            position: absolute;
            bottom: 0;
            right: 3px;
            width: 8px;
            height: 8px;
            border-radius: 100%;
            box-shadow: 0 0 0 2px #2b2c40;
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

        /* Voice Message Styles */
        .voice-recorder {
            display: inline-flex;
            align-items: center;
        }

        .voice-recorder-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: {{ $settings['primary_color'] }};
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
            background-color: {{ $settings['primary_color'] }};
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

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
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
            background-color: {{ $settings['primary_color'] }};
            border-radius: 5px;
            margin: 0 1px;
            height: 20%;
            transition: height 0.2s ease;
            display: inline-block;
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

        /* تأثير نبض للزر */
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
                <div class="col app-chat-contacts app-sidebar flex-grow-0 overflow-hidden border-end"
                    id="app-chat-contacts">
                    <div class="sidebar-header px-6 border-bottom d-flex align-items-center">
                        <div class="d-flex align-items-center me-6 me-lg-0">
                            <div class="flex-shrink-0 avatar avatar-online me-4"
                                data-bs-toggle="sidebar" data-overlay="app-overlay-ex" data-target="#app-chat-sidebar-left">
                                <img class="user-avatar rounded-circle cursor-pointer"
                                    src="{{ asset(auth()->user()->photo) }}" alt="Avatar">
                            </div>
                            <div class="flex-grow-1 input-group input-group-merge rounded-pill">
                                <span class="input-group-text" id="basic-addon-search31"><i
                                        class="bx bx-search bx-sm"></i></span>
                                <input type="text" class="form-control chat-search-input"
                                    placeholder="@lang('l.Search')..." aria-label="@lang('l.Search')..."
                                    aria-describedby="basic-addon-search31">
                            </div>
                        </div>
                        <i class="bx bx-x bx-lg cursor-pointer position-absolute top-50 end-0 translate-middle d-lg-none d-block"
                            data-overlay="" data-bs-toggle="sidebar" data-target="#app-chat-contacts"></i>
                    </div>
                    <div class="sidebar-body ps ps--active-y">

                        <!-- Chats -->
                        <ul class="list-unstyled chat-contact-list py-2 mb-0" id="chat-list">
                            <li class="chat-contact-list-item chat-contact-list-item-title mt-0">
                                <h5 class="text-primary mb-0">@lang('l.Previous Chats')</h5>
                            </li>
                            @forelse($ticket->user->tickets()->orderByDesc('id')->get() as $tic)
                                <li class="chat-contact-list-item mb-1 @if ($ticket->id == $tic->id) active @endif">
                                    <a href="{{ route('dashboard.users.tickets-show', ['id' => encrypt($tic->id)]) }}"
                                        class="d-flex align-items-center text-secondary">
                                        <div
                                            class="flex-shrink-0 avatar @if ($tic->status == 'in_progress') avatar-info @elseif($tic->status == 'answered') avatar-warning blink @else avatar-offline @endif">
                                            <img src="{{ asset($ticket->user->photo) }}" alt="Avatar"
                                                class="rounded-circle">
                                        </div>
                                        <div class="chat-contact-info flex-grow-1 ms-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="chat-contact-name text-truncate m-0 fw-normal">
                                                    {{ __('l.' . ucfirst($tic->support_type)) }}</h6>
                                                <small class="text-muted">{{ $tic->updated_at->diffForHumans() }}</small>
                                            </div>
                                            <small
                                                class="chat-contact-status text-truncate">{{ Str::limit($tic->subject, 30) }}</small>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="chat-contact-list-item">
                                    <h6 class="text-muted mb-0 text-center">@lang('l.No Chats Found')</h6>
                                </li>
                            @endforelse
                        </ul>

                        <!-- Contacts -->
                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                        </div>
                        <div class="ps__rail-y" style="top: 0px; height: 475px; right: 0px;">
                            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 223px;"></div>
                        </div>
                    </div>
                </div>
                <!-- /Chat contacts -->

                <!-- Chat History -->
                <div class="col app-chat-history">
                    <div class="chat-history-wrapper">
                        <div class="chat-history-header border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex overflow-hidden align-items-center">
                                    <i class="bx bx-menu bx-lg cursor-pointer d-lg-none d-block me-4"
                                        data-bs-toggle="sidebar" data-overlay="" data-target="#app-chat-contacts"></i>
                                    <div
                                        class="flex-shrink-0 avatar @if ($ticket->user->isOnline()) avatar-online @else avatar-offline @endif">
                                        <img src="{{ asset($ticket->user->photo) }}" alt="Avatar" class="rounded-circle"
                                            data-bs-toggle="sidebar" data-overlay="" data-target="#app-chat-sidebar-right">
                                    </div>
                                    <div class="chat-contact-info flex-grow-1 ms-4">
                                        <h6 class="m-0 fw-normal">
                                            {{ __('l.' . ucfirst($ticket->support_type)) }}</h6>
                                        <small class="user-status text-body">{{ $ticket->subject }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chat-history-body ps ps--active-y">
                            <ul class="list-unstyled chat-history">
                                @foreach ($ticket->ticketMessages as $comment)
                                    <li class="chat-message @if ($comment->user_id == $ticket->user_id) chat-message-right @endif">
                                        <div class="d-flex overflow-hidden">
                                            @if ($comment->user_id != $ticket->user_id)
                                                <div class="user-avatar flex-shrink-0 me-4">
                                                    <div class="avatar avatar-sm">
                                                        <img src="{{ asset($comment->user->photo) }}" alt="Avatar"
                                                            class="rounded-circle">
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="chat-message-wrapper flex-grow-1">
                                                <div class="chat-message-text">
                                                    @if (strpos($comment->content, 'data:audio/') === 0)
                                                        <!-- رسالة صوتية -->
                                                        <div class="voice-message" data-audio="{{ $comment->content }}">
                                                            <div class="voice-message-play" data-voice-id="vm-{{ $comment->id }}">
                                                                <i class="bx bx-play"></i>
                                                            </div>
                                                            <div class="voice-message-wave">
                                                                <div class="wave-container" id="vm-{{ $comment->id }}">
                                                                    @for ($i = 0; $i < 20; $i++)
                                                                        <div class="wave-bar" style="height: {{ rand(10, 90) }}%;"></div>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            <div class="voice-message-duration">00:00</div>
                                                        </div>
                                                    @else
                                                        <!-- رسالة نصية عادية -->
                                                    <p class="mb-0">{!! nl2br(e($comment->content)) !!}</p>
                                                    @endif
                                                </div>
                                                @if ($comment->attachment)
                                                    <div class="chat-message-attachment">
                                                        <a href="{{ asset($comment->attachment) }}" target="_blank">
                                                            <i class="fa fa-eye"></i> @lang('l.Show Attachment')
                                                        </a>
                                                    </div>
                                                @endif
                                                <div class="text-end text-muted mt-1">
                                                    @if ($comment->user_id == $ticket->user_id)
                                                        <i class="bx bx-check-double bx-16px text-success me-1"></i>
                                                    @endif
                                                    <small>{{ $comment->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                            @if ($comment->user_id == $ticket->user_id)
                                                <div class="user-avatar flex-shrink-0 ms-4">
                                                    <div class="avatar avatar-sm">
                                                        <img src="{{ asset($comment->user->photo) }}" alt="Avatar"
                                                            class="rounded-circle">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Chat message form -->
                        @if ($ticket->status != 'closed')
                            <div class="chat-history-footer shadow-xs">
                                <form class="d-flex justify-content-between align-items-center"
                                    method="post" action="{{ route('dashboard.users.tickets-reply') }}"
                                    enctype="multipart/form-data" id="chatForm">
                                    @csrf
                                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                    <input type="hidden" name="audio_message" id="audio_message">
                                    <textarea name="message" class="form-control message-input border-0 me-4 shadow-none" autofocus required
                                        placeholder="@lang('l.Type your message here...')" id="messageInput"></textarea>
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
                                            <div class="emoji-picker" id="emoji-picker" style="display: none; position: absolute; bottom: 100%;">
                                                <!-- emoji picker code in js -->
                                            </div>
                                        </div>
                                        <div class="attachment-container mx-1">
                                            <label for="attach-doc" class="btn btn-icon rounded-circle btn-outline-primary mb-0">
                                                <i class="bx bx-paperclip bx-sm"></i>
                                                <input type="file" id="attach-doc" name="attachment" hidden>
                                            </label>
                                        </div>
                                        <button class="btn btn-primary d-flex send-msg-btn mx-1" type="submit" id="sendMsgBtn">
                                            <span class="align-middle d-md-inline-block d-none">@lang('l.Send')</span>
                                            <i class="bx bx-paper-plane bx-sm ms-md-2 ms-0"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- /Chat History -->

                <div class="app-overlay"></div>
            </div>
        </div>
</div>
@endsection



@section('js')
    <script src="{{ asset('assets/themes/default/js/app-chat.js') }}"></script>

    <!-- Emoji Picker Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            const emojiButton = document.getElementById('emoji-button');
            const emojiPicker = document.getElementById('emoji-picker');
            const messageInput = document.querySelector('textarea[name="message"]');

            // إنشاء محتوى picker الايموجي
            let pickerHTML = '';
            for (let category in emojis) {
                pickerHTML += `
                    <div class="emoji-category">
                        <div class="emoji-category-title text-center">${category}</div>
                        <div class="emoji-list">
                            ${emojis[category].map(emoji => `<span class="emoji" data-emoji="${emoji}">${emoji}</span>`).join('')}
                        </div>
                    </div>
                `;
            }
            emojiPicker.innerHTML = pickerHTML;

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
                    // لا نغلق الـ picker تلقائياً للسماح باختيار المزيد من الايموجي
                });
            });

            document.addEventListener('click', function(e) {
                if (!emojiPicker.contains(e.target) && e.target !== emojiButton) {
                    emojiPicker.style.display = 'none';
                }
            });

            // منع إغلاق الـ picker عند النقر داخله
            emojiPicker.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>

    <!-- Get New Messages & Sound Notifications Script -->
    <script>
        let lastMessageId = {{ $ticket->ticketMessages->last()->id ?? 0 }};
        let isActive = true;
        let notificationEnabled = true;
        let audioContext;
        let audioBuffer;
        let notificationPermissionGranted = false;

        // تهيئة Web Audio API
        function initAudioContext() {
            try {
                window.AudioContext = window.AudioContext || window.webkitAudioContext;
                audioContext = new AudioContext();

                fetch('{{ asset('assets/themes/default/sounds/notification.mp3') }}')
                    .then(response => response.arrayBuffer())
                    .then(arrayBuffer => audioContext.decodeAudioData(arrayBuffer))
                    .then(buffer => {
                        audioBuffer = buffer;
                    })
                    .catch(e => console.error('فشل تحميل ملف الصوت:', e));
            } catch (e) {
                console.error('لا يدعم المتصفح Web Audio API:', e);
            }
        }

        // تشغيل الصوت باستخدام Web Audio API
        function playNotificationSoundWithAPI() {
            if (!audioContext || !audioBuffer) return false;

            try {
                if (audioContext.state === 'suspended') {
                    audioContext.resume();
                }

                const gainNode = audioContext.createGain();
                gainNode.gain.value = 1.0;

                const source = audioContext.createBufferSource();
                source.buffer = audioBuffer;
                source.connect(gainNode);
                gainNode.connect(audioContext.destination);

                source.start(0);
                return true;
            } catch (e) {
                return false;
            }
        }

        // تشغيل الصوت باستخدام عناصر HTML
        function playNotificationSoundWithElement() {
            // قائمة عناصر الصوت المتاحة
            const audioSources = [
                document.getElementById('notificationSound'),
                document.getElementById('backupSound'),
                document.getElementById('hiddenAudioTrigger')
            ];

            // تجربة كل عنصر صوت
            for (let sound of audioSources) {
                if (sound) {
                    try {
                        sound.currentTime = 0;
                        sound.volume = 1;
                        sound.muted = false;
                        sound.play().catch(() => {});
                    } catch (e) {}
                }
            }

            // محاولة أخيرة باستخدام عنصر جديد
            try {
                const newSound = new Audio('{{ asset('assets/themes/default/sounds/notification.mp3') }}');
                newSound.volume = 1;
                newSound.play().catch(() => {});
            } catch (e) {}

            return true;
        }

        // تشغيل الصوت بأي طريقة متاحة
        function playNotificationSound() {
            return playNotificationSoundWithAPI() || playNotificationSoundWithElement();
        }

        // فتح قفل الصوت عند تفاعل المستخدم
        function unlockAudio() {
            // تشغيل كل عناصر الصوت المتاحة
            [
                document.getElementById('notificationSound'),
                document.getElementById('backupSound'),
                document.getElementById('hiddenAudioTrigger')
            ].forEach(audio => {
                if (audio) {
                    audio.volume = 0.2;
                    audio.play().then(() => {
                        audio.pause();
                        audio.currentTime = 0;
                        audio.volume = 1;
                    }).catch(() => {});
                }
            });

            // تنشيط Web Audio API
            if (audioContext && audioContext.state === 'suspended') {
                audioContext.resume();
            }
        }

        // طلب إذن الإشعارات
        function requestNotificationPermission() {
            if (!("Notification" in window)) return;

            if (Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    notificationPermissionGranted = (permission === "granted");
                });
            } else {
                notificationPermissionGranted = (Notification.permission === "granted");
            }
        }

        // إرسال إشعار
        function sendNotification(message) {
            // محاولة تشغيل الصوت أولاً
            playNotificationSound();

            // إرسال إشعار إذا كان لدينا إذن
            if (notificationPermissionGranted && "Notification" in window) {
                try {
                    const notification = new Notification("رسالة جديدة", {
                        body: message || "لديك رسالة جديدة في التذكرة",
                        icon: "{{ asset('assets/themes/default/img/logo.png') }}",
                        silent: true
                    });

                    // تشغيل الصوت مرة أخرى عند ظهور الإشعار
                    notification.onshow = function() {
                        playNotificationSound();
                    };

                    // إغلاق الإشعار تلقائيًا
                    setTimeout(() => notification.close(), 5000);
                } catch (e) {}
            }

            // إرسال إشعار عبر Service Worker
            if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                navigator.serviceWorker.ready.then(registration => {
                    registration.active.postMessage({
                        type: 'NOTIFICATION',
                        message: message,
                        url: window.location.href
                    });
                }).catch(() => {});
            }

            // إرسال الإشعار عبر كل القنوات المتاحة
            sendCrossTabNotification(message);
        }

        // إرسال تنبيه عبر علامات التبويب المختلفة
        function sendCrossTabNotification(message) {
            // استخدام BroadcastChannel
            if ('BroadcastChannel' in window) {
                try {
                    const channel = new BroadcastChannel('ticket_notifications');
                    channel.postMessage({
                        type: 'new_message',
                        message: message,
                        timestamp: Date.now()
                    });
                } catch (e) {}
            }

            // استخدام localStorage
            try {
                localStorage.setItem('ticket_notification', JSON.stringify({
                    message: message,
                    timestamp: Date.now(),
                    ticket_id: {{ $ticket->id }}
                }));
            } catch (e) {}

            // إرسال حدث مخصص
            const event = new CustomEvent('ticketNotification', {
                detail: {
                    message: message
                }
            });
            window.dispatchEvent(event);
        }

        // تحديد حالة النشاط عند تغيير التبويب
        document.addEventListener('visibilitychange', function() {
            isActive = !document.hidden;

            if (isActive && audioContext && audioContext.state === 'suspended') {
                audioContext.resume();
            }
        });

        // فحص الرسائل الجديدة
        function checkNewMessages() {
            $.ajax({
                url: '{{ route('dashboard.users.tickets-get-new-messages') }}',
                method: 'GET',
                data: {
                    ticket_id: '{{ encrypt($ticket->id) }}',
                    last_message_id: lastMessageId
                },
                success: function(response) {
                    if (response.length > 0) {
                        let chatHistory = $('.chat-history-body');
                        let chatList = $('.chat-history');
                        let newMessages = false;
                        let lastNewMessage = null;

                        response.forEach(function(message) {
                            // تحديث آخر معرف للرسالة فقط إذا كانت الرسالة جديدة
                            if (message.id > lastMessageId) {
                                lastMessageId = message.id;
                                newMessages = true;
                                lastNewMessage = message;

                                // التحقق من عدم وجود الرسالة مسبقاً
                                if ($(`[data-message-id="${message.id}"]`).length === 0) {
                                    // معالجة الرسائل الصوتية
                                    let messageContent = '';
                                    if (message.content && message.content.startsWith('data:audio/')) {
                                        // إنشاء عنصر الرسالة الصوتية
                                        messageContent = `
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
                                    } else {
                                        messageContent = `<p class="mb-0">${message.content}</p>`;
                                    }

                                    let messageHtml = `
                                        <li class="chat-message ${message.is_user ? 'chat-message-right' : ''}" data-message-id="${message.id}">
                                            <div class="d-flex overflow-hidden">
                                                ${!message.is_user ? `
                                                        <div class="user-avatar flex-shrink-0 me-4">
                                                            <div class="avatar avatar-sm">
                                                                <img src="${message.user.photo}" alt="Avatar" class="rounded-circle">
                                                            </div>
                                                        </div>
                                                    ` : ''}
                                                <div class="chat-message-wrapper flex-grow-1">
                                                    <div class="chat-message-text">
                                                        ${messageContent}
                                                    </div>
                                                    ${message.attachment ? `
                                                            <div class="chat-message-attachment">
                                                                <a href="${message.attachment}" target="_blank">
                                                                    <i class="fa fa-eye"></i> @lang('l.Show Attachment')
                                                                </a>
                                                            </div>
                                                        ` : ''}
                                                    <div class="text-end text-muted mt-1">
                                                        ${message.is_user ?
                                                        `<i class="bx bx-check-double bx-16px text-success me-1"></i>` : ''}
                                                        <small>${message.created_at}</small>
                                                    </div>
                                                </div>
                                                ${message.is_user ? `
                                                        <div class="user-avatar flex-shrink-0 ms-4">
                                                            <div class="avatar avatar-sm">
                                                                <img src="${message.user.photo}" alt="Avatar" class="rounded-circle">
                                                            </div>
                                                        </div>
                                                    ` : ''}
                                            </div>
                                        </li>
                                    `;

                                    chatList.append(messageHtml);
                                    initializeVoiceMessagePlayers();

                                    // تمرير تلقائي إلى أسفل بعد إضافة رسالة جديدة
                                    chatHistory.scrollTop(chatHistory[0].scrollHeight);
                                }
                            }
                        });

                        // إرسال إشعار إذا كانت هناك رسائل جديدة والتنبيهات مفعلة
                        if (newMessages && notificationEnabled) {
                            // تحديد نص الرسالة
                            let messageText = lastNewMessage.content;
                            if (messageText && messageText.startsWith('data:audio/')) {
                                messageText = "رسالة صوتية جديدة";
                            }

                            // إرسال تنبيه إذا كانت النافذة غير نشطة
                            if (!isActive) {
                                sendNotification(messageText);
                            } else {
                                // تشغيل الصوت فقط إذا كانت النافذة نشطة
                                playNotificationSound();
                            }
                        }
                    }
                },
                error: function(xhr) {
                    console.error('خطأ في جلب الرسائل الجديدة:', xhr);
                }
            });
        }

        // تنفيذ التحقق من الرسائل كل 5 ثوانٍ
        setInterval(checkNewMessages, 5000);

        // تهيئة ميزة التنبيهات الصوتية
        function initializeSoundNotifications() {
            // إضافة زر تفعيل/إيقاف الإشعارات الصوتية - تم إخفاؤه
            const chatHeader = document.querySelector('.chat-history-header .d-flex:first-child');
            if (chatHeader) {
                // تم تغيير طريقة إضافة الزر حتى لا يؤثر على تخطيط الصفحة
                const notificationBtn = document.createElement('span');
                notificationBtn.style.display = 'none'; // إخفاء العنصر تماماً
                notificationBtn.innerHTML = `
                    <button class="btn btn-icon btn-sm" id="toggleNotification" style="display: none;" title="@lang('l.Toggle Sound Notifications')">
                        <i class="bx bx-bell"></i>
                    </button>
                `;
                document.body.appendChild(notificationBtn); // إضافته للـ body بدلاً من header

                // تفعيل الإشعارات تلقائياً دون إظهار أي عناصر
                const toggleBtn = document.getElementById('toggleNotification');
                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function() {
                        notificationEnabled = !notificationEnabled;
                        if (notificationEnabled) {
                            requestNotificationPermission();
                            unlockAudio();
                        }
                    });
                }
            }

            // تفعيل الإشعارات تلقائياً
            requestNotificationPermission();
            unlockAudio();
        }

        // تهيئة استماع الإشعارات عبر علامات التبويب
        function setupCrossTabNotifications() {
            // استماع للتغييرات عبر localStorage
            window.addEventListener('storage', function(e) {
                if (e.key === 'ticket_notification') {
                    try {
                        const data = JSON.parse(e.newValue);
                        if (data && data.timestamp > Date.now() - 5000) {
                            playNotificationSound();
                        }
                    } catch (e) {}
                }
            });

            // استماع للإشعارات عبر BroadcastChannel
            if ('BroadcastChannel' in window) {
                const channel = new BroadcastChannel('ticket_notifications');
                channel.onmessage = function(event) {
                    if (event.data && event.data.type === 'new_message') {
                        playNotificationSound();
                    }
                };
            }

            // استماع لأحداث مخصصة
            window.addEventListener('ticketNotification', function() {
                playNotificationSound();

                // تشغيل الاهتزاز إن أمكن
                if ('vibrate' in navigator) {
                    navigator.vibrate([200, 100, 200]);
                }
            });
        }

        // تهيئة Service Worker
        function setupServiceWorker() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('{{ asset('sw.js') }}').then(
                    function(registration) {
                        console.log('تم تسجيل Service Worker بنجاح');
                    }
                ).catch(function(error) {
                    console.error('فشل تسجيل Service Worker:', error);
                });
            }
        }

        // تهيئة المكونات عند تحميل الصفحة
        $(document).ready(function() {
            // إنشاء عنصر صوت خفي للتنشيط
            if (!document.getElementById('hiddenAudioTrigger')) {
                const hiddenAudio = document.createElement('audio');
                hiddenAudio.id = 'hiddenAudioTrigger';
                hiddenAudio.src = '{{ asset('assets/themes/default/sounds/notification.mp3') }}';
                hiddenAudio.preload = 'auto';
                document.body.appendChild(hiddenAudio);
            }

            // تهيئة السياق الصوتي
            initAudioContext();

            // طلب إذن الإشعارات
            requestNotificationPermission();

            // إعداد Service Worker
            setupServiceWorker();

            // إعداد استماع الإشعارات عبر علامات التبويب
            setupCrossTabNotifications();

            // التمرير إلى آخر رسالة
            let chatHistory = $('.chat-history-body');
            chatHistory.scrollTop(chatHistory[0].scrollHeight);

            // تهيئة مشغلات الرسائل الصوتية
            initializeVoiceMessagePlayers();

            // تهيئة ميزة التنبيهات الصوتية
            initializeSoundNotifications();

            // تفعيل الصوت عند تفاعل المستخدم مع الصفحة
            document.body.addEventListener('click', unlockAudio, {
                once: true
            });
        });
    </script>

    <!-- Voice Recorder Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // الحصول على العناصر
            const voiceRecorderBtn = document.getElementById('voiceRecorderBtn');
            const voiceTimer = document.getElementById('voiceTimer');
            const voiceActions = document.getElementById('voiceActions');
            const sendVoiceBtn = document.getElementById('sendVoiceBtn');
            const cancelVoiceBtn = document.getElementById('cancelVoiceBtn');
            const messageInput = document.getElementById('messageInput');
            const audioMessageInput = document.getElementById('audio_message');
            const chatForm = document.getElementById('chatForm');
            const sendMsgBtn = document.getElementById('sendMsgBtn');

            // متغيرات للتسجيل
            let mediaRecorder;
            let audioChunks = [];
            let startTime;
            let timerInterval;
            let isRecording = false;
            let isAudioMessageReady = false;

            // فحص دعم المتصفح للتسجيل الصوتي
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                voiceRecorderBtn.style.display = 'none';
                console.error('@lang('l.Voice recording is not supported in this browser')');
                return;
            }

            // بدء/إيقاف التسجيل عند النقر على زر الميكروفون
            voiceRecorderBtn.addEventListener('click', function() {
                if (!isRecording) {
                    startRecording();
                } else {
                    stopRecording();
                    voiceActions.classList.add('show');
                }
            });

            // إرسال التسجيل
            sendVoiceBtn.addEventListener('click', function() {
                if (audioMessageInput.value) {
                    isAudioMessageReady = true;
                    // إعادة تفعيل زر الإرسال قبل تقديم النموذج
                    if (sendMsgBtn) {
                        sendMsgBtn.disabled = false;
                    }
                    chatForm.submit();
                }
            });

            // إلغاء التسجيل
            cancelVoiceBtn.addEventListener('click', function() {
                resetRecording();
            });

            // بدء التسجيل
            function startRecording() {
                // طلب الوصول إلى الميكروفون
                navigator.mediaDevices.getUserMedia({
                        audio: true
                    })
                    .then(function(stream) {
                        // تحديث واجهة المستخدم
                        isRecording = true;
                        voiceRecorderBtn.classList.add('recording');
                        voiceTimer.style.display = 'inline-block';
                        messageInput.setAttribute('disabled', 'disabled');
                        audioChunks = [];

                        // تعطيل زر إرسال الرسالة النصية أثناء التسجيل
                        if (sendMsgBtn) {
                            sendMsgBtn.disabled = true;
                        }

                        // إنشاء مسجل الوسائط
                        mediaRecorder = new MediaRecorder(stream);

                        // جمع البيانات المسجلة
                        mediaRecorder.ondataavailable = function(event) {
                            if (event.data.size > 0) {
                                audioChunks.push(event.data);
                            }
                        };

                        // معالجة نهاية التسجيل
                        mediaRecorder.onstop = function() {
                            // تحويل البيانات المسجلة إلى blob ثم إلى base64
                            const audioBlob = new Blob(audioChunks, {
                                type: 'audio/webm'
                            });
                            const reader = new FileReader();
                            reader.readAsDataURL(audioBlob);
                            reader.onloadend = function() {
                                const base64data = reader.result;
                                audioMessageInput.value = base64data;
                            };

                            // إيقاف جميع المسارات
                            stream.getTracks().forEach(track => track.stop());
                        };

                        // بدء التسجيل
                        mediaRecorder.start();

                        // بدء المؤقت
                        startTime = new Date();
                        updateTimer();
                        timerInterval = setInterval(updateTimer, 1000);
                    })
                    .catch(function(err) {
                        console.error('@lang('l.Error accessing microphone'): ', err);
                        alert('@lang('l.Could not access microphone. Please check permissions.')');

                        // إعادة تفعيل زر الإرسال في حالة حدوث خطأ
                        if (sendMsgBtn) {
                            sendMsgBtn.disabled = false;
                        }
                    });
            }

            // إيقاف التسجيل
            function stopRecording() {
                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    isRecording = false;
                    voiceRecorderBtn.classList.remove('recording');
                    clearInterval(timerInterval);
                    mediaRecorder.stop();
                }
            }

            // إعادة ضبط حالة التسجيل
            function resetRecording() {
                isRecording = false;
                isAudioMessageReady = false;
                voiceRecorderBtn.classList.remove('recording');
                voiceTimer.style.display = 'none';
                voiceActions.classList.remove('show');
                messageInput.removeAttribute('disabled');
                audioMessageInput.value = '';
                clearInterval(timerInterval);

                // إعادة تفعيل زر الإرسال عند إلغاء التسجيل
                if (sendMsgBtn) {
                    sendMsgBtn.disabled = false;
                }

                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    mediaRecorder.stop();
                }
            }

            // تحديث المؤقت
            function updateTimer() {
                const now = new Date();
                const diff = (now - startTime) / 1000;
                const minutes = Math.floor(diff / 60).toString().padStart(2, '0');
                const seconds = Math.floor(diff % 60).toString().padStart(2, '0');
                voiceTimer.textContent = `${minutes}:${seconds}`;

                // إيقاف التسجيل بعد 3 دقائق
                if (diff >= 180) {
                    stopRecording();
                    voiceActions.classList.add('show');
                }
            }

            // التحقق من وجود رسالة قبل الإرسال
            chatForm.addEventListener('submit', function(e) {
                // إذا كان هناك رسالة صوتية جاهزة للإرسال
                if (isAudioMessageReady && audioMessageInput.value) {
                    return true; // السماح بإرسال النموذج
                }

                // إذا كان هناك نص مكتوب
                if (messageInput.value.trim()) {
                    return true; // السماح بإرسال النموذج
                }

                // لا توجد رسالة نصية أو صوتية
                if (!audioMessageInput.value && !messageInput.value.trim()) {
                    e.preventDefault();
                    alert('@lang('l.Please type a message or record a voice message')');
                    return false;
                }
            });
        });

        // إنشاء عنصر الرسالة الصوتية
        function createVoiceMessageElement(audioData) {
            const randomId = 'vm-' + Math.random().toString(36).substr(2, 9);
            return `
                <div class="voice-message" data-audio="${audioData}">
                    <div class="voice-message-play" data-voice-id="${randomId}">
                        <i class="bx bx-play"></i>
                    </div>
                    <div class="voice-message-wave">
                        <div class="wave-container" id="${randomId}">
                            ${Array(20).fill().map(() =>
                                `<div class="wave-bar" style="height: ${Math.floor(Math.random() * 80) + 10}%;"></div>`
                            ).join('')}
                        </div>
                    </div>
                    <div class="voice-message-duration">00:00</div>
                </div>
            `;
        }

        // تهيئة مشغلات الرسائل الصوتية
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

                        // إنشاء عنصر الصوت إذا لم يكن موجودًا
                        let audioElement = document.getElementById('audio-' + voiceId);
                        if (!audioElement) {
                            audioElement = document.createElement('audio');
                            audioElement.id = 'audio-' + voiceId;
                            audioElement.src = audioData;
                            audioElement.style.display = 'none';
                            document.body.appendChild(audioElement);

                            // تحديث مدة الصوت عند تحميله
                            audioElement.addEventListener('loadedmetadata', function() {
                                const minutes = Math.floor(audioElement.duration / 60).toString()
                                    .padStart(2, '0');
                                const seconds = Math.floor(audioElement.duration % 60).toString()
                                    .padStart(2, '0');
                                durationEl.textContent = `${minutes}:${seconds}`;
                            });

                            // تحديث الموجة أثناء التشغيل
                            audioElement.addEventListener('timeupdate', function() {
                                const progressPercent = (audioElement.currentTime / audioElement
                                    .duration);

                                // تحديث عرض شريط الموجة المنقضي
                                waveBars.forEach((bar, index) => {
                                    const barIndex = index / waveBars.length;
                                    if (barIndex < progressPercent) {
                                        bar.style.backgroundColor = '#52c41a';
                                    } else {
                                        bar.style.backgroundColor =
                                            '{{ $settings['primary_color'] }}';
                                    }
                                });

                                // تحديث النص المعروض للوقت المنقضي
                                const currentMinutes = Math.floor(audioElement.currentTime / 60)
                                    .toString().padStart(2, '0');
                                const currentSeconds = Math.floor(audioElement.currentTime % 60)
                                    .toString().padStart(2, '0');
                                durationEl.textContent = `${currentMinutes}:${currentSeconds}`;
                            });

                            // إعادة تعيين الموجة عند انتهاء التشغيل
                            audioElement.addEventListener('ended', function() {
                                waveBars.forEach(bar => {
                                    bar.style.backgroundColor =
                                        '{{ $settings['primary_color'] }}';
                                });
                                playButton.innerHTML = '<i class="bx bx-play"></i>';

                                // إعادة مدة الصوت الكاملة
                                const minutes = Math.floor(audioElement.duration / 60).toString()
                                    .padStart(2, '0');
                                const seconds = Math.floor(audioElement.duration % 60).toString()
                                    .padStart(2, '0');
                                durationEl.textContent = `${minutes}:${seconds}`;
                            });
                        }

                        // تبديل بين تشغيل وإيقاف الصوت
                        if (audioElement.paused) {
                            // إيقاف جميع الملفات الصوتية الأخرى قبل التشغيل
                            document.querySelectorAll('audio').forEach(audio => {
                                if (audio.id !== 'audio-' + voiceId && !audio.paused) {
                                    audio.pause();
                                    const otherId = audio.id.replace('audio-', '');
                                    const otherPlayButton = document.querySelector(
                                        `[data-voice-id="${otherId}"]`);
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
    </script>
@endsection
