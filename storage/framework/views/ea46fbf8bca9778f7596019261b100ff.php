<?php $__env->startComponent('mail::message'); ?>
# <?php echo app('translator')->get('Hello!'); ?>

<?php echo app('translator')->get('Your :app account logged in from a new device.', ['app' => config('app.name')]); ?>

> **<?php echo app('translator')->get('Account:'); ?>** <?php echo new \Illuminate\Support\EncodedHtmlString($account->email); ?><br/>
> **<?php echo app('translator')->get('Time:'); ?>** <?php echo new \Illuminate\Support\EncodedHtmlString($time->toCookieString()); ?><br/>
> **<?php echo app('translator')->get('IP Address:'); ?>** <?php echo new \Illuminate\Support\EncodedHtmlString($ipAddress); ?><br/>
> **<?php echo app('translator')->get('Browser:'); ?>** <?php echo new \Illuminate\Support\EncodedHtmlString($browser); ?><br/>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($location && $location['default'] === false): ?>
> **<?php echo app('translator')->get('Location:'); ?>** <?php echo new \Illuminate\Support\EncodedHtmlString($location['city'] ?? __('Unknown City')); ?>, <?php echo new \Illuminate\Support\EncodedHtmlString($location['state'] ?? __('Unknown State')); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php echo app('translator')->get('If this was you, you can ignore this alert. If you suspect any suspicious activity on your account, please change your password.'); ?>

<?php echo app('translator')->get('Regards,'); ?><br/>
<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/vendor/authentication-log/emails/new.blade.php ENDPATH**/ ?>