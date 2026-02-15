let questionCounter = 0;
let currentEditingQuestion = null;
let testId = '';
let questionStatus = {};

// تهيئة الصفحة
$(document).ready(function () {
    // الحصول على البيانات من الصفحة
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

    // تهيئة MathJax للمحتوى الموجود
    if (typeof MathJax !== 'undefined') {
        MathJax.typesetPromise().then(() => {
            console.log('MathJax initialized successfully');
            // تطبيق MathJax على الحقول الموجودة
            $('.question-text-editor, .option-text-editor').each(function () {
                if ($(this).val().trim()) {
                    renderMath(this);
                }
            });
        }).catch((err) => {
            console.error('MathJax initialization error:', err);
        });
    } else {
        console.warn('MathJax not loaded');
    }

    // تطبيق الأحداث
    bindEvents();

    // تطبيق MathJax على جميع textarea الموجودة
    setTimeout(function () {
        $('.question-text-editor, .option-text-editor').each(function () {
            const content = $(this).val();
            if (content && (content.includes('\\(') || content.includes('\\[') || content.includes('\\{') || content.includes('\\frac') || content.includes('\\sqrt'))) {
                console.log('Applying MathJax to textarea with content:', content.substring(0, 50));
                renderMath(this);
            }
        });

        // أيضاً تطبيق MathJax على أي محتوى يحتوي على معادلات
        if (typeof MathJax !== 'undefined') {
            console.log('Applying MathJax to entire page...');
            renderMathJax();
        }
    }, 1500);
});

// ربط الأحداث
function bindEvents() {
    // تطبيق MathJax على التحديث التلقائي للحقول
    $(document).on('input', '.question-text-editor, .option-text-editor', function () {
        const element = this;
        clearTimeout(element.mathTimeout);
        element.mathTimeout = setTimeout(() => {
            renderMath(element);
        }, 500);
    });

    // تطبيق MathJax فور فقدان التركيز
    $(document).on('blur', '.question-text-editor, .option-text-editor', function () {
        renderMath(this);
    });

    // تطبيق MathJax عند اللصق
    $(document).on('paste', '.question-text-editor, .option-text-editor', function () {
        const element = this;
        setTimeout(() => {
            renderMath(element);
        }, 100);
    });

    // تحديث إشارة الخيار الصحيح
    $(document).on('change', 'input[type="radio"]', function () {
        const questionCard = $(this).closest('.question-card');
        const radioName = $(this).attr('name');

        // إعادة تعيين جميع الخيارات لنفس السؤال
        questionCard.find(`input[name="${radioName}"]`).each(function () {
            $(this).closest('.option-item').find('.option-letter').removeClass('correct');
            $(this).closest('.option-item').removeClass('correct-answer');
        });

        // تعيين الخيار المحدد كصحيح
        $(this).closest('.option-item').find('.option-letter').addClass('correct');
        $(this).closest('.option-item').addClass('correct-answer');
    });
}

// إضافة سؤال جديد
function addNewQuestion() {
    // التحقق من إمكانية إضافة المزيد من الأسئلة
    if (questionStatus && questionStatus.all_complete) {
        showErrorMessage(window.translations?.all_questions_added_already || 'تم إضافة جميع الأسئلة المطلوبة');
        return;
    }

    // حساب العدد الصحيح للأسئلة
    const currentQuestionsCount = $('.question-card').length;
    const newQuestionNumber = currentQuestionsCount + 1;

    const questionHtml = createNewQuestionHtml(newQuestionNumber);

    $('#emptyState').hide();
    $('#floatingAddBtn').removeClass('pulse');

    // التأكد من إضافة السؤال في النهاية
    $('#questionsContainer').append(questionHtml);

    console.log('Question added to container. Total questions now:', $('.question-card').length);

    // تطبيق MathJax على المحتوى الجديد
    if (typeof MathJax !== 'undefined') {
        renderMathJax();
    }

    // إضافة تأثير بصري لطيف للسؤال الجديد أولاً
    $(`#question-${newQuestionNumber}`).hide().fadeIn(500, function () {
        // ثم التمرير إلى السؤال الجديد بعد ظهوره
        setTimeout(() => {
            $(`#question-${newQuestionNumber}`)[0]?.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // التركيز على حقل النص
            $(`#question-${newQuestionNumber} .question-text-editor`).focus();
        }, 100);
    });
}

