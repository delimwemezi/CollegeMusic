<?php $__env->startSection('title', 'Explore Catalog'); ?>
<?php $__env->startSection('header_title', 'Explore Catalog'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Search & Filter Music</h1>
        <p class="page-subtitle">Find songs, albums, EPs, and artists across the platform</p>
    </div>
</div>

<!-- Search & Filters Card -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <form action="<?php echo e(route('search')); ?>" method="GET">
            <div style="display: flex; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="flex: 1; position: relative;">
                    <input type="text" name="q" class="form-input" value="<?php echo e(request('q')); ?>" placeholder="Search by song name, album title, or artist name..." style="padding-left: 2.75rem;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-search"></i> Search
                </button>
            </div>

            <!-- Advanced Filters Drawer -->
            <div style="background-color: var(--bg-input); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="margin-bottom: 1rem; font-size: 0.85rem; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.05em;">
                    <i class="fa-solid fa-sliders"></i> Advanced Filtering Options
                </h4>
                
                <div class="grid-cols-4" style="margin-bottom: 0;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-size: 0.8rem;">Filter by Genre</label>
                        <select name="genre" class="form-select" style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                            <option value="">All Genres</option>
                            <?php $__currentLoopData = $allGenres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g); ?>" <?php echo e(request('genre') == $g ? 'selected' : ''); ?>><?php echo e($g); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-size: 0.8rem;">Release Type</label>
                        <select name="type" class="form-select" style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                            <option value="">All Types</option>
                            <option value="single" <?php echo e(request('type') == 'single' ? 'selected' : ''); ?>>Single</option>
                            <option value="ep" <?php echo e(request('type') == 'ep' ? 'selected' : ''); ?>>EP</option>
                            <option value="album" <?php echo e(request('type') == 'album' ? 'selected' : ''); ?>>Album</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-size: 0.8rem;">Released Since</label>
                        <input type="date" name="date" class="form-input" value="<?php echo e(request('date')); ?>" style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-size: 0.8rem;">Artist Name Contains</label>
                        <input type="text" name="artist" class="form-input" value="<?php echo e(request('artist')); ?>" placeholder="Artist name..." style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Search Results Tabs Toggle -->
<div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem;">
    <button class="btn btn-secondary tab-btn active" onclick="switchSearchTab('tracks-results', this)" style="border-bottom: 2px solid var(--primary); border-radius: 0; background: none; border-left: none; border-right: none; border-top: none; padding-bottom: 1rem; color: var(--text-primary);">
        <i class="fa-solid fa-music"></i> Songs (<?php echo e(count($tracks)); ?>)
    </button>
    <button class="btn btn-secondary tab-btn" onclick="switchSearchTab('releases-results', this)" style="border-radius: 0; background: none; border: none; padding-bottom: 1rem; color: var(--text-secondary);">
        <i class="fa-solid fa-record-vinyl"></i> Albums & EPs (<?php echo e(count($releases)); ?>)
    </button>
    <button class="btn btn-secondary tab-btn" onclick="switchSearchTab('artists-results', this)" style="border-radius: 0; background: none; border: none; padding-bottom: 1rem; color: var(--text-secondary);">
        <i class="fa-solid fa-user-astronaut"></i> Artists (<?php echo e(count($artists)); ?>)
    </button>
</div>

<!-- Tab Panel: Songs -->
<div id="tracks-results" class="search-tab-panel">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Matching Audio Tracks</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if($tracks->isEmpty()): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No matching songs found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Song Title</th>
                                <th>Artist</th>
                                <th>Album / Release</th>
                                <th>Genre</th>
                                <th>ISRC</th>
                                <th>Preview</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $tracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $track): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td style="font-weight: bold;"><?php echo e($track->title); ?></td>
                                    <td><?php echo e($track->artist_name); ?></td>
                                    <td>
                                        <?php if($track->release): ?>
                                            <a href="<?php echo e(route('releases.show', $track->release->id)); ?>"><?php echo e($track->release->title); ?></a>
                                        <?php else: ?>
                                            Single Upload
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($track->release ? $track->release->genre : 'Unknown'); ?></td>
                                    <td style="font-family: monospace; font-size: 0.85rem;"><?php echo e($track->isrc); ?></td>
                                    <td>
                                        <audio controls style="height: 24px; max-width: 200px;">
                                            <source src="<?php echo e(asset('storage/' . $track->audio_file)); ?>" type="audio/mpeg">
                                        </audio>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tab Panel: Releases -->
