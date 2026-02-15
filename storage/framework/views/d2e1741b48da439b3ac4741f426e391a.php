<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="<?php echo e(route('index')); ?>" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="<?php echo e(asset($settings['logo'])); ?>" alt="" class="logo logo-lg w-100" />
                <img src="<?php echo e(asset($settings['favicon'])); ?>" alt="" class="logo logo-sm w-100" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                
                <li class="nxl-item <?php echo e(request()->is('home') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('home')); ?>" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext"><?php echo app('translator')->get('l.Dashboard'); ?></span>
                    </a>
                </li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->roles()->exists()): ?> <!-- users links -->
                    <?php echo $__env->make('themes.default.layouts.back.headerUsers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php else: ?>
                    <li class="nxl-item nxl-caption">
                        <label>-----</label>
                    </li>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show lectures')): ?>
                        <li class="nxl-item <?php echo e(request()->is('lectures') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.lectures')); ?>" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-video"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Lectures'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show lives')): ?>
                        <li class="nxl-item <?php echo e(request()->is('lives') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.lives')); ?>" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-tv"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Live'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show tests')): ?>
                        <li class="nxl-item <?php echo e(request()->is('tests') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.tests')); ?>" class="nxl-link">
                                <span class="nxl-micon">
                                    <i class="feather-activity"></i>
                                </span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Tests'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nxl-item nxl-caption">
                        <label>-----</label>
                    </li>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show courses')): ?>
                        <li class="nxl-item <?php echo e(request()->is('courses') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.courses')); ?>" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-book-open"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Courses'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show levels')): ?>
                        <li class="nxl-item <?php echo e(request()->is('levels') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.levels')); ?>" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-layers"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Levels'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nxl-item nxl-caption">
                        <label>-----</label>
                    </li>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show contact_us')): ?>
                        <li class="nxl-item <?php echo e(request()->is('contact-us') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.contacts')); ?>" class="nxl-link position-relative">
                                <span class="nxl-micon"><i class="feather-message-circle"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Contact Us'); ?></span>
                                <?php $newRequests = \App\Models\Contact::where('status', 0)->count(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newRequests > 0): ?>
                                    <span
                                        class="notification-badge"
                                        style="
                                            position: absolute;
                                            top: 8px;
                                            right: 18px;
                                            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
                                            color: #fff;
                                            border-radius: 50%;
                                            min-width: 24px;
                                            height: 24px;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            font-size: 0.95em;
                                            font-weight: bold;
                                            box-shadow: 0 2px 8px rgba(255,75,43,0.15);
                                            border: 2px solid #fff;
                                            z-index: 2;
                                            animation: pulse-badge 1.2s infinite;
                                        ">
                                        <?php echo e($newRequests); ?>

                                    </span>
                                    <style>
                                        @keyframes pulse-badge {
                                            0% { box-shadow: 0 0 0 0 rgba(255,75,43,0.5);}
                                            70% { box-shadow: 0 0 0 10px rgba(255,75,43,0);}
                                            100% { box-shadow: 0 0 0 0 rgba(255,75,43,0);}
                                        }
                                    </style>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show students')): ?>
                        <li class="nxl-item <?php echo e(request()->is('customers') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.customers')); ?>" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-users"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Customers'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show invoices')): ?>
                        <li class="nxl-item <?php echo e(request()->is('invoices') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.invoices')); ?>" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-file-text"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Invoices'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show users')): ?>
                        <li class="nxl-item <?php echo e(request()->is('users') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.users')); ?>" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-users"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Users'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show roles')): ?>
                        <li class="nxl-item" <?php echo e(request()->is('roles') ? 'active' : ''); ?>>
                            <a href="<?php echo e(route('dashboard.admins.roles')); ?>" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-lock"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Roles'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nxl-item nxl-caption">
                        <label>-----</label>
                    </li>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show settings')): ?>
                        <li class="nxl-item <?php echo e(request()->is('settings') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.settings')); ?>" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-settings"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Settings'); ?></span>
                            </a>
                        </li>
                        <li class="nxl-item <?php echo e(request()->is('payments') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard.admins.payments')); ?>" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                                <span class="nxl-mtext"><?php echo app('translator')->get('l.Payment Gateways'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <li class="nxl-item nxl-caption">
                    <label>-----</label>
                </li>
                <li class="nxl-item <?php echo e(Route::is('dashboard.profile') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('dashboard.profile')); ?>" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-user"></i></span>
                        <span class="nxl-mtext"><?php echo app('translator')->get('l.Account'); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/layouts/back/header.blade.php ENDPATH**/ ?>