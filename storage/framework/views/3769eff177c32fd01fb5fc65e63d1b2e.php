

<?php $__env->startSection('title'); ?>
    <?= get_label('pusher_settings', 'Pusher settings') ?>
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
                            <?= get_label('pusher', 'Pusher') ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="alert alert-primary" role="alert">
                    <?= get_label('important_settings_for_chat_feature_to_be_work', 'Important settings for chat feature to be work') ?>,
                    <a href="https://dashboard.pusher.com/apps"
                        target="_blank"><?= get_label('click_here_to_find_these_settings_on_your_pusher_account', 'Click here to find these settings on your pusher account') ?></a>.
                </div>
                <form action="<?php echo e(route('settings.store_pusher')); ?>" class="form-submit-event" method="POST">
                    <input type="hidden" name="redirect_url" value="<?php echo e(route('settings.pusher')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="company_title" class="form-label"><?= get_label('pusher_app_id', 'Pusher APP ID') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="pusher_app_id"
                                placeholder="Enter pusher APP ID"
                                value="<?= config('constants.ALLOW_MODIFICATION') === 0 ? str_repeat('*', strlen($pusher_settings['pusher_app_id'])) : $pusher_settings['pusher_app_id'] ?>">

                            <?php $__errorArgs = ['pusher_app_id'];
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
                            <label for="company_title"
                                class="form-label"><?= get_label('pusher_app_key', 'Pusher APP key') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="pusher_app_key"
                                placeholder="Enter pusher APP key"
                                value="<?= config('constants.ALLOW_MODIFICATION') === 0 ? str_repeat('*', strlen($pusher_settings['pusher_app_key'])) : $pusher_settings['pusher_app_key'] ?>">

                            <?php $__errorArgs = ['pusher_app_key'];
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
                            <label for="company_title"
                                class="form-label"><?= get_label('pusher_app_secret', 'Pusher APP secret') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="pusher_app_secret"
                                placeholder="Enter pusher APP secret"
                                value="<?= config('constants.ALLOW_MODIFICATION') === 0 ? str_repeat('*', strlen($pusher_settings['pusher_app_secret'])) : $pusher_settings['pusher_app_secret'] ?>">

                            <?php $__errorArgs = ['pusher_app_secret'];
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
                            <label for="company_title"
                                class="form-label"><?= get_label('pusher_app_cluster', 'Pusher APP cluster') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="pusher_app_cluster"
                                placeholder="Enter pusher APP cluster"
                                value="<?= config('constants.ALLOW_MODIFICATION') === 0 ? str_repeat('*', strlen($pusher_settings['pusher_app_cluster'])) : $pusher_settings['pusher_app_cluster'] ?>">

                            <?php $__errorArgs = ['pusher_app_id'];
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

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /srv/http/localhost/resources/views/settings/pusher_settings.blade.php ENDPATH**/ ?>