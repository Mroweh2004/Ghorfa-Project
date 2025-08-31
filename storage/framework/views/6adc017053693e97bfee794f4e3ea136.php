
<?php $__env->startSection('title', 'Login Page'); ?>
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">

<?php if(auth()->guard()->check()): ?>
    <div class="login-container">
        <h2>You are already logged in.</h2>
        <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">Go to Home</a>
    </div>
<?php endif; ?>

<?php if(auth()->guard()->guest()): ?>
<div class="login-container">
    <h1 class="login-title">Login</h1>

   

    <form action="<?php echo e(route('submit.login')); ?>" method="POST" class="login-form">
        <?php echo csrf_field(); ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">

        <button type="submit" class="login-button">Login</button>
        <?php if($errors->any()): ?>
        <div class="error-messages">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li style="color: red"><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    </form>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/login.blade.php ENDPATH**/ ?>