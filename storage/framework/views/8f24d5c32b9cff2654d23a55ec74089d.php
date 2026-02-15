<div class="question-header">
    <div class="d-flex align-items-center">
        <span class="question-number"><?php echo e($question->question_order); ?></span>
        <?php
            $partHeaderLabels = [
                'part1' => 'l.first_part',
                'part2' => 'l.second_part',
                'part3' => 'l.third_part',
                'part4' => 'l.fourth_part',
                'part5' => 'l.fifth_part',
            ];
            $countField = $question->part . '_questions_count';
            $totalInPart = $question->test->$countField ?? 0;
        ?>
        <small class="text-muted ms-2">
            (<?php echo e($question->question_order); ?> of <?php echo e($totalInPart); ?> - <?php echo app('translator')->get($partHeaderLabels[$question->part] ?? 'l.question_part'); ?>)
        </small>
        <select class="form-select question-type-select ms-3"
            onchange="handleQuestionTypeChange('<?php echo e($question->id); ?>', this.value)">
            <option value="mcq" <?php echo e($question->type === 'mcq' ? 'selected' : ''); ?>><?php echo app('translator')->get('l.mcq'); ?></option>
            <option value="tf" <?php echo e($question->type === 'tf' ? 'selected' : ''); ?>><?php echo app('translator')->get('l.tf'); ?></option>
            <option value="numeric" <?php echo e($question->type === 'numeric' ? 'selected' : ''); ?>><?php echo app('translator')->get('l.numeric'); ?></option>
        </select>

        <button class="btn btn-outline-danger btn-sm ms-2"
            onclick="deleteQuestion('<?php echo e($question->id); ?>', '<?php echo e($question->question_order); ?>')">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>
</div>

