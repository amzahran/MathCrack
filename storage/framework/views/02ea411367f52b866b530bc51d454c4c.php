<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('l.edit'); ?> - <?php echo e($test->name); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .main-content label.form-label,
        .main-content label {
            font-size: 18px !important;
            font-weight: 600;
        }

        .main-content h6.text-primary.border-bottom {
            font-size: 22px;
            font-weight: 700;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($error); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit lectures')): ?>
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0"><?php echo app('translator')->get('l.edit'); ?> - <span class="text-primary"><?php echo e($test->name); ?></span></h4>
                    <p class="text-muted mb-0"><?php echo e($test->course->name ?? ''); ?></p>
                </div>
                <div>
                    <a href="<?php echo e(route('dashboard.admins.tests-show', ['id' => encrypt($test->id)])); ?>"
                       class="btn btn-info waves-effect waves-light mb-2">
                        <i class="fa fa-eye ti-xs me-1"></i>
                        <?php echo app('translator')->get('l.View'); ?>
                    </a>
                    <a href="<?php echo e(route('dashboard.admins.tests')); ?>"
                       class="btn btn-secondary waves-effect waves-light mb-2">
                        <i class="fa fa-arrow-left ti-xs me-1"></i>
                        <?php echo app('translator')->get('l.back_to_list'); ?>
                    </a>
                </div>
            </div>

            <?php
                $hasStudents = $test->studentTests()->exists();

                // override مبني على نفس الصلاحية التي فتحت الصفحة
                $adminOverride = Gate::check('edit lectures');

                // لو عندك طلاب + مش معاك override => اقفل
                $locked = $hasStudents && !$adminOverride;

                // استنتاج عدد الموديولات من عدد الأسئلة
                $initialModulesCount = 1;
                for ($i = 5; $i >= 1; $i--) {
                    $field = "part{$i}_questions_count";
                    if (!empty($test->$field) && $test->$field > 0) {
                        $initialModulesCount = $i;
                        break;
                    }
                }
            ?>

            <div class="card" style="padding: 15px;">
                <div class="card-body">
                    <form action="<?php echo e(route('dashboard.admins.tests-update')); ?>" method="POST" id="editTestForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="id" value="<?php echo e(encrypt($test->id)); ?>">

                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3"><?php echo app('translator')->get('l.basic_information'); ?></h6>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><?php echo app('translator')->get('l.test_name'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="<?php echo e(old('name', $test->name)); ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label"><?php echo app('translator')->get('l.Course'); ?> <span class="text-danger">*</span></label>
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value=""><?php echo app('translator')->get('l.select_course'); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($course->id); ?>" <?php echo e(old('course_id', $test->course_id) == $course->id ? 'selected' : ''); ?>>
                                                <?php echo e($course->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label"><?php echo app('translator')->get('l.test_description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo e(old('description', $test->description)); ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label"><?php echo app('translator')->get('l.test_price'); ?> (EGP) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" min="0" step="0.01"
                                           value="<?php echo e(old('price', $test->price)); ?>" required>
                                    <small class="form-text text-muted"><?php echo app('translator')->get('l.put_zero_if_free'); ?></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 mt-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                            <?php echo e(old('is_active', $test->is_active) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="is_active">
                                            <?php echo app('translator')->get('l.Active'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3"><?php echo app('translator')->get('l.scoring_system'); ?></h6>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasStudents && !$adminOverride): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong><?php echo app('translator')->get('l.warning'); ?>:</strong> <?php echo app('translator')->get('l.cannot_edit_structure_students_taken'); ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_score" class="form-label"><?php echo app('translator')->get('l.total_score'); ?> <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="total_score" name="total_score" min="1" max="1000"
                                           value="<?php echo e(old('total_score', $test->total_score)); ?>"
                                           <?php echo e($locked ? 'readonly' : ''); ?> required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="initial_score" class="form-label"><?php echo app('translator')->get('l.initial_score'); ?> <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="initial_score" name="initial_score" min="0" max="800"
                                           value="<?php echo e(old('initial_score', $test->initial_score)); ?>" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="default_question_score" class="form-label"><?php echo app('translator')->get('l.default_question_score'); ?> <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="default_question_score" name="default_question_score" min="1" max="100"
                                           value="<?php echo e(old('default_question_score', $test->default_question_score)); ?>"
                                           <?php echo e($locked ? 'readonly' : ''); ?> required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong><?php echo app('translator')->get('l.Note'); ?>:</strong> <?php echo app('translator')->get('l.total_score_calculation'); ?>
                                    <br>
                                    <span id="score-calculation" class="fw-bold"></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3"><?php echo app('translator')->get('l.test_structure'); ?></h6>
                            </div>

                            <div class="col-md-4">
                                <label for="modules_count" class="form-label"><?php echo app('translator')->get('l.modules_count'); ?></label>
                                <select class="form-select" id="modules_count">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i=1;$i<=5;$i++): ?>
                                        <option value="<?php echo e($i); ?>" <?php echo e($initialModulesCount == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <small class="form-text text-muted">
                                    <?php echo app('translator')->get('l.modules_help_text', [], null); ?> ?? 'Select number of modules (1–5). Hidden modules will be treated as 0 questions.'
                                </small>
                            </div>

                            <div class="col-12"></div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?>
                                <?php
                                    $questionsField = "part{$i}_questions_count";
                                    $timeField      = "part{$i}_time_minutes";
                                    $isRequired     = $i === 1;
                                ?>

                                <div class="col-md-6 module-block" data-module="<?php echo e($i); ?>">
                                    <div class="mb-3">
                                        <label for="<?php echo e($questionsField); ?>" class="form-label">
                                            Module <?php echo e($i); ?> Questions Count
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isRequired): ?><span class="text-danger">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="<?php echo e($questionsField); ?>"
                                            name="<?php echo e($questionsField); ?>"
                                            min="<?php echo e($isRequired ? 1 : 0); ?>"
                                            max="100"
                                            value="<?php echo e(old($questionsField, $test->$questionsField ?? 0)); ?>"
                                            <?php echo e($locked ? 'readonly' : ''); ?>

                                            <?php echo e($isRequired ? 'required' : ''); ?>

                                        >
                                    </div>
                                </div>

                                <div class="col-md-6 module-block" data-module="<?php echo e($i); ?>">
                                    <div class="mb-3">
                                        <label for="<?php echo e($timeField); ?>" class="form-label">
                                            Module <?php echo e($i); ?> Time (Minutes)
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isRequired): ?><span class="text-danger">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="<?php echo e($timeField); ?>"
                                            name="<?php echo e($timeField); ?>"
                                            min="<?php echo e($isRequired ? 1 : 0); ?>"
                                            max="300"
                                            value="<?php echo e(old($timeField, $test->$timeField ?? 0)); ?>"
                                            <?php echo e($locked ? 'readonly' : ''); ?>

                                            <?php echo e($isRequired ? 'required' : ''); ?>

                                        >
                                    </div>
                                </div>
                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="break_time_minutes" class="form-label"><?php echo app('translator')->get('l.break_time_minutes'); ?></label>
                                    <input type="number" class="form-control" id="break_time_minutes" name="break_time_minutes" min="0" max="60"
                                           value="<?php echo e(old('break_time_minutes', $test->break_time_minutes)); ?>"
                                           <?php echo e($locked ? 'readonly' : ''); ?>>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_attempts" class="form-label"><?php echo app('translator')->get('l.max_attempts'); ?> <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="max_attempts" name="max_attempts" min="1" max="10"
                                           value="<?php echo e(old('max_attempts', $test->max_attempts ?? 1)); ?>"
                                           <?php echo e($locked ? 'readonly' : ''); ?> required>
                                    <small class="form-text text-muted"><?php echo app('translator')->get('l.max_attempts_help'); ?></small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong><?php echo app('translator')->get('l.Note'); ?>:</strong> <?php echo app('translator')->get('l.test_timing_info'); ?>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-end d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back()"><?php echo app('translator')->get('l.Cancel'); ?></button>
                                    <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('l.Update'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(function() {
            const locked = <?php echo e($locked ? 'true' : 'false'); ?>;

            function getInt(selector) {
                return parseInt($(selector).val()) || 0;
            }

            function updateScoreCalculation() {
                const initialScore  = getInt('#initial_score');
                const questionScore = getInt('#default_question_score');

                const part1Count = getInt('#part1_questions_count');
                const part2Count = getInt('#part2_questions_count');
                const part3Count = getInt('#part3_questions_count');
                const part4Count = getInt('#part4_questions_count');
                const part5Count = getInt('#part5_questions_count');

                const totalQuestions   = part1Count + part2Count + part3Count + part4Count + part5Count;
                const questionsTotal   = totalQuestions * questionScore;
                const calculatedTotal  = initialScore + questionsTotal;

                if (!locked) {
                    $('#total_score').val(calculatedTotal);
                    let message = `Initial Score: ${initialScore} + (${totalQuestions} Questions × ${questionScore}) = ${calculatedTotal}`;
                    $('#score-calculation')
                        .removeClass('text-danger')
                        .addClass('text-success')
                        .html(message);
                } else {
                    const totalScore = getInt('#total_score');
                    let message = `Initial Score: ${initialScore} + (${totalQuestions} Questions × ${questionScore}) = ${calculatedTotal}`;

                    if (calculatedTotal !== totalScore) {
                        message += ` ⚠️ should_be: ${totalScore}`;
                        $('#score-calculation')
                            .addClass('text-danger')
                            .removeClass('text-success');
                    } else {
                        message += ` ✅`;
                        $('#score-calculation')
                            .removeClass('text-danger')
                            .addClass('text-success');
                    }

                    $('#score-calculation').html(message);
                }
            }

            $('#initial_score, #default_question_score, ' +
              '#part1_questions_count, #part2_questions_count, #part3_questions_count, #part4_questions_count, #part5_questions_count'
            ).on('input', updateScoreCalculation);

            updateScoreCalculation();

            function toggleModulesEdit() {
                let count = parseInt($('#modules_count').val()) || 1;

                $('.module-block').each(function () {
                    const mod = parseInt($(this).data('module'));
                    if (mod <= count) {
                        $(this).show();
                    } else {
                        $(this).hide();
                        const input = $(this).find('input[type="number"]');
                        if (!locked) {
                            input.val(0);
                        }
                    }
                });

                if (count <= 1) {
                    if (!locked) {
                        $('#break_time_minutes').val(0);
                    }
                    $('#break_time_minutes').closest('.col-md-6').hide();
                } else {
                    $('#break_time_minutes').closest('.col-md-6').show();
                }
            }

            $('#modules_count').on('change', toggleModulesEdit);
            toggleModulesEdit();

            $('#editTestForm').on('submit', function () {
                if (!locked) {
                    let count = parseInt($('#modules_count').val()) || 1;
                    for (let i = count + 1; i <= 5; i++) {
                        $('#part' + i + '_questions_count').val(0);
                        $('#part' + i + '_time_minutes').val(0);
                    }
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('themes.default.layouts.back.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/back/admins/tests/edit.blade.php ENDPATH**/ ?>