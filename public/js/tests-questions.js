/*************************************************
 * Test Questions JS (MathCrack) - Clean Version
 *************************************************/

let questionCounter = 0;
let currentEditingQuestion = null;
let testId = '';
let questionStatus = {};

/* ================================
   Document Ready
================================ */
$(document).ready(function () {
    // بيانات عامة من الـ Blade
    testId = window.testId || '';
    questionStatus = window.questionStatus || {};
    questionCounter = $('#questionsContainer .question-card').length;

    if (questionCounter === 0) {
        $('#emptyState').show();
        $('#floatingAddBtn').addClass('pulse');
    } else {
        $('#emptyState').hide();
        $('#floatingAddBtn').removeClass('pulse');
    }

    // تهيئة MathJax للمحتوى الحالي
    if (typeof MathJax !== 'undefined') {
        MathJax.typesetPromise().then(() => {
            console.log('MathJax initialized successfully');
            $('.question-text-editor, .option-text-editor, .question-explanation').each(function () {
                if ($(this).val().trim()) {
                    renderMath(this);
                }
            });
        }).catch(err => {
            console.error('MathJax initialization error:', err);
        });
    } else {
        console.warn('MathJax not loaded');
    }

    // ربط الأحداث العامة
    bindEvents();

    // إعادة تطبيق MathJax بعد التحميل بقليل
    setTimeout(function () {
        $('.question-text-editor, .option-text-editor, .question-explanation').each(function () {
            const content = $(this).val();
            if (content && (content.includes('\\(') || content.includes('\\[') || content.includes('\\{') || content.includes('\\frac') || content.includes('\\sqrt'))) {
                renderMath(this);
            }
        });

        if (typeof MathJax !== 'undefined') {
            renderMathJax();
        }
    }, 1500);
});

/* ================================
   Events Binding
================================ */
function bindEvents() {
    // تحديث معاينة MathJax مع الكتابة
    $(document).on('input', '.question-text-editor, .option-text-editor, .question-explanation', function () {
        const element = this;
        clearTimeout(element.mathTimeout);
        element.mathTimeout = setTimeout(() => {
            renderMath(element);
        }, 500);
    });

    // عند فقد التركيز
    $(document).on('blur', '.question-text-editor, .option-text-editor, .question-explanation', function () {
        renderMath(this);
    });

    // عند اللصق
    $(document).on('paste', '.question-text-editor, .option-text-editor, .question-explanation', function () {
        const element = this;
        setTimeout(() => {
            renderMath(element);
        }, 100);
    });

    // تحديث شكل الخيار الصحيح
    $(document).on('change', 'input[type="radio"]', function () {
        const questionCard = $(this).closest('.question-card');
        const radioName = $(this).attr('name');

        questionCard.find(`input[name="${radioName}"]`).each(function () {
            $(this).closest('.option-item').find('.option-letter').removeClass('correct');
            $(this).closest('.option-item').removeClass('correct-answer');
        });

        $(this).closest('.option-item').find('.option-letter').addClass('correct');
        $(this).closest('.option-item').addClass('correct-answer');
    });

    // تغيير الموديول في الفورم (للسؤال الجديد فقط)
    $(document).on('change', '.question-part', function () {
        const selectedPart = $(this).val();
        const questionCard = $(this).closest('.question-card');
        if (selectedPart) {
            updateQuestionNumbering(questionCard, selectedPart);
        }
    });
}

/* ================================
   Helpers: Modules
================================ */

// تحويل window.modules إلى Array موحدة
function getModulesArray() {
    const src = window.modules || {};
    if (Array.isArray(src)) {
        return src.map(m => {
            if (!m) return null;
            if (!m.key && m.part) m.key = m.part;
            return m;
        }).filter(Boolean);
    }

    return Object.keys(src).map(key => {
        const m = src[key] || {};
        if (!m.key) m.key = key;
        return m;
    });
}