<div class="question-body mt-3">
    <div class="row">
        <!-- نص السؤال والصورة -->
        <div class="col-md-8">
            <div class="mb-3">
                <label class="form-label fw-bold"><?php echo app('translator')->get('l.question_text'); ?>:</label>
                <textarea class="form-control question-text-editor" rows="3"
                    placeholder="<?php echo app('translator')->get('l.question_text_placeholder'); ?>" onblur="renderMath(this)"><?php echo e($question->question_text); ?></textarea>
                <small class="form-text text-muted"><?php echo app('translator')->get('l.math_support_note'); ?></small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold"><?php echo app('translator')->get('l.question_image_optional'); ?>:</label>
                <input type="file" class="form-control question-image-input" accept="image/*" 
                    data-question-id="<?php echo e($question->id); ?>"
                    onchange="previewImage(this, 'question', '<?php echo e($question->id); ?>')">
                <small class="form-text text-muted"><?php echo app('translator')->get('l.image_size_limit'); ?></small>

                <!-- معاينة صورة السؤال -->
                <div class="mt-2" id="question-image-preview-<?php echo e($question->id); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($question->question_image): ?>
                        <img src="<?php echo e(asset($question->question_image)); ?>" alt="Question Image" 
                            class="img-thumbnail" style="max-height: 150px;">
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                            onclick="markForRemoval('question', '<?php echo e($question->id); ?>')">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                        <input type="hidden" name="remove_question_image" id="remove-question-<?php echo e($question->id); ?>" value="0">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- ==================== قسم الشرح ==================== -->
            <div class="explanation-section mt-4 pt-3 border-top">
                <h6 class="fw-bold mb-3"><?php echo app('translator')->get('l.question_explanation'); ?></h6>
                
                <!-- نص الشرح -->
                <div class="mb-3">
                    <label class="form-label fw-bold"><?php echo app('translator')->get('l.question_explanation_optional'); ?>:</label>
                    <textarea 
                        class="form-control explanation-text-editor" 
                        rows="3" 
                        placeholder="<?php echo app('translator')->get('l.question_explanation_placeholder'); ?>"
                        onblur="renderMath(this)"
                    ><?php echo e($question->explanation ?? ''); ?></textarea>
                    <small class="form-text text-muted"><?php echo app('translator')->get('l.write_explanation_help'); ?></small>
                </div>

                <!-- صورة الشرح -->
                <div class="mb-3">
                    <label class="form-label fw-bold"><?php echo app('translator')->get('l.explanation_image_optional'); ?>:</label>
                    <input type="file" class="form-control explanation-image-input" accept="image/*"
                        data-question-id="<?php echo e($question->id); ?>"
                        onchange="previewImage(this, 'explanation', '<?php echo e($question->id); ?>')">
                    <small class="form-text text-muted"><?php echo app('translator')->get('l.image_size_limit'); ?></small>

                    <!-- معاينة صورة الشرح -->
                    <div class="mt-2" id="explanation-image-preview-<?php echo e($question->id); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($question->explanation_image): ?>
                            <img src="<?php echo e(asset($question->explanation_image)); ?>" alt="Explanation Image" 
                                class="img-thumbnail" style="max-height: 150px;">
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                                onclick="markForRemoval('explanation', '<?php echo e($question->id); ?>')">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                            <input type="hidden" name="remove_explanation_image" id="remove-explanation-<?php echo e($question->id); ?>" value="0">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- إعدادات السؤال -->
        <div class="col-md-4">
            <div class="question-settings p-3 bg-light rounded">
                <h6 class="fw-bold mb-3"><?php echo app('translator')->get('l.question_settings'); ?></h6>

                <div class="mb-3">
                    <label class="form-label fw-bold"><?php echo app('translator')->get('l.question_part'); ?>:</label>
                    <?php
                        $partSelectLabels = [
                            'part1' => 'l.part_first',
                            'part2' => 'l.part_second',
                            'part3' => 'l.part_third',
                            'part4' => 'l.part_fourth',
                            'part5' => 'l.part_fifth',
                        ];
                    ?>
                    <select class="form-select part-select" required>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partSelectLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partKey => $labelKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $countField = $partKey . '_questions_count';
                                $maxQuestions = $question->test->$countField ?? 0;
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($maxQuestions > 0): ?>
                                <option value="<?php echo e($partKey); ?>" <?php echo e($question->part === $partKey ? 'selected' : ''); ?>>
                                    <?php echo app('translator')->get($labelKey); ?>
                                </option>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>

                <div class="mt-2">
                    <label class="form-label fw-bold"><?php echo app('translator')->get('l.points_label'); ?>:</label>
                    <input type="number" class="form-control score-input" min="1"
                        value="<?php echo e($question->score); ?>">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- خيارات حسب نوع السؤال -->
