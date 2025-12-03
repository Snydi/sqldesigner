<?php echo $__env->make('mail.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<p>New user just registered with email: <?php echo e($userEmail); ?></p>
<?php echo $__env->make('mail.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /var/www/html/backend/resources/views/mail/admin/newUserRegistration.blade.php ENDPATH**/ ?>