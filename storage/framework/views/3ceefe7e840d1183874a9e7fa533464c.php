<?php
    $prefix = null;
    $currentRoute = Route::current();
    if ($currentRoute) {
        $uriSegments = explode('/', $currentRoute->uri());
        $prefix = count($uriSegments) > 1 ? $uriSegments[0] : '';
    }
    use App\Models\Workspace;
    $workspace = Workspace::find(session()->get('workspace_id'));
    $auth_user = getAuthenticatedUser();
    $toSelectProjectUsers = isset($workspace) ? $workspace->users : [];
    $toSelectProjectClients = isset($workspace) ? $workspace->clients : [];
    $roles = \Spatie\Permission\Models\Role::where([['name', '!=', 'admin'], ['name', '!=', 'superadmin']])->get();
    $adminId = getAdminIdByUserRole();
    $admin = App\Models\Admin::with('user', 'teamMembers.user')->find($adminId);
    $clients = App\Models\Client::where('admin_id', $adminId)->get();
?>
<?php if(Request::is($prefix . '/projects') ||
        Request::is($prefix . '/projects/*') ||
        Request::is($prefix . '/tasks') ||
        Request::is($prefix . '/tasks/*') ||
        Request::is($prefix . '/status/manage') ||
        Request::is($prefix . '/users') ||
        Request::is($prefix . '/clients')): ?>
    <div class="modal fade" id="create_status_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('status.store')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_status', 'Create status') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="nameBasic" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select" id="color" name="color">
                                <option class="badge bg-label-primary" value="primary"
                                    <?php echo e(old('color') == 'primary' ? 'selected' : ''); ?>>
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary"
                                    <?php echo e(old('color') == 'secondary' ? 'selected' : ''); ?>>
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success"
                                    <?php echo e(old('color') == 'success' ? 'selected' : ''); ?>>
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger"
                                    <?php echo e(old('color') == 'danger' ? 'selected' : ''); ?>>
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning"
                                    <?php echo e(old('color') == 'warning' ? 'selected' : ''); ?>>
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"
                                    <?php echo e(old('color') == 'info' ? 'selected' : ''); ?>><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"
                                    <?php echo e(old('color') == 'dark' ? 'selected' : ''); ?>><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <?php if(isAdminOrHasAllDataAccess()): ?>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label
                                    class="form-label"><?= get_label('roles_can_set_status', 'Roles Can Set the Status') ?>
                                    <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                        data-bs-offset="0,4" data-bs-placement="top" title=""
                                        data-bs-original-title="<?php echo e(get_label('roles_can_set_status_info', 'Including Admin and Roles with All Data Access Permission, Users/Clients Under Selected Role(s) Will Have Permission to Set This Status.')); ?>"></i></label>
                                <div class="input-group">
                                    <select class="form-control js-example-basic-multiple" name="role_ids[]"
                                        multiple="multiple"
                                        data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                        <?php if(isset($roles)): ?>
                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_status_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?php echo e(route('status.update')); ?>" class="modal-content form-submit-event" method="POST">
                <input type="hidden" name="id" id="status_id">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_status', 'Update status') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="status_title" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select" id="status_color" name="color" required>
                                <option class="badge bg-label-primary" value="primary">
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary">
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success">
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger">
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning">
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <?php if(isAdminOrHasAllDataAccess()): ?>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label
                                    class="form-label"><?= get_label('roles_can_set_status', 'Roles Can Set the Status') ?>
                                    <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                        data-bs-offset="0,4" data-bs-placement="top" title=""
                                        data-bs-original-title="<?php echo e(get_label('roles_can_set_status_info', 'Including Admin and Roles with All Data Access Permission, Users/Clients Under Selected Role(s) Will Have Permission to Set This Status.')); ?>"></i></label>
                                <div class="input-group">
                                    <select class="form-control js-example-basic-multiple" name="role_ids[]"
                                        multiple="multiple"
                                        data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                        <?php if(isset($roles)): ?>
                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<div class="modal fade" id="confirmUpdateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('confirm_update_status', 'Do You Want to Update the Status?') ?></p>
                <textarea class="form-control" id="statusNote" placeholder="<?= get_label('optional_note', 'Optional Note') ?>"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="declineUpdateStatus"
                    data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmUpdateStatus"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmUpdatePriorityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('confirm_update_priority', 'Do You Want to Update the Priority?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="declineUpdatePriority"
                    data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmUpdatePriority"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<?php if(Request::is($prefix . '/projects') ||
        Request::is($prefix . '/projects/*') ||
        Request::is($prefix . '/tags/manage') ||
        Request::is($prefix . '/users') ||
        Request::is($prefix . '/clients')): ?>
    <div class="modal fade" id="create_tag_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('tags.store')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_tag', 'Create tag') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="nameBasic" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-primary" id="color" name="color">
                                <option class="badge bg-label-primary" value="primary"
                                    <?php echo e(old('color') == 'primary' ? 'selected' : ''); ?>>
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary"
                                    <?php echo e(old('color') == 'secondary' ? 'selected' : ''); ?>>
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success"
                                    <?php echo e(old('color') == 'success' ? 'selected' : ''); ?>>
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger"
                                    <?php echo e(old('color') == 'danger' ? 'selected' : ''); ?>>
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning"
                                    <?php echo e(old('color') == 'warning' ? 'selected' : ''); ?>>
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"
                                    <?php echo e(old('color') == 'info' ? 'selected' : ''); ?>><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"
                                    <?php echo e(old('color') == 'dark' ? 'selected' : ''); ?>><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/tags/manage')): ?>
    <div class="modal fade" id="edit_tag_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('tags.update')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="id" id="tag_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_tag', 'Update tag') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="tag_title" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-primary" id="tag_color" name="color">
                                <option class="badge bg-label-primary" value="primary">
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary">
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success">
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger">
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning">
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/home') || Request::is($prefix . '/todos')): ?>
    <div class="modal fade" id="create_todo_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('todos.store')); ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_todo', 'Create todo') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('priority', 'Priority') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select" name="priority">
                                <option value="low" <?php echo e(old('priority') == 'low' ? 'selected' : ''); ?>>
                                    <?= get_label('low', 'Low') ?></option>
                                <option value="medium" <?php echo e(old('priority') == 'medium' ? 'selected' : ''); ?>>
                                    <?= get_label('medium', 'Medium') ?></option>
                                <option value="high" <?php echo e(old('priority') == 'high' ? 'selected' : ''); ?>>
                                    <?= get_label('high', 'High') ?></option>
                            </select>
                        </div>
                    </div>
                    <label for="description" class="form-label"><?= get_label('description', 'Description') ?></label>
                    <textarea class="form-control description" name="description"
                        placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_todo_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?php echo e(route('todos.update')); ?>" class="modal-content form-submit-event" method="POST">
                <input type="hidden" name="id" id="todo_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_todo', 'Update todo') ?></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="todo_title" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('priority', 'Priority') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select" id="todo_priority" name="priority">
                                <option value="low"><?= get_label('low', 'Low') ?></option>
                                <option value="medium"><?= get_label('medium', 'Medium') ?></option>
                                <option value="high"><?= get_label('high', 'High') ?></option>
                            </select>
                        </div>
                    </div>
                    <label for="description" class="form-label"><?= get_label('description', 'Description') ?></label>
                    <textarea class="form-control description" id="todo_description" name="description"
                        placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></span></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<div class="modal fade" id="default_language_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('set_primary_lang_alert', 'Are you want to set as your primary language?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary" id="confirm"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="leaveWorkspaceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= get_label('confirm_leave_workspace', 'Are you sure you want leave this workspace?') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger" id="confirm"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_language_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-submit-event" action="<?php echo e(route('languages.store')); ?>" method="POST">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_language', 'Create language') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="name"
                            placeholder="For Example: English" />
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-danger mt-1 text-xs"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('code', 'Code') ?> <span
                                class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="code" placeholder="For Example: en" />
                        <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-danger mt-1 text-xs"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn"
                    class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="edit_language_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form class="modal-content form-submit-event" action="<?php echo e(route('languages.update')); ?>" method="POST">
            <input type="hidden" name="id" id="language_id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_language', 'Update language') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="name" id="language_title"
                            placeholder="For Example: English" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn"
                    class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
            </div>
        </form>
    </div>
