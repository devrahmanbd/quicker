<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="<?php echo e(asset('assets/')); ?>" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?php echo $__env->yieldContent('title'); ?> - <?php echo e($general_settings['company_title'] ?? 'Quicker - Saas'); ?></title>
    <meta name="description" content="" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon"
        href="<?php echo e(asset($general_settings['favicon'] ?? 'storage/logos/default_favicon.png')); ?>" />
    <!-- Fonts -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/google-fonts.css')); ?>" />
    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/boxicons.css')); ?>" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/core.css')); ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/theme-default.css')); ?>"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/demo.css')); ?>" />
    <?php if(Request::is('master-panel/projects/kanban-view')): ?>
        <link
            href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Open+Sans:wght@400;700&family=Montserrat:wght@400;600;700&family=Source+Sans+Pro:wght@400;600;700&family=Nunito:wght@400;700&family=Raleway:wght@400;600;700&family=Ubuntu:wght@400;700&family=Work+Sans:wght@400;700&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/project-kanban.css')); ?>" />
    <?php endif; ?>
    <?php if(Request::is('master-panel/projects/mind-map/*')): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/mind-map.css')); ?>" />
    <?php endif; ?>
    
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
       <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/lib/libphonenumber/build/utils.js"></script>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/pages/page-auth.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/apex-charts.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/lightbox/lightbox.min.css')); ?>" />
    <!-- Page CSS -->
    <!-- Helpers -->
    <script src="<?php echo e(asset('assets/vendor/js/helpers.js')); ?>"></script>
    <!-- Date picker -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/daterangepicker.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap-datetimepicker.min.css')); ?>" />
    <link href="<?php echo e(asset('assets/css/select2.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/bootstrap-table.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/dragula.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/toastr.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/dropzone.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/fullcalendar/core/main.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/fullcalendar/daygrid/main.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/fullcalendar/list/main.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/frappe-gannt.css')); ?>" rel="stylesheet" />
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?php echo e(asset('assets/js/config.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/jquery/jquery.js')); ?>"></script>
</head>
<body>
    <!-- Layout wrapper -->
    <?php if(Request::is('forgot-password') ||
            Request::is('login') ||
            Request::segment(1) == 'reset-password' ||
            Request::is('install') ||
            Request::is('privacy-policy') ||
            Request::is('terms-and-condition') ||
            Request::is('refund-policy') ||
            Request::is('register')): ?>
        <?php echo $__env->yieldContent('content'); ?>
        <?php echo $__env->make('labels', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <!-- Menu -->
                <?php if (auth('web')->check() || auth('client')->check()): ?>
                <?php if (isset($component)) { $__componentOriginal5893a4ae82cbde8a6e1ba16203c33ac0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5893a4ae82cbde8a6e1ba16203c33ac0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.menu','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5893a4ae82cbde8a6e1ba16203c33ac0)): ?>
<?php $attributes = $__attributesOriginal5893a4ae82cbde8a6e1ba16203c33ac0; ?>
<?php unset($__attributesOriginal5893a4ae82cbde8a6e1ba16203c33ac0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5893a4ae82cbde8a6e1ba16203c33ac0)): ?>
<?php $component = $__componentOriginal5893a4ae82cbde8a6e1ba16203c33ac0; ?>
<?php unset($__componentOriginal5893a4ae82cbde8a6e1ba16203c33ac0); ?>
<?php endif; ?>
                <!-- Layout container -->
                <div class="layout-page">
                    <?php echo $__env->make('partials._navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <!-- Content wrapper -->
                    <div class="content-wrapper">
                        <?php echo $__env->yieldContent('content'); ?>
                        <?php echo $__env->make('modals', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php echo $__env->make('labels', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <!-- Content wrapper -->
                    <!-- footer -->
                    <?php if (isset($component)) { $__componentOriginal8a8716efb3c62a45938aca52e78e0322 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8a8716efb3c62a45938aca52e78e0322 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8a8716efb3c62a45938aca52e78e0322)): ?>
<?php $attributes = $__attributesOriginal8a8716efb3c62a45938aca52e78e0322; ?>
<?php unset($__attributesOriginal8a8716efb3c62a45938aca52e78e0322); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8a8716efb3c62a45938aca52e78e0322)): ?>
<?php $component = $__componentOriginal8a8716efb3c62a45938aca52e78e0322; ?>
<?php unset($__componentOriginal8a8716efb3c62a45938aca52e78e0322); ?>
<?php endif; ?>
                    <!-- / footer -->
                <?php else: ?>
                    <div class="container-fluid container-p-y">
                        <div class="misc-wrapper d-flex flex-column align-items-center justif-content-center">
                            <h2 class="mx-2 mb-2">
                                <?= get_label('session_expired', 'Session expired') ?>!!!
                            </h2>
                            <div class="my-5">
                                <img src="/assets/img/illustrations/page-misc-error-light.png"
                                    alt="page-misc-error-light" width="500" class="img-fluid"
                                    data-app-dark-img="illustrations/page-misc-error-dark.png"
                                    data-app-light-img="illustrations/page-misc-error-light.png" />
                            </div>
                            <a href="<?php echo e(url('/')); ?>" class="btn btn-primary">
                                <?= get_label('log_in', 'Log in') ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <!-- / Layout page -->
        </div>
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
<?php endif; ?>
<script src="<?php echo e(asset('assets/js/time-tracker.js')); ?>"></script>
<?php if(getAuthenticatedUser() && !getAuthenticatedUser()->hasRole('superadmin') &&!getAuthenticatedUser()->hasRole('manager')): ?>
    <?php if(Auth::guard('web')->check()): ?>
        <div onclick="toggleChatIframe()">
            <span>
                <a href="javascript:void(0);">
                    <img src="<?php echo e(asset('storage/chat-icon.png')); ?>" class="chat-img" alt="Chat">
                </a>
            </span>
        </div>
        <div id="chatIframeContainer" class="chat-iframe-container">
            <iframe src="/<?php echo e('chat'); ?>" frameborder="0" class="chat-iframe"></iframe>
        </div>
    <?php endif; ?>