<div id="releases-results" class="search-tab-panel" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Matching Albums, EPs and Singles</h3>
        </div>
        <div class="card-body">
            <?php if($releases->isEmpty()): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No matching releases found.</p>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                    <?php $__currentLoopData = $releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column;">
                            <div style="padding: 1.25rem; display: flex; gap: 1rem; align-items: center;">
                                <?php if($rel->cover_image): ?>
                                    <img src="<?php echo e(asset('storage/' . $rel->cover_image)); ?>" alt="Cover" style="width: 70px; height: 70px; border-radius: var(--radius-sm); object-fit: cover; border: 1px solid var(--border-color);">
                                <?php else: ?>
                                    <div style="width: 70px; height: 70px; background-color: var(--bg-input); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; border: 1px dashed var(--border-color);">
                                        <i class="fa-solid fa-record-vinyl" style="color: var(--text-muted);"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h4 style="font-size: 1.05rem; margin-bottom: 0.15rem;">
                                        <a href="<?php echo e(route('releases.show', $rel->id)); ?>"><?php echo e($rel->title); ?></a>
                                    </h4>
                                    <p style="color: var(--text-secondary); font-size: 0.8rem;">Artist: <strong><?php echo e($rel->artist->name); ?></strong></p>
                                    <span style="font-size: 0.75rem; text-transform: uppercase; padding: 0.1rem 0.4rem; background-color: var(--bg-input); border-radius: 4px; color: var(--accent);">
                                        <?php echo e($rel->type); ?>

                                    </span>
                                </div>
                            </div>
                            <div style="border-top: 1px solid var(--border-color); padding: 0.75rem 1.25rem; font-size: 0.8rem; color: var(--text-secondary); display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                                <span>Genre: <strong><?php echo e($rel->genre); ?></strong></span>
                                <span><?php echo e($rel->release_date ? $rel->release_date->format('Y-m-d') : 'Immediate'); ?></span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tab Panel: Artists -->
<div id="artists-results" class="search-tab-panel" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Matching Artist Profiles</h3>
        </div>
        <div class="card-body">
            <?php if($artists->isEmpty()): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No matching artists found.</p>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.5rem;">
                    <?php $__currentLoopData = $artists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card" style="margin-bottom: 0; text-align: center; padding: 1.5rem;">
                            <?php if($art->profile_picture): ?>
                                <img src="<?php echo e(asset('storage/' . $art->profile_picture)); ?>" alt="Avatar" style="width: 80px; height: 80px; border-radius: var(--radius-full); object-fit: cover; border: 2px solid var(--border-color); margin: 0 auto 1rem;">
                            <?php else: ?>
                                <div style="width: 80px; height: 80px; border-radius: var(--radius-full); background-color: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 1.5rem; margin: 0 auto 1rem;">
                                    <?php echo e(strtoupper(substr($art->name, 0, 2))); ?>

                                </div>
                            <?php endif; ?>
                            
                            <h4 style="font-size: 1.1rem; margin-bottom: 0.25rem;"><?php echo e($art->name); ?></h4>
                            <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1rem;">
                                <?php echo e($art->verification_status === 'verified' ? 'Verified Artist' : 'Profile Awaiting Verification'); ?>

                            </p>
                            
                            <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.8rem;">
                                <?php echo e($art->bio ?? 'No biography details provided yet.'); ?>

                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function switchSearchTab(tabId, btn) {
        var panels = document.getElementsByClassName('search-tab-panel');
        for (var i = 0; i < panels.length; i++) {
            panels[i].style.display = 'none';
        }
        document.getElementById(tabId).style.display = 'block';

        var buttons = document.getElementsByClassName('tab-btn');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('active');
            buttons[i].style.borderBottom = 'none';
            buttons[i].style.color = 'var(--text-secondary)';
        }

        btn.classList.add('active');
        btn.style.borderBottom = '2px solid var(--primary)';
        btn.style.color = 'var(--text-primary)';
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\College-Music\resources\views/search/index.blade.php ENDPATH**/ ?>