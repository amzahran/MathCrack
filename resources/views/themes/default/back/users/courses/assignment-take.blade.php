@extends('themes.default.layouts.back.student-master')

@section('title')
    {{ $studentAssignment->lectureAssignment->title }} - @lang('l.assignment')
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/back/css/assignment-take.css') }}">
    <!-- MathJax Configuration - Rebuilt -->
        <script>
        window.MathJax = {
            tex: {
                inlineMath: [
                    ['$', '$'],
                    ['\\(', '\\)']
                ],
                displayMath: [
                    ['$$', '$$'],
                    ['\\[', '\\]']
                ],
                processEscapes: true
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code'],
                processHtmlClass: 'tex2jax_process'
            },
            chtml: {
                displayAlign: 'center'
            },
            startup: {
                ready: () => {
                    MathJax.startup.defaultReady();
                    MathJax.typesetPromise();
                    console.log("✅ MathJax ready and centered.");
                }
            }
        };
    </script>
    <script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <style>
        /* Custom animations and responsive adjustments */
        .question-card {
            animation: fadeInUp 0.5s ease;
        }

        .question-text {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        mjx-container[display="true"] {
            display: block !important;
            text-align: center !important;
            margin: 1em auto !important;
        }

        /* MathJax styling - Simplified */
        mjx-container {
            display: inline-block !important;
            margin: 2px 4px !important;
        }

        .question-text mjx-container {
            font-size: 1.1em !important;
        }

        .option-text mjx-container {
            font-size: 1em !important;
        }

        /* Ensure visibility */
        .question-text, .option-text {
            line-height: 1.6 !important;
        }

        .option-image {
            max-width: 100px;
            max-height: 100px;
            margin-top: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* تحسين الأزرار */
        .action-buttons-container .btn {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .action-buttons-container .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-save-progress {
            position: relative;
        }

        .btn-save-progress .text-success {
            color: #28a745 !important;
        }

        .btn-save-progress .text-warning {
            color: #ffc107 !important;
        }

        /* تحسين responsive للأزرار */
        @media (max-width: 768px) {
            .action-buttons-container {
                flex-direction: column;
                width: 100%;
            }

            .action-buttons-container .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .question-text {
                font-size: 1rem;
            }

            .option-text {
                font-size: 0.9rem;
            }

            .MathJax {
                font-size: 1em !important;
            }
        }

        /* ألوان شريط التنقل بين الأسئلة */
        .question-indicator.answered {
            background-color: #28a745 !important;
            color: white !important;
            border-color: #28a745 !important;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <!-- Assignment Header -->
            <div class="assignment-header">
                <div class="assignment-content">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="mb-3">{{ $studentAssignment->lectureAssignment->title }}</h1>
                            <p class="mb-3 opacity-90">{{ $studentAssignment->lectureAssignment->description ?? __('l.no_description_available') }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i class="fas fa-book me-1"></i>{{ $studentAssignment->lectureAssignment->lecture->course->name }}
                                </span>
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i class="fas fa-video me-1"></i>{{ $studentAssignment->lectureAssignment->lecture->name }}
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="d-flex flex-column align-items-end">
                            @if ($studentAssignment->lectureAssignment->time_limit)
                                    <div class="timer-container mb-3">
                                    <div class="timer-display" id="timer">--:--</div>
                                    <div class="timer-label">@lang('l.time_remaining')</div>
                                </div>
                            @else
                                    <div class="timer-container mb-3">
                                    <div class="timer-display">∞</div>
                                    <div class="timer-label">@lang('l.no_time_limit')</div>
                                </div>
                            @endif

                                <!-- Action Buttons -->
                                <div class="action-buttons-container d-flex gap-2">
                                    <button class="btn btn-outline-warning btn-save-progress" onclick="saveProgress(true)" title="@lang('l.save_progress')">
                                        <i class="fas fa-save me-1"></i>@lang('l.save_progress')
                                    </button>
                                    <button class="btn btn-success" onclick="submitAssignment()" title="@lang('l.submit_assignment')">
                                        <i class="fas fa-check me-1"></i>@lang('l.submit_assignment')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill" style="width: 0%"></div>
                </div>
                <div class="progress-text">
                    <span id="progress-text">@lang('l.question') 1 @lang('l.of') {{ $studentAssignment->lectureAssignment->questions->count() }}</span>
                </div>
            </div>


            <!-- Questions Container -->
            <div id="questions-container">
                @foreach ($studentAssignment->lectureAssignment->questions as $index => $question)
                    <div class="question-card question-item" id="question-{{ $index }}"
                         data-type="{{ $question->type }}"
                         data-question-id="{{ $question->id }}"
                         style="display: {{ $index === 0 ? 'block' : 'none' }}">
                        <div class="question-number">{{ $index + 1 }}</div>

                        <div class="question-text">
                            {!! nl2br($question->question_text) !!}
                        </div>

                        @if ($question->question_image)
                            <div class="question-image-container">
                                <img src="{{ asset($question->question_image) }}"
                                     alt="Question Image"
                                     class="question-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="image-error" style="display: none; text-align: center; padding: 2rem; color: #666;">
                                    <i class="fas fa-image fa-3x mb-3"></i>
                                    <p>@lang('l.image_not_available')</p>
                                </div>
                            </div>
                        @endif

                        <div class="options-container">
                            @if ($question->type === 'mcq')
                                @foreach ($question->options as $optionIndex => $option)
                                    <label class="option-item" for="option-{{ $question->id }}-{{ $option->id }}">
                                        <input type="radio"
                                               class="option-radio"
                                               id="option-{{ $question->id }}-{{ $option->id }}"
                                               name="question-{{ $question->id }}"
                                               value="{{ $option->id }}"
                                               data-question="{{ $index }}"
                                               onchange="saveAnswer({{ $index }}, {{ $option->id }})">
                                        <div class="option-content">
                                            <div class="option-letter">{{ chr(65 + $optionIndex) }}</div>
                                            <div class="option-text">{!! $option->option_text !!}</div>
                                            @if ($option->option_image)
                                                <img src="{{ asset($option->option_image) }}" class="option-image" alt="Option Image">
                                            @endif
                                        </div>
                                    </label>
                                @endforeach

                            @elseif ($question->type === 'tf')
                                <div class="tf-options">
                                    <label class="tf-option tf-true" for="tf-true-{{ $question->id }}">
                                        <input type="radio"
                                               class="option-radio"
                                               id="tf-true-{{ $question->id }}"
                                               name="question-{{ $question->id }}"
                                               value="true"
                                               data-question="{{ $index }}"
                                               onchange="saveAnswer({{ $index }}, 'true')">
                                        <div class="tf-icon">✓</div>
                                        <div class="option-text">@lang('l.true')</div>
                                    </label>

                                    <label class="tf-option tf-false" for="tf-false-{{ $question->id }}">
                                        <input type="radio"
                                               class="option-radio"
                                               id="tf-false-{{ $question->id }}"
                                               name="question-{{ $question->id }}"
                                               value="false"
                                               data-question="{{ $index }}"
                                               onchange="saveAnswer({{ $index }}, 'false')">
                                        <div class="tf-icon">✗</div>
                                        <div class="option-text">@lang('l.false')</div>
                                    </label>
                                </div>

                            @elseif ($question->type === 'essay')
                                <textarea class="essay-answer"
                                          placeholder="@lang('l.write_your_answer_here')"
                                          data-question="{{ $index }}"
                                          onblur="saveAnswer({{ $index }}, this.value)"></textarea>

                            @elseif ($question->type === 'numeric')
                                <div class="numeric-input-container">
                                    <div class="numeric-input-wrapper">
                                        <input type="text"
                                               class="numeric-answer"
                                               placeholder="@lang('l.enter_your_answer')"
                                               data-question="{{ $index }}"
                                               onblur="saveAnswer({{ $index }}, this.value)"
                                               onkeypress="handleNumericInput(event, {{ $index }})"
                                               oninput="validateNumericInput(this, {{ $index }})">
                                        <div class="numeric-buttons">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addToNumericInput('{{ $index }}', '.')">.</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addToNumericInput('{{ $index }}', '-')">-</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addToNumericInput('{{ $index }}', '/')">/</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addToNumericInput('{{ $index }}', '*')">×</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addToNumericInput('{{ $index }}', '+')">+</button>
                                        </div>
                                    </div>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        @lang('l.numeric_input_help_extended')
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Question Navigation -->
            <div class="question-navigation">
                <div class="nav-buttons">
                    <button class="btn btn-outline-primary btn-custom" id="prev-btn" onclick="previousQuestion()">
                        <i class="fas fa-chevron-left me-2"></i>@lang('l.previous')
                    </button>

                    <div class="question-indicators" id="question-indicators">
                        @foreach ($studentAssignment->lectureAssignment->questions as $index => $question)
                            <div class="question-indicator {{ $index === 0 ? 'current' : '' }}"
                                 onclick="goToQuestion({{ $index }})"
                                 data-question="{{ $index }}">
                                {{ $index + 1 }}
                            </div>
                        @endforeach
                    </div>

                    <button class="btn btn-outline-primary btn-custom" id="next-btn" onclick="nextQuestion()">
                        @lang('l.next')<i class="fas fa-chevron-right ms-2"></i>
                    </button>
                </div>
            </div>
            <!-- Action Buttons - Moved to better position -->
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentQuestion = 0;
        let totalQuestions = {{ $studentAssignment->lectureAssignment->questions->count() }};
        let answers = {};
        let timeLimit = {{ $studentAssignment->lectureAssignment->time_limit ?? 0 }};
        let startTime = new Date('{{ $studentAssignment->started_at }}');
        let timerInterval;
        let timeWarningShown = false;
        let questionIds = @json($studentAssignment->lectureAssignment->questions->pluck('id'));

        // تحميل الإجابات المحفوظة
        @foreach ($studentAssignment->answers as $answer)
            answers[{{ $answer->lecture_question_id }}] = '{{ $answer->answer_text ?? $answer->selected_option_id }}';
            console.log('Loaded answer from DB:', {
                questionId: {{ $answer->lecture_question_id }},
                answer: '{{ $answer->answer_text ?? $answer->selected_option_id }}'
            });
        @endforeach

        console.log('All loaded answers from database:', answers);

        // منع استخدام زر الماوس الأيمن
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // منع استخدام مفاتيح الاختصار
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                return false;
            }
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.keyCode === 73)) {
                e.preventDefault();
                return false;
            }
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'C' || e.key === 'c' || e.keyCode === 67)) {
                e.preventDefault();
                return false;
            }
            if ((e.ctrlKey || e.metaKey) && (e.key === 'U' || e.key === 'u' || e.keyCode === 85)) {
                e.preventDefault();
                return false;
            }
        });

        // منع السحب والإفلات
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
        });

        // بدء المؤقت
        if (timeLimit > 0) {
            startTimer();
        }

        function startTimer() {
            timerInterval = setInterval(function() {
                let now = new Date();
                let elapsedSeconds = Math.floor((now - startTime) / 1000);
                let remainingSeconds = (timeLimit * 60) - elapsedSeconds;

                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    Swal.fire({
                        title: '@lang("l.time_expired")',
                        text: '@lang("l.assignment_will_be_submitted_automatically")',
                        icon: 'warning',
                        confirmButtonText: '@lang("l.ok")',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        submitAnswers();
                    });
                    return;
                }

                let hours = Math.floor(remainingSeconds / 3600);
                let minutes = Math.floor((remainingSeconds % 3600) / 60);
                let seconds = remainingSeconds % 60;

                let display = '';
                if (hours > 0) {
                    display = hours + ':' + (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                } else {
                    display = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                }

                let timerElement = document.getElementById('timer');
                if (timerElement) {
                    timerElement.textContent = display;

                    // تغيير اللون حسب الوقت المتبقي
                    if (remainingSeconds <= 300) { // 5 دقائق أو أقل
                        timerElement.style.color = '#f44336';
                        timerElement.style.animation = 'pulse 1s infinite';

                        // تحذير نهائي
                        if (!timeWarningShown && remainingSeconds <= 300) {
                            timeWarningShown = true;
                            Swal.fire({
                                title: '@lang("l.final_warning")',
                                text: '@lang("l.only_5_minutes_left")',
                                icon: 'warning',
                                timer: 5000,
                                showConfirmButton: false
                            });
                        }
                    } else if (remainingSeconds <= 600) { // 10 دقائق أو أقل
                        timerElement.style.color = '#ff9800';

                        // تحذير أولي
                        if (!timeWarningShown && remainingSeconds <= 600) {
                            timeWarningShown = true;
                            Swal.fire({
                                title: '@lang("l.time_warning")',
                                text: '@lang("l.only_10_minutes_left")',
                                icon: 'info',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        timerElement.style.color = 'white';
                        timerElement.style.animation = 'none';
                    }
                }
            }, 1000);
        }

        function saveAnswer(questionIndex, answer) {
            let questionId = questionIds[questionIndex];
            console.log('saveAnswer called:', {
                questionIndex: questionIndex,
                questionId: questionId,
                answer: answer,
                answerType: typeof answer
            });

            answers[questionId] = answer;
            console.log('Updated answers object:', answers);

            updateQuestionIndicator(questionIndex, true);
            updateNavigationColors(); // تحديث ألوان التنقل

            // حفظ في localStorage كنسخة احتياطية
            try {
                localStorage.setItem('assignment_progress_{{ $studentAssignment->id }}', JSON.stringify(answers));
                console.log('Saved to localStorage');
            } catch (e) {
                console.error('Failed to save to localStorage:', e);
            }

            // إضافة تأثير بصري للإجابة
            let questionCard = document.getElementById(`question-${questionIndex}`);
            if (questionCard) {
                questionCard.style.borderColor = '#4caf50';
                questionCard.style.boxShadow = '0 0 10px rgba(76, 175, 80, 0.3)';

                setTimeout(() => {
                    questionCard.style.borderColor = '#e3f2fd';
                    questionCard.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
                }, 1000);
            }
        }

        function updateQuestionIndicator(questionIndex, answered) {
            let indicator = document.querySelector(`[data-question="${questionIndex}"]`);
            if (indicator) {
                indicator.classList.remove('current', 'answered');
                if (answered) {
                    indicator.classList.add('answered');
                } else if (questionIndex === currentQuestion) {
                    indicator.classList.add('current');
                }
            }
        }

        // دالة لتحديث ألوان شريط التنقل
        function updateNavigationColors() {
            document.querySelectorAll('.question-indicator').forEach((indicator, index) => {
                let questionId = questionIds[index];
                indicator.classList.remove('answered');

                // إضافة لون أخضر للمجاب عليه
                if (answers[questionId] !== undefined && answers[questionId] !== null && answers[questionId] !== '') {
                    indicator.classList.add('answered');
                }
            });
        }

                function showQuestion(questionIndex) {
            console.log('Showing question:', questionIndex);

            // إخفاء جميع الأسئلة
            document.querySelectorAll('.question-item').forEach(item => {
                item.style.display = 'none';
                item.style.opacity = '0';
            });

            // إظهار السؤال المطلوب
            let targetQuestion = document.getElementById(`question-${questionIndex}`);
            if (targetQuestion) {
                console.log('Found target question:', targetQuestion);

                targetQuestion.style.display = 'block';
                targetQuestion.style.opacity = '0';
                targetQuestion.style.transform = 'translateY(20px)';

                // إعادة تحميل MathJax أولاً ثم عرض السؤال
                updateMathJaxForQuestion(targetQuestion).then(() => {
                    console.log('MathJax updated for question', questionIndex);

                    // تأثير ظهور السؤال بعد تحديث MathJax
                                                setTimeout(() => {
                    targetQuestion.style.transition = 'all 0.5s ease';
                    targetQuestion.style.opacity = '1';
                    targetQuestion.style.transform = 'translateY(0)';
                }, 100);
                }).catch(err => {
                    console.error('MathJax update failed:', err);
                    // عرض السؤال حتى لو فشل MathJax
                    setTimeout(() => {
                        targetQuestion.style.transition = 'all 0.5s ease';
                        targetQuestion.style.opacity = '1';
                        targetQuestion.style.transform = 'translateY(0)';
                    }, 100);
                });
            } else {
                console.error('Target question not found for index:', questionIndex);
                console.log('Available questions:', document.querySelectorAll('.question-item'));
            }

            // تحديث المؤشرات
            document.querySelectorAll('.question-indicator').forEach((indicator, index) => {
                indicator.classList.remove('current');
                if (index === questionIndex) {
                    indicator.classList.add('current');
                }
            });

            // تحديث أزرار التنقل
            let prevBtn = document.getElementById('prev-btn');
            let nextBtn = document.getElementById('next-btn');

            if (prevBtn) prevBtn.disabled = questionIndex === 0;
            if (nextBtn) nextBtn.disabled = questionIndex === totalQuestions - 1;

            // تحديث شريط التقدم
            let progressFill = document.getElementById('progress-fill');
            let progressText = document.getElementById('progress-text');

            if (progressFill && progressText) {
                let progress = ((questionIndex + 1) / totalQuestions) * 100;
                progressFill.style.width = progress + '%';
                progressText.textContent = '@lang("l.question") ' + (questionIndex + 1) + ' @lang("l.of") ' + totalQuestions;
            }

            currentQuestion = questionIndex;
        }

        function nextQuestion() {
            if (currentQuestion < totalQuestions - 1) {
                showQuestion(currentQuestion + 1);
            }
        }

        function previousQuestion() {
            if (currentQuestion > 0) {
                showQuestion(currentQuestion - 1);
            }
        }

        function goToQuestion(questionIndex) {
            showQuestion(questionIndex);
        }

        function saveProgress(showNotification = false) {
            // حفظ التقدم في localStorage
            localStorage.setItem('assignment_progress_{{ $studentAssignment->id }}', JSON.stringify(answers));

            // حفظ التقدم في قاعدة البيانات عبر AJAX
            fetch('{{ route("dashboard.users.assignments-save-progress") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: '{{ encrypt($studentAssignment->id) }}',
                    answers: answers
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && showNotification) {
                    // عرض رسالة نجاح فقط عند الطلب
                    Swal.fire({
                        title: '@lang("l.success")',
                        text: '@lang("l.progress_saved_successfully")',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }

                // تحديث مؤشر الحفظ بصمت
                updateSaveIndicator(true);
            })
            .catch(error => {
                console.error('Error saving progress:', error);
                updateSaveIndicator(false);

                if (showNotification) {
                Swal.fire({
                    title: '@lang("l.warning")',
                    text: '@lang("l.progress_saved_locally")',
                    icon: 'warning',
                    timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        }

        // دالة لتحديث مؤشر الحفظ
        function updateSaveIndicator(success) {
            const saveBtn = document.querySelector('.btn-save-progress');
            if (saveBtn) {
                const icon = saveBtn.querySelector('i');
                if (success) {
                    icon.className = 'fas fa-check me-2 text-success';
                    setTimeout(() => {
                        icon.className = 'fas fa-save me-2';
                    }, 2000);
                } else {
                    icon.className = 'fas fa-exclamation-triangle me-2 text-warning';
                    setTimeout(() => {
                        icon.className = 'fas fa-save me-2';
                    }, 3000);
                }
            }
        }

        function submitAssignment() {
            // التحقق من الإجابات
            let answeredCount = Object.keys(answers).length;
            let unansweredCount = totalQuestions - answeredCount;

            let confirmText = '@lang("l.are_you_sure_you_want_to_submit")';
            if (unansweredCount > 0) {
                confirmText = `هل أنت متأكد من رغبتك في تسليم الواجب؟\n\nلديك ${unansweredCount} أسئلة لم تجب عليها.`;
            }

            Swal.fire({
                title: '@lang("l.confirm_submission")',
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4caf50',
                cancelButtonColor: '#d33',
                confirmButtonText: '@lang("l.yes_submit")',
                cancelButtonText: '@lang("l.cancel")'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAnswers();
                }
            });
        }

        function submitAnswers() {
            console.log('submitAnswers called');
            console.log('Answers object:', answers);
            console.log('Answers count:', Object.keys(answers).length);

            // التحقق من وجود إجابات
            if (Object.keys(answers).length === 0) {
                Swal.fire({
                    title: '@lang("l.error")',
                    text: '@lang("l.no_answers_provided")',
                    icon: 'warning'
                });
                return;
            }

            // إظهار مؤشر التحميل
            Swal.fire({
                title: '@lang("l.submitting")',
                text: '@lang("l.please_wait")',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const requestData = {
                id: '{{ encrypt($studentAssignment->id) }}',
                answers: answers
            };

            console.log('Request data:', requestData);

            // إرسال الإجابات
            fetch('{{ route("dashboard.users.assignments-submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Response error text:', text);
                        throw new Error(`HTTP ${response.status}: ${text}`);
                    });
                }

                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);

                if (data.success) {
                    Swal.fire({
                        title: '@lang("l.success")',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    Swal.fire({
                        title: '@lang("l.error")',
                        text: data.error || '@lang("l.something_went_wrong")',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Full error object:', error);
                console.error('Error message:', error.message);

                Swal.fire({
                    title: '@lang("l.error")',
                    text: error.message || '@lang("l.something_went_wrong")',
                    icon: 'error'
                });
            });
        }

                // تحميل الإجابات المحفوظة عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded - Initial setup');
            console.log('Initial answers from server:', answers);
            console.log('Question IDs:', questionIds);

            // محاولة استعادة الإجابات من localStorage أولاً
            let savedProgress = localStorage.getItem('assignment_progress_{{ $studentAssignment->id }}');
            if (savedProgress) {
                try {
                    let savedAnswers = JSON.parse(savedProgress);
                    console.log('Loaded answers from localStorage:', savedAnswers);
                    // دمج الإجابات المحفوظة مع الإجابات من قاعدة البيانات
                    answers = { ...answers, ...savedAnswers };
                    console.log('Merged answers:', answers);
                } catch (e) {
                    console.error('Error parsing saved progress:', e);
                }
            }

            // تهيئة بسيطة للمعادلات
            function initMath() {
                if (window.MathJax && window.MathJax.typesetPromise) {
                    console.log('🚀 Starting MathJax...');
                    MathJax.typesetPromise().then(() => {
                        console.log('✅ MathJax initialized');
                    });
                } else {
                    setTimeout(initMath, 300);
                }
            }
            initMath();

            // تحديث مؤشرات الأسئلة
            Object.keys(answers).forEach(questionId => {
                let questionIndex = questionIds.indexOf(parseInt(questionId));
                if (questionIndex !== -1) {
                    updateQuestionIndicator(questionIndex, true);
                }
            });

            // تحديث ألوان التنقل عند تحميل الصفحة
            updateNavigationColors();

            // استعادة الإجابات في النموذج
            Object.keys(answers).forEach(questionId => {
                let questionIndex = questionIds.indexOf(parseInt(questionId));
                if (questionIndex !== -1) {
                    let answer = answers[questionId];

                    // البحث عن السؤال في الصفحة
                    let questionElement = document.getElementById(`question-${questionIndex}`);
                    if (questionElement) {
                        let questionType = questionElement.getAttribute('data-type');

                        if (questionType === 'mcq') {
                            let radio = questionElement.querySelector(`input[name="question-${questionId}"][value="${answer}"]`);
                            if (radio) {
                                radio.checked = true;
                                radio.closest('.option-item').classList.add('selected');
                            }
                        } else if (questionType === 'tf') {
                            let radio = questionElement.querySelector(`input[name="question-${questionId}"][value="${answer}"]`);
                            if (radio) {
                                radio.checked = true;
                                radio.closest('.tf-option').classList.add('selected');
                            }
                        } else if (questionType === 'essay') {
                            let textarea = questionElement.querySelector('textarea');
                            if (textarea) {
                                textarea.value = answer;
                            }
                        } else if (questionType === 'numeric') {
                            let input = questionElement.querySelector('input[type="text"]');
                            if (input) {
                                input.value = answer;
                                // تطبيق التأثيرات البصرية
                                if (answer && !isNaN(answer)) {
                                    input.style.borderColor = '#4caf50';
                                    input.style.background = '#f1f8e9';
                                }
                            }
                        }
                    }
                }
            });

            // تحديث أزرار التنقل
            let prevBtn = document.getElementById('prev-btn');
            if (prevBtn) prevBtn.disabled = true;

            // إضافة تأثيرات تفاعلية
            addInteractiveEffects();

            // حفظ تلقائي كل 30 ثانية (بدون إشعارات)
            setInterval(() => saveProgress(false), 30000);

            // انتظار تحميل MathJax ثم تحديث جميع الأسئلة
            ensureMathJaxLoaded().then(() => {
                return MathJax.typesetPromise();
            }).then(() => {
                console.log('MathJax loaded successfully');

                // تأكد من عرض السؤال الأول بشكل صحيح
                const firstQuestion = document.getElementById('question-0');
                if (firstQuestion) {
                    console.log('Ensuring first question is visible');
                    firstQuestion.style.display = 'block';
                    firstQuestion.style.opacity = '1';
                    firstQuestion.style.transform = 'translateY(0)';

                    // تحديث MathJax للسؤال الأول
                    updateMathJaxForQuestion(firstQuestion);
                }

                // تأكد من أن جميع الأسئلة لها العناصر المطلوبة
                document.querySelectorAll('.question-item').forEach((question, index) => {
                    console.log(`Question ${index}:`, question.id, question.style.display);
                });
            }).catch(err => {
                console.error('MathJax error:', err);
            });
        });

                function handleNumericInput(event, questionIndex) {
            // السماح بالأرقام والنقطة والعلامة السالبة والعمليات الحسابية
            const allowedKeys = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', '-', '+', '*', '/', 'Enter', 'Backspace', 'Delete', 'Tab'];

            if (!allowedKeys.includes(event.key)) {
                event.preventDefault();
                return false;
            }

            // حفظ الإجابة عند الضغط على Enter
            if (event.key === 'Enter') {
                let result = evaluateExpression(event.target.value);
                if (result !== null) {
                    event.target.value = result;
                    saveAnswer(questionIndex, result);
                }
                event.target.blur();
            }
        }

        function validateNumericInput(input, questionIndex) {
            let value = input.value;

            // التحقق من صحة التعبير الرياضي
            if (value && isValidExpression(value)) {
                input.style.borderColor = '#4caf50';
                input.style.background = '#f1f8e9';
            } else if (value) {
                input.style.borderColor = '#f44336';
                input.style.background = '#ffebee';
            } else {
                input.style.borderColor = '#e9ecef';
                input.style.background = '#f8f9fa';
            }
        }

        function isValidExpression(expression) {
            try {
                // التحقق من أن التعبير يحتوي على أرقام وعمليات حسابية صحيحة فقط
                const cleanExpression = expression.replace(/[0-9+\-*/.()\s]/g, '');
                if (cleanExpression.length > 0) {
                    return false;
                }

                // محاولة تقييم التعبير
                evaluateExpression(expression);
                return true;
            } catch (e) {
                return false;
            }
        }

        function evaluateExpression(expression) {
            try {
                // تنظيف التعبير
                let cleanExpression = expression.replace(/\s/g, '');

                // التحقق من الأمان
                if (!/^[0-9+\-*/.()]+$/.test(cleanExpression)) {
                    return null;
                }

                // تقييم التعبير
                let result = Function('"use strict"; return (' + cleanExpression + ')')();

                // التحقق من أن النتيجة رقم
                if (typeof result === 'number' && !isNaN(result) && isFinite(result)) {
                    return result.toString();
                }

                return null;
            } catch (e) {
                return null;
            }
        }

        function addToNumericInput(questionIndex, symbol) {
            let input = document.querySelector(`input[data-question="${questionIndex}"]`);
            if (input) {
                let currentValue = input.value;
                let cursorPos = input.selectionStart;

                // إدراج الرمز في موضع المؤشر
                let newValue = currentValue.slice(0, cursorPos) + symbol + currentValue.slice(cursorPos);
                input.value = newValue;

                // تحديث موضع المؤشر
                input.setSelectionRange(cursorPos + 1, cursorPos + 1);

                // تحديث التحقق
                validateNumericInput(input, questionIndex);

                // حفظ الإجابة
                saveAnswer(questionIndex, newValue);

                // التركيز على الحقل
                input.focus();
            }
        }

        function addInteractiveEffects() {
            // تأثيرات للخيارات
            document.querySelectorAll('.option-item').forEach(item => {
                item.addEventListener('click', function() {
                    // إزالة التحديد من جميع الخيارات في نفس السؤال
                    let questionId = this.querySelector('input').name;
                    document.querySelectorAll(`input[name="${questionId}"]`).forEach(input => {
                        input.closest('.option-item').classList.remove('selected');
                    });

                    // تحديد الخيار المختار
                    this.classList.add('selected');
                });
            });

            // تأثيرات للأسئلة صح/خطأ
            document.querySelectorAll('.tf-option').forEach(item => {
                item.addEventListener('click', function() {
                    // إزالة التحديد من جميع الخيارات في نفس السؤال
                    let questionId = this.querySelector('input').name;
                    document.querySelectorAll(`input[name="${questionId}"]`).forEach(input => {
                        input.closest('.tf-option').classList.remove('selected');
                    });

                    // تحديد الخيار المختار
                    this.classList.add('selected');
                });
            });

            // تأثيرات لحقول الإدخال
            document.querySelectorAll('.essay-answer, .numeric-answer').forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.borderColor = '#2196f3';
                    this.style.boxShadow = '0 0 0 3px rgba(33, 150, 243, 0.1)';
                    this.style.background = 'white';
                });

                input.addEventListener('blur', function() {
                    this.style.borderColor = '#e9ecef';
                    this.style.boxShadow = 'none';
                    this.style.background = '#f8f9fa';
                });

                // تأثيرات خاصة للأسئلة الرقمية
                if (input.classList.contains('numeric-answer')) {
                    input.addEventListener('input', function() {
                        let value = this.value;
                        let questionIndex = this.getAttribute('data-question');

                        // التحقق من صحة القيمة
                        if (value && !isNaN(value)) {
                            this.style.borderColor = '#4caf50';
                            this.style.background = '#f1f8e9';
                        } else if (value) {
                            this.style.borderColor = '#f44336';
                            this.style.background = '#ffebee';
                        } else {
                            this.style.borderColor = '#e9ecef';
                            this.style.background = '#f8f9fa';
                        }
                    });
                }
            });

            // إضافة تأثيرات للصور
            document.querySelectorAll('.question-image').forEach(img => {
                img.addEventListener('load', function() {
                    this.style.opacity = '0';
                    this.style.transform = 'scale(0.9)';

                    setTimeout(() => {
                        this.style.transition = 'all 0.5s ease';
                        this.style.opacity = '1';
                        this.style.transform = 'scale(1)';
                    }, 100);
                });
            });
        }

        // حفظ التقدم عند مغادرة الصفحة (بدون إشعارات)
        window.addEventListener('beforeunload', function() {
            saveProgress(false);
        });

        // إضافة تأثير نبض للمؤقت
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);

                // دالة لتحسين تحميل MathJax
        function ensureMathJaxLoaded() {
            if (window.MathJax && window.MathJax.typesetPromise) {
                return Promise.resolve();
            }

            return new Promise((resolve) => {
                const checkMathJax = setInterval(() => {
                    if (window.MathJax && window.MathJax.typesetPromise) {
                        clearInterval(checkMathJax);
                        resolve();
                    }
                }, 100);

                // timeout للتأكد من عدم الانتظار إلى ما لا نهاية
                setTimeout(() => {
                    clearInterval(checkMathJax);
                    resolve();
                }, 10000);
            });
        }

                // دالة لتحديث MathJax لسؤال معين
        // دالة مبسطة لتحديث المعادلات
        function updateMathJaxForQuestion(questionElement) {
            if (!questionElement) {
                console.warn('❌ No question element provided');
                return Promise.resolve();
            }

            return new Promise((resolve) => {
                if (!window.MathJax || !window.MathJax.typesetPromise) {
                    console.warn('⚠️ MathJax not ready');
                    resolve();
                    return;
                }

                console.log('🔄 Processing MathJax for question...');

                // معالجة بسيطة ومباشرة
                MathJax.typesetPromise([questionElement]).then(() => {
                    console.log('✅ MathJax processed successfully');
                    resolve();
                }).catch((error) => {
                    console.error('❌ MathJax error:', error);
                    resolve();
                });
            });
        }
    </script>
@endsection