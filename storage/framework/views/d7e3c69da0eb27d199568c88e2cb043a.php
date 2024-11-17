

<?php $__env->startSection('title'); ?>
    <?= get_label('update_role', 'Update role') ?>
<?php $__env->stopSection(); ?>
<?php

use Spatie\Permission\Models\Permission; ?>

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
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('roles.index')); ?>"><?= get_label('permissions', 'Permissions') ?></a>
                        </li>
                        <li class="breadcrumb-item active">
                            <?= get_label('update_role', 'Update role') ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('roles.update', ['id' => $role->id])); ?>" class="form-submit-event" method="POST">
                    <input type="hidden" name="redirect_url" value="<?php echo e(route('roles.index')); ?>">
                    <?php echo method_field('PUT'); ?>
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="mb-3">
                            <label for="name" class="form-label"><?= get_label('name', 'Name') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" placeholder="Enter role name" id="name"
                                name="name" placeholder="Enter Name" value="<?php echo e($role->name); ?>">
                            <?php $__errorArgs = ['name'];
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
                        <div class="mb-3">
                            <label class="form-label" for=""><?= get_label('data_access', 'Data Access') ?> (<small
                                    class="text-muted mt-2">If all data access is selected, user under this roles will have
                                    unrestricted access to all data, irrespective of any specific assignments or
                                    restrictions</small>)</label>
                            <div class="btn-group btn-group d-flex justify-content-center" role="group"
                                aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="permissions[]" id="access_all_data"
                                    value="<?= $guard == 'client' ? Permission::where('name', 'access_all_data')->where('guard_name', 'client')->first()->id : Permission::where('name', 'access_all_data')->where('guard_name', 'web')->first()->id ?>"
                                    <?php echo e($role_permissions->contains('name', 'access_all_data') ? 'checked' : ''); ?>>
                                <label class="btn btn-outline-primary"
                                    for="access_all_data"><?= get_label('all_data_access', 'All Data Access') ?></label>

                                <input type="radio" class="btn-check" name="permissions[]" id="access_allocated_data"
                                    value="0"
                                    <?php echo e($role_permissions->contains('name', 'access_all_data') ? '' : 'checked'); ?>>
                                <label class="btn btn-outline-primary"
                                    for="access_allocated_data"><?= get_label('allocated_data_access', 'Allocated Data Access') ?></label>
                            </div>

                        </div>
                    </div>


                    <hr class="mb-2" />

                    <div class="table-responsive text-nowrap">
                        <table class="table my-2">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input type="checkbox" id="selectAllColumnPermissions" class="form-check-input">
                                            <label class="form-check-label"
                                                for="selectAllColumnPermissions"><?= get_label('select_all', 'Select all') ?></label>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $__currentLoopData = config('taskify.permissions'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module => $permissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" id="selectRow<?php echo e($module); ?>"
                                                    class="form-check-input row-permission-checkbox"
                                                    data-module="<?php echo e($module); ?>">
                                                <label class="form-check-label"
                                                    for="selectRow<?php echo e($module); ?>"><?php echo e($module); ?></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-wrap justify-content-between">

                                                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="form-check mx-4">
                                                        <?php if($guard == 'client'): ?>
                                                            <?php
                                                            $permissionModel = Permission::where('name', $permission)->where('guard_name', 'client')->first();
                                                            
                                                            ?>

                                                            <input type="checkbox" name="permissions[]"
                                                                value="<?php echo e($permissionModel ? $permissionModel->id : ''); ?>"
                                                                class="form-check-input permission-checkbox"
                                                                data-module="<?php echo e($module); ?>"
                                                                <?php echo e($role_permissions->contains('name', $permission) ? 'checked' : ''); ?>>
                                                            <label
                                                                class="form-check-label text-capitalize"><?php echo e($permissionModel ? substr($permissionModel->name, 0, strpos($permissionModel->name, '_')) : ''); ?></label>
                                                        <?php else: ?>
                                                            <input type="checkbox" name="permissions[]"
                                                                value="<?php print_r(Permission::findByName($permission)->id); ?>"
                                                                class="form-check-input permission-checkbox"
                                                                data-module="<?php echo e($module); ?>"
                                                                <?php echo e($role_permissions->contains('name', $permission) ? 'checked' : ''); ?>>
                                                            <label
                                                                class="form-check-label text-capitalize"><?php print_r(substr($permission, 0, strpos($permission, '_'))); ?></label>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>


                        </table>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2"
                            id="submit_btn"><?= get_label('update', 'Update') ?></button>
                        <button type="reset"
                            class="btn btn-outline-secondary"><?= get_label('cancel', 'Cancel') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Siddharth Gor\Test\resources\views/roles/edit_role.blade.php ENDPATH**/ ?>