<footer class="footer py-2 bg-gradient-dark position-relative overflow-hidden fixed-bottom">
    <img src="/assets/front-end/img/gallery/pattern-lines.svg" alt="pattern-lines"
        class="position-absolute  w-100 opacity-2 img-container">
    <div class="container position-relative mt-5">
        <div class="row ">
            <div class="col-md-6 mb-5 mb-lg-0">
                <div class="img-box">
                    <img src="<?php echo e(asset($general_settings['footer_logo'])); ?>">

                </div>
                <p class="mb-1 pb-1 text-white">
                <ul class="list-unstyled text-light mb-md-4">
                    <li class="lh-lg"><?php echo $general_settings['company_address']; ?></li>

                    <li class="lh-lg">
                        <a href=mailto="<?php echo e($general_settings['support_email']); ?>" class = "text-light"> <i
                                class="far fa-envelope"></i>
                            <?php echo e($general_settings['support_email']); ?> </a>
                    </li>
                </ul>
                </p>

            </div>

            <div class="col-md-6 ms-lg-0 mb-md-0 mb-4">

                <div class = "d-flex justify-content-center row">
                    <h5 class="lh-lg fw-bold text-light text-center"><?php echo e(get_label('quick_links', 'Quick Links')); ?></h5>
                    <div class="col-4">
                        <ul class="list-unstyled mb-md-2 mx-3">

                            <li class="lh-lg"><a href="<?php echo e(route('frontend.index')); ?>"
                                    class="d-inline-flex align-items-center text-light "><i
                                        class="fas fa-arrow-right me-2"></i><?php echo e(get_label('home', 'Home')); ?></a></li>
                            <li class="lh-lg"><a href="<?php echo e(route('frontend.contact_us')); ?>"
                                    class="d-inline-flex align-items-center text-light "><i
                                        class="fas fa-arrow-right me-2"></i><?php echo e(get_label('contact_us', 'Contact Us')); ?></a>
                            </li>
                            <li class="lh-lg"><a href="<?php echo e(route('frontend.about_us')); ?>"
                                    class="d-inline-flex align-items-center text-light "><i
                                        class="fas fa-arrow-right me-2"></i><?php echo e(get_label('about_us', 'About Us')); ?></a>
                            </li>
                            <li class="lh-lg"><a href="<?php echo e(route('frontend.refund_policy')); ?>"
                                    class="d-inline-flex align-items-center text-light "><i
                                        class="fas fa-arrow-right me-2"></i><?php echo e(get_label('refund_policy', 'Refund Policy')); ?></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-5">


                        <ul class="list-unstyled mb-md-2 mx-3">
                            <li class="lh-lg"><a href="<?php echo e(route('frontend.features')); ?>"
                                    class="d-inline-flex align-items-center text-light "><i
                                        class="fas fa-arrow-right me-2"></i><?php echo e(get_label('features', 'Features')); ?></a>
                            </li>
                            <li class="lh-lg"><a href="<?php echo e(route('frontend.pricing')); ?>"
                                    class="d-inline-flex align-items-center text-light "><i
                                        class="fas fa-arrow-right me-2"></i><?php echo e(get_label('pricing_plans', 'Pricing')); ?></a>
                            </li>

                            <li class="lh-lg"><a href="<?php echo e(route('frontend.privacy_policy')); ?>"
                                    class="d-inline-flex align-items-center text-light "><i
                                        class="fas fa-arrow-right me-2"></i><?php echo e(get_label('privacy_policy', 'Privacy Policy')); ?></a>
                            </li>
                            <li class="lh-lg"><a href="<?php echo e(route('frontend.terms_and_condition')); ?>"
                                    class="d-inline-flex align-items-center text-light "><i
                                        class="fas fa-arrow-right me-2"></i><?php echo e(get_label('terms_and_conditions', 'Terms & Conditions')); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <hr class="horizontal light mb-4 mt-lg-4">
        <div class="row d-flex justify-content-center row text-capitalize text-center text-white">
            <div class="col-md-auto">
                <p class="">&copy; <?php echo e(date('Y')); ?> <?php echo e($general_settings['company_title']); ?></p>
            </div>
            <div class="col-md-auto text-white">

                <?php echo $general_settings['footer_text']; ?>


            </div>
        </div>


    </div>
</footer>
<?php /**PATH C:\Siddharth Gor\Test\resources\views/front-end/footer.blade.php ENDPATH**/ ?>