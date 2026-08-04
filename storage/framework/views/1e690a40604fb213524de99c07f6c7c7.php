<?php $__env->startSection('title', 'Distribute Music'); ?>
<?php $__env->startSection('header_title', 'Music Distribution Wizard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Distribute New Music</h1>
        <p class="page-subtitle">Submit your release to major streaming platforms and digital stores worldwide</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Progress Steps -->
        <div class="wizard-steps">
            <div class="wizard-step active" id="stepIndicator1">
                <span class="step-number">1</span>
                <span class="step-label">Release Details</span>
            </div>
            <div class="wizard-step" id="stepIndicator2">
                <span class="step-number">2</span>
                <span class="step-label">Artwork & Release</span>
            </div>
            <div class="wizard-step" id="stepIndicator3">
                <span class="step-number">3</span>
                <span class="step-label">Upload Tracks</span>
            </div>
            <div class="wizard-step" id="stepIndicator4">
                <span class="step-number">4</span>
                <span class="step-label">Select Platforms</span>
            </div>
        </div>

        <form id="distributionForm" action="<?php echo e(route('releases.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <!-- STEP 1: Release Details -->
            <div class="step-section" id="stepSection1">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><i class="fa-solid fa-compact-disc"></i> Step 1: Release Metadata</h3>
                
                <div class="grid-cols-2">
                    <div class="form-group">
                        <label class="form-label" for="artist_id">Select Artist Profile</label>
                        <select id="artist_id" name="artist_id" class="form-select" required>
                            <?php $__currentLoopData = $artists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($art->id); ?>"><?php echo e($art->name); ?> (<?php echo e($art->verification_status); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="title">Release Title</label>
                        <input type="text" id="title" name="title" class="form-input" placeholder="e.g. Midnight Memories" value="<?php echo e(old('title')); ?>" required>
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="grid-cols-3">
                    <div class="form-group">
                        <label class="form-label" for="type">Release Type</label>
                        <select id="type" name="type" class="form-select" onchange="adjustTrackLimits()" required>
                            <option value="single">Single (1 Track)</option>
                            <option value="ep">EP (2 - 6 Tracks)</option>
                            <option value="album">Album (6+ Tracks)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="genre">Primary Genre</label>
                        <select id="genre" name="genre" class="form-select" required>
                            <option value="Pop">Pop</option>
                            <option value="Hip-Hop/Rap">Hip-Hop/Rap</option>
                            <option value="Rock">Rock</option>
                            <option value="Electronic/Dance">Electronic/Dance</option>
                            <option value="R&B/Soul">R&B/Soul</option>
                            <option value="Afrobeats">Afrobeats</option>
                            <option value="Reggae">Reggae</option>
                            <option value="Jazz">Jazz</option>
                            <option value="Classical">Classical</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="language">Language of Lyrics</label>
                        <select id="language" name="language" class="form-select" required>
                            <option value="English">English</option>
                            <option value="French">French</option>
                            <option value="Spanish">Spanish</option>
                            <option value="Yoruba">Yoruba</option>
                            <option value="Igbo">Igbo</option>
                            <option value="Swahili">Swahili</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="copyright_info">Copyright Holder</label>
                    <input type="text" id="copyright_info" name="copyright_info" class="form-input" placeholder="e.g. ℗ 2026 Records Lab" value="<?php echo e(old('copyright_info')); ?>" required>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-primary" onclick="goToStep(2)">
                        Next Step <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: Cover Art & Scheduling -->
            <div class="step-section" id="stepSection2" style="display: none;">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><i class="fa-solid fa-image"></i> Step 2: Cover Artwork & Release Scheduling</h3>

                <div class="grid-cols-2">
                    <div>
                        <div class="form-group">
                            <label class="form-label">Upload Album Cover</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center;">
                                <div class="artwork-preview" id="artworkPreview">
                                    <i class="fa-regular fa-image" style="font-size: 3rem; color: var(--text-muted);"></i>
                                </div>
                                <div>
                                    <input type="file" id="cover_image" name="cover_image" class="form-input" accept="image/jpeg,image/png" onchange="previewArtwork(this)" required>
                                    <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.5rem;">
                                        Resolution: Perfect square (recommended 3000 x 3000 px). JPEG/PNG only (Max 3MB).
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="form-group">
                            <label class="form-label" for="scheduling_type">Distribution Schedule</label>
                            <select id="scheduling_type" name="scheduling_type" class="form-select" onchange="toggleScheduleDate()" required>
                                <option value="immediate">Release Immediately (Upon Approval)</option>
                                <option value="scheduled">Schedule Future Release Date</option>
                            </select>
                        </div>

                        <div class="form-group" id="releaseDateGroup" style="display: none;">
                            <label class="form-label" for="release_date">Scheduled Release Date</label>
                            <input type="date" id="release_date" name="release_date" class="form-input" min="<?php echo e(date('Y-m-d')); ?>">
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Platform ingestion takes roughly 3-5 business days.</small>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(1)">
                        <i class="fa-solid fa-arrow-left"></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" onclick="goToStep(3)">
                        Next Step <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: Upload Tracks -->
            <div class="step-section" id="stepSection3" style="display: none;">
                <h3 style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <span><i class="fa-solid fa-music"></i> Step 3: Tracklist & Audio Files</span>
                    <button type="button" class="btn btn-secondary btn-sm" id="addTrackBtn" onclick="addTrackRow()">
                        <i class="fa-solid fa-circle-plus"></i> Add Track
                    </button>
                </h3>
                
                <div id="tracklistContainer">
                    <!-- Track Input Row -->
                    <div class="card track-row" id="trackRow_0" style="margin-bottom: 1rem; border-color: rgba(255,255,255,0.05);">
                        <div class="card-body" style="padding: 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <h4 class="track-number-label" style="font-size: 0.9rem; color: var(--primary);">Track #1</h4>
                                <button type="button" class="btn btn-danger btn-sm delete-track-btn" style="display: none; padding: 0.25rem 0.5rem;" onclick="removeTrackRow(0)">
                                    <i class="fa-solid fa-trash"></i> Remove
                                </button>
                            </div>
                            
                            <div class="grid-cols-2">
                                <div class="form-group">
                                    <label class="form-label">Song Title</label>
                                    <input type="text" name="track_title[]" class="form-input" placeholder="e.g. Summer Vibes" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Audio File</label>
                                    <input type="file" name="track_file[]" class="form-input" accept="audio/mp3,audio/wav,audio/flac" required>
                                    <small style="color: var(--text-muted); font-size: 0.75rem;">Supported Formats: MP3, WAV, FLAC (Max 20MB)</small>
                                </div>
                            </div>

                            <div class="grid-cols-3" style="margin-bottom: 0;">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label">Composer</label>
                                    <input type="text" name="track_composer[]" class="form-input" placeholder="e.g. Ludwig van Beethoven" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label">Songwriter</label>
                                    <input type="text" name="track_songwriter[]" class="form-input" placeholder="e.g. Bob Dylan" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label">ISRC Code (Optional)</label>
                                    <input type="text" name="track_isrc[]" class="form-input" placeholder="Auto-generated if empty">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(2)">
                        <i class="fa-solid fa-arrow-left"></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" onclick="goToStep(4)">
                        Next Step <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 4: Store Platforms Selection -->
            <div class="step-section" id="stepSection4" style="display: none;">
                <h3 style="margin-bottom: 0.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><i class="fa-solid fa-shop"></i> Step 4: Digital Streaming Platforms Selection</h3>
                <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1.5rem;">Select which music download stores and digital services to distribute your catalogue to.</p>

                <div class="form-group" style="margin-bottom: 1.5rem; display: flex; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="selectAllStores(true)">Select All</button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="selectAllStores(false)" style="margin-left: 0.5rem;">Deselect All</button>
                </div>

                <div class="platform-grid">
                    <?php
                        $stores = [
                            ['name' => 'Spotify', 'icon' => 'fa-brands fa-spotify', 'color' => '#1ed760'],
                            ['name' => 'Apple Music', 'icon' => 'fa-brands fa-apple', 'color' => '#fc3c44'],
                            ['name' => 'Amazon Music', 'icon' => 'fa-brands fa-amazon', 'color' => '#00a8e1'],
                            ['name' => 'YouTube Music', 'icon' => 'fa-brands fa-youtube', 'color' => '#ff0000'],
                            ['name' => 'Deezer', 'icon' => 'fa-solid fa-compact-disc', 'color' => '#ff007f'],
                            ['name' => 'Tidal', 'icon' => 'fa-solid fa-gem', 'color' => '#00ffcc'],
                            ['name' => 'TikTok', 'icon' => 'fa-brands fa-tiktok', 'color' => '#ff0050'],
                            ['name' => 'Pandora', 'icon' => 'fa-solid fa-p', 'color' => '#0051ff'],
                        ];
                    ?>

                    <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="platform-card" onclick="toggleStoreCard(this)">
                            <input type="checkbox" name="stores[]" value="<?php echo e($st['name']); ?>" style="display:none;" class="store-checkbox">
                            <i class="<?php echo e($st['icon']); ?> platform-icon" style="transition: var(--transition-fast);"></i>
                            <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary);"><?php echo e($st['name']); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['stores'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="invalid-feedback" style="display:block; margin-top: 1rem;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <div style="margin-top: 2.5rem; padding: 1.5rem; background-color: var(--bg-input); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <h4 style="margin-bottom: 0.5rem;"><i class="fa-solid fa-circle-info" style="color: var(--primary); margin-right: 0.25rem;"></i> Submission Disclaimer</h4>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5;">
                        By submitting this release, you warrant that you own or control all global copyrights to the musical compositions and audio recordings. Once approved, the distribution fee (if any) is non-refundable. Rejections can be corrected and resubmitted without paying again.
                    </p>
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(3)">
                        <i class="fa-solid fa-arrow-left"></i> Previous
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-circle-check"></i> Submit Release
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    var currentStep = 1;
    var trackCount = 1;

    function goToStep(step) {
        // Simple form validation per step
        if (step > currentStep) {
            if (currentStep === 1) {
                var title = document.getElementById('title').value.trim();
                if (!title) {
                    alert('Please enter a release title.');
                    return;
                }
            } else if (currentStep === 2) {
                var cover = document.getElementById('cover_image').files.length;
                if (!cover) {
                    alert('Please upload a cover artwork image.');
                    return;
                }
            } else if (currentStep === 3) {
                // Ensure song files and details are entered
                var trackInputs = document.getElementsByName('track_title[]');
                for (var i = 0; i < trackInputs.length; i++) {
                    if (!trackInputs[i].value.trim()) {
                        alert('Please fill out all track titles.');
                        return;
                    }
                }
            }
        }

        // Hide current section
        document.getElementById('stepSection' + currentStep).style.display = 'none';
        document.getElementById('stepIndicator' + currentStep).classList.remove('active');
        if (step < currentStep) {
            document.getElementById('stepIndicator' + currentStep).classList.remove('completed');
        } else {
            document.getElementById('stepIndicator' + currentStep).classList.add('completed');
        }

        // Show next section
        currentStep = step;
        document.getElementById('stepSection' + currentStep).style.display = 'block';
        document.getElementById('stepIndicator' + currentStep).classList.add('active');
        document.getElementById('stepIndicator' + currentStep).classList.remove('completed');
    }

    function previewArtwork(input) {
        var preview = document.getElementById('artworkPreview');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="artwork-img">';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = '<i class="fa-regular fa-image" style="font-size: 3rem; color: var(--text-muted);"></i>';
        }
    }

    function toggleScheduleDate() {
        var scheduling = document.getElementById('scheduling_type').value;
        var dateGroup = document.getElementById('releaseDateGroup');
        var dateInput = document.getElementById('release_date');
        if (scheduling === 'scheduled') {
            dateGroup.style.display = 'block';
            dateInput.required = true;
        } else {
            dateGroup.style.display = 'none';
            dateInput.required = false;
        }
    }

    function toggleStoreCard(card) {
        var checkbox = card.querySelector('.store-checkbox');
        checkbox.checked = !checkbox.checked;
        card.classList.toggle('selected', checkbox.checked);
    }

    function selectAllStores(select) {
        var cards = document.querySelectorAll('.platform-card');
        cards.forEach(function(card) {
            var checkbox = card.querySelector('.store-checkbox');
            checkbox.checked = select;
            card.classList.toggle('selected', select);
        });
    }

    function adjustTrackLimits() {
        var type = document.getElementById('type').value;
        var addBtn = document.getElementById('addTrackBtn');
        
        if (type === 'single') {
            // Remove extra tracks, only keep the first one
            var rows = document.querySelectorAll('.track-row');
            for (var i = 1; i < rows.length; i++) {
                rows[i].remove();
            }
            addBtn.style.display = 'none';
            trackCount = 1;
            updateTrackLabels();
        } else {
            addBtn.style.display = 'inline-flex';
        }
    }

    function addTrackRow() {
        var type = document.getElementById('type').value;
        var rows = document.querySelectorAll('.track-row');
        
        if (type === 'ep' && rows.length >= 6) {
            alert('An EP cannot have more than 6 tracks. Please upgrade release type to Album.');
            return;
        }

        var container = document.getElementById('tracklistContainer');
        var newId = Date.now();
        var rowHtml = `
            <div class="card track-row" id="trackRow_${newId}" style="margin-bottom: 1rem; border-color: rgba(255,255,255,0.05); animation: fadeIn 0.3s ease;">
                <div class="card-body" style="padding: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 class="track-number-label" style="font-size: 0.9rem; color: var(--primary);">Track</h4>
                        <button type="button" class="btn btn-danger btn-sm delete-track-btn" style="padding: 0.25rem 0.5rem;" onclick="removeTrackRow('${newId}')">
                            <i class="fa-solid fa-trash"></i> Remove
                        </button>
                    </div>
                    
                    <div class="grid-cols-2">
                        <div class="form-group">
                            <label class="form-label">Song Title</label>
                            <input type="text" name="track_title[]" class="form-input" placeholder="e.g. Summer Vibes" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Audio File</label>
                            <input type="file" name="track_file[]" class="form-input" accept="audio/mp3,audio/wav,audio/flac" required>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Supported Formats: MP3, WAV, FLAC (Max 20MB)</small>
                        </div>
                    </div>

                    <div class="grid-cols-3" style="margin-bottom: 0;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Composer</label>
                            <input type="text" name="track_composer[]" class="form-input" placeholder="e.g. Ludwig van Beethoven" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Songwriter</label>
                            <input type="text" name="track_songwriter[]" class="form-input" placeholder="e.g. Bob Dylan" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">ISRC Code (Optional)</label>
                            <input type="text" name="track_isrc[]" class="form-input" placeholder="Auto-generated if empty">
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', rowHtml);
        updateTrackLabels();
    }

    function removeTrackRow(id) {
        var row = document.getElementById('trackRow_' + id);
        if (row) {
            row.remove();
            updateTrackLabels();
        }
    }

    function updateTrackLabels() {
        var labels = document.querySelectorAll('.track-number-label');
        labels.forEach(function(label, index) {
            label.textContent = 'Track #' + (index + 1);
        });

        // Toggle delete button visibility based on total rows
        var deleteBtns = document.querySelectorAll('.delete-track-btn');
        if (labels.length <= 1) {
            deleteBtns.forEach(btn => btn.style.display = 'none');
        } else {
            deleteBtns.forEach(btn => btn.style.display = 'block');
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\College-Music\resources\views/releases/create.blade.php ENDPATH**/ ?>