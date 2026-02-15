<?php $__env->startSection('title'); ?>
    403 | <?php echo app('translator')->get('l.You are not authorized!'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <div class="container-xxl container-p-y mt-5 text-center">
        <div class="misc-wrapper">
            <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem">403</h1>
            <h4 class="mb-2 mx-2"><?php echo app('translator')->get('l.You are not authorized!'); ?> 🔐</h4>
            <p class="mb-6 mx-2"><?php echo app('translator')->get('l.You do not have permission to view this page using the credentials that you have provided while login.'); ?></p>
            <p class="mb-6 mx-2"><?php echo app('translator')->get('l.Please contact your site administrator.'); ?></p>
            <a href="<?php echo e(route('home')); ?>" class="btn btn-primary"><?php echo app('translator')->get('l.Back to home'); ?></a>
            <div class="mt-6">
                <img src="<?php echo e(asset('assets/themes/default/img/illustrations/girl-with-laptop-light.png')); ?>"
                    alt="page-misc-not-authorized-light" width="500" class="img-fluid"
                    data-app-light-img="illustrations/girl-with-laptop-light.png"
                    data-app-dark-img="illustrations/girl-with-laptop-dark.png">
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('themes.default.layouts.back.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/back/permission-denied.blade.php ENDPATH**/ ?>