<?php $__env->startSection('title'); ?>
    <?= get_label('404', '404 Error') ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="card mt-4 text-center">
            <div class="card-body">
                <div class="misc-wrapper">
                    <h2 class="mx-2 mb-2"><?= get_label('page_not_found', 'Page Not Found') ?></h2>
                    <p class="mx-2 mb-4">
                    <?php echo e(get_label('page_not_found_description' , ' We apologize for the inconvenience. The page you requested was not found (404 error). It could be due to a broken link, incorrect URL, or the page may have been removed')); ?>.
                    </p>

                    <a href="<?php echo e(route('home.index')); ?>" class="btn btn-primary"><?= get_label('home', 'Home') ?></a>
                    <div class="mt-3">
                        <img src="<?php echo e(asset('/storage/man-with-laptop-light.png')); ?>" alt="page-misc-error-light"
                            width="500" class="img-fluid" data-app-dark-img="illustrations/page-misc-error-dark.png"
                            data-app-light-img="illustrations/page-misc-error-light.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Siddharth Gor\Test\resources\views/errors/404.blade.php ENDPATH**/ ?>