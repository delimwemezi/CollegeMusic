<?php $__env->startSection('title', 'My Catalogue'); ?>
<?php $__env->startSection('header_title', 'Catalogue Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Music Catalogue</h1>
        <p class="page-subtitle">Manage your uploads, releases, and artist profile details</p>
    </div>
    <div>
        <a href="<?php echo e(route('releases.create')); ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Distribute New Music
        </a>
    </div>
</div>

<!-- Tabs Toggle Navigation -->
<div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem;">
    <button class="btn btn-secondary tab-btn active" onclick="switchTab('releases-tab', this)" style="border-bottom: 2px solid var(--primary); border-radius: 0; background: none; border-left: none; border-right: none; border-top: none; padding-bottom: 1rem; color: var(--text-primary);">
        <i class="fa-solid fa-compact-disc"></i> Releases & Tracks
    </button>
    <button class="btn btn-secondary tab-btn" onclick="switchTab('profile-tab', this)" style="border-radius: 0; background: none; border: none; padding-bottom: 1rem; color: var(--text-secondary);">
        <i class="fa-solid fa-id-card"></i> Artist Verification & Profile
    </button>
    <?php if(auth()->user()->isRecordLabel() || auth()->user()->isDistributor()): ?>
        <button class="btn btn-secondary tab-btn" onclick="switchTab('artists-tab', this)" style="border-radius: 0; background: none; border: none; padding-bottom: 1rem; color: var(--text-secondary);">
            <i class="fa-solid fa-users"></i> Managed Artists
        </button>
    <?php endif; ?>
</div>

<!-- Tab 1: Releases and Tracks -->
<div id="releases-tab" class="tab-content-panel">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Distributed and Uploaded Releases</h3>
        </div>
        <div class="card-body">
            <?php if($releases->isEmpty()): ?>
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-music" style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-muted);"></i>
                    <p style="font-size: 1rem; margin-bottom: 1.5rem;">You haven't uploaded any music releases yet.</p>
                    <a href="<?php echo e(route('releases.create')); ?>" class="btn btn-primary">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload Your First Release
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Artist</th>
                                <th>Type</th>
                                <th>Genre</th>
                                <th>Release Date</th>
                                <th>Payment Status</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <?php if($release->cover_image): ?>
                                            <img src="<?php echo e(asset('storage/' . $release->cover_image)); ?>" alt="Cover" style="width: 48px; height: 48px; border-radius: var(--radius-sm); object-fit: cover;">
                                        <?php else: ?>
                                            <div style="width: 48px; height: 48px; background-color: var(--bg-input); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-record-vinyl" style="color: var(--text-muted);"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo e($release->title); ?></div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted);"><?php echo e(count($release->tracks)); ?> Track(s)</span>
                                    </td>
                                    <td><?php echo e($release->artist->name); ?></td>
                                    <td><span style="text-transform: uppercase; font-size: 0.8rem;"><?php echo e($release->type); ?></span></td>
                                    <td><?php echo e($release->genre); ?></td>
                                    <td><?php echo e($release->release_date ? $release->release_date->format('Y-m-d') : 'Immediate'); ?></td>
                                    <td>
                                        <?php if($release->billing_status === 'paid'): ?>
                                            <span class="badge badge-approved"><i class="fa-solid fa-circle-check"></i> Paid</span>
                                        <?php else: ?>
                                            <span class="badge badge-pending"><i class="fa-solid fa-clock"></i> Unpaid</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($release->distribution_status === 'pending'): ?>
                                            <span class="badge badge-pending">Review Pending</span>
                                        <?php elseif($release->distribution_status === 'approved'): ?>
                                            <span class="badge badge-approved">Approved</span>
                                        <?php elseif($release->distribution_status === 'distributed'): ?>
                                            <span class="badge badge-distributed">Distributed</span>
                                        <?php elseif($release->distribution_status === 'rejected'): ?>
                                            <span class="badge badge-rejected" title="Reason: <?php echo e($release->rejection_reason); ?>">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; vertical-align: middle;">
                                        <div style="display: inline-flex; gap: 0.5rem;">
                                            <a href="<?php echo e(route('releases.show', $release->id)); ?>" class="btn btn-secondary btn-sm">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>

                                            <!-- Allow edit if pending/rejected and unpaid or not fully approved -->
                                            <?php if(($release->distribution_status === 'pending' || $release->distribution_status === 'rejected')): ?>
                                                <a href="<?php echo e(route('releases.edit', $release->id)); ?>" class="btn btn-primary btn-sm" style="background-color: var(--accent); border-color: var(--accent);">
                                                    <i class="fa-solid fa-pen"></i> Edit
                                                </a>
                                            <?php endif; ?>

                                            <!-- Remove/Takedown request -->
                                            <?php if($release->distribution_status === 'distributed'): ?>
                                                <form action="<?php echo e(route('releases.takedown', $release->id)); ?>" method="POST" onsubmit="return confirm('Requesting a takedown will remove your release from all stores. Continue?');" style="margin: 0;">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa-solid fa-trash-can"></i> Takedown
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                
                                <?php if($release->distribution_status === 'rejected' && $release->rejection_reason): ?>
                                    <tr style="background-color: rgba(239, 68, 68, 0.03);">
                                        <td colspan="9" style="padding: 0.75rem 1.5rem; font-size: 0.85rem; border-top: none;">
                                            <div style="color: #fca5a5; display: flex; align-items: center; gap: 0.5rem;">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <strong>Rejection Reason:</strong> <?php echo e($release->rejection_reason); ?> (You can edit details and submit again)
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tab 2: Artist Profile & Verification -->
<div id="profile-tab" class="tab-content-panel" style="display: none;">
    <div class="grid-cols-2">
        <!-- Identity Verification Doc Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-shield-halved"></i> Artist Identity Verification</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1.5rem; padding: 1rem; border-radius: var(--radius-md); background-color: var(--bg-input); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h4 style="font-size: 0.95rem; margin-bottom: 0.25rem;">Verification Status</h4>
                        <p style="color: var(--text-secondary); font-size: 0.85rem;">Required to distribute music to Apple & Spotify</p>
                    </div>
                    <div>
                        <?php if($artist && $artist->verification_status === 'verified'): ?>
                            <span class="badge badge-approved" style="font-size: 0.9rem; padding: 0.4rem 1rem;"><i class="fa-solid fa-circle-check" style="margin-right: 0.35rem;"></i> Verified Account</span>
                        <?php elseif($artist && $artist->verification_status === 'pending'): ?>
                            <span class="badge badge-pending" style="font-size: 0.9rem; padding: 0.4rem 1rem;"><i class="fa-solid fa-hourglass-half" style="margin-right: 0.35rem;"></i> Review Pending</span>
                        <?php else: ?>
                            <span class="badge badge-rejected" style="font-size: 0.9rem; padding: 0.4rem 1rem;"><i class="fa-solid fa-circle-xmark" style="margin-right: 0.35rem;"></i> Unverified</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if(!$artist || $artist->verification_status !== 'verified'): ?>
                    <form action="<?php echo e(route('artist.verify')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label class="form-label" for="doc_type">Document Type</label>
                            <select id="doc_type" name="doc_type" class="form-select" required>
                                <option value="National ID">National Identity Card / ID Card</option>
                                <option value="Passport">International Passport</option>
                                <option value="Drivers License">Driver's License</option>
                                <option value="Label Certificate">Label Incorporation Document</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="doc_file">Upload Document Scan</label>
                            <input type="file" id="doc_file" name="doc_file" class="form-input" required>
                            <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.35rem;">Supported formats: PDF, JPEG, PNG (Max 5MB)</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Submit Documents for Review
                        </button>
                    </form>
                <?php else: ?>
                    <div style="text-align: center; color: var(--success); padding: 2rem 0;">
                        <i class="fa-solid fa-circle-check" style="font-size: 3.5rem; margin-bottom: 1rem;"></i>
                        <h4>Your account is fully verified!</h4>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.25rem;">You have unrestricted access to all major streaming platforms distribution portals.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Social Media configuration -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-share-nodes"></i> Artist Biography & Social Links</h3>
            </div>
            <div class="card-body">
                <?php if(!$artist): ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 2rem;">Please submit your verification documents first or upload music to create an artist profile.</p>
                <?php else: ?>
                    <form action="<?php echo e(route('artist.profile.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="artist_id" value="<?php echo e($artist->id); ?>">

                        <div class="form-group">
                            <label class="form-label" for="artist_bio">Biography</label>
                            <textarea id="artist_bio" name="bio" class="form-textarea" rows="4" placeholder="Tell the world about your music..."><?php echo e($artist->bio); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contact_info">Public Booking Contact Info</label>
                            <input type="text" id="contact_info" name="contact_info" class="form-input" value="<?php echo e($artist->contact_info); ?>" placeholder="e.g. bookings@myband.com">
                        </div>

                        <?php
                            $socials = $artist->social_links ?? [];
                        ?>

                        <div class="form-group">
                            <label class="form-label" for="facebook"><i class="fa-brands fa-facebook" style="color: #1877f2; margin-right: 0.25rem;"></i> Facebook Page URL</label>
                            <input type="url" id="facebook" name="facebook" class="form-input" value="<?php echo e($socials['facebook'] ?? ''); ?>" placeholder="https://facebook.com/myprofile">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="twitter"><i class="fa-brands fa-x-twitter" style="color: #fff; margin-right: 0.25rem;"></i> X (Twitter) URL</label>
                            <input type="url" id="twitter" name="twitter" class="form-input" value="<?php echo e($socials['twitter'] ?? ''); ?>" placeholder="https://x.com/myprofile">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="instagram"><i class="fa-brands fa-instagram" style="color: #e1306c; margin-right: 0.25rem;"></i> Instagram URL</label>
                            <input type="url" id="instagram" name="instagram" class="form-input" value="<?php echo e($socials['instagram'] ?? ''); ?>" placeholder="https://instagram.com/myprofile">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="spotify"><i class="fa-brands fa-spotify" style="color: #1ed760; margin-right: 0.25rem;"></i> Spotify Artist URL</label>
                            <input type="url" id="spotify" name="spotify" class="form-input" value="<?php echo e($socials['spotify'] ?? ''); ?>" placeholder="https://open.spotify.com/artist/... ">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-user-check"></i> Save Artist Profile
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tab 3: Managed Artists (Labels/Distributors only) -->
<?php if(auth()->user()->isRecordLabel() || auth()->user()->isDistributor()): ?>
    <div id="artists-tab" class="tab-content-panel" style="display: none;">
        <div class="grid-cols-2">
            <!-- List of Artists -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registered Artists</h3>
                </div>
                <div class="card-body">
                    <?php if($artists->isEmpty()): ?>
                        <p style="color: var(--text-muted); text-align: center; padding: 2rem;">No artists registered yet. Create one on the right to start uploading catalog releases.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Bio Summary</th>
                                        <th>Verification</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $artists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $managed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td style="font-weight: bold;"><?php echo e($managed->name); ?></td>
                                            <td style="color: var(--text-secondary); font-size: 0.85rem;">
                                                <?php echo e(Str::limit($managed->bio ?? 'No bio filled yet.', 50)); ?>

                                            </td>
                                            <td>
                                                <?php if($managed->verification_status === 'verified'): ?>
                                                    <span class="badge badge-approved">Verified</span>
                                                <?php elseif($managed->verification_status === 'pending'): ?>
                                                    <span class="badge badge-pending">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge badge-rejected">Unverified</span>
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

            <!-- Create Artist Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-user-plus"></i> Register New Managed Artist</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('artist.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label class="form-label" for="artist_name">Artist Stage Name</label>
                            <input type="text" id="artist_name" name="name" class="form-input" placeholder="e.g. The Beats Maker" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="artist_desc">Artist Biography</label>
                            <textarea id="artist_desc" name="bio" class="form-textarea" rows="3" placeholder="Brief biography of the artist..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contact">Artist Contact Info / Legal Info</label>
                            <input type="text" id="contact" name="contact_info" class="form-input" placeholder="e.g. manager@artist.com">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-plus"></i> Add Artist Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function switchTab(tabId, btn) {
        // Hide all tab panels
        var panels = document.getElementsByClassName('tab-content-panel');
        for (var i = 0; i < panels.length; i++) {
            panels[i].style.display = 'none';
        }
        
        // Show selected panel
        document.getElementById(tabId).style.display = 'block';

        // Reset styling for all buttons
        var buttons = document.getElementsByClassName('tab-btn');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('active');
            buttons[i].style.borderBottom = 'none';
            buttons[i].style.color = 'var(--text-secondary)';
        }

        // Highlight selected button
        btn.classList.add('active');
        btn.style.borderBottom = '2px solid var(--primary)';
        btn.style.color = 'var(--text-primary)';
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\College-Music\resources\views/catalogue/index.blade.php ENDPATH**/ ?>