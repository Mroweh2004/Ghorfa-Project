<?php $__env->startSection('content'); ?>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('css/register.css')); ?>">

<div class="auth-wrapper">
  <div class="auth-shell">
    
    <aside class="auth-aside">
      <div class="brand">
        <div class="brand-logo">
          <img src="<?php echo e(asset('img/logo.png')); ?>" alt="Ghorfa logo" width="32" height="32" loading="lazy">
        </div>
        <h1>Ghorfa</h1>
      </div>

      <h2>Create your account</h2>
      <p class="aside-sub">Join us in less than a minute. Manage your profile, bookings and more.</p>

      <ul class="bullets">
        <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg> Secure & private</li>
        <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg> Fast onboarding</li>
        <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L9 18l-5-5"/></svg> Landlord tools</li>
      </ul>

      <div class="aside-footer">Already have an account?
        <a href="<?php echo e(route('login')); ?>">Sign in</a>
      </div>
    </aside>

    
    <main class="auth-main">
      <div class="register-card">

        
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

        <form action="<?php echo e(route('submit.register')); ?>" method="POST" enctype="multipart/form-data" novalidate>
          <?php echo csrf_field(); ?>

          
          <div class="form-row">
            <label for="profile_image" class="profile-image-label">
              <div class="profile-image-preview" id="imagePreview">
                <i class="fas fa-user-circle" aria-hidden="true"></i>
                <span>Upload Profile Image</span>
                <img id="profileImageTag" alt="" />
              </div>
              <input type="file" id="profile_image" name="profile_image" accept="image/*" class="profile-image-input">
            </label>
            <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="grid two">
            <div class="form-group">
              <label for="first_name">First Name</label>
              <div class="input-wrap">
                <span class="input-icon">@</span>
                <input type="text" id="first_name" name="first_name" value="<?php echo e(old('first_name')); ?>" placeholder="e.g. Ali" required>
              </div>
              <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
              <label for="last_name">Last Name</label>
              <div class="input-wrap">
                <span class="input-icon">@</span>
                <input type="text" id="last_name" name="last_name" value="<?php echo e(old('last_name')); ?>" placeholder="e.g. Ahmad" required>
              </div>
              <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z" fill="none"/><path d="M4 8l8 5 8-5"/></svg>
              </span>
              <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="you@example.com" required>
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

          <div class="grid two">
            <div class="form-group">
              <label for="password">Password</label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 10h12v10H6z"/><path d="M8 10V7a4 4 0 018 0v3"/></svg>
                </span>
                <input type="password" id="password" name="password" placeholder="Min 8 characters" required>
                <button type="button" class="toggle-eye" data-target="password" aria-label="Show or hide password">👁</button>
              </div>
              <small id="pwHint" class="hint">Use 8+ chars with letters & numbers</small>
              <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
              <label for="password_confirmation">Confirm Password</label>
              <div class="input-wrap">
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 10h12v10H6z"/><path d="M8 10V7a4 4 0 018 0v3"/></svg>
                </span>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" required>
                <button type="button" class="toggle-eye" data-target="password_confirmation" aria-label="Show or hide password">👁</button>
              </div>
              <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
          </div>

          <div class="form-group">
            <label for="phone_nb">Phone Number</label>
            <div class="input-inline">
              <span class="dial">+961</span>
              <input type="tel" id="phone_nb" name="phone_nb" inputmode="numeric" pattern="[0-9 ]*" value="<?php echo e(old('phone_nb')); ?>" placeholder="70 123 456" required>
            </div>
            <?php $__errorArgs = ['phone_nb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="form-group">
            <label>Date of Birth</label>
            <div class="dob-grid">
              <select name="dob_day" id="dob_day" aria-label="Day">
                <option value="">DD</option>
                <?php for($d=1;$d<=31;$d++): ?>
                  <option value="<?php echo e($d); ?>" <?php if(old('dob_day') == $d): echo 'selected'; endif; ?>><?php echo e(sprintf('%02d',$d)); ?></option>
                <?php endfor; ?>
              </select>
              <select name="dob_month" id="dob_month" aria-label="Month">
                <option value="">MM</option>
                <?php for($m=1;$m<=12;$m++): ?>
                  <option value="<?php echo e($m); ?>" <?php if(old('dob_month') == $m): echo 'selected'; endif; ?>><?php echo e(sprintf('%02d',$m)); ?></option>
                <?php endfor; ?>
              </select>
              <select name="dob_year" id="dob_year" aria-label="Year">
                <option value="">YYYY</option>
                <?php for($y=date('Y');$y>=1900;$y--): ?>
                  <option value="<?php echo e($y); ?>" <?php if(old('dob_year') == $y): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                <?php endfor; ?>
              </select>
            </div>
            
            <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3" placeholder="Street, City, Country"><?php echo e(old('address')); ?></textarea>
            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="form-group landlord">
            <label class="switch">
              <input type="checkbox" id="is_landlord" name="is_landlord" value="1" <?php if(old('is_landlord')): echo 'checked'; endif; ?>>
              <span class="slider"></span>
            </label>
            <label for="is_landlord" class="switch-label">I am a landlord</label>
            <?php $__errorArgs = ['is_landlord'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <button type="submit" class="register-btn">Create Account</button>

          <p class="terms">By creating an account, you agree to our <a href="<?php echo e(url('/terms')); ?>">Terms</a> & <a href="<?php echo e(url('/privacy')); ?>">Privacy Policy</a>.</p>
        </form>
      </div>
    </main>
  </div>
</div>

<script src="https://kit.fontawesome.com/a2c0d5f0d1.js" crossorigin="anonymous"></script>
<script src="<?php echo e(asset('js/register.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/register.blade.php ENDPATH**/ ?>