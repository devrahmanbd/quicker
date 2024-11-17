<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="<?php echo e(asset('assets/')); ?>" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?php echo $__env->yieldContent('title'); ?> - <?php echo e($general_settings['company_title'] ?? 'Taskify - Saas'); ?></title>


    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon"
        href="<?php echo e(asset($general_settings['favicon'] ?? 'storage/logos/default_favicon.png')); ?>" />
    <?php echo $__env->make('front-end.include-css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="<?php echo e(asset('assets/vendor/libs/jquery/jquery.js')); ?>"></script>


</head>

<body class = "">
    <?php echo $__env->make('front-end.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <header>

    </header>
    <main class = "mt-4 ">

        <?php echo $__env->yieldContent('content'); ?>

    </main>
    <footer>
        <?php echo $__env->make('front-end.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </footer>


    <?php echo $__env->make('front-end.include-js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</body>


</html>
<?php /**PATH C:\Siddharth Gor\Test\resources\views/front-end/layout.blade.php ENDPATH**/ ?>