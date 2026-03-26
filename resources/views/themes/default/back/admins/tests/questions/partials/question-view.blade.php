<div class="question-header">
    <div class="d-flex align-items-center">
        <span class="question-number">{{ $question->question_order }}</span>
        @php
            $partHeaderLabels = [
                'part1' => 'l.first_part',
                'part2' => 'l.second_part',
                'part3' => 'l.third_part',
                'part4' => 'l.fourth_part',
                'part5' => 'l.fifth_part',
            ];
            $countField = $question->part . '_questions_count';
            $totalInPart = $question->test->$countField ?? 0;
        @endphp

        <small class="text-muted ms-2">
            ({{ $question->question_order }} of {{ $totalInPart }} - @lang($partHeaderLabels[$question->part] ?? 'l.question_part'))
        </small>

        <select class="form-select question-type-select custom-question-type-select ms-3">
            <option value="mcq" {{ $question->type === 'mcq' ? 'selected' : '' }}>@lang('l.mcq')</option>
            <option value="tf" {{ $question->type === 'tf' ? 'selected' : '' }}>@lang('l.tf')</option>
            <option value="numeric" {{ $question->type === 'numeric' ? 'selected' : '' }}>@lang('l.numeric')</option>
        </select>

        <span class="question-type-badge ms-2
            {{ $question->type === 'mcq' ? 'mcq-badge' : '' }}
            {{ $question->type === 'tf' ? 'tf-badge' : '' }}
            {{ $question->type === 'numeric' ? 'numeric-badge' : '' }}">
            @if($question->type === 'mcq')
                @lang('l.mcq')
            @elseif($question->type === 'tf')
                @lang('l.tf')
            @else
                @lang('l.numeric')
            @endif
        </span>

        <button class="btn btn-outline-danger btn-sm ms-2"
            onclick="deleteQuestion('{{ $question->id }}', '{{ $question->question_order }}')">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>
</div>

