<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('l.tests_management'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        /* تكبير كل عناوين الحقول داخل المودال */
        .modal label.form-label,
        .modal label {
            font-size: 18px !important;
            font-weight: 600;
        }

        /* تكبير عناوين الأقسام داخل المودال */
        .modal h6.text-primary.border-bottom {
            font-size: 22px;
            font-weight: 700;
        }

         /* إخفاء عناصر apexcharts إذا كانت موجودة */
    .apexcharts-canvas,
    #salesOverviewChart,
    #revenueChart,
    #growthChart {
        display: none !important;
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

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show lectures')): ?>
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0"><?php echo app('translator')->get('l.tests_management'); ?></h4>
                    <p class="text-muted mb-0"><?php echo app('translator')->get('l.manage_all_tests'); ?></p>
                </div>
                <div>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add tests')): ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestModal">
                            <i class="fa fa-plus ti-xs me-1"></i>
                            <?php echo app('translator')->get('l.add_new_test'); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- فلاتر البحث -->
            <div class="card mb-3">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="course_filter" class="form-label"><?php echo app('translator')->get('l.Course'); ?></label>
                                <select class="form-select" id="course_filter" name="course_id">
                                    <option value=""><?php echo app('translator')->get('l.all_courses'); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($course->id); ?>"><?php echo e($course->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="status_filter" class="form-label"><?php echo app('translator')->get('l.status'); ?></label>
                                <select class="form-select" id="status_filter" name="status">
                                    <option value=""><?php echo app('translator')->get('l.all_statuses'); ?></option>
                                    <option value="1"><?php echo app('translator')->get('l.active'); ?></option>
                                    <option value="0"><?php echo app('translator')->get('l.inactive'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="price_filter" class="form-label"><?php echo app('translator')->get('l.price_type'); ?></label>
                                <select class="form-select" id="price_filter" name="price_type">
                                    <option value=""><?php echo app('translator')->get('l.all_prices'); ?></option>
                                    <option value="free"><?php echo app('translator')->get('l.free'); ?></option>
                                    <option value="paid"><?php echo app('translator')->get('l.paid'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary btn-sm" id="clearFilters">
                                    <i class="fa fa-undo me-1"></i><?php echo app('translator')->get('l.clear_filters'); ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول الاختبارات -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="testsTable" class="table table-striped table-bordered nowrap w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo app('translator')->get('l.test_name'); ?></th>
                                <th><?php echo app('translator')->get('l.Course'); ?></th>
                                <th><?php echo app('translator')->get('l.price'); ?></th>
                                <th><?php echo app('translator')->get('l.questions_status'); ?></th>
                                <th><?php echo app('translator')->get('l.students_count'); ?></th>
                                <th><?php echo app('translator')->get('l.status'); ?></th>
                                <th><?php echo app('translator')->get('l.actions'); ?></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add tests')): ?>
                <!-- مودال إضافة اختبار جديد -->
                <!-- Add Test Modal -->
<div class="modal fade" id="addTestModal" tabindex="-1" aria-labelledby="addTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTestModalLabel"><?php echo app('translator')->get('l.add_new_test'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('dashboard.admins.tests-store')); ?>" method="POST" id="addTestForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                    <div class="row">
                        ...
                        <!-- معلومات أساسية -->
                                        <div class="col-12">
                                            <h6 class="text-primary border-bottom pb-2 mb-3"><?php echo app('translator')->get('l.basic_information'); ?></h6>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label"><?php echo app('translator')->get('l.test_name'); ?> <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="course_id" class="form-label"><?php echo app('translator')->get('l.Course'); ?> <span class="text-danger">*</span></label>
                                                <select class="form-select" id="course_id" name="course_id" required>
                                                    <option value=""><?php echo app('translator')->get('l.select_course'); ?></option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($course->id); ?>"><?php echo e($course->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="description" class="form-label"><?php echo app('translator')->get('l.test_description'); ?></label>
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="price" class="form-label"><?php echo app('translator')->get('l.test_price'); ?> (EGP) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="0" required>
                                                <small class="form-text text-muted"><?php echo app('translator')->get('l.put_zero_if_free'); ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3 mt-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                                    <label class="form-check-label" for="is_active">
                                                        <?php echo app('translator')->get('l.Active'); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- نظام الدرجات -->
                                        <div class="col-12">
                                            <h6 class="text-primary border-bottom pb-2 mb-3"><?php echo app('translator')->get('l.scoring_system'); ?></h6>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="total_score" class="form-label"><?php echo app('translator')->get('l.total_score'); ?> <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="total_score" name="total_score" min="1" max="1000" value="800" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="initial_score" class="form-label"><?php echo app('translator')->get('l.initial_score'); ?> <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="initial_score" name="initial_score" min="0" max="800" value="200" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="default_question_score" class="form-label"><?php echo app('translator')->get('l.default_question_score'); ?> <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="default_question_score" name="default_question_score" min="1" max="100" value="15" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <strong><?php echo app('translator')->get('l.Note'); ?>:</strong>
                                                <?php echo app('translator')->get('l.total_score_calculation'); ?>
                                                <br>
                                                <span id="score-calculation" class="fw-bold"></span>
                                            </div>
                                        </div>

                                        <!-- هيكل الاختبار -->
                                        <div class="col-12">
                                            <h6 class="text-primary border-bottom pb-2 mb-2"><?php echo app('translator')->get('l.test_structure'); ?></h6>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="modules_count" class="form-label"><?php echo app('translator')->get('l.modules_count'); ?></label>
                                            <select class="form-select" id="modules_count" name="modules_count">
                                                <option value="1">1</option>
                                                <option value="2" selected>2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                            <small class="form-text text-muted">
                                                <?php echo app('translator')->get('l.modules_help_text', [], null); ?> ?? 'Select number of modules (1–5). Hidden modules will be treated as 0 questions.'
                                            </small>
                                        </div>

                                        <div class="col-12"></div>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?>
                                            <div class="col-md-6 module-block" data-module="<?php echo e($i); ?>">
                                                <div class="mb-3">
                                                    <label for="part<?php echo e($i); ?>_questions_count" class="form-label">
                                                        Module <?php echo e($i); ?> Questions Count
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i === 1): ?> <span class="text-danger">*</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </label>
                                                    <input type="number"
                                                           class="form-control"
                                                           id="part<?php echo e($i); ?>_questions_count"
                                                           name="part<?php echo e($i); ?>_questions_count"
                                                           min="<?php echo e($i === 1 ? 1 : 0); ?>"
                                                           max="100"
                                                           value="<?php echo e($i <= 2 ? 22 : 0); ?>"
                                                           <?php if($i === 1): ?> required <?php endif; ?>>
                                                </div>
                                            </div>

                                            <div class="col-md-6 module-block" data-module="<?php echo e($i); ?>">
                                                <div class="mb-3">
                                                    <label for="part<?php echo e($i); ?>_time_minutes" class="form-label">
                                                        Module <?php echo e($i); ?> Time (Minutes)
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i === 1): ?> <span class="text-danger">*</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </label>
                                                    <input type="number"
                                                           class="form-control"
                                                           id="part<?php echo e($i); ?>_time_minutes"
                                                           name="part<?php echo e($i); ?>_time_minutes"
                                                           min="<?php echo e($i === 1 ? 1 : 0); ?>"
                                                           max="300"
                                                           value="<?php echo e($i <= 2 ? 35 : 0); ?>"
                                                           <?php if($i === 1): ?> required <?php endif; ?>>
                                                </div>
                                            </div>
                                        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="break_time_minutes" class="form-label"><?php echo app('translator')->get('l.break_time_minutes'); ?></label>
                                                <input type="number" class="form-control" id="break_time_minutes" name="break_time_minutes"
                                                       min="0" max="60" value="15">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_attempts" class="form-label"><?php echo app('translator')->get('l.max_attempts'); ?> <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="max_attempts" name="max_attempts"
                                                       min="1" max="10" value="1" required>
                                                <small class="form-text text-muted"><?php echo app('translator')->get('l.max_attempts_help'); ?></small>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                <strong><?php echo app('translator')->get('l.Note'); ?>:</strong> <?php echo app('translator')->get('l.test_timing_info'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app('translator')->get('l.Cancel'); ?></button>
                                    <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('l.save'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
$(document).ready(function() {
    console.log('Tests Management - Loading...');

    // إعداد DataTable
    var testsTable = $('#testsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?php echo e(route('dashboard.admins.tests')); ?>",
            data: function (d) {
                d.course_id  = $('#course_filter').val();
                d.status     = $('#status_filter').val();
                d.price_type = $('#price_filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name',        name: 'name' },
            { data: 'course_name', name: 'course.name', defaultContent: '' },
            { data: 'price_formatted',   name: 'price', orderable: false, searchable: false },
            { data: 'questions_status',  name: 'questions_status', orderable: false, searchable: false },
            { data: 'students_count',    name: 'students_count', searchable: false },
            { data: 'status',            name: 'status', orderable: false, searchable: false },
            { data: 'action',            name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
        language: {
            url: "<?php echo e(app()->getLocale() == 'ar' ? asset('back-assets/js/datatable-ar.json') : ''); ?>"
        }
    });

    // تطبيق الفلاتر
    $('#course_filter, #status_filter, #price_filter').on('change', function() {
        testsTable.draw();
    });

    // مسح الفلاتر
    $('#clearFilters').click(function() {
        $('#filterForm')[0].reset();
        testsTable.draw();
    });

    // تبديل الحالة
    $(document).on('change', '.status-toggle', function() {
        var checkbox = $(this);
        var testId   = checkbox.data('id');
        var isActive = checkbox.is(':checked');

        $.ajax({
            url: "<?php echo e(route('dashboard.admins.tests-toggle-status')); ?>",
            method: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                id: testId
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                    checkbox.prop('checked', !isActive);
                }
            },
            error: function() {
                toastr.error('<?php echo app('translator')->get("l.error_occurred"); ?>');
                checkbox.prop('checked', !isActive);
            }
        });
    });

    // ===== حساب الدرجات التلقائي =====
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

        const totalQuestions  = part1Count + part2Count + part3Count + part4Count + part5Count;
        const questionsTotal  = totalQuestions * questionScore;
        const calculatedTotal = initialScore + questionsTotal;

        $('#total_score').val(calculatedTotal);

        let message = `Initial Score: ${initialScore} + (${totalQuestions} Questions × ${questionScore}) = ${calculatedTotal}`;
        $('#score-calculation')
            .removeClass('text-danger')
            .addClass('text-success')
            .html(message);
    }

    $('#initial_score, #default_question_score,' +
      '#part1_questions_count, #part2_questions_count, #part3_questions_count, #part4_questions_count, #part5_questions_count'
    ).on('input', updateScoreCalculation);

    updateScoreCalculation();

    // ===== إظهار / إخفاء الموديولات =====
    function toggleModules() {
        var count = parseInt($('#modules_count').val()) || 1;

        $('.module-block').each(function () {
            var mod = parseInt($(this).data('module'));
            if (mod <= count) {
                $(this).show();
            } else {
                $(this).hide();
                $(this).find('input[type="number"]').val(0);
            }
        });

        if (count <= 1) {
            $('#break_time_minutes').val(0);
            $('#break_time_minutes').closest('.col-md-6').hide();
        } else {
            $('#break_time_minutes').closest('.col-md-6').show();
            if (!$('#break_time_minutes').val()) {
                $('#break_time_minutes').val(15);
            }
        }
    }

    $('#modules_count').on('change', toggleModules);
    toggleModules();

    $('#addTestForm').on('submit', function () {
        var count = parseInt($('#modules_count').val()) || 1;
        for (var i = count + 1; i <= 5; i++) {
            $('#part' + i + '_questions_count').val(0);
            $('#part' + i + '_time_minutes').val(0);
        }
    });

    // حذف السجل
    $(document).on('click', '.delete-record', function() {
        var href     = $(this).attr('href');
        var testName = $(this).closest('tr').find('td:eq(1)').text();
        if (confirm('<?php echo app('translator')->get("l.are_you_sure_delete_test"); ?> "' + testName + '"?')) {
            window.location.href = href;
        }
        return false;
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('themes.default.layouts.back.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/back/admins/tests/index.blade.php ENDPATH**/ ?>