<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('l.Register'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-css'); ?>
<style>
    /* ===== إزالة الخلفية الحمراء فقط ===== */
    .auth-minimal-wrapper {
        background: #f4f6fb !important;
    }

    /* تأكد من عدم وجود خلفيات حمراء في العناصر الأخرى */
    body, 
    main, 
    .auth-minimal-inner,
    .minimal-card-wrapper,
    .card,
    .card-body {
        background: transparent !important;
    }

    /* خاص للبطاقة فقط */
    .card {
        background: #ffffff !important;
    }

    /* ===== الشعار من الصورة الأصلية ===== */
    .mathcrack-original-header {
        text-align: center;
        margin: 0 auto 20px auto;
        padding-top: 20px;
    }

    .mathcrack-logo-img {
        height: 60px;
        width: auto;
        margin-bottom: 10px;
        object-fit: contain;
    }

    .mathcrack-original-title {
        font-size: 28px;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 4px 0;
        letter-spacing: -0.5px;
    }

    .prof-name-original {
        font-size: 14px;
        color: #64748b;
        margin: 0;
        font-weight: 500;
    }

    /* ===== تعديل المسافات ===== */
    .card-body {
        padding-top: 5px !important;
    }

    .fs-20 {
        margin-top: 0 !important;
        margin-bottom: 25px !important;
        font-size: 22px !important;
        color: #1e293b;
    }

    /* إخفاء الشعار الدائري القديم */
    .wd-50 {
        display: none !important;
    }

    /* ===== CSS الأصلي للصفحة ===== */
    .error-message { color: red; }

    .iti { width: 100%; }
    .iti__country { direction: ltr; }
    .iti__country-list { left: 0; }
    #phone { text-align: left; }
    .iti__selected-flag { direction: ltr; }

    .form-label{
        font-weight: 600;
        margin-bottom: 6px;
        display: inline-block;
    }

    /* password input-group */
    .password-group{
        direction: ltr;
    }

    .password-group .form-control{
        border-right: 0;
        padding-right: 14px;
    }

    .password-group .input-group-text{
        background: transparent;
        cursor: pointer;
        user-select: none;
        border-left: 0;
        min-width: 46px;
        justify-content: center;
    }

    .password-group .input-group-text i{
        font-size: 18px;
        color: #6c757d;
        line-height: 1;
    }

    .pass-help{
        font-size: 13px;
        color: #6c757d;
        margin-top: 6px;
    }

    .pass-error{
        display: none;
        font-size: 13px;
        color: #dc3545;
        margin-top: 6px;
    }

    /* تحسينات للحقول */
    .form-control {
        border: 1px solid #e5e7eb;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* تعديل تخطيط الاسم الأول والأخير */
    .name-row {
        display: flex;
        gap: 15px;
        margin-bottom: 1rem;
    }

    .name-col {
        flex: 1;
    }

    @media (max-width: 576px) {
        .name-row {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0">
                
                
                <div class="mathcrack-original-header">
                    
                    <img src="<?php echo e(asset('logo.png')); ?>" 
                         alt="MathCrack Logo" 
                         class="mathcrack-logo-img"
                         onerror="this.style.display='none'; document.getElementById('text-logo').style.display='block';">
                    
                    
                    <div id="text-logo" style="display: none;">
                        <h1 class="mathcrack-original-title">MathCrack</h1>
                        <p class="prof-name-original">Prof. Ahmed Omar</p>
                    </div>
                </div>
                

                <div class="card-body p-sm-5" style="padding-top: 15px !important;">
                    <h2 class="fs-20 fw-bolder mb-4"><?php echo app('translator')->get('l.Register'); ?></h2>

                    <form id="formAuthentication" class="w-100 mt-4 pt-2" method="post" action="<?php echo e(route('register')); ?>">
                        <?php echo csrf_field(); ?>

                        
                        <div class="name-row">
                            <div class="name-col">
                                <label class="form-label"><?php echo app('translator')->get('l.First Name'); ?></label>
                                <input type="text" class="form-control" id="firstname" name="firstname"
                                       value="<?php echo e(old('firstname')); ?>"
                                       placeholder="<?php echo app('translator')->get('l.Enter your first name'); ?>" autofocus required />
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['class' => 'mt-2 error-message','messages' => $errors->get('firstname')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-2 error-message','messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('firstname'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                            </div>

                            <div class="name-col">
                                <label class="form-label"><?php echo app('translator')->get('l.Last Name'); ?></label>
                                <input type="text" class="form-control" id="lastname" name="lastname"
                                       value="<?php echo e(old('lastname')); ?>"
                                       placeholder="<?php echo app('translator')->get('l.Enter your last name'); ?>" required />
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['class' => 'mt-2 error-message','messages' => $errors->get('lastname')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-2 error-message','messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('lastname'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><?php echo app('translator')->get('l.Email'); ?></label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo e(old('email')); ?>"
                                   placeholder="<?php echo app('translator')->get('l.Enter your email'); ?>" required />
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['class' => 'mt-2 error-message','messages' => $errors->get('email')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-2 error-message','messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('email'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><?php echo app('translator')->get('l.Phone'); ?></label>
                            <input type="hidden" id="phone_code" name="phone_code" value="<?php echo e(old('phone_code', '+20')); ?>" required>
                            <div class="input-group">
                                <input type="tel" id="phone" name="phone" 
                                       class="form-control multi-steps-mobile" 
                                       value="<?php echo e(old('phone')); ?>" required />
                            </div>
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['class' => 'mt-2 error-message','messages' => $errors->get('phone')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-2 error-message','messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('phone'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><?php echo app('translator')->get('l.Level'); ?></label>
                            <select id="level_id" class="form-control" name="level_id" required>
                                <option value="">Select your level</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($level->id); ?>" <?php echo e(old('level_id') == $level->id ? 'selected' : ''); ?>>
                                        <?php echo e($level->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['class' => 'mt-2 error-message','messages' => $errors->get('level')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-2 error-message','messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('level'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>

                        <input type="text" name="fax_number" style="display:none">

                        <div class="mb-4">
                            <label class="form-label"><?php echo app('translator')->get('l.Password'); ?></label>

                            <div class="input-group password-group">
                                <input type="password" id="password" class="form-control" name="password"
                                       minlength="8" placeholder="Enter your password" required />
                                <span class="input-group-text password-toggle" data-target="password" role="button" tabindex="0" aria-label="Toggle password">
                                    <i class="feather-eye"></i>
                                </span>
                            </div>

                            <div class="pass-help">Minimum 8 characters</div>
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['class' => 'mt-2 error-message','messages' => $errors->get('password')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-2 error-message','messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('password'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>

                            <div class="input-group password-group">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control" minlength="8"
                                       placeholder="Enter your password again" required />
                                <span class="input-group-text password-toggle" data-target="password_confirmation" role="button" tabindex="0" aria-label="Toggle confirm password">
                                    <i class="feather-eye"></i>
                                </span>
                            </div>

                            <div id="passMismatch" class="pass-error">Password and confirm password do not match</div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['recaptcha']): ?>
                            <div class="g-recaptcha mb-4" data-sitekey="<?php echo e(config('app.recaptcha.key')); ?>"></div>
                            <input type="hidden" name="disabled-submit" id="disabled-submit">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-5">
                            <button class="btn btn-lg btn-primary w-100" id="submitButton" type="submit"><?php echo app('translator')->get('l.Sign up'); ?></button>
                        </div>
                    </form>

                    <div class="mt-5 text-muted">
                        <span><?php echo app('translator')->get('l.Already have an account?'); ?></span>
                        <a href="<?php echo e(route('login')); ?>" class="fw-bold">
                            <span><?php echo app('translator')->get('l.Sign in instead'); ?></span>
                        </a>
                    </div>

                    <?php
                        $activeLogins = collect([
                            $settings['facebookLogin'],
                            $settings['googleLogin'],
                            $settings['twitterLogin'],
                        ])->filter()->count();
                    ?>

                    <div class="w-100 mt-5 text-center mx-auto">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['facebookLogin'] || $settings['googleLogin'] || $settings['twitterLogin']): ?>
                            <div class="mb-4 border-bottom position-relative">
                                <span class="small py-1 px-3 text-uppercase text-muted bg-white position-absolute translate-middle">
                                    <?php echo app('translator')->get('l.or'); ?>
                                </span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['facebookLogin']): ?>
                                <a href="<?php echo e(route('auth.facebook')); ?>" class="btn btn-light-brand flex-fill" title="Login with Facebook">
                                    <i class="feather-facebook"></i>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['googleLogin']): ?>
                                <a href="<?php echo e(route('auth.google')); ?>" class="btn btn-light-brand flex-fill" title="Login with Google">
                                    <i class="fa fa-google"></i>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['twitterLogin']): ?>
                                <a href="<?php echo e(route('auth.twitter')); ?>" class="btn btn-light-brand flex-fill" title="Login with Twitter">
                                    <i class="feather-twitter"></i>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<script>
$(document).ready(function () {

    var phoneInput = document.querySelector("#phone");
    var iti = window.intlTelInput(phoneInput, {
        initialCountry: "eg",
        geoIpLookup: function (callback) {
            $.get('https://ipinfo.io', function () {}, "jsonp").always(function (resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        },
        nationalMode: false,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        separateDialCode: true,
        formatOnDisplay: true,
        preferredCountries: ["eg", "sa", "ae"]
    });

    // استعادة رمز الدولة من old data
    var phone_code = document.querySelector("#phone_code");
    <?php if(old('phone_code')): ?>
        phone_code.value = "<?php echo e(old('phone_code')); ?>";
    <?php else: ?>
        phone_code.value = iti.getSelectedCountryData().dialCode;
    <?php endif; ?>

    phoneInput.addEventListener("countrychange", function () {
        phone_code.value = iti.getSelectedCountryData().dialCode;
    });

    // استعادة رقم الهاتف من old data
    <?php if(old('phone')): ?>
        // انتظر حتى يتم تهيئة intl-tel-input
        setTimeout(function() {
            var oldPhone = "<?php echo e(old('phone')); ?>";
            if (oldPhone) {
                // تنظيف الرقم من المسافات والرموز
                oldPhone = oldPhone.replace(/\s+/g, '').replace('+', '');
                
                // استخراج رمز الدولة من old data
                <?php if(old('phone_code')): ?>
                    var oldPhoneCode = "<?php echo e(old('phone_code')); ?>";
                    oldPhoneCode = oldPhoneCode.replace('+', '');
                    
                    // إزالة رمز الدولة من بداية الرقم إذا كان موجوداً
                    if (oldPhone.startsWith(oldPhoneCode)) {
                        oldPhone = oldPhone.substring(oldPhoneCode.length);
                    }
                <?php endif; ?>
                
                // تعيين القيمة في حقل الهاتف
                if (phoneInput && iti) {
                    phoneInput.value = oldPhone;
                }
            }
        }, 500);
    <?php endif; ?>

    function togglePassword(targetId, el) {
        var field = document.getElementById(targetId);
        if (!field) return;

        var icon = el.querySelector('i');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('feather-eye');
            icon.classList.add('feather-eye-off');
        } else {
            field.type = 'password';
            icon.classList.remove('feather-eye-off');
            icon.classList.add('feather-eye');
        }
    }

    document.querySelectorAll('.password-toggle').forEach(function (el) {
        el.addEventListener('click', function () {
            togglePassword(this.getAttribute('data-target'), this);
        });

        el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                togglePassword(this.getAttribute('data-target'), this);
            }
        });
    });

    function checkPasswordMatch() {
        var pass = document.getElementById('password').value;
        var conf = document.getElementById('password_confirmation').value;
        var msg = document.getElementById('passMismatch');

        if (!conf) {
            msg.style.display = 'none';
            return true;
        }

        if (pass !== conf) {
            msg.style.display = 'block';
            return false;
        }

        msg.style.display = 'none';
        return true;
    }

    document.getElementById('password').addEventListener('input', checkPasswordMatch);
    document.getElementById('password_confirmation').addEventListener('input', checkPasswordMatch);

    document.getElementById('formAuthentication').addEventListener('submit', function (e) {
        if (!checkPasswordMatch()) e.preventDefault();
    });

});
</script>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['recaptcha']): ?>
<script>
window.addEventListener('load', () => {
    const $recaptcha = document.querySelector('#g-recaptcha-response');
    if ($recaptcha) $recaptcha.setAttribute('required', 'required');
});
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('themes.default.auth.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/auth/register.blade.php ENDPATH**/ ?>