// إنشاء HTML للسؤال الجديد
function createNewQuestionHtml(questionId) {
    const part1Available = questionStatus.part1_count < questionStatus.part1_max;
    const part2Available = questionStatus.part2_count < questionStatus.part2_max;

    return `
        <div class="question-card new-question" data-question-id="new-${questionId}" id="question-${questionId}">
            <div class="question-header">
                <div class="d-flex align-items-center">
                    <span class="question-number">${questionId}</span>
                    <small class="text-muted ms-2" id="question-numbering-${questionId}">
                        (${window.translations?.numbering_will_be_set || 'سيتم تحديد الترقيم بعد اختيار الجزء'})
                    </small>
                    <select class="form-select question-type-select" style="width: auto;" onchange="handleQuestionTypeChange('new-${questionId}', this.value)">
                        <option value="mcq">${window.translations?.mcq || 'اختيار من متعدد'}</option>
                        <option value="tf">${window.translations?.tf || 'صح أم خطأ'}</option>
                        <option value="numeric">${window.translations?.numeric || 'رقمي'}</option>
                    </select>
                    <span class="question-type-badge mcq-badge ms-2">${window.translations?.mcq || 'اختيار من متعدد'}</span>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-icon btn-save" onclick="saveQuestion('new-${questionId}')" title="${window.translations?.save || 'حفظ'}">
                        <i class="fas fa-save"></i>
                    </button>
                    <button class="btn btn-icon btn-delete" onclick="deleteQuestion('new-${questionId}')" title="${window.translations?.delete || 'حذف'}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="question-content">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">${window.translations?.question_text || 'نص السؤال'}:</label>
                        <textarea class="form-control question-text-editor"
                                placeholder="${window.translations?.question_text_placeholder || 'اكتب نص السؤال هنا...'}"
                                onblur="renderMath(this)"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">${window.translations?.question_image_optional || 'صورة السؤال (اختياري)'}:</label>
                        <input type="file" class="form-control question-image" accept="image/*">
                        <small class="form-text text-muted">${window.translations?.image_size_limit || 'الحد الأقصى: 2 ميجا، الأنواع المدعومة: JPG, PNG, GIF'}</small>

                        <div class="mt-3">
                            <label class="form-label fw-bold">${window.translations?.question_part || 'جزء السؤال'}:</label>
                            <select class="form-select question-part" required>
                                <option value="">${window.translations?.select_part || 'اختر الجزء'}</option>
                                ${part1Available ?
            `<option value="part1">${window.translations?.part_first || 'الجزء الأول'} (${questionStatus.part1_count}/${questionStatus.part1_max})</option>` : ''}
                                ${part2Available ?
            `<option value="part2">${window.translations?.part_second || 'الجزء الثاني'} (${questionStatus.part2_count}/${questionStatus.part2_max})</option>` : ''}
                            </select>
                        </div>

                        <div class="mt-2">
                            <label class="form-label fw-bold">${window.translations?.points_label || 'النقاط'}:</label>
                            <input type="number" class="form-control question-score" min="1" value="15">
                        </div>
                    </div>
                </div>
            </div>

            <div class="options-container" id="options-${questionId}">
                ${createMCQOptions(questionId)}
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <label class="form-label fw-bold">${window.translations?.question_explanation_optional || 'شرح السؤال (اختياري)'}:</label>
                    <textarea class="form-control question-explanation" rows="2"
                            placeholder="${window.translations?.question_explanation_placeholder || 'اكتب شرحاً للسؤال أو إرشادات للطلاب...'}"></textarea>
                </div>
            </div>
        </div>
    `;
}

// إنشاء خيارات الاختيار من متعدد
function createMCQOptions(questionId) {
    return `
        <label class="form-label fw-bold">${window.translations?.options || 'الخيارات'}:</label>
        <div class="mcq-options">
            ${createOptionHtml(questionId, 0, 'A')}
            ${createOptionHtml(questionId, 1, 'B')}
            ${createOptionHtml(questionId, 2, 'C')}
            ${createOptionHtml(questionId, 3, 'D')}
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addMCQOption(${questionId})">
            <i class="fas fa-plus me-1"></i> ${window.translations?.add_option || 'إضافة خيار'}
        </button>
    `;
}

// إنشاء HTML للخيار
function createOptionHtml(questionId, optionIndex, letter) {
    return `
        <div class="option-item" data-option-index="${optionIndex}">
            <div class="option-header">
                <span class="option-letter">${letter}</span>
                <input type="radio" name="correct-new-${questionId}" value="${optionIndex}" class="form-check-input ms-2" id="option-${questionId}-${optionIndex}">
                <label for="option-${questionId}-${optionIndex}" class="ms-2 small text-muted">${window.translations?.correct_answer || 'الإجابة الصحيحة'}</label>
                <div class="ms-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMCQOption(this)" ${optionIndex < 2 ? 'style="display:none"' : ''}>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="option-content">
                <textarea class="form-control option-text-editor"
                        placeholder="${window.translations?.option_text_placeholder || 'نص الخيار...'}"
                        onblur="renderMath(this)"></textarea>
            </div>
        </div>
    `;
}