// أول موديول يسمح بإضافة سؤال جديد
function getFirstAvailableModuleKey() {
    const modulesArr = getModulesArray();
    let chosen = null;

    modulesArr.forEach(m => {
        if (chosen || !m) return;

        const max = parseInt(m.max || 0, 10);
        const current = parseInt(m.current || 0, 10);
        const remaining = m.hasOwnProperty('remaining')
            ? parseInt(m.remaining || 0, 10)
            : Math.max(max - current, 0);
        const canAdd = m.hasOwnProperty('can_add') ? !!m.canAdd || !!m.can_add : remaining > 0;

        if (max > 0 && remaining > 0 && canAdd) {
            chosen = m.key;
        }
    });

    return chosen; // part1 / part2 / ... أو null لو كله مليان
}

// بناء HTML لقائمة الموديولات
function buildModuleOptionsHtml(selectedKey = '') {
    const modulesArr = getModulesArray();
    let html = `<option value="">${window.translations?.select_part || 'Select Module'}</option>`;

    if (!modulesArr.length) {
        // في حالة عدم وجود بيانات من السيرفر
        for (let i = 1; i <= 5; i++) {
            html += `<option value="part${i}">Module ${i}</option>`;
        }
        return html;
    }

    modulesArr.forEach(m => {
        if (!m) return;

        const max = parseInt(m.max || 0, 10);
        const current = parseInt(m.current || 0, 10);
        const remaining = m.hasOwnProperty('remaining')
            ? parseInt(m.remaining || 0, 10)
            : Math.max(max - current, 0);

        if (max <= 0) return;

        const canAdd = m.hasOwnProperty('can_add') ? !!m.can_add : remaining > 0;
        const disabledAttr = canAdd ? '' : 'disabled';
        const selectedAttr = (m.key === selectedKey) ? 'selected' : '';

        const label = m.label || ('Module ' + (m.key || ''));
        html += `<option value="${m.key}" ${disabledAttr} ${selectedAttr}>${label} (${current}/${max})</option>`;
    });

    return html;
}

/* ================================
   Add New Question
================================ */

function addNewQuestion() {
    // اختيار موديول تلقائي
    const autoPart = getFirstAvailableModuleKey();

    if (!autoPart) {
        showErrorMessage(
            window.translations?.all_questions_added_already ||
            'تم إضافة جميع الأسئلة المطلوبة لكل الموديولات'
        );
        return;
    }

    // حساب رقم السؤال الجديد
    const currentQuestionsCount = $('.question-card').length;
    const newQuestionNumber = currentQuestionsCount + 1;

    const questionHtml = createNewQuestionHtml(newQuestionNumber, autoPart);

    $('#emptyState').hide();
    $('#floatingAddBtn').removeClass('pulse');

    $('#questionsContainer').append(questionHtml);

    const $newCard = $(`#question-${newQuestionNumber}`);
    // مجرد تأكيد على اختيار الموديول
    $newCard.find('.question-part').val(autoPart);

    console.log('Question added. Auto-selected part:', autoPart);

    if (typeof MathJax !== 'undefined') {
        renderMathJax($newCard[0]);
    }

    const newQuestionContainer = $newCard.find('.mcq-options');
    if (newQuestionContainer.length > 0) {
        updateDeleteButtonsVisibility(newQuestionContainer);
    }

    $newCard.hide().fadeIn(300, function () {
        setTimeout(() => {
            $newCard[0]?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            $newCard.find('.question-text-editor').focus();
        }, 100);
    });
}

/* ================================
   Build Question HTML
================================ */