<div class="question-body mt-3">
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label class="form-label fw-bold">@lang('l.question_text'):</label>
                <textarea class="form-control question-text-editor" rows="3"
                    placeholder="@lang('l.question_text_placeholder')" onblur="renderMath(this)">{{ $question->question_text }}</textarea>
                <small class="form-text text-muted">@lang('l.math_support_note')</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">@lang('l.question_image_optional'):</label>
                <input type="file" class="form-control question-image-input question-image" accept="image/*"
                    data-question-id="{{ $question->id }}"
                    onchange="previewImage(this, 'question', '{{ $question->id }}')">
                <small class="form-text text-muted">@lang('l.image_size_limit')</small>

                <div class="mt-2" id="question-image-preview-{{ $question->id }}">
                    @if ($question->question_image)
                        <div class="existing-image-block">
                            <img src="{{ asset($question->question_image) }}" alt="Question Image"
                                class="img-thumbnail" style="max-height: 150px;">
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                                onclick="markForRemoval(event, 'question', '{{ $question->id }}')">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                            <input type="hidden" name="remove_question_image" id="remove-question-{{ $question->id }}" value="0">
                        </div>
                    @else
                        <input type="hidden" name="remove_question_image" id="remove-question-{{ $question->id }}" value="0">
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="question-settings p-3 bg-light rounded">
                <h6 class="fw-bold mb-3">@lang('l.question_settings')</h6>

                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('l.question_part'):</label>
                    @php
                        $partSelectLabels = [
                            'part1' => 'l.part_first',
                            'part2' => 'l.part_second',
                            'part3' => 'l.part_third',
                            'part4' => 'l.part_fourth',
                            'part5' => 'l.part_fifth',
                        ];
                    @endphp
                    <select class="form-select part-select question-part" required>
                        @foreach ($partSelectLabels as $partKey => $labelKey)
                            @php
                                $countField = $partKey . '_questions_count';
                                $maxQuestions = $question->test->$countField ?? 0;
                            @endphp
                            @if ($maxQuestions > 0)
                                <option value="{{ $partKey }}" {{ $question->part === $partKey ? 'selected' : '' }}>
                                    @lang($labelKey)
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="mt-2">
                    <label class="form-label fw-bold">@lang('l.points_label'):</label>
                    <input type="number" class="form-control score-input question-score" min="1"
                        value="{{ $question->score }}">
                </div>

                <div class="mt-2">
                    <label class="form-label fw-bold">@lang('l.difficulty_label'):</label>
                    <select class="form-select question-difficulty">
                        <option value="">@lang('l.select_difficulty')</option>
                        <option value="easy" {{ ($question->difficulty ?? '') === 'easy' ? 'selected' : '' }}>@lang('l.easy')</option>
                        <option value="medium" {{ ($question->difficulty ?? '') === 'medium' ? 'selected' : '' }}>@lang('l.medium')</option>
                        <option value="hard" {{ ($question->difficulty ?? '') === 'hard' ? 'selected' : '' }}>@lang('l.hard')</option>
                    </select>
                </div>

                <div class="mt-2">
                    <label class="form-label fw-bold">@lang('l.content_label'):</label>
                    <select class="form-select question-content-select">
                        <option value="">@lang('l.select_content')</option>
                        <option value="algebra" {{ ($question->content ?? '') === 'algebra' ? 'selected' : '' }}>Algebra</option>
                        <option value="advanced_math" {{ ($question->content ?? '') === 'advanced_math' ? 'selected' : '' }}>Advanced Math</option>
                        <option value="problem_solving_and_data_analysis" {{ ($question->content ?? '') === 'problem_solving_and_data_analysis' ? 'selected' : '' }}>Problem Solving and Data Analysis</option>
                        <option value="geometry_and_trigonometry" {{ ($question->content ?? '') === 'geometry_and_trigonometry' ? 'selected' : '' }}>Geometry and Trigonometry</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="options-container" id="options-{{ $question->id }}">
    <div class="mcq-options" style="{{ $question->type === 'mcq' ? '' : 'display: none;' }}">
        <label class="form-label fw-bold">@lang('l.options'):</label>
        <div class="options-list">
            @if ($question->options && $question->options->count() > 0)
                @foreach ($question->options as $index => $option)
                    <div class="option-item" data-option-index="{{ $index }}" data-option-id="{{ $option->id }}">
                        <div class="option-header">
                            <span class="option-letter">{{ chr(65 + $index) }}</span>
                            <input type="radio" name="correct-{{ $question->id }}" value="{{ $index }}"
                                class="form-check-input ms-2 correct-radio"
                                {{ $option->is_correct ? 'checked' : '' }}>
                            <label class="ms-2 small text-muted">@lang('l.correct_answer')</label>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                    onclick="removeMCQOption(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="option-content">
                            <textarea class="form-control option-text-editor" rows="2"
                                placeholder="@lang('l.option_text_placeholder')" onblur="renderMath(this)">{{ $option->option_text }}</textarea>
                            <div class="mt-2">
                                <label class="form-label small">@lang('l.option_image_optional'):</label>
                                <input type="file" class="form-control option-image-input option-image" accept="image/*"
                                    data-question-id="{{ $question->id }}"
                                    data-option-index="{{ $index }}"
                                    onchange="previewImage(this, 'option', '{{ $question->id }}', '{{ $index }}')">
                                <small class="form-text text-muted">@lang('l.image_size_limit')</small>

                                <div class="mt-2" id="option-image-preview-{{ $question->id }}-{{ $index }}">
                                    @if ($option->option_image)
                                        <div class="existing-image-block">
                                            <img src="{{ asset($option->option_image) }}" alt="Option Image"
                                                class="img-thumbnail" style="max-height: 100px;">
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                                                onclick="markOptionForRemoval(event, '{{ $question->id }}', '{{ $option->id }}')">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                            <input type="hidden" name="remove_option_image[{{ $option->id }}]"
                                                id="remove-option-{{ $question->id }}-{{ $option->id }}" value="0">
                                        </div>
                                    @else
                                        <input type="hidden" name="remove_option_image[{{ $option->id }}]"
                                            id="remove-option-{{ $question->id }}-{{ $option->id }}" value="0">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="option-item" data-option-index="0">
                    <div class="option-header">
                        <span class="option-letter">A</span>
                        <input type="radio" name="correct-{{ $question->id }}" value="0"
                            class="form-check-input ms-2 correct-radio" checked>
                        <label class="ms-2 small text-muted">@lang('l.correct_answer')</label>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="removeMCQOption(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="option-content">
                        <textarea class="form-control option-text-editor" rows="2"
                            placeholder="@lang('l.option_text_placeholder')" onblur="renderMath(this)"></textarea>
                        <div class="mt-2">
                            <label class="form-label small">@lang('l.option_image_optional'):</label>
                            <input type="file" class="form-control option-image-input option-image" accept="image/*"
                                data-question-id="{{ $question->id }}"
                                data-option-index="0"
                                onchange="previewImage(this, 'option', '{{ $question->id }}', '0')">
                            <small class="form-text text-muted">@lang('l.image_size_limit')</small>
                            <div class="mt-2" id="option-image-preview-{{ $question->id }}-0">
                                <input type="hidden" name="remove_option_image[0]"
                                    id="remove-option-{{ $question->id }}-0" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="option-item" data-option-index="1">
                    <div class="option-header">
                        <span class="option-letter">B</span>
                        <input type="radio" name="correct-{{ $question->id }}" value="1"
                            class="form-check-input ms-2 correct-radio">
                        <label class="ms-2 small text-muted">@lang('l.correct_answer')</label>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="removeMCQOption(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="option-content">
                        <textarea class="form-control option-text-editor" rows="2"
                            placeholder="@lang('l.option_text_placeholder')" onblur="renderMath(this)"></textarea>
                        <div class="mt-2">
                            <label class="form-label small">@lang('l.option_image_optional'):</label>
                            <input type="file" class="form-control option-image-input option-image" accept="image/*"
                                data-question-id="{{ $question->id }}"
                                data-option-index="1"
                                onchange="previewImage(this, 'option', '{{ $question->id }}', '1')">
                            <small class="form-text text-muted">@lang('l.image_size_limit')</small>
                            <div class="mt-2" id="option-image-preview-{{ $question->id }}-1">
                                <input type="hidden" name="remove_option_image[1]"
                                    id="remove-option-{{ $question->id }}-1" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addMCQOption('{{ $question->id }}')">
            <i class="fas fa-plus"></i> @lang('l.add_option')
        </button>
    </div>

    <div class="tf-options" style="{{ $question->type === 'tf' ? '' : 'display: none;' }}">
        <label class="form-label fw-bold">@lang('l.select_correct_answer'):</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input tf-radio" type="radio" name="tf-{{ $question->id }}" value="true"
                    id="tf-true-{{ $question->id }}" {{ $question->correct_answer === 'true' ? 'checked' : '' }}>
                <label class="form-check-label" for="tf-true-{{ $question->id }}">
                    @lang('l.true')
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input tf-radio" type="radio" name="tf-{{ $question->id }}" value="false"
                    id="tf-false-{{ $question->id }}" {{ $question->correct_answer === 'false' ? 'checked' : '' }}>
                <label class="form-check-label" for="tf-false-{{ $question->id }}">
                    @lang('l.false')
                </label>
            </div>
        </div>
    </div>

    <div class="numeric-options" style="{{ $question->type === 'numeric' ? '' : 'display: none;' }}">
        <label class="form-label fw-bold">@lang('l.numeric_answer_label'):</label>
        <input type="number" class="form-control numeric-input numeric-answer" step="any"
            value="{{ $question->correct_answer }}">
    </div>