// التحكم في تغيير نوع السؤال
function handleQuestionTypeChange(questionId, type) {
    let actualQuestionId = questionId;
    if (questionId.startsWith('new-')) {
        actualQuestionId = questionId.replace('new-', '');
    }

    const questionCard = $(`[data-question-id="${questionId}"]`);
    if (questionCard.length === 0) {
        console.error('Question card not found for ID:', questionId);
        return;
    }

    const badge = questionCard.find('.question-type-badge');

    // تحديث الشارة
    badge.removeClass('mcq-badge tf-badge numeric-badge');

    // إخفاء جميع أنواع الخيارات أولاً
    questionCard.find('.mcq-options, .tf-options, .numeric-options').hide();

    switch (type) {
        case 'mcq':
            badge.addClass('mcq-badge').text(window.translations?.mcq || 'اختيار من متعدد');

            // إذا كان للأسئلة الجديدة، إنشاء HTML جديد
            if (questionId.startsWith('new-')) {
                const container = questionCard.find('.options-container');
                if (container.length > 0) {
                    container.html(createMCQOptions(actualQuestionId));
                }
            } else {
                // للأسئلة المحفوظة، إظهار قسم MCQ
                questionCard.find('.mcq-options').show();
            }
            break;

        case 'tf':
            badge.addClass('tf-badge').text(window.translations?.tf || 'صح أم خطأ');

            if (questionId.startsWith('new-')) {
                const container = questionCard.find('.options-container');
                if (container.length > 0) {
                    container.html(createTrueFalseOptions(actualQuestionId));
                }
            } else {
                questionCard.find('.tf-options').show();
            }
            break;

        case 'numeric':
            badge.addClass('numeric-badge').text(window.translations?.numeric || 'رقمي');

            if (questionId.startsWith('new-')) {
                const container = questionCard.find('.options-container');
                if (container.length > 0) {
                    container.html(createNumericAnswer(actualQuestionId));
                }
            } else {
                questionCard.find('.numeric-options').show();
            }
            break;
    }

    // إعادة تطبيق MathJax على العناصر الجديدة
    if (typeof MathJax !== 'undefined') {
        setTimeout(() => {
            renderMathJax();
        }, 100);
    }
}

// إنشاء خيارات صواب/خطأ
function createTrueFalseOptions(questionId) {
    return `
        <label class="form-label fw-bold">الخيارات:</label>
        <div class="tf-options">
            <div class="option-item" data-option-index="0">
                <div class="option-header">
                    <span class="option-letter">✓</span>
                    <input type="radio" name="correct-new-${questionId}" value="true" class="form-check-input ms-2">
                    <span class="ms-2 fw-bold text-success">صحيح</span>
                </div>
            </div>
            <div class="option-item" data-option-index="1">
                <div class="option-header">
                    <span class="option-letter">✗</span>
                    <input type="radio" name="correct-new-${questionId}" value="false" class="form-check-input ms-2">
                    <span class="ms-2 fw-bold text-danger">خطأ</span>
                </div>
            </div>
        </div>
    `;
}

// إنشاء إجابة رقمية
function createNumericAnswer(questionId) {
    return `
        <div class="numeric-container">
            <label class="form-label fw-bold">${window.translations?.correct_numeric_answer || 'الإجابة الرقمية الصحيحة'}:</label>
            <input type="number" class="form-control numeric-answer"
                   placeholder="${window.translations?.enter_correct_number || 'أدخل الرقم الصحيح'}" step="any">
            <small class="text-muted mt-1">${window.translations?.decimal_numbers_allowed || 'يُسمح بالأرقام العشرية'}</small>
        </div>
    `;
}

// إنشاء خيارات صواب/خطأ
function createTrueFalseOptions(questionId) {
    return `
        <div class="tf-options">
            <label class="form-label fw-bold">${window.translations?.correct_answer_label || 'الإجابة الصحيحة:'}</label>
            <div class="tf-container">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="correct-new-${questionId}"
                           value="1" id="tf-true-new-${questionId}">
                    <label class="form-check-label" for="tf-true-new-${questionId}">${window.translations?.true || 'صحيح'}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="correct-new-${questionId}"
                           value="0" id="tf-false-new-${questionId}">
                    <label class="form-check-label" for="tf-false-new-${questionId}">${window.translations?.false || 'خطأ'}</label>
                </div>
            </div>
        </div>
    `;
}