<?php endif; ?>
<!-- / Layout wrapper -->
<?php if(getAuthenticatedUser() && getAuthenticatedUser()->can('create_timesheet')): ?>
    <!-- Timer image -->
    <div onclick="open_timer_section()">
        <span>
            <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="left"
                data-bs-original-title="<?= get_label('time_tracker', 'Time tracker') ?>">
                <img src="<?php echo e(asset('storage/94150-clock.png')); ?>" class="timer-img" id="timer-image" alt="Timer"
                    data-bs-toggle="modal" data-bs-target="#timerModal">
            </a>
        </span>
    </div>
<?php endif; ?>
<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="<?php echo e(asset('assets/vendor/libs/popper/popper.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/js/bootstrap.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/js/menu.js')); ?>"></script>
<!-- endbuild -->
<!-- Main JS -->
<script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/ui-toasts.js')); ?>"></script>
<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="<?php echo e(asset('assets/js/buttons.js')); ?>"></script>
<!-- select 2 js !-->
<script src="<?php echo e(asset('assets/js/select2.min.js')); ?>"></script>
<!-- Bootstrap-table -->
<script src="<?php echo e(asset('assets/js/bootstrap-table/bootstrap-table.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/bootstrap-table/bootstrap-table-export.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/bootstrap-table/tableExport.min.js')); ?>"></script>
<!-- Dragula -->
<script src="<?php echo e(asset('assets/js/dragula.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/popper.js')); ?>"></script>
<!-- Toastr -->
<script src="<?php echo e(asset('assets/js/toastr.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce-jquery.min.js')); ?>"></script>
<!-- Date picker -->
<script src="<?php echo e(asset('assets/js/moment.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/daterangepicker.js')); ?>"></script>
<script src="<?php echo e(asset('assets/lightbox/lightbox.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/dropzone.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/extended-ui-perfect-scrollbar.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/fullcalendar/core/main.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/fullcalendar/interaction/main.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/fullcalendar/daygrid/main.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/fullcalendar/list/main.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/fullcalendar/google-calendar/main.js')); ?>"></script>
<?php if(Request::is('master-panel/projects/mind-map/*')): ?>
<script src="<?php echo e(asset('assets/js/mind-map.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/dom-to-image.js')); ?>"></script>
<!-- screenshot plugin -->
<script src="<?php echo e(asset('assets/js/mindmap-screenshot.js')); ?>"></script>
<?php endif; ?>
<?php if (auth('web')->check() || auth('client')->check()): ?>
<script>
    var authUserId = '<?= getAuthenticatedUser()->id ?>';
</script>
<?php endif; ?>
<!-- Custom js -->
<script>
    var csrf_token = '<?php echo e(csrf_token()); ?>';
    var js_date_format = '<?php echo e($js_date_format ?? 'YYYY - MM - DD'); ?>';
</script>
<script src="<?php echo e(asset('assets/js/frappe-gantt.js')); ?>"></script>

<script src="<?php echo e(asset('assets/js/tribute.js')); ?>"></script>
<script>
    var toastTimeOut = <?php echo e(isset($general_settings['toast_time_out']) ? $general_settings['toast_time_out'] : 5); ?>;
    var toastPosition = "<?php echo e(isset($general_settings['toast_position']) ? $general_settings['toast_position'] : 'toast-top-right'); ?>";
</script>
<script src="<?php echo e(asset('assets/js/custom.js')); ?>"></script>
<?php if(session()->has('message')): ?>
<script>
    toastr.options = {
        "positionClass": toastPosition,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": parseFloat(toastTimeOut) * 1000,
        "progressBar": true,
        "extendedTimeOut": "1000",
        "closeButton": true
    };
    toastr.success('<?php echo e(session('message')); ?>', 'Success');
</script>
<?php elseif(session()->has('error')): ?>
<script>
    toastr.options = {
        "positionClass": toastPosition,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": parseFloat(toastTimeOut) * 1000,
        "progressBar": true,
        "extendedTimeOut": "1000",
        "closeButton": true
    };
    toastr.error('<?php echo e(session('error')); ?>', 'Error');
</script>
<?php endif; ?>
</body>
</html>
<?php /**PATH /srv/http/localhost/resources/views/layout.blade.php ENDPATH**/ ?>