function createNewQuestionHtml(questionId, defaultPartKey = '') {
    const modulesHtml = buildModuleOptionsHtml(defaultPartKey);

    return `
        <div class="question-card new-question" data-question-id="new-${questionId}" id="question-${questionId}">
            <div class="question-header">
                <div class="d-flex align-items-center">
                    <span class="question-number">${questionId}</span>
                    <small class="text-muted ms-2" id="question-numbering-${questionId}">
                        (${window.translations?.numbering_will_be_set || 'Numbering will be set'})
                    </small>
                    <select class="form-select question-type-select" style="width: auto;"
                            onchange="handleQuestionTypeChange('new-${questionId}', this.value)">
                        <option value="mcq">${window.translations?.mcq || 'Multiple Choice'}</option>
                        <option value="tf">${window.translations?.tf || 'True/False'}</option>
                        <option value="numeric">${window.translations?.numeric || 'Numeric'}</option>
                    </select>
                    <span class="question-type-badge mcq-badge ms-2">
                        ${window.translations?.mcq || 'Multiple Choice'}
                    </span>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-icon btn-delete"
                            onclick="deleteQuestion('new-${questionId}')"
                            title="${window.translations?.delete || 'Delete'}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="question-content">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">
                            ${window.translations?.question_text || 'Question Text'}:
                        </label>
                        <textarea class="form-control question-text-editor"
                                  placeholder="${window.translations?.question_text_placeholder || 'Enter question text...'}"
                                  onblur="renderMath(this)"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            ${window.translations?.question_image_optional || 'Question Image (Optional)'}:
                        </label>
                        <input type="file" class="form-control question-image" accept="image/*">
                        <small class="form-text text-muted">
                            ${window.translations?.image_size_limit || 'Max: 2MB, Supported: JPG, PNG, GIF'}
                        </small>

                        <div class="mt-3">
                            <label class="form-label fw-bold">
                                ${window.translations?.question_part || 'Question Module'}:
                            </label>
                            <select class="form-select question-part" required>
                                ${modulesHtml}
                            </select>
                        </div>

                        <div class="mt-2">
                            <label class="form-label fw-bold">
                                ${window.translations?.points_label || 'Points'}:
                            </label>
                            <input type="number" class="form-control question-score"
                                   min="1"
                                   value="${window.translations?.default_score || '15'}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="options-container" id="options-${questionId}">
                ${createMCQOptions(questionId)}
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <label class="form-label fw-bold">
                        ${window.translations?.question_explanation_optional || 'Explanation (Optional)'}:
                    </label>
                    <textarea class="form-control question-explanation" rows="2"
                              placeholder="${window.translations?.question_explanation_placeholder || 'Enter explanation...'}"
                              onblur="renderMath(this)"></textarea>
                    <div class="mt-2">
                        <label class="form-label fw-bold">
                            ${window.translations?.explanation_image_optional || 'Explanation Image (Optional)'}:
                        </label>
                        <input type="file" class="form-control explanation-image" accept="image/*">
                        <small class="form-text text-muted">
                            ${window.translations?.image_size_limit || 'Max: 2MB, Supported: JPG, PNG, GIF'}
                        </small>
                    </div>
                </div>
            </div>

            <div class="question-footer mt-3">
                <div class="d-flex justify-content-end">
                    <button class="btn btn-success btn-save"
                            onclick="saveQuestion('new-${questionId}')"
                            title="${window.translations?.save || 'Save'}">
                        <i class="fas fa-save me-2"></i>${window.translations?.save || 'Save'}
                    </button>
                </div>
            </div>
        </div>
    `;
}

/* ================================
   Options (MCQ / TF / Numeric)
================================ */

function createMCQOptions(questionId) {
    return `
        <label class="form-label fw-bold">
            ${window.translations?.options || 'Options'}:
        </label>
        <div class="mcq-options">
            ${createOptionHtml(questionId, 0, 'A')}
            ${createOptionHtml(questionId, 1, 'B')}
            ${createOptionHtml(questionId, 2, 'C')}
            ${createOptionHtml(questionId, 3, 'D')}
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                onclick="addMCQOption(${questionId})">
            <i class="fas fa-plus me-1"></i>
            ${window.translations?.add_option || 'إضافة خيار'}
        </button>
    `;
}

function createOptionHtml(questionId, optionIndex, letter) {
    return `
        <div class="option-item" data-option-index="${optionIndex}">
            <div class="option-header">
                <span class="option-letter">${letter}</span>
                <input type="radio"
                       name="correct-new-${questionId}"
                       value="${optionIndex}"
                       class="form-check-input ms-2"
                       id="option-${questionId}-${optionIndex}">
                <label for="option-${questionId}-${optionIndex}"
                       class="ms-2 small text-muted">
                    ${window.translations?.correct_answer || 'correct answer'}
                </label>
                <div class="ms-auto">
                    <button type="button"
                            class="btn btn-sm btn-outline-danger"
                            onclick="removeMCQOption(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="option-content">
                <textarea class="form-control option-text-editor"
                          placeholder="${window.translations?.option_text_placeholder || 'option text...'}"
                          onblur="renderMath(this)"></textarea>
                <div class="mt-2">
                    <label class="form-label small">
                        ${window.translations?.option_image_optional || 'صورة الخيار (اختياري)'}:
                    </label>
                    <input type="file" class="form-control option-image" accept="image/*">
                    <small class="form-text text-muted">
                        ${window.translations?.image_size_limit || 'الحد الأقصى: 2 ميجا، الأنواع المدعومة: JPG, PNG, GIF'}
                    </small>
                </div>
            </div>
        </div>
    `;
}