// إضافة خيار جديد للاختيار من متعدد
function addMCQOption(questionId) {
    console.log('Adding MCQ option for question:', questionId);

    // للأسئلة الجديدة
    let container = $(`#options-${questionId} .mcq-options`);

    // للأسئلة المحفوظة
    if (container.length === 0) {
        container = $(`[data-question-id="${questionId}"] .mcq-options .options-list`);
        if (container.length === 0) {
            container = $(`[data-question-id="${questionId}"] .mcq-options`);
        }
    }

    console.log('Container found:', container.length, container);

    const optionCount = container.children('.option-item').length;
    console.log('Current option count:', optionCount);

    if (optionCount >= 6) {
        showErrorMessage(window.translations?.max_options_limit || 'الحد الأقصى 6 خيارات');
        return;
    }

    const letter = String.fromCharCode(65 + optionCount);

    // إنشاء HTML للخيار الجديد بناءً على نوع السؤال
    let optionHtml;
    if ($(`#options-${questionId}`).length > 0) {
        // للأسئلة الجديدة
        optionHtml = createOptionHtml(questionId, optionCount, letter);
    } else {
        // للأسئلة المحفوظة
        optionHtml = `
        <div class="option-item" data-option-index="${optionCount}">
            <div class="option-header">
                <span class="option-letter">${letter}</span>
                <input type="radio" name="correct-${questionId}" value="${optionCount}"
                       class="form-check-input ms-2" id="option-${questionId}-${optionCount}">
                <label for="option-${questionId}-${optionCount}" class="ms-2 small text-muted">الإجابة الصحيحة</label>
                <div class="ms-auto">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMCQOption(this)" ${optionCount < 2 ? 'style="display:none"' : ''}>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="option-content">
                <textarea class="form-control option-text-editor"
                        placeholder="نص الخيار..."
                        onblur="renderMath(this)"></textarea>
            </div>
        </div>`;
    }

    container.append(optionHtml);

    // إظهار أزرار الحذف للخيارات الإضافية
    if (optionCount >= 2) {
        container.find('.btn-outline-danger').show();
    }

    // تطبيق MathJax على الخيار الجديد
    if (typeof MathJax !== 'undefined') {
        setTimeout(() => {
            renderMathJax();
        }, 100);
    }
}

// حذف خيار من الاختيار من متعدد
function removeMCQOption(button) {
    const optionItem = $(button).closest('.option-item');
    const container = optionItem.closest('.mcq-options');

    if (container.children('.option-item').length <= 2) return;

    optionItem.remove();

    // إعادة ترقيم الخيارات
    container.children('.option-item').each(function (index) {
        const letter = String.fromCharCode(65 + index);
        $(this).find('.option-letter').text(letter);
        $(this).attr('data-option-index', index);
    });

    if (container.children('.option-item').length <= 2) {
        container.find('.btn-outline-danger').hide();
    }
}

// تحويل السؤال للوضع التعديلي
function editQuestion(questionId) {
    // هنا يمكن إضافة منطق تحويل السؤال للوضع التعديلي
    // للآن سيتم إعادة تحميل الصفحة
    location.reload();
}

