<!-- workspaces -->

<?php if(is_countable($workspaces) && count($workspaces) > 0): ?>
<?php
$visibleColumns = getUserPreferences('workspaces');
?>
<div class="card">
    <div class="card-body">
        <?php echo e($slot); ?>

        <div class="row">
            <?php if(isAdminOrHasAllDataAccess()): ?>
             <div class="col-md-3 mb-3">
                        <select class="form-select" id="workspace_user_filter" aria-label="Default select example">


                        </select>
                    </div>

            <div class="col-md-3 mb-3">
                        <select class="form-select" id="workspace_client_filter" aria-label="Default select example">

                        </select>
                    </div>
            <?php endif; ?>
        </div>

        <div class="table-responsive text-nowrap">
            <input type="hidden" id="data_type" value="workspaces">
            <input type="hidden" id="save_column_visibility">
            <table id="table" data-toggle="table" data-loading-template="loadingTemplate" data-url="<?php echo e(route('workspaces.list')); ?>" data-icons-prefix="bx" data-icons="icons" data-show-refresh="true" data-total-field="total" data-trim-on-search="false" data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-side-pagination="server" data-show-columns="true" data-pagination="true" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-query-params="queryParams">
                <thead>
                    <tr>
                        <th data-checkbox="true"></th>
                        <th data-field="id" data-visible="<?php echo e((in_array('id', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('id', 'ID') ?></th>
                        <th data-field="title" data-visible="<?php echo e((in_array('title', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('title', 'Title') ?></th>
                        <th data-field="users" data-visible="<?php echo e((in_array('users', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>"><?php echo e(get_label('users', 'Users')); ?></th>
                        <th data-field="clients" data-visible="<?php echo e((in_array('clients', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>"><?php echo e(get_label('clients', 'Clients')); ?></th>
                        <th data-field="created_at" data-visible="<?php echo e((in_array('created_at', $visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('created_at', 'Created at') ?></th>
                        <th data-field="updated_at" data-visible="<?php echo e((in_array('updated_at', $visibleColumns)) ? 'true' : 'false'); ?>" data-sortable="true"><?= get_label('updated_at', 'Updated at') ?></th>
                        <th data-field="actions" data-visible="<?php echo e((in_array('actions', $visibleColumns) || empty($visibleColumns)) ? 'true' : 'false'); ?>"><?php echo e(get_label('actions', 'Actions')); ?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<?php
$type = 'Workspaces'; ?>
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
<?php /**PATH C:\Siddharth Gor\Test\resources\views/components/workspaces-card.blade.php ENDPATH**/ ?>