<?php $__env->startSection('title'); ?>
<?php echo app('translator')->get('l.Dashboard'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('seo'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .dashboard-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,.125);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,.15);
        }

        .counter {
            transition: all 0.5s ease;
        }

        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stats-icon {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card welcome-card text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h2 class="text-white mb-2"><?php echo app('translator')->get('l.dashboard_welcome'); ?></h2>
                                <p class="text-white-75 mb-0"><?php echo app('translator')->get('l.dashboard_platform_stats'); ?></p>
                            </div>
                            <div class="text-end">
                                <i class="feather-trending-up fs-1 text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Statistics -->
        <div class="row">
            <!-- Levels Statistics -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-primary-subtle text-primary stats-icon">
                                    <i class="feather-layers"></i>
                                </div>
                                <div>
                                    <div class="fs-2 fw-bold text-dark">
                                        <span class="counter"><?php echo e($stats['levels']); ?></span>
                                    </div>
                                    <h3 class="fs-14 fw-semibold"><?php echo app('translator')->get('l.levels_count'); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses Statistics -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-success-subtle text-success stats-icon">
                                    <i class="feather-book-open"></i>
                                </div>
                                <div>
                                    <div class="fs-2 fw-bold text-dark">
                                        <span class="counter"><?php echo e($stats['courses']); ?></span>
                                    </div>
                                    <h3 class="fs-14 fw-semibold"><?php echo app('translator')->get('l.courses_count'); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lectures Statistics -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-warning-subtle text-warning stats-icon">
                                    <i class="feather-video"></i>
                                </div>
                                <div>
                                    <div class="fs-2 fw-bold text-dark">
                                        <span class="counter"><?php echo e($stats['lectures']); ?></span>
                                    </div>
                                    <h3 class="fs-14 fw-semibold"><?php echo app('translator')->get('l.lectures_count'); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tests Statistics -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-info-subtle text-info stats-icon">
                                    <i class="feather-file-text"></i>
                                </div>
                                <div>
                                    <div class="fs-2 fw-bold text-dark">
                                        <span class="counter"><?php echo e($stats['tests']); ?></span>
                                    </div>
                                    <h3 class="fs-14 fw-semibold"><?php echo app('translator')->get('l.tests_count'); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(function() {
            // Animate counters
            $('.counter').each(function() {
                var $this = $(this);
                var countTo = parseInt($this.text());

                $({ countNum: 0 }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'linear',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
            });

            // Add entrance animation to cards
            $('.dashboard-card').each(function(index) {
                $(this).css({
                    opacity: 0,
                    transform: 'translateY(20px)'
                });

                setTimeout(() => {
                    $(this).animate({
                        opacity: 1
                    }, 600).css('transform', 'translateY(0)');
                }, index * 200);
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('themes.default.layouts.back.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/back/index.blade.php ENDPATH**/ ?>