// حفظ السؤال
function saveQuestion(questionId) {
    console.log('=== SAVE QUESTION STARTED ===');
    console.log('Question ID:', questionId);
    console.log('Available routes:', window.routes);

    const questionCard = $(`[data-question-id="${questionId}"]`);
    if (questionCard.length === 0) {
        showErrorMessage(window.translations?.question_not_found || 'لم يتم العثور على السؤال');
        return;
    }

    const questionData = extractQuestionData(questionCard);
    if (!validateQuestionData(questionData)) {
        return;
    }

    showLoading(true);

    // إنشاء FormData للملفات
    const formData = new FormData();

    // الحصول على CSRF token
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (!csrfToken) {
        csrfToken = $('input[name="_token"]').val();
    }
    if (!csrfToken && window.Laravel && window.Laravel.csrfToken) {
        csrfToken = window.Laravel.csrfToken;
    }

    formData.append('_token', csrfToken);
    formData.append('test_id', testId);

    // إضافة بيانات السؤال
    Object.keys(questionData).forEach(key => {
        if (key === 'options') {
            console.log('Processing options:', questionData[key]);
            questionData[key].forEach((option, index) => {
                formData.append(`options[${index}][option_text]`, option.option_text || '');
                formData.append(`options[${index}][is_correct]`, option.is_correct ? 1 : 0);
                console.log(`Option ${index}: text="${option.option_text}", is_correct=${option.is_correct}`);
            });
        } else if (key === 'question_image' && questionData[key]) {
            formData.append(key, questionData[key]);
        } else {
            formData.append(key, questionData[key]);
        }
    });

    const isNewQuestion = questionId.toString().startsWith('new-');

    // تحديد الـ URL بناءً على نوع السؤال
    let url;

    if (isNewQuestion) {
        url = window.routes ? window.routes.questionsStore : '/dashboard/admins/tests-questions/store';
    } else {
        url = window.routes ? window.routes.questionsUpdate : '/dashboard/admins/tests-questions/update';
        formData.append('id', questionId);
        formData.append('_method', 'PATCH');
    }

    console.log('=== SENDING AJAX REQUEST ===');
    console.log('URL:', url);
    console.log('Is New Question:', isNewQuestion);
    console.log('Question ID:', questionId);
    console.log('Question data:', questionData);
    console.log('CSRF Token:', csrfToken);

    // إظهار محتويات FormData للتشخيص
    console.log('=== FORM DATA CONTENTS ===');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log('Success response:', response);
            showLoading(false);

            if (isNewQuestion && response.question_id) {
                // تحديث ID السؤال الجديد
                questionCard.attr('data-question-id', response.question_id);
                questionCard.find('.btn-save').attr('onclick', `saveQuestion('${response.question_id}')`);
                questionCard.find('.btn-delete').attr('onclick', `deleteQuestion('${response.question_id}')`);
                questionCard.find('.question-type-select').attr('onchange', `handleQuestionTypeChange('${response.question_id}', this.value)`);

                // تحديث أسماء radio buttons للأسئلة الجديدة
                console.log('Updating radio button names for saved question');
                questionCard.find('input[type="radio"]').each(function () {
                    const currentName = $(this).attr('name');
                    const currentId = $(this).attr('id');
                    console.log('Found radio button:', currentName, currentId);

                    if (currentName && currentName.includes('new-')) {
                        const newName = currentName.replace(/new-\d+/, response.question_id);
                        $(this).attr('name', newName);
                        console.log('Updated name from', currentName, 'to', newName);

                        if (currentId && currentId.includes('new-')) {
                            const newId = currentId.replace(/new-\d+/, response.question_id);
                            $(this).attr('id', newId);
                            // تحديث label المرتبط
                            $(this).siblings(`label[for="${currentId}"]`).attr('for', newId);
                            console.log('Updated id from', currentId, 'to', newId);
                        }
                    }
                });

                // إزالة كلاس السؤال الجديد وإضافة تأثير بصري للنجاح
                questionCard.removeClass('new-question').addClass('saved-question');
                setTimeout(() => {
                    questionCard.removeClass('saved-question');
                }, 2000);
            } else if (!isNewQuestion) {
                // للأسئلة المُحدثة - إضافة تأثير بصري
                questionCard.addClass('question-updated');
                setTimeout(() => {
                    questionCard.removeClass('question-updated');
                }, 2000);
            }

            showSuccessMessage(response.message || window.translations?.question_saved_successfully || 'تم حفظ السؤال بنجاح');

            // إعادة تطبيق MathJax
            if (typeof MathJax !== 'undefined') {
                renderMathJax();
            }

            // تحديث حالة التقدم
            updateQuestionStatus(questionData.part);
        },
        error: function (xhr) {
            console.error('AJAX Error:', xhr);
            console.error('Status:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            console.error('Response JSON:', xhr.responseJSON);

            showLoading(false);
            if (xhr.status === 422) {
                const errors = xhr.responseJSON?.errors || {};
                showValidationErrors(errors);
            } else {
                const errorMessage = xhr.responseJSON?.message || xhr.responseText || window.translations?.unknown_error || 'خطأ غير معروف';
                showErrorMessage((window.translations?.save_question_error || 'خطأ في حفظ السؤال') + ': ' + errorMessage);
            }
        }
    });
}

