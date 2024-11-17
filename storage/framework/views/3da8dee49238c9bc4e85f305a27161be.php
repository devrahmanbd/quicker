<nav class="navbar navbar-expand-lg navbar-light bg-primary py-3 fixed-top">
    <div class="container">
        <div class="img-box">
            <a class="navbar-brand" href="/"><img src="<?php echo e(asset($general_settings['full_logo'])); ?>"
                    class="img-box"></a>
        </div>
        <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse"
            data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon mt-2">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button>

        <div class="collapse navbar-collapse w-100 pt-3 pb-2 py-lg-0 ms-lg-4 ps-lg-5" id="navigation">
            <ul class="justify-content-end navbar-nav navbar-nav-hover w-100">
                <li class="nav-item mx-2">
                    <a class="nav-link ps-2 d-flex justify-content-between cursor-pointer align-items-center <?php echo e(Request::is('/') ? 'active text-primary fw-bold ' : ''); ?>"
                        href="<?php echo e(route('frontend.index')); ?>"><?php echo e(get_label('home', 'Home')); ?></a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link ps-2 d-flex justify-content-between cursor-pointer align-items-center <?php echo e(Request::is('about-us') ? 'active text-primary fw-bold ' : ''); ?>"
                        href="<?php echo e(route('frontend.about_us')); ?>"><?php echo e(get_label('about_us', 'About Us')); ?></a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link ps-2 d-flex justify-content-between cursor-pointer align-items-center <?php echo e(Request::is('pricing') ? 'active text-primary fw-bold ' : ''); ?>"
                        href="<?php echo e(route('frontend.pricing')); ?>"><?php echo e(get_label('pricing_plans', 'Pricing Plans')); ?></a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link ps-2 d-flex justify-content-between cursor-pointer align-items-center <?php echo e(Request::is('features') ? 'active text-primary fw-bold ' : ''); ?>"
                        href="<?php echo e(route('frontend.features')); ?>"><?php echo e(get_label('features', 'Features')); ?></a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link ps-2 d-flex justify-content-between cursor-pointer align-items-center <?php echo e(Request::is('contact-us') ? 'active text-primary fw-bold ' : ''); ?>"
                        href="<?php echo e(route('frontend.contact_us')); ?>"><?php echo e(get_label('contact_us', 'Contact Us')); ?></a>
                </li>

                <li class="nav-item dropdown dropdown-hover mx-2">
                    <a class="nav-link ps-2 justify-content-between cursor-pointer align-items-center"
                        id="dropdownMenuPages1" data-bs-toggle="dropdown" aria-expanded="false" href="#">
                        <i class="fas fa-language" aria-hidden="true"></i> <img
                            src="https://demos.creative-tim.com/soft-ui-design-system/assets/img/down-arrow-dark.svg"
                            alt="down-arrow" class="arrow ms-1">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-animation  mt-0 mt-lg-3 p-3 border-radius-lg w-50"
                        aria-labelledby="dropdownMenuDocs">
                        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $checked = $language->code == app()->getLocale() ? "<i class='menu-icon tf-icons fa fa-check-square text-primary'></i>" : "<i class='menu-icon tf-icons fa fa-square text-solid'></i>"; ?>
                            <li class="dropdown-item  rounded">
                                <a class="text-dark"
                                    href="<?php echo e(route('languages.switch', ['code' => $language->code])); ?>">
                                    <?= $checked ?>
                                    <?php echo e($language->name); ?>

                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    </ul>
                </li>


            </ul>

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-2">
                <?php if(auth()->check()): ?>
                    <?php if(auth()->user()->hasRole('superadmin')): ?>
                        <a href="<?php echo e(route('superadmin.panel')); ?>"
                            class="btn btn-sm btn-round mb-0 me-1 mt-2 mt-md-0 bg-gradient-primary"><?php echo e(get_label('dashboard', 'Dashboard')); ?></a>
                    <?php else: ?>
                        <a href="<?php echo e(route('home.index')); ?>"
                            class="btn btn-sm btn-round mb-0 me-1 mt-2 mt-md-0 bg-gradient-primary"><?php echo e(get_label('dashboard', 'Dashboard')); ?></a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>"
                        class="btn btn-sm btn-round mb-0 me-1 mt-2 mt-md-0 bg-gradient-primary"><?php echo e(get_label('login', 'Login')); ?></a>
                <?php endif; ?>
            </div>


        </div>
    </div>
</nav>
<?php /**PATH C:\Siddharth Gor\Test\resources\views/front-end/navbar.blade.php ENDPATH**/ ?>