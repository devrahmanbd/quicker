<?php $__env->startSection('title'); ?>
    <?php echo e(get_label('login', 'Login')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <section class="section-py mt-7 py-2  ">
        <div class="container">
            <h1 class="text-center display-5 fw-semi-bold mt-5">
                <?php echo e(get_label('login_register_heading', 'Login or Register')); ?>

            </h1>
            <p class="text-center fs-0 fs-md-1">
                <?php echo e(get_label('login_register_subheading', 'Access your account or create a new one to start managing your projects.')); ?>

            </p>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="img-container">
                        <lottie-player src="/assets/front-end/img/gallery/Animation - 1712314757629.json"
                            background="transparent" speed="1" class="lottie" loop autoplay>
                        </lottie-player>
                    </div>
                </div>
                <div class="col-md-6 mt-8">
                    <ul class="nav nav-pills nav-justified mb-4" id="auth-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="login-tab" data-bs-toggle="tab" href="#login" role="tab"
                                aria-controls="login" aria-selected="true"><?php echo e(get_label('login', 'Login')); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="register-tab" data-bs-toggle="tab" href="#register" role="tab"
                                aria-controls="register" aria-selected="false"><?php echo e(get_label('register', 'Register')); ?></a>
                        </li>
                    </ul>
                    <div class="tab-content" id="auth-tab-content">
                        <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                            <div class="card shadow showcase-page-gradient">
                                <div class="card-body">
                                    <form id="formAuthentication" class="mb-3 form-submit-event"
                                        action="<?php echo e(route('users.authenticate')); ?>" method="POST">
                                        <input type="hidden" name="redirect_url" value="<?php echo e(route('home.index')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <div class="mb-3">
                                            <label for="email" class="form-label"><?php echo e(get_label('email', 'Email')); ?> <span
                                                    class="asterisk">*</span></label>
                                            <input type="text" class="form-control" id="email" name="email"
                                                placeholder="<?php echo e(get_label('enter_your_email', 'Please enter your email')); ?>"
                                                value="<?= config('constants.ALLOW_MODIFICATION') === 0 ? 'superadmin@gmail.com' : '' ?>"
                                                autofocus />

                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <p class="text-danger text-xs mt-1"><?php echo e($message); ?></p>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                        </div>
                                        <div class="mb-3 form-password-toggle">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label"
                                                    for="password"><?php echo e(get_label('password', 'Password')); ?>

                                                    <span class="asterisk">*</span></label>
                                                <a href="<?php echo e(route('forgot-password')); ?>">
                                                    <small
                                                        class ="text-dark"><?php echo e(get_label('forgot_password', 'Forgot Password?')); ?></small>
                                                </a>
                                            </div>
                                            <div class="input-group">
                                                <input type="password" id="password" class="form-control" name="password"
                                                    placeholder="<?php echo e(get_label('enter_your_password', 'Please enter your password')); ?>"
                                                    value="<?= config('constants.ALLOW_MODIFICATION') === 0 ? '12345678' : '' ?>"
                                                    aria-describedby="password" />
                                                    <span class="m-2 text-end text-sm" id="eyeicon">
                                                        <i class="far fa-eye-slash"></i>
                                                    </span>
                                            </div>

                                            <p class="text-danger text-xs mt-1 error-message"></p>

                                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <p class="text-danger text-xs mt-1"><?php echo e($message); ?></p>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>


                                        </div>

                                        <div class="mb-4">
                                            <button class="btn bg-gradient-primary d-grid w-100" type="submit"
                                                id = "loginBtn"><?php echo e(get_label('login', 'Login')); ?></button>
                                        </div>
                                        <?php if(config('constants.ALLOW_MODIFICATION') === 0): ?>
                                            <div class="mb-3">
                                                <button class="btn bg-gradient-danger d-grid w-100 superadmin-login"
                                                    type="button"><?php echo e(get_label('login_as_superadmin', 'Login As Super Admin')); ?></button>
                                            </div>
                                            <div class="mb-3">
                                                <button class="btn bg-gradient-success d-grid w-100 admin-login"
                                                    type="button"><?php echo e(get_label('login_as_admin', 'Login As Admin')); ?></button>
                                            </div>
                                            <div class="mb-3">
                                                <button class="btn bg-gradient-info d-grid w-100 member-login"
                                                    type="button"><?php echo e(get_label('login_as_team_member', 'Login As  Team Member')); ?></button>
                                            </div>
                                            <div class="mb-3">
                                                <button class="btn bg-gradient-warning d-grid w-100 client-login"
                                                    type="button"><?php echo e(get_label('login_as_client', 'Login As Client')); ?></button>
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                            <div class = " card">
                                <div class = "card-body">
                                    <form id = "formRegister" action = "<?php echo e(route('users.register')); ?>" method = "POST">
                                        <?php echo csrf_field(); ?>
                                        <!-- Name input -->
                                        <div class="row mt-3">
                                            <div class="col-lg-6 mb-3">
                                                <label for="first_name"
                                                    class="form-label"><?= get_label('first_name', 'First Name') ?>:</label>
                                                <input type="text" class="form-control" id="first_name"
                                                    name="first_name"
                                                    placeholder = "<?php echo e(get_label('first_name', 'First Name')); ?>"
                                                    value="<?php echo e(old('first_name')); ?>" required>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <label for="last_name"
                                                    class="form-label"><?= get_label('last_name', 'Last Name') ?>:</label>
                                                <input type="text" class="form-control" id="last_name"
                                                    name="last_name" placeholder=<?= get_label('last_name', 'Last Name') ?>
                                                    value="<?php echo e(old('last_name')); ?>" required>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-lg-6 mb-3">
                                                <label for="email"
                                                    class="form-label"><?= get_label('email', 'Email') ?>:</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="<?php echo e(old('email')); ?>"
                                                    placeholder = "<?= get_label('enter_your_email', 'Email') ?>" required>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <label for="phone"
                                                    class="form-label"><?= get_label('phone_number', 'Phone Number') ?>:</label>
                                                <input type="text" class="form-control" id="phone_number"
                                                    name="phone"
                                                    placeholder ="<?= get_label('enter_your_phone_number', 'Please Enter YourPhone Number') ?>"
                                                    value="<?php echo e(old('phone')); ?>" required>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-lg-6 mb-3">
                                                <label for="password"
                                                    class="form-label"><?= get_label('password', 'Password') ?>:</label>
                                                <input type="password" class="form-control" id="password"
                                                    name="password"
                                                    placeholder = "<?= get_label('enter_your_password', 'Password') ?>"
                                                    required>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <label for="password_confirmation"
                                                    class="form-label"><?= get_label('confirm_password', 'Confirm Password') ?>:</label>
                                                <input type="password" class="form-control" id="password_confirmation"
                                                    placeholder ="<?= get_label('confirm_password', 'Confirm Password') ?>"
                                                    name="password_confirmation" required>
                                            </div>
                                            <button type="button" id="registerCustomer"
                                                class="btn btn-primary"><?= get_label('register', 'Register ') ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front-end.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /srv/http/localhost/resources/views/front-end/login.blade.php ENDPATH**/ ?>