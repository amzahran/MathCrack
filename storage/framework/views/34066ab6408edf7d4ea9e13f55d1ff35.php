<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('l.break_time'); ?> - <?php echo e($test->name); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }

        .break-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .break-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 18px 55px rgba(15, 23, 42, 0.18);
            padding: 36px 32px 32px;
            text-align: center;
            max-width: 560px;
            width: 100%;
            position: relative;
        }

        .break-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 24px;
            right: 24px;
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);
        }

        .break-icon {
            width: 72px;
            height: 72px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 6px auto 18px;
            background: #ecfdf3;
            color: #16a34a;
        }

        .break-icon i {
            font-size: 30px;
        }

        .break-title {
            font-size: 1.9rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .break-subtitle {
            font-size: 0.98rem;
            color: #6b7280;
            margin-bottom: 22px;
        }
 .break-note {
    white-space: nowrap;
}

        .test-info-section {
            background: #f9fafb;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        .test-name {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 10px;
        }

        .module-progress {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .module-item {
            padding: 8px 10px;
            border-radius: 999px;
            font-size: 0.76rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #4b5563;
        }

        .module-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
        }

        .module-item.completed .module-dot {
            background: #16a34a;
        }

        .module-item.current .module-dot {
            background: #f59e0b;
        }

        .module-item.next .module-dot {
            background: #3b82f6;
        }

        .module-item.pending .module-dot {
            background: #9ca3af;
        }

        .module-label {
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.7rem;
            color: #9ca3af;
            font-weight: 600;
        }

        .module-text strong {
            font-weight: 700;
        }

        .break-timer-section {
            background: #05786eff;
            border-radius: 18px;
            padding: 22px 20px 18px;
            margin-bottom: 24px;
            color: #020815ff;
            border: 1px solid #f70818ff;
        }

        .timer-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #010711ff;
            margin-bottom: 10px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .break-timer {
            font-size: 2.6rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            font-family: "SF Mono", ui-monospace, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            margin-bottom: 4px;
        }

        .timer-description {
            font-size: 0.86rem;
            color: #020811ff;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .btn-action {
            padding: 12px 22px;
            border-radius: 999px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            min-width: 180px;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
        }

        .btn-primary-action:hover:not([disabled]) {
            transform: translateY(-1px);
            box-shadow: 0 14px 32px rgba(37, 99, 235, 0.45);
        }

        .btn-primary-action[disabled] {
            opacity: 0.5;
            cursor: default;
            box-shadow: none;
        }

        .btn-secondary-action {
            background: #ffffff;
            color: #4b5563;
            border: 1px solid #d1d5db;
        }

        .btn-secondary-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.15);
            border-color: #9ca3af;
        }

        .small-note {
            font-size: 0.8rem;
            color: #9ca3af;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .break-card {
                padding: 28px 20px 22px;
            }

            .break-title {
                font-size: 1.6rem;
            }

            .break-timer {
                font-size: 2.2rem;
            }

            .test-info-section {
                text-align: left;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $totalModules = 5;
        $currentModule = $currentModule ?? 1;
        $nextModule = $currentModule + 1;
    ?>

    <div class="break-container">
        <div class="break-card">
            <div class="break-icon">
                <i class="fas fa-mug-hot"></i>
            </div>

            <h1 class="break-title"><?php echo app('translator')->get('l.break_time'); ?></h1>
            <p class="break-subtitle"><?php echo app('translator')->get('l.break_time_description'); ?></p>

            <div class="test-info-section">
                <div class="test-name"><?php echo e($test->name); ?></div>

                <div class="module-progress">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= $totalModules; $i++): ?>
                        <?php
                            $statusClass = 'pending';
                            if ($i < $currentModule) {
                                $statusClass = 'completed';
                            } elseif ($i === $currentModule) {
                                $statusClass = 'current';
                            } elseif ($i === $nextModule) {
                                $statusClass = 'next';
                            }
                        ?>

                        <div class="module-item <?php echo e($statusClass); ?>">
                            <div class="module-dot"></div>
                            <div class="module-text">
                                <span class="module-label">Module <?php echo e($i); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < $currentModule): ?>
                                    <span><?php echo app('translator')->get('l.completed'); ?></span>
                                <?php elseif($i === $currentModule): ?>
                                    <span><strong><?php echo app('translator')->get('l.current'); ?></strong></span>
                                <?php elseif($i === $nextModule): ?>
                                    <span><?php echo app('translator')->get('l.next'); ?></span>
                                <?php else: ?>
                                    <span><?php echo app('translator')->get('l.pending'); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($test->break_time_minutes > 0): ?>
                <div class="break-timer-section">
                    <div class="timer-title"><?php echo app('translator')->get('l.break_time_remaining'); ?></div>
                    <div class="break-timer" id="break-timer">
                        <span id="timer-minutes"><?php echo e(str_pad($test->break_time_minutes, 2, '0', STR_PAD_LEFT)); ?></span>
                        :
                        <span id="timer-seconds">00</span>
                    </div>
                    <div class="timer-description">
                        <?php echo app('translator')->get('l.break_timer_description'); ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="action-buttons">
                <button
    type="button"
    id="start-next-module-btn"
    class="btn-action btn-primary-action"
    onclick="startNextModule()"