function createNumericAnswer(questionId) {
    return `
        <div class="numeric-options">
            <label class="form-label fw-bold">
                ${window.translations?.correct_numeric_answer || 'the correct numeric answer'}:
            </label>
            <input type="number" class="form-control numeric-answer"
                   placeholder="${window.translations?.enter_correct_number || 'enter the correct number'}"
                   step="any">
            <small class="text-muted mt-1">
                ${window.translations?.decimal_numbers_allowed || 'يُسمح بالأرقام العشرية'}
            </small>
        </div>
    `;
}

function createTrueFalseOptions(questionId) {
    return `
        <div class="tf-options">
            <label class="form-label fw-bold">
                ${window.translations?.correct_answer_label || 'الإجابة الصحيحة:'}
            </label>
            <div class="tf-container">
                <div class="form-check form-check-inline">
                    <input class="form-check-input"
                           type="radio"
                           name="correct-new-${questionId}"
                           value="1"
                           id="tf-true-new-${questionId}">
                    <label class="form-check-label" for="tf-true-new-${questionId}">
                        ${window.translations?.true || 'صحيح'}
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input"
                           type="radio"
                           name="correct-new-${questionId}"
                           value="0"
                           id="tf-false-new-${questionId}">
                    <label class="form-check-label" for="tf-false-new-${questionId}">
                        ${window.translations?.false || 'خطأ'}
                    </label>
                </div>
            </div>
        </div>
    `;
}

/* تغيير نوع السؤال */
function handleQuestionTypeChange(questionId, type) {
    let actualQuestionId = questionId;
    if (questionId.startsWith('new-')) {
        actualQuestionId = questionId.replace('new-', '');
    }

    const questionCard = $(`[data-question-id="${questionId}"]`);
    if (!questionCard.length) {
        console.error('Question card not found for ID:', questionId);
        return;
    }

    const badge = questionCard.find('.question-type-badge');
    badge.removeClass('mcq-badge tf-badge numeric-badge');

    questionCard.find('.mcq-options, .tf-options, .numeric-options').remove();

    const container = questionCard.find('.options-container');

    switch (type) {
        case 'mcq':
            badge.addClass('mcq-badge').text(window.translations?.mcq || 'Multiple Choice');
            container.html(createMCQOptions(actualQuestionId));
            updateDeleteButtonsVisibility(container.find('.mcq-options'));
            break;

        case 'tf':
            badge.addClass('tf-badge').text(window.translations?.tf || 'True/False');
            container.html(createTrueFalseOptions(actualQuestionId));
            break;

        case 'numeric':
            badge.addClass('numeric-badge').text(window.translations?.numeric || 'Numeric');
            container.html(createNumericAnswer(actualQuestionId));
            break;
    }

    if (typeof MathJax !== 'undefined') {
        setTimeout(() => renderMathJax(container[0]), 100);
    }
}

/* إضافة / حذف خيارات MCQ */

