<?php $__env->startSection('title', __('messages.explore_music')); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .explore-hero {
        padding: 4rem 0 3rem 0;
        text-align: center;
        background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.1), transparent 50%);
    }

    .explore-title {
        font-size: 2.75rem;
        font-weight: 800;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, #a5b4fc, #818cf8, #6366f1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .explore-subtitle {
        color: var(--text-secondary);
        max-width: 600px;
        margin: 0 auto;
        font-size: 1.05rem;
    }

    /* Filters Drawer Style */
    .filter-drawer {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
    }

    .search-input-wrapper {
        position: relative;
        flex: 1;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    .search-input-field {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        background-color: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-full);
        color: var(--text-primary);
        font-size: 0.95rem;
        transition: var(--transition);
    }

    .search-input-field:focus {
        border-color: var(--primary);
        background-color: rgba(255, 255, 255, 0.04);
        outline: none;
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.15);
    }

    /* Tabs buttons styling */
    .explore-tabs {
        display: flex;
        gap: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 2.5rem;
    }

    .explore-tab-btn {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-family: var(--font-heading);
        font-weight: 600;
        font-size: 1.05rem;
        padding-bottom: 1rem;
        cursor: pointer;
        position: relative;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .explore-tab-btn:hover {
        color: var(--text-primary);
    }

    .explore-tab-btn.active {
        color: var(--primary);
    }

    .explore-tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 3px;
        background-color: var(--primary);
        border-radius: var(--radius-full);
        box-shadow: 0 0 10px var(--primary);
    }

    /* Store Badge Styles */
    .store-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .store-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.75rem;
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 600;
        color: #ffffff;
        transition: var(--transition);
    }

    .store-badge:hover {
        transform: translateY(-1px);
        filter: brightness(1.1);
        color: #ffffff;
    }

    .badge-spotify {
        background-color: #1DB954;
    }

    .badge-apple {
        background-color: #fc3c44;
    }

    .badge-youtube {
        background-color: #ff0000;
    }

    .badge-generic {
        background-color: var(--bg-input);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
    }

    /* Play Button Design */
    .play-trigger-btn {
        width: 38px;
        height: 38px;
        border-radius: var(--radius-full);
        background-color: var(--primary);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        border: none;
    }

    .play-trigger-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 0 12px var(--primary);
        color: #ffffff;
    }

    /* Table custom layout */
    .explore-track-row {
        transition: var(--transition);
    }
    .explore-track-row:hover {
        background-color: rgba(255, 255, 255, 0.01);
    }

    /* Floating bottom player */
    .floating-player {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 90px;
        background-color: rgba(17, 24, 39, 0.85);
        backdrop-filter: blur(20px);
        border-top: 1px solid var(--border-color);
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.5);
        z-index: 2000;
        transform: translateY(100%);
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        align-items: center;
    }

    .floating-player.show {
        transform: translateY(0);
    }

    .player-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 2rem;
    }

    .player-metadata {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 240px;
        max-width: 320px;
    }

    .player-cover {
        width: 54px;
        height: 54px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        border: 1px solid rgba(255,255,255,0.08);
    }

    .player-details {
        overflow: hidden;
    }

    .player-title {
        font-family: var(--font-heading);
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.15rem;
    }

    .player-artist {
        font-size: 0.8rem;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .player-controls {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.35rem;
        max-width: 500px;
    }

    .player-buttons {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .player-btn {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 1rem;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .player-btn:hover {
        color: var(--text-primary);
    }

    .player-btn-main {
        width: 40px;
        height: 40px;
        background-color: #ffffff;
        color: #0b0f19;
        border-radius: var(--radius-full);
        font-size: 1.1rem;
    }

    .player-btn-main:hover {
        transform: scale(1.08);
        background-color: var(--primary);
        color: #ffffff;
    }

    .player-progress-area {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .player-time {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-family: monospace;
        min-width: 35px;
    }

    .player-progress-bar-wrapper {
        flex: 1;
        height: 4px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-full);
        position: relative;
        cursor: pointer;
    }

    .player-progress-bar-fill {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background-color: var(--primary);
        border-radius: var(--radius-full);
        width: 0%;
        box-shadow: 0 0 8px var(--primary);
    }

    .player-side-actions {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        min-width: 240px;
        justify-content: flex-end;
    }

    .player-volume-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .player-volume-slider {
        width: 80px;
        height: 4px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    /* Grid columns */
    .explore-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .explore-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
    }

    .explore-card:hover {
        transform: translateY(-4px);
        border-color: var(--primary-glow);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }

    .explore-card-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .explore-card-cover-wrapper {
        position: relative;
        aspect-ratio: 1;
        background-color: rgba(0,0,0,0.2);
        overflow: hidden;
    }

    .explore-card-cover {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .explore-card:hover .explore-card-cover {
        transform: scale(1.05);
    }

    .explore-card-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background-color: rgba(11, 15, 25, 0.75);
        backdrop-filter: blur(8px);
        padding: 0.25rem 0.6rem;
        border-radius: var(--radius-sm);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--primary);
        border: 1px solid rgba(255,255,255,0.05);
    }

    .artist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 1.5rem;
    }

    .artist-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem 1.5rem;
        text-align: center;
        transition: var(--transition);
    }

    .artist-card:hover {
        transform: translateY(-4px);
        border-color: var(--primary-glow);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }

    .artist-avatar {
        width: 96px;
        height: 96px;
        border-radius: var(--radius-full);
        object-fit: cover;
        margin: 0 auto 1.25rem;
        border: 2px solid var(--border-color);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .artist-avatar-placeholder {
        width: 96px;
        height: 96px;
        border-radius: var(--radius-full);
        background-color: var(--primary);
        color: #ffffff;
        font-weight: bold;
        font-size: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="explore-hero">
    <div class="container">
        <h1 class="explore-title"><?php echo e(__('messages.explore_music')); ?></h1>
        <p class="explore-subtitle"><?php echo e(__('messages.explore_subtitle')); ?></p>
    </div>
</div>

<div class="container" style="margin-bottom: 7rem;">
    <!-- Filters Drawer Card -->
    <div class="filter-drawer">
        <form action="<?php echo e(route('explore')); ?>" method="GET">
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <div class="search-input-wrapper">
                    <input type="text" name="q" class="search-input-field" value="<?php echo e(request('q')); ?>" placeholder="Search by song name, album title, or artist name...">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <button type="submit" class="btn btn-primary" style="border-radius: var(--radius-full); padding: 0 2rem;">
                    <i class="fa-solid fa-search"></i> Search
                </button>
            </div>

            <!-- Advanced Filters Grid -->
            <div style="background-color: rgba(255,255,255,0.01); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="margin-bottom: 1rem; font-size: 0.85rem; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-sliders" style="color: var(--primary);"></i> Filters
                </h4>
                
                <div class="grid-cols-4" style="margin-bottom: 0;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-size: 0.8rem;">Genre</label>
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

    <!-- Explore Tabs Menu -->
    <div class="explore-tabs">
        <button class="explore-tab-btn active" onclick="switchExploreTab('tracks-panel', this)">
            <i class="fa-solid fa-music"></i> Songs (<?php echo e(count($tracks)); ?>)
        </button>
        <button class="explore-tab-btn" onclick="switchExploreTab('releases-panel', this)">
            <i class="fa-solid fa-compact-disc"></i> Albums & EPs (<?php echo e(count($releases)); ?>)
        </button>
        <button class="explore-tab-btn" onclick="switchExploreTab('artists-panel', this)">
            <i class="fa-solid fa-user-astronaut"></i> Artists (<?php echo e(count($artists)); ?>)
        </button>
    </div>

    <!-- Tab Panel: Songs -->
    <div id="tracks-panel" class="explore-tab-panel">
        <div class="card" style="border-radius: var(--radius-lg); overflow: hidden;">
            <div class="card-body" style="padding: 0;">
                <?php if($tracks->isEmpty()): ?>
                    <div style="text-align: center; color: var(--text-muted); padding: 4rem 0;">
                        <i class="fa-solid fa-music" style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-muted);"></i>
                        <p>No matching songs found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 60px; text-align: center;">Listen</th>
                                    <th>Song Title</th>
                                    <th>Artist</th>
                                    <th>Release</th>
                                    <th>Genre</th>
                                    <th>Available on Stores</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $tracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $track): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $coverUrl = $track->release && $track->release->cover_image 
                                            ? asset('storage/' . $track->release->cover_image) 
                                            : null;
                                        $audioUrl = asset('storage/' . $track->audio_file);
                                    ?>
                                    <tr class="explore-track-row">
                                        <td style="text-align: center; vertical-align: middle;">
                                            <button class="play-trigger-btn" onclick="playTrack('<?php echo e($track->title); ?>', '<?php echo e($track->artist_name); ?>', '<?php echo e($coverUrl); ?>', '<?php echo e($audioUrl); ?>')">
                                                <i class="fa-solid fa-play"></i>
                                            </button>
                                        </td>
                                        <td style="font-weight: 700; vertical-align: middle; color: var(--text-primary);"><?php echo e($track->title); ?></td>
                                        <td style="vertical-align: middle;"><?php echo e($track->artist_name); ?></td>
                                        <td style="vertical-align: middle; color: var(--text-secondary);">
                                            <?php echo e($track->release ? $track->release->title : 'Single'); ?>

                                        </td>
                                        <td style="vertical-align: middle;"><span style="padding: 0.2rem 0.5rem; background: rgba(99,102,241,0.08); border-radius: 4px; color: var(--primary); font-size: 0.8rem; font-weight: 500;"><?php echo e($track->release ? $track->release->genre : 'Unknown'); ?></span></td>
                                        <td style="vertical-align: middle;">
                                            <div class="store-badges">
                                                <a href="https://open.spotify.com/search/<?php echo e(urlencode($track->artist_name . ' ' . $track->title)); ?>" target="_blank" class="store-badge badge-spotify">
                                                    <i class="fa-brands fa-spotify"></i> Spotify
                                                </a>
                                                <a href="https://music.apple.com/search?term=<?php echo e(urlencode($track->artist_name . ' ' . $track->title)); ?>" target="_blank" class="store-badge badge-apple">
                                                    <i class="fa-brands fa-apple"></i> Apple
                                                </a>
                                                <a href="https://music.youtube.com/search?q=<?php echo e(urlencode($track->artist_name . ' ' . $track->title)); ?>" target="_blank" class="store-badge badge-youtube">
                                                    <i class="fa-brands fa-youtube"></i> YouTube
                                                </a>
                                            </div>
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
    <div id="releases-panel" class="explore-tab-panel" style="display: none;">
        <?php if($releases->isEmpty()): ?>
            <div class="card" style="padding: 4rem 0; text-align: center; color: var(--text-muted);">
                <i class="fa-solid fa-compact-disc" style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-muted);"></i>
                <p>No matching releases found.</p>
            </div>
        <?php else: ?>
            <div class="explore-grid">
                <?php $__currentLoopData = $releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="explore-card">
                        <div class="explore-card-cover-wrapper">
                            <?php if($rel->cover_image): ?>
                                <img src="<?php echo e(asset('storage/' . $rel->cover_image)); ?>" alt="Cover" class="explore-card-cover">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: var(--bg-input);">
                                    <i class="fa-solid fa-record-vinyl" style="font-size: 4rem; color: var(--text-muted);"></i>
                                </div>
                            <?php endif; ?>
                            <span class="explore-card-badge"><?php echo e($rel->type); ?></span>
                        </div>
                        <div class="explore-card-body">
                            <h4 style="font-size: 1.1rem; margin-bottom: 0.25rem; font-family: var(--font-heading); font-weight: 700; color: var(--text-primary);"><?php echo e($rel->title); ?></h4>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1rem;">Artist: <strong><?php echo e($rel->artist->name); ?></strong></p>
                            
                            <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; justify-content: space-between; margin-bottom: 1.25rem; border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: auto;">
                                <span>Genre: <strong style="color: var(--text-secondary)"><?php echo e($rel->genre); ?></strong></span>
                                <span><?php echo e($rel->release_date ? $rel->release_date->format('Y-m-d') : 'Live'); ?></span>
                            </div>

                            <div style="margin-top: auto;">
                                <span style="font-size: 0.7rem; font-weight: bold; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 0.5rem;"><?php echo e(__('messages.stream_on')); ?>:</span>
                                <div class="store-badges">
                                    <a href="https://open.spotify.com/search/<?php echo e(urlencode($rel->artist->name . ' ' . $rel->title)); ?>" target="_blank" class="store-badge badge-spotify" style="padding: 0.25rem 0.6rem; font-size: 0.7rem;">
                                        <i class="fa-brands fa-spotify"></i> Spotify
                                    </a>
                                    <a href="https://music.apple.com/search?term=<?php echo e(urlencode($rel->artist->name . ' ' . $rel->title)); ?>" target="_blank" class="store-badge badge-apple" style="padding: 0.25rem 0.6rem; font-size: 0.7rem;">
                                        <i class="fa-brands fa-apple"></i> Apple
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tab Panel: Artists -->
    <div id="artists-panel" class="explore-tab-panel" style="display: none;">
        <?php if($artists->isEmpty()): ?>
            <div class="card" style="padding: 4rem 0; text-align: center; color: var(--text-muted);">
                <i class="fa-solid fa-user-astronaut" style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-muted);"></i>
                <p>No matching artists found.</p>
            </div>
        <?php else: ?>
            <div class="artist-grid">
                <?php $__currentLoopData = $artists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="artist-card">
                        <?php if($art->profile_picture): ?>
                            <img src="<?php echo e(asset('storage/' . $art->profile_picture)); ?>" alt="Avatar" class="artist-avatar">
                        <?php else: ?>
                            <div class="artist-avatar-placeholder">
                                <?php echo e(strtoupper(substr($art->name, 0, 2))); ?>

                            </div>
                        <?php endif; ?>
                        
                        <h4 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                            <?php echo e($art->name); ?>

                            <?php if($art->verification_status === 'verified'): ?>
                                <i class="fa-solid fa-circle-check" style="color: var(--primary); font-size: 0.9rem;" title="Verified Artist"></i>
                            <?php endif; ?>
                        </h4>
                        <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
                            <?php echo e($art->verification_status === 'verified' ? 'Verified Profile' : 'Platform Artist'); ?>

                        </p>
                        
                        <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; height: 3.8rem; margin: 0;">
                            <?php echo e($art->bio ?? 'No biography details provided yet.'); ?>

                        </p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Premium Floating Audio Player -->
