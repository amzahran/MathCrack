<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('l.my_courses'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .course-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            cursor: pointer;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .course-image {
            height: 200px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            position: relative;
            overflow: hidden;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .course-card:hover .course-image img {
            transform: scale(1.05);
        }

        .course-image .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(30, 64, 175, 0.8), rgba(59, 130, 246, 0.8));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .course-card:hover .course-image .overlay {
            opacity: 1;
        }

        .course-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .course-meta {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .course-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-number {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e40af;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #95a5a6;
            margin-top: 5px;
        }

        .no-courses {
            text-align: center;
            padding: 50px 20px;
            color: #7f8c8d;
        }

        .no-courses i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #bdc3c7;
        }

        .page-headers {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            border-radius: 15px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 300;
            color: white !important;
        }

        .page-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            color: white !important;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-headers">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1><?php echo app('translator')->get('l.my_courses'); ?></h1>
                        <p><?php echo app('translator')->get('l.explore_your_courses_description'); ?></p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="stats-summary">
                            <span class="badge bg-light text-dark fs-6 px-3 py-2">
                                <?php echo e($courses->count()); ?> <?php echo app('translator')->get('l.courses_available'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($courses->count() > 0): ?>
            <div class="row">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card course-card" onclick="viewCourse('<?php echo e(encrypt($course->id)); ?>')">
                            <div class="course-image">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->image): ?>
                                    <img src="<?php echo e(asset($course->image)); ?>" alt="<?php echo e($course->name); ?>">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-graduation-cap fa-4x text-white opacity-50"></i>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="overlay">
                                    <i class="fas fa-eye fa-2x text-white"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="course-title"><?php echo e($course->name); ?></h5>
                                <div class="course-meta">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-layer-group me-2 text-primary"></i>
                                        <span><?php echo e($course->level->name ?? '-'); ?></span>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->price): ?>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-tag me-2 text-success"></i>
                                            <span><?php echo e($course->price); ?> <?php echo app('translator')->get('l.currency'); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-gift me-2 text-success"></i>
                                            <span class="text-success"><?php echo app('translator')->get('l.Free'); ?></span>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="course-stats">
                                    <div class="stat-item">
                                        <span class="stat-number"><?php echo e($course->lectures_count); ?></span>
                                        <div class="stat-label"><?php echo app('translator')->get('l.lectures'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number"><?php echo e($course->lectures->sum(function($lecture) { return $lecture->assignments->count(); })); ?></span>
                                        <div class="stat-label"><?php echo app('translator')->get('l.assignments'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">
                                            <i class="fas fa-calendar-alt text-muted"></i>
                                        </span>
                                        <div class="stat-label"><?php echo e($course->updated_at->diffForHumans()); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php else: ?>
            <div class="no-courses">
                <i class="fas fa-graduation-cap"></i>
                <h3><?php echo app('translator')->get('l.no_courses_available'); ?></h3>
                <p><?php echo app('translator')->get('l.no_courses_description'); ?></p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        function viewCourse(courseId) {
            window.location.href = "<?php echo e(route('dashboard.users.courses-lectures')); ?>?id=" + courseId;
        }

        // Add some animation on page load
        $(document).ready(function() {
            $('.course-card').each(function(index) {
                $(this).css('opacity', '0').delay(index * 100).animate({
                    opacity: 1
                }, 500);
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('themes.default.layouts.back.student-master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/back/users/courses/courses-list.blade.php ENDPATH**/ ?>