function addMCQOption(questionId) {
    let container = $(`#options-${questionId} .mcq-options`);

    if (!container.length) {
        container = $(`[data-question-id="${questionId}"] .mcq-options .options-list`);
        if (!container.length) {
            container = $(`[data-question-id="${questionId}"] .mcq-options`);
        }
    }

    const optionCount = container.children('.option-item').length;

    if (optionCount >= 6) {
        showErrorMessage(window.translations?.max_options_limit || 'the maximum is 6 options');
        return;
    }

    const letter = String.fromCharCode(65 + optionCount);
    let optionHtml;

    if ($(`#options-${questionId}`).length > 0) {
        optionHtml = createOptionHtml(questionId, optionCount, letter);
    } else {
        // للسؤال القديم (من الداتا بيز)
        optionHtml = `
            <div class="option-item" data-option-index="${optionCount}">
                <div class="option-header">
                    <span class="option-letter">${letter}</span>
                    <input type="radio"
                           name="correct-${questionId}"
                           value="${optionCount}"
                           class="form-check-input ms-2"
                           id="option-${questionId}-${optionCount}">
                    <label for="option-${questionId}-${optionCount}"
                           class="ms-2 small text-muted">
                        ${window.translations?.correct_answer || 'correct answer'}
                    </label>
                    <div class="ms-auto">
                        <button type="button"
                                class="btn btn-outline-danger btn-sm"
                                onclick="removeMCQOption(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="option-content">
                    <textarea class="form-control option-text-editor"
                              placeholder="${window.translations?.option_text_placeholder || 'option text...'}"
                              onblur="renderMath(this)"></textarea>
                    <div class="mt-2">
                        <label class="form-label small">
                            ${window.translations?.option_image_optional || 'صورة الخيار (اختياري)'}:
                        </label>
                        <input type="file" class="form-control option-image" accept="image/*">
                        <small class="form-text text-muted">
                            ${window.translations?.image_size_limit || 'الحد الأقصى: 2 ميجا، الأنواع المدعومة: JPG, PNG, GIF'}
                        </small>
                    </div>
                </div>
            </div>
        `;
    }

    container.append(optionHtml);
    updateDeleteButtonsVisibility(container);

    if (typeof MathJax !== 'undefined') {
        setTimeout(() => renderMathJax(container[0]), 100);
    }
}

function updateDeleteButtonsVisibility(container) {
    const optionCount = container.children('.option-item').length;
    if (optionCount <= 2) {
        container.find('.btn-outline-danger').hide();
    } else {
        container.find('.btn-outline-danger').show();
    }
}

function removeMCQOption(button) {
    const optionItem = $(button).closest('.option-item');
    const container = optionItem.closest('.mcq-options');

    optionItem.remove();

    container.children('.option-item').each(function (index) {
        const letter = String.fromCharCode(65 + index);
        $(this).find('.option-letter').text(letter);
        $(this).attr('data-option-index', index);
    });

    updateDeleteButtonsVisibility(container);
}

/* ================================
   Edit / Save / Delete
================================ */

function editQuestion(questionId) {
    // يمكن لاحقاً عمل edit بدون reload
    location.reload();
}