// استخراج بيانات السؤال
function extractQuestionData(questionCard) {
    const questionType = questionCard.find('.question-type-select').val();
    const questionText = questionCard.find('.question-text-editor').val();
    const questionImage = questionCard.find('.question-image')[0]?.files[0];
    const questionPart = questionCard.find('.question-part').val();
    const score = questionCard.find('.question-score').val();
    const explanation = questionCard.find('.question-explanation').val();

    const data = {
        question_text: questionText || '',
        type: questionType || 'mcq',
        part: questionPart || '',
        score: parseInt(score) || 15,
        explanation: explanation || ''
    };

    if (questionImage) {
        data.question_image = questionImage;
    }

    // استخراج بيانات الخيارات حسب نوع السؤال
    switch (questionType) {
        case 'mcq':
            data.options = extractMCQOptions(questionCard);
            console.log('=== MCQ OPTIONS EXTRACTED ===');
            console.log('Number of options:', data.options ? data.options.length : 0);
            console.log('Options data:', data.options);
            break;
        case 'tf':
            data.correct_answer = extractTFAnswer(questionCard);
            console.log('=== TRUE/FALSE ANSWER EXTRACTED ===');
            console.log('Answer:', data.correct_answer);
            break;
        case 'numeric':
            const numericValue = questionCard.find('.numeric-answer, .numeric-answer-input').val() || '';
            console.log('Extracting numeric answer. Found elements:', questionCard.find('.numeric-answer, .numeric-answer-input').length, 'Value:', numericValue);
            data.correct_answer = numericValue;
            break;
    }

    return data;
}

// استخراج خيارات الاختيار من متعدد
function extractMCQOptions(questionCard) {
    const options = [];
    const originalQuestionId = questionCard.attr('data-question-id');
    const isNewQuestion = originalQuestionId && originalQuestionId.toString().startsWith('new-');

    console.log('Extracting MCQ options for question ID:', originalQuestionId);

    // البحث عن جميع الخيارات في التصميمين
    let optionItems;

    if (isNewQuestion) {
        // للأسئلة الجديدة: البحث داخل #options-X .mcq-options
        const questionNumber = originalQuestionId.replace('new-', '');
        optionItems = $(`#options-${questionNumber} .mcq-options .option-item`);
        console.log('Searching for new question options in:', `#options-${questionNumber} .mcq-options .option-item`);
    } else {
        // للأسئلة المحفوظة: البحث داخل السؤال نفسه
        optionItems = questionCard.find('.mcq-options .option-item');
        console.log('Searching for saved question options in question card');
    }

    console.log('Found option items:', optionItems.length, optionItems);

    optionItems.each(function (index) {
        const optionText = $(this).find('.option-text-editor').val() || '';

        // البحث عن radio button في هذا الخيار بناءً على الاسم المستخدم فعلياً
        let radioButton = $(this).find('input[type="radio"]');
        let isCorrect = false;

        if (radioButton.length > 0) {
            isCorrect = radioButton.is(':checked');
            console.log(`Option ${index}: found radio with name="${radioButton.attr('name')}", checked=${isCorrect}`);
        } else {
            console.log(`Option ${index}: no radio button found`);
        }

        console.log(`Option ${index}:`, { text: optionText, is_correct: isCorrect });

        options.push({
            option_text: optionText,
            is_correct: isCorrect
        });
    });

    console.log('Final MCQ options:', options);
    return options;
}

// استخراج إجابة صواب/خطأ
function extractTFAnswer(questionCard) {
    const originalQuestionId = questionCard.attr('data-question-id');
    const isNewQuestion = originalQuestionId && originalQuestionId.toString().startsWith('new-');

    let selectedOption;

    if (isNewQuestion) {
        // للأسئلة الجديدة: البحث بـ name="correct-new-X"
        const questionNumber = originalQuestionId.replace('new-', '');
        selectedOption = $(`input[name="correct-new-${questionNumber}"]:checked`).val();
        console.log('Searching for new TF answer with name:', `correct-new-${questionNumber}`);
    } else {
        // للأسئلة المحفوظة: البحث داخل السؤال
        selectedOption = questionCard.find('.tf-options input[type="radio"]:checked, .tf-container input[type="radio"]:checked').val();
        console.log('Searching for saved TF answer in question card');
    }

    console.log('Extracting TF answer for question:', originalQuestionId, 'Selected:', selectedOption);

    return selectedOption || '';
}