</div>

<div class="explanation-section mt-4 pt-3 border-top">
    <h6 class="fw-bold mb-3">@lang('l.question_explanation')</h6>

    <div class="mb-3">
        <label class="form-label fw-bold">@lang('l.question_explanation_optional'):</label>
        <textarea
            class="form-control explanation-text-editor question-explanation"
            rows="3"
            placeholder="@lang('l.question_explanation_placeholder')"
            onblur="renderMath(this)"
        >{{ $question->explanation ?? '' }}</textarea>
        <small class="form-text text-muted">@lang('l.write_explanation_help')</small>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">@lang('l.explanation_image_optional'):</label>
        <input type="file" class="form-control explanation-image-input explanation-image" accept="image/*"
            data-question-id="{{ $question->id }}"
            onchange="previewImage(this, 'explanation', '{{ $question->id }}')">
        <small class="form-text text-muted">@lang('l.image_size_limit')</small>

        <div class="mt-2" id="explanation-image-preview-{{ $question->id }}">
            @if ($question->explanation_image)
                <div class="existing-image-block">
                    <img src="{{ asset($question->explanation_image) }}" alt="Explanation Image"
                        class="img-thumbnail" style="max-height: 150px;">
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                        onclick="markForRemoval(event, 'explanation', '{{ $question->id }}')">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                    <input type="hidden" name="remove_explanation_image" id="remove-explanation-{{ $question->id }}" value="0">
                </div>
            @else
                <input type="hidden" name="remove_explanation_image" id="remove-explanation-{{ $question->id }}" value="0">
            @endif
        </div>
    </div>