<div id="premiumPlayer" class="floating-player">
    <div class="container player-content">
        <!-- Metadata -->
        <div class="player-metadata">
            <img id="playerCoverImg" src="" alt="Cover" class="player-cover" onerror="this.src='/storage/covers/placeholder.png'; this.onerror=null;">
            <div class="player-details">
                <div id="playerTrackTitle" class="player-title">Track Name</div>
                <div id="playerArtistName" class="player-artist">Artist Name</div>
            </div>
        </div>

        <!-- Controls -->
        <div class="player-controls">
            <div class="player-buttons">
                <button class="player-btn" onclick="seekBack()"><i class="fa-solid fa-backward-step"></i></button>
                <button id="playerPlayPauseBtn" class="player-btn player-btn-main" onclick="togglePlayback()"><i class="fa-solid fa-play"></i></button>
                <button class="player-btn" onclick="seekForward()"><i class="fa-solid fa-forward-step"></i></button>
            </div>
            <div class="player-progress-area">
                <span id="playerCurrentTime" class="player-time">00:00</span>
                <div class="player-progress-bar-wrapper" id="playerProgressBar" onclick="scrubPlayback(event)">
                    <div id="playerProgressBarFill" class="player-progress-bar-fill"></div>
                </div>
                <span id="playerTotalTime" class="player-time">00:00</span>
            </div>
        </div>

        <!-- Volume & Stores -->
        <div class="player-side-actions">
            <!-- Dynamic search links -->
            <div style="display: flex; gap: 0.4rem; align-items: center; border-right: 1px solid var(--border-color); padding-right: 1rem; margin-right: 0.5rem;">
                <span style="font-size: 0.65rem; text-transform: uppercase; color: var(--text-muted); font-weight: bold; margin-right: 0.25rem;">Links:</span>
                <a id="playerLinkSpotify" href="#" target="_blank" class="store-badge badge-spotify" style="padding: 0.25rem 0.5rem; font-size: 0.65rem;"><i class="fa-brands fa-spotify"></i></a>
                <a id="playerLinkApple" href="#" target="_blank" class="store-badge badge-apple" style="padding: 0.25rem 0.5rem; font-size: 0.65rem;"><i class="fa-brands fa-apple"></i></a>
            </div>
            
            <div class="player-volume-wrapper">
                <button class="player-btn" id="volumeMuteBtn" onclick="toggleMute()"><i class="fa-solid fa-volume-high"></i></button>
                <input type="range" id="volumeSlider" class="player-volume-slider" min="0" max="1" step="0.05" value="0.8" oninput="adjustVolume(event)">
            </div>
        </div>
    </div>
