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

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
            <a href="#applications-section" class="action-card">
                <i class="fas fa-file-alt"></i>
                <span>Review Applications</span>
            </a>
            <a href="#users-section" class="action-card">
                <i class="fas fa-user-cog"></i>
                <span>Manage Users</span>
            </a>
            <a href="#recent-activity" class="action-card">
                <i class="fas fa-history"></i>
                <span>Recent Activity</span>
            </a>
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

        <!-- Recent Activity Section -->
        <div id="recent-activity" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-history"></i>
                    Recent Activity
                </h2>
            </div>
            <div class="activity-grid">
                <div class="activity-card">
                    <h3><i class="fas fa-user-plus"></i> Recent Users</h3>
                    <ul class="activity-list">
                        <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li>
                            <span class="activity-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <div class="activity-content">
                                <strong><?php echo e($user->name); ?></strong>
                                <span class="activity-meta"><?php echo e($user->created_at->diffForHumans()); ?></span>
                            </div>
                            <span class="role-badge role-<?php echo e($user->role); ?>"><?php echo e(ucfirst($user->role)); ?></span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="empty-activity">No recent users</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="activity-card">
                    <h3><i class="fas fa-building"></i> Recent Properties</h3>
                    <ul class="activity-list">
                        <?php $__empty_1 = true; $__currentLoopData = $recentProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li>
                            <span class="activity-icon">
                                <i class="fas fa-home"></i>
                            </span>
                            <div class="activity-content">
                                <strong><?php echo e($property->title); ?></strong>
                                <span class="activity-meta">by <?php echo e($property->user->name); ?> • <?php echo e($property->created_at->diffForHumans()); ?></span>
                            </div>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="empty-activity">No recent properties</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo e(asset('js/admin.js')); ?>"></script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>