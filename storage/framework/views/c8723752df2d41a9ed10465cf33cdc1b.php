<script src="<?php echo e(asset('assets/front-end/assets/js/core/popper.js')); ?>"></script>
<script src="<?php echo e(asset('assets/front-end/assets/js/core/bootstrap.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/front-end/assets/js/soft-design-system.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/front-end/assets/js/plugins/countup.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/front-end/assets/js/plugins/flatpickr.min.js')); ?>"></script>
<script src="<?php echo e('assets/front-end/assets/js/plugins/typedjs.js'); ?>"></script>
<script src="<?php echo e(asset('assets/front-end/assets/js/custom.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce-jquery.min.js')); ?>"></script>

<!-- Date picker -->
<script src="<?php echo e(asset('assets/js/moment.min.js')); ?>"></script>

<script src="<?php echo e(asset('assets/js/daterangepicker.js')); ?>"></script>

<script src="<?php echo e(asset('assets/lightbox/lightbox.min.js')); ?>"></script>

<script src="<?php echo e(asset('assets/js/dropzone.min.js')); ?>"></script>
<script>
    var csrf_token = '<?php echo e(csrf_token()); ?>';
    var js_date_format = '<?php echo e($js_date_format ?? 'YYYY-MM-DD'); ?>';
</script>


<script src="<?php echo e(asset('assets/front-end/assets/js/loopple/loopple.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/toastr.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/front-end/assets/js/plugins/lottie.js')); ?>"></script>

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

<?php /**PATH C:\Siddharth Gor\Test\resources\views/front-end/include-js.blade.php ENDPATH**/ ?>