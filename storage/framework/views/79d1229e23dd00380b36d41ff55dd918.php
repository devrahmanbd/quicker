

<?php $__env->startSection('title'); ?>
    <?= get_label('email_settings', 'E-mail settings') ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between mt-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('home.index')); ?>"><?= get_label('home', 'Home') ?></a>
                        </li>
                        <li class="breadcrumb-item">
                            <?= get_label('settings', 'Settings') ?>
                        </li>
                        <li class="breadcrumb-item active">
                            <?= get_label('email', 'E-mail') ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="alert alert-primary" role="alert">
                    <?= get_label('important_settings_for_email_feature_to_be_work', 'Important settings for email feature to be work') ?>,
                    <a href="https://www.gmass.co/smtp-test"
                        target="_blank"><?= get_label('click_here_to_test_your_email_settings', 'Click here to test your email settings') ?></a>.
                </div>
                <form action="<?php echo e(route('settings.store_email')); ?>" class="form-submit-event" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" name="redirect_url" value="<?php echo e(route('settings.email')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="" class="form-label"><?= get_label('email', 'E-mail') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" id="" name="email"
                                placeholder="Enter email"
                                value="<?= config('constants.ALLOW_MODIFICATION') === 0 ? str_repeat('*', strlen($email_settings['email'])) : $email_settings['email'] ?>">

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
                        <div class="mb-3 col-md-6">
                            <label for="" class="form-label"><?= get_label('password', 'Password') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="password" name="password" placeholder="Enter password"
                                value="<?= $email_settings['password'] ?>">

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
                        <div class="mb-3 col-md-6">
                            <label for="" class="form-label"><?= get_label('smtp_host', 'SMTP host') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" id="" name="smtp_host"
                                placeholder="Enter SMTP host" value="<?= $email_settings['smtp_host'] ?>">

                            <?php $__errorArgs = ['smtp_host'];
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

                        <div class="mb-3 col-md-6">
                            <label for="" class="form-label"><?= get_label('smtp_port', 'SMTP port') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" id="" name="smtp_port"
                                placeholder="Enter SMTP port" value="<?= $email_settings['smtp_port'] ?>">

                            <?php $__errorArgs = ['smtp_port'];
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
                        <div class="mb-3 col-md-6">
                            <label class="form-label"
                                for=""><?= get_label('email_content_type', 'Email content type') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group">
                                <select class="form-control" type="text" id="" name="email_content_type">
                                    <option value="text"
                                        <?= $email_settings['email_content_type'] == 'text' ? 'selected' : '' ?>>Text
                                    </option>
                                    <option value="html"
                                        <?= $email_settings['email_content_type'] == 'html' ? 'selected' : '' ?>>HTML
                                    </option>
                                </select>
                            </div>
                            <?php $__errorArgs = ['email_content_type'];
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

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for=""><?= get_label('smtp_encryption', 'SMTP Encryption') ?>
                                <span class="asterisk">*</span></label>
                            <div class="input-group">
                                <select class="form-control" type="text" id="" name="smtp_encryption">
                                    <option value="off"
                                        <?= $email_settings['smtp_encryption'] == 'off' ? 'selected' : '' ?>>Off</option>
                                    <option value="ssl"
                                        <?= $email_settings['smtp_encryption'] == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value="tls"
                                        <?= $email_settings['smtp_encryption'] == 'tls' ? 'selected' : '' ?>>TLS</option>
                                </select>
                            </div>
                            <?php $__errorArgs = ['smtp_encryption'];
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

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2"
                                id="submit_btn"><?= get_label('update', 'Update') ?></button>
                            <button type="reset"
                                class="btn btn-outline-secondary"><?= get_label('cancel', 'Cancel') ?></button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /srv/http/localhost/resources/views/settings/email_settings.blade.php ENDPATH**/ ?>