</div>

<div class="question-footer mt-3">
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-success btn-save" onclick="quickSaveQuestion('{{ $question->id }}')">
            <i class="fas fa-save me-2"></i>@lang('l.save')
        </button>
    </div>
</div>

<script>
let savingInProgress = false;

function getQuestionCard(questionId) {
    return document.querySelector(`[data-question-id="${questionId}"]`);
}

function showMessage(message, type = 'info') {
    document.querySelectorAll('.message-alert').forEach(el => el.remove());

    const div = document.createElement('div');
    div.className = `message-alert alert alert-${type}`;
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 520px;
        padding: 14px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        white-space: pre-line;
    `;
    div.innerHTML = `
        <div class="d-flex align-items-center">
            <span style="flex:1">${message}</span>
            <button type="button" class="btn-close btn-sm" onclick="this.closest('.message-alert').remove()"></button>
        </div>
    `;
    document.body.appendChild(div);

    setTimeout(() => {
        if (div.parentElement) div.remove();
    }, 4000);
}

async function safeJson(response) {
    const text = await response.text();
    try {
        return JSON.parse(text);
    } catch (e) {
        return { success: false, message: 'Invalid response', raw: text };
    }
}

async function quickSaveQuestion(questionId) {
    if (savingInProgress) {
        showMessage('Saving in progress', 'warning');
        return;
    }

    const card = getQuestionCard(questionId);
    if (!card) return;

    const saveBtn = card.querySelector('.btn-save');
    const originalHtml = saveBtn ? saveBtn.innerHTML : '';

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('test_id', '{{ $question->test_id }}');
    formData.append('id', questionId);

    const qText = card.querySelector('.question-text-editor');
    const partSel = card.querySelector('.question-part, .part-select');
    const scoreIn = card.querySelector('.question-score, .score-input');
    const typeSel = card.querySelector('.question-type-select');
    const expText = card.querySelector('.question-explanation, .explanation-text-editor');
    const difficultySel = card.querySelector('.question-difficulty');
    const contentSel = card.querySelector('.question-content-select');

    formData.append('question_text', qText ? qText.value : '');
    formData.append('part', partSel ? partSel.value : 'part1');
    formData.append('score', scoreIn ? scoreIn.value : 15);
    formData.append('type', typeSel ? typeSel.value : 'mcq');
    formData.append('difficulty', difficultySel ? difficultySel.value : '');
    formData.append('content', contentSel ? contentSel.value : '');
    formData.append('explanation', expText ? expText.value : '');

    const qType = typeSel ? typeSel.value : 'mcq';

    if (qType === 'mcq') {
        const optionEls = card.querySelectorAll('.option-item');

        optionEls.forEach((optEl) => {
            const realIndex = optEl.getAttribute('data-option-index');
            const optionId = optEl.getAttribute('data-option-id');
            const optText = optEl.querySelector('.option-text-editor');
            const correctRadio = optEl.querySelector('.correct-radio, input[type="radio"]');
            const optImgInput = optEl.querySelector('.option-image-input, .option-image');
            const removeOpt = optEl.querySelector(`input[id^="remove-option-"]`);

            formData.append(`options[${realIndex}][option_text]`, optText ? optText.value : '');
            formData.append(`options[${realIndex}][is_correct]`, correctRadio && correctRadio.checked ? '1' : '0');
            formData.append(`options[${realIndex}][remove_image]`, removeOpt ? (removeOpt.value || '0') : '0');

            if (optionId) {
                formData.append(`options[${realIndex}][id]`, optionId);
                formData.append(`remove_option_image[${optionId}]`, removeOpt ? (removeOpt.value || '0') : '0');
            }

            if (optImgInput && optImgInput.files && optImgInput.files[0]) {
                formData.append(`options[${realIndex}][option_image]`, optImgInput.files[0]);
            }
        });
    }

    if (qType === 'tf') {
        const selectedTF = card.querySelector('.tf-radio:checked');
        if (selectedTF) {
            formData.append('correct_answer', selectedTF.value === 'true' ? '1' : '0');
        } else {
            formData.append('correct_answer', '1');
        }
    }

    if (qType === 'numeric') {
        const numericInput = card.querySelector('.numeric-answer, .numeric-input');
        formData.append('correct_answer', numericInput ? numericInput.value : '0');
    }

    const qImgInput = card.querySelector('.question-image, .question-image-input');
    if (qImgInput && qImgInput.files && qImgInput.files[0]) {
        formData.append('question_image', qImgInput.files[0]);
    }

    const expImgInput = card.querySelector('.explanation-image, .explanation-image-input');
    if (expImgInput && expImgInput.files && expImgInput.files[0]) {
        formData.append('explanation_image', expImgInput.files[0]);
    }

    const removeQ = card.querySelector(`#remove-question-${questionId}`);
    if (removeQ) formData.append('remove_question_image', removeQ.value || '0');

    const removeE = card.querySelector(`#remove-explanation-${questionId}`);
    if (removeE) formData.append('remove_explanation_image', removeE.value || '0');

    const url = '{{ route("dashboard.admins.tests-questions-update") }}';

    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = 'Saving';
    }

    savingInProgress = true;

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (response.status === 419) {
            showMessage('CSRF token expired. Refresh the page', 'danger');
            return;
        }

        const data = await safeJson(response);

        if (!response.ok) {
            let msg = data.message || 'Save failed';

            if (data.errors) {
                const lines = [];
                Object.keys(data.errors).forEach(key => {
                    const arr = data.errors[key];
                    if (Array.isArray(arr)) {
                        arr.forEach(item => lines.push(item));
                    } else {
                        lines.push(arr);
                    }
                });

                if (lines.length) {
                    msg += '\n' + lines.join('\n');
                }
            }

            showMessage(msg, 'danger');
            return;
        }

        if (data.success) {
            showMessage(data.message || 'Saved', 'success');
        } else {
            showMessage(data.message || 'Save failed', 'danger');
        }
    } catch (err) {
        showMessage('Network error', 'danger');
    } finally {
        savingInProgress = false;
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalHtml;
        }
    }
}