<div class="mcq-options" style="<?php echo e($question->type === 'mcq' ? '' : 'display: none;'); ?>">
    <label class="form-label fw-bold"><?php echo app('translator')->get('l.options'); ?>:</label>
    <div class="options-list">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($question->options && $question->options->count() > 0): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="option-item" data-option-index="<?php echo e($index); ?>">
                    <div class="option-header">
                        <span class="option-letter"><?php echo e(chr(65 + $index)); ?></span>
                        <input type="radio" name="correct-<?php echo e($question->id); ?>" value="<?php echo e($index); ?>"
                            class="form-check-input ms-2 correct-radio"
                            <?php echo e($option->is_correct ? 'checked' : ''); ?>>
                        <label class="ms-2 small text-muted"><?php echo app('translator')->get('l.correct_answer'); ?></label>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMCQOption(this)"
                                <?php echo e($index < 2 ? 'style=display:none' : ''); ?>>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="option-content">
                        <textarea class="form-control option-text-editor" rows="2"
                            placeholder="<?php echo app('translator')->get('l.option_text_placeholder'); ?>" onblur="renderMath(this)"><?php echo e($option->option_text); ?></textarea>
                        <div class="mt-2">
                            <label class="form-label small"><?php echo app('translator')->get('l.option_image_optional'); ?>:</label>
                            <input type="file" class="form-control option-image-input" accept="image/*"
                                data-question-id="<?php echo e($question->id); ?>"
                                data-option-index="<?php echo e($index); ?>"
                                onchange="previewImage(this, 'option', '<?php echo e($question->id); ?>', '<?php echo e($index); ?>')">
                            <small class="form-text text-muted"><?php echo app('translator')->get('l.image_size_limit'); ?></small>
                            
                            <!-- معاينة صورة الخيار -->
                            <div class="mt-2" id="option-image-preview-<?php echo e($question->id); ?>-<?php echo e($index); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($option->option_image): ?>
                                    <img src="<?php echo e(asset($option->option_image)); ?>" alt="Option Image" 
                                        class="img-thumbnail" style="max-height: 100px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                                        onclick="markOptionForRemoval('<?php echo e($question->id); ?>', <?php echo e($index); ?>)">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                    <input type="hidden" name="remove_option_image[<?php echo e($index); ?>]" 
                                        id="remove-option-<?php echo e($question->id); ?>-<?php echo e($index); ?>" value="0">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <!-- خيار A -->
            <div class="option-item" data-option-index="0">
                <div class="option-header">
                    <span class="option-letter">A</span>
                    <input type="radio" name="correct-<?php echo e($question->id); ?>" value="0"
                        class="form-check-input ms-2 correct-radio" checked>
                    <label class="ms-2 small text-muted"><?php echo app('translator')->get('l.correct_answer'); ?></label>
                </div>
                <div class="option-content">
                    <textarea class="form-control option-text-editor" rows="2"
                        placeholder="<?php echo app('translator')->get('l.option_text_placeholder'); ?>" onblur="renderMath(this)"></textarea>
                    <div class="mt-2">
                        <label class="form-label small"><?php echo app('translator')->get('l.option_image_optional'); ?>:</label>
                        <input type="file" class="form-control option-image-input" accept="image/*"
                            data-question-id="<?php echo e($question->id); ?>"
                            data-option-index="0"
                            onchange="previewImage(this, 'option', '<?php echo e($question->id); ?>', '0')">
                        <small class="form-text text-muted"><?php echo app('translator')->get('l.image_size_limit'); ?></small>
                        <div class="mt-2" id="option-image-preview-<?php echo e($question->id); ?>-0"></div>
                    </div>
                </div>
            </div>

            <!-- خيار B -->
            <div class="option-item" data-option-index="1">
                <div class="option-header">
                    <span class="option-letter">B</span>
                    <input type="radio" name="correct-<?php echo e($question->id); ?>" value="1"
                        class="form-check-input ms-2 correct-radio">
                    <label class="ms-2 small text-muted"><?php echo app('translator')->get('l.correct_answer'); ?></label>
                </div>
                <div class="option-content">
                    <textarea class="form-control option-text-editor" rows="2"
                        placeholder="<?php echo app('translator')->get('l.option_text_placeholder'); ?>" onblur="renderMath(this)"></textarea>
                    <div class="mt-2">
                        <label class="form-label small"><?php echo app('translator')->get('l.option_image_optional'); ?>:</label>
                        <input type="file" class="form-control option-image-input" accept="image/*"
                            data-question-id="<?php echo e($question->id); ?>"
                            data-option-index="1"
                            onchange="previewImage(this, 'option', '<?php echo e($question->id); ?>', '1')">
                        <small class="form-text text-muted"><?php echo app('translator')->get('l.image_size_limit'); ?></small>
                        <div class="mt-2" id="option-image-preview-<?php echo e($question->id); ?>-1"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addMCQOption(this)">
        <i class="fas fa-plus"></i> <?php echo app('translator')->get('l.add_option'); ?>
    </button>
</div>

<!-- خيارات سؤال صح/خطأ -->
<div class="tf-options" style="<?php echo e($question->type === 'tf' ? '' : 'display: none;'); ?>">
    <label class="form-label fw-bold"><?php echo app('translator')->get('l.select_correct_answer'); ?>:</label>
    <div class="d-flex gap-3">
        <div class="form-check">
            <input class="form-check-input tf-radio" type="radio" name="tf-<?php echo e($question->id); ?>" value="true"
                id="tf-true-<?php echo e($question->id); ?>" <?php echo e($question->correct_answer === 'true' ? 'checked' : ''); ?>>
            <label class="form-check-label" for="tf-true-<?php echo e($question->id); ?>">
                <?php echo app('translator')->get('l.true'); ?>
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input tf-radio" type="radio" name="tf-<?php echo e($question->id); ?>" value="false"
                id="tf-false-<?php echo e($question->id); ?>" <?php echo e($question->correct_answer === 'false' ? 'checked' : ''); ?>>
            <label class="form-check-label" for="tf-false-<?php echo e($question->id); ?>">
                <?php echo app('translator')->get('l.false'); ?>
            </label>
        </div>
    </div>
