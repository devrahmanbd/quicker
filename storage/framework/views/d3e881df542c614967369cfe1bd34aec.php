<!-- Navbar -->

<?php

use App\Models\Language;
$authenticatedUser = getAuthenticatedUser();

$current_language = Language::where('code', app()->getLocale())->get(['name', 'code']);
$default_language = getAuthenticatedUser()->lang;
$unreadNotificationsCount = $authenticatedUser->notifications->where('pivot.read_at', null)->count();
$unreadNotifications = $authenticatedUser->notifications()->wherePivot('read_at', null)->getQuery()->orderBy('id', 'desc')->take(3)->get();

?>
<?php if (auth('web')->check() || auth('client')->check()): ?>
<div id="section-not-to-print">
    <nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
        id="layout-navbar">

        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-xl-0 d-xl-none me-3">
            <a class="nav-item nav-link me-xl-4 px-0" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
            </a>
        </div>
        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

            <div class="nav-item">
                <i class="bx bx-search"></i><span id="global-search"></span>
            </div>


            

            <ul class="navbar-nav align-items-center ms-auto flex-row">
                <?php if(config('constants.ALLOW_MODIFICATION') === 0): ?>
                    <li><span class="badge bg-danger demo-mode">Demo mode</span><span class="demo-mode-icon-only">
                            <i class='bx bx-error-alt text-danger'></i>
                        </span></li>
                <?php endif; ?>

                <?php if(!$authenticatedUser->hasRole('superadmin')): ?>
                    <li class="nav-item navbar-dropdown dropdown-user dropdown ml-1">

                    <li class="nav-item navbar-dropdown dropdown">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                            data-bs-toggle="dropdown">
                            <i class='bx bx-bell bx-sm'></i> <span id="unreadNotificationsCount"
                                class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger <?php echo e($unreadNotificationsCount > 0 ? '' : 'd-none'); ?>"><?php echo e($unreadNotificationsCount); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end w-px-400">
                            <li class="dropdown-header dropdown-header-highlighted"><i
                                    class="bx bx-bell bx-md me-2"></i><?php echo e(get_label('notifications', 'Notifications')); ?>

                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                            </li>
                            <div id="unreadNotificationsContainer">
                                <?php if($unreadNotificationsCount > 0): ?>
                                    <?php $__currentLoopData = $unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <?php
                                                // Mapping of notification types to their respective routes
                                                $routes = [
                                                    'project' => '/master-panel/projects/information/$id',
                                                    'task' => '/master-panel/tasks/information/$id',
                                                    'workspace' => '/master-panel/workspaces',
                                                    'meeting' => '/master-panel/meetings',
                                                    'project_comment_mention' =>
                                                        '/master-panel/projects/information/$id',
                                                    'task_comment_mention' => '/master-panel/tasks/information/$id',
                                                    'leave_request' => '/master-panel/leave-requests',
                                                ];

                                                // Fallback route if the type is not matched in the array
                                                $defaultRoute = '/master-panel/notifications';

                                                // Determine the base URL based on the notification type, or fallback to the default
                                                $baseUrl = $routes[$notification->type] ?? $defaultRoute;

                                                // Check if the URL contains the '$id' placeholder and replace it with the actual id if available
                                                if (
                                                    strpos($baseUrl, '$id') !== false &&
                                                    !empty($notification->type_id)
                                                ) {
                                                    $url = str_replace('$id', $notification->type_id, $baseUrl);
                                                } else {
                                                    $url = $baseUrl; // No id to append or not a route that requires it
                                                }
                                            ?>


                                            <a class="dropdown-item update-notification-status"
                                                data-id="<?php echo e($notification->id); ?>" href="<?php echo e($url); ?>">



                                                <div class="d-flex align-items-center">
                                                    <div class="fw-semibold me-auto"><?php echo e($notification->title); ?> <small
                                                            class="text-muted mx-2"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                                                    </div>
                                                    <i class="bx bx-bell me-2"></i>
                                                </div>
                                                <div class="mt-2">
                                                    <?php echo e(strlen($notification->message) > 50 ? substr($notification->message, 0, 50) . '...' : $notification->message); ?>

                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <li class="d-flex align-items-center justify-content-center p-5">
                                        <span><?php echo e(get_label('no_unread_notifications', 'No unread notifications')); ?></span>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                <?php endif; ?>
                            </div>
                            <li class="d-flex justify-content-between">
                                <a href="<?php echo e(route('notifications.index')); ?>"
                                    class="p-3"><b><?php echo e(get_label('view_all', 'View all')); ?></b></a>

                                <a href="#" class="p-3 text-end"
                                    id="mark-all-notifications-as-read"><b><?php echo e(get_label('mark_all_as_read', 'Mark all as read')); ?></b></a>
                            </li>
                        </ul>
                    </li>
                    </li>
                <?php endif; ?>


                <li class="nav-item navbar-dropdown dropdown-user dropdown ml-1">

                    <div class="btn-group dropend px-1">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="icon-only"><i class='bx bx-globe'></i></span> <span
                                class="language-name"><?= $current_language[0]['name'] ?? '' ?></span>
                        </button>
                        <ul class="dropdown-menu language-dropdown">
                            <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $checked = $language->code == app()->getLocale() ? "<i class='menu-icon tf-icons bx bx-check-square text-primary'></i>" : "<i class='menu-icon tf-icons bx bx-square text-solid'></i>"; ?>
                                <li class="dropdown-item">
                                    <a href="<?php echo e(route('languages.switch', ['code' => $language->code])); ?>">
                                        <?= $checked ?>

                                        <?php echo e($language->name); ?>


                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <?php if($current_language[0]['code'] == $default_language): ?>
                                <li><span class="badge bg-primary mx-5 mb-1 mt-1" data-bs-toggle="tooltip"
                                        data-bs-placement="left"
                                        data-bs-original-title="<?= get_label('current_language_is_your_primary_language', 'Current language is your primary language') ?>"><?= get_label('primary', 'Primary') ?></span>
                                </li>
                            <?php else: ?>
                                <a href="javascript:void(0);"><span class="badge bg-secondary mx-5 mb-1 mt-1"
                                        id="set-as-default" data-lang="<?php echo e(app()->getLocale()); ?>"
                                        data-url="<?php echo e(route('languages.set_default')); ?>" data-bs-toggle="tooltip"
                                        data-bs-placement="left"
                                        data-bs-original-title="<?= get_label('set_current_language_as_your_primary_language', 'Set current language as your primary language') ?>"><?= get_label('set_as_primary', 'Set as primary') ?></span></a>
                            <?php endif; ?>

                        </ul>
                    </div>
                    </button>
                </li>

                <li class="nav-item navbar-dropdown dropdown-user dropdown mx-2 mt-3">
                    <p class="nav-item">
                        <span class="nav-mobile-hidden"><?= get_label('hi', 'Hi') ?>👋</span>
                        <span class="nav-mobile-hidden"><?php echo e(getAuthenticatedUser()->first_name); ?></span>
                    </p>

                </li>



                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="<?php echo e(getAuthenticatedUser()->photo ? asset('storage/' . getAuthenticatedUser()->photo) : asset('storage/photos/no-image.jpg')); ?>"
                                alt class="w-px-40 rounded-circle" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex">
                                    <div class="me-3 flex-shrink-0">
                                        <div class="avatar avatar-online">
                                            <img src="<?php echo e(getAuthenticatedUser()->photo ? asset('storage/' . getAuthenticatedUser()->photo) : asset('storage/photos/no-image.jpg')); ?>"
                                                alt class="w-px-40 rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block"><?php echo e(getAuthenticatedUser()->first_name); ?>

                                            <?php echo e(getAuthenticatedUser()->last_name); ?></span>
                                        <small class="text-muted text-capitalize">
                                            <?php echo e(getAuthenticatedUser()->getRoleNames()->first()); ?>

                                        </small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item"
                                href="<?php echo e(Request::segment(1) === 'superadmin'
                                    ? route('profile_superadmin.show', ['user' => getAuthenticatedUser()->id])
                                    : route('profile.show', ['user' => getAuthenticatedUser()->id])); ?>">
                                <i class="bx bx-user me-2"></i>
                                <span class="align-middle"><?= get_label('my_profile', 'My Profile') ?></span>
                            </a>
                        </li>
                        <?php if(!(Request::segment(1) == 'superadmin')): ?>
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('preferences.index')); ?>">
                                    <i class='bx bx-cog me-2'></i>
                                    <span class="align-middle"><?= get_label('preferences', 'Preferences') ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('clear.cache')); ?>"
                                onclick="event.preventDefault(); document.getElementById('clear-cache-form').submit();">
                                <i class='bx bx-refresh me-2'></i>
                                <span class="align-middle"><?= get_label('clear_cache', 'Clear Cache') ?></span>
                            </a>
                            <form id="clear-cache-form" action="<?php echo e(route('clear.cache')); ?>" method="POST"
                                style="display: none;">
                                <?php echo csrf_field(); ?>
                            </form>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <form action="<?php echo e(route('logout')); ?>" method="POST" class="dropdown-item">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i
                                        class="bx bx-log-out-circle"></i> <?= get_label('logout', 'Logout') ?></button>

                            </form>
                        </li>
                    </ul>
                </li>

                <!--/ User -->
            </ul>
        </div>
    </nav>
</div>
<?php else: ?>
<?php endif; ?>
<script>
    var label_search = '<?= get_label('search', 'Search') ?>';
</script>
<script src="<?php echo e(asset('assets/js/pages/navbar.js')); ?>"></script>

<!-- / Navbar -->
<?php /**PATH C:\Siddharth Gor\Test\resources\views/partials/_navbar.blade.php ENDPATH**/ ?>