<?php $__env->startSection('title'); ?>
    <?= get_label('notes', 'Notes') ?>
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
                        <li class="breadcrumb-item active">
                            <?= get_label('notes', 'Notes') ?>
                        </li>
                    </ol>
                </nav>
            </div>
            <div>
                <span data-bs-toggle="modal" data-bs-target="#create_note_modal">
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                        data-bs-placement="left" data-bs-original-title="<?= get_label('create_note', 'Create note') ?>">
                        <i class='bx bx-plus'></i>
                    </a>
                </span>
            </div>
        </div>

        <div class="card">
            <?php if($notes->count() > 0): ?>
             <div class="card-body">
            <button type="button" id="delete-selected" class="btn btn-outline-danger mx-4" data-type="notes">
                <i class="bx bx-trash"></i> <?php echo e(get_label('delete_selected', 'Delete Selected')); ?>

            </button>
            <div class="form-check mt-3 mx-4">
                <input type="checkbox" id="select-all" class="form-check-input">
                <label for="select-all" class="form-check-label"><?php echo e(get_label('select_all', 'Select All')); ?></label>
            </div>

                <div class="row  mt-4 sticky-notes">
                    <?php $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4 sticky-note">
                            <div class="sticky-content sticky-note-bg-<?= $note->color ?>">
                                <div class="text-end">

                                    <a href="javascript:void(0);" class="btn btn-primary btn-xs edit-note"
                                        data-url="<?php echo e(route('notes.get', ['id' => $note->id])); ?>"
                                        data-id='<?php echo e($note->id); ?>' data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-original-title="<?= get_label('update', 'Update') ?>">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    <a href="javascript:void(0);" class="btn btn-danger btn-xs mx-1 delete"
                                        data-id='<?php echo e($note->id); ?>' data-type='notes' data-reload='true'
                                        data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-original-title="<?= get_label('delete', 'Delete') ?>">
                                        <i class="bx bx-trash"></i>
                                    </a>

                                </div>
                                 <div class="d-flex align-items-center">
                                <input type="checkbox" class="ms-0 mx-2 selected-items" value="<?php echo e($note->id); ?>">
                                <span class="note-id">#<?php echo e($note->id); ?></span>
                            </div>
                                <h4><?= $note->title ?></h4>
                                <p><?= $note->description ?></p>
                                <b><?= get_label('created_at', 'Created at') ?> : </b><span
                                    class="text-primary"><?php echo e(format_date($note->created_at)); ?></span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php else: ?>
                <?php
                $type = 'Notes';
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
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Siddharth Gor\Test\resources\views/notes/list.blade.php ENDPATH**/ ?>