<?php
use App\Models\User;
use App\Models\Subscription;
use App\Models\Admin;
use App\Models\Ticket;
use App\Models\Workspace;
use App\Models\LeaveRequest;
use Chatify\ChatifyMessenger;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
$user = getAuthenticatedUser();
$adminId = getAdminIdByUserRole();
if (isAdminOrHasAllDataAccess()) {
    $workspaces = Workspace::where('admin_id', $adminId)->skip(0)->take(5)->get();
    $total_workspaces = Workspace::where('admin_id', $adminId)->count();
} else {
    $workspaces = $user->workspaces;
    $total_workspaces = count($workspaces);
    $workspaces = $user->workspaces()->skip(0)->take(5)->get();
}
$current_workspace = Workspace::find(session()->get('workspace_id'));
$current_workspace_title = $current_workspace->title ?? 'No workspace(s) found';
$messenger = new ChatifyMessenger();
$unread = $messenger->totalUnseenMessages();
$pending_todos_count = $user->todos(0)->count();
$ongoing_meetings_count = $user->meetings('ongoing')->count();
$query = LeaveRequest::where('status', 'pending')->where('workspace_id', session()->get('workspace_id'));
if (!is_admin_or_leave_editor()) {
    $query->where('user_id', $user->id);
}
$pendingLeaveRequestsCount = $query->count();
$prefix = null;
$openTicketsCount = Ticket::where('status', 'open')->count();
$currentRoute = Route::current();
if ($currentRoute) {
    $uriSegments = explode('/', $currentRoute->uri());
    $prefix = count($uriSegments) > 1 ? $uriSegments[0] : '';
}
?>
<?php if($user->hasrole('superadmin') || $user->hasrole('manager')): ?>
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme menu-container">
        <div class="app-brand demo">
            <a href="<?php echo e(route('home.index')); ?>" class="app-brand-link">
                <span class="app-brand-logo demo">
                    <img src="<?php echo e(asset($general_settings['full_logo'])); ?>" width="200px" alt="" />
                </span>
            </a>
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large d-block d-xl-none ms-auto">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
        </div>
        <div class="menu-inner-shadow"></div>
        <ul class="menu-inner py-1">
            <hr class="dropdown-divider" />
            <!-- Dashboard -->
            <li class="menu-item <?php echo e(Request::is($prefix . '/home') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('superadmin.panel')); ?>" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle text-danger"></i>
                    <div><?= get_label('dashboard', 'Dashboard') ?></div>
                </a>
            </li>
            <li class="menu-item <?php echo e(Request::is($prefix . '/customers') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('customers.index')); ?>" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-circle text-info"></i>
                    <div><?= get_label('customers', 'Customers') ?></div>
                </a>
            </li>
            <li class="menu-item <?php echo e(Request::is($prefix . '/plans') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('plans.index')); ?>" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-task text-primary"></i>
                    <div><?= get_label('plans', 'Plans') ?></div>
                </a>
            </li>
            <li class="menu-item <?php echo e(Request::is($prefix . '/subscriptions') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('subscriptions.index')); ?>" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-spreadsheet text-secondary"></i>
                    <div><?= get_label('subscriptions', 'Subscriptions') ?></div>
                </a>
            </li>
            <li class="menu-item <?php echo e(Request::is($prefix . '/transactions') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('transactions.index')); ?>" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-money text-danger"></i>
                    <div><?= get_label('transactions', 'Transactions') ?></div>
                </a>
            </li>

            <?php if(auth()->check() && auth()->user()->hasRole('superadmin')): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/managers') || Request::is($prefix . '/managers/*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('managers.index')); ?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user text-dark"></i>
                        <div>
                            <?php echo e(get_label('managers', 'Managers')); ?>

                        </div>
                    </a>
                </li>
            <?php endif; ?>


            <li class="menu-item <?php echo e(Request::is('support') || Request::is('support/*') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('support.index')); ?>" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-support text-info"></i>
                    <div>
                        <?php echo e(get_label('support', 'Support')); ?>

                        <?php if($openTicketsCount > 0): ?>
                            <span
                                class="badge badge-center bg-danger w-px-20 h-px-20 rounded-circle flex-shrink-0"><?php echo e($openTicketsCount); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            </li>

            <?php if(auth()->check() && auth()->user()->hasRole('superadmin')): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/settings') || Request::is($prefix . '/roles/*') || Request::is($prefix . '/settings/*') ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-cog text-success"></i>
                        <div data-i18n="User interface"><?= get_label('settings', 'Settings') ?></div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item <?php echo e(Request::is($prefix . '/settings/general') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('settings.index')); ?>" class="menu-link">
                                <div><?= get_label('general', 'General') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/settings/security') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('security.index')); ?>" class="menu-link">
                                <div><?= get_label('security_settings', 'Security Settings') ?></div>
                            </a>
                        </li>
                        <li
                            class="menu-item <?php echo e(Request::is($prefix . '/settings/permission') || Request::is('roles/*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('roles.index')); ?>" class="menu-link">
                                <div><?= get_label('permissions', 'Permissions') ?></div>
                            </a>
                        </li>
                        <li
                            class="menu-item <?php echo e(Request::is($prefix . '/settings/languages') || Request::is($prefix . '/settings/languages/create') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('languages.index')); ?>" class="menu-link">
                                <div><?= get_label('languages', 'Languages') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/settings/email') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('settings.email')); ?>" class="menu-link">
                                <div><?= get_label('email', 'Email') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is('settings/sms-gateway') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('sms_gateway.index')); ?>" class="menu-link">
                                <div><?= get_label('notifications_settings', 'Notifications Settings') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/settings/pusher') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('settings.pusher')); ?>" class="menu-link">
                                <div><?= get_label('pusher', 'Pusher') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/settings/media-storage') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('settings.media_storage')); ?>" class="menu-link">
                                <div><?= get_label('media_storage', 'Media storage') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/settings/payment-methods') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('payment_method.index')); ?>" class="menu-link">
                                <div><?= get_label('payment_methods', 'Payment Methods') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/settings/privacy-policy') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('privacy_policy.index')); ?>" class="menu-link">
                                <div><?= get_label('privacy_policy', 'Privacy Policy') ?></div>
                            </a>
                        </li>
                        <li
                            class="menu-item <?php echo e(Request::is($prefix . '/settings/terms-and-conditions') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('terms_and_conditions.index')); ?>" class="menu-link">
                                <div><?= get_label('terms_and_conditions', 'Terms And Conditions') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/settings/refund-policy') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('refund_policy.index')); ?>" class="menu-link">
                                <div><?= get_label('refund_policy', 'Refund Policy') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/settings/templates') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('templates.index')); ?>" class="menu-link">
                                <div><?= get_label('notification_templates', 'Notification Templates') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/settings/system-updater') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('update.index')); ?>" class="menu-link">
                                <div><?= get_label('system_updater', 'System updater') ?></div>
                            </a>
                        </li>

                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </aside>
<?php else: ?>
    <?php
        $modules = get_subscriptionModules();
    ?>
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme menu-container">
        <div class="app-brand demo">
            <a href="<?php echo e(route('home.index')); ?>" class="app-brand-link">
                <span class="app-brand-logo demo">
                    <img src="<?php echo e(asset($general_settings['full_logo'])); ?>" width="200px" alt="" />
                </span>
                <!-- <span class="app-brand-text demo menu-text fw-bolder ms-2">Quicker</span> -->
            </a>
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large d-block d-xl-none ms-auto">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
        </div>
        <div class="menu-search-bar px-3 py-2">
            <input type="text" id="menu-search" class="form-control"
                placeholder="<?php echo e(get_label('search_menu', 'Search Menu')); ?>...">
        </div>

        <div class="btn-group dropend px-2">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <?= $current_workspace_title ?>
            </button>
            <ul class="dropdown-menu">
                <?php $__currentLoopData = $workspaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workspace): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $checked = $workspace->id == session()->get('workspace_id') ? "<i class='menu-icon tf-icons bx bx-check-square text-primary'></i>" : "<i class='menu-icon tf-icons bx bx-square text-solid'></i>"; ?>
                    <li><a class="dropdown-item"
                            href="<?php echo e(route('workspaces.switch', ['id' => $workspace->id])); ?>"><?= $checked ?><?php echo e($workspace->title); ?>

                            <?= $workspace->is_primary ? ' <span class="badge bg-success">' . get_label('primary', 'Primary') . '</span>' : '' ?></a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <?php if($user->can('manage_workspaces')): ?>
                    <li><a class="dropdown-item" href="<?php echo e(route('workspaces.index')); ?>"><i
                                class='menu-icon tf-icons bx bx-bar-chart-alt-2 text-success'></i><?= get_label('manage_workspaces', 'Manage workspaces') ?>
                            <?= $total_workspaces > 5 ? '<span class="badge badge-center bg-primary"> + ' . ($total_workspaces - 5) . '</span>' : '' ?></a>
                    </li>
                    <?php if($user->can('create_workspaces')): ?>
                        <li><a class="dropdown-item" href="<?php echo e(route('workspaces.create')); ?>"><i
                                    class='menu-icon tf-icons bx bx-plus text-warning'></i><?= get_label('create_workspace', 'Create workspace') ?></a></span>
                        </li>
                        <!-- <li><span data-bs-toggle="modal" data-bs-target="#create_workspace_modal"><a class="dropdown-item" href="javascript:void(0);"><i class='menu-icon tf-icons bx bx-plus text-warning'></i><?= get_label('create_workspace', 'Create workspace') ?></a></span></li> -->
                    <?php endif; ?>
                    <?php if($user->can('create_workspaces')): ?>
                        <li><a class="dropdown-item"
                                href=" <?php echo e(route('workspaces.edit', ['id' => session()->get('workspace_id')])); ?>"><i
                                    class='menu-icon tf-icons bx bx-edit text-info'></i><?= get_label('edit_workspace', 'Edit workspace') ?></a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <li><a class="dropdown-item" href="#"
                        data-route-prefix="<?php echo e(Route::getCurrentRoute()->getPrefix()); ?>" id="remove-participant"><i
                            class='menu-icon tf-icons bx bx-exit text-danger'></i><?= get_label('remove_me_from_workspace', 'Remove me from workspace') ?></a>
                </li>
            </ul>
        </div>
        <ul class="menu-inner py-1" id="dynamic-menu">
            <hr class="dropdown-divider" />
            <!-- Dashboard -->
            <li class="menu-item <?php echo e(Request::is($prefix . '/home') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('home.index')); ?>" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle text-danger"></i>
                    <div><?= get_label('dashboard', 'Dashboard') ?></div>
                </a>
            </li>
            <?php if($user->can('manage_projects')): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/projects') || Request::is($prefix . '/tags/*') || Request::is($prefix . '/projects/*') ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-briefcase-alt-2 text-success"></i>
                        <div><?= get_label('projects', 'Projects') ?></div>
                        <!-- Pin Icon -->
                    </a>
                    <ul class="menu-sub">
                        <li
                            class="menu-item <?php echo e(Request::is($prefix . '/projects') || (Request::is($prefix . '/projects/*') && !Request::is($prefix . '/projects/favorite')) ? 'active' : ''); ?>">
                            <?php
                                $defaultView = getUserPreferences('projects', 'default_view');
                                if ($defaultView == 'list') {
                                    $url = route('projects.list_view');
                                } elseif ($defaultView == 'grid') {
                                    $url = route('projects.index');
                                } elseif ($defaultView == 'kanban_view') {
                                    $url = route('projects.kanban_view');
                                } else {
                                    $url = route('projects.index');
                                }
                            ?>
                            <a href="<?php echo e($url); ?>" class="menu-link">
                                <div><?= get_label('manage_projects', 'Manage projects') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/projects/favorite') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('projects.index', ['type' => 'favorite'])); ?>" class="menu-link">
                                <div><?= get_label('favorite_projects', 'Favorite projects') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/tags/*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('tags.index')); ?>" class="menu-link">
                                <div><?= get_label('tags', 'Tags') ?></div>
                            </a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if(in_array('tasks', $modules)): ?>
                <?php if($user->can('manage_tasks')): ?>
                    <?php
                        $defaultView = getUserPreferences('tasks', 'default_view');
                        if ($defaultView == 'tasks/draggable') {
                            $url = route('tasks.draggable');
                        } elseif ($defaultView == 'calendar-view') {
                            $url = route('tasks.calendar_view');
                        } else {
                            $url = route('tasks.index');
                        }
                    ?>
                    <li
                        class="menu-item <?php echo e(Request::is($prefix . '/tasks') || Request::is($prefix . '/tasks/*') ? 'active' : ''); ?>">
                        <a href="<?php echo e($url); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-task text-primary"></i>
                            <div><?= get_label('tasks', 'Tasks') ?></div>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(in_array('chat', $modules)): ?>
                <?php if(Auth::guard('web')->check()): ?>
                    <li
                        class="menu-item <?php echo e(Request::is($prefix . '/chat') || Request::is($prefix . '/chat/*') ? 'active' : ''); ?>">
                        <a href="/chat" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-chat text-warning"></i>
                            <div><?= get_label('chat', 'Chat') ?> <span
                                    class="badge badge-center bg-danger w-px-20 h-px-20 flex-shrink-0"><?php echo e($unread); ?></span>
                            </div>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(in_array('finance', $modules)): ?>
                <?php if(
                    $user->can('manage_estimates_invoices') ||
                        $user->can('manage_expenses') ||
                        $user->can('manage_payment_methods') ||
                        $user->can('manage_expense_types') ||
                        $user->can('manage_payments') ||
                        $user->can('manage_taxes') ||
                        $user->can('manage_units') ||
                        $user->can('manage_items')): ?>
                    <li
                        class="menu-item <?php echo e(Request::is($prefix . '/estimates-invoices') || Request::is($prefix . '/estimates-invoices/*') || Request::is($prefix . '/taxes') || Request::is($prefix . '/payment-methods') || Request::is($prefix . '/payments') || Request::is($prefix . '/units') || Request::is($prefix . '/items') || Request::is($prefix . '/expenses') || Request::is($prefix . '/expenses/*') ? 'active open' : ''); ?>">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-box text-success"></i>
                            <?= get_label('finance', 'Finance') ?>
                        </a>
                        <ul class="menu-sub">
                            <?php if($user->can('manage_expenses')): ?>
                                <li
                                    class="menu-item <?php echo e(Request::is($prefix . '/expenses') || Request::is($prefix . '/expenses/*') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('expenses.index')); ?>" class="menu-link">
                                        <div><?= get_label('expenses', 'Expenses') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($user->can('manage_expense_types')): ?>
                                <li
                                    class="menu-item <?php echo e(Request::is($prefix . '/expenses/expense-types') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('expenses-type.index')); ?>" class="menu-link">
                                        <div><?= get_label('expense_type', 'Expense type') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($user->can('manage_estimates_invoices')): ?>
                                <li
                                    class="menu-item <?php echo e(Request::is($prefix . '/estimates-invoices') || Request::is($prefix . '/estimates-invoices/*') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('estimates-invoices.index')); ?>" class="menu-link">
                                        <div><?= get_label('etimates_invoices', 'Estimates/Invoices') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($user->can('manage_payments')): ?>
                                <li class="menu-item <?php echo e(Request::is($prefix . '/payments') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('payments.index')); ?>" class="menu-link">
                                        <div><?= get_label('payments', 'Payments') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($user->can('manage_payment_methods')): ?>
                                <li class="menu-item <?php echo e(Request::is($prefix . '/payment-methods') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('paymentMethods.index')); ?>" class="menu-link">
                                        <div><?= get_label('payment_methods', 'Payment methods') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($user->can('manage_taxes')): ?>
                                <li class="menu-item <?php echo e(Request::is($prefix . '/taxes') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('taxes.index')); ?>" class="menu-link">
                                        <div><?= get_label('taxes', 'Taxes') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($user->can('manage_units')): ?>
                                <li class="menu-item <?php echo e(Request::is($prefix . '/units') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('units.index')); ?>" class="menu-link">
                                        <div><?= get_label('units', 'Units') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($user->can('manage_items')): ?>
                                <li class="menu-item <?php echo e(Request::is($prefix . '/items') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('items.index')); ?>" class="menu-link">
                                        <div><?= get_label('items', 'Items') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(in_array('contracts', $modules)): ?>
                <?php if($user->can('manage_contracts') || $user->can('manage_contract_types')): ?>
                    <li
                        class="menu-item <?php echo e(Request::is($prefix . '/contracts') || Request::is($prefix . '/contracts/*') || Request::is('contract-types') ? 'active open' : ''); ?>">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-news text-success"></i>
                            <?= get_label('contracts', 'Contracts') ?>
                        </a>
                        <ul class="menu-sub">
                            <?php if($user->can('manage_contracts')): ?>
                                <li
                                    class="menu-item <?php echo e(Request::is($prefix . '/contracts') || Request::is($prefix . '/contracts') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('contracts.index')); ?>" class="menu-link">
                                        <div><?= get_label('manage_contracts', 'Manage contracts') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($user->can('manage_contract_types')): ?>
                                <li
                                    class="menu-item <?php echo e(Request::is($prefix . '/contracts/contract-types') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('contracts.contract_types')); ?>" class="menu-link">
                                        <div><?= get_label('contract_types', 'Contract types') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(in_array('payslips', $modules)): ?>
                <?php if($user->can('manage_payslips') || $user->can('manage_allowances') || $user->can('manage_deductions')): ?>
                    <li
                        class="menu-item <?php echo e(Request::is($prefix . '/payslips') || Request::is($prefix . '/payslips/*') || Request::is($prefix . '/payment-methods') || Request::is($prefix . '/allowances') || Request::is($prefix . '/deductions') ? 'active open' : ''); ?>">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-box text-warning"></i>
                            <?= get_label('payslips', 'Payslips') ?>
                        </a>
                        <ul class="menu-sub">
                            <?php if($user->can('manage_payslips')): ?>
                                <li
                                    class="menu-item <?php echo e(Request::is($prefix . '/payslips') || Request::is($prefix . '/payslips/*') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('payslips.index')); ?>" class="menu-link">
                                        <div><?= get_label('manage_payslips', 'Manage payslips') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($user->can('manage_allowances')): ?>
                                <li class="menu-item <?php echo e(Request::is($prefix . '/allowances') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('allowances.index')); ?>" class="menu-link">
                                        <div><?= get_label('allowances', 'Allowances') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($user->can('manage_deductions')): ?>
                                <li class="menu-item <?php echo e(Request::is($prefix . '/deductions') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('deductions.index')); ?>" class="menu-link">
                                        <div><?= get_label('deductions', 'Deductions') ?></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'admin')): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/reports') || Request::is($prefix . '/reports/*') ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bxs-report text-dark"></i>
                        <?= get_label('reports', 'Reports') ?>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item <?php echo e(Request::is($prefix . '/reports/projects-report') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('reports.projects-report')); ?>" class="menu-link">
                                <div><?= get_label('projects_report', 'Projects Report') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/reports/tasks-report') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('reports.tasks-report')); ?>" class="menu-link">
                                <div><?= get_label('tasks_report', 'Tasks Report') ?></div>
                            </a>
                        </li>

                        <li class="menu-item <?php echo e(Request::is($prefix . '/reports/invoices-report') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('reports.invoices-report')); ?>" class="menu-link">
                                <div><?= get_label('invoices_report', 'Invoices Report') ?></div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Request::is($prefix . '/reports/leaves-report') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('reports.leaves-report')); ?>" class="menu-link">
                                <div><?= get_label('leaves_report', 'Leaves Report') ?></div>
                            </a>
                        </li>
                        <li
                            class="menu-item <?php echo e(Request::is($prefix . '/reports/income-vs-expense-report') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('reports.income-vs-expense-report')); ?>" class="menu-link">
                                <div><?= get_label('income_vs_expense_report', 'Income vs. Expense Report') ?></div>
                            </a>
                        </li>

                    </ul>
                </li>
            <?php endif; ?>
            <?php if(in_array('notes', $modules)): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/notes') || Request::is($prefix . '/notes/*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('notes.index')); ?>" class="menu-link">
                        <i class='menu-icon tf-icons bx bx-notepad text-primary'></i>
                        <div><?= get_label('notes', 'Notes') ?></div>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(in_array('todos', $modules)): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/todos') || Request::is($prefix . '/todos/*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('todos.index')); ?>" class="menu-link">
                        <i class='menu-icon tf-icons bx bx-list-check text-dark'></i>
                        <div><?= get_label('todos', 'Todos') ?> <span
                                class="badge badge-center bg-danger w-px-20 h-px-20 flex-shrink-0"><?php echo e($pending_todos_count); ?></span>
                        </div>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(in_array('meetings', $modules)): ?>
                <?php if($user->can('manage_meetings')): ?>
                    <li
                        class="menu-item <?php echo e(Request::is($prefix . '/meetings') || Request::is($prefix . '/meetings/*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('meetings.index')); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-shape-polygon text-success"></i>
                            <div><?= get_label('meetings', 'Meetings') ?> <span
                                    class="badge badge-center bg-success w-px-20 h-px-20 flex-shrink-0"><?php echo e($ongoing_meetings_count); ?></span>
                            </div>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if($user->can('manage_users')): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/users') || Request::is($prefix . '/users/*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('users.index')); ?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-group text-primary"></i>
                        <div><?= get_label('users', 'Users') ?></div>
                    </a>
                </li>
            <?php endif; ?>
            <?php if($user->can('manage_clients')): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/clients') || Request::is($prefix . '/clients/*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('clients.index')); ?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-group text-warning"></i>
                        <div><?= get_label('clients', 'Clients') ?></div>
                    </a>
                </li>
            <?php endif; ?>
            <?php if($user->can('manage_statuses')): ?>
                <li class="menu-item <?php echo e(Request::is($prefix . '/status/manage') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('status.index')); ?>" class="menu-link">
                        <i class='menu-icon tf-icons bx bx-grid-small text-secondary'></i>
                        <div><?= get_label('statuses', 'Statuses') ?></div>
                    </a>
                </li>
            <?php endif; ?>
            <?php if($user->can('manage_priorities')): ?>
                <li class="menu-item <?php echo e(Request::is($prefix . '/priority/manage') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('priority.manage')); ?>" class="menu-link">
                        <i class='menu-icon tf-icons bx bx-up-arrow-alt text-success'></i>
                        <div><?= get_label('priorities', 'Priorities') ?></div>
                    </a>
                </li>
            <?php endif; ?>
            <?php if($user->can('manage_workspaces')): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/workspaces') || Request::is($prefix . '/workspaces/*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('workspaces.index')); ?>" class="menu-link">
                        <i class='menu-icon tf-icons bx bx-check-square text-danger'></i>
                        <div><?= get_label('workspaces', 'Workspaces') ?></div>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(Auth::guard('web')->check()): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/leave-requests') || Request::is($prefix . '/leave-requests/*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('leave_requests.index')); ?>" class="menu-link">
                        <i class='menu-icon tf-icons bx bx-right-arrow-alt text-danger'></i>
                        <div><?= get_label('leave_requests', 'Leave requests') ?> <span
                                class="badge badge-center bg-danger w-px-20 h-px-20 flex-shrink-0"><?php echo e($pendingLeaveRequestsCount); ?></span>
                        </div>
                    </a>
                </li>
            <?php endif; ?>
            <?php if($user->can('manage_activity_log')): ?>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/activity-log') || Request::is($prefix . '/activity-log/*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('activity_log.index')); ?>" class="menu-link">
                        <i class='menu-icon tf-icons bx bx-line-chart text-warning'></i>
                        <div><?= get_label('activity_log', 'Activity log') ?></div>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'admin')): ?>
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text"><?php echo e(get_label('admin_settings', 'Admin Settings')); ?></span>
                </li>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/subcription-plan') || Request::is($prefix . '/subscription-plan/*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('subscription-plan.index')); ?>" class="menu-link">
                        <i class='menu-icon tf-icons bx bx-task text-primary'></i>
                        <div><?= get_label('subscription_plan', 'Subscription Plan') ?>
                        </div>
                    </a>
                </li>
                <li
                    class="menu-item <?php echo e(Request::is($prefix . '/settings') || Request::is($prefix . '/settings/*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin_settings.index')); ?>" class="menu-link">
                        <i class='menu-icon tf-icons bx bx-cog text-dark'></i>
                        <div><?= get_label('settings', 'Settings') ?>
                        </div>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </aside>
<?php endif; ?>
<?php /**PATH /srv/http/localhost/resources/views/components/menu.blade.php ENDPATH**/ ?>