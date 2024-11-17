<?php $__env->startSection('title'); ?>
    <?= $is_favorite == 1 ? get_label('favorite_projects', 'Favorite projects') : get_label('projects', 'Projects') ?> -
    <?= get_label('grid_view', 'Grid view') ?>
<?php $__env->stopSection(); ?>
<?php
    $user = getAuthenticatedUser();
?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-2 mt-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('home.index')); ?>"><?= get_label('home', 'Home') ?></a>
                        </li>
                        <?php if($is_favorite == 1): ?>
                            <li class="breadcrumb-item"><a
                                    href="<?php echo e(url('/master-panel/' . getUserPreferences('projects', 'default_view'))); ?>"><?= get_label('projects', 'Projects') ?></a>
                            </li>
                            <li class="breadcrumb-item active"><?= get_label('favorite', 'Favorite') ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item active"><?= get_label('projects', 'Projects') ?></li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
            <div>
                <?php
                    $projectDefaultView = getUserPreferences('projects', 'default_view');
                ?>
                <?php if(!$projectDefaultView || $projectDefaultView === 'grid'): ?>
                    <span class="badge bg-primary"><?= get_label('default_view', 'Default View') ?></span>
                <?php else: ?>
                    <a href="javascript:void(0);"><span class="badge bg-secondary" id="set-default-view"
                            data-type="projects"
                            data-view="grid"><?= get_label('set_as_default_view', 'Set as Default View') ?></span></a>
                <?php endif; ?>
            </div>
            <div>
                <?php
                    $url =
                        $is_favorite == 1
                            ? url('/master-panel/projects/list/favorite')
                            : url('/master-panel/projects/list');
                    $additionalParams = request()->has('status')
                        ? '/master-panel/projects/list?status=' . request()->status
                        : '';
                    $finalUrl = url($additionalParams ?: $url);
                    $currentPath = request()->path();
                    $showCreateButton = !in_array($currentPath, ['projects/list/favorite', 'projects/favorite']);
                ?>
                <?php if($showCreateButton): ?>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_project_modal"><button
                            type="button" class="btn btn-sm btn-primary action_create_projects" data-bs-toggle="tooltip"
                            data-bs-placement="left"
                            data-bs-original-title="<?= get_label('create_project', 'Create project') ?>"><i
                                class='bx bx-plus'></i></button></a>
                <?php endif; ?>
                <a href="<?php echo e($finalUrl); ?>"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                        data-bs-placement="left" data-bs-original-title="<?= get_label('list_view', 'List view') ?>"><i
                            class='bx bx-list-ul'></i></button></a>
                <a href="<?php echo e(route('projects.kanban_view')); ?>"><button type="button" class="btn btn-sm btn-primary"
                        data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-original-title="<?= get_label('kanban_view', 'Kanban View') ?>"><i
                            class='bx bxs-dashboard'></i></button></a>
                <a href="<?php echo e(route('projects.gantt_chart')); ?>"><button type="button" class="btn btn-sm btn-primary"
                        data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-original-title="<?= get_label('gantt_chart_view', 'Gantt Chart View') ?>"><i
                            class='bx bxs-collection'></i></button></a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <select class="form-select select2-ajax" id="status_filter" aria-label="Filter by status">
                    <option value=""><?= get_label('filter_by_status', 'Filter by status') ?></option>
                </select>

            </div>
            <div class="col-md-3 mb-3">
                <select class="form-select" id="sort" aria-label="Default select example">
                    <option value=""><?= get_label('sort_by', 'Sort by') ?></option>
                    <option value="newest" <?= request()->sort && request()->sort == 'newest' ? 'selected' : '' ?>>
                        <?= get_label('newest', 'Newest') ?></option>
                    <option value="oldest" <?= request()->sort && request()->sort == 'oldest' ? 'selected' : '' ?>>
                        <?= get_label('oldest', 'Oldest') ?></option>
                    <option value="recently-updated"
                        <?= request()->sort && request()->sort == 'recently-updated' ? 'selected' : '' ?>>
                        <?= get_label('most_recently_updated', 'Most recently updated') ?></option>
                    <option value="earliest-updated"
                        <?= request()->sort && request()->sort == 'earliest-updated' ? 'selected' : '' ?>>
                        <?= get_label('least_recently_updated', 'Least recently updated') ?></option>
                </select>
            </div>
            <div class="col-md-5 mb-3">
                <select id="selected_tags" class="form-control js-example-basic-multiple" name="tag[]" multiple="multiple"
                    data-placeholder="<?= get_label('filter_by_tags', 'Filter by tags') ?>">
                    <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tag->id); ?>" <?php if(in_array($tag->id, $selectedTags)): ?> selected <?php endif; ?>>
                            <?php echo e($tag->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-1">
                <div>
                    <button type="button" id="tags_filter" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                        data-bs-placement="left" data-bs-original-title="<?= get_label('filter', 'Filter') ?>"><i
                            class='bx bx-filter-alt'></i></button>
                </div>
            </div>
        </div>
        <?php if(is_countable($projects) && count($projects) > 0): ?>
            <?php
                $showSettings =
                    $user->can('edit_projects') || $user->can('delete_projects') || $user->can('create_projects');
                $canEditProjects = $user->can('edit_projects');
                $canDeleteProjects = $user->can('delete_projects');
                $canDuplicateProjects = $user->can('create_projects');
            ?>
            <div class="d-flex row mt-4">
                <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <?php if(count($project->tags) > 0): ?>
                                    <div class="mb-3">
                                        <?php $__currentLoopData = $project->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-<?php echo e($tag->color); ?> mt-1"><?php echo e($tag->title); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="card-title">
                                        <a href="<?php echo e(url('/master-panel/projects/information/' . $project->id)); ?>"
                                            target="_blank" class="text-body">
                                            <strong><?php echo e($project->title); ?></strong>
                                        </a>
                                    </h4>
                                    <div class="d-flex align-items-center">
                                        <?php if($showSettings): ?>
                                            <div class="dropdown">
                                                <a href="javascript:void(0);" class="mx-2" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class='bx bx-cog'></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <?php if($canEditProjects): ?>
                                                        <a href="javascript:void(0);" class="edit-project"
                                                            data-id="<?php echo e($project->id); ?>">
                                                            <li class="dropdown-item">
                                                                <i class='menu-icon tf-icons bx bx-edit text-primary'></i>
                                                                <?php echo e(get_label('edit', 'Edit')); ?>

                                                            </li>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if($canDeleteProjects): ?>
                                                        <a href="javascript:void(0);" class="delete" data-reload="true"
                                                            data-type="projects" data-id="<?php echo e($project->id); ?>">
                                                            <li class="dropdown-item">
                                                                <i class='menu-icon tf-icons bx bx-trash text-danger'></i>
                                                                <?php echo e(get_label('delete', 'Delete')); ?>

                                                            </li>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if($canDuplicateProjects): ?>
                                                        <a href="javascript:void(0);" class="duplicate"
                                                            data-type="projects" data-id="<?php echo e($project->id); ?>"
                                                            data-title="<?php echo e($project->title); ?>" data-reload="true">
                                                            <li class="dropdown-item">
                                                                <i class='menu-icon tf-icons bx bx-copy text-warning'></i>
                                                                <?php echo e(get_label('duplicate', 'Duplicate')); ?>

                                                            </li>
                                                        </a>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                        <a href="javascript:void(0);" class="quick-view mx-2"
                                            data-id="<?php echo e($project->id); ?>" data-type="project">
                                            <i class='bx bx-info-circle text-info' data-bs-toggle="tooltip"
                                                data-bs-placement="right" title="Quick View"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="mx-2">
                                            <i class='bx <?php echo e($project->is_favorite ? 'bxs' : 'bx'); ?>-star favorite-icon text-warning'
                                                data-id="<?php echo e($project->id); ?>" data-bs-toggle="tooltip"
                                                data-bs-placement="right"
                                                data-bs-original-title="<?php echo e($project->is_favorite ? get_label('remove_favorite', 'Click to remove from favorite') : get_label('add_favorite', 'Click to mark as favorite')); ?>"
                                                data-favorite="<?php echo e($project->is_favorite); ?>"></i>
                                        </a>
                                        <a href="<?php echo e(route('projects.info', ['id' => $project->id])); ?>#navs-top-discussions"
                                            target="_blank" class="text-danger mx-2">
                                            <i class='bx bx-message-rounded-dots' data-bs-toggle="tooltip"
                                                data-bs-placement="right" title="Discussion"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php if($project->budget != ''): ?>
                                    <span
                                        class='badge bg-label-primary me-1'><?php echo e(format_currency($project->budget)); ?></span>
                                <?php endif; ?>
                                <div class="my-<?php echo e($project->budget != '' ? '3' : '2'); ?>">

                                    <div class="row align-items-center mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label"><?php echo e(get_label('status', 'Status')); ?></label>
                                            <div class="d-flex align-items-center">
                                                <div class="status-selector" id="statusSelector">
                                                    <?php if(isset($project->status)): ?>
                                                        <span
                                                            class="status-tag badge bg-label-<?php echo e($project->status->color); ?> selected"
                                                            data-value="<?php echo e($project->status->id); ?>">
                                                            <?php echo e($project->status->title); ?>

                                                        </span>
                                                    <?php else: ?>
                                                        <span
                                                            class="status-tag badge bg-label-dark selected"><?php echo e(get_label('no_status', 'No status')); ?></span>
                                                    <?php endif; ?>

                                                </div>
                                                <?php if($project->note): ?>
                                                    <div>
                                                        <span class="ms-2" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            data-bs-original-title="<?php echo e($project->note); ?>"><i
                                                                class="text-primary bx bxs-notepad"></i></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><?php echo e(get_label('priority', 'Priority')); ?></label>
                                            <div class="priority-selector" id="prioritySelector">
                                                <?php if(isset($project->priority)): ?>
                                                    <span
                                                        class="priority-tag badge bg-label-<?php echo e($project->priority->color); ?> selected"
                                                        data-value="<?php echo e($project->priority->id); ?>">
                                                        <?php echo e($project->priority->title); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span
                                                        class="priority-tag badge bg-label-dark selected"><?php echo e(get_label('no_priority', 'No priority')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tasks Insights -->
                                <div class="mt-3">
                                    <label class="form-label"><?php echo e(get_label('tasks_insights', 'Tasks Insights')); ?></label>
                                    <div class="progress h-100">
                                        <?php if($project->tasks->count() > 0): ?>
                                            <?php
                                                $totalTasks = $project->tasks->count();
                                                $groupedTasks = $project->tasks->groupBy('status_id');
                                            ?>
                                            <?php $__currentLoopData = $groupedTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusId => $tasks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $status = App\Models\Status::find($statusId);
                                                    $taskCount = $tasks->count();
                                                    $percentage = ($taskCount / $totalTasks) * 100;
                                                ?>
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-<?php echo e($status->color); ?>"
                                                    role="progressbar" style="width: <?php echo e($percentage); ?>%;"
                                                    aria-valuenow="<?php echo e($percentage); ?>" aria-valuemin="0"
                                                    aria-valuemax="100"
                                                    title="<?php echo e($status->title); ?>: <?php echo e($taskCount); ?> tasks">
                                                    <?php echo e($status->title); ?> (<?php echo e($taskCount); ?>)
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary w-100"
                                                role="progressbar w-100" aria-valuenow="100" aria-valuemin="0"
                                                aria-valuemax="100"
                                                title="<?php echo e(get_label('no_tasks_yet', 'No Tasks Yet')); ?>">
                                                <?php echo e(get_label('no_tasks_yet', 'No Tasks Yet')); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between my-4">
                                    <span>
                                        <i class='bx bx-task text-primary'></i>
                                        <b><?php echo e(isAdminOrHasAllDataAccess() ? count($project->tasks) : $auth_user->project_tasks($project->id)->count()); ?></b>
                                        <?php echo e(get_label('tasks', 'Tasks')); ?>

                                    </span>
                                    <a href="<?php echo e(url('/master-panel/projects/tasks/draggable/' . $project->id)); ?>"
                                        class="btn btn-sm rounded-pill btn-outline-primary"><?php echo e(get_label('tasks', 'Tasks')); ?></a>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <p class="card-text">
                                            <?php echo e(get_label('users', 'Users')); ?>:
                                        <ul class="list-unstyled users-list avatar-group d-flex align-items-center m-0">
                                            <?php
                                                $users = $project->users;
                                                $count = count($users);
                                                $displayed = 0;
                                            ?>
                                            <?php if($count > 0): ?>
                                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($displayed < 10): ?>
                                                        <li class="avatar avatar-sm pull-up"
                                                            title="<?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?>">
                                                            <a href="<?php echo e(route('users.show', ['id' => $user->id])); ?>"
                                                                target="_blank">
                                                                <img src="<?php echo e($user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')); ?>"
                                                                    class="rounded-circle"
                                                                    alt="<?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?>">
                                                            </a>
                                                        </li>
                                                        <?php $displayed++; ?>
                                                    <?php else: ?>
                                                        <?php
                                                            $remaining = $count - $displayed;
                                                        ?>
                                                        <span
                                                            class="badge badge-center rounded-pill bg-primary mx-1">+<?php echo e($remaining); ?></span>
                                                    <?php break; ?>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <a href="javascript:void(0)"
                                                class="btn btn-icon btn-sm btn-outline-primary btn-sm rounded-circle edit-project update-users-clients"
                                                data-id="<?php echo e($project->id); ?>">
                                                <span class="bx bx-edit"></span>
                                            </a>
                                        <?php else: ?>
                                            <span
                                                class="badge bg-primary"><?php echo e(get_label('not_assigned', 'Not assigned')); ?></span>
                                            <a href="javascript:void(0)"
                                                class="btn btn-icon btn-sm btn-outline-primary btn-sm rounded-circle edit-project update-users-clients"
                                                data-id="<?php echo e($project->id); ?>">
                                                <span class="bx bx-edit"></span>
                                            </a>
                                        <?php endif; ?>
                                    </ul>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="card-text">
                                        <?php echo e(get_label('clients', 'Clients')); ?>:
                                    <ul class="list-unstyled users-list avatar-group d-flex align-items-center m-0">
                                        <?php
                                            $clients = $project->clients;
                                            $count = count($clients);
                                            $displayed = 0;
                                        ?>
                                        <?php if($count > 0): ?>
                                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($displayed < 10): ?>
                                                    <li class="avatar avatar-sm pull-up"
                                                        title="<?php echo e($client->first_name); ?> <?php echo e($client->last_name); ?>">
                                                        <a href="<?php echo e(route('clients.profile', ['id' => $client->id])); ?>"
                                                            target="_blank">
                                                            <img src="<?php echo e($client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg')); ?>"
                                                                class="rounded-circle"
                                                                alt="<?php echo e($client->first_name); ?> <?php echo e($client->last_name); ?>">
                                                        </a>
                                                    </li>
                                                    <?php $displayed++; ?>
                                                <?php else: ?>
                                                    <?php
                                                        $remaining = $count - $displayed;
                                                    ?>
                                                    <span
                                                        class="badge badge-center rounded-pill bg-primary mx-1">+<?php echo e($remaining); ?></span>
                                                <?php break; ?>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <a href="javascript:void(0)"
                                            class="btn btn-icon btn-sm btn-outline-primary btn-sm rounded-circle edit-project update-users-clients"
                                            data-id="<?php echo e($project->id); ?>">
                                            <span class="bx bx-edit"></span>
                                        </a>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-primary"><?php echo e(get_label('not_assigned', 'Not assigned')); ?></span>
                                        <a href="javascript:void(0)"
                                            class="btn btn-icon btn-sm btn-outline-primary btn-sm rounded-circle edit-project update-users-clients"
                                            data-id="<?php echo e($project->id); ?>">
                                            <span class="bx bx-edit"></span>
                                        </a>
                                    <?php endif; ?>
                                </ul>
                                </p>
                            </div>


                            <div class="row mt-2">
                                <div class="col-md-4 text-start">
                                    <i class="bx bx-calendar text-success"></i>
                                    <?= get_label('starts_at', 'Starts at') ?>:
                                    <?php echo e(format_date($project->start_date)); ?>

                                </div>
                                <div class="col-md-4 text-end">
                                    <i class="bx bx-calendar text-danger"></i>
                                    <?= get_label('ends_at', 'Ends at') ?>: <?php echo e(format_date($project->end_date)); ?>

                                </div>
                                <?php
                                    $endDate = \Carbon\Carbon::parse($project->end_date);
                                    $currentDate = \Carbon\Carbon::now();
                                    $daysDifference = $endDate->diffInDays($currentDate);
                                    $isOverdue = $currentDate->gt($endDate);
                                ?>
                                <div class="col-md-4 text-end">
                                    <span>
                                        <i
                                            class="bx bx-calendar-event text-<?php echo e($isOverdue ? 'warning' : 'primary'); ?>"></i>
                                        <b><?php echo e($daysDifference); ?></b>
                                        <?php echo e($isOverdue ? get_label('days_overdue', 'Days Overdue') : get_label('days_left', 'Days Left')); ?>

                                    </span>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div>
            <?php echo e($projects->links()); ?>

        </div>
    </div>

<!-- delete project modal -->
<?php else: ?>
<?php $type = 'projects'; ?>
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
<script>
    var add_favorite = '<?= get_label('add_favorite', 'Click to mark as favorite') ?>';
    var remove_favorite = '<?= get_label('remove_favorite', 'Click to remove from favorite') ?>';
</script>
<script src="<?php echo e(asset('assets/js/pages/project-grid.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Siddharth Gor\Test\resources\views/projects/grid_view.blade.php ENDPATH**/ ?>