// التحقق من صحة بيانات السؤال
function validateQuestionData(data) {
    if (!data.question_text.trim()) {
        showErrorMessage(window.translations?.question_text_required || 'نص السؤال مطلوب');
        return false;
    }

    if (!data.part) {
        showErrorMessage(window.translations?.question_part_required || 'يجب اختيار جزء السؤال');
        return false;
    }

    if (data.type === 'mcq') {
        if (!data.options || data.options.length < 2) {
            showErrorMessage(window.translations?.min_two_options_required || 'يجب إضافة خيارين على الأقل');
            return false;
        }

        console.log('Validating MCQ options:', data.options);
        const hasCorrectAnswer = data.options.some(option => option.is_correct === true);
        console.log('Has correct answer:', hasCorrectAnswer);

        if (!hasCorrectAnswer) {
            showErrorMessage(window.translations?.must_select_correct_answer || 'يجب اختيار الإجابة الصحيحة');
            return false;
        }
    }

    if (data.type === 'tf') {
        console.log('Validating TF answer:', data.correct_answer);
        if (!data.correct_answer && data.correct_answer !== '0') {
            showErrorMessage(window.translations?.must_select_tf_answer || 'يجب اختيار الإجابة الصحيحة (صح أم خطأ)');
            return false;
        }
    }

    if (data.type === 'numeric') {
        console.log('Validating numeric answer:', data.correct_answer);
        if (!data.correct_answer || data.correct_answer.toString().trim() === '') {
            showErrorMessage(window.translations?.numeric_answer_required || 'يجب إدخال الإجابة الرقمية');
            return false;
        }
    }

    return true;
}

// حذف السؤال
function deleteQuestion(questionId) {
    if (!confirm(window.translations?.confirm_delete_question || 'هل أنت متأكد من حذف هذا السؤال؟ لا يمكن التراجع عن هذا الإجراء')) {
        return;
    }

    const questionCard = $(`[data-question-id="${questionId}"]`);
    if (questionCard.length === 0) {
        showErrorMessage(window.translations?.question_not_found || 'لم يتم العثور على السؤال');
        return;
    }

    const isNewQuestion = questionId.toString().startsWith('new-');

    if (isNewQuestion) {
        // حذف مباشر للأسئلة الجديدة غير المحفوظة
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
        data: {
            id: questionId
        },
        success: function (response) {
            showLoading(false);
            questionCard.fadeOut(300, function () {
                $(this).remove();
                checkEmptyState();
            });
            showSuccessMessage(window.translations?.question_deleted_successfully || 'تم حذف السؤال بنجاح');
        },
        error: function (xhr) {
            showLoading(false);
            showErrorMessage((window.translations?.delete_question_error || 'خطأ في حذف السؤال') + ': ' + (xhr.responseJSON?.message || window.translations?.unknown_error || 'خطأ غير معروف'));
        }
    });
}

// التحقق من حالة عدم وجود أسئلة
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

// تحديث حالة التقدم
function updateQuestionStatus(part) {
    if (part === 'part1') {
        questionStatus.part1_count++;
        if (questionStatus.part1_count >= questionStatus.part1_max) {
            questionStatus.part1_complete = true;
        }
    } else if (part === 'part2') {
        questionStatus.part2_count++;
        if (questionStatus.part2_count >= questionStatus.part2_max) {
            questionStatus.part2_complete = true;
        }
    }

    questionStatus.all_complete = questionStatus.part1_complete && questionStatus.part2_complete;

    // تحديث واجهة المستخدم
    setTimeout(() => {
        location.reload(); // إعادة تحميل لتحديث العرض
    }, 1000);
}

// عرض رسالة النجاح
function showSuccessMessage(message) {
    const alert = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.main-content').prepend(alert);
    setTimeout(() => {
        $('.alert-success').fadeOut();
    }, 5000);
}

// عرض رسالة الخطأ
function showErrorMessage(message) {
    const alert = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.main-content').prepend(alert);
    setTimeout(() => {
        $('.alert-danger').fadeOut();
    }, 7000);
}

// عرض أخطاء التحقق
function showValidationErrors(errors) {
    let errorMessage = 'يرجى تصحيح الأخطاء التالية:<ul>';
    Object.values(errors).forEach(error => {
        if (Array.isArray(error)) {
            error.forEach(err => {
                errorMessage += `<li>${err}</li>`;
            });
        } else {
            errorMessage += `<li>${error}</li>`;
        }
    });
    errorMessage += '</ul>';
    showErrorMessage(errorMessage);
}

// إظهار/إخفاء شاشة التحميل
function showLoading(show) {
    if (show) {
        $('#loadingOverlay').css('display', 'flex');
    } else {
        $('#loadingOverlay').hide();
    }
}

