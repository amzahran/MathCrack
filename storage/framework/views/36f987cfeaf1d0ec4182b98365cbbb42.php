<script>
    window.primaryColor = "<?php echo e($settings['primary_color'] ?? '#FFAB1D'); ?>";
</script>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">


<?php echo $__env->yieldContent('page-css'); ?>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

        <title><?php echo $__env->yieldContent('title'); ?> | <?php echo e($settings['name']); ?></title>

        <meta name="description" content="<?php echo $__env->yieldContent('description'); ?>" />

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset($settings['favicon'])); ?>" />
        <!--! BEGIN: Bootstrap CSS-->
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/themes/default/css/bootstrap.min.css')); ?>">
        <!--! END: Bootstrap CSS-->
        <!--! BEGIN: Vendors CSS-->
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/themes/default/vendors/css/vendors.min.css')); ?>" />
        <script src="https://kit.fontawesome.com/3bcd125e2e.js" crossorigin="anonymous"></script>
        <!--! END: Vendors CSS-->
        <!--! BEGIN: Custom CSS-->
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/themes/default/css/theme.min.css')); ?>">
        <!-- google recaptcha -->
        <script async src="https://www.google.com/recaptcha/api.js?hl=<?php echo e(app()->getLocale()); ?>"></script>
        <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    
    </head>

    <?php echo $__env->yieldContent('page-scripts'); ?>
    <style>
body{ background: #111dcf  !important; }
</style>


    <body>

        <?php echo $__env->yieldContent('content'); ?>

        <!-- vendors.min.js {always must need to be top} -->
        <script src="<?php echo e(asset('assets/themes/default/vendors/js/vendors.min.js')); ?>"></script>
        <!--! BEGIN: Apps Init  !-->
        <script src="<?php echo e(asset('assets/themes/default/js/common-init.min.js')); ?>"></script>
        <!-- Page JS -->
        <?php echo $__env->yieldContent('page-scripts'); ?>
    </body>

</html>
<?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/auth/layout.blade.php ENDPATH**/ ?>