

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
      class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
      dir="<?php echo e(in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr'); ?>"
      data-theme="theme-default"
      data-assets-path="<?php echo e(asset('assets/themes/default')); ?>/"
      data-template="vertical-menu-template"
      data-style="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $__env->yieldContent('title'); ?></title>

    <?php
        $settings = app('view')->shared('settings') ?? [];
        $favicon = $settings['favicon'] ?? null;
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($favicon): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset($favicon)); ?>" />
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/themes/default/vendor/fonts/iconify-icons.css')); ?>" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/themes/default/vendor/libs/pickr/pickr-themes.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/themes/default/vendor/css/core.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/themes/default/css/demo.css')); ?>" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/themes/default/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/themes/default/vendor/css/pages/page-misc.css')); ?>" />

    <script src="<?php echo e(asset('assets/themes/default/vendor/js/helpers.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/themes/default/vendor/js/template-customizer.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/themes/default/js/config.js')); ?>"></script>
</head>

<body>
<div class="container-xxl container-p-y">
    <div class="misc-wrapper">
        <h1 class="mb-2 mx-2" style="line-height: 6rem;font-size: 6rem;"><?php echo $__env->yieldContent('code'); ?></h1>
        <h4 class="mb-2 mx-2"><?php echo $__env->yieldContent('title'); ?></h4>
        <p class="mb-6 mx-2"><?php echo $__env->yieldContent('message'); ?></p>

        <a href="<?php echo e(LaravelLocalization::getLocalizedURL(app()->getLocale(), '/')); ?>

" class="btn btn-primary"><?php echo e(__('l.Back to Home')); ?></a>

        <div class="mt-6">
            <img
                src="<?php echo e(asset('assets/themes/default/img/illustrations/page-misc-error-light.png')); ?>"
                alt="error"
                width="500"
                class="img-fluid"
                data-app-light-img="illustrations/page-misc-error-light.png"
                data-app-dark-img="illustrations/page-misc-error-dark.png"
            />
        </div>
    </div>
</div>

<script src="<?php echo e(asset('assets/themes/default/vendor/libs/jquery/jquery.js')); ?>"></script>
<script src="<?php echo e(asset('assets/themes/default/vendor/libs/popper/popper.js')); ?>"></script>
<script src="<?php echo e(asset('assets/themes/default/vendor/js/bootstrap.js')); ?>"></script>
<script src="<?php echo e(asset('assets/themes/default/vendor/libs/@algolia/autocomplete-js.js')); ?>"></script>
<script src="<?php echo e(asset('assets/themes/default/vendor/libs/pickr/pickr.js')); ?>"></script>
<script src="<?php echo e(asset('assets/themes/default/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')); ?>"></script>
<script src="<?php echo e(asset('assets/themes/default/vendor/libs/hammer/hammer.js')); ?>"></script>
<script src="<?php echo e(asset('assets/themes/default/vendor/libs/i18n/i18n.js')); ?>"></script>
<script src="<?php echo e(asset('assets/themes/default/vendor/js/menu.js')); ?>"></script>
<script src="<?php echo e(asset('assets/themes/default/js/main.js')); ?>"></script>
</body>
</html>
<?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/errors/layout.blade.php ENDPATH**/ ?>