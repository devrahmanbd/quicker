<?php $__env->startSection('title'); ?>
    <?= get_label('plans', 'Plans') ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between mt-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('superadmin.panel')); ?>"><?= get_label('home', 'Home') ?></a>
                        </li>
                        <li class="breadcrumb-item active">
                            <?= get_label('plans', 'Plans') ?>
                        </li>
                    </ol>
                </nav>
            </div>

            <div>
                <a href="<?php echo e(route('plans.create')); ?>">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-original-title=" <?= get_label('create_plans', 'Create Plans') ?>">
                        <i class="bx bx-plus"></i>
                    </button>
                </a>
                <a href="<?php echo e(route('plans.index')); ?>">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-original-title="<?= get_label('plans', 'Plans') ?>">
                        <i class='bx bx-list-ul'></i>
                    </button>
                </a>
            </div>
        </div>
        <?php if(is_countable($plans) && count($plans) > 0): ?>
            <div class="card">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><?php echo e(get_label('plans', 'Plans')); ?></h4>
                    <input type="hidden" id="data_type" value="plans">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo e(get_label('filter_by_status' , 'Filter by status')); ?></label>
                            <select id="status" name="status" class="form-select">
                                <option value=""><?php echo e(get_label('select_status' , 'Select Status')); ?></option>
                                <option value="active"><?php echo e(get_label('active' , 'Active')); ?></option>
                                <option value="inactive"><?php echo e(get_label('inactive' , 'Inactive')); ?></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo e(get_label('filter_by_type' , 'Filter By Type')); ?></label>
                            <select id="filter_by_type" class="form-select">
                                <option value=""><?php echo e(get_label('select_type' ,'Select Type')); ?></option>
                                <option value="free"><?php echo e(get_label('free' , 'Free')); ?></option>
                                <option value="paid"><?php echo e(get_label('paid' , 'Paid')); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive text-nowrap">

                        <table id="table" class="table" data-toggle="table" data-loading-template="loadingTemplate"
                            data-url="<?php echo e(route('plans.list')); ?>" data-icons-prefix="bx" data-icons="icons"
                            data-show-refresh="true" data-total-field="total" data-trim-on-search="false"
                            data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                            data-side-pagination="server" data-show-columns="true" data-pagination="true"
                            data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
                            data-query-params="queryParams">
                            <thead>
                                <tr>
                                    <th data-checkbox="true"></th>
                                    <th data-visible="false" data-sortable="true" data-field="id">
                                        <?php echo e(get_label('id', 'ID')); ?></th>
                                    <th data-field="name"><?php echo e(get_label('name', 'Name')); ?></th>
                                    <th data-visible="false" data-field="description" data-sortable="true">
                                        <?php echo e(get_label('description', 'Description')); ?></th>
                                    <th data-field="plan_type"><?php echo e(get_label('plan_type', 'Plan Type')); ?></th>
                                    <th data-visible="false" data-field="max_projects">
                                        <?php echo e(get_label('max_projects', 'Maximum Projects')); ?></th>
                                    <th data-visible="false" data-field="max_clients">
                                        <?php echo e(get_label('max_clients', 'Maximum Clients')); ?></th>
                                    <th data-visible="false" data-field="max_team_members">
                                        <?php echo e(get_label('max_team_members', 'Maximum Team Members')); ?></th>
                                    <th data-visible="false" data-field="max_workspaces">
                                        <?php echo e(get_label('max_workspaces', 'Maximum Workshops')); ?></th>
                                    <th data-field="modules"><?php echo e(get_label('modules', 'Modules')); ?></th>
                                    <th data-field="monthly_price"><?php echo e(get_label('monthly_price', 'Monthly Price')); ?>

                                    </th>
                                    <th data-visible="false" data-field="monthly_discounted_price">
                                        <?php echo e(get_label('monthly_discounted_price', 'Monthly Discounted Price')); ?></th>
                                    <th data-field="yearly_price"><?php echo e(get_label('yearly_price', 'Yearly Price')); ?></th>
                                    <th data-visible="false" data-field="yearly_discounted_price">
                                        <?php echo e(get_label('yearly_discounted_price', 'Yearly Discounted Price')); ?></th>
                                    <th data-field="lifetime_price"><?php echo e(get_label('lifetime_price', 'Lifetime Price')); ?>

                                    </th>
                                    <th data-visible="false" data-field="lifetime_discounted_price">
                                        <?php echo e(get_label('lifetime_discounted_price', 'Lifetime Discounted Price')); ?></th>
                                    <th data-field="status"><?php echo e(get_label('status', 'Status')); ?></th>
                                    <th data-formatter="actionFormatter"><?php echo e(get_label('actions', 'Actions')); ?></th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php
            $type = 'Plans';
            ?>
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

    </div>
    <?php
        $routePrefix = Route::getCurrentRoute()->getPrefix();
    ?>

    <script>
        var label_update = '<?= get_label('update ', 'Update ') ?>';
        var label_delete = '<?= get_label('delete ', 'Delete ') ?>';
        var routePrefix = '<?php echo e($routePrefix); ?>';
    </script>

    <script src="<?php echo e(asset('assets/js/pages/plans.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Siddharth Gor\Test\resources\views/superadmin/plans/list.blade.php ENDPATH**/ ?>