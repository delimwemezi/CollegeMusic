<?php $__env->startSection('title', 'Security Audit Logs'); ?>
<?php $__env->startSection('header_title', 'System Monitoring'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Security & Audit Activity Logs</h1>
        <p class="page-subtitle">Inspect historical user actions, access control changes, and security footprints for compliance auditing</p>
    </div>
    <div>
        <a href="<?php echo e(route('admin')); ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Console
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Historical Audit Trial Entries</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User Email</th>
                        <th>User Role</th>
                        <th>Action Category</th>
                        <th>Activity Description</th>
                        <th>IP Address</th>
                        <th>User Agent Signature</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="color: var(--text-secondary); white-space: nowrap;"><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></td>
                            <td style="font-weight: 600;"><?php echo e($log->user ? $log->user->email : 'System Guest'); ?></td>
                            <td>
                                <?php if($log->user): ?>
                                    <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary);">
                                        <?php echo e(str_replace('_', ' ', $log->user->role)); ?>

                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.75rem;">GUEST</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge" style="background-color: var(--bg-input); color: var(--primary); text-transform: capitalize;">
                                    <?php echo e(str_replace('_', ' ', $log->action)); ?>

                                </span>
                            </td>
                            <td style="color: var(--text-primary); max-width: 300px; font-weight: 500;"><?php echo e($log->description); ?></td>
                            <td style="font-family: monospace; color: var(--text-secondary);"><?php echo e($log->ip_address); ?></td>
                            <td style="color: var(--text-muted); font-size: 0.75rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo e($log->user_agent); ?>">
                                <?php echo e($log->user_agent); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="margin-top: 1.5rem;">
    <?php echo e($logs->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\College-Music\resources\views/admin/logs.blade.php ENDPATH**/ ?>