function previewImage(input, type, questionId, optionIndex = null) {
    if (!input.files || !input.files[0]) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        let previewId = '';
        let container = null;

        if (type === 'question') {
            previewId = `question-image-preview-${questionId}`;
            container = document.getElementById(previewId);
            if (container) {
                container.innerHTML = `
                    <div class="existing-image-block">
                        <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;">
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                            onclick="markForRemoval(event, 'question', '${questionId}')">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                        <input type="hidden" name="remove_question_image" id="remove-question-${questionId}" value="0">
                    </div>
                `;
            }
        } else if (type === 'explanation') {
            previewId = `explanation-image-preview-${questionId}`;
            container = document.getElementById(previewId);
            if (container) {
                container.innerHTML = `
                    <div class="existing-image-block">
                        <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;">
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                            onclick="markForRemoval(event, 'explanation', '${questionId}')">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                        <input type="hidden" name="remove_explanation_image" id="remove-explanation-${questionId}" value="0">
                    </div>
                `;
            }
        } else if (type === 'option') {
            const optionItem = input.closest('.option-item');
            if (!optionItem) return;

            previewId = `option-image-preview-${questionId}-${optionIndex}`;
            container = document.getElementById(previewId);

            const removeInput = optionItem.querySelector('input[id^="remove-option-"]');
            const optionId = optionItem.getAttribute('data-option-id');
            const optionKey = optionId ? optionId : optionItem.getAttribute('data-option-key');

            if (removeInput) {
                removeInput.value = '0';
                removeInput.name = `remove_option_image[${optionKey}]`;
                removeInput.id = `remove-option-${questionId}-${optionKey}`;
            }

            if (container) {
                container.innerHTML = `
                    <div class="existing-image-block">
                        <img src="${e.target.result}" class="img-thumbnail" style="max-height: 100px;">
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                            onclick="markOptionForRemoval(event, '${questionId}', '${optionKey}')">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                        <input type="hidden" name="remove_option_image[${optionKey}]"
                            id="remove-option-${questionId}-${optionKey}" value="0">
                    </div>
                `;
            }
        }
    };

    reader.readAsDataURL(input.files[0]);
}

