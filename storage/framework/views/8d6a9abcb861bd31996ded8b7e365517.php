<?php $__env->startSection('title'); ?>
    <?= get_label('home', 'Home') ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between mt-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item active">
                            <a href="<?php echo e(route('superadmin.panel')); ?>"><?= get_label('home', 'Home') ?></a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-money"></i></span>
                            </div>
                            <h1 class="ms-1 mb-0 text-primary"><?php echo e($totalMonthlyRevenue); ?></h1>
                        </div>
                        <p class="mb-1 fw-semibold h4"><?php echo get_label('monthly_revenue', 'Total Revenue (Monthly)'); ?>

                        </p>
                        <p class="mb-0">

                            <span
                                class="<?php echo e($Statuses['revenue']); ?> fw-bold fs-5"><?php echo e($percentageChange['revenue'] . '%'); ?></span>
                            <small class="text-muted"><?php echo e(get_label('percentageChange', 'Change from last month ')); ?></small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning"><i class="bx bxs-user"></i></span>
                            </div>
                            <h1 class="ms-1 mb-0 text-warning"><?php echo e($thisMonthCustomerCount); ?></h1>
                        </div>
                        <p class="mb-1 fw-semibold h4">
                            <?php echo get_label('monthly_customer', 'Total Customers <span class="text-muted">(Monthly)</span>'); ?>


                        </p>
                        <p class="mb-0">

                            <span
                                class="<?php echo e($Statuses['customer']); ?> fw-bold fs-5"><?php echo e($percentageChange['customer'] . '%'); ?></span>
                            <small class="text-muted"><?php echo e(get_label('percentageChange', 'Change from last month ')); ?></small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-spreadsheet"></i></span>
                            </div>
                            <h1 class="ms-1 mb-0 text-info"><?php echo e($thisMonthActiveSubscription); ?></h1>
                        </div>
                        <p class="mb-1 fw-semibold h4">
                            <?php echo get_label('monthly_subscription', 'Active Subscriptions <span class="text-muted">(Monthly)</span>'); ?>


                        </p>
                        <p class="mb-0">

                            <span
                                class="<?php echo e($Statuses['activeSubscription']); ?> fw-bold fs-5"><?php echo e($percentageChange['activeSubscription'] . '%'); ?></span>
                            <small
                                class="text-muted"><?php echo e(get_label('percentageChange', 'Change from last month ')); ?></small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-task"></i></span>
                            </div>
                            <h1 class="ms-1 mb-0 text-danger"><?php echo e($totalPlans); ?></h1>
                        </div>
                        <p class="mb-1 fw-semibold h4"><?php echo e(get_label('totalPlans', 'Total Plans')); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header justify-content-start pb-0">
                        <div class="card-title">
                            <h5 class="m-0 me-2"><?php echo e(get_label('total_customers', 'Total Customers')); ?></h5>
                            <p class=" text-muted mb-1"><?php echo e(get_label('total_count_of_customers', 'Total Count of Customers')); ?>Total Count of Customers</p>
                            <h3 class=" text-primary"><?php echo e($customerCounts); ?></h3>
                        </div>
                    </div>
                    <div class="card-body">


                        <div id="customerChart" class="mb-3 min-height-200"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header justify-content-start pb-0 ">
                        <div class="card-title">
                            <h5 class="m-0 me-2"><?php echo e(get_label('total_revrenue', 'Total Revenue')); ?></h5>
                            <p class=" text-muted mb-1"><?php echo e(get_label('total_revenue_obtained', 'Total Revenue obtained')); ?></p>
                            <p class="text-muted">
                                <?php echo e(get_settings('general_settings')['currency_symbol']); ?>

                                <span id="totalRevenue" class="h3 text-primary"></span>
                            </p>
                        </div>
                    </div>
                    <div class="card-body">

                        <div id="revenueChart" class="mb-3 min-height-200"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7 col-sm-12 mb-3 mt-3">
                <div class="card">
                    <div class="card-header justify-content-start pb-0">
                        <div class="card-title">
                            <h5 class="m-0 me-2"><?php echo e(get_label('subscription_rate', 'Subscription Rate')); ?></h5>
                            <p class=" text-muted mb-1">
                                <?php echo e(get_label('subcription_rate_of_plans', 'Subscription Rate of Plans')); ?></p>

                        </div>
                    </div>
                    <div class="card-body">
                        <div id="subscriptionRateChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 col-sm-12 mb-3 mt-3">
                <div class="card">
                    <div class="card-header justify-content-start pb-0">
                        <div class="card-title">
                            <h5 class="m-0 me-2"><?php echo e(get_label('plan_sales', 'Plan Sales')); ?></h5>
                            <p class="text-muted mb-0">
                                <?php echo e(get_label('get_active_subscription_per_plan', 'Active Subscriptions Per Plans')); ?></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="planSalesChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12 mb-3 mt-3">
                <div class="card">
                    <div class="card-header justify-content-start pb-0">
                        <div class="card-title">
                            <h4 class="m-0 me-2"><?php echo e(get_label('recent_transactions', 'Recent Transactions')); ?></h4>
                            <p class="text-muted mb-0">
                                <?php echo e(get_label('recently_added_transactions', 'Recently Added Transactions')); ?></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">

                            <table id="table" data-toggle="table" data-loading-template="loadingTemplate"
                                data-url="<?php echo e(route('chart.recentTransactions')); ?>" data-icons-prefix="bx"
                                data-icons="icons" data-show-refresh="true" data-total-field="total"
                                data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                data-side-pagination="server" data-show-columns="true" data-pagination="true"
                                data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
                                data-search="true" data-search-highlight="true" data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-visible='false' data-sortable="true" data-field="id">
                                            <?php echo e(get_label('id', 'ID')); ?></th>
                                        <th data-field="name"><?php echo e(get_label('name', 'Name')); ?></th>
                                        <th data-field="phone"><?php echo e(get_label('phone_number', 'Phone Number')); ?></th>
                                        <th data-field="email"><?php echo e(get_label('email', 'Email')); ?></th>
                                        <th data-field="amount"><?php echo e(get_label('amount', 'Amount')); ?></th>
                                        <th data-field="payment_method">
                                            <?php echo e(get_label('payment_method', 'Payment Method')); ?></th>
                                        <th data-field="status"><?php echo e(get_label('status', 'Status')); ?></th>
                                        <th data-field="created_at"><?php echo e(get_label('created_at', 'Created At')); ?></th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 mb-3 mt-3">
                <div class="card">
                    <div class="card-header justify-content-start pb-0">
                        <div class="card-title">
                            <h5 class="m-0 me-2"><?php echo e(get_label('top_customers', 'Top Customers')); ?></h5>
                            <p class="text-muted mb-0">
                                <?php echo e(get_label('topCustomers', 'Top 5 Customers by Maximum Purchase')); ?>

                            </p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">

                            <table id="table" data-toggle="table" data-loading-template="loadingTemplate"
                                data-url="<?php echo e(route('chart.bestCustomers')); ?>" data-icons-prefix="bx" data-icons="icons"
                                data-show-refresh="true" data-total-field="total" data-trim-on-search="false"
                                data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                data-side-pagination="server" data-show-columns="true" data-pagination="true"
                                data-search="true" data-search-highlight="true" data-sort-name="id"
                                data-sort-order="desc" data-mobile-responsive="true" data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-visible='false' data-sortable="true" data-field="id">
                                            <?php echo e(get_label('id', 'ID')); ?></th>
                                        <th data-field="name"><?php echo e(get_label('name', 'Name')); ?></th>
                                        <th data-field="phone"><?php echo e(get_label('phone_number', 'Phone Number')); ?></th>
                                        <th data-field="email"><?php echo e(get_label('email', 'Email')); ?></th>
                                        <th data-field="total_earnings">
                                            <?php echo e(get_label('total_earnings', 'Total Earnings')); ?></th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <script>
        var customerMonthlyCountUrl = "<?php echo e(route('chart.customer_monthly_count')); ?>";
        var revenueDataUrl = "<?php echo e(route('chart.revenue_data')); ?>";
        var subscriptionRateUrl = "<?php echo e(route('chart.subscription_rate')); ?>";
        var getActiveSubscriptionPerPlanUrl = "<?php echo e(route('chart.activeSubscriptionPerPlan')); ?>";
    </script>

    <script src="<?php echo e(asset('assets/js/apexcharts.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/pages/dashboard-charts.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /srv/http/localhost/resources/views/superadmin/dashborad/index.blade.php ENDPATH**/ ?>