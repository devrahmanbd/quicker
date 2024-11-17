<?php $__env->startSection('title'); ?>
    <?= get_label('checkout', 'Checkout') ?>
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
                            <a
                                href="<?php echo e(route('subscription-plan.index')); ?>"><?= get_label('subscription_plan', 'Subscription Plan') ?></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('subscription-plan.buy-plan')); ?>"><?= get_label('buy_plan', 'Buy Plan') ?></a>
                        </li>
                        <li class="breadcrumb-item active">
                            <?= get_label('checkout', 'Checkout') ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card ">
                <div class="row">
                    <div class="col-lg-6 card-body border-end">
                        <h4 class="mb-2"><?php echo e(get_label('checkout', 'Checkout')); ?></h4>
                        <p class="mb-0">
                            <?php echo e(get_label('checkoutDescription1', 'All plans include advanced tools and features to boost your product.')); ?>

                            <!-- Display Plan Details -->
                        <div class="row py-4 my-2 mb-2">
                            <div class="col-md mb-md-0 mb-2">
                                <h5 class="mb-1"><?php echo e(get_label('plan_details', 'Plan Details')); ?></h5>
                                <ul class="list-unstyled">
                                    <li>
                                        <h3 class="text-primary me-2"><?php echo e($plan->name); ?><small class="text-muted"> -
                                                <?php echo e($plan->description); ?></small> </h3>

                                    </li>
                                    <?php
                                        $modules = json_decode($plan->modules);
                                        $checkedModules = [];
                                        $uncheckedModules = [];
                                        foreach (config('taskify.modules') as $moduleName => $moduleData) {
                                            $included = in_array($moduleName, $modules);
                                            if ($included) {
                                                $checkedModules[] = [
                                                    'name' => $moduleName,
                                                    'icon' => $moduleData['icon'],
                                                ];
                                            } else {
                                                $uncheckedModules[] = [
                                                    'name' => $moduleName,
                                                    'icon' => $moduleData['icon'],
                                                ];
                                            }
                                        }
                                        $sortedModules = array_merge($checkedModules, $uncheckedModules);
                                    ?>
                                    <?php $__currentLoopData = $sortedModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $iconClass = in_array($module['name'], $modules)
                                                ? 'bx bx-check-circle text-success'
                                                : 'bx bxs-x-circle text-danger';
                                        ?>
                                        <li class="mb-2 text-dark">
                                            <i class="<?php echo e($iconClass); ?> me-2"></i>
                                            <i class="<?php echo e($module['icon']); ?>"></i>
                                            <?php echo e(ucfirst($module['name'])); ?>

                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <div class = "row">
                                <div class="col-md-6 mb-md-0 mb-2">
                                    <h6 class ="  text-capitalize">
                                        <?php echo e(get_label('max_projects', 'Maximum Number of projects')); ?>:
                                        <?php echo $plan->max_projects == -1
                                            ? '<span class="text-primary fw-semibold">' . get_label('unlimited', 'Unlimited') . '</span>'
                                            : '<span class="text-primary fw-semibold">' . $plan->max_projects . '</span>'; ?>

                                    </h6>
                                </div>
                                <div class="col-md-6 mb-md-0 mb-2">
                                    <h6 class ="  text-capitalize">
                                        <?php echo e(get_label('max_workspaces', 'Maximum Number of workspaces')); ?>:
                                        <?php echo $plan->max_worksapces == -1
                                            ? '<span class="text-primary fw-semibold">' . get_label('unlimited', 'Unlimited') . '</span>'
                                            : '<span class="text-primary fw-semibold">' . $plan->max_worksapces . '</span>'; ?>

                                    </h6>
                                </div>
                                <div class="col-md-6 mb-md-0 mb-2">
                                    <h6 class ="  text-capitalize">
                                        <?php echo e(get_label('max_team_members', 'Maximum Number of team members')); ?>:
                                        <?php echo $plan->max_team_members == -1
                                            ? '<span class="text-primary fw-semibold">' . get_label('unlimited', 'Unlimited') . '</span>'
                                            : '<span class="text-primary fw-semibold">' . $plan->max_team_members . '</span>'; ?>

                                    </h6>
                                </div>
                                <div class="col-md-6 mb-md-0 mb-2">
                                    <h6 class ="  text-capitalize">
                                        <?php echo e(get_label('max_clients', 'Maximum Number of clients')); ?>:
                                        <?php echo $plan->max_clients == -1
                                            ? '<span class="text-primary fw-semibold">' . get_label('unlimited', 'Unlimited') . '</span>'
                                            : '<span class="text-primary fw-semibold">' . $plan->max_clients . '</span>'; ?>

                                    </h6>
                                </div>
                            </div>
                        </div>



                        <h5 class="mt-2 mb-4"><?php echo e(get_label('billing_details', 'Billing Details')); ?></h5>
                        <div class="row">
                            <div class="col-md mb-md-0 mb-2">
                                <h5 class =" text-dark"> <?php echo e(get_label('tenure', 'Tenure')); ?> :
                                    <span class ="text-primary text-capitalize"><?php echo e(get_label($tenure, $tenure)); ?></span>
                                </h5>
                            </div>
                            <div class="col-md mb-md-0 mb-2">
                                <h5 class =" text-dark"> <?php echo e(get_label('price', 'Price')); ?>:
                                    <span class="text-primary text-capitalize">
                                        <?php
                                            $selectedTenure = $tenure; // Default tenure (you can change this based on your implementation)
                                            $selectedPrice = '';
                                            $finalPrice = ''; // Initialize selected price variable

                                            switch ($selectedTenure) {
                                                case 'monthly':
                                                    $originalPrice = $plan->monthly_price;
                                                    $discountedPrice = $plan->monthly_discounted_price;
                                                    break;
                                                case 'yearly':
                                                    $originalPrice = $plan->yearly_price;
                                                    $discountedPrice = $plan->yearly_discounted_price;
                                                    break;
                                                case 'lifetime':
                                                    $originalPrice = $plan->lifetime_price;
                                                    $discountedPrice = $plan->lifetime_discounted_price;
                                                    break;
                                                default:
                                                    $originalPrice = $plan->monthly_price; // Default to monthly price
                                                    $discountedPrice = $plan->monthly_discounted_price;
                                            }

                                            // Check if discounted price is greater than 0
                                            if ($discountedPrice > 0) {
                                                echo $currency_symbol .
                                                    '<del>' .
                                                    $originalPrice .
                                                    '</del> ' .
                                                    $discountedPrice;
                                                $finalPrice = $discountedPrice;
                                            } else {
                                                echo $currency_symbol . $originalPrice;
                                                $finalPrice = $originalPrice;
                                            }
                                        ?>
                                    </span>
                                </h5>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <?php if($finalPrice > 0): ?>
                            <div class="row">
                                <div class="col-md-12 mb-md-0 mb-2">
                                    <div class="d-flex flex-column mt-3 mb-3 payment_methods">
                                        <h5 class="mb-4 text-capitalize">
                                            <?php echo e(get_label('payment_methods', 'Payment Methods')); ?></h5>
                                        <div class="btn-group flex-wrap" role="group"
                                            aria-label="Horizontal button group">
                                            <input type="radio" class="btn-check" value="pay_pal" name="options"
                                                id="pay_pal" autocomplete="off" />
                                            <label class="btn btn-outline-primary btn-lg" for="pay_pal">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-uppercase fw-bold"><i class="bx bxl-paypal"></i>
                                                        <?php echo e(get_label('paypal', 'Paypal')); ?></span>
                                                </div>
                                            </label>

                                            <input type="radio" class="btn-check" name="options" value="phonepe"
                                                id="phonepe" autocomplete="off" />
                                            <label class="btn btn-outline-primary btn-lg" for="phonepe">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-uppercase fw-bold"><i class="bx bx-rupee"></i>
                                                        <?php echo e(get_label('phonepe', 'PhonePe')); ?></span>
                                                </div>
                                            </label>

                                            <input type="radio" class="btn-check" name="options" value="stripe"
                                                id="stripe" autocomplete="off" />
                                            <label class="btn btn-outline-primary btn-lg" for="stripe">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-uppercase fw-bold"><i class="bx bxl-stripe"></i>
                                                        <?php echo e(get_label('stripe', 'Stripe')); ?></span>
                                                </div>
                                            </label>

                                            <input type="radio" class="btn-check" name="options" value="paystack"
                                                id="paystack" autocomplete="off" />
                                            <label class="btn btn-outline-primary btn-lg" for="paystack">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-uppercase fw-bold"><i class="bx bx-coin-stack"></i>
                                                        <?php echo e(get_label('paystack', 'Paystack')); ?></span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>



                        <!-- Credit Card Info -->
                        <div id="form-credit-card">

                            <!-- Display credit card info form here -->
                        </div>
                    </div>

                    <div class="col-lg-6 card-body">
                        <h4 class="mb-2"><?php echo e(get_label('order_summary', 'Order Summary')); ?></h4>
                        <p class="pb-2 mb-0"><?php echo get_label(
                            'orderSummaryDecs',
                            'It can help you manage and service orders before,<br> during and after fulfilment',
                        ); ?>.</p>



                        <!-- Display Order Summary -->
                        <div id="orderSummaryDiv" class=" text-capitalize  p-4 rounded mt-4">
                            <input type = "hidden" name = "plan_name" value = "<?php echo e($plan->name); ?>" />
                            <input type = "hidden" name = "plan_id" value = "<?php echo e($plan->id); ?>" />
                            <input type = "hidden" name = "tenure" value = "<?php echo e($tenure); ?>" />
                            <input type = "hidden" name = "currency_symbol" value = "<?php echo e($currency_symbol); ?>" />
                            <input type = "hidden" name = "total_price" value = "<?php echo e($finalPrice); ?>" />
                            <input type = "hidden" name = "user_id" value = "<?php echo e(Auth::id()); ?>" />
                            <!-- Display order summary details here dynamically -->
                            <h5 class="text-primary" id="finalPlan"> </h5>
                            <h5 class="text-primary" id="finalPrice"> </h5>
                            <h5 class="text-primary" id="paymentMethod"></h5>
                        </div>

                        <div class="mt-4 ">
                            <a href="<?php echo e(route('subscription-plan.buy-plan')); ?>" id="changePlanBtn"
                                class="btn btn-outline-primary btn-sm d-none"><?php echo e(get_label('change_plan', 'Change Plan')); ?></a>
                        </div>
                        <!-- Total and Proceed with Payment Button -->


                        <p class="mt-4 pt-2">
                            <?php echo e(get_label(
                                'order_accept',
                                'By continuing, you accept to our Terms of Services and Privacy Policy.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        Please
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        note that payments are non-refundable',
                            )); ?>.
                        </p>

                        <div class="mt-4 d-none" id="proceedPaymentBtn">
                            <button data-url = "<?php echo e(route('subscription-plan.store')); ?>" id="paymentIntializeBtn"
                                type="submit"
                                class="btn btn-outline-primary col-12"><?php echo e(get_label('proceed_with_payment', 'Proceed with Payment')); ?></button>

                        </div>
                        <div class="mb-3 mt-3" id="paypal_div">
                            <div id="paypal-button-container"></div>
                        </div>
                        <!-- Modal for Stripe payment form -->
                        <div id="stripe_checkout">
                            <!-- Checkout will insert the payment form here -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script
        src="https://www.paypal.com/sdk/js?client-id=<?php echo e($paypal_settings['paypal_client_id']); ?>"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="<?php echo e(asset('assets/js/pages/subscription-plan.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Siddharth Gor\Test\resources\views/subscription-plan/checkout.blade.php ENDPATH**/ ?>