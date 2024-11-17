<!-- projects card -->
<?php
$flag = (Request::segment(2) == 'home' || Request::segment(2) == 'users' || Request::segment(2) == 'clients' || isset($viewAssigned) && $viewAssigned == 1) || isset($projects) && !is_countable($projects) || count($projects) == 0 ? 0 : 1;
$visibleColumns = getUserPreferences('projects');
?>
<div class="<?= $flag == 1 ? 'card ' : '' ?>mt-2">
    <?php if($flag == 1): ?>
    <div class="card-body">
        <?php endif; ?>
        <?php echo e($slot); ?>

        <?php if((isset($projects) && is_countable($projects) && count($projects) > 0) || (isset($viewAssigned) && $viewAssigned == 1)): ?>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="input-group input-group-merge">
                    <input type="text" id="project_start_date_between" name="start_date_between" class="form-control" placeholder="<?= get_label('start_date_between', 'Start date between') ?>" autocomplete="off">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="input-group input-group-merge">
                    <input type="text" id="project_end_date_between" name="project_end_date_between" class="form-control" placeholder="<?= get_label('end_date_between', 'End date between') ?>" autocomplete="off">
                </div>
            </div>
            <?php if(isAdminOrHasAllDataAccess() && !isset($viewAssigned)): ?>
            <?php if(!isset($id) || (explode('_',$id)[0] !='client' && explode('_',$id)[0] !='user')): ?>
            <div class="col-md-4 mb-3">
                <select class="form-select" id="projects_user_filter" aria-label="Default select example">
                    <option value=""><?= get_label('select_user', 'Select user') ?></option>

                </select>
            </div>

            <div class="col-md-4 mb-3">
                <select class="form-select" id="projects_client_filter" aria-label="Default select example">

                </select>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            <div class="col-md-4 mb-3">
                <select class="form-select" id="status_filter" aria-label="Default select example">
                    <option value=""><?= get_label('select_status', 'Select status') ?></option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status->id); ?>" <?php if(request()->has('status') && request()->status == $status->id): ?> selected <?php endif; ?>><?php echo e($status->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <input type="hidden" name="project_start_date_from" id="project_start_date_from">
        <input type="hidden" name="project_start_date_to" id="project_start_date_to">

        <input type="hidden" name="project_end_date_from" id="project_end_date_from">
        <input type="hidden" name="project_end_date_to" id="project_end_date_to">

        <input type="hidden" id="is_favorites" value="<?php echo e($favorites??''); ?>">

        <div class="table-responsive text-nowrap">
            <input type="hidden" id="data_type" value="projects">
            <input type="hidden" id="data_table" value="projects_table">
            <input type="hidden" id="save_column_visibility">
            <table id="projects_table" data-toggle="table" data-url="<?php echo e(isset($viewAssigned) && $viewAssigned == 1 ? '' : (!empty($id) ? route('projects.list' , ['id' => $id])  : route('projects.list') )); ?>" data-loading-template="loadingTemplate" data-icons-prefix="bx" data-icons="icons" data-show-refresh="true" data-total-field="total" data-trim-on-search="false" data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-side-pagination="server" data-show-columns="true" data-pagination="true" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-query-params="queryParamsProjects">
                <thead>
                    <tr>
                        <th data-checkbox="true"></th>
                        <th data-field="id" data-visible="<?php echo e((in_array('id', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('id', 'ID') ?></th>
                        <th data-field="title" data-visible="<?php echo e((in_array('title', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('title', 'Title') ?></th>
                        <th data-field="users" data-visible="<?php echo e((in_array('users', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>"><?php echo e(get_label('users', 'Users')); ?></th>
                        <th data-field="clients" data-visible="<?php echo e((in_array('clients', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>"><?php echo e(get_label('clients', 'Clients')); ?></th>
                        <th data-field="tasks_count" data-visible="<?php echo e((in_array('tasks_count', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="false"><?= get_label('tasks_count', 'Tasks Count') ?></th>
                        <th data-field="status_id" data-visible="<?php echo e((in_array('status_id', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true" class="status-column"><?= get_label('status', 'Status') ?></th>
                        <th data-field="priority_id" data-visible="<?php echo e((in_array('priority_id', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true" class="priority-column"><?= get_label('priority', 'Priority') ?></th>
                        <th data-field="start_date" data-visible="<?php echo e((in_array('start_date', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('starts_at', 'Starts at') ?></th>
                        <th data-field="end_date" data-visible="<?php echo e((in_array('end_date', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('ends_at', 'Ends at') ?></th>
                        <th data-field="budget" data-visible="<?php echo e((in_array('budget', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('budget', 'Budget') ?></th>
                        <th data-field="tags" data-visible="<?php echo e((in_array('tags', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('tags', 'Tags') ?></th>
                        <th data-field="created_at" data-visible="<?php echo e((in_array('created_at', $visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('created_at', 'Created at') ?></th>
                        <th data-field="updated_at" data-visible="<?php echo e((in_array('updated_at', $visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('updated_at', 'Updated at') ?></th>
                        <th data-field="actions" data-visible="<?php echo e((in_array('actions', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>"><?php echo e(get_label('actions', 'Actions')); ?></th>

                    </tr>
                </thead>
            </table>
        </div>
        <?php else: ?>
        <?php
        $type = 'Projects'; ?>
        <?php if (isset($component)) { $__componentOriginal0fbbaf7987e44b855eae69b67dad7fdf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0fbbaf7987e44b855eae69b67dad7fdf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state-card','data' => ['type' => $type]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('empty-state-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($type)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0fbbaf7987e44b855eae69b67dad7fdf)): ?>
<?php $attributes = $__attributesOriginal0fbbaf7987e44b855eae69b67dad7fdf; ?>
<?php unset($__attributesOriginal0fbbaf7987e44b855eae69b67dad7fdf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0fbbaf7987e44b855eae69b67dad7fdf)): ?>
<?php $component = $__componentOriginal0fbbaf7987e44b855eae69b67dad7fdf; ?>
<?php unset($__componentOriginal0fbbaf7987e44b855eae69b67dad7fdf); ?>
<?php endif; ?>
        <?php endif; ?>
        <?php if($flag == 1): ?>
    </div>
    <?php endif; ?>
</div>

<script>
    var label_update = '<?= get_label('update', 'Update') ?>';
    var label_delete = '<?= get_label('delete', 'Delete') ?>';
    var label_not_assigned = '<?= get_label('not_assigned', 'Not assigned') ?>';
    var add_favorite = '<?= get_label('add_favorite', 'Click to mark as favorite') ?>';
    var remove_favorite = '<?= get_label('remove_favorite', 'Click to remove from favorite') ?>';
    var label_duplicate = '<?= get_label('duplicate', 'Duplicate') ?>';

</script>
<script src="<?php echo e(asset('assets/js/pages/project-list.js')); ?>"></script>
<?php /**PATH C:\Siddharth Gor\Test\resources\views/components/projects-card.blade.php ENDPATH**/ ?>