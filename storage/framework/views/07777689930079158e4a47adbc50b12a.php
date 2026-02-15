<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('l.test_questions'); ?> - <?php echo e($test->name); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<script>
window.MathJax = {
    tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['$$', '$$'], ['\\[', '\\]']],
        processEscapes: true,
        processEnvironments: true,
        packages: {'[+]': ['ams', 'newcommand', 'mathtools', 'cancel', 'color', 'bbox']},
        macros: {
            // تعريف الألوان
            colorbox: ['\\bbox[border:1px solid #1;background:#1;color:#2;padding:2px]{#3}', 3],
            textcolor: ['\\color{#1}{#2}', 2],

            // تعريف الدوائر المرقمة
            Mycircled: ['\\bbox[border:2px solid black;border-radius:50%;padding:3px]{\\text{#1}}', 1],

            // تعريف item
            item: '\\bullet\\;',

            // تعريف hspace و vspace
            hspace: ['\\phantom{\\rule{#1}{0pt}}', 1],
            vspace: ['\\\\[#1]', 1],

            // تعريفات إضافية للرياضيات
            dfrac: ['\\displaystyle\\frac{#1}{#2}', 2],

            // تعريف Large
            Large: ['\\large{#1}', 1]
        }
    },
    options: {
        skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
    },
    chtml: {
        scale: 1.0,
        minScale: 0.5,
        matchFontHeight: false
    },
    startup: {
        ready: function () {
            MathJax.startup.defaultReady();
            console.log('✅ MathJax loaded successfully for test questions');
        }
    }
};
</script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<link rel="stylesheet" href="<?php echo e(asset('css/tests-questions.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show lectures')): ?>
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0"><?php echo app('translator')->get('l.test_questions'); ?> - <span class="text-primary"><?php echo e($test->name); ?></span></h4>
                    <p class="text-muted mb-0"><?php echo e($test->course->name ?? ''); ?></p>
                </div>
                <div>
                    <a href="<?php echo e(route('dashboard.admins.tests-show', ['id' => encrypt($test->id)])); ?>" class="btn btn-info waves-effect waves-light">
                        <i class="fas fa-eye me-2"></i>
                        <?php echo app('translator')->get('l.view_test'); ?>
                    </a>
                    <a href="<?php echo e(route('dashboard.admins.tests-preview', ['id' => encrypt($test->id)])); ?>" class="btn btn-success waves-effect waves-light" target="_blank">
                        <i class="fas fa-eye me-2"></i>
                        <?php echo app('translator')->get('l.preview_as_student'); ?>
                    </a>
                    <a href="<?php echo e(route('dashboard.admins.tests')); ?>" class="btn btn-secondary waves-effect waves-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        <?php echo app('translator')->get('l.back_to_list'); ?>
                    </a>
                </div>
            </div>

            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex flex-row flex-wrap justify-content-center w-100">
                                <?php
                                    $activeModules = array_filter($modules, fn($m) => ($m['max'] ?? 0) > 0);
                                ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activeModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partKey => $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="mx-3 mb-2 text-center" style="min-width: 120px;">
                                        <h6 class="<?php echo e($loop->index % 2 == 0 ? 'text-primary' : 'text-success'); ?>">
                                            <?php echo e($module['label']); ?>

                                        </h6>
                                        <div class="badge bg-primary fs-6">
                                            <?php echo e($module['current']); ?>/<?php echo e($module['max']); ?>

                                        </div>
                                        <div class="small"><?php echo app('translator')->get('l.questions'); ?></div>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($module['remaining'] > 0): ?>
                                            <div class="text-warning small mt-1">
                                                <?php echo e($module['remaining']); ?> <?php echo app('translator')->get('l.questions_remaining'); ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-success small mt-1">
                                                <?php echo app('translator')->get('l.completed'); ?>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($activeModules) === 0): ?>
                                    <div class="text-muted">
                                        <?php echo app('translator')->get('l.no_modules_defined'); ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Alert -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$allModulesComplete): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong><?php echo app('translator')->get('l.questions_incomplete'); ?>:</strong>
                    <?php $first = true; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($module['max'] ?? 0) > 0 && $module['remaining'] > 0): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$first): ?>, <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php echo e($module['label']); ?>: <?php echo e($module['remaining']); ?> <?php echo app('translator')->get('l.questions_remaining'); ?>
                            <?php $first = false; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong><?php echo app('translator')->get('l.test_ready'); ?>!</strong> <?php echo app('translator')->get('l.all_questions_added'); ?>.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Questions Container -->
            <div class="quiz-container">
                <div id="questionsContainer">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="question-card" data-question-id="<?php echo e($question->id); ?>">
                            <?php echo $__env->make('themes.default.back.admins.tests.questions.partials.question-view', ['question' => $question, 'index' => $index], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-5" id="emptyState">
                            <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted"><?php echo app('translator')->get('l.no_questions_yet'); ?></h5>
                            <p class="text-muted"><?php echo app('translator')->get('l.start_adding_first_question'); ?></p>
                            <button type="button" class="btn btn-primary" onclick="addNewQuestion()">
                                <i class="fas fa-plus me-2"></i>
                                <?php echo app('translator')->get('l.add_first_question'); ?>
                            </button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden"><?php echo app('translator')->get('l.loading'); ?>...</span>
            </div>
            <p class="mt-2 mb-0"><?php echo app('translator')->get('l.saving'); ?>...</p>
        </div>
    </div>

    <!-- Floating Add Question Button -->
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add lectures')): ?>
    <button type="button" class="floating-add-btn" onclick="addNewQuestion()" title="<?php echo app('translator')->get('l.add_new_question'); ?>" id="floatingAddBtn">
        <i class="fas fa-plus"></i>
    </button>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
// تمرير البيانات المطلوبة للـ JavaScript
window.testId = '<?php echo e(encrypt($test->id)); ?>';
window.questionStatus = <?php echo json_encode($questionStatus, 15, 512) ?>;

window.modules = <?php echo json_encode($modules, 15, 512) ?>; // بيانات الموديولات
window.availableParts = <?php echo json_encode($modules, 15, 512) ?>; // ✅ نستخدمها في الـ JS للأجزاء المتاحة
window.Laravel = {
    csrfToken: '<?php echo e(csrf_token()); ?>'
};
window.routes = {
    questionsStore: '<?php echo e(route("dashboard.admins.tests-questions-store")); ?>',
    questionsUpdate: '<?php echo e(route("dashboard.admins.tests-questions-update")); ?>',
    questionsDelete: '<?php echo e(route("dashboard.admins.tests-questions-delete")); ?>'
};
window.translations = {
    all_questions_added_already: '<?php echo app('translator')->get("l.all_questions_added_already"); ?>',
    max_options_limit: '<?php echo app('translator')->get("l.max_options_limit"); ?>',
    question_not_found: '<?php echo app('translator')->get("l.question_not_found"); ?>',
    question_text_required: '<?php echo app('translator')->get("l.question_text_required"); ?>',
    question_part_required: '<?php echo app('translator')->get("l.question_part_required"); ?>',
    min_two_options_required: '<?php echo app('translator')->get("l.min_two_options_required"); ?>',
    must_select_correct_answer: '<?php echo app('translator')->get("l.must_select_correct_answer"); ?>',
    must_select_tf_answer: '<?php echo app('translator')->get("l.must_select_tf_answer"); ?>',
    numeric_answer_required: '<?php echo app('translator')->get("l.numeric_answer_required"); ?>',
    save_question_error: '<?php echo app('translator')->get("l.save_question_error"); ?>',
    delete_question_error: '<?php echo app('translator')->get("l.delete_question_error"); ?>',
    unknown_error: '<?php echo app('translator')->get("l.unknown_error"); ?>',
    question_saved_successfully: '<?php echo app('translator')->get("l.question_saved_successfully"); ?>',
    question_deleted_successfully: '<?php echo app('translator')->get("l.question_deleted_successfully"); ?>',
    confirm_delete_question: '<?php echo app('translator')->get("l.confirm_delete_question"); ?>',
    multiple_choice: '<?php echo app('translator')->get("l.multiple_choice"); ?>',
    true_false: '<?php echo app('translator')->get("l.true_false"); ?>',
    numeric_question: '<?php echo app('translator')->get("l.numeric_question"); ?>',

    question_text: '<?php echo app('translator')->get("l.question_text"); ?>',
    question_text_placeholder: '<?php echo app('translator')->get("l.question_text_placeholder"); ?>',
    math_support_note: '<?php echo app('translator')->get("l.math_support_note"); ?>',
    question_image_optional: '<?php echo app('translator')->get("l.question_image_optional"); ?>',
    image_size_limit: '<?php echo app('translator')->get("l.image_size_limit"); ?>',
    question_part: '<?php echo app('translator')->get("l.question_part"); ?>',
    select_part: '<?php echo app('translator')->get("l.select_part"); ?>',

    // أسماء الأجزاء (اختياري)
    part_first: '<?php echo app('translator')->get("l.first_part"); ?>',
    part_second: '<?php echo app('translator')->get("l.second_part"); ?>',
    part_third: '<?php echo app('translator')->get("l.third_part"); ?>',
    part_fourth: '<?php echo app('translator')->get("l.fourth_part"); ?>',
    part_fifth: '<?php echo app('translator')->get("l.fifth_part"); ?>',

    points_label: '<?php echo app('translator')->get("l.points_label"); ?>',
    question_explanation_optional: '<?php echo app('translator')->get("l.question_explanation_optional"); ?>',
    question_explanation_placeholder: '<?php echo app('translator')->get("l.question_explanation_placeholder"); ?>',
    options: '<?php echo app('translator')->get("l.options"); ?>',
    correct_answer: '<?php echo app('translator')->get("l.correct_answer"); ?>',
    option_text_placeholder: '<?php echo app('translator')->get("l.option_text_placeholder"); ?>',
    add_option: '<?php echo app('translator')->get("l.add_option"); ?>',
    correct_answer_label: '<?php echo app('translator')->get("l.correct_answer_label"); ?>',
    true: '<?php echo app('translator')->get("l.true"); ?>',
    false: '<?php echo app('translator')->get("l.false"); ?>',
    correct_numeric_answer: '<?php echo app('translator')->get("l.correct_numeric_answer"); ?>',
    enter_correct_number: '<?php echo app('translator')->get("l.enter_correct_number"); ?>',
    decimal_numbers_allowed: '<?php echo app('translator')->get("l.decimal_numbers_allowed"); ?>',
    mcq: '<?php echo app('translator')->get("l.mcq"); ?>',
    tf: '<?php echo app('translator')->get("l.tf"); ?>',
    numeric: '<?php echo app('translator')->get("l.numeric"); ?>',
    save: '<?php echo app('translator')->get("l.save"); ?>',
    delete: '<?php echo app('translator')->get("l.delete"); ?>',
    numbering_will_be_set: '<?php echo app('translator')->get("l.numbering_will_be_set"); ?>',
    explanation_image_optional: '<?php echo app('translator')->get("l.explanation_image_optional"); ?>',
    option_image_optional: '<?php echo app('translator')->get("l.option_image_optional"); ?>',
    default_score: '<?php echo e($test->default_question_score); ?>',
};
</script>

<script src="<?php echo e(asset('js/tests-questions.js')); ?>"></script>


<script>
(function () {
    // نحول window.modules إلى Array
    function getModulesArray() {
        const src = window.modules || {};
        if (Array.isArray(src)) {
            return src;
        }
        return Object.keys(src).map(function (key) {
            const m = src[key] || {};
            if (!m.key) {
                m.key = key;
            }
            return m;
        });
    }

    function rebuildModuleSelects() {
        const modulesArr = getModulesArray();
        if (!modulesArr.length) return;

        // نبحث عن أي select يحتوي على نصوص "Module X (0/0)"
        const selects = document.querySelectorAll('select');
        selects.forEach(function (select) {
            const options = Array.from(select.options || []);
            const hasOldPattern = options.some(function (opt) {
                const txt = (opt.textContent || '').trim();
                return /^Module [1-5] \(0\/0\)$/.test(txt);
            });

            if (!hasOldPattern) {
                return; // ليس هذا هو Select الموديولات
            }

            const oldValue = select.value;

            // نفرغ الخيارات القديمة
            while (select.firstChild) {
                select.removeChild(select.firstChild);
            }

            // خيار افتراضي
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = (window.translations && window.translations.select_part)
                ? window.translations.select_part
                : 'Select Module';
            select.appendChild(placeholder);

            // نضيف الموديولات التي max > 0 وبها أسئلة متبقية
            modulesArr.forEach(function (m) {
                if (!m) return;

                const max = m.max || 0;
                const current = m.current || 0;
                const remaining = (typeof m.remaining !== 'undefined') ? m.remaining : (max - current);
                const canAdd = (m.hasOwnProperty('can_add')) ? m.can_add : (remaining > 0);

                if (max <= 0) return;
                if (!canAdd) return; // مكتمل → لا يظهر

                const opt = document.createElement('option');
                opt.value = m.key;
                opt.textContent = m.label + ' (' + current + '/' + max + ')';
                select.appendChild(opt);
            });

            // لو كانت هناك قيمة سابقة ومازالت موجودة نحافظ عليها
            if (oldValue) {
                const exists = Array.from(select.options).some(function (o) { return o.value === oldValue; });
                if (exists) {
                    select.value = oldValue;
                }
            }
        });
    }
    document.addEventListener('DOMContentLoaded', rebuildModuleSelects);
    // لأن الأسئلة تُضاف ديناميكياً، نعيد البناء كل ثانية
    setInterval(rebuildModuleSelects, 1000);
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('themes.default.layouts.back.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/back/admins/tests/questions/index.blade.php ENDPATH**/ ?>