function markForRemoval(e, type, questionId) {
    const id = type === 'question'
        ? `remove-question-${questionId}`
        : `remove-explanation-${questionId}`;

    const input = document.getElementById(id);
    if (!input) return;

    input.value = '1';

    const card = getQuestionCard(questionId);
    if (card) {
        const fileInput = type === 'question'
            ? card.querySelector('.question-image, .question-image-input')
            : card.querySelector('.explanation-image, .explanation-image-input');
        if (fileInput) fileInput.value = '';
    }

    const btn = e.currentTarget;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-check"></i> Marked';

    const previewDiv = document.getElementById(`${type}-image-preview-${questionId}`);
    if (previewDiv) {
        const img = previewDiv.querySelector('img');
        if (img) img.style.opacity = '0.35';

        const oldMsg = previewDiv.querySelector('.remove-note');
        if (oldMsg) oldMsg.remove();

        const msg = document.createElement('small');
        msg.className = 'text-danger d-block mt-2 remove-note';
        msg.innerHTML = '<i class="fas fa-trash"></i> Image will be removed when you save';
        previewDiv.appendChild(msg);
    }

    showMessage('Image will be removed when you save the question', 'warning');
}

function markOptionForRemoval(e, questionId, optionKey) {
    const input = document.getElementById(`remove-option-${questionId}-${optionKey}`);
    if (!input) return;

    input.value = '1';

    const optionItem = e.currentTarget.closest('.option-item');
    if (optionItem) {
        const fileInput = optionItem.querySelector('.option-image-input, .option-image');
        if (fileInput) fileInput.value = '';
    }

    const btn = e.currentTarget;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-check"></i> Marked';

    const previewDiv = optionItem ? optionItem.querySelector('[id^="option-image-preview-"]') : null;
    if (previewDiv) {
        const img = previewDiv.querySelector('img');
        if (img) img.style.opacity = '0.35';

        const oldMsg = previewDiv.querySelector('.remove-note');
        if (oldMsg) oldMsg.remove();

        const msg = document.createElement('small');
        msg.className = 'text-danger d-block mt-2 remove-note';
        msg.innerHTML = '<i class="fas fa-trash"></i> Image will be removed when you save';
        previewDiv.appendChild(msg);
    }

    showMessage('Option image will be removed when you save the question', 'warning');
}

function removeMCQOption(button) {
    const optionItem = button.closest('.option-item');
    if (!optionItem) return;

    const container = optionItem.closest('.options-list');
    if (!container) return;

    optionItem.remove();
    reindexMCQOptions(container);
}

