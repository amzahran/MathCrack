<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('l.test_preview'); ?> - <?php echo e($test->name); ?>

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
            colorbox: ['\\bbox[border:1px solid #1;background:#1;color:#2;padding:2px]{#3}', 3],
            textcolor: ['\\color{#1}{#2}', 2],
            Mycircled: ['\\bbox[border:2px solid black;border-radius:50%;padding:3px]{\\text{#1}}', 1],
            item: '\\bullet\\;',
            hspace: ['\\phantom{\\rule{#1}{0pt}}', 1],
            vspace: ['\\\\[#1]', 1],
            dfrac: ['\\displaystyle\\frac{#1}{#2}', 2],
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
            console.log('✅ MathJax loaded for test preview');
        }
    }
};
</script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<link rel="stylesheet" href="<?php echo e(asset('css/tests-questions.css')); ?>">
<style>
/* ---------- ألوان عامة قريبة من MathCrack ---------- */
:root {
    --mc-purple: #4c43f5; /* الأزرق البنفسجي */
    --mc-purple-light: #f4f5ff; /* خلفية الخيارات */
    --mc-purple-soft: #eef0ff; /* خلفية الإجابة الصحيحة */
    --mc-border: #dfe3f0;
}

/* ---------- كارت السؤال في المعاينة ---------- */
.preview-mode .question-card {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    background: #ffffff;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* رقم السؤال */
.question-number {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--mc-purple);
    color: #fff;
    font-weight: 600;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* بادج نوع السؤال */
.question-type-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.mcq-badge {
    background: #e8f5e9;
    color: #2e7d32;
}

.tf-badge {
    background: #fff3e0;
    color: #ef6c00;
}

.numeric-badge {
    background: #e3f2fd;
    color: #1565c0;
}

/* ---------- جزء الموديول (Module / Part) ---------- */
.part-section {
    margin-bottom: 40px;
    padding: 25px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 0 3px 15px rgba(0,0,0,0.05);
}

.part-header {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

.part-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--mc-purple);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 20px;
    margin-right: 20px;
    box-shadow: 0 4px 12px rgba(76,67,245,0.35);
}

.part-title {
    margin: 0;
    color: #333;
    font-weight: 600;
}

.part-info {
    color: #666;
    font-size: 14px;
    margin-top: 5px;
}

/* ---------- كروت المعلومات العامة ---------- */
.test-preview-container {
    padding: 25px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-top: 20px;
}

.test-header {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.05);
}

.test-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 10px;
}

