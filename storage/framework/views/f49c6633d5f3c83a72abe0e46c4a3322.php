<?php $__env->startSection('title'); ?>
    <?= get_label('projects', 'Projects') ?> - <?= get_label('list_view', 'List view') ?>
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
                            <a href="<?php echo e(route('projects.index')); ?>"><?= get_label('projects', 'Projects') ?></a>
                        </li>
                        <?php if($is_favorites == 1): ?>
                            <li class="breadcrumb-item"><a
                                    href="<?php echo e(route('projects.index', ['type' => 'favorite'])); ?>"><?php echo e(get_label('favorite', 'Favorite')); ?></a>
                            </li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active"><?= get_label('list', 'List') ?></li>
                    </ol>
                </nav>
            </div>
             <div>
            <?php
            $projectDefaultView = getUserPreferences('projects', 'default_view');
            ?>
            
            <?php if($projectDefaultView === 'list'): ?>
            <span class="badge bg-primary"><?= get_label('default_view', 'Default View') ?></span>
            <?php else: ?>
            <a href="javascript:void(0);"><span class="badge bg-secondary" id="set-default-view" data-type="projects" data-view="list"><?= get_label('set_as_default_view', 'Set as Default View') ?></span></a>
            <?php endif; ?>
        </div>
            <div>
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_project_modal"><button type="button" class="btn btn-sm btn-primary action_create_projects" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('create_project', 'Create project') ?>"><i class='bx bx-plus'></i></button></a>
                <a
                    href="<?php echo e(url(request()->has('status') ? route('projects.index', ['status' => request()->status]) : route('projects.index'))); ?>">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-original-title="<?= get_label('grid_view', 'Grid view') ?>">
                        <i class='bx bxs-grid-alt'></i>
                    </button>
                </a>
                <a href="<?php echo e(route('projects.kanban_view')); ?>"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('kanban_view', 'Kanban View') ?>"><i class='bx bxs-dashboard'></i></button></a>
                <a href="<?php echo e(route('projects.gantt_chart')); ?>"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('gantt_chart_view', 'Gantt Chart View') ?>"><i class='bx bxs-collection'></i></button></a>

            </div>
        </div>
        <?php if (isset($component)) { $__componentOriginalca6d693a608f33d88584be89bf68c49d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalca6d693a608f33d88584be89bf68c49d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.projects-card','data' => ['projects' => $projects,'users' => $users,'clients' => $clients,'favorites' => $is_favorites]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('projects-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['projects' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($projects),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'clients' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clients),'favorites' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($is_favorites)]); ?>
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
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Siddharth Gor\Test\resources\views/projects/projects.blade.php ENDPATH**/ ?>