// تحديث ترقيم السؤال
function updateQuestionNumbering(questionCard, selectedPart) {
    const questionId = questionCard.attr('data-question-id');
    if (!questionId || !questionId.toString().startsWith('new-')) return;

    const numbering = questionCard.find(`#question-numbering-${questionId.replace('new-', '')}`);
    if (numbering.length === 0) return;

    if (selectedPart === 'part1') {
        const currentCount = window.questionStatus ? window.questionStatus.part1_count + 1 : 1;
        const maxCount = window.questionStatus ? window.questionStatus.part1_max : '?';
        numbering.text(`(${currentCount} of ${maxCount} - Module 1)`);
    } else if (selectedPart === 'part2') {
        const currentCount = window.questionStatus ? window.questionStatus.part2_count + 1 : 1;
        const maxCount = window.questionStatus ? window.questionStatus.part2_max : '?';
        numbering.text(`(${currentCount} of ${maxCount} - Module 2)`);
    }
}

// ربط حدث تغيير جزء السؤال للأسئلة الجديدة
$(document).on('change', '.question-part', function () {
    const selectedPart = $(this).val();
    const questionCard = $(this).closest('.question-card');

    if (selectedPart) {
        updateQuestionNumbering(questionCard, selectedPart);
    }
});

// دالة مساعدة لتطبيق MathJax
function renderMathJax(element = null) {
    if (typeof MathJax === 'undefined') return;

    try {
        if (element) {
            MathJax.typesetPromise([element]).then(() => {
                console.log('MathJax rendered for element');
            }).catch((err) => {
                console.warn('MathJax rendering failed:', err);
            });
        } else {
            MathJax.typesetPromise().then(() => {
                console.log('MathJax rendered for page');
            }).catch((err) => {
                console.warn('MathJax rendering failed:', err);
            });
        }
    } catch (e) {
        console.warn('MathJax rendering failed:', e);
    }
}

// تطبيق MathJax على العنصر
function renderMath(element) {
    if (typeof MathJax !== 'undefined') {
        const $element = $(element);
        const text = $element.val() || '';

        // التحقق من وجود معادلات رياضية أو أوامر LaTeX
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

        if (hasLatex) {
            // إنشاء div مؤقت لعرض المعادلة
            let previewDiv = $element.siblings('.math-preview');
            if (previewDiv.length === 0) {
                $element.after('<div class="math-preview mt-2 p-2 border rounded bg-light"></div>');
                previewDiv = $element.siblings('.math-preview');
            }

            // تنظيف النص وتحويل بعض الأوامر المتقدمة لتوافق مع MathJax
            let processedText = text
                .replace(/\\item/g, '• ')
                .replace(/\\colorbox\{([^}]+)\}\{([^}]*)\}\{([^}]+)\}/g, '\\bbox[border:2px solid $1;background:$1;color:$2]{$3}')
                .replace(/\\textcolor\{([^}]+)\}\{([^}]+)\}/g, '\\color{$1}{$2}')
                .replace(/\\Mycircled\{([^}]+)\}/g, '\\bbox[border:2px solid black;border-radius:50%;padding:3px]{\\text{$1}}')
                .replace(/\\hspace\{([^}]+)\}/g, '\\phantom{\\rule{$1}{0pt}}')
                .replace(/\\vspace\{([^}]+)\}/g, '\\\\[$1]')
                .replace(/\\Large\{([^}]+)\}/g, '\\large{$1}');

            // عرض النص مع المعادلات
            previewDiv.html('<div class="text-muted small mb-1"><i class="fas fa-calculator me-1"></i> Preview:</div>' + processedText).show();

            // تطبيق MathJax على المعاينة
            setTimeout(() => {
                try {
                    if (typeof MathJax !== 'undefined' && MathJax.typesetPromise) {
                        MathJax.typesetPromise([previewDiv[0]]).then(() => {
                            previewDiv.find('.text-muted').first().html('<i class="fas fa-check-circle text-success me-1"></i> Preview:');
                        }).catch((e) => {
                            console.warn('MathJax rendering error:', e);
                            previewDiv.find('.text-muted').first().html('<i class="fas fa-exclamation-triangle text-warning me-1"></i>خطأ في تفسير المعادلة:');
                            // إظهار تفاصيل الخطأ للمطور
                            if (e.message && e.message.includes('substitution count exceeded')) {
                                previewDiv.append('<div class="small text-danger mt-1">تحذير: هناك مرجع دائري في الـ macro. تجنب استخدام \\varepsilon أو \\pi مباشرة.</div>');
                            }
                        });
                    }
                } catch (e) {
                    console.warn('MathJax rendering error:', e);
                }
            }, 200);
        } else {
            // إخفاء معاينة المعادلة إذا لم تكن هناك معادلات
            $element.siblings('.math-preview').hide();
        }
    }
}