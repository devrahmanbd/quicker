<?php $__env->startSection('title'); ?>
    <?= get_label('workspaces', 'Workspaces') ?>
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
                            <?= get_label('workspaces', 'Workspaces') ?>
                        </li>
                    </ol>
                </nav>
            </div>
            <div>
                 <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#createWorkspaceModal"><button type="button" class="btn btn-sm btn-primary action_create_workspaces" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('create_workspace', 'Create workspace') ?>"><i class='bx bx-plus'></i></button></a>
            </div>
        </div>
        <?php if (isset($component)) { $__componentOriginald799cb842d23bfe06e6b25e8687669c9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald799cb842d23bfe06e6b25e8687669c9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.workspaces-card','data' => ['workspaces' => $workspaces,'users' => $users,'clients' => $clients,'admin' => $admin]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('workspaces-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['workspaces' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workspaces),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'clients' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clients),'admin' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($admin)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald799cb842d23bfe06e6b25e8687669c9)): ?>
<?php $attributes = $__attributesOriginald799cb842d23bfe06e6b25e8687669c9; ?>
<?php unset($__attributesOriginald799cb842d23bfe06e6b25e8687669c9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald799cb842d23bfe06e6b25e8687669c9)): ?>
<?php $component = $__componentOriginald799cb842d23bfe06e6b25e8687669c9; ?>
<?php unset($__componentOriginald799cb842d23bfe06e6b25e8687669c9); ?>
<?php endif; ?>
    </div>
    <?php
        $routePrefix = Route::getCurrentRoute()->getPrefix();
    ?>

    <script>
        var label_update = '<?= get_label('update', 'Update') ?>';
        var label_delete = '<?= get_label('delete', 'Delete') ?>';
        var label_not_assigned = '<?= get_label('not_assigned', 'Not assigned') ?>';
        var label_duplicate = '<?= get_label('duplicate', 'Duplicate') ?>';
        var routePrefix = '<?php echo e($routePrefix); ?>';
    </script>
    <script src="<?php echo e(asset('assets/js/pages/workspaces.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Siddharth Gor\Test\resources\views/workspaces/workspaces.blade.php ENDPATH**/ ?>