<?php $__env->startSection('title', 'Artist Verifications'); ?>
<?php $__env->startSection('header_title', 'Artist Verification Queue'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Identity Verification Queue</h1>
        <p class="page-subtitle">Inspect submitted identification documents and verify artist profiles for listing eligibility</p>
    </div>
    <div>
        <a href="<?php echo e(route('admin')); ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Console
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Verification Request Records</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if($artists->isEmpty()): ?>
            <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No artist verification records found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Artist Stage Name</th>
                            <th>Contact Info</th>
                            <th>Associated User Email</th>
                            <th>Uploaded Docs</th>
                            <th>Verification Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $artists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $artist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="font-weight: bold;"><?php echo e($artist->name); ?></td>
                                <td><?php echo e($artist->contact_info ?? 'Not provided'); ?></td>
                                <td><?php echo e($artist->user ? $artist->user->email : 'Label Created Profile'); ?></td>
                                <td style="vertical-align: middle;">
                                    <?php
                                        $docs = $artist->verification_documents ?? [];
                                    ?>
                                    <?php $__empty_1 = true; $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div style="margin-bottom: 0.25rem;">
                                            <a href="<?php echo e(asset('storage/' . $doc['path'])); ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <i class="fa-solid fa-file-pdf"></i> View <?php echo e($doc['type']); ?>

                                            </a>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">No documents uploaded</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($artist->verification_status === 'verified'): ?>
                                        <span class="badge badge-approved">Verified Account</span>
                                    <?php elseif($artist->verification_status === 'pending'): ?>
                                        <span class="badge badge-pending">Review Pending</span>
                                    <?php else: ?>
                                        <span class="badge badge-rejected">Unverified / Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if($artist->verification_status === 'pending'): ?>
                                        <div style="display: inline-flex; gap: 0.4rem;">
                                            <form action="<?php echo e(route('admin.artists.verify', $artist->id)); ?>" method="POST" style="margin: 0;">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="status" value="verified">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa-solid fa-user-check"></i> Approve
                                                </button>
                                            </form>
                                            
                                            <form action="<?php echo e(route('admin.artists.verify', $artist->id)); ?>" method="POST" style="margin: 0;">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-danger btn-sm" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #fca5a5;">
                                                    <i class="fa-solid fa-user-xmark"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Reviewed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div style="margin-top: 1.5rem;">
    <?php echo e($artists->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\College-Music\resources\views/admin/artists.blade.php ENDPATH**/ ?>