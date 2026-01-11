<?php $__env->startSection('title', 'Landlord Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/landlord-dashboard.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="landlord-dashboard-wrapper">
    <div class="dashboard-header">
        <h1>Landlord Dashboard</h1>
        <p>Manage your properties and track your listings</p>
    </div>

    <div class="dashboard-layout">
        <!-- Left Sidebar Navigation -->
        <aside class="landlord-sidebar">
            <nav class="sidebar-nav">
                <a href="<?php echo e(route('list-property')); ?>" class="nav-item">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add New Property</span>
                </a>
                <a href="#published-section" class="nav-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Published Properties</span>
                    <?php if($approvedProperties->count() > 0): ?>
                        <span class="nav-badge"><?php echo e($approvedProperties->count()); ?></span>
                    <?php endif; ?>
                </a>
                <a href="#pending-section" class="nav-item">
                    <i class="fas fa-clock"></i>
                    <span>Pending Approval</span>
                    <?php if($pendingProperties->count() > 0): ?>
                        <span class="nav-badge"><?php echo e($pendingProperties->count()); ?></span>
                    <?php endif; ?>
                </a>
                <?php if($rejectedProperties->count() > 0): ?>
                <a href="#rejected-section" class="nav-item">
                    <i class="fas fa-times-circle"></i>
                    <span>Rejected Properties</span>
                    <span class="nav-badge nav-badge-danger"><?php echo e($rejectedProperties->count()); ?></span>
                </a>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="dashboard-main">
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card stat-card-primary">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo e($stats['total_properties']); ?></h3>
                        <p>Total Properties</p>
                    </div>
                </div>

                <div class="stat-card stat-card-success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo e($stats['active_listings']); ?></h3>
                        <p>Published Properties</p>
                    </div>
                </div>

                <div class="stat-card stat-card-warning">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo e($stats['pending_properties']); ?></h3>
                        <p>Pending Approval</p>
                    </div>
                </div>

                <div class="stat-card stat-card-info">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo e($stats['total_likes']); ?></h3>
                        <p>Total Likes</p>
                    </div>
                </div>
            </div>

            <!-- Main Content Sections -->
            <div class="dashboard-content">
        
        <!-- Published Properties Section -->
        <?php if($approvedProperties->count() > 0): ?>
        <div id="published-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Published Properties
                    <span class="badge badge-success"><?php echo e($approvedProperties->count()); ?></span>
                </h2>
            </div>
            <div class="properties-table-wrapper">
                <table class="properties-table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $approvedProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $primaryImage = $property->images->first();
                                $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop';
                            ?>
                            <tr>
                                <td>
                                    <div class="property-table-cell">
                                        <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($property->title); ?>" class="property-table-image">
                                        <div class="property-table-info">
                                            <strong><?php echo e($property->title); ?></strong>
                                            <?php if($property->bedroom_nb || $property->bathroom_nb): ?>
                                                <span class="property-table-details">
                                                    <?php if($property->bedroom_nb): ?>
                                                        <i class="fas fa-bed"></i> <?php echo e($property->bedroom_nb); ?>

                                                    <?php endif; ?>
                                                    <?php if($property->bathroom_nb): ?>
                                                        <i class="fas fa-bath"></i> <?php echo e($property->bathroom_nb); ?>

                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo e($property->city); ?>, <?php echo e($property->country); ?>

                                </td>
                                <td><?php echo e($property->property_type); ?></td>
                                <td class="property-price-cell">$<?php echo e(number_format($property->price)); ?>/month</td>
                                <td>
                                    <span class="status-badge status-approved">Published</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="<?php echo e(route('properties.edit', $property->id)); ?>" class="btn-icon" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo e(route('properties.show', $property->id)); ?>" class="btn-icon" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pending Properties Section -->
        <?php if($pendingProperties->count() > 0): ?>
        <div id="pending-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-clock"></i>
                    Pending Approval
                    <span class="badge badge-warning"><?php echo e($pendingProperties->count()); ?></span>
                </h2>
            </div>
            <div class="properties-table-wrapper">
                <table class="properties-table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $pendingProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $primaryImage = $property->images->first();
                                $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop';
                            ?>
                            <tr class="table-row-pending">
                                <td>
                                    <div class="property-table-cell">
                                        <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($property->title); ?>" class="property-table-image">
                                        <div class="property-table-info">
                                            <strong><?php echo e($property->title); ?></strong>
                                            <?php if($property->bedroom_nb || $property->bathroom_nb): ?>
                                                <span class="property-table-details">
                                                    <?php if($property->bedroom_nb): ?>
                                                        <i class="fas fa-bed"></i> <?php echo e($property->bedroom_nb); ?>

                                                    <?php endif; ?>
                                                    <?php if($property->bathroom_nb): ?>
                                                        <i class="fas fa-bath"></i> <?php echo e($property->bathroom_nb); ?>

                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo e($property->city); ?>, <?php echo e($property->country); ?>

                                </td>
                                <td><?php echo e($property->property_type); ?></td>
                                <td class="property-price-cell">$<?php echo e(number_format($property->price)); ?>/month</td>
                                <td>
                                    <span class="status-badge status-pending">Pending</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="<?php echo e(route('properties.edit', $property->id)); ?>" class="btn-icon" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo e(route('properties.show', $property->id)); ?>" class="btn-icon" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Rejected Properties Section -->
        <?php if($rejectedProperties->count() > 0): ?>
        <div id="rejected-section" class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-times-circle"></i>
                    Rejected Properties
                    <span class="badge badge-danger"><?php echo e($rejectedProperties->count()); ?></span>
                </h2>
            </div>
            <div class="properties-table-wrapper">
                <table class="properties-table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rejectedProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $primaryImage = $property->images->first();
                                $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop';
                            ?>
                            <tr class="table-row-rejected">
                                <td>
                                    <div class="property-table-cell">
                                        <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($property->title); ?>" class="property-table-image">
                                        <div class="property-table-info">
                                            <strong><?php echo e($property->title); ?></strong>
                                            <?php if($property->bedroom_nb || $property->bathroom_nb): ?>
                                                <span class="property-table-details">
                                                    <?php if($property->bedroom_nb): ?>
                                                        <i class="fas fa-bed"></i> <?php echo e($property->bedroom_nb); ?>

                                                    <?php endif; ?>
                                                    <?php if($property->bathroom_nb): ?>
                                                        <i class="fas fa-bath"></i> <?php echo e($property->bathroom_nb); ?>

                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo e($property->city); ?>, <?php echo e($property->country); ?>

                                </td>
                                <td><?php echo e($property->property_type); ?></td>
                                <td class="property-price-cell">$<?php echo e(number_format($property->price)); ?>/month</td>
                                <td>
                                    <span class="status-badge status-rejected">Rejected</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="<?php echo e(route('properties.edit', $property->id)); ?>" class="btn-icon" title="Edit & Resubmit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo e(route('properties.show', $property->id)); ?>" class="btn-icon" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Empty State -->
        <?php if($approvedProperties->count() == 0 && $pendingProperties->count() == 0 && $rejectedProperties->count() == 0): ?>
        <div class="content-section">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3>No Properties Yet</h3>
                <p>You haven't listed any properties yet. Start by adding your first property!</p>
                <a href="<?php echo e(route('list-property')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> List Your First Property
                </a>
            </div>
        </div>
        <?php endif; ?>
            </div>
        </main>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item');
    const sections = document.querySelectorAll('.content-section');
    
    // Handle click on nav items
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Update active state
                    navItems.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                }
            }
        });
    });

    // Update active nav item on scroll
    function updateActiveNav() {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (window.pageYOffset >= sectionTop - 100) {
                current = section.getAttribute('id');
            }
        });

        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('href') === '#' + current) {
                item.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', updateActiveNav);
    updateActiveNav(); // Initial check
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ghorfa-Project\resources\views/landlord/dashboard.blade.php ENDPATH**/ ?>