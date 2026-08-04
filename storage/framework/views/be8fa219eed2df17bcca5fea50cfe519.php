<?php $__env->startSection('title', __('messages.dashboard')); ?>
<?php $__env->startSection('header_title', __('messages.dashboard')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('messages.welcome_back_user')); ?>, <?php echo e(auth()->user()->name); ?>!</h1>
        <p class="page-subtitle">
            <?php echo e(__('messages.account_type')); ?>: <strong style="text-transform: capitalize;"><?php echo e(str_replace('_', ' ', auth()->user()->role)); ?></strong> 
            &bull; <?php echo e(__('messages.plan')); ?>: 
            <?php if(auth()->user()->subscription && auth()->user()->subscription->status === 'active'): ?>
                <span style="color: var(--warning); font-weight: bold;"><i class="fa-solid fa-crown"></i> <?php echo e(__('messages.premium_plan')); ?></span>
            <?php else: ?>
                <span style="color: var(--text-secondary);"><?php echo e(__('messages.free_plan')); ?></span>
            <?php endif; ?>
        </p>
    </div>
    <div>
        <a href="<?php echo e(route('releases.create')); ?>" class="btn btn-primary">
            <i class="fa-solid fa-cloud-arrow-up"></i> <?php echo e(__('messages.distribute_new_music')); ?>

        </a>
    </div>
</div>

<!-- Warning Card: Verification Reminder -->
<?php if($showVerificationWarning): ?>
    <div class="alert alert-warning" style="margin-bottom: 2rem; border-color: rgba(245, 158, 11, 0.3);">
        <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.75rem; color: var(--warning);"></i>
        <div style="flex: 1;">
            <h4 style="font-weight: bold; margin-bottom: 0.25rem; color: var(--text-primary);"><?php echo e(__('messages.verification_warning_title')); ?></h4>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">
                <?php echo e(__('messages.verification_warning_desc')); ?>

            </p>
        </div>
        <div>
            <a href="<?php echo e(route('catalogue')); ?>" class="btn btn-secondary btn-sm" style="background-color: var(--bg-card); border-color: var(--border-color); color: #fff;">
                <i class="fa-solid fa-id-card"></i> <?php echo e(__('messages.verify_account_now')); ?>

            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Stats Counters Grid -->