.test-info {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.info-item {
    display: flex;
    align-items: center;
    color: #555;
}

.info-item i {
    margin-right: 8px;
    font-size: 16px;
}


/* الحاوية الرئيسية */
.options-grid {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
/* صندوق الخيار (مختصر ومصغّر) */
.option-item {
    display: flex;
    align-items: center;
    gap: 10px;

    width: 100%;
    padding: 4px 8px !important;   /* صغرنا الارتفاع */
    min-height: 22px !important;

    background: var(--mc-purple-light);
    border: 1px solid var(--mc-border);
    border-radius: 8px;
    transition: 0.2s;
}

/* Hover */
.option-item:hover {
    background: #e9ebff;
    border-color: #c8cdf3;
}

/* دائرة حرف الخيار */
.option-letter {
    width: 24px !important;
    height: 24px !important;

    border-radius: 50%;
    background: var(--mc-purple);
    color: #fff;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 13px !important;
    font-weight: 700;
}

/* الإجابة الصحيحة فقط */
.option-item.correct-answer {
    background: #d9f4df !important;
    border-color: #4caf50 !important;
}

.option-item.correct-answer .option-letter {
    background: #4caf50 !important;
}


/* دائرة الحرف في الإجابة الصحيحة */
.option-item.correct-answer .option-letter {
    background: #4caf50 !important;
    border-color: #4caf50 !important;
    color: #fff !important;
}

/* النص داخل الخيار */
.option-content {
    flex: 1;
    font-size: 15px;
    color: #263238;
}

/* ---------- تمييز الإجابة الصحيحة فقط ---------- */
.option-item.correct-answer {
    background: #e6f4ea;              /* أخضر فاتح مثل الصورة */
    border-color: #34a853;
    box-shadow: 0 0 0 1px rgba(52,168,83,0.25);
}

.option-item.correct-answer .option-letter {
    background: #34a853;
    border-color: #34a853;
    color: #ffffff;
}

/* صح/خطأ والعددي يستعمل نفس الكارت */
.option-icon {
    font-size: 18px;
    margin-right: 8px;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* الحاوية الرئيسية للخيارات */
.options-grid {
    display: flex;
    flex-direction: column;
    gap: 4px !important;
}

/* صندوق الخيار (ارتفاع صغير) */
.question-card .options-grid .option-item {
    display: flex;
    align-items: center;
    gap: 6px;

    width: 100%;
    padding: 2px 8px !important;   /* لو لسه عالي قلليها */
    min-height: 14px !important;   /* تقدري تخليها 22 */

    background: var(--mc-purple-light);
    border: 1px solid var(--mc-border);
    border-radius: 8px;
    cursor: default;
    transition: 0.2s;
}

/* دائرة حرف A B C D */
.question-card .options-grid .option-item .option-letter {
    width: 20px !important;
    height: 20px !important;
    font-size: 11px !important;
    line-height: 20px !important;

    border-radius: 50%;
    background: var(--mc-purple);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

/* النص داخل الخيار */
.question-card .options-grid .option-item .option-content {
    font-size: 13px !important;
    line-height: 1.1 !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

/* تمييز الإجابة الصحيحة */
.question-card .options-grid .option-item.correct-answer {
    background: #e6f4ea !important;
    border-color: #34a853 !important;
    box-shadow: 0 0 0 1px rgba(52,168,83,0.25);
}

.question-card .options-grid .option-item.correct-answer .option-letter {
    background: #34a853 !important;
    border-color: #34a853 !important;
}

/* ---------- الطباعة ---------- */
@media print {
    .no-print {
        display: none !important;
    }

    .question-card {
        page-break-inside: avoid;
        border: 1px solid #ccc !important;
        box-shadow: none !important;
    }

    .part-section {
        page-break-inside: avoid;
    }
}

/* ---------- شاشة التحميل ---------- */
#loadingOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: none;
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.loading-spinner {
    text-align: center;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}


/* توسيط صورة السؤال الأساسية */
.question-main-image {
    display: block;
    margin: 10px auto;
    max-width: 100%;
}

/* توسيط صورة الشرح الأساسية */
.explanation-main-image {
    display: block;
    margin: 10px auto;
    max-width: 100%;
}

/* تصغير ارتفاع صندوق الخيار فعلاً */
.options-grid .option-item {
    padding: 4px 14px !important;  /* يقلل المسافة فوق وتحت */
    min-height: 28px !important;   /* ارتفاع أقل */
}

/* تصغير حجم دائرة حرف الاختيار */
.options-grid .option-item .option-letter {
    width: 22px !important;
    height: 22px !important;
    font-size: 13px !important;
}

/* تقليل ارتفاع صندوق الخيار */
.option-item {
    padding: 4px 8px !important;   /* تقليل المسافات الداخلية */
    min-height: 25px !important;    /* تصغير الارتفاع */
}

/* تقليل حجم دائرة حرف A B C */
.option-letter {
    width: 22px !important;
    height: 22px !important;
    font-size: 12px !important;
    line-height: 22px !important;
}

/* تقليل ارتفاع النص داخل الخيار */
.option-content {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    line-height: 1.2 !important;
}

/* تقليل المسافات بين الخيارات */
.options-grid {
    gap: 6px !important;
}

/* تقليل ارتفاع صندوق الخيار */
.question-card .options-grid .option-item {
    display: flex;
    align-items: center;
    gap: 8px;

    width: 100%;
    padding: 4px 12px !important;   /* ده أهم سطر للارتفاع */
    min-height: 34px !important;    /* ولو لسه عالي نخلّيها 30 أو 28 */

    background: var(--mc-purple-light);
    border: 1px solid var(--mc-border);
    border-radius: 10px;

    cursor: default;
    transition: 0.25s ease-in-out;
}

/* تصغير دائرة الحرف */
.question-card .options-grid .option-item .option-letter {
    width: 22px !important;
    height: 22px !important;
    font-size: 12px !important;
    line-height: 22px !important;
}

/* تصغير النص داخل الخيار */
.question-card .options-grid .option-item .option-content {
    font-size: 14px !important;
    line-height: 1.2 !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

/* تقليل المسافة بين الكروت */
.question-card .options-grid {
    gap: 6px !important;
}

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0"><?php echo app('translator')->get('l.test_preview'); ?> - <span class="text-primary"><?php echo e($test->name); ?></span></h4>
                <p class="text-muted mb-0"><?php echo e($test->course->name ?? ''); ?></p>
            </div>
            <div class="no-print">
                <a href="<?php echo e(route('dashboard.admins.tests-show', ['id' => encrypt($test->id)])); ?>" class="btn btn-info waves-effect waves-light">
                    <i class="fas fa-eye me-2"></i>
                    <?php echo app('translator')->get('l.view_test'); ?>
                </a>
                <a href="<?php echo e(route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)])); ?>" class="btn btn-secondary waves-effect waves-light">
                    <i class="fas fa-arrow-left me-2"></i>
                    <?php echo app('translator')->get('l.back_to_questions'); ?>
                </a>
                <button onclick="window.print()" class="btn btn-outline-primary waves-effect waves-light">
                    <i class="fas fa-print me-2"></i>
                    <?php echo app('translator')->get('l.print'); ?>
                </button>
                <button onclick="copyPreviewLink()" class="btn btn-outline-success waves-effect waves-light">
                    <i class="fas fa-link me-2"></i>
                    <?php echo app('translator')->get('l.copy_link'); ?>
                </button>
            </div>
        </div>

        <!-- Cards for Modules Status -->
        <div class="card mb-4 no-print">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex flex-row flex-wrap justify-content-center w-100">
                            <?php
                                // حساب بيانات الموديولات للعرض
                                $modulesData = [];
                                $totalQuestions = 0;
                                for ($i = 1; $i <= 5; $i++) {
                                    $field = 'part' . $i . '_questions_count';
                                    $max = $test->$field ?? 0;
                                    if ($max > 0) {
                                        $current = count($modulesQuestions[$i] ?? []);
                                        $totalQuestions += $current;
                                        $modulesData[] = [
    'key' => 'part' . $i,
    'label' => 'Module ' . $i,
    'current' => $current,
    'max' => $max,
    'remaining' => $max - $current
];

                                    }
                                }
                            ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $modulesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($modulesData) === 0): ?>
                                <div class="text-muted">
                                    <?php echo app('translator')->get('l.no_modules_defined'); ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Info -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo app('translator')->get('l.test_info'); ?></h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-book text-primary me-2"></i>
                                <strong><?php echo app('translator')->get('l.course'); ?>:</strong> <?php echo e($test->course->name ?? 'N/A'); ?>

                            </li>
                            <li class="mb-2">
                                <i class="fas fa-clock text-success me-2"></i>
                                <strong><?php echo app('translator')->get('l.total_time'); ?>:</strong> <?php echo e($totalTime ?? 0); ?> <?php echo app('translator')->get('l.minutes'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-star text-warning me-2"></i>
                                <strong><?php echo app('translator')->get('l.total_score'); ?>:</strong> <?php echo e($test->total_score); ?> <?php echo app('translator')->get('l.points'); ?>
                            </li>
                            <li>
                                <i class="fas fa-question-circle text-info me-2"></i>
                                <strong><?php echo app('translator')->get('l.total_questions'); ?>:</strong> <?php echo e($test->total_questions_count ?? $test->questions()->count()); ?>

                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo app('translator')->get('l.test_description'); ?></h6>
                        <p class="mb-0"><?php echo e($test->description ?? trans('l.no_description')); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Preview -->
        <div class="test-preview-container">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalQuestionsAdded > 0): ?>
                <!-- Loop for each part -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($partNumber = 1; $partNumber <= 5; $partNumber++): ?>
                    <?php
                        $questions = $modulesQuestions[$partNumber] ?? collect();
                        $questionsCount = $questions->count();
                        
                        // تخطي الأجزاء الفارغة
                        if ($questionsCount === 0 && ($partsStats[$partNumber]['expected_count'] ?? 0) === 0) {
                            continue;
                        }
                    ?>
                    
                    <div class="part-section">
                        <div class="part-header">
                            <div class="part-icon"><?php echo e($partNumber); ?></div>
                            <div>
                                <h2 class="part-title">Module <?php echo e($partNumber); ?></h2>

                                <div class="part-info">
                                    <?php echo e($questionsCount); ?> <?php echo app('translator')->get('l.questions'); ?> •
                                    <?php echo e($partsStats[$partNumber]['time_minutes'] ?? 0); ?> <?php echo app('translator')->get('l.minutes'); ?> •
                                    <?php echo e($partsStats[$partNumber]['points_sum'] ?? 0); ?> <?php echo app('translator')->get('l.points'); ?>
                                </div>
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($questionsCount > 0): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="question-card preview-mode">
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
                                                $partLabel = $partHeaderLabels[$question->part] ?? 'l.question_part';
                                            ?>
                                            
                                            <div class="question-type-badge ms-2 <?php echo e($question->type); ?>-badge">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($question->type):
                                                    case ('mcq'): ?> <?php echo app('translator')->get('l.mcq'); ?> <?php break; ?>
                                                    <?php case ('tf'): ?> <?php echo app('translator')->get('l.tf'); ?> <?php break; ?>
                                                    <?php case ('numeric'): ?> <?php echo app('translator')->get('l.numeric'); ?> <?php break; ?>
                                                    <?php default: ?> <?php echo e($question->type); ?>

                                                <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            
                                            <small class="text-muted ms-2">
                                                (<?php echo app('translator')->get($partLabel); ?>) • <?php echo e($question->score); ?> <?php echo app('translator')->get('l.points'); ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="question-body mt-3">
                                        <!-- Question Text -->
                                        <div class="mb-3">
                                            <div class="question-text tex2jax_process"><?php echo $question->question_text; ?></div>
                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($question->question_image): ?>
    <div class="mt-3">
        <img src="<?php echo e(asset($question->question_image)); ?>" alt="<?php echo app('translator')->get('l.question_image'); ?>" 
             class="img-thumbnail question-main-image" style="max-height: 200px; max-width: 100%;">
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        </div>

                                        <!-- Options based on type -->
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($question->type === 'mcq' && $question->options->count() > 0): ?>
                                            <div class="options-container mb-4">
                                                <label class="form-label fw-bold mb-3"><?php echo app('translator')->get('l.options'); ?>:</label>
                                                <div class="options-grid">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="option-item <?php echo e($option->is_correct ? 'correct-answer' : ''); ?>">
    <div class="option-letter"><?php echo e(chr(65 + $index)); ?></div>
    <div class="option-content">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="option-text tex2jax_process"><?php echo $option->option_text; ?></div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($option->is_correct): ?>
                <span class="correct-badge">
                    <i class="fas fa-check-circle me-1"></i> <?php echo app('translator')->get('l.correct_answer'); ?>
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($option->option_image): ?>
            <div class="mt-1">
                <img src="<?php echo e(asset($option->option_image)); ?>" alt="<?php echo app('translator')->get('l.option_image'); ?>" 
                     class="img-thumbnail" style="max-height: 120px; max-width: 100%;">
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                            
                                        <?php elseif($question->type === 'tf'): ?>
                                            <div class="options-container mb-4">
                                                <label class="form-label fw-bold mb-3"><?php echo app('translator')->get('l.correct_answer'); ?>:</label>
                                                <div class="d-flex gap-4">
                                                    <div class="option-item <?php echo e($question->correct_answer == 'true' ? 'correct-answer' : ''); ?>">
                                                        <div class="option-icon">✓</div>
                                                        <div class="option-text"><?php echo app('translator')->get('l.true'); ?></div>
                                                    </div>
                                                    <div class="option-item <?php echo e($question->correct_answer == 'false' ? 'correct-answer' : ''); ?>">
                                                        <div class="option-icon">✗</div>
                                                        <div class="option-text"><?php echo app('translator')->get('l.false'); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        <?php elseif($question->type === 'numeric'): ?>
                                            <div class="options-container mb-4">
                                                <label class="form-label fw-bold mb-3"><?php echo app('translator')->get('l.correct_answer'); ?>:</label>
                                                <div class="option-item correct-answer">
                                                    <div class="option-icon">#</div>
                                                    <div class="option-text"><?php echo e($question->correct_answer); ?></div>
                                                </div>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <!-- Explanation -->
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($question->explanation || $question->explanation_image): ?>
                                            <div class="explanation-section mt-4 pt-3 border-top">
                                                <h6 class="fw-bold mb-3"><?php echo app('translator')->get('l.explanation'); ?>:</h6>
                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($question->explanation): ?>
                                                    <div class="explanation-text tex2jax_process mb-3"><?php echo $question->explanation; ?></div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($question->explanation_image): ?>
    <div class="explanation-image mt-3">
        <img src="<?php echo e(asset($question->explanation_image)); ?>" alt="<?php echo app('translator')->get('l.explanation_image'); ?>" 
             class="img-thumbnail explanation-main-image" style="max-height: 200px; max-width: 100%;">
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <?php echo app('translator')->get('l.no_questions_in_this_part'); ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-5x text-muted mb-4"></i>
                    <h4 class="text-muted"><?php echo app('translator')->get('l.no_questions_yet'); ?></h4>
                    <p class="text-muted"><?php echo app('translator')->get('l.add_questions_to_preview'); ?></p>
                    <a href="<?php echo e(route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)])); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        <?php echo app('translator')->get('l.add_questions'); ?>
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden"><?php echo app('translator')->get('l.loading'); ?>...</span>
            </div>
            <p class="mt-2 mb-0"><?php echo app('translator')->get('l.loading'); ?>...</p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
$(document).ready(function() {
    // معالجة MathJax بعد تحميل الصفحة
    if (typeof MathJax !== 'undefined') {
        MathJax.typesetPromise();
    }
    
    // إظهار رسالة التحميل
    window.showLoading = function() {
        $('#loadingOverlay').fadeIn();
    };
    
    // إخفاء رسالة التحميل
    window.hideLoading = function() {
        $('#loadingOverlay').fadeOut();
    };
    
    // نسخ رابط المعاينة
    window.copyPreviewLink = function() {
        const link = window.location.href;
        navigator.clipboard.writeText(link).then(() => {
            showToast('success', '<?php echo app('translator')->get("l.link_copied_successfully"); ?>');
        }).catch(err => {
            showToast('error', '<?php echo app('translator')->get("l.failed_to_copy_link"); ?>');
        });
    };
    
    // طباعة الصفحة
    window.printTest = function() {
        window.print();
    };
    
    // دالة عرض الرسائل
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.style.cssText = 'position: fixed; bottom: 20px; right: 20px; z-index: 9999;';
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', function () {
            toast.remove();
        });
    }
    
    // تحديث MathJax بعد تغيير المحتوى
    $(document).on('contentChanged', function() {
        if (typeof MathJax !== 'undefined') {
            MathJax.typesetPromise();
        }
    });
    
    // إخفاء أزرار الطباعة عند الطباعة الفعلية
    window.addEventListener('beforeprint', function() {
        $('.no-print').hide();
    });
    
    window.addEventListener('afterprint', function() {
        $('.no-print').show();
    });
    
    // إضافة زر للعودة للأعلى
    const scrollButton = document.createElement('button');
    scrollButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollButton.className = 'btn btn-primary btn-floating';
    scrollButton.style.cssText = 'position: fixed; bottom: 80px; right: 20px; z-index: 1000; width: 50px; height: 50px; border-radius: 50%; display: none;';
    scrollButton.onclick = function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
    document.body.appendChild(scrollButton);
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollButton.style.display = 'flex';
            scrollButton.style.alignItems = 'center';
            scrollButton.style.justifyContent = 'center';
        } else {
            scrollButton.style.display = 'none';
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('themes.default.layouts.back.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/back/admins/tests/preview.blade.php ENDPATH**/ ?>