>
    <i class="fas fa-play"></i>
    Start Module <?php echo e($nextModule); ?>

</button>

                <a
                    href="<?php echo e(route('dashboard.users.tests')); ?>"
                    class="btn-action btn-secondary-action"
                    onclick="return confirmExit()"
                >
                    <i class="fas fa-sign-out-alt"></i>
                    <?php echo app('translator')->get('l.exit_test'); ?>
                </a>
            </div>

            <!-- <div class="small-note">
                <?php echo app('translator')->get('l.break_small_hint'); ?>
            </div> -->
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let breakTimeSeconds = <?php echo e($test->break_time_minutes * 60); ?>;
        let timerInterval = null;

        $(document).ready(function () {
            if (<?php echo e((int) $test->break_time_minutes); ?> > 0) {
                initializeBreakTimer();
            } else {
                enableStartButton();
            }
        });

        function initializeBreakTimer() {
            const breakEndTime = new Date('<?php echo e($studentTest->part1_ended_at ?? now()); ?>').getTime()
                + (<?php echo e($test->break_time_minutes); ?> * 60 * 1000);

            const currentTime = new Date().getTime();

            if (currentTime >= breakEndTime) {
                breakTimeSeconds = 0;
                updateTimerDisplay();
                enableStartButton();
                return;
            }

            breakTimeSeconds = Math.max(0, Math.floor((breakEndTime - currentTime) / 1000));
            updateTimerDisplay();

            if (breakTimeSeconds <= 0) {
                enableStartButton();
                return;
            }

            timerInterval = setInterval(function () {
                breakTimeSeconds--;
                updateTimerDisplay();

                if (breakTimeSeconds <= 0) {
                    clearInterval(timerInterval);
                    enableStartButton();
                    showBreakEndNotification();
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(breakTimeSeconds / 60);
            const seconds = breakTimeSeconds % 60;

            const minutesEl = document.getElementById('timer-minutes');
            const secondsEl = document.getElementById('timer-seconds');

            if (!minutesEl || !secondsEl) return;

            minutesEl.textContent = minutes.toString().padStart(2, '0');
            secondsEl.textContent = seconds.toString().padStart(2, '0');
        }

        function enableStartButton() {
            const btn = document.getElementById('start-next-module-btn');
            if (!btn) return;
            btn.disabled = false;
        }

        function showBreakEndNotification() {
            Swal.fire({
                title: '<?php echo app('translator')->get("l.break_time_over"); ?>',
                text: '<?php echo app('translator')->get("l.starting_next_module_automatically"); ?>',
                icon: 'info',
                confirmButtonColor: '#1e40af',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 2500,
                timerProgressBar: true
            }).then(() => {
                startNextModule();
            });
        }

        function startNextModule() {
            if (timerInterval) {
                clearInterval(timerInterval);
            }

            const currentModule = <?php echo e($currentModule ?? 1); ?>;
            const nextModule = currentModule + 1;

            Swal.fire({
                title: 'Loading',
                text: 'Moving to next module...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/dashboard/users/tests/<?php echo e($test->id); ?>/start-module/${nextModule}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }

                    if (response.ok) {
                        return response.json().then(data => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.href = '/dashboard/users/tests/<?php echo e($test->id); ?>/take';
                            }
                        });
                    }

                    throw new Error('Server error');
                })
                .catch(() => {
                    window.location.href = '/dashboard/users/tests/<?php echo e($test->id); ?>/take';
                });
        }

        function confirmExit() {
            return confirm('<?php echo app('translator')->get("l.exit_test_warning"); ?>');
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('themes.default.layouts.back.student-master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/back/users/tests/break.blade.php ENDPATH**/ ?>