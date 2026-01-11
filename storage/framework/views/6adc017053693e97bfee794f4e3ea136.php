<?php $__env->startSection('title', 'Login Page'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php if(auth()->guard()->check()): ?>
<div class="auth-wrapper">
    <div class="auth-shell single-panel">
        <div class="auth-main">
            <div class="auth-card login-card">
                <h1 class="card-title">You are already logged in.</h1>
                <p class="card-sub">Head back to the homepage to continue exploring.</p>
                <div class="form-actions">
                    <a href="<?php echo e(route('home')); ?>" class="login-btn btn-link">Go to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if(auth()->guard()->guest()): ?>
<div class="auth-wrapper">
    <div class="auth-shell">
        <aside class="auth-aside login-aside">
            <div class="brand">
                <div class="brand-logo">
                    <img src="<?php echo e(asset('img/logo.png')); ?>" alt="Ghorfa logo" width="32" height="32" loading="lazy">
                    <span class="logo-dot"></span>
                </div>
                <h1>Ghorfa</h1>
            </div>
            <h2>Welcome back</h2>
            <p class="aside-sub">Sign in to manage your listings, track favourites, and pick up where you left off.</p>
            <ul class="bullets">
                <li>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg>
                    Keep your bookings in sync
                </li>
                <li>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg>
                    Chat with landlords instantly
                </li>
                <li>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg>
                    Save and compare properties
                </li>
            </ul>
            <div class="aside-footer">
                New to Ghorfa?
                <a href="<?php echo e(route('register')); ?>">Create an account</a>
            </div>
        </aside>

        <div class="auth-main">
            <div class="auth-card login-card">
                <h1 class="card-title">Sign in</h1>
                <p class="card-sub">Enter your details to access your dashboard.</p>

                <?php if($errors->any()): ?>
                    <div class="alert">
                        <strong>We found <?php echo e($errors->count()); ?> <?php echo e(Str::plural('issue', $errors->count())); ?>:</strong>
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('submit.login')); ?>" method="POST" class="login-form" novalidate>
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8l8 5 8-5"/><rect x="4" y="4" width="16" height="16" fill="none"/></svg>
                            </span>
                            <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="you@example.com" required autocomplete="email" autofocus>
                        </div>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 10h12v10H6z"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            </span>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="login-btn">Login</button>
                    </div>

                    <p class="auth-switch">
                        Don't have an account?
                        <a href="<?php echo e(route('register')); ?>">Sign up</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/login.blade.php ENDPATH**/ ?>