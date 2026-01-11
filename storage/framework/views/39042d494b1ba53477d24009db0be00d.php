<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-dashboard">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card-primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo e($stats['total_users']); ?></h3>
                <p>Total Users</p>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo e($stats['total_landlords']); ?></h3>
                <p>Landlords</p>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo e($stats['total_properties']); ?></h3>
                <p>Properties</p>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo e($stats['pending_applications']); ?></h3>
                <p>Pending Applications</p>
            </div>
        </div>
    </div>

    <!-- Main Content Sections -->
    <div class="dashboard-content" data-pending-applications-route="<?php echo e(route('admin.pending-applications')); ?>">
        
        <!-- Pending Applications Section -->
        <div id="applications-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-file-alt"></i>
                    Pending Landlord Applications
                    <?php if($stats['pending_applications'] > 0): ?>
                        <span class="badge badge-warning"><?php echo e($stats['pending_applications']); ?></span>
                    <?php endif; ?>
                </h2>
            </div>
            <div class="applications-table">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Applied At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($pendingApplications->count() > 0): ?>
                            <?php $__currentLoopData = $pendingApplications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr data-application-id="<?php echo e($application->id); ?>">
                                <td><?php echo e($application->user->name); ?></td>
                                <td><?php echo e($application->user->email); ?></td>
                                <td><?php echo e($application->phone ?? $application->user->phone_nb ?? 'N/A'); ?></td>
                                <td><?php echo e($application->created_at->diffForHumans()); ?></td>
                                <td>
                                    <button 
                                        type="button" 
                                        class="btn btn-success approve-btn" 
                                        data-application-id="<?php echo e($application->id); ?>"
                                        onclick="handleApprove(<?php echo e($application->id); ?>)"
                                    >
                                        Approve
                                    </button>
                                    <button 
                                        type="button" 
                                        class="btn btn-danger reject-btn" 
                                        data-application-id="<?php echo e($application->id); ?>"
                                        onclick="handleReject(<?php echo e($application->id); ?>)"
                                    >
                                        Reject
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="empty-state-cell">
                                    <i class="fas fa-check-circle"></i>
                                    No pending applications
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User Management Section -->
        <div id="users-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-user-cog"></i>
                    User Management
                </h2>
            </div>
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($user->id); ?></td>
                            <td><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo e($user->role); ?>">
                                    <?php echo e(ucfirst($user->role)); ?>

                                </span>
                            </td>
                            <td><?php echo e($user->created_at->format('M d, Y')); ?></td>
                            <td>
                                <form action="<?php echo e(route('admin.users.delete', $user->id)); ?>" method="POST" style="display: inline;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="empty-state-cell">
                                <i class="fas fa-users"></i>
                                No users found
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="pagination-wrapper">
                    <?php echo e($users->links()); ?>

                </div>
            </div>
        </div>

        <!-- Pending Properties Section -->
        <div id="properties-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-building"></i>
                    Pending Properties for Approval
                    <?php if($stats['pending_properties'] > 0): ?>
                        <span class="badge badge-warning"><?php echo e($stats['pending_properties']); ?></span>
                    <?php endif; ?>
                </h2>
            </div>
            <div class="properties-table">
                <table>
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Landlord</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($pendingProperties->count() > 0): ?>
                            <?php $__currentLoopData = $pendingProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr data-property-id="<?php echo e($property->id); ?>">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <?php if($property->images->first()): ?>
                                            <img src="<?php echo e(asset('storage/' . $property->images->first()->path)); ?>" alt="<?php echo e($property->title); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-home" style="color: #94a3b8;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo e($property->title); ?></strong>
                                            <div style="font-size: 0.875rem; color: #64748b;"><?php echo e($property->property_type); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo e($property->user->name); ?></td>
                                <td><?php echo e($property->city); ?>, <?php echo e($property->country); ?></td>
                                <td>$<?php echo e(number_format($property->price)); ?>/month</td>
                                <td><?php echo e($property->created_at->diffForHumans()); ?></td>
                                <td>
                                    <a 
                                        href="<?php echo e(route('properties.show', $property->id)); ?>" 
                                        target="_blank"
                                        class="btn btn-info"
                                        style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;"
                                    >
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button 
                                        type="button" 
                                        class="btn btn-success approve-property-btn" 
                                        data-property-id="<?php echo e($property->id); ?>"
                                        onclick="handleApproveProperty(<?php echo e($property->id); ?>)"
                                    >
                                        Approve
                                    </button>
                                    <button 
                                        type="button" 
                                        class="btn btn-danger reject-property-btn" 
                                        data-property-id="<?php echo e($property->id); ?>"
                                        onclick="handleRejectProperty(<?php echo e($property->id); ?>)"
                                    >
                                        Reject
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-state-cell">
                                    <i class="fas fa-check-circle"></i>
                                    No pending properties
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div id="recent-activity" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-history"></i>
                    Recent Activities
                </h2>
            </div>
            <ul class="activity-list">
                <?php if($recentActivities->count() > 0): ?>
                    <?php $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <div class="activity-icon">
                                <?php if($activity->type === 'property_created'): ?>
                                    <i class="fas fa-plus-circle text-success"></i>
                                <?php elseif($activity->type === 'property_updated'): ?>
                                    <i class="fas fa-edit text-info"></i>
                                <?php elseif($activity->type === 'property_deleted'): ?>
                                    <i class="fas fa-trash text-danger"></i>
                                <?php elseif($activity->type === 'property_approved'): ?>
                                    <i class="fas fa-check-circle text-success"></i>
                                <?php elseif($activity->type === 'property_rejected'): ?>
                                    <i class="fas fa-times-circle text-danger"></i>
                                <?php elseif($activity->type === 'application_approved'): ?>
                                    <i class="fas fa-user-check text-success"></i>
                                <?php elseif($activity->type === 'application_rejected'): ?>
                                    <i class="fas fa-user-times text-danger"></i>
                                <?php else: ?>
                                    <i class="fas fa-circle text-primary"></i>
                                <?php endif; ?>
                            </div>
                            <div class="activity-content">
                                <div class="activity-description">
                                    <?php echo e($activity->description); ?>

                                </div>
                                <div class="activity-meta">
                                    <?php if($activity->user): ?>
                                        <span class="activity-user">
                                            <i class="fas fa-user"></i> <?php echo e($activity->user->name); ?>

                                        </span>
                                    <?php endif; ?>
                                    <span class="activity-time">
                                        <i class="fas fa-clock"></i> <?php echo e($activity->created_at->diffForHumans()); ?>

                                    </span>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <li class="empty-activity">
                        <i class="fas fa-history"></i>
                        <span>No activities yet</span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/admin.js')); ?>"></script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>