function reindexMCQOptions(container) {
    const optionItems = container.querySelectorAll('.option-item');

    optionItems.forEach((item, index) => {
        item.setAttribute('data-option-index', index);

        const letter = item.querySelector('.option-letter');
        if (letter) {
            letter.textContent = String.fromCharCode(65 + index);
        }

        const radio = item.querySelector('.correct-radio, input[type="radio"]');
        if (radio) {
            const questionIdMatch = radio.name.match(/correct-(.+)/);
            const qid = questionIdMatch ? questionIdMatch[1] : '';
            radio.value = index;
            radio.name = `correct-${qid}`;
        }

        const optionImageInput = item.querySelector('.option-image-input, .option-image');
        if (optionImageInput) {
            optionImageInput.setAttribute('data-option-index', index);
            const qid = optionImageInput.getAttribute('data-question-id');
            optionImageInput.setAttribute(
                'onchange',
                `previewImage(this, 'option', '${qid}', '${index}')`
            );
        }

        const previewDiv = item.querySelector('[id^="option-image-preview-"]');
        const removeInput = item.querySelector('[id^="remove-option-"]');
        const removeBtn = item.querySelector('.existing-image-block button.btn-outline-danger');

        const qid = optionImageInput ? optionImageInput.getAttribute('data-question-id') : null;
        const optionId = item.getAttribute('data-option-id');
        const optionKey = optionId ? optionId : item.getAttribute('data-option-key');

        if (previewDiv && qid) {
            previewDiv.id = `option-image-preview-${qid}-${index}`;
        }

        if (removeInput && qid && optionKey) {
            removeInput.name = `remove_option_image[${optionKey}]`;
            removeInput.id = `remove-option-${qid}-${optionKey}`;
        }

        if (removeBtn && qid && optionKey) {
            removeBtn.setAttribute(
                'onclick',
                `markOptionForRemoval(event, '${qid}', '${optionKey}')`
            );
        }
    });
}

function addMCQOption(questionId) {
    const container = document.querySelector(`#options-${questionId} .options-list`);
    if (!container) return;

    const optionCount = container.querySelectorAll('.option-item').length;
    const letter = String.fromCharCode(65 + optionCount);
    const optionKey = `new-${Date.now()}-${optionCount}`;

    const html = `
        <div class="option-item" data-option-index="${optionCount}" data-option-key="${optionKey}">
            <div class="option-header">
                <span class="option-letter">${letter}</span>
                <input type="radio" name="correct-${questionId}" value="${optionCount}"
                    class="form-check-input ms-2 correct-radio">
                <label class="ms-2 small text-muted">@lang('l.correct_answer')</label>
                <div class="ms-auto">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                        onclick="removeMCQOption(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="option-content">
                <textarea class="form-control option-text-editor" rows="2"
                    placeholder="@lang('l.option_text_placeholder')" onblur="renderMath(this)"></textarea>
                <div class="mt-2">
                    <label class="form-label small">@lang('l.option_image_optional'):</label>
                    <input type="file" class="form-control option-image-input option-image" accept="image/*"
                        data-question-id="${questionId}"
                        data-option-index="${optionCount}"
                        onchange="previewImage(this, 'option', '${questionId}', '${optionCount}')">
                    <small class="form-text text-muted">@lang('l.image_size_limit')</small>
                    <div class="mt-2" id="option-image-preview-${questionId}-${optionCount}">
                        <input type="hidden" name="remove_option_image[${optionKey}]"
                            id="remove-option-${questionId}-${optionKey}" value="0">
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    reindexMCQOptions(container);
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const card = document.activeElement ? document.activeElement.closest('[data-question-id]') : null;
            if (!card) return;
            const qid = card.getAttribute('data-question-id');
            if (qid) quickSaveQuestion(qid);
        }
    });
});

if (!document.querySelector('#message-styles')) {
    const style = document.createElement('style');
    style.id = 'message-styles';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .message-alert { animation: slideIn 0.3s ease; }
        .btn-save:disabled { opacity: 0.6; cursor: not-allowed; }
        .existing-image-block img { transition: opacity 0.2s ease; }
    `;
    document.head.appendChild(style);
}
</script>