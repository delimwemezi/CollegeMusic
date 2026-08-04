<?php $__env->startSection('title', 'Release Approvals'); ?>
<?php $__env->startSection('header_title', 'Release Verification'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Catalog Review Queue</h1>
        <p class="page-subtitle">Inspect submitted music tracks, verify metadata, and manage platform ingestion</p>
    </div>
    <div>
        <a href="<?php echo e(route('admin')); ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Console
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pending & Managed Music Releases</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if($releases->isEmpty()): ?>
            <div style="text-align: center; padding: 4rem 1.5rem; color: var(--text-secondary);">
                <i class="fa-solid fa-circle-check" style="font-size: 3.5rem; color: var(--success); margin-bottom: 1rem;"></i>
                <h3>Review queue is completely empty!</h3>
                <p style="color: var(--text-secondary); margin-top: 0.25rem;">Artists have no pending submissions at the moment.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Release details</th>
                            <th>Artist / Creator</th>
                            <th>Billing</th>
                            <th>Status</th>
                            <th>Track Preview</th>
                            <th style="text-align: right;">Review Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php if($release->cover_image): ?>
                                        <img src="<?php echo e(asset('storage/' . $release->cover_image)); ?>" alt="Cover" style="width: 60px; height: 60px; border-radius: var(--radius-sm); object-fit: cover; border: 1px solid var(--border-color);">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background-color: var(--bg-input); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; border: 1px dashed var(--border-color);">
                                            <i class="fa-solid fa-music" style="color: var(--text-muted);"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 1rem;"><?php echo e($release->title); ?></div>
                                    <div style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.15rem;">
                                        Type: <strong style="text-transform: uppercase;"><?php echo e($release->type); ?></strong> &bull; Genre: <strong><?php echo e($release->genre); ?></strong> &bull; Lang: <strong><?php echo e($release->language); ?></strong>
                                    </div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                                        Copyright: <?php echo e($release->copyright_info); ?>

                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 500;"><?php echo e($release->artist->name); ?></div>
                                    <span style="font-size: 0.75rem; color: var(--text-secondary);">User: <?php echo e($release->artist->user->email ?? 'N/A'); ?></span>
                                </td>
                                <td>
                                    <?php if($release->billing_status === 'paid'): ?>
                                        <span class="badge badge-approved" style="font-size: 0.75rem;">Paid ($<?php echo e(number_format($release->price_paid, 2)); ?>)</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending" style="font-size: 0.75rem;">Unpaid / Subscribed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($release->distribution_status === 'pending'): ?>
                                        <span class="badge badge-pending">Pending Review</span>
                                    <?php elseif($release->distribution_status === 'approved'): ?>
                                        <span class="badge badge-approved">Approved</span>
                                    <?php elseif($release->distribution_status === 'distributed'): ?>
                                        <span class="badge badge-distributed">Distributed</span>
                                    <?php elseif($release->distribution_status === 'rejected'): ?>
                                        <span class="badge badge-rejected">Rejected</span>
                                    <?php elseif($release->distribution_status === 'pending_takedown'): ?>
                                        <span class="badge badge-rejected" style="background-color: rgba(239,68,68,0.1); color: var(--danger);">Takedown Req</span>
                                    <?php endif; ?>
                                </td>
                                <td style="width: 240px; vertical-align: middle;">
                                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                                        <?php $__currentLoopData = $release->tracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $track): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div style="font-size: 0.8rem; font-weight: 500; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                <i class="fa-solid fa-circle-play" style="color: var(--primary); margin-right: 0.25rem;"></i> <?php echo e($track->title); ?>

                                            </div>
                                            <audio controls style="height: 24px; width: 100%; outline: none;">
                                                <source src="<?php echo e(asset('storage/' . $track->audio_file)); ?>" type="audio/mpeg">
                                            </audio>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </td>
                                <td style="text-align: right; vertical-align: middle;">
                                    <?php if($release->distribution_status === 'pending'): ?>
                                        <div style="display: flex; flex-direction: column; gap: 0.4rem; max-width: 160px; margin-left: auto;">
                                            <form action="<?php echo e(route('admin.releases.review', $release->id)); ?>" method="POST" style="margin: 0;">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-success btn-sm btn-block">
                                                    <i class="fa-solid fa-circle-check"></i> Approve Release
                                                </button>
                                            </form>
                                            
                                            <button type="button" class="btn btn-danger btn-sm btn-block" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #fca5a5;" onclick="showRejectionInput('<?php echo e($release->id); ?>')">
                                                <i class="fa-solid fa-circle-xmark"></i> Reject Release
                                            </button>
                                        </div>

                                        <!-- Expandable Rejection Panel -->
                                        <div id="rejectionForm_<?php echo e($release->id); ?>" style="display: none; margin-top: 0.5rem; text-align: left; background-color: var(--bg-input); padding: 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); animation: fadeIn 0.2s ease;">
                                            <form action="<?php echo e(route('admin.releases.review', $release->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="reject">
                                                <label class="form-label" style="font-size: 0.75rem;">Reason for Rejection</label>
                                                <textarea name="rejection_reason" class="form-textarea" rows="2" style="font-size: 0.8rem; padding: 0.4rem;" placeholder="e.g. Low quality cover art or duplicate track metadata." required></textarea>
                                                <div style="display: flex; gap: 0.25rem; justify-content: flex-end; margin-top: 0.5rem;">
                                                    <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="hideRejectionInput('<?php echo e($release->id); ?>')">Cancel</button>
                                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Submit</button>
                                                </div>
                                            </form>
                                        </div>

                                    <?php elseif($release->distribution_status === 'approved'): ?>
                                        <form action="<?php echo e(route('admin.releases.review', $release->id)); ?>" method="POST" style="margin: 0;">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="action" value="distribute">
                                            <button type="submit" class="btn btn-primary btn-sm" style="background-color: var(--purple); border-color: var(--purple);">
                                                <i class="fa-solid fa-tower-broadcast"></i> Distribute to DSPs
                                            </button>
                                        </form>
                                    <?php elseif($release->distribution_status === 'distributed'): ?>
                                        <span style="color: var(--success); font-size: 0.85rem; font-weight: 500;">
                                            <i class="fa-solid fa-cloud-check"></i> Distributed
                                        </span>
                                    <?php elseif($release->distribution_status === 'pending_takedown'): ?>
                                        <form action="<?php echo e(route('admin.releases.review', $release->id)); ?>" method="POST" style="margin: 0;">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="rejection_reason" value="Takedown request processed by administrator.">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-trash-can"></i> Confirm Takedown
                                            </button>
                                        </form>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function showRejectionInput(id) {
        document.getElementById('rejectionForm_' + id).style.display = 'block';
    }
    
    function hideRejectionInput(id) {
        document.getElementById('rejectionForm_' + id).style.display = 'none';
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\College-Music\resources\views/admin/releases.blade.php ENDPATH**/ ?>