</div>

<!-- سؤال رقمي -->
<div class="numeric-options" style="<?php echo e($question->type === 'numeric' ? '' : 'display: none;'); ?>">
    <label class="form-label fw-bold"><?php echo app('translator')->get('l.numeric_answer_label'); ?>:</label>
    <input type="number" class="form-control numeric-input" step="any"
        value="<?php echo e($question->correct_answer); ?>">
</div>

<div class="question-footer mt-3">
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-success btn-save" onclick="quickSaveQuestion('<?php echo e($question->id); ?>')">
  <i class="fas fa-save me-2"></i><?php echo app('translator')->get('l.save'); ?>
</button>

    </div>
</div>

<!-- ... الكود HTML كما هو ... -->

<script>
// =============================================
// نظام الحفظ المبسط والصحيح
// =============================================


let savingInProgress = false;

function getQuestionCard(questionId) {
  return document.querySelector(`[data-question-id="${questionId}"]`);
}

function showMessage(message, type) {
  document.querySelectorAll('.message-alert').forEach(el => el.remove());

  const div = document.createElement('div');
  div.className = `message-alert alert alert-${type || 'info'}`;
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
  formData.append('_token', '<?php echo e(csrf_token()); ?>');
  formData.append('test_id', '<?php echo e($question->test_id); ?>');

  const isNew = String(questionId).startsWith('new-');
  if (!isNew) formData.append('id', questionId);

  const qText = card.querySelector('.question-text-editor');
  const partSel = card.querySelector('.part-select');
  const scoreIn = card.querySelector('.score-input');
  const typeSel = card.querySelector('.question-type-select');
  const expText = card.querySelector('.explanation-text-editor');

  formData.append('question_text', qText ? qText.value : '');
  formData.append('part', partSel ? partSel.value : 'part1');
  formData.append('score', scoreIn ? scoreIn.value : 15);
  formData.append('type', typeSel ? typeSel.value : 'mcq');
  formData.append('explanation', expText ? expText.value : '');

  const qType = typeSel ? typeSel.value : 'mcq';

  if (qType === 'mcq') {
    const optionEls = card.querySelectorAll('.option-item');
    optionEls.forEach((optEl, index) => {
      const optText = optEl.querySelector('.option-text-editor');
      const correctRadio = optEl.querySelector('.correct-radio');
      const optImgInput = optEl.querySelector('.option-image-input');

      formData.append(`options[${index}][option_text]`, optText ? optText.value : '');
      formData.append(`options[${index}][is_correct]`, correctRadio && correctRadio.checked ? '1' : '0');

      if (optImgInput && optImgInput.files && optImgInput.files[0]) {
        formData.append(`options[${index}][option_image]`, optImgInput.files[0]);
      }

      const removeOpt = optEl.querySelector(`input[id^="remove-option-"]`);
      if (removeOpt) {
        formData.append(`remove_option_image[${index}]`, removeOpt.value || '0');
      }
    });
  }

  if (qType === 'tf') {
    const selectedTF = card.querySelector('.tf-radio:checked');
    formData.append('correct_answer', selectedTF ? selectedTF.value : 'true');
  }

  if (qType === 'numeric') {
    const numericInput = card.querySelector('.numeric-input');
    formData.append('correct_answer', numericInput ? numericInput.value : '0');
  }

  const qImgInput = card.querySelector('.question-image-input');
  if (qImgInput && qImgInput.files && qImgInput.files[0]) {
    formData.append('question_image', qImgInput.files[0]);
  }

  const expImgInput = card.querySelector('.explanation-image-input');
  if (expImgInput && expImgInput.files && expImgInput.files[0]) {
    formData.append('explanation_image', expImgInput.files[0]);
  }

  const removeQ = card.querySelector(`#remove-question-${questionId}`);
  if (removeQ) formData.append('remove_question_image', removeQ.value || '0');

  const removeE = card.querySelector(`#remove-explanation-${questionId}`);
  if (removeE) formData.append('remove_explanation_image', removeE.value || '0');

  const url = isNew
    ? '<?php echo e(route("dashboard.admins.tests-questions-store")); ?>'
    : '<?php echo e(route("dashboard.admins.tests-questions-update")); ?>';

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
        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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
      showMessage(data.message || 'Save failed', 'danger');
      return;
    }

    if (data.success) {
      showMessage(data.message || 'Saved', 'success');

      if (isNew && data.question_id) {
        updateQuestionAfterSave(card, data.question_id, data.question_number || null);
      }
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

function updateQuestionAfterSave(card, realId, questionNumber) {
  const oldId = card.getAttribute('data-question-id');
  card.setAttribute('data-question-id', realId);

  const numberSpan = card.querySelector('.question-number');
  if (numberSpan && questionNumber) numberSpan.textContent = questionNumber;

  const saveBtn = card.querySelector('.btn-save');
  if (saveBtn) saveBtn.setAttribute('onclick', `quickSaveQuestion('${realId}')`);

  const delBtn = card.querySelector('.btn-outline-danger');
  if (delBtn && questionNumber) delBtn.setAttribute('onclick', `deleteQuestion('${realId}', '${questionNumber}')`);

  const typeSelect = card.querySelector('.question-type-select');
  if (typeSelect) typeSelect.setAttribute('onchange', `handleQuestionTypeChange('${realId}', this.value)`);

  const radios = card.querySelectorAll('input[type="radio"]');
  radios.forEach(r => {
    const n = r.getAttribute('name');
    if (n && oldId && n.includes(oldId)) r.setAttribute('name', n.replace(oldId, realId));
  });
}

function previewImage(input, type, questionId, optionIndex) {
  if (!input.files || !input.files[0]) return;

  const reader = new FileReader();
  reader.onload = function(e) {
    let previewId = '';
    if (type === 'question') previewId = `question-image-preview-${questionId}`;
    if (type === 'explanation') previewId = `explanation-image-preview-${questionId}`;
    if (type === 'option') previewId = `option-image-preview-${questionId}-${optionIndex}`;

    const container = document.getElementById(previewId);
    if (!container) return;

    container.innerHTML = `
      <div class="mt-2">
        <img src="${e.target.result}" class="img-thumbnail" style="max-height:150px">
      </div>
    `;
  };
  reader.readAsDataURL(input.files[0]);
}

function markForRemoval(type, questionId) {
  const id = type === 'question' ? `remove-question-${questionId}` : `remove-explanation-${questionId}`;
  const input = document.getElementById(id);
  if (input) input.value = '1';
}

function markOptionForRemoval(questionId, optionIndex) {
  const input = document.getElementById(`remove-option-${questionId}-${optionIndex}`);
  if (input) input.value = '1';
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

// تحديث السؤال بعد الحفظ
// =============================================
function updateQuestionAfterSave(questionCard, questionId, questionNumber) {
    questionCard.setAttribute('data-question-id', questionId);
    
    // تحديث رقم السؤال
    const numberSpan = questionCard.querySelector('.question-number');
    if (numberSpan && questionNumber) {
        numberSpan.textContent = questionNumber;
    }
    
    // تحديث زر الحذف
    const deleteBtn = questionCard.querySelector('.btn-outline-danger');
    if (deleteBtn) {
        deleteBtn.setAttribute('onclick', `deleteQuestion('${questionId}', '${questionNumber}')`);
    }
    
    // تحديث زر النوع
    const typeSelect = questionCard.querySelector('.question-type-select');
    if (typeSelect) {
        typeSelect.setAttribute('onchange', `handleQuestionTypeChange('${questionId}', this.value)`);
    }
    
    // تحديث radio buttons name
    const radios = questionCard.querySelectorAll('input[type="radio"]');
    radios.forEach(radio => {
        const oldName = radio.getAttribute('name');
        if (oldName && oldName.includes('new-')) {
            const newName = oldName.replace(/new-\d+/, questionId);
            radio.setAttribute('name', newName);
        }
    });
}

// =============================================
// دالة عرض الرسائل
// =============================================
function showMessage(message, type = 'info') {
    // إزالة رسائل سابقة
    document.querySelectorAll('.message-alert').forEach(el => el.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `message-alert alert alert-${type}`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 500px;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease;
        white-space: pre-line;
    `;
    
    let icon = '';
    switch(type) {
        case 'success': icon = '✅ '; break;
        case 'error': icon = '❌ '; break;
        case 'warning': icon = '⚠️ '; break;
        case 'info': icon = 'ℹ️ '; break;
    }
    
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <span style="font-size: 1.3em; margin-right: 10px;">${icon}</span>
            <span style="flex: 1;">${message}</span>
            <button type="button" class="btn-close btn-sm" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // إزالة بعد 5 ثواني
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }
    }, 5000);
}

// =============================================
// إضافة أنماط CSS
// =============================================
if (!document.querySelector('#message-styles')) {
    const style = document.createElement('style');
    style.id = 'message-styles';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .message-alert { animation: slideIn 0.3s ease; }
        .btn-save:disabled { opacity: 0.6; cursor: not-allowed; }
    `;
    document.head.appendChild(style);
}

// =============================================
// تهيئة الصفحة
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Save system loaded');
    
    // اختصار Ctrl+S
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const activeElement = document.activeElement;
            const questionCard = activeElement.closest('.question-card');
            if (questionCard) {
                const questionId = questionCard.getAttribute('data-question-id');
                if (questionId) quickSaveQuestion(questionId);
            }
        }
    });
});

