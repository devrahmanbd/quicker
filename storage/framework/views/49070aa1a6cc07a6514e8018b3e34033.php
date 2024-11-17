<?php $__env->startSection('title'); ?>
    <?= get_label('customers', 'Customers') ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between flex-wraps  mt-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('superadmin.panel')); ?>"><?= get_label('home', 'Home') ?></a>
                        </li>

                        <li class="breadcrumb-item active">
                            <?= get_label('customers', 'Customers') ?>
                        </li>

                    </ol>
                </nav>
            </div>

            <div>
                <a href="<?php echo e(route('customers.create')); ?>"><button type="button" class="btn btn-sm btn-primary"
                        data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-original-title=" <?= get_label('create_customers', 'Create Customers') ?>"><i
                            class="bx bx-plus"></i></button></a>
                <a href="<?php echo e(route('customers.index')); ?>"><button type="button" class="btn btn-sm btn-primary"
                        data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-original-title="<?= get_label('customers', 'Customers') ?>"><i
                            class='bx bx-list-ul'></i></button></a>
            </div>
        </div>
        <?php if(is_countable($customers) && count($customers) > 0): ?>
            <div class="card">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><?php echo e(get_label('customers', 'Customers')); ?></h4>
                    <input type="hidden" id="data_type" value="customers">
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">

                        <table id="table" data-toggle="table" data-loading-template="loadingTemplate"
                            data-url="<?php echo e(route('customers.list')); ?>" data-icons-prefix="bx" data-icons="icons"
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
                                    <th data-field="first_name" data-sortable="true"><?php echo e(get_label('first_name', 'First Name')); ?></th>
                                    <th data-field="last_name" data-sortable="true">
                                        <?php echo e(get_label('last_name', 'Last Name')); ?></th>
                                    <th data-field="phone"><?php echo e(get_label('phone_number', 'Phone Number')); ?></th>
                                    <th data-field="email"><?php echo e(get_label('email', 'Email')); ?></th>
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
            $type = 'Customers'; ?>
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

    <script src="<?php echo e(asset('assets/js/pages/customers.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Siddharth Gor\Test\resources\views/superadmin/customers/index.blade.php ENDPATH**/ ?>