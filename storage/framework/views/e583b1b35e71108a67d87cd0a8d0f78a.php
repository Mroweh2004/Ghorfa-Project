<?php $__env->startSection('content'); ?>
<?php if(auth()->guard()->check()): ?>
    
    <script src="<?php echo e(asset('js/profile.js')); ?>" defer></script>
    <link rel="stylesheet" href="<?php echo e(asset('css/profile.css')); ?>">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <main>
        <div class="profile-container">
            <div class="profile-card profile-card-shadow" role="region" aria-label="User profile">

                
                <div class="profile-header">
                    <div class="avatar-wrapper">
                        <div
                            class="profile-image profile-image-margin profile-image-wrapper"
                            id="avatarClickTarget"
                            role="button"
                            tabindex="0"
                            aria-label="View profile photo"
                        >
                            <?php if(Auth::user()->profile_image): ?>
                                <img
                                    src="<?php echo e(asset('storage/' . Auth::user()->profile_image)); ?>"
                                    alt="<?php echo e(Auth::user()->name); ?> profile image"
                                    onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?php echo e(urlencode(Auth::user()->name)); ?>&background=random&color=fff';"
                                >
                            <?php else: ?>
                                <img
                                    src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(Auth::user()->name)); ?>&background=random&color=fff"
                                    alt="Default profile avatar for <?php echo e(Auth::user()->name); ?>"
                                >
                            <?php endif; ?>
                        </div>

                        
                        <form method="POST" action="<?php echo e(route('profile.update.photo')); ?>" enctype="multipart/form-data" class="avatar-inline-form" title="Change photo">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="file" name="profile_image" id="avatarFile" accept="image/*" class="avatar-file-input" hidden>
                            <label for="avatarFile" class="avatar-edit-btn" aria-label="Change profile photo">
                                <i class="fa-duotone fa-regular fa-pen-to-square"></i>
                            </label>
                        </form>
                    </div>

                    <h2 class="profile-name" aria-live="polite"><?php echo e(Auth::user()->name); ?></h2>

                    <div class="profile-info profile-info-margin">
                        <p>
                            <span class="profile-icon-email">📧</span>
                            <a href="mailto:<?php echo e(Auth::user()->email); ?>"><?php echo e(Auth::user()->email); ?></a>
                        </p>
                        <p>
                            <span class="profile-icon-phone">📞</span>
                            <?php if(!empty(Auth::user()->phone_nb)): ?>
                                +961 <?php echo e(Auth::user()->phone_nb); ?>

                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </p>
                        <?php if(!empty(Auth::user()->date_of_birth)): ?>
                            <p>
                                <span class="profile-icon-dob">🎂</span>
                                <?php echo e(\Carbon\Carbon::parse(Auth::user()->date_of_birth)->format('F j, Y')); ?>

                            </p>
                        <?php endif; ?>
                        <?php if(!empty(Auth::user()->address)): ?>
                            <p>
                                <span class="profile-icon-address">📍</span>
                                <?php echo e(Auth::user()->address); ?>

                            </p>
                        <?php endif; ?>
                        <?php if(Auth::user()->isLandlord()): ?>
                            <p><span class="profile-icon-landlord">🏠</span>Landlord</p>
                        <?php endif; ?>
                        <p>
                            <span class="profile-icon-joined">📅</span>
                            Joined <?php echo e(Auth::user()->created_at->format('F Y')); ?>

                        </p>
                        <?php if(Auth::user()->last_login_at): ?>
                            <p>
                                <span class="profile-icon-last-login">🕒</span>
                                Last login: <?php echo e(\Carbon\Carbon::parse(Auth::user()->last_login_at)->diffForHumans()); ?>

                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="profile-actions-inline">
                        <button type="button" class="edit-profile-btn" id="toggleEditBtn">Edit profile</button>
                    </div>
                </div>

                
                <div id="avatarModal" class="avatar-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Profile photo">
                    <div class="avatar-modal-backdrop" data-close="avatarModal"></div>
                    <div class="avatar-modal-dialog" role="document">
                        <button type="button" class="avatar-modal-close" data-close="avatarModal" aria-label="Close">×</button>
                        <div class="avatar-modal-body">
                            <?php if(Auth::user()->profile_image): ?>
                                <img id="avatarModalImg" src="<?php echo e(asset('storage/' . Auth::user()->profile_image)); ?>" alt="<?php echo e(Auth::user()->name); ?> profile image large">
                            <?php else: ?>
                                <img id="avatarModalImg" src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(Auth::user()->name)); ?>&background=random&color=fff" alt="Default profile avatar for <?php echo e(Auth::user()->name); ?>">
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="<?php echo e(route('profile.update.photo')); ?>" enctype="multipart/form-data" class="avatar-modal-actions">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="file" name="profile_image" id="avatarFileModal" accept="image/*" class="avatar-file-input" hidden>
                            <label for="avatarFileModal" class="btn-secondary avatar-change-btn">Edit photo</label>
                        </form>
                    </div>
                </div>

                
                <?php if(session('success')): ?>
                    <div class="alert-success" role="status"><?php echo e(session('success')); ?></div>
                <?php endif; ?>

                
                <?php
                    /** @var \App\Models\User $user */
                    $user = Auth::user();
                    $dob = $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth) : null;
                    $showEdit = $errors->any();
                ?>

                
                <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data" id="editProfileSection"
                    <?php if(!$showEdit): ?> 
                    style="display:none" 
                    <?php endif; ?>
                    >
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    
                    <button type="button" class="exit-edit-btn" id="exitEditBtn" style="display:none;">
                        <svg viewBox="0 0 24 24" aria-hidden="true" width="20" height="20">
                            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                        <span>Exit</span>
                    </button>

                        <div class="profile-form">

                        <div class="grid two">
                            
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <div class="input-wrap">
                                    <input
                                        id="first_name"
                                        name="first_name"
                                        type="text"
                                        value="<?php echo e(old('first_name', $user->first_name)); ?>"
                                        placeholder="e.g. Ali"
                                        required
                                        spellcheck="false"
                                        autocomplete="given-name">
                                </div>
                                <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <div class="input-wrap">                    
                                    <input
                                        id="last_name"
                                        name="last_name"
                                        type="text"
                                        value="<?php echo e(old('last_name', $user->last_name)); ?>"
                                        placeholder="e.g. Ahmad"
                                        required
                                        spellcheck="false"
                                        autocomplete="family-name"
                                    >
                                </div>
                                <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-wrap">
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="<?php echo e(old('email', $user->email)); ?>"
                                    placeholder="you@example.com"
                                    required
                                    inputmode="email"
                                    autocomplete="email"
                                >
                            </div>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="form-group">
                            <label for="phone_nb">Phone Number</label>
                            <div class="input-inline">
                                <span class="dial">+961</span>
                                <input
                                    id="phone_nb"
                                    name="phone_nb"
                                    type="tel"
                                    inputmode="numeric"
                                    pattern="[0-9 ]*"
                                    value="<?php echo e(old('phone_nb', $user->phone_nb)); ?>"
                                    placeholder="70 123 456"
                                    required
                                    autocomplete="tel-national"
                                >
                            </div>
                            <?php $__errorArgs = ['phone_nb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <div class="dob-grid">
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
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea
                                id="address"
                                name="address"
                                rows="3"
                                placeholder="Street, City, Country"
                                autocomplete="street-address"
                            ><?php echo e(old('address', $user->address)); ?></textarea>
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                    </div>

                    
                    <div class="form-actions profile-actions">
                        <button type="submit" class="btn-secondary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/profile/info.blade.php ENDPATH**/ ?>