// =============================================
// دوال دعم أخرى
// =============================================

// معاينة الصور
function previewImage(input, type, questionId, optionIndex = null) {
    if (!input.files || !input.files[0]) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        let previewId;
        if (type === 'question') previewId = `question-image-preview-${questionId}`;
        else if (type === 'explanation') previewId = `explanation-image-preview-${questionId}`;
        else if (type === 'option' && optionIndex !== null) previewId = `option-image-preview-${questionId}-${optionIndex}`;
        
        const container = document.getElementById(previewId);
        if (container) {
            container.innerHTML = `
                <div class="mt-2">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;">
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                        onclick="removeImagePreview('${type}', '${questionId}', '${optionIndex}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        }
    };
    reader.readAsDataURL(input.files[0]);
}

function removeImagePreview(type, questionId, optionIndex = null) {
    let previewId;
    if (type === 'question') previewId = `question-image-preview-${questionId}`;
    else if (type === 'explanation') previewId = `explanation-image-preview-${questionId}`;
    else if (type === 'option' && optionIndex !== null) previewId = `option-image-preview-${questionId}-${optionIndex}`;
    
    const container = document.getElementById(previewId);
    if (container) {
        container.innerHTML = '';
        // مسح input الملف
        let input;
        if (type === 'question') input = document.querySelector(`.question-image-input[data-question-id="${questionId}"]`);
        else if (type === 'explanation') input = document.querySelector(`.explanation-image-input[data-question-id="${questionId}"]`);
        else if (type === 'option' && optionIndex !== null) {
            input = document.querySelector(`.option-image-input[data-question-id="${questionId}"][data-option-index="${optionIndex}"]`);
        }
        if (input) input.value = '';
    }
}

function markForRemoval(type, questionId) {
    if (confirm('Remove this image?')) {
        const input = document.getElementById(`remove-${type}-${questionId}`);
        if (input) input.value = '1';
    }
}

function markOptionForRemoval(questionId, optionIndex) {
    if (confirm('Remove option image?')) {
        const input = document.getElementById(`remove-option-${questionId}-${optionIndex}`);
        if (input) input.value = '1';
    }
}
</script><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/back/admins/tests/questions/partials/question-view.blade.php ENDPATH**/ ?>