function saveQuestion(questionId) {
    console.log('=== SAVE QUESTION STARTED ===', questionId);

    const questionCard = $(`[data-question-id="${questionId}"]`);
    if (!questionCard.length) {
        showErrorMessage(window.translations?.question_not_found || 'لم يتم العثور على السؤال');
        return;
    }

    const questionData = extractQuestionData(questionCard);
    if (!validateQuestionData(questionData)) {
        return;
    }

    showLoading(true);

    const formData = new FormData();

    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (!csrfToken) csrfToken = $('input[name="_token"]').val();
    if (!csrfToken && window.Laravel && window.Laravel.csrfToken) {
        csrfToken = window.Laravel.csrfToken;
    }

    formData.append('_token', csrfToken);
    formData.append('test_id', testId);

    Object.keys(questionData).forEach(key => {
        if (key === 'options') {
            questionData[key].forEach((option, index) => {
                formData.append(`options[${index}][option_text]`, option.option_text || '');
                formData.append(`options[${index}][is_correct]`, option.is_correct ? 1 : 0);
                if (option.option_image) {
                    formData.append(`options[${index}][option_image]`, option.option_image);
                }
            });
        } else if ((key === 'question_image' || key === 'explanation_image') && questionData[key]) {
            formData.append(key, questionData[key]);
        } else {
            formData.append(key, questionData[key]);
        }
    });

    const isNewQuestion = questionId.toString().startsWith('new-');
    let url;

    if (isNewQuestion) {
        url = window.routes ? window.routes.questionsStore : '/dashboard/admins/tests-questions/store';
    } else {
        url = window.routes ? window.routes.questionsUpdate : '/dashboard/admins/tests-questions/update';
        formData.append('id', questionId);
        formData.append('_method', 'PATCH');
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            showLoading(false);
            showSuccessMessage(
                response.message ||
                window.translations?.question_saved_successfully ||
                'تم حفظ السؤال بنجاح'
            );

            // ✅ AUTO-ADD: إضافة سؤال جديد تلقائيًا
            setTimeout(() => {
                // التحقق من وجود أسئلة متبقية
                const modulesArr = getModulesArray();
                let canAddMore = false;
                
                modulesArr.forEach(m => {
                    if (m && m.can_add && m.remaining > 0) {
                        canAddMore = true;
                    }
                });
                
                if (canAddMore) {
                    addNewQuestion(); // إضافة سؤال جديد
                } else {
                    showErrorMessage(
                        window.translations?.all_questions_added_already || 
                        'تم إضافة جميع الأسئلة المطلوبة'
                    );
                }
            }, 800); // تأخير 800ms لرؤية رسالة النجاح
            
            // إعادة تحميل الصفحة بعد ثانية (اختياري)
            setTimeout(() => {
                location.reload();
            }, 1000);
        },
        error: function (xhr) {
            console.error('AJAX Error:', xhr);
            showLoading(false);

            if (xhr.status === 422) {
                const errors = xhr.responseJSON?.errors || {};
                showValidationErrors(errors);
            } else {
                const errorMessage = xhr.responseJSON?.message || xhr.responseText ||
                    window.translations?.unknown_error || 'خطأ غير معروف';
                showErrorMessage(
                    (window.translations?.save_question_error || 'خطأ في حفظ السؤال') +
                    ': ' + errorMessage
                );
            }
        }
    });
}
/* استخراج بيانات السؤال من الـ DOM */
function extractQuestionData(questionCard) {
    const questionType = questionCard.find('.question-type-select').val();
    const questionText = questionCard.find('.question-text-editor').val();
    const questionImage = questionCard.find('.question-image')[0]?.files[0];
    const questionPart = questionCard.find('.question-part').val();
    const score = questionCard.find('.question-score').val();
    const explanation = questionCard.find('.question-explanation').val();
    const explanationImage = questionCard.find('.explanation-image')[0]?.files[0];

    const data = {
        question_text: questionText || '',
        type: questionType || 'mcq',
        part: questionPart || '',
        score: parseInt(score || 15, 10),
        explanation: explanation || ''
    };

    if (questionImage) data.question_image = questionImage;
    if (explanationImage) data.explanation_image = explanationImage;

    switch (questionType) {
        case 'mcq':
            data.options = extractMCQOptions(questionCard);
            break;
        case 'tf':
            data.correct_answer = extractTFAnswer(questionCard);
            break;
        case 'numeric':
            const numericValue = questionCard.find('.numeric-answer, .numeric-answer-input').val() || '';
            data.correct_answer = numericValue;
            break;
    }

    return data;
}

function extractMCQOptions(questionCard) {
    const options = [];
    const originalQuestionId = questionCard.attr('data-question-id');
    const isNewQuestion = originalQuestionId && originalQuestionId.toString().startsWith('new-');

    let optionItems;

    if (isNewQuestion) {
        const questionNumber = originalQuestionId.replace('new-', '');
        optionItems = $(`#options-${questionNumber} .mcq-options .option-item`);
    } else {
        optionItems = questionCard.find('.mcq-options .option-item');
    }

    optionItems.each(function () {
        const optionText = $(this).find('.option-text-editor').val() || '';
        const optionImage = $(this).find('.option-image')[0]?.files[0];

        const radioButton = $(this).find('input[type="radio"]');
        const isCorrect = radioButton.length ? radioButton.is(':checked') : false;

        const optionData = {
            option_text: optionText,
            is_correct: isCorrect
        };

        if (optionImage) optionData.option_image = optionImage;

        options.push(optionData);
    });

    return options;
}

function extractTFAnswer(questionCard) {
    const originalQuestionId = questionCard.attr('data-question-id');
    const isNewQuestion = originalQuestionId && originalQuestionId.toString().startsWith('new-');
    let selectedOption;

    if (isNewQuestion) {
        const questionNumber = originalQuestionId.replace('new-', '');
        selectedOption = $(`input[name="correct-new-${questionNumber}"]:checked`).val();
    } else {
        selectedOption = questionCard.find('.tf-options input[type="radio"]:checked, .tf-container input[type="radio"]:checked').val();
    }

    return selectedOption || '';
}

