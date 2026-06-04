<!DOCTYPE html>

<head>
    <title>Pusher Testt</title>
    <link href="<?php echo e(asset('package/toastr/toastr/build/toastr.css')); ?>" rel="stylesheet" />
    
    <script src="<?php echo e(asset('vendor/jquery/jquery.min.js')); ?>"></script>
    
    <script src="<?php echo e(asset('package/toastr/toastr/build/toastr.min.js')); ?>"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('18781accf1f887a59b22', {
            cluster: 'ap1'
        });

        var channel = pusher.subscribe('channel-reservation');
        channel.bind('reservation-event', function(data) {
            toastr.success(JSON.stringify(data['message']), "Success");
        });

    </script>
</head>

<body>
    <h1>Pusher Testt</h1>
    <p>
        Try publishing an event to channel <code>my-channel</code>
        with event name <code>my-event</code>.
    </p>
</body>

<script src="<?php echo e(asset('package/sweetalert2/dist/sweetalert2.min.js')); ?>"></script>

<script src="<?php echo e(asset('package/toastr/toastr/build/toastr.min.js')); ?>"></script>
<script>
    <?php if(Session::has('success')): ?>
        toastr.success("<?php echo e(Session::get('success')); ?>","Success")
    <?php endif; ?>
    <?php if(Session::has('failed')): ?>
        toastr.error("<?php echo e(Session::get('failed')); ?>","Failed")
    <?php endif; ?>

</script>
<?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views\event\index.blade.php ENDPATH**/ ?>