<div class="grid-stats">
    <div class="stat-card">
        <div>
            <div class="stat-title"><?php echo e(__('messages.catalog_releases')); ?></div>
            <div class="stat-value"><?php echo e($releasesCount); ?></div>
            <div class="stat-change" style="color: var(--text-muted);">Uploaded singles & albums</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-compact-disc"></i></div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title"><?php echo e(__('messages.cumulative_streams')); ?></div>
            <div class="stat-value"><?php echo e(number_format($totalStreams)); ?></div>
            <div class="stat-change" style="color: var(--success);"><i class="fa-solid fa-arrow-trend-up"></i> Playbacks across DSPs</div>
        </div>
        <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fa-solid fa-play"></i></div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title"><?php echo e(__('messages.available_payout_balance')); ?></div>
            <div class="stat-value">$<?php echo e(number_format($availableBalance, 2)); ?></div>
            <div class="stat-change" style="color: var(--text-muted);">Royalties available to withdraw</div>
        </div>
        <div class="stat-icon" style="background-color: rgba(139, 92, 246, 0.1); color: var(--purple);"><i class="fa-solid fa-wallet"></i></div>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Recent Activity Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-chart-line"></i> <?php echo e(__('messages.playback_activity_trend')); ?></h3>
            <a href="<?php echo e(route('analytics')); ?>" style="font-size: 0.85rem; font-weight: 500;"><?php echo e(__('messages.details')); ?></a>
        </div>
        <div class="card-body" style="height: 280px; position: relative;">
            <canvas id="dashboardStreamChart"></canvas>
        </div>
    </div>

    <!-- Alert Notifications Panel -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-bell"></i> <?php echo e(__('messages.recent_alerts')); ?></h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: flex-start; gap: 1rem; animation: fadeIn 0.3s ease;">
                    <?php if(str_contains($notif->action, 'approve')): ?>
                        <div style="background-color: rgba(16, 185, 129, 0.1); color: var(--success); padding: 0.5rem; border-radius: var(--radius-sm);"><i class="fa-solid fa-circle-check"></i></div>
                    <?php elseif(str_contains($notif->action, 'reject')): ?>
                        <div style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.5rem; border-radius: var(--radius-sm);"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <?php else: ?>
                        <div style="background-color: rgba(59, 130, 246, 0.1); color: var(--primary); padding: 0.5rem; border-radius: var(--radius-sm);"><i class="fa-solid fa-info"></i></div>
                    <?php endif; ?>
                    
                    <div style="flex: 1;">
                        <h4 style="font-size: 0.875rem; color: var(--text-primary); font-weight: 600;">
                            <?php if($notif->action === 'release_approved'): ?> Release Approved
                            <?php elseif($notif->action === 'release_rejected'): ?> Release Rejected
                            <?php elseif($notif->action === 'release_distributed'): ?> Release Ingested / Distributed
                            <?php elseif($notif->action === 'withdrawal_completed'): ?> Payout Approved & Paid
                            <?php elseif($notif->action === 'withdrawal_rejected'): ?> Payout Request Rejected
                            <?php else: ?> Notification Alert
                            <?php endif; ?>
                        </h4>
                        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.15rem; line-height: 1.4;"><?php echo e($notif->description); ?></p>
                        <small style="color: var(--text-muted); font-size: 0.7rem; display: block; margin-top: 0.25rem;"><?php echo e($notif->created_at->diffForHumans()); ?></small>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 3rem 0; font-size: 0.9rem;">
                    <i class="fa-solid fa-bell-slash" style="font-size: 2.5rem; margin-bottom: 0.75rem; color: var(--text-muted);"></i>
                    <p><?php echo e(__('messages.no_alerts')); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Releases list -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-record-vinyl"></i> <?php echo e(__('messages.recent_releases')); ?></h3>
        <a href="<?php echo e(route('catalogue')); ?>" style="font-size: 0.85rem; font-weight: 500;"><?php echo e(__('messages.view_entire_catalog')); ?></a>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if($releases->isEmpty()): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <p><?php echo e(__('messages.no_releases')); ?></p>
                <a href="<?php echo e(route('releases.create')); ?>" class="btn btn-primary btn-sm" style="margin-top: 0.5rem;"><?php echo e(__('messages.upload_music_now')); ?></a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Release Title</th>
                            <th>Release Type</th>
                            <th>Genre</th>
                            <th>Tracks</th>
                            <th>Stores</th>
                            <th>Billing Status</th>
                            <th>Distribution Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php if($rel->cover_image): ?>
                                        <img src="<?php echo e(asset('storage/' . $rel->cover_image)); ?>" alt="Cover" style="width: 48px; height: 48px; border-radius: var(--radius-sm); object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 48px; height: 48px; background-color: var(--bg-input); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-music" style="color: var(--text-muted);"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="font-size: 0.95rem;"><a href="<?php echo e(route('releases.show', $rel->id)); ?>"><?php echo e($rel->title); ?></a></strong>
                                    <?php if(auth()->user()->isRecordLabel() || auth()->user()->isDistributor()): ?>
                                        <span style="font-size: 0.75rem; color: var(--text-secondary); display: block;">Artist: <?php echo e($rel->artist->name); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><span style="text-transform: uppercase; font-size: 0.8rem; font-weight: 500;"><?php echo e($rel->type); ?></span></td>
                                <td><?php echo e($rel->genre); ?></td>
                                <td><?php echo e(count($rel->tracks)); ?> Track(s)</td>
                                <td>
                                    <div style="display: flex; gap: 0.25rem;">
                                        <?php $__currentLoopData = $rel->stores->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span style="font-size: 0.75rem; background-color: var(--bg-input); padding: 0.1rem 0.35rem; border-radius: 4px;" title="<?php echo e($store->store_name); ?>">
                                                <?php echo e($store->store_name); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(count($rel->stores) > 4): ?>
                                            <span style="font-size: 0.75rem; color: var(--text-muted);">+<?php echo e(count($rel->stores) - 4); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($rel->billing_status === 'paid'): ?>
                                        <span class="badge badge-approved">Paid</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">Unpaid</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($rel->distribution_status === 'pending'): ?>
                                        <span class="badge badge-pending">Review Pending</span>
                                    <?php elseif($rel->distribution_status === 'approved'): ?>
                                        <span class="badge badge-approved">Approved</span>
                                    <?php elseif($rel->distribution_status === 'distributed'): ?>
                                        <span class="badge badge-distributed">Distributed</span>
                                    <?php elseif($rel->distribution_status === 'rejected'): ?>
                                        <span class="badge badge-rejected">Rejected</span>
                                    <?php elseif($rel->distribution_status === 'pending_takedown'): ?>
                                        <span class="badge badge-rejected" style="background-color: rgba(239,68,68,0.1); color: var(--danger);">Takedown Req</span>
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
    // Activity Chart configuration
    var ctxDashboard = document.getElementById('dashboardStreamChart');
    if (ctxDashboard) {
        var dashboardChart = new Chart(ctxDashboard.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Streaming playbacks count',
                    data: [
                        <?php echo e($totalStreams * 0.05); ?>, 
                        <?php echo e($totalStreams * 0.07); ?>, 
                        <?php echo e($totalStreams * 0.06); ?>, 
                        <?php echo e($totalStreams * 0.09); ?>, 
                        <?php echo e($totalStreams * 0.12); ?>, 
                        <?php echo e($totalStreams * 0.10); ?>, 
                        <?php echo e($totalStreams * 0.14); ?>, 
                        <?php echo e($totalStreams * 0.11); ?>, 
                        <?php echo e($totalStreams * 0.08); ?>, 
                        <?php echo e($totalStreams * 0.06); ?>, 
                        <?php echo e($totalStreams * 0.05); ?>, 
                        <?php echo e($totalStreams * 0.07); ?>

                    ],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.03)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.02)' },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.02)' },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    }
                }
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\College-Music\resources\views/dashboard.blade.php ENDPATH**/ ?>