/* Validation */
function validateQuestionData(data) {
    if (!data.question_text.trim()) {
        showErrorMessage(window.translations?.question_text_required || 'Question text is required');
        return false;
    }

    if (!data.part) {
        showErrorMessage(window.translations?.question_part_required || 'Please select a module');
        return false;
    }

    // التحقق من أن الموديول مازال فيه مكان
    const modulesArr = getModulesArray();
    const m = modulesArr.find(x => x && x.key === data.part);
    if (m) {
        const max = parseInt(m.max || 0, 10);
        const current = parseInt(m.current || 0, 10);
        const remaining = m.hasOwnProperty('remaining')
            ? parseInt(m.remaining || 0, 10)
            : Math.max(max - current, 0);
        const canAdd = m.hasOwnProperty('can_add') ? !!m.can_add : remaining > 0;

        if (!canAdd) {
            showErrorMessage('This module is already full. Please select another module.');
            return false;
        }
    }

    if (data.type === 'mcq') {
        if (!data.options || data.options.length < 2) {
            showErrorMessage(window.translations?.min_two_options_required || 'At least 2 options required');
            return false;
        }

        const hasCorrectAnswer = data.options.some(o => o.is_correct === true);
        if (!hasCorrectAnswer) {
            showErrorMessage(window.translations?.must_select_correct_answer || 'Must select correct answer');
            return false;
        }
    }

    if (data.type === 'tf') {
        if (!data.correct_answer && data.correct_answer !== '0') {
            showErrorMessage(window.translations?.must_select_tf_answer || 'Please select true/false answer');
            return false;
        }
    }

    if (data.type === 'numeric') {
        if (!data.correct_answer || data.correct_answer.toString().trim() === '') {
            showErrorMessage(window.translations?.numeric_answer_required || 'Numeric answer required');
            return false;
        }
    }

    return true;
}

/* Delete Question */
function deleteQuestion(questionId) {
    if (!confirm(window.translations?.confirm_delete_question || 'هل أنت متأكد من حذف هذا السؤال؟ لا يمكن التراجع عن هذا الإجراء')) {
        return;
    }

    const questionCard = $(`[data-question-id="${questionId}"]`);
    if (!questionCard.length) {
        showErrorMessage(window.translations?.question_not_found || 'لم يتم العثور على السؤال');
        return;
    }

    const isNewQuestion = questionId.toString().startsWith('new-');

    if (isNewQuestion) {
        questionCard.fadeOut(300, function () {
            $(this).remove();
            checkEmptyState();
        });
        return;
    }

    showLoading(true);

    $.ajax({
        url: window.routes ? window.routes.questionsDelete : '/dashboard/admins/tests-questions/delete',
        method: 'GET',
        data: { id: questionId },
        success: function () {
            showLoading(false);
            questionCard.fadeOut(300, function () {
                $(this).remove();
                checkEmptyState();
            });
            showSuccessMessage(window.translations?.question_deleted_successfully || 'تم حذف السؤال بنجاح');
        },
        error: function (xhr) {
            showLoading(false);
            showErrorMessage(
                (window.translations?.delete_question_error || 'خطأ في حذف السؤال') +
                ': ' +
                (xhr.responseJSON?.message || window.translations?.unknown_error || 'خطأ غير معروف')
            );
        }
    });
}

/* Misc UI Helpers */

function checkEmptyState() {
    const questionCount = $('#questionsContainer .question-card').length;
    if (questionCount === 0) {
        $('#emptyState').show();
        $('#floatingAddBtn').addClass('pulse');
    } else {
        $('#emptyState').hide();
        $('#floatingAddBtn').removeClass('pulse');
    }
}

function showSuccessMessage(message) {
    const alert = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.main-content').prepend(alert);
    setTimeout(() => $('.alert-success').fadeOut(), 5000);
}

function showErrorMessage(message) {
    const alert = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.main-content').prepend(alert);
    setTimeout(() => $('.alert-danger').fadeOut(), 7000);
}

