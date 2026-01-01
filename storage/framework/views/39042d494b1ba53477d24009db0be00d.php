<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/admin.css')); ?>">

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="dashboard-content" data-pending-applications-route="<?php echo e(route('admin.pending-applications')); ?>">
        <div class="applications-section" style="margin-bottom: 2rem;">
            <h2>Pending Landlord Applications (<?php echo e($pendingApplications->count()); ?>)</h2>
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
                                <td><?php echo e($application->phone ?? $application->user->phone_nb); ?></td>
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
                                <td colspan="5" style="text-align: center; padding: 2rem; color: #6b7280;">
                                    No pending applications
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="users-section">
            <h2>User Management</h2>
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
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($user->id); ?></td>
                            <td><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td>
                                <span style="padding: 0.25rem 0.5rem; border-radius: 4px; background: <?php echo e($user->role === 'landlord' ? '#10b981' : ($user->role === 'admin' ? '#ef4444' : '#6b7280')); ?>; color: white; font-size: 0.875rem;">
                                    <?php echo e(ucfirst($user->role)); ?>

                                </span>
                            </td>
                            <td><?php echo e($user->created_at); ?></td>
                            <td>
                                <form action="<?php echo e(route('admin.users.delete', $user->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo e(asset('js/admin.js')); ?>"></script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>