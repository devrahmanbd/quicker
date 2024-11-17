<?php $__env->startSection('title'); ?>
    <?= get_label('todo_list', 'Todo list') ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-2 mt-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('home.index')); ?>"><?= get_label('home', 'Home') ?></a>
                        </li>
                        <li class="breadcrumb-item active">
                            <?= get_label('todos', 'Todos') ?>
                        </li>
                    </ol>
                </nav>
            </div>
            <div>
                <span data-bs-toggle="modal" data-bs-target="#create_todo_modal"><a href="javascript:void(0);"
                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-original-title="<?= get_label('create_todo', 'Create todo') ?>"><i
                            class='bx bx-plus'></i></a></span>
            </div>
        </div>

        <?php if(is_countable($todos) && count($todos) > 0): ?>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <!-- Delete Selected Button -->
                        <button type="button" id="delete-selected" class="btn btn-outline-danger" data-type="todos">
                            <i class="bx bx-trash"></i> <?php echo e(get_label('delete_selected', 'Delete Selected')); ?>

                        </button>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th><?= get_label('id', 'ID') ?></th>
                                    <th><?= get_label('status', 'Status') ?></th>
                                    <th><?= get_label('title', 'Title') ?></th>
                                    <th><?= get_label('priority', 'Priority') ?></th>
                                    <th><?= get_label('description', 'Description') ?></th>
                                    <th><?= get_label('updated_at', 'Updated at') ?></th>
                                    <th><?= get_label('actions', 'Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php $__currentLoopData = $todos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $todo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><input type="checkbox" class="selected-items form-check-input"
                                                value="<?php echo e($todo->id); ?>"></td>
                                        <td><?php echo e($todo->id); ?></td>
                                        <td>
                                            <input type="checkbox" id="<?php echo e($todo->id); ?>" onclick='update_status(this)'
                                                name="<?php echo e($todo->id); ?>" class="form-check-input mt-0"
                                                <?php echo e($todo->is_completed ? 'checked' : ''); ?>>
                                        </td>
                                        <td>
                                            <h4 class="<?= $todo->is_completed ? 'striked' : '' ?> m-0"
                                                id="<?php echo e($todo->id); ?>_title"><?php echo e($todo->title); ?></h4>
                                            <h7 class="text-muted m-0"><?php echo e(format_date($todo->created_at, true)); ?></h7>
                                        </td>
                                        <td>
                                            <span
                                                class='badge bg-label-<?php echo e(config('taskify.priority_labels')[$todo->priority]); ?> me-1'><?php echo e($todo->priority); ?></span>
                                        </td>
                                        <td>
                                            <?php echo $todo->description; ?>

                                        </td>
                                        <td>
                                            <?php echo e(format_date($todo->updated_at, true)); ?>

                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="javascript:void(0);" class="edit-todo" data-bs-toggle="modal"
                                                    data-bs-target="#edit_todo_modal" data-id="<?php echo e($todo->id); ?>"
                                                    title="<?= get_label('update', 'Update') ?>" class="card-link"><i
                                                        class='bx bx-edit mx-1'></i></a>
                                                <a href="javascript:void(0);" type="button" data-id="<?php echo e($todo->id); ?>"
                                                    data-type="todos" data-reload="true"
                                                    title="<?= get_label('delete', 'Delete') ?>"
                                                    class="card-link delete mx-4"><i
                                                        class='bx bx-trash text-danger mx-1'></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php
            $type = 'Todos'; ?>
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


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Siddharth Gor\Test\resources\views/todos/list.blade.php ENDPATH**/ ?>