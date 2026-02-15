


<li class="nxl-item nxl-caption">
    <label>-----</label>
</li>
<li class="nxl-item <?php echo e(request()->is('dashboard.users.courses') ? 'active' : ''); ?>">
    <a href="<?php echo e(route('dashboard.users.courses')); ?>" class="nxl-link">
        <span class="nxl-micon"><i class="feather-book"></i></span>
        <span class="nxl-mtext"><?php echo app('translator')->get('l.Courses'); ?></span>
    </a>
</li>
<li class="nxl-item <?php echo e(request()->is('dashboard.users.invoices') ? 'active' : ''); ?>">
    <a href="<?php echo e(route('dashboard.users.invoices')); ?>" class="nxl-link">
        <span class="nxl-micon"><i class="feather-file-text"></i></span>
        <span class="nxl-mtext"><?php echo app('translator')->get('l.Invoices'); ?></span>
    </a>
</li><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/layouts/back/headerUsers.blade.php ENDPATH**/ ?>