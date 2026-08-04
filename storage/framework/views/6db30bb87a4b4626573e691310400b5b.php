<?php $__env->startSection('title', 'Users Management'); ?>
<?php $__env->startSection('header_title', 'Account Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title">User Accounts Control</h1>
        <p class="page-subtitle">Manage user records, check registration configurations, and toggle account access states</p>
    </div>
    <div>
        <a href="<?php echo e(route('admin')); ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Console
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">User Accounts Directory</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Subscription</th>
                        <th>Verification</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td><?php echo e($user->phone ?? 'Not provided'); ?></td>
                            <td>
                                <span class="badge" style="background-color: rgba(99, 102, 241, 0.08); color: var(--accent); text-transform: uppercase; font-size: 0.75rem;">
                                    <?php echo e(str_replace('_', ' ', $user->role)); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($user->subscription && $user->subscription->plan_name === 'Premium' && $user->subscription->status === 'active'): ?>
                                    <span class="badge badge-approved"><i class="fa-solid fa-crown" style="margin-right: 0.25rem;"></i> Premium</span>
                                <?php else: ?>
                                    <span class="badge badge-pending">Free Plan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($user->email_verified_at): ?>
                                    <span style="color: var(--success); font-size: 0.85rem;"><i class="fa-solid fa-circle-check"></i> Verified</span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;"><i class="fa-solid fa-hourglass-half"></i> Unverified</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($user->status === 'active'): ?>
                                    <span class="badge badge-approved">Active</span>
                                <?php elseif($user->status === 'suspended'): ?>
                                    <span class="badge badge-rejected">Suspended</span>
                                <?php else: ?>
                                    <span class="badge badge-pending">Deactivated</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <?php if($user->id !== auth()->id()): ?> <!-- Do not suspend/toggle self -->
                                    <form action="<?php echo e(route('admin.users.status', $user->id)); ?>" method="POST" style="margin: 0; display: inline-block;">
                                        <?php echo csrf_field(); ?>
                                        <?php if($user->status === 'active'): ?>
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="btn btn-danger btn-sm" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #fca5a5;">
                                                <i class="fa-solid fa-user-slash"></i> Suspend
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-success btn-sm" style="background-color: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2); color: #a7f3d0;">
                                                <i class="fa-solid fa-user-check"></i> Activate
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Current Session</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="margin-top: 1.5rem;">
    <?php echo e($users->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\College-Music\resources\views/admin/users.blade.php ENDPATH**/ ?>