<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/profile/profile.css')); ?>">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/profile/profile.js')); ?>" defer></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php if(auth()->guard()->check()): ?>
<div class="profile-info-wrapper">

    <div class="profile-layout-container">
        
        <aside class="profile-sidebar">
            <nav class="sidebar-nav">
                <a href="<?php echo e(route('profileInfo')); ?>" class="nav-item active" title="Profile Info">
                    <i class="fas fa-th-large"></i>
                </a>
                <a href="<?php echo e(route('profileFavorites')); ?>" class="nav-item" title="Favorites">
                    <i class="far fa-heart"></i>
                </a>
                <a href="<?php echo e(route('profileProperties')); ?>" class="nav-item" title="Properties">
                    <i class="fas fa-map-marker-alt"></i>
                </a>
                <a href="<?php echo e(route('profileInfo')); ?>" class="nav-item" title="Profile">
                    <i class="fas fa-user"></i>
                </a>
                <a href="#" class="nav-item" title="Settings">
                    <i class="fas fa-cog"></i>
                </a>
            </nav>
        </aside>

        
        <main class="profile-main-content">
            <div class="profile-card-modern">
                
                <div class="profile-banner"></div>

                
                <div class="profile-header-section">
                    <div class="profile-avatar-section">
                        <div class="profile-avatar-large">
                            <?php if(Auth::user()->profile_image): ?>
                                <img src="<?php echo e(asset('storage/' . Auth::user()->profile_image)); ?>" alt="<?php echo e(Auth::user()->name); ?>">
                            <?php else: ?>
                                <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(Auth::user()->name)); ?>&background=random&color=fff" alt="<?php echo e(Auth::user()->name); ?>">
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="<?php echo e(route('profile.update.photo')); ?>" enctype="multipart/form-data" class="avatar-upload-form">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="file" name="profile_image" id="avatarFile" accept="image/*" hidden>
                            <label for="avatarFile" class="avatar-edit-icon">
                                <i class="fas fa-camera"></i>
                            </label>
                        </form>
                    </div>
                    <div class="profile-name-section">
                        <h2 class="profile-full-name"><?php echo e(Auth::user()->first_name); ?> <?php echo e(Auth::user()->last_name); ?></h2>
                        <p class="profile-email-display"><?php echo e(Auth::user()->email); ?></p>
                    </div>
                    <div class="profile-header-actions">
                        <button type="button" class="edit-profile-btn-modern" id="toggleEditBtn">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>

                
                <?php if(session('success')): ?>
                    <div class="alert-success-modern" role="status"><?php echo e(session('success')); ?></div>
                <?php endif; ?>

                
                <?php
                    $user = Auth::user();
                    $dob = $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth) : null;
                    $showEdit = $errors->any();
                ?>

                
                <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data" id="editProfileSection" class="profile-form-modern" <?php if(!$showEdit): ?> style="display:none" <?php endif; ?>>
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="form-grid-two">
                        
                        <div class="form-field-group">
                            <label for="full_name">Full Name</label>
                            <div class="input-field-modern">
                                <input type="text" id="full_name" value="<?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?>" placeholder="Your Full Name" readonly>
                            </div>
                        </div>

                        
                        <div class="form-field-group">
                            <label for="phone_nb">Phone Number</label>
                            <div class="input-field-modern">
                                <span class="input-prefix">+961</span>
                                <input type="tel" id="phone_nb" name="phone_nb" value="<?php echo e(old('phone_nb', $user->phone_nb)); ?>" placeholder="70 123 456" required>
                            </div>
                            <?php $__errorArgs = ['phone_nb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-message"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="form-grid-two">
                        
                        <div class="form-field-group">
                            <label for="first_name">First Name</label>
                            <div class="input-field-modern">
                                <input type="text" id="first_name" name="first_name" value="<?php echo e(old('first_name', $user->first_name)); ?>" placeholder="Your First Name" required>
                            </div>
                            <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-message"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="form-field-group">
                            <label for="last_name">Last Name</label>
                            <div class="input-field-modern">
                                <input type="text" id="last_name" name="last_name" value="<?php echo e(old('last_name', $user->last_name)); ?>" placeholder="Your Last Name" required>
                            </div>
                            <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-message"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="form-grid-two">
                        
                        <div class="form-field-group">
                            <label>Date of Birth</label>
                            <div class="dob-selectors">
                                <select name="dob_day" id="dob_day" aria-label="Day">
                                    <option value="">DD</option>
                                    <?php for($d = 1; $d <= 31; $d++): ?>
                                        <option value="<?php echo e($d); ?>" <?php if($dob && $dob->day == $d): echo 'selected'; endif; ?>><?php echo e(sprintf('%02d',$d)); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select name="dob_month" id="dob_month" aria-label="Month">
                                    <option value="">MM</option>
                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo e($m); ?>" <?php if($dob && $dob->month == $m): echo 'selected'; endif; ?>><?php echo e(sprintf('%02d',$m)); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select name="dob_year" id="dob_year" aria-label="Year">
                                    <option value="">YYYY</option>
                                    <?php for($y = date('Y'); $y >= 1900; $y--): ?>
                                        <option value="<?php echo e($y); ?>" <?php if($dob && $dob->year == $y): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-message"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="form-field-group">
                            <label for="address">Address</label>
                            <div class="input-field-modern">
                                <input type="text" id="address" name="address" value="<?php echo e(old('address', $user->address)); ?>" placeholder="Street, City, Country">
                            </div>
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-message"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    
                    <div class="email-section-modern">
                        <div class="email-section-header">
                            <h3 class="email-section-title">
                                <i class="fas fa-envelope"></i> My email Address
                            </h3>
                        </div>
                        <div class="email-display-item">
                            <div class="email-info">
                                <i class="fas fa-envelope email-icon"></i>
                                <div>
                                    <span class="email-value"><?php echo e($user->email); ?></span>
                                    <span class="email-meta"><?php echo e($user->email_verified_at ? 'Verified' : 'Not verified'); ?> • <?php echo e($user->created_at->diffForHumans()); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="form-actions-modern">
                        <button type="button" class="btn-cancel" id="exitEditBtn" style="display:none;">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Save changes
                        </button>
                    </div>
                </form>

                
                <div id="viewMode" class="profile-view-mode" <?php if($showEdit): ?> style="display:none" <?php endif; ?>>
                    <div class="form-grid-two">
                        <div class="info-display-group">
                            <label>Full Name</label>
                            <div class="info-value"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></div>
                        </div>
                        <div class="info-display-group">
                            <label>Phone Number</label>
                            <div class="info-value">+961 <?php echo e($user->phone_nb ?? 'N/A'); ?></div>
                        </div>
                    </div>
                    <div class="form-grid-two">
                        <div class="info-display-group">
                            <label>Date of Birth</label>
                            <div class="info-value"><?php echo e($dob ? $dob->format('F j, Y') : 'Not set'); ?></div>
                        </div>
                        <div class="info-display-group">
                            <label>Address</label>
                            <div class="info-value"><?php echo e($user->address ?? 'Not set'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/profile/info.blade.php ENDPATH**/ ?>