function showValidationErrors(errors) {
    let errorMessage = 'يرجى تصحيح الأخطاء التالية:<ul>';
    Object.values(errors).forEach(error => {
        if (Array.isArray(error)) {
            error.forEach(err => errorMessage += `<li>${err}</li>`);
        } else {
            errorMessage += `<li>${error}</li>`;
        }
    });
    errorMessage += '</ul>';
    showErrorMessage(errorMessage);
}

function showLoading(show) {
    if (show) {
        $('#loadingOverlay').css('display', 'flex');
    } else {
        $('#loadingOverlay').hide();
    }
}

/* تحديث النص الصغير الخاص بالترقيم */
function updateQuestionNumbering(questionCard, selectedPart) {
    const questionId = questionCard.attr('data-question-id');
    if (!questionId) return;

    const numericId = questionId.toString().replace('new-', '');
    const numbering = questionCard.find(`#question-numbering-${numericId}`);
    if (!numbering.length) return;

    const modulesArr = getModulesArray();
    const m = modulesArr.find(x => x && x.key === selectedPart);
    const label = m ? (m.label || selectedPart) : selectedPart;

    numbering.text(`(${label})`);
}

/* ================================
   MathJax Helpers
================================ */

function renderMathJax(element = null) {
    if (typeof MathJax === 'undefined') return;

    try {
        if (element) {
            MathJax.typesetPromise([element]).catch(err => {
                console.warn('MathJax rendering failed:', err);
            });
        } else {
            MathJax.typesetPromise().catch(err => {
                console.warn('MathJax rendering failed:', err);
            });
        }
    } catch (e) {
        console.warn('MathJax rendering failed:', e);
    }
}

function renderMath(element) {
    if (typeof MathJax === 'undefined') return;

    const $element = $(element);
    const text = $element.val() || '';

    const mathPatterns = [
        '$', '$$', '\\(', '\\)', '\\[', '\\]', '\\{', '\\}',
        '\\frac', '\\sqrt', '\\sum', '\\int',
        '\\dfrac', '\\varepsilon', '\\pi',
        '\\colorbox', '\\textcolor', '\\Mycircled',
        '\\item', '\\hspace', '\\vspace', '\\Large',
        '^', '_', '\\alpha', '\\beta', '\\gamma',
        '\\theta', '\\omega', '\\Delta', '\\Omega'
    ];

    const hasLatex = mathPatterns.some(pattern => text.includes(pattern));

    let previewDiv = $element.siblings('.math-preview');

    if (!hasLatex && !text.includes('\n')) {
        previewDiv.hide();
        return;
    }

    if (!previewDiv.length) {
        $element.after('<div class="math-preview mt-2 p-2 border rounded bg-light"></div>');
        previewDiv = $element.siblings('.math-preview');
    }

    let processedText = text
        .replace(/\\item/g, '• ')
        .replace(/\\colorbox\{([^}]+)\}\{([^}]*)\}\{([^}]+)\}/g, '\\bbox[border:2px solid $1;background:$1;color:$2]{$3}')
        .replace(/\\textcolor\{([^}]+)\}\{([^}]+)\}/g, '\\color{$1}{$2}')
        .replace(/\\Mycircled\{([^}]+)\}/g, '\\bbox[border:2px solid black;border-radius:50%;padding:3px]{\\text{$1}}')
        .replace(/\\hspace\{([^}]+)\}/g, '\\phantom{\\rule{$1}{0pt}}')
        .replace(/\\vspace\{([^}]+)\}/g, '\\\\[$1]')
        .replace(/\\Large\{([^}]+)\}/g, '\\large{$1}')
        .replace(/\n/g, '<br>')
        .replace(/  +/g, match => '&nbsp;'.repeat(match.length))
        .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');

    const icon = hasLatex
        ? '<i class="fas fa-calculator me-1"></i>'
        : '<i class="fas fa-eye me-1"></i>';

    previewDiv.html(
        `<div class="text-muted small mb-1">${icon} Preview:</div>` +
        `<div style="white-space: pre-wrap; font-family: inherit;">${processedText}</div>`
    ).show();

    if (hasLatex && typeof MathJax !== 'undefined' && MathJax.typesetPromise) {
        MathJax.typesetPromise([previewDiv[0]]).catch(e => {
            console.warn('MathJax rendering error:', e);
        });
    }
}