</div>
<?php if(Request::is($prefix . '/leave-requests') || Request::is($prefix . '/leave-requests/*')): ?>
    <div class="modal fade" id="create_leave_request_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('leave_requests.store')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="table" value="lr_table">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_leave_requet', 'Create leave request') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <?php if(is_admin_or_leave_editor()): ?>
                            <div class="col-12 mb-3">
                                <label class="form-label"
                                    for="user_id"><?= get_label('select_user', 'Select user') ?> <span
                                        class="asterisk">*</span></label>
                                <select class="form-select selectLruser"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>"
                                    name="user_id">
                                    <?php if(isset($users)): ?>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->id); ?>"
                                                <?= $user->id == getAuthenticatedUser()->id ? 'selected' : '' ?>>
                                                <?php echo e($user->first_name . ' ' . $user->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <label class="form-check-label" for="partialLeave">
                                    <input class="form-check-input" type="checkbox" name="partialLeave"
                                        id="partialLeave">
                                    <?= get_label('partial_leave', 'Partial Leave') ?>?
                                </label>
                            </div>
                        </div>
                        <div class="col-5 leave-from-date-div mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('from_date', 'From date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="start_date" name="from_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-5 leave-to-date-div mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('to_date', 'To date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="lr_end_date" name="to_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-2 leave-from-time-div d-none mb-3">
                            <label class="form-label" for=""><?= get_label('from_time', 'From Time') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="time" name="from_time" class="form-control"
                                value="<?php echo e(old('from_time')); ?>">
                        </div>
                        <div class="col-2 leave-to-time-div d-none mb-3">
                            <label class="form-label" for=""><?= get_label('to_time', 'To Time') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="time" name="to_time" class="form-control" value="<?php echo e(old('to_time')); ?>">
                        </div>
                        <div class="col-2 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('days', 'Days') ?></label>
                            <input type="text" id="total_days" class="form-control" value="1"
                                placeholder="" disabled>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input leaveVisibleToAll" type="checkbox"
                                    name="leaveVisibleToAll" id="leaveVisibleToAll">
                                <label class="form-check-label"
                                    for="leaveVisibleToAll"><?= get_label('visible_to_all', 'Visible To All') ?>? <i
                                        class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                        data-bs-offset="0,4" data-bs-placement="top" title=""
                                        data-bs-html="true"
                                        data-bs-original-title="<?php echo e(get_label('leave_visible_to_info', 'Disabled: Requestee, Admin, and Leave Editors, along with selected users, will be able to know when the requestee is on leave. Enabled: All team members will be able to know when the requestee is on leave.')); ?>"></i></label>
                            </div>
                        </div>
                        <div class="col-12 leaveVisibleToDiv mb-3">
                            <select class="form-control js-example-basic-multiple" name="visible_to_ids[]"
                                multiple="multiple"
                                data-placeholder="<?= get_label('select_users_leave_visible_to', 'Select Users Leave Visible To') ?>">
                                <?php if(isset($users)): ?>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!is_admin_or_leave_editor($user) && $user->id != $auth_user->id): ?>
                                            <option value="<?php echo e($user->id); ?>">
                                                <?php echo e($user->first_name . ' ' . $user->last_name); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <label for="description" class="form-label"><?= get_label('reason', 'Reason') ?> <span
                            class="asterisk">*</span></label>
                    <textarea class="form-control" name="reason"
                        placeholder="<?= get_label('please_enter_leave_reason', 'Please enter leave reason') ?>"></textarea>
                    <?php if(is_admin_or_leave_editor()): ?>
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="status" id="create_lr_pending"
                                        value="pending" checked>
                                    <label class="btn btn-outline-primary"
                                        for="create_lr_pending"><?= get_label('pending', 'Pending') ?></label>
                                    <input type="radio" class="btn-check" name="status" id="create_lr_approved"
                                        value="approved">
                                    <label class="btn btn-outline-primary"
                                        for="create_lr_approved"><?= get_label('approved', 'Approved') ?></label>
                                    <input type="radio" class="btn-check" name="status" id="create_lr_rejected"
                                        value="rejected">
                                    <label class="btn btn-outline-primary"
                                        for="create_lr_rejected"><?= get_label('rejected', 'Rejected') ?></label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_leave_request_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('leave_requests.update')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="table" value="lr_table">
                <input type="hidden" name="id" id="lr_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_leave_request', 'Update leave request') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <?php if(is_admin_or_leave_editor()): ?>
                            <div class="col-12 mb-3">
                                <label class="form-label"><?= get_label('user', 'User') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="text" id="leaveUser" class="form-control" disabled>
                            </div>
                        <?php endif; ?>
                        <div class="col-12">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="updatePartialLeave"
                                    name="partialLeave">
                                <label class="form-check-label"
                                    for="updatePartialLeave"><?= get_label('partial_leave', 'Partial Leave') ?>?</label>
                            </div>
                        </div>
                        <div class="col-5 leave-from-date-div mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('from_date', 'From date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="update_start_date" name="from_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-5 leave-to-date-div mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('to_date', 'To date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="update_end_date" name="to_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-2 leave-from-time-div d-none mb-3">
                            <label class="form-label" for=""><?= get_label('from_time', 'From Time') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="time" name="from_time" class="form-control">
                        </div>
                        <div class="col-2 leave-to-time-div d-none mb-3">
                            <label class="form-label" for=""><?= get_label('to_time', 'To Time') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="time" name="to_time" class="form-control">
                        </div>
                        <div class="col-2 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('days', 'Days') ?></label>
                            <input type="text" id="update_total_days" class="form-control" value="1"
                                placeholder="" disabled>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input leaveVisibleToAll" type="checkbox"
                                    name="leaveVisibleToAll" id="updateLeaveVisibleToAll">
                                <label class="form-check-label"
                                    for="updateLeaveVisibleToAll"><?= get_label('visible_to_all', 'Visible To All') ?>?
                                    <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                        data-bs-offset="0,4" data-bs-placement="top" title=""
                                        data-bs-html="true"
                                        data-bs-original-title="<?php echo e(get_label('leave_visible_to_info', 'Disabled: Requestee, Admin, and Leave Editors, along with selected users, will be able to know when the requestee is on leave. Enabled: All team members will be able to know when the requestee is on leave.')); ?>"></i></label>
                            </div>
                        </div>
                        <div class="col-12 leaveVisibleToDiv mb-3">
                            <select class="form-control js-example-basic-multiple" name="visible_to_ids[]"
                                multiple="multiple"
                                data-placeholder="<?= get_label('select_users_leave_visible_to', 'Select Users Leave Visible To') ?>">
                                <?php if(isset($users)): ?>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!is_admin_or_leave_editor($user) && $user->id != $auth_user->id): ?>
                                            <option value="<?php echo e($user->id); ?>">
                                                <?php echo e($user->first_name . ' ' . $user->last_name); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label"><?= get_label('reason', 'Reason') ?> <span
                                    class="asterisk">*</span></label>
                            <textarea class="form-control" name="reason"
                                placeholder="<?= get_label('please_enter_leave_reason', 'Please enter leave reason') ?>"></textarea>
                        </div>
                        <?php
                            $isAdminOrLr = is_admin_or_leave_editor();
                            $disabled = !$isAdminOrLr ? 'disabled' : '';
                        ?>
                        <div class="col-12 d-flex justify-content-center">
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="status" id="update_lr_pending"
                                    value="pending" <?php echo e($disabled); ?>>
                                <label class="btn btn-outline-primary"
                                    for="update_lr_pending"><?= get_label('pending', 'Pending') ?></label>
                                <input type="radio" class="btn-check" name="status" id="update_lr_approved"
                                    value="approved" <?php echo e($disabled); ?>>
                                <label class="btn btn-outline-primary"
                                    for="update_lr_approved"><?= get_label('approved', 'Approved') ?></label>
                                <input type="radio" class="btn-check" name="status" id="update_lr_rejected"
                                    value="rejected" <?php echo e($disabled); ?>>
                                <label class="btn btn-outline-primary"
                                    for="update_lr_rejected"><?= get_label('rejected', 'Rejected') ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="create_contract_type_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form class="modal-content form-submit-event" action="<?php echo e(route('contracts.store_contract_type')); ?>"
            method="POST">
            <input type="hidden" name="dnr">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">
                    <?= get_label('create_contract_type', 'Create contract type') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="type"
                            placeholder="<?= get_label('please_enter_contract_type', 'Please enter contract type') ?>" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn"
                    class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="edit_contract_type_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form class="modal-content form-submit-event" action="<?php echo e(route('contracts.update_contract_type')); ?>"
            method="POST">
            <input type="hidden" name="dnr">
            <input type="hidden" id="update_contract_type_id" name="id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">
                    <?= get_label('update_contract_type', 'Update contract type') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="type" id="contract_type"
                            placeholder="<?= get_label('please_enter_contract_type', 'Please enter contract type') ?>" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn"
                    class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
            </div>
        </form>
    </div>
</div>
<?php if(Request::is($prefix . '/contracts')): ?>
    <div class="modal fade" id="create_contract_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('contracts.store')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="table" value="contracts_table">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_contract', 'Create contract') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" name="title" class="form-control"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('value', 'Value') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input type="text" name="value" class="form-control"
                                    placeholder="<?= get_label('please_enter_value', 'Please enter value') ?>">
                            </div>
                            <span class="text-danger error-message"></span>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('starts_at', 'Starts at') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="start_date" name="start_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('ends_at', 'Ends at') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="end_date" name="end_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <?php if(!isClient()): ?>
                            <label class="form-label"
                                for=""><?= get_label('select_client', 'Select client') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="col-12 mb-3">
                                <select class="form-select" name="client_id">
                                    <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                    <?php if(isset($clients)): ?>
                                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($client->id); ?>">
                                                <?php echo e($client->first_name . ' ' . $client->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <label class="form-label" for=""><?= get_label('select_project', 'Select project') ?>
                            <span class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-select" name="project_id">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <?php if(isset($projects)): ?>
                                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($project->id); ?>"><?php echo e($project->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <label class="form-label"
                            for=""><?= get_label('select_contract_type', 'Select contract type') ?> <span
                                class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-select" name="contract_type_id">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <?php if(isset($contract_types)): ?>
                                    <?php $__currentLoopData = $contract_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($type->id); ?>"><?php echo e($type->type); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateContractTypeModal"><button
                                        type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_contract_type', 'Create contract type') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="/contracts/contract-types"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_contract_types', 'Manage contract types') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <label for="description" class="form-label"><?= get_label('description', 'Description') ?></label>
                    <textarea class="form-control description" name="description" id="contract_description"
                        placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_contract_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('contracts.update')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="table" value="contracts_table">
                <input type="hidden" id="contract_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_contract', 'Update contract') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="title" name="title" class="form-control"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('value', 'Value') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input type="text" id="value" name="value" class="form-control"
                                    placeholder="<?= get_label('please_enter_value', 'Please enter value') ?>">
                            </div>
                            <span class="text-danger error-message"></span>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('starts_at', 'Starts at') ?>
                                <span class="asterisk">*</span></label>
                            <input type="text" id="update_start_date" name="start_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('ends_at', 'Ends at') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="update_end_date" name="end_date" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <label class="form-label" for=""><?= get_label('select_client', 'Select client') ?>
                            <span class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-select" id="client_id" name="client_id">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <?php if(isset($clients)): ?>
                                    <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($client->id); ?>">
                                            <?php echo e($client->first_name . ' ' . $client->last_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <label class="form-label"
                            for=""><?= get_label('select_project', 'Select project') ?> <span
                                class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-select" id="project_id" name="project_id">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <?php if(isset($projects)): ?>
                                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($project->id); ?>"><?php echo e($project->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <label class="form-label"
                            for=""><?= get_label('select_contract_type', 'Select contract type') ?> <span
                                class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-select" id="contract_type_id" name="contract_type_id">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <?php if(isset($contract_types)): ?>
                                    <?php $__currentLoopData = $contract_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($type->id); ?>"><?php echo e($type->type); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateContractTypeModal"><button
                                        type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_contract_type', 'Create contract type') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="/contracts/contract-types"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_contract_types', 'Manage contract types') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <label for="description"
                        class="form-label"><?= get_label('description', 'Description') ?></label>
                    <textarea class="form-control description" name="description" id="update_contract_description"
                        placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/payslips/create') || Request::is($prefix . '/payment-methods')): ?>
    <div class="modal fade" id="create_pm_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('paymentMethods.store')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_payment_method', 'Create payment method') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="Please enter title" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_pm_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('paymentMethods.update')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="pm_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_payment_method', 'Update payment method') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title" id="pm_title"
                                placeholder="Please enter title" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/payslips/*') || Request::is($prefix . '/allowances')): ?>
    <div class="modal fade" id="create_allowance_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('allowances.store')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_allowance', 'Create allowance') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="Please enter title" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input class="form-control min_0" min="0" type="number" name="amount"
                                    step="0.01" placeholder="Please enter amount">
                            </div>
                            <p class="text-danger error-message mt-1 text-xs"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_allowance_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('allowances.update')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" name="id" id="allowance_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_allowance', 'Update allowance') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="allowance_title" name="title"
                                placeholder="Please enter title" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input class="form-control" type="number" id="allowance_amount" name="amount"
                                    step="0.01" placeholder="Please enter amount">
                            </div>
                            <p class="text-danger error-message mt-1 text-xs"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/payslips/*') || Request::is($prefix . '/deductions')): ?>
    <div class="modal fade" id="create_deduction_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('deductions.store')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_deduction', 'Create deduction') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="Please enter title" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                    class="asterisk">*</span></label>
                            <select id="deduction_type" name="type" class="form-select">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <option value="amount"><?= get_label('amount', 'Amount') ?></option>
                                <option value="percentage"><?= get_label('percentage', 'Percentage') ?></option>
                            </select>
                        </div>
                        <div class="col-md-12 d-none mb-3" id="amount_div">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input class="form-control min_0" min="0" type="number" name="amount"
                                    step="0.01" placeholder="Please enter amount">
                            </div>
                            <p class="text-danger error-message mt-1 text-xs"></p>
                        </div>
                        <div class="col-md-12 d-none mb-3" id="percentage_div">
                            <label class="form-label" for=""><?= get_label('percentage', 'Percentage') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control max_100" max="100" type="number" name="percentage"
                                placeholder="Please enter percentage">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_deduction_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('deductions.update')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="deduction_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_deduction', 'Update deduction') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="deduction_title" name="title"
                                placeholder="Please enter title" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                    class="asterisk">*</span></label>
                            <select id="update_deduction_type" name="type" class="form-control">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <option value="amount"><?= get_label('amount', 'Amount') ?></option>
                                <option value="percentage"><?= get_label('percentage', 'Percentage') ?></option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3" id="update_amount_div">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input class="form-control" type="number" id="deduction_amount" name="amount"
                                    step="0.01" placeholder="Please enter amount">
                            </div>
                            <p class="text-danger error-message mt-1 text-xs"></p>
                        </div>
                        <div class="col-md-12 mb-3" id="update_percentage_div">
                            <label class="form-label" for=""><?= get_label('percentage', 'Percentage') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="number" id="deduction_percentage"
                                name="percentage" placeholder="Please enter percentage">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/estimates-invoices/create') ||
        Request::is($prefix . '/taxes') ||
        Request::is($prefix . '/units') ||
        Request::is($prefix . '/items')): ?>
    <div class="modal fade" id="create_tax_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('taxes.store')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_tax', 'Create tax') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                    class="asterisk">*</span></label>
                            <select id="deduction_type" name="type" class="form-select">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <option value="amount"><?= get_label('amount', 'Amount') ?></option>
                                <option value="percentage"><?= get_label('percentage', 'Percentage') ?></option>
                            </select>
                        </div>
                        <div class="col-md-12 d-none mb-3" id="amount_div">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input class="form-control" type="number" min="0" name="amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                            </div>
                            <p class="text-danger error-message mt-1 text-xs"></p>
                        </div>
                        <div class="col-md-12 d-none mb-3" id="percentage_div">
                            <label class="form-label" for=""><?= get_label('percentage', 'Percentage') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="number" name="percentage" min="1"
                                max="100"
                                placeholder="<?= get_label('please_enter_percentage', 'Please enter percentage') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_tax_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('taxes.update')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="tax_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_tax', 'Update tax') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="tax_title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span
                                    class="asterisk">*</span></label>
                            <select id="update_tax_type" name="type" class="form-select" disabled>
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <option value="amount"><?= get_label('amount', 'Amount') ?></option>
                                <option value="percentage"><?= get_label('percentage', 'Percentage') ?></option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3" id="update_amount_div">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input class="form-control" type="text" id="tax_amount" name="amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>"
                                    disabled>
                            </div>
                            <p class="text-danger error-message mt-1 text-xs"></p>
                        </div>
                        <div class="col-md-12 mb-3" id="update_percentage_div">
                            <label class="form-label" for=""><?= get_label('percentage', 'Percentage') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="number" id="tax_percentage" name="percentage"
                                placeholder="<?= get_label('please_enter_percentage', 'Please enter percentage') ?>"
                                disabled>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="create_unit_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('units.store')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_unit', 'Create unit') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_unit_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('units.update')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="unit_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_unit', 'Update unit') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="unit_title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" id="unit_description" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="create_item_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('items.store')); ?>" method="POST">
                <?php if(Request::is('items')): ?>
                    <input type="hidden" name="dnr">
                <?php else: ?>
                    <input type="hidden" name="reload">
                <?php endif; ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_item', 'Create item') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('price', 'Price') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="price"
                                placeholder="<?= get_label('please_enter_price', 'Please enter price') ?>" />
                        </div>
                        <?php if(isset($units) && is_iterable($units)): ?>
                            <div class="col-md-6 mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('unit', 'Unit') ?></label>
                                <select class="form-select" name="unit_id">
                                    <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                    <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($unit->id); ?>"><?php echo e($unit->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_item_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('items.update')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="item_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_item', 'Update item') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="item_title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('price', 'Price') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="item_price" name="price"
                                placeholder="<?= get_label('please_enter_price', 'Please enter price') ?>" />
                        </div>
                        <?php if(isset($units) && is_iterable($units)): ?>
                            <div class="col-md-6 mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('unit', 'Unit') ?></label>
                                <select class="form-select" id="item_unit_id" name="unit_id">
                                    <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                    <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($unit->id); ?>"><?php echo e($unit->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" id="item_description" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/notes')): ?>
    <div class="modal fade" id="create_note_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('notes.store')); ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_note', 'Create note') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="nameBasic" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-success" name="color">
                                <option class="badge bg-label-success" value="info"
                                    <?php echo e(old('color') == 'info' ? 'selected' : ''); ?>><?= get_label('green', 'Green') ?>
                                </option>
                                <option class="badge bg-label-warning" value="warning"
                                    <?php echo e(old('color') == 'warning' ? 'selected' : ''); ?>>
                                    <?= get_label('yellow', 'Yellow') ?></option>
                                <option class="badge bg-label-danger" value="danger"
                                    <?php echo e(old('color') == 'danger' ? 'selected' : ''); ?>><?= get_label('red', 'Red') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_note_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('notes.update')); ?>" method="POST">
                <input type="hidden" name="id" id="note_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_note', 'Update note') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" id="note_title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" id="note_description" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select select-bg-label-success" id="note_color" name="color">
                                <option class="badge bg-label-success" value="success"
                                    <?php echo e(old('color') == 'success' ? 'selected' : ''); ?>>
                                    <?= get_label('green', 'Green') ?></option>
                                <option class="badge bg-label-warning" value="warning"
                                    <?php echo e(old('color') == 'warning' ? 'selected' : ''); ?>>
                                    <?= get_label('yellow', 'Yellow') ?></option>
                                <option class="badge bg-label-danger" value="danger"
                                    <?php echo e(old('color') == 'danger' ? 'selected' : ''); ?>><?= get_label('red', 'Red') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('delete_account_alert', 'Are you sure you want to delete your account?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <form id="formAccountDeactivation"
                    action="<?php echo e(route('profile.destroy', ['user' => getAuthenticatedUser()->id])); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger"><?= get_label('yes', 'Yes') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> '</button>
            </div>
            <div class="modal-body">
                <p><?= get_label('delete_alert', 'Are you sure you want to delete?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger"
                    id="confirmDelete"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmDeleteSelectedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> '</button>
            </div>
            <div class="modal-body">
                <p><?= get_label('delete_selected_alert', 'Are you sure you want to delete selected record(s)?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger"
                    id="confirmDeleteSelections"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="duplicateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('duplicate_warning', 'Are you sure you want to duplicate?') ?></p>
                <div id="titleDiv" class="d-none"><label
                        class="form-label"><?= get_label('update_title', 'Update Title') ?></label><input
                        type="text" class="form-control" id="updateTitle"
                        placeholder="<?= get_label('enter_title_duplicate', 'Enter Title For Item Being Duplicated') ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmDuplicate"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="timerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('time_tracker', 'Time tracker') ?>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <div class="stopwatch">
                        <div class="stopwatch_time">
                            <input type="text" name="hour" id="hour" value="00"
                                class="form-control stopwatch_time_input" readonly>
                            <div class="stopwatch_time_lable"><?= get_label('hours', 'Hours') ?></div>
                        </div>
                        <div class="stopwatch_time">
                            <input type="text" name="minute" id="minute" value="00"
                                class="form-control stopwatch_time_input" readonly>
                            <div class="stopwatch_time_lable"><?= get_label('minutes', 'Minutes') ?></div>
                        </div>
                        <div class="stopwatch_time">
                            <input type="text" name="second" id="second" value="00"
                                class="form-control stopwatch_time_input" readonly>
                            <div class="stopwatch_time_lable"><?= get_label('second', 'Second') ?></div>
                        </div>
                    </div>
                    <div class="selectgroup selectgroup-pills d-flex justify-content-around mt-3">
                        <label class="selectgroup-item">
                            <span class="selectgroup-button selectgroup-button-icon" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-original-title="<?= get_label('start', 'Start') ?>"
                                id="start" onclick="startTimer()"><i class="bx bx-play"></i></span>
                        </label>
                        <label class="selectgroup-item">
                            <span class="selectgroup-button selectgroup-button-icon" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-original-title="<?= get_label('stop', 'Stop') ?>"
                                id="end" onclick="stopTimer()"><i class="bx bx-stop"></i></span>
                        </label>
                        <label class="selectgroup-item">
                            <span class="selectgroup-button selectgroup-button-icon" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-original-title="<?= get_label('pause', 'Pause') ?>"
                                id="pause" onclick="pauseTimer()"><i class="bx bx-pause"></i></span>
                        </label>
                    </div>
                    <div class="form-group mb-0 mt-3">
                        <label class="label"><?= get_label('message', 'Message') ?>:</label>
                        <textarea class="form-control" id="time_tracker_message" placeholder="Please Enter Your Message" name="message"></textarea>
                    </div>
                </div>
                <?php if(getAuthenticatedUser()->can('manage_timesheet')): ?>
                    <div class="modal-footer justify-content-center">
                        <a href="<?php echo e(route('time_tracker.index')); ?>" class="btn btn-primary"><i
                                class="bx bxs-time"></i> <?= get_label('view_timesheet', 'View timesheet') ?></a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="stopTimerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> '</button>
            </div>
            <div class="modal-body">
                <p><?= get_label('stop_timer_alert', 'Are you sure you want to stop the timer?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger"
                    id="confirmStop"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<?php if(Request::is($prefix . '/estimates-invoices/create') ||
        preg_match('/^estimates-invoices\/edit\/\d+$/', Request::path())): ?>
    <div class="modal fade" id="edit-billing-address" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_billing_details', 'Update billing details') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        '</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('name', 'Name') ?> <span
                                    class="asterisk">*</span></label>
                            <input name="update_name" id="update_name" class="form-control"
                                placeholder="<?= get_label('please_enter_name', 'Please enter name') ?>"
                                value="<?php echo e($estimate_invoice->name ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('contact', 'Contact') ?> <span
                                    class="asterisk">*</span></label>
                            <input name="update_contact" id="update_contact" class="form-control"
                                placeholder="<?= get_label('please_enter_contact', 'Please enter contact') ?>"
                                value="<?php echo e($estimate_invoice->phone ?? ''); ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('address', 'Address') ?> <span
                                    class="asterisk">*</span></label>
                            <textarea class="form-control" placeholder="<?= get_label('please_enter_address', 'Please enter address') ?>"
                                name="update_address" id="update_address"><?php echo e($estimate_invoice->address ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('city', 'City') ?> <span
                                    class="asterisk">*</span></label>
                            <input name="update_city" id="update_city" class="form-control"
                                placeholder="<?= get_label('please_enter_city', 'Please enter city') ?>"
                                value="<?php echo e($estimate_invoice->city ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('state', 'State') ?> <span
                                    class="asterisk">*</span></label>
                            <input name="update_contact" id="update_state" class="form-control"
                                placeholder="<?= get_label('please_enter_state', 'Please enter state') ?>"
                                value="<?php echo e($estimate_invoice->city ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('country', 'Country') ?> <span
                                    class="asterisk">*</span></label>
                            <input name="update_country" id="update_country" class="form-control"
                                placeholder="<?= get_label('please_enter_country', 'Please enter country') ?>"
                                value="<?php echo e($estimate_invoice->country ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('zip_code', 'Zip code') ?> <span
                                    class="asterisk">*</span></label>
                            <input name="update_zip_code" id="update_zip_code" class="form-control"
                                placeholder="<?= get_label('please_enter_zip_code', 'Please enter zip code') ?>"
                                value="<?php echo e($estimate_invoice->zip_code ?? ''); ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="button" class="btn btn-primary"
                        id="apply_billing_details"><?= get_label('apply', 'Apply') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/expenses') || Request::is($prefix . '/expenses/*')): ?>
    <div class="modal fade" id="create_expense_type_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('expenses-type.store')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_expense_type', 'Create expense type') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_expense_type_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('expenses-type.update')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="update_expense_type_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_expense_type', 'Update expense type') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" class="form-control" name="title" id="expense_type_title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                        <div class="col mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control" name="description" id="expense_type_description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php if(Request::is($prefix . '/expenses')): ?>
        <div class="modal fade" id="create_expense_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content form-submit-event" action="<?php echo e(route('expenses.store')); ?>"
                    method="POST">
                    <input type="hidden" name="dnr">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">
                            <?= get_label('create_expense', 'Create expense') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="text" class="form-control" name="title"
                                    placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                            </div>
                            <div class="col mb-3">
                                <label class="form-label"><?= get_label('expense_type', 'Expense type') ?> <span
                                        class="asterisk">*</span></label>
                                <select class="form-select" name="expense_type_id">
                                    <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                    <?php if(isset($expense_types)): ?>
                                        <?php $__currentLoopData = $expense_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($type->id); ?>"><?php echo e($type->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label"><?= get_label('user', 'User') ?> <span
                                        class="asterisk">*</span></label>
                                <select class="form-select" name="user_id">
                                    <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                    <?php if(isset($users)): ?>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->id); ?>">
                                                <?php echo e($user->first_name . ' ' . $user->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col mb-3">
                                <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                        class="asterisk">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                    <input class="form-control" type="number" min="0" name="amount"
                                        placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                                </div>
                                <span class="text-danger error-message"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic"
                                    class="form-label"><?= get_label('expense_date', 'Expense date') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="text" id="expense_date" name="expense_date" class="form-control"
                                    placeholder="" autocomplete="off">
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('note', 'Note') ?></label>
                                <textarea class="form-control" name="note"
                                    placeholder="<?= get_label('please_enter_note_if_any', 'Please enter note if any') ?>"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                        <button type="submit" id="submit_btn"
                            class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="edit_expense_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content form-submit-event" action="<?php echo e(route('expenses.update')); ?>"
                    method="POST">
                    <input type="hidden" name="dnr">
                    <input type="hidden" id="update_expense_id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">
                            <?= get_label('update_expense', 'Update expense') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="text" class="form-control" id="expense_title" name="title"
                                    placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                            </div>
                            <div class="col mb-3">
                                <label class="form-label"><?= get_label('expense_type', 'Expense type') ?> <span
                                        class="asterisk">*</span></label>
                                <select class="form-select" id="expense_type_id" name="expense_type_id">
                                    <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                    <?php if(isset($expense_types)): ?>
                                        <?php $__currentLoopData = $expense_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($type->id); ?>"><?php echo e($type->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label"><?= get_label('user', 'User') ?> <span
                                        class="asterisk">*</span></label>
                                <select class="form-select" id="expense_user_id" name="user_id">
                                    <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                    <?php if(isset($users)): ?>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->id); ?>">
                                                <?php echo e($user->first_name . ' ' . $user->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col mb-3">
                                <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                        class="asterisk">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                    <input class="form-control" type="number" min="0"
                                        id="expense_amount" name="amount"
                                        placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                                </div>
                                <span class="text-danger error-message"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic"
                                    class="form-label"><?= get_label('expense_date', 'Expense date') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="text" id="update_expense_date" name="expense_date"
                                    class="form-control" placeholder="" autocomplete="off">
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label"><?= get_label('note', 'Note') ?></label>
                                <textarea class="form-control" id="expense_note" name="note"
                                    placeholder="<?= get_label('please_enter_note_if_any', 'Please enter note if any') ?>"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <?= get_label('close', 'Close') ?>
                        </button>
                        <button type="submit" id="submit_btn"
                            class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php if(Request::is($prefix . '/payments')): ?>
    <div class="modal fade" id="create_payment_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('payments.store')); ?>" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_payment', 'Create payment') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('user', 'User') ?></label>
                            <select class="form-select" name="user_id" id="select_user">

                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('invoice', 'Invoice') ?></label>
                            <select class="form-select" name="invoice_id" id="select_invoice">

                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('payment_method', 'Payment method') ?></label>
                            <select class="form-select" name="payment_method_id" id="select_payment_method">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <?php if(isset($payment_methods)): ?>
                                    <?php $__currentLoopData = $payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($pm->id); ?>"><?php echo e($pm->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input class="form-control" type="number" min="0" name="amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                            </div>
                            <span class="text-danger error-message"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('payment_date', 'Payment date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="payment_date" name="payment_date" class="form-control"
                                placeholder="<?php echo e(get_label('please_select', 'Please Select')); ?>"
                                autocomplete="off">
                        </div>
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note"
                                placeholder="<?= get_label('please_enter_note_if_any', 'Please enter note if any') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="edit_payment_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('payments.update')); ?>"
                method="POST">
                <input type="hidden" name="dnr">
                <input type="hidden" id="update_payment_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_payment', 'Update payment') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('user', 'User') ?></label>
                            <select class="form-select" name="user_id" id="payment_user_id">

                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('invoice', 'Invoice') ?></label>
                            <select class="form-select" name="invoice_id" id="payment_invoice_id">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <?php if(isset($invoices)): ?>
                                    <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($invoice->id); ?>">
                                            <?php echo e(get_label('invoice_id_prefix', 'INVC-') . $invoice->id); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label"><?= get_label('payment_method', 'Payment method') ?></label>
                            <select class="form-select" name="payment_method_id" id="payment_pm_id">
                                <option value=""><?= get_label('please_select', 'Please select') ?></option>
                                <?php if(isset($payment_methods)): ?>
                                    <?php $__currentLoopData = $payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($pm->id); ?>"><?php echo e($pm->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input class="form-control" type="number" min="0" name="amount"
                                    id="payment_amount"
                                    placeholder="<?= get_label('please_enter_amount', 'Please enter amount') ?>">
                            </div>
                            <span class="text-danger error-message"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic"
                                class="form-label"><?= get_label('payment_date', 'Payment date') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" name="payment_date" class="form-control"
                                id="update_payment_date" placeholder="" autocomplete="off">
                        </div>
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note" id="payment_note"
                                placeholder="<?= get_label('please_enter_note_if_any', 'Please enter note if any') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<div class="modal fade" id="mark_all_notifications_as_read_modal" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('mark_all_notifications_as_read_alert', 'Are you sure you want to mark all notifications as read?') ?>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmMarkAllAsRead"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="update_notification_status_modal" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('update_notifications_status_alert', 'Are you sure you want to update notification status?') ?>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmNotificationStatus"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="restore_default_modal" tabindex="-1"  aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('confirm_restore_default_template', 'Are you sure you want to restore default template?') ?>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirmRestoreDefault"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="sms_instuction_modal" tabindex="-1"  aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Sms Gateway Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <ul>
                        <li class="my-4">Read and follow instructions carefully while configuration sms gateway
                            setting </li>
                        <li class="my-4">Firstly open your sms gateway account . You can find api keys in your
                            account -> API keys & credentials -> create api key </li>
                        <li class="my-4">After create key you can see here Account sid and auth token </li>
                        <div class="simplelightbox-gallery">
                            <a href="<?php echo e(asset('storage/images/base_url_and_params.png')); ?>" target="_blank">
                                <img src="<?php echo e(asset('storage/images/base_url_and_params.png')); ?>" class="w-100">
                            </a>
                        </div>
                        <li class="my-4">For Base url Messaging -> Send an SMS</li>
                        <div class="simplelightbox-gallery">
                            <a href="<?php echo e(asset('storage/images/api_key_and_token.png')); ?>" target="_blank">
                                <img src="<?php echo e(asset('storage/images/api_key_and_token.png')); ?>" class="w-100">
                            </a>
                        </div>
                        <li class="my-4">check this for admin panel settings</li>
                        <div class="simplelightbox-gallery">
                            <a href="<?php echo e(asset('storage/images/sms_gateway_1.png')); ?>" target="_blank">
                                <img src="<?php echo e(asset('storage/images/sms_gateway_1.png')); ?>" class="w-100">
                            </a>
                        </div>
                        <div class="simplelightbox-gallery">
                            <a href="<?php echo e(asset('storage/images/sms_gateway_2.png')); ?>" target="_blank">
                                <img src="<?php echo e(asset('storage/images/sms_gateway_2.png')); ?>" class="w-100">
                            </a>
                        </div>
                        <li class="my-4"><b>Make sure you entered valid data as per instructions before proceed</b>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php if(Request::is($prefix . '/projects') ||
        Request::is($prefix . '/projects/*') ||
        Request::is($prefix . '/tasks') ||
        Request::is($prefix . '/tasks/*') ||
        Request::is($prefix . '/priority/manage')): ?>
    <div class="modal fade" id="create_priority_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content form-submit-event" action="<?php echo e(route('priority.store')); ?>" method="POST">
                <?php if(Request::is('priority/manage')): ?>
                    <input type="hidden" name="dnr">
                <?php endif; ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_priority', 'Create Priority') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="nameBasic" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select" id="color" name="color">
                                <option class="badge bg-label-primary" value="primary"
                                    <?php echo e(old('color') == 'primary' ? 'selected' : ''); ?>>
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary"
                                    <?php echo e(old('color') == 'secondary' ? 'selected' : ''); ?>>
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success"
                                    <?php echo e(old('color') == 'success' ? 'selected' : ''); ?>>
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger"
                                    <?php echo e(old('color') == 'danger' ? 'selected' : ''); ?>>
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning"
                                    <?php echo e(old('color') == 'warning' ? 'selected' : ''); ?>>
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"
                                    <?php echo e(old('color') == 'info' ? 'selected' : ''); ?>><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"
                                    <?php echo e(old('color') == 'dark' ? 'selected' : ''); ?>><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/priority/manage')): ?>
    <div class="modal fade" id="edit_priority_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?php echo e(route('priority.update')); ?>" class="modal-content form-submit-event"
                method="POST">
                <input type="hidden" name="id" id="priority_id">
                <input type="hidden" name="dnr">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_priority', 'Update Priority') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="priority_title" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span
                                    class="asterisk">*</span></label>
                            <select class="form-select" id="priority_color" name="color" required>
                                <option class="badge bg-label-primary" value="primary">
                                    <?= get_label('primary', 'Primary') ?>
                                </option>
                                <option class="badge bg-label-secondary" value="secondary">
                                    <?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge bg-label-success" value="success">
                                    <?= get_label('success', 'Success') ?></option>
                                <option class="badge bg-label-danger" value="danger">
                                    <?= get_label('danger', 'Danger') ?></option>
                                <option class="badge bg-label-warning" value="warning">
                                    <?= get_label('warning', 'Warning') ?></option>
                                <option class="badge bg-label-info" value="info"><?= get_label('info', 'Info') ?>
                                </option>
                                <option class="badge bg-label-dark" value="dark"><?= get_label('dark', 'Dark') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/projects') || Request::is($prefix . '/projects/list') || Request::is($prefix . '/projects/kanban-view')): ?>
    <div class="modal fade" id="create_project_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="<?php echo e(route('projects.store')); ?>" class="form-submit-event modal-content" method="POST">
                <?php if(!Request::is('projects')): ?>
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="table" value="projects_table">
                <?php endif; ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('create_project', 'Create Project') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="<?php echo e(old('title')); ?>">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="status"><?= get_label('status', 'Status') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group">
                                <select class="form-control statusDropdown" name="status_id">
                                    <?php if(isset($statuses)): ?>
                                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(canSetStatus($status)): ?>
                                                <option value="<?php echo e($status->id); ?>"
                                                    data-color="<?php echo e($status->color); ?>"
                                                    <?php echo e(old('status') == $status->id ? 'selected' : ''); ?>>
                                                    <?php echo e($status->title); ?> (<?php echo e($status->color); ?>)</option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateStatusModal"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_status', 'Create status') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="<?php echo e(route('status.index')); ?>" target="_blank"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_statuses', 'Manage statuses') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                            <?php $__errorArgs = ['status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= get_label('priority', 'Priority') ?></label>
                            <div class="input-group">
                                <select class="form-select" name="priority_id">
                                    <?php if(isset($priorities)): ?>
                                        <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($priority->id); ?>"
                                                class="badge bg-label-<?php echo e($priority->color); ?>"
                                                <?php echo e(old('priority') == $priority->id ? 'selected' : ''); ?>>
                                                <?php echo e($priority->title); ?> (<?php echo e($priority->color); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreatePriorityModal"><button
                                        type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_priority', 'Create Priority') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="<?php echo e(route('priority.manage')); ?>" target="_blank"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_priorities', 'Manage Priorities') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                            <?php $__errorArgs = ['priority_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="budget" class="form-label"><?= get_label('budget', 'Budget') ?></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input class="form-control min-0" min="0" type="number" id="budget"
                                    name="budget"
                                    placeholder="<?= get_label('please_enter_budget', 'Please enter budget') ?>"
                                    value="<?php echo e(old('budget')); ?>">
                            </div>
                            <span class="text-danger error-message"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="start_date"><?= get_label('starts_at', 'Starts at') ?>
                                <span class="asterisk">*</span></label>
                            <input type="text" id="start_date" name="start_date" class="form-control"
                                value=""autocomplete="off">
                            <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="due_date"><?= get_label('ends_at', 'Ends at') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="end_date" name="end_date" class="form-control"
                                value="" autocomplete="off">
                            <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="">
                                <?= get_label('task_accessibility', 'Task Accessibility') ?>
                                <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                    data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true"
                                    title=""
                                    data-bs-original-title="<b><?php echo e(get_label('assigned_users', 'Assigned Users')); ?>:</b> <?php echo e(get_label('assigned_users_info', 'You Will Need to Manually Select Task Users When Creating Tasks Under This Project.')); ?> <br><b><?php echo e(get_label('project_users', 'Project Users')); ?>:</b> <?php echo e(get_label('project_users_info', 'When Creating Tasks Under This Project, the Task Users Selection Will Be Automatically Filled With Project Users.')); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="top"></i>
                            </label>
                            <div class="input-group">
                                <select class="form-select" name="task_accessibility">
                                    <option value="assigned_users">
                                        <?= get_label('assigned_users', 'Assigned Users') ?></option>
                                    <option value="project_users"><?= get_label('project_users', 'Project Users') ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_users', 'Select users') ?></label>
                            <div class="input-group">
                                <select class="form-control js-example-basic-multiple" id="project_users" name="user_id[]"
                                    multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    <?php if(isset($toSelectProjectUsers)): ?>
                                        <?php $__currentLoopData = $toSelectProjectUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $selected = $user->id == getAuthenticatedUser()->id ? 'selected' : ''; ?>
                                            <option value="<?php echo e($user->id); ?>"
                                                <?php echo e(collect(old('user_id'))->contains($user->id) ? 'selected' : ''); ?>

                                                <?= $selected ?>><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="client_id"><?= get_label('select_clients', 'Select clients') ?></label>
                            <div class="input-group">
                                <select class="form-control js-example-basic-multiple" name="client_id[]"
                                    multiple="multiple" id="project_clients"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    <?php if(isset($toSelectProjectClients)): ?>
                                        <?php $__currentLoopData = $toSelectProjectClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $selected = $client->id == getAuthenticatedUser()->id && $auth_user->hasRole('client') ? 'selected' : ''; ?>
                                            <option value="<?php echo e($client->id); ?>"
                                                <?php echo e(collect(old('client_id'))->contains($client->id) ? 'selected' : ''); ?>

                                                <?= $selected ?>><?php echo e($client->first_name); ?> <?php echo e($client->last_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label"
                                for=""><?= get_label('select_tags', 'Select tags') ?></label>
                            <div class="input-group">
                                <select class="form-control tagsDropdown" name="tag_ids[]" multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    <?php if(isset($tags)): ?>
                                        <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($tag->id); ?>" data-color="<?php echo e($tag->color); ?>"
                                                <?php echo e(collect(old('tag_ids'))->contains($tag->id) ? 'selected' : ''); ?>>
                                                <?php echo e($tag->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateTagModal"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_tag', 'Create tag') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="<?php echo e(route('tags.index')); ?>"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_tags', 'Manage tags') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" rows="5" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note" rows="3"
                                placeholder="<?= get_label('optional_note', 'Optional Note') ?>"></textarea>
                        </div>
                    </div>
                    <div class="alert alert-primary" role="alert">
                        <?= get_label('you_will_be_project_participant_automatically', 'You will be project participant automatically.') ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/projects') ||Request::is($prefix . '/projects/favorite')||
        Request::is($prefix . '/projects/list') ||
        Request::is($prefix . '/projects/information/*') ||
        Request::is($prefix . '/home') ||
        Request::is($prefix . '/users/profile/*') ||
        Request::is($prefix . '/clients/profile/*') ||
        Request::is($prefix . '/users') ||
        Request::is($prefix . '/clients')): ?>
    <div class="modal fade" id="edit_project_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="<?php echo e(route('projects.update')); ?>" class="form-submit-event modal-content"
                method="POST">
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="id" id="project_id">
                <?php if(!Request::is($prefix.'/projects') && !Request::is($prefix.'/projects/information/*')): ?>
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="table" value="projects_table">
                <?php endif; ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        <?= get_label('update_project', 'Update Project') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="title" id="project_title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="<?php echo e(old('title')); ?>">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="status"><?= get_label('status', 'Status') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group">
                                <select class="form-control statusDropdown" name="status_id"
                                    id="project_status_id">
                                    <?php if(isset($statuses)): ?>
                                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($status->id); ?>" data-color="<?php echo e($status->color); ?>"
                                                <?php echo e(old('status') == $status->id ? 'selected' : ''); ?>>
                                                <?php echo e($status->title); ?> (<?php echo e($status->color); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <?php $__errorArgs = ['status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateStatusModal"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_status', 'Create status') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="<?php echo e(route('status.index')); ?>" target="_blank"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_statuses', 'Manage statuses') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= get_label('priority', 'Priority') ?></label>
                            <div class="input-group">
                                <select class="form-select" name="priority_id" id="project_priority_id">
                                    <?php if(isset($priorities)): ?>
                                        <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($priority->id); ?>"
                                                class="badge bg-label-<?php echo e($priority->color); ?>"
                                                <?php echo e(old('priority') == $priority->id ? 'selected' : ''); ?>>
                                                <?php echo e($priority->title); ?> (<?php echo e($priority->color); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreatePriorityModal"><button
                                        type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_priority', 'Create Priority') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="<?php echo e(route('priority.manage')); ?>" target="_blank"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_priorities', 'Manage Priorities') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                            <?php $__errorArgs = ['priority_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="budget" class="form-label"><?= get_label('budget', 'Budget') ?></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><?php echo e($general_settings['currency_symbol']); ?></span>
                                <input class="form-control" type="text" id="project_budget" name="budget"
                                    placeholder="<?= get_label('please_enter_budget', 'Please enter budget') ?>"
                                    value="<?php echo e(old('budget')); ?>">
                            </div>
                            <span class="text-danger error-message"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="start_date"><?= get_label('starts_at', 'Starts at') ?>
                                <span class="asterisk">*</span></label>
                            <input type="text" id="update_start_date" name="start_date" class="form-control"
                                value="" autocomplete="off">
                            <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="due_date"><?= get_label('ends_at', 'Ends at') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="update_end_date" name="end_date" class="form-control"
                                value="" autocomplete="off">
                            <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="">
                                <?= get_label('task_accessibility', 'Task Accessibility') ?>
                                <i class='bx bx-info-circle text-primary' data-bs-toggle="tooltip"
                                    data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true"
                                    title=""
                                    data-bs-original-title="<b><?php echo e(get_label('assigned_users', 'Assigned Users')); ?>:</b> <?php echo e(get_label('assigned_users_info', 'You Will Need to Manually Select Task Users When Creating Tasks Under This Project.')); ?><br><b><?php echo e(get_label('project_users', 'Project Users')); ?>:</b> <?php echo e(get_label('project_users_info', 'When Creating Tasks Under This Project, the Task Users Selection Will Be Automatically Filled With Project Users.')); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="top"></i>
                            </label>
                            <div class="input-group">
                                <select class="form-select" name="task_accessibility" id="task_accessibility">
                                    <option value="assigned_users">
                                        <?= get_label('assigned_users', 'Assigned Users') ?></option>
                                    <option value="project_users"><?= get_label('project_users', 'Project Users') ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_users', 'Select users') ?></label>
                            <div class="input-group">
                                <select class="form-control js-example-basic-multiple" name="user_id[]"
                                    multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    <?php if(isset($toSelectProjectUsers)): ?>
                                        <?php $__currentLoopData = $toSelectProjectUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $selected = $user->id == getAuthenticatedUser()->id ? 'selected' : ''; ?>
                                            <option value="<?php echo e($user->id); ?>"
                                                <?php echo e(collect(old('user_id'))->contains($user->id) ? 'selected' : ''); ?>

                                                <?= $selected ?>><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="client_id"><?= get_label('select_clients', 'Select clients') ?></label>
                            <div class="input-group">
                                <select class="form-control js-example-basic-multiple" name="client_id[]"
                                    multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    <?php if(isset($toSelectProjectClients)): ?>
                                        <?php $__currentLoopData = $toSelectProjectClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $selected = $client->id == getAuthenticatedUser()->id && $auth_user->hasRole('client') ? 'selected' : ''; ?>
                                            <option value="<?php echo e($client->id); ?>"
                                                <?php echo e(collect(old('client_id'))->contains($client->id) ? 'selected' : ''); ?>

                                                <?= $selected ?>><?php echo e($client->first_name); ?> <?php echo e($client->last_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label"
                                for=""><?= get_label('select_tags', 'Select tags') ?></label>
                            <div class="input-group">
                                <select class="form-control tagsDropdown" name="tag_ids[]" multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    <?php if(isset($tags)): ?>
                                        <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($tag->id); ?>" data-color="<?php echo e($tag->color); ?>"
                                                <?php echo e(collect(old('tag_ids'))->contains($tag->id) ? 'selected' : ''); ?>>
                                                <?php echo e($tag->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateTagModal"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_tag', 'Create tag') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="<?php echo e(route('tags.index')); ?>"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_tags', 'Manage tags') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" rows="5" name="description" id="project_description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note" id="projectNote" rows="3"
                                placeholder="<?= get_label('optional_note', 'Optional Note') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<div class="modal fade" id="set_default_view_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('set_default_view_alert', 'Are You Want to Set as Default View?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirm"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>
<?php if(Request::is($prefix . '/tasks') ||
        Request::is($prefix . '/tasks/draggable') ||
        Request::is($prefix . '/projects/information/*') ||
        Request::is($prefix . '/projects/tasks/draggable/*') ||
        Request::is($prefix . '/projects/tasks/list/*')
        || Request::is($prefix . '/tasks/calendar-view')): ?>
    <div class="modal fade" id="create_task_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="<?php echo e(route('tasks.store')); ?>" class="form-submit-event modal-content" method="POST">
                <?php if(
                    !Request::is($prefix . '/projects/tasks/draggable/*') &&
                        !Request::is('tasks/draggable') &&
                        !Request::is('projects/information/*')): ?>
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="table" value="task_table">
                <?php endif; ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_task', 'Create Task') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="<?php echo e(old('title')); ?>">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="status"><?= get_label('status', 'Status') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group">
                                <select class="form-select statusDropdown" name="status_id">
                                    <?php if(isset($statuses)): ?>
                                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(canSetStatus($status)): ?>
                                                <option value="<?php echo e($status->id); ?>"
                                                    data-color="<?php echo e($status->color); ?>"
                                                    <?php echo e(old('status') == $status->id ? 'selected' : ''); ?>>
                                                    <?php echo e($status->title); ?> (<?php echo e($status->color); ?>)</option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateStatusModal"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_status', 'Create status') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="<?php echo e(route('status.index')); ?>" target="_blank"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_statuses', 'Manage statuses') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                            <?php $__errorArgs = ['status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= get_label('priority', 'Priority') ?></label>
                            <div class="input-group">
                                <select class="form-select" name="priority_id">
                                    <?php if(isset($priorities)): ?>
                                        <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($priority->id); ?>"
                                                class="badge bg-label-<?php echo e($priority->color); ?>"
                                                <?php echo e(old('priority') == $priority->id ? 'selected' : ''); ?>>
                                                <?php echo e($priority->title); ?> (<?php echo e($priority->color); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mt-2">
                                <a href="javascript:void(0);"class="openCreatePriorityModal"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_priority', 'Create Priority') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="<?php echo e(route('priority.manage')); ?>" target="_blank"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_priorities', 'Manage Priorities') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                            <?php $__errorArgs = ['priority_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="start_date"><?= get_label('starts_at', 'Starts at') ?>
                                <span class="asterisk">*</span></label>
                            <input type="text" id="task_start_date" name="start_date" class="form-control"
                                value="">
                            <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="due_date"><?= get_label('ends_at', 'Ends at') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="task_end_date" name="due_date" class="form-control"
                                value="">
                            <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <?php $project_id = 0;
                    if (!isset($project->id)) {
                    ?>
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_project', 'Select project') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group">
                                <select class="form-control selectTaskProject" name="project"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    <option value=""></option>
                                    <?php if(isset($projects)): ?>
                                        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($project->id); ?>"
                                                <?php echo e(old('project') == $project->id ? 'selected' : ''); ?>>
                                                <?php echo e($project->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <?php } else {
                        $project_id = $project->id ?>
                        <input type="hidden" name="project" value="<?php echo e($project_id); ?>">
                        <div class="mb-3">
                            <label for="project_title" class="form-label"><?= get_label('project', 'Project') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="text" value="<?php echo e($project->title); ?>" readonly>
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="row" id="selectTaskUsers">
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_users', 'Select users') ?> <span
                                    id="users_associated_with_project"></span><?php if (!empty($project_id)) { ?>
                                (<?= get_label('users_associated_with_project', 'Users associated with project') ?>
                                <b><?php echo e($project->title); ?></b>)
                                <?php } ?></label>
                            <div class="input-group">
                                <select class="form-control js-example-basic-multiple" name="users_id[]"
                                    multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    <?php if (isset($project_id) && !empty($project_id)) { ?>
                                    <?php $__currentLoopData = $toSelectTaskUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                        $selected = '';
                                        // Check if task_accessibility is 'project_users' or if the user is the authenticated user
                                        if ($project->task_accessibility == 'project_users' || $user->id == getAuthenticatedUser()->id) {
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="<?php echo e($user->id); ?>"
                                            <?php echo e(collect(old('user_id'))->contains($user->id) ? 'selected' : ''); ?>

                                            <?= $selected ?>><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" rows="5" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note" rows="3"
                                placeholder="<?= get_label('optional_note', 'Optional Note') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/tasks') ||
        Request::is($prefix . '/tasks/draggable') ||
        Request::is($prefix . '/projects/tasks/draggable/*') ||
        Request::is($prefix . '/projects/tasks/list/*') ||
        Request::is($prefix . '/tasks/information/*') ||
        Request::is($prefix . '/home') ||
        Request::is($prefix . '/users/profile/*') ||
        Request::is($prefix . '/clients/profile/*') ||
        Request::is($prefix . '/projects/information/*') ||
        Request::is($prefix . '/users') ||
        Request::is($prefix . '/clients')): ?>
    <div class="modal fade" id="edit_task_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="<?php echo e(route('tasks.update')); ?>" class="form-submit-event modal-content" method="POST">
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="id" id="id">
                <?php if(
                    !Request::is($prefix . '/projects/tasks/draggable/*') &&
                        !Request::is($prefix . '/tasks/draggable') &&
                        !Request::is($prefix . '/tasks/information/*')): ?>
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="table" value="task_table">
                <?php endif; ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_task', 'Update Task') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" id="title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="<?php echo e(old('title')); ?>">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="status"><?= get_label('status', 'Status') ?> <span
                                    class="asterisk">*</span></label>
                            <div class="input-group">
                                <select class="form-select statusDropdown" name="status_id" id="task_status_id">
                                    <?php if(isset($statuses)): ?>
                                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($status->id); ?>" data-color="<?php echo e($status->color); ?>"
                                                <?php echo e(old('status') == $status->id ? 'selected' : ''); ?>>
                                                <?php echo e($status->title); ?> (<?php echo e($status->color); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreateStatusModal"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_status', 'Create status') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="<?php echo e(route('status.index')); ?>" target="_blank"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_statuses', 'Manage statuses') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                            <?php $__errorArgs = ['status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= get_label('priority', 'Priority') ?></label>
                            <div class="input-group">
                                <select class="form-select" name="priority_id" id="priority_id">
                                    <?php if(isset($priorities)): ?>
                                        <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($priority->id); ?>"
                                                class="badge bg-label-<?php echo e($priority->color); ?>"
                                                <?php echo e(old('priority') == $priority->id ? 'selected' : ''); ?>>
                                                <?php echo e($priority->title); ?> (<?php echo e($priority->color); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mt-2">
                                <a href="javascript:void(0);" class="openCreatePriorityModal"><button
                                        type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('create_priority', 'Create Priority') ?>"><i
                                            class="bx bx-plus"></i></button></a>
                                <a href="<?php echo e(route('priority.manage')); ?>" target="_blank"><button type="button"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title="<?= get_label('manage_priorities', 'Manage Priorities') ?>"><i
                                            class="bx bx-list-ul"></i></button></a>
                            </div>
                            <?php $__errorArgs = ['priority_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="start_date"><?= get_label('starts_at', 'Starts at') ?>
                                <span class="asterisk">*</span></label>
                            <input type="text" id="update_start_date" name="start_date" class="form-control"
                                value="">
                            <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="due_date"><?= get_label('ends_at', 'Ends at') ?> <span
                                    class="asterisk">*</span></label>
                            <input type="text" id="update_end_date" name="due_date" class="form-control"
                                value="">
                            <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="project_title" class="form-label"><?= get_label('project', 'Project') ?>
                                <span class="asterisk">*</span></label>
                            <input class="form-control" type="text" id="update_project_title" readonly>
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"
                                for="user_id"><?= get_label('select_users', 'Select users') ?> <span
                                    id="task_update_users_associated_with_project"></span></label>
                            <div class="input-group">
                                <select class="form-control js-example-basic-multiple" name="user_id[]"
                                    multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="description"
                                class="form-label"><?= get_label('description', 'Description') ?></label>
                            <textarea class="form-control description" id="task_description" rows="5" name="description"
                                placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label"><?= get_label('note', 'Note') ?></label>
                            <textarea class="form-control" name="note" rows="3" id="taskNote"
                                placeholder="<?= get_label('optional_note', 'Optional Note') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix . '/projects/list') ||
        Request::is($prefix . '/home') ||
        Request::is($prefix . '/projects') ||
        Request::is($prefix . '/users/profile/*') ||
        Request::is($prefix . '/clients/profile/*') ||
        Request::is($prefix . '/tasks') ||
        Request::is($prefix . '/tasks/draggable') ||
        Request::is($prefix . '/projects/information/*')): ?>
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><span id="typePlaceholder"></span>
                        <?= get_label('quick_view', 'Quick View') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 id="quickViewTitlePlaceholder" class="text-muted"></h5>
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs" role="tablist">
                            <?php if($auth_user->can('manage_users')): ?>
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab"
                                        data-bs-toggle="tab" data-bs-target="#navs-top-quick-view-users"
                                        aria-controls="navs-top-quick-view-users">
                                        <i
                                            class="menu-icon tf-icons bx bx-group text-primary"></i><?= get_label('users', 'Users') ?>
                                    </button>
                                </li>
                            <?php endif; ?>
                            <?php if($auth_user->can('manage_clients')): ?>
                                <li class="nav-item">
                                    <button type="button"
                                        class="nav-link <?php echo e(!$auth_user->can('manage_users') ? 'active' : ''); ?>"
                                        role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-top-quick-view-clients"
                                        aria-controls="navs-top-quick-view-clients">
                                        <i
                                            class="menu-icon tf-icons bx bx-group text-warning"></i><?= get_label('clients', 'Clients') ?>
                                    </button>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <button type="button"
                                    class="nav-link <?php echo e(!$auth_user->can('manage_users') && !$auth_user->can('manage_clients') ? 'active' : ''); ?>"
                                    role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-top-quick-view-description"
                                    aria-controls="navs-top-quick-view-description">
                                    <i
                                        class="menu-icon tf-icons bx bx-notepad text-success"></i><?= get_label('description', 'Description') ?>
                                </button>
                            </li>
                        </ul>
                        <input type="hidden" id="type">
                        <input type="hidden" id="typeId">
                        <div class="tab-content">
                            <?php if($auth_user->can('manage_users')): ?>
                                <div class="tab-pane fade active show" id="navs-top-quick-view-users"
                                    role="tabpanel">
                                    <div class="table-responsive text-nowrap">
                                        <!-- <input type="hidden" id="data_type" value="users">
                                <input type="hidden" id="data_table" value="usersTable"> -->
                                        <table id="usersTable" data-toggle="table"
                                            data-loading-template="loadingTemplate"
                                            data-url="/master-panel/users/list" data-icons-prefix="bx"
                                            data-icons="icons" data-show-refresh="true" data-total-field="total"
                                            data-trim-on-search="false" data-data-field="rows"
                                            data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                            data-side-pagination="server" data-show-columns="true"
                                            data-pagination="true" data-sort-name="id" data-sort-order="desc"
                                            data-mobile-responsive="true"
                                            data-query-params="queryParamsUsersClients">
                                            <thead>
                                                <tr>
                                                    <th data-checkbox="true"></th>
                                                    <th data-sortable="true" data-field="id">
                                                        <?= get_label('id', 'ID') ?></th>
                                                    <th data-formatter="userFormatter" data-sortable="true"
                                                        data-field="first_name"><?= get_label('users', 'Users') ?>
                                                    </th>
                                                    <th data-field="role"><?= get_label('role', 'Role') ?></th>
                                                    <th data-field="phone" data-sortable="true"
                                                        data-visible="false">
                                                        <?= get_label('phone_number', 'Phone number') ?></th>
                                                    <th data-field="assigned"><?= get_label('assigned', 'Assigned') ?>
                                                    </th>
                                                    <th data-sortable="true" data-field="created_at"
                                                        data-visible="false">
                                                        <?= get_label('created_at', 'Created at') ?></th>
                                                    <th data-sortable="true" data-field="updated_at"
                                                        data-visible="false">
                                                        <?= get_label('updated_at', 'Updated at') ?></th>
                                                    
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if($auth_user->can('manage_clients')): ?>
                                <div class="tab-pane fade <?php echo e(!$auth_user->can('manage_users') ? 'active show' : ''); ?>"
                                    id="navs-top-quick-view-clients" role="tabpanel">
                                    <div class="table-responsive text-nowrap">
                                        <!-- <input type="hidden" id="data_type" value="clients">
                            <input type="hidden" id="data_table" value="clientsTable"> -->
                                        <table id="clientsTable" data-toggle="table"
                                            data-loading-template="loadingTemplate"
                                            data-url="/master-panel/clients/list" data-icons-prefix="bx"
                                            data-icons="icons" data-show-refresh="true" data-total-field="total"
                                            data-trim-on-search="false" data-data-field="rows"
                                            data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                            data-side-pagination="server" data-show-columns="true"
                                            data-pagination="true" data-sort-name="id" data-sort-order="desc"
                                            data-mobile-responsive="true"
                                            data-query-params="queryParamsUsersClients">
                                            <thead>
                                                <tr>
                                                    <th data-checkbox="true"></th>
                                                    <th data-sortable="true" data-field="id">
                                                        <?= get_label('id', 'ID') ?></th>
                                                    <th data-formatter="clientFormatter" data-sortable="true">
                                                        <?= get_label('client', 'Client') ?></th>
                                                    <th data-field="company" data-sortable="true"
                                                        data-visible="false"><?= get_label('company', 'Company') ?>
                                                    </th>
                                                    <th data-field="phone" data-sortable="true"
                                                        data-visible="false">
                                                        <?= get_label('phone_number', 'Phone number') ?></th>
                                                    <th data-field="assigned"><?= get_label('assigned', 'Assigned') ?>
                                                    </th>
                                                    <th data-sortable="true" data-field="created_at"
                                                        data-visible="false">
                                                        <?= get_label('created_at', 'Created at') ?></th>
                                                    <th data-sortable="true" data-field="updated_at"
                                                        data-visible="false">
                                                        <?= get_label('updated_at', 'Updated at') ?></th>
                                                    
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="tab-pane fade <?php echo e(!$auth_user->can('manage_users') && !$auth_user->can('manage_clients') ? 'active show' : ''); ?>"
                                id="navs-top-quick-view-description" role="tabpanel">
                                <p class="pt-3" id="quickViewDescPlaceholder"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="modal fade" id="confirmSaveColumnVisibility" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('save_column_visibility_alert', 'Are You Want to Save Column Visibility?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="confirm"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createWorkspaceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(get_label('create_workspace', 'Create Workspace')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('workspaces.store')); ?>" class="form-submit-event" method="POST">
                <input type="hidden" name="dnr">
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" id="title" name="title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="<?php echo e(old('title')); ?>">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <select id="" class="form-control js-example-basic-multiple"
                                name="user_ids[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                <?php if(isset($admin)): ?>
                                    <?php $__currentLoopData = $admin->teamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teamMember): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($teamMember->user->id); ?>"
                                            <?php echo e($teamMember->user->id == auth()->id() ? 'selected' : ''); ?>>
                                            <?php echo e($teamMember->user->first_name); ?> <?php echo e($teamMember->user->last_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($admin->user->id); ?>"
                                        <?php echo e($admin->user->id == auth()->id() ? 'selected' : ''); ?>>
                                        <?php echo e($admin->user->first_name); ?> <?php echo e($admin->user->last_name); ?>

                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <select id="" class="form-control js-example-basic-multiple"
                                name="client_ids[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $selected = $client->id == getAuthenticatedUser()->id && $auth_user->hasRole('client') ? 'selected' : ''; ?>
                                    <option value="<?php echo e($client->id); ?>"
                                        <?php echo e(collect(old('client_ids'))->contains($client->id) ? 'selected' : ''); ?>

                                        <?= $selected ?>><?php echo e($client->first_name); ?> <?php echo e($client->last_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <?php if(isAdminOrHasAllDataAccess()): ?>
                        <div class="row">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <label class="form-check-label" for="primaryWorkspace">
                                        <input class="form-check-input" type="checkbox" name="primaryWorkspace"
                                            id="primaryWorkspace">
                                        <?= get_label('primary_workspace', 'Primary Workspace') ?>?
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="alert alert-primary alert-dismissible" role="alert">
                        <?= get_label('you_will_be_workspace_participant_automatically', 'You will be workspace participant automatically.') ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editWorkspaceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(get_label('update_workspace', 'Update Workspace')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('workspaces.update')); ?>" class="form-submit-event" method="POST">
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="id" id="workspace_id">
                <input type="hidden" name="dnr">
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                    class="asterisk">*</span></label>
                            <input class="form-control" type="text" name="title" id="workspace_title"
                                placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>"
                                value="<?php echo e(old('title')); ?>">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <select id="" class="form-control js-example-basic-multiple"
                                name="user_ids[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                <?php if(isset($admin)): ?>
                                    <?php
                                    $workspace_users = $workspace ? $workspace->users : collect();
                                    ?>
                                    <?php $__currentLoopData = $admin->teamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teamMember): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(isset($workspace_users)): ?>
                                            <option value="<?php echo e($teamMember->user->id); ?>"
                                                <?php echo e($workspace_users->contains($teamMember->user) ? 'selected' : ''); ?>>
                                                <?php echo e($teamMember->user->first_name); ?> <?php echo e($teamMember->user->last_name); ?>

                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($admin->user->id); ?>"
                                        <?php echo e($workspace_users->contains($admin->user) ? 'selected' : ''); ?>>
                                        <?php echo e($admin->user->first_name); ?> <?php echo e($admin->user->last_name); ?>

                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <select id="" class="form-control js-example-basic-multiple"
                                name="client_ids[]" multiple="multiple"
                                data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                <?php if(isset($admin)): ?>
                                    <?php
                                    $workspace_clients = $workspace ? $workspace->clients : collect();
                                    ?>
                                    <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($client->id); ?>"
                                            <?php echo e($workspace_clients->contains($client) ? 'selected' : ''); ?>>
                                            <?php echo e($client->first_name); ?> <?php echo e($client->last_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <?php if(isAdminOrHasAllDataAccess()): ?>
                        <div class="row">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <label class="form-check-label" for="updatePrimaryWorkspace">
                                        <input class="form-check-input" type="checkbox" name="primaryWorkspace"
                                            id="updatePrimaryWorkspace">
                                        <?= get_label('primary_workspace', 'Primary Workspace') ?>?
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                    <button type="submit" id="submit_btn"
                        class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if(Request::is($prefix . '/meetings')): ?>
    <div class="modal fade" id="createMeetingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(get_label('create_meeting', 'Create Meeting')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('meetings.store')); ?>" class="form-submit-event" method="POST">
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="table" value="meetings_table">
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                        class="asterisk">*</span></label>
                                <input class="form-control" type="text" name="title"
                                    placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>">
                                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for=""><?= get_label('starts_at', 'Starts at') ?>
                                    <span class="asterisk">*</span></label>
                                <input type="text" id="start_date" name="start_date" class="form-control"
                                    value="">
                                <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for=""><?= get_label('time', 'Time') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="time" name="start_time" class="form-control">
                                <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="end_date_time"><?= get_label('ends_at', 'Ends at') ?>
                                    <span class="asterisk">*</span></label>
                                <input type="text" id="end_date" name="end_date" class="form-control"
                                    value="" autocomplete="off">
                                <?php $__errorArgs = ['end_date_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for=""><?= get_label('time', 'Time') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="time" name="end_time" class="form-control">
                                <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label"
                                    for="user_id"><?= get_label('selec_users', 'Select users') ?></label>
                                <select id="" class="form-control js-example-basic-multiple"
                                    name="user_ids[]" multiple="multiple"
                                    data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $selected = $user->id == getAuthenticatedUser()->id ? 'selected' : ''; ?>
                                        <option value="<?php echo e($user->id); ?>"
                                            <?php echo e(collect(old('user_ids'))->contains($user->id) ? 'selected' : ''); ?>

                                            <?= $selected ?>><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <?php echo $__env->make('partials.select', [
                                    'label' => get_label('select_clients', 'Select clients'),
                                    'name' => 'client_ids[]',
                                    'items' => $clients ?? [],
                                    'authUserId' => $auth_user->id,
                                    'for' => 'clients',
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                        <div class="alert alert-primary alert-dismissible" role="alert">
                            <?= get_label('you_will_be_meeting_participant_automatically', 'You will be meeting participant automatically.') ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                        <button type="submit" id="submit_btn"
                            class="btn btn-primary me-2"><?= get_label('create', 'Create') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editMeetingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(get_label('update_meeting', 'Update Meeting')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('meetings.update')); ?>" class="form-submit-event" method="POST">
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="dnr">
                    <input type="hidden" name="id" id="meeting_id">
                    <input type="hidden" name="table" value="meetings_table">
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span
                                        class="asterisk">*</span></label>
                                <input class="form-control" type="text" id="meeting_title" name="title"
                                    placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>">
                                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for=""><?= get_label('starts_at', 'Starts at') ?>
                                    <span class="asterisk">*</span></label>
                                <input type="text" id="update_start_date" name="start_date"
                                    class="form-control" value="">
                                <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for=""><?= get_label('time', 'Time') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="time" id="meeting_start_time" name="start_time"
                                    class="form-control">
                                <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="end_date_time"><?= get_label('ends_at', 'Ends at') ?>
                                    <span class="asterisk">*</span></label>
                                <input type="text" id="update_end_date" name="end_date" class="form-control"
                                    value="">
                                <?php $__errorArgs = ['end_date_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for=""><?= get_label('time', 'Time') ?> <span
                                        class="asterisk">*</span></label>
                                <input type="time" id="meeting_end_time" name="end_time"
                                    class="form-control">
                                <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <?php echo $__env->make('partials.select', [
                                    'label' => get_label('select_users', 'Select users'),
                                    'name' => 'user_ids[]',
                                    'items' => $users ?? [],
                                    'authUserId' => $auth_user->id,
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <?php echo $__env->make('partials.select', [
                                    'label' => get_label('select_clients', 'Select clients'),
                                    'name' => 'client_ids[]',
                                    'items' => $clients ?? [],
                                    'authUserId' => $auth_user->id,
                                    'for' => 'clients',
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                        <div class="alert alert-primary alert-dismissible" role="alert">
                            <?= get_label('you_will_be_meeting_participant_automatically', 'You will be meeting participant automatically.') ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                        <button type="submit" id="submit_btn"
                            class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(Request::is($prefix . '/users') ||
        Request::is($prefix . '/clients') ||
        Request::is($prefix . '/projects/list') ||
        Request::is($prefix . '/projects') ||
        Request::is($prefix . '/tasks') ||
        Request::is($prefix . '/tasks/draggable')): ?>
    <div class="modal fade" id="viewAssignedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1"><span id="userPlaceholder"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab"
                                    data-bs-toggle="tab" data-bs-target="#navs-top-view-assigned-projects"
                                    aria-controls="navs-top-view-assigned-projects">
                                    <i
                                        class="menu-icon tf-icons bx bx-briefcase-alt-2 text-success"></i><?= get_label('projects', 'Projects') ?>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-top-view-assigned-tasks"
                                    aria-controls="navs-top-view-assigned-tasks">
                                    <i
                                        class="menu-icon tf-icons bx bx-task text-primary"></i><?= get_label('tasks', 'Tasks') ?>
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="navs-top-view-assigned-projects"
                                role="tabpanel">
                                <?php if($auth_user->can('manage_projects')): ?>
                                    <?php if (isset($component)) { $__componentOriginalca6d693a608f33d88584be89bf68c49d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalca6d693a608f33d88584be89bf68c49d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.projects-card','data' => ['viewAssigned' => 1]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('projects-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['viewAssigned' => 1]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalca6d693a608f33d88584be89bf68c49d)): ?>
<?php $attributes = $__attributesOriginalca6d693a608f33d88584be89bf68c49d; ?>
<?php unset($__attributesOriginalca6d693a608f33d88584be89bf68c49d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalca6d693a608f33d88584be89bf68c49d)): ?>
<?php $component = $__componentOriginalca6d693a608f33d88584be89bf68c49d; ?>
<?php unset($__componentOriginalca6d693a608f33d88584be89bf68c49d); ?>
<?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-primary" role="alert">
                                        <?php echo e(get_label('no_projects_view_permission', 'You don\'t have permission to view projects.')); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="tab-pane fade" id="navs-top-view-assigned-tasks" role="tabpanel">
                                <?php if($auth_user->can('manage_tasks')): ?>
                                    <?php if (isset($component)) { $__componentOriginal6e07742d54bdaa8ecbb34c156d7ec080 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6e07742d54bdaa8ecbb34c156d7ec080 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.tasks-card','data' => ['viewAssigned' => 1,'emptyState' => 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tasks-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['viewAssigned' => 1,'emptyState' => 0]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6e07742d54bdaa8ecbb34c156d7ec080)): ?>
<?php $attributes = $__attributesOriginal6e07742d54bdaa8ecbb34c156d7ec080; ?>
<?php unset($__attributesOriginal6e07742d54bdaa8ecbb34c156d7ec080); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6e07742d54bdaa8ecbb34c156d7ec080)): ?>
<?php $component = $__componentOriginal6e07742d54bdaa8ecbb34c156d7ec080; ?>
<?php unset($__componentOriginal6e07742d54bdaa8ecbb34c156d7ec080); ?>
<?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-primary" role="alert">
                                        <?php echo e(get_label('no_tasks_view_permission', 'You don\'t have permission to view tasks.')); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if(Request::is($prefix.'/projects/information/*')): ?>
<?php if(isset($project)): ?>
<!-- Modal for Reply Submission -->
<div class="modal fade" id="replyModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel"><?php echo e(get_label('post_reply', 'Post Reply')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm" method="POST" enctype="multipart/form-data" action="<?php echo e(route('comments.store', ['id' => $project->id])); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="parent_id" name="parent_id" value="">
                    <input type="hidden" name="model_type" value="App\Models\Project">
                    <input type="hidden" name="model_id" value="<?php echo e($project->id); ?>">
                    <div class="mb-3">
                        <label for="content" class="form-label"><?php echo e(get_label('reply', 'Reply')); ?></label>
                        <textarea id="project-reply-content" data-mention-type="project" data-mention-id = "<?php echo e($project->id); ?>" name="content" rows="4" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="attachments" class="form-label"><?php echo e(get_label('attachments', 'Attachments')); ?></label>
                        <input type="file" multiple name="attachments[]" id="attachments" class="form-control" multiple>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2"><?php echo e(get_label('submit', 'Submit')); ?></button>
                        <button type="button" id="closeModal" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(get_label('close', 'Close')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Comment Submission -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel"><?php echo e(get_label('add_comment' , 'Add Comment')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="comment-form" method="POST" enctype="multipart/form-data" action="<?php echo e(route('comments.store', ['id' => $project->id])); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="model_type" value="App\Models\Project">
                    <input type="hidden" name="model_id" value="<?php echo e($project->id); ?>">
                    <input type="hidden" name="parent_id" value="">
                    <div class="mb-3">
                        <textarea id="project-comment-content" name="content" rows="4" class="form-control" data-mention-type="project" data-mention-id="<?php echo e($project->id); ?>" placeholder="<?php echo e(get_label('enter_comment' , 'Enter Comment')); ?>" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="attachments" class="form-label"><?php echo e(get_label('attachments' , 'Attachments')); ?></label>
                        <input type="file" multiple name="attachments[]" id="attachments" class="form-control" multiple>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo e(get_label('post_comment' , 'Post Comment')); ?></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(get_label('cancel' , 'Cancel')); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="EditCommentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel"><?php echo e(get_label('edit_comment' , 'Edit Comment')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="comment-form" class="form-submit-event" method="POST" enctype="multipart/form-data" action="<?php echo e(route('comments.update')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="comment_id" id="comment_id" value="">
                    <div class="mb-3">
                        <textarea id="edit-project-comment-content" name="content" rows="4" class="form-control"  data-mention-type="project" data-mention-id="<?php echo e($project->id); ?>" placeholder="<?php echo e(get_label('enter_comment' , 'Enter Comment')); ?>" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo e(get_label('update' , 'Update')); ?></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(get_label('cancel' , 'Cancel')); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>.
<div class="modal fade" id="DeleteCommentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel"><?php echo e(get_label('delete_comment' , 'Delete Comment')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="delete-comment-form" class="form-submit-event" method="POST" enctype="multipart/form-data" action="<?php echo e(route('comments.destroy')); ?>">
                <?php echo method_field('Delete'); ?>
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="comment_id" id="delete_comment_id" value="">
                    <p><?php echo e(get_label('delete_alert' , 'Are you sure you want to delete?')); ?></p>
                    <button type="submit" class="btn btn-danger"><?php echo e(get_label('delete' , 'Delete')); ?></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(get_label('cancel' , 'Cancel')); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>
<!-- Tasks Discussions Modal -->
<?php if(Request::is($prefix.'/tasks/information/*')): ?>
<?php if(isset($task)): ?>
<div class="modal fade" id="task-reply-modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel"><?php echo e(get_label('post_reply', 'Post Reply')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm" method="POST" enctype="multipart/form-data" action="<?php echo e(route('tasks.comments.store', ['id' => $task->id])); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="parent_id" name="parent_id" value="">
                    <input type="hidden" name="model_type" value="App\Models\Task">
                    <input type="hidden" name="model_id" value="<?php echo e($task->id); ?>">
                    <div class="mb-3">
                        <label for="content" class="form-label"><?php echo e(get_label('reply', 'Reply')); ?></label>
                        <textarea id="task-reply-content" data-mention-type="task" data-mention-id="<?php echo e($task->id); ?>" name="content" rows="4" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="attachments" class="form-label"><?php echo e(get_label('attachments', 'Attachments')); ?></label>
                        <input type="file" multiple name="attachments[]" id="attachments" class="form-control" multiple>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2"><?php echo e(get_label('submit', 'Submit')); ?></button>
                        <button type="button" id="closeModal" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(get_label('close', 'Close')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="task_commentModal" tabindex="-1" aria-labelledby="task_commentModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="task_commentModalLabel"><?php echo e(get_label('add_comment' , 'Add Comment')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="comment-form" method="POST" enctype="multipart/form-data" action="<?php echo e(route('tasks.comments.store', ['id' => $task->id])); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="model_type" value="App\Models\Task">
                    <input type="hidden" name="model_id" value="<?php echo e($task->id); ?>">
                    <input type="hidden" name="parent_id" value="">
                    <div class="mb-3">
                        <textarea id="task-comment-content" data-mention-type="task" data-mention-id="<?php echo e($task->id); ?>" name="content" rows="4" class="form-control" placeholder="<?php echo e(get_label('enter_comment' , 'Enter Comment')); ?>" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="attachments" class="form-label"><?php echo e(get_label('attachments' , 'Attachments')); ?></label>
                        <input type="file" multiple name="attachments[]" id="attachments" class="form-control" multiple>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo e(get_label('post_comment' , 'Post Comment')); ?></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(get_label('cancel' , 'Cancel')); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="TaskEditCommentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel"><?php echo e(get_label('edit_comment' , 'Edit Comment')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="comment-form" class="form-submit-event" method="POST" enctype="multipart/form-data" action="<?php echo e(route('tasks.comments.update')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="comment_id" id="comment_id" value="">
                    <div class="mb-3">
                        <textarea id="task-comment-edit-content" data-mention-type="task" data-mention-id="<?php echo e($task->id); ?>" name="content" rows="4" class="form-control" placeholder="<?php echo e(get_label('enter_comment' , 'Enter Comment')); ?>" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo e(get_label('update' , 'Update')); ?></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(get_label('cancel' , 'Cancel')); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>.
<div class="modal fade" id="TaskDeleteCommentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel"><?php echo e(get_label('delete_comment' , 'Delete Comment')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="delete-comment-form" class="form-submit-event" method="POST" enctype="multipart/form-data" action="<?php echo e(route('tasks.comments.destroy')); ?>">
                <?php echo method_field('Delete'); ?>
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="comment_id" id="delete_comment_id" value="">
                    <p><?php echo e(get_label('delete_alert' , 'Are you sure you want to delete?')); ?></p>
                    <button type="submit" class="btn btn-danger"><?php echo e(get_label('delete' , 'Delete')); ?></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(get_label('cancel' , 'Cancel')); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php /**PATH /srv/http/localhost/resources/views/modals.blade.php ENDPATH**/ ?>