</div>

<audio id="globalAudioElement" style="display: none;"></audio>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function switchExploreTab(tabId, btn) {
        var panels = document.getElementsByClassName('explore-tab-panel');
        for (var i = 0; i < panels.length; i++) {
            panels[i].style.display = 'none';
        }
        document.getElementById(tabId).style.display = 'block';

        var buttons = document.getElementsByClassName('explore-tab-btn');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('active');
        }
        btn.classList.add('active');
    }

    // Audio Playback Engine
    const audio = document.getElementById('globalAudioElement');
    const player = document.getElementById('premiumPlayer');
    const playPauseBtn = document.getElementById('playerPlayPauseBtn');
    const coverImg = document.getElementById('playerCoverImg');
    const trackTitle = document.getElementById('playerTrackTitle');
    const artistName = document.getElementById('playerArtistName');
    const currentTimeText = document.getElementById('playerCurrentTime');
    const totalTimeText = document.getElementById('playerTotalTime');
    const progressBarFill = document.getElementById('playerProgressBarFill');
    const volumeSlider = document.getElementById('volumeSlider');
    const volumeMuteBtn = document.getElementById('volumeMuteBtn');

    // Dynamic Store Links in Player
    const linkSpotify = document.getElementById('playerLinkSpotify');
    const linkApple = document.getElementById('playerLinkApple');

    let currentTrackUrl = "";

    function formatTime(seconds) {
        if (isNaN(seconds)) return "00:00";
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    function playTrack(title, artist, cover, audioSrc) {
        // Show player
        player.classList.add('show');

        // Set metadata
        trackTitle.innerText = title;
        artistName.innerText = artist;
        
        if (cover) {
            coverImg.src = cover;
            coverImg.style.display = 'block';
        } else {
            // default placeholder
            coverImg.src = 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=120&auto=format&fit=crop&q=60';
        }

        // Set Dynamic search links
        const searchQuery = encodeURIComponent(`${artist} ${title}`);
        linkSpotify.href = `https://open.spotify.com/search/${searchQuery}`;
        linkApple.href = `https://music.apple.com/search?term=${searchQuery}`;

        // Handle Audio Element source change
        if (currentTrackUrl !== audioSrc) {
            audio.src = audioSrc;
            currentTrackUrl = audioSrc;
        }

        audio.play();
        playPauseBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
    }

    function togglePlayback() {
        if (audio.paused) {
            audio.play();
            playPauseBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
        } else {
            audio.pause();
            playPauseBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
        }
    }

    function seekBack() {
        audio.currentTime = Math.max(0, audio.currentTime - 10);
    }

    function seekForward() {
        audio.currentTime = Math.min(audio.duration || 0, audio.currentTime + 10);
    }

    function toggleMute() {
        audio.muted = !audio.muted;
        if (audio.muted) {
            volumeMuteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark" style="color: var(--danger)"></i>';
        } else {
            updateVolumeIcon(audio.volume);
        }
    }

    function adjustVolume(event) {
        const val = event.target.value;
        audio.volume = val;
        audio.muted = false;
        updateVolumeIcon(val);
    }

    function updateVolumeIcon(volume) {
        if (volume == 0) {
            volumeMuteBtn.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
        } else if (volume < 0.4) {
            volumeMuteBtn.innerHTML = '<i class="fa-solid fa-volume-low"></i>';
        } else {
            volumeMuteBtn.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
        }
    }

    function scrubPlayback(event) {
        const rect = document.getElementById('playerProgressBar').getBoundingClientRect();
        const clickX = event.clientX - rect.left;
        const width = rect.width;
        const percentage = clickX / width;
        audio.currentTime = percentage * audio.duration;
    }

    // Audio Event Listeners
    audio.addEventListener('timeupdate', () => {
        const cur = audio.currentTime;
        const dur = audio.duration || 0;
        currentTimeText.innerText = formatTime(cur);
        totalTimeText.innerText = formatTime(dur);
        
        const percentage = dur > 0 ? (cur / dur) * 100 : 0;
        progressBarFill.style.width = `${percentage}%`;
    });

    audio.addEventListener('loadedmetadata', () => {
        totalTimeText.innerText = formatTime(audio.duration);
    });

    audio.addEventListener('ended', () => {
        playPauseBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
        progressBarFill.style.width = '0%';
        currentTimeText.innerText = "00:00";
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\College-Music\resources\views/search/explore.blade.php ENDPATH**/ ?>