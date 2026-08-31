@extends('layouts.app')

@section('title', 'Distribute Music')
@section('header_title', 'Music Distribution Wizard')

@section('content')
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
            <div class="wizard-step active" id="stepIndicator1" onclick="handleStepClick(1)">
                <span class="step-number">1</span>
                <span class="step-label">Release Details</span>
            </div>
            <div class="wizard-step" id="stepIndicator2" onclick="handleStepClick(2)">
                <span class="step-number">2</span>
                <span class="step-label">Artwork & Release</span>
            </div>
            <div class="wizard-step" id="stepIndicator3" onclick="handleStepClick(3)">
                <span class="step-number">3</span>
                <span class="step-label">Upload Tracks</span>
            </div>
            <div class="wizard-step" id="stepIndicator4" onclick="handleStepClick(4)">
                <span class="step-number">4</span>
                <span class="step-label">Select Platforms</span>
            </div>
        </div>

        <!-- Validation Alert Banner -->
        <div id="wizardAlertBox" class="alert alert-danger animate-fade-up" style="display: none; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-circle-exclamation" style="font-size: 1.2rem;"></i>
            <span id="wizardAlertMessage" style="font-weight: 500;"></span>
        </div>

        <form id="distributionForm" action="{{ route('releases.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- STEP 1: Release Details -->
            <div class="step-section" id="stepSection1">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><i class="fa-solid fa-compact-disc"></i> Step 1: Release Metadata</h3>
                
                <div class="grid-cols-2">
                    <div class="form-group">
                        <label class="form-label" for="artist_id">Select Artist Profile</label>
                        <select id="artist_id" name="artist_id" class="form-select" required>
                            @foreach($artists as $art)
                                <option value="{{ $art->id }}">{{ $art->name }} ({{ $art->verification_status }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="title">Release Title</label>
                        <input type="text" id="title" name="title" class="form-input" placeholder="e.g. Midnight Memories" value="{{ old('title') }}" required>
                        @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
                        <label class="form-label" for="genre" id="genreLabel">Primary Genre</label>
                        <select id="genre" name="genre" class="form-select" onchange="syncTrackGenreDefaults()" required>
                            <option value="Pop">Pop</option>
                            <option value="Hip-Hop/Rap">Hip-Hop/Rap</option>
                            <option value="Rock">Rock</option>
                            <option value="Electronic/Dance">Electronic/Dance</option>
                            <option value="R&B/Soul">R&B/Soul</option>
                            <option value="Afrobeats">Afrobeats</option>
                            <option value="Reggae">Reggae</option>
                            <option value="Jazz">Jazz</option>
                            <option value="Classical">Classical</option>
                            <option value="Bongo Flava">Bongo Flava</option>
                            <option value="Gospel">Gospel</option>
                            <option value="Country">Country</option>
                            <option value="Latin">Latin</option>
                            <option value="Indie">Indie</option>
                        </select>
                        <small id="genreHint" style="color: var(--text-muted); font-size: 0.75rem; display: none; margin-top: 0.25rem;">This will be the default genre for all tracks. You can change each track's genre individually in Step 3.</small>
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
                    <input type="text" id="copyright_info" name="copyright_info" class="form-input" placeholder="e.g. ℗ 2026 Records Lab" value="{{ old('copyright_info') }}" required>
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
                            <input type="date" id="release_date" name="release_date" class="form-input" min="{{ date('Y-m-d') }}">
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

                            {{-- Per-track genre dropdown (visible only for EP/Album) --}}
                            <div class="track-genre-group" style="display: none; margin-bottom: 1rem;">
                                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fa-solid fa-tags" style="color: var(--primary);"></i> Track Genre / Category
                                </label>
                                <select name="track_genre[]" class="form-select track-genre-select">
                                    <option value="Pop">Pop</option>
                                    <option value="Hip-Hop/Rap">Hip-Hop/Rap</option>
                                    <option value="Rock">Rock</option>
                                    <option value="Electronic/Dance">Electronic/Dance</option>
                                    <option value="R&B/Soul">R&B/Soul</option>
                                    <option value="Afrobeats">Afrobeats</option>
                                    <option value="Reggae">Reggae</option>
                                    <option value="Jazz">Jazz</option>
                                    <option value="Classical">Classical</option>
                                    <option value="Bongo Flava">Bongo Flava</option>
                                    <option value="Gospel">Gospel</option>
                                    <option value="Country">Country</option>
                                    <option value="Latin">Latin</option>
                                    <option value="Indie">Indie</option>
                                </select>
                                <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Choose the genre for this specific track (can differ from the release genre)</small>
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
                    @php
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
                    @endphp

                    @foreach($stores as $st)
                        <div class="platform-card" onclick="toggleStoreCard(this)">
                            <input type="checkbox" name="stores[]" value="{{ $st['name'] }}" style="display:none;" class="store-checkbox">
                            <i class="{{ $st['icon'] }} platform-icon" style="transition: var(--transition-fast);"></i>
                            <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary);">{{ $st['name'] }}</div>
                        </div>
                    @endforeach
                </div>
                @error('stores')
                    <span class="invalid-feedback" style="display:block; margin-top: 1rem;">{{ $message }}</span>
                @enderror

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
@endsection

@section('scripts')
<script>
    var currentStep = 1;
    var trackCount = 1;

    // Remove validation errors when user types or changes an input
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('distributionForm');
        if (form) {
            form.addEventListener('input', function(e) {
                if (e.target.classList.contains('is-invalid')) {
                    e.target.classList.remove('is-invalid');
                }
                var alertBox = document.getElementById('wizardAlertBox');
                if (alertBox && alertBox.style.display !== 'none') {
                    // Check if any invalid fields remain
                    if (document.querySelectorAll('.is-invalid').length === 0) {
                        alertBox.style.display = 'none';
                    }
                }
            });

            form.addEventListener('change', function(e) {
                if (e.target.classList.contains('is-invalid')) {
                    e.target.classList.remove('is-invalid');
                }
            });

            // Prevent form submit if any step has incomplete/invalid information
            form.addEventListener('submit', function(e) {
                for (var s = 1; s <= 4; s++) {
                    if (!validateStep(s)) {
                        e.preventDefault();
                        if (currentStep !== s) {
                            switchStepDisplay(s);
                        }
                        return false;
                    }
                }
            });
        }
    });

    function showStepError(inputElement, message) {
        clearValidationErrors();
        if (inputElement) {
            inputElement.classList.add('is-invalid');
            inputElement.focus();
            inputElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        var alertBox = document.getElementById('wizardAlertBox');
        var alertMsg = document.getElementById('wizardAlertMessage');
        if (alertBox && alertMsg) {
            alertMsg.textContent = message;
            alertBox.style.display = 'flex';
        }
    }

    function showStepNotification(message) {
        clearValidationErrors();
        var alertBox = document.getElementById('wizardAlertBox');
        var alertMsg = document.getElementById('wizardAlertMessage');
        if (alertBox && alertMsg) {
            alertMsg.textContent = message;
            alertBox.style.display = 'flex';
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(function(el) {
            el.classList.remove('is-invalid');
        });
        var alertBox = document.getElementById('wizardAlertBox');
        if (alertBox) {
            alertBox.style.display = 'none';
        }
    }

    function validateStep(stepNumber) {
        clearValidationErrors();

        // Step 1: Release Details
        if (stepNumber === 1) {
            var artistId = document.getElementById('artist_id');
            if (!artistId || !artistId.value) {
                showStepError(artistId, 'Step 1: Please select an artist profile.');
                return false;
            }

            var title = document.getElementById('title');
            if (!title || !title.value.trim()) {
                showStepError(title, 'Step 1: Please enter the release title.');
                return false;
            }

            var type = document.getElementById('type');
            if (!type || !type.value) {
                showStepError(type, 'Step 1: Please select a release type.');
                return false;
            }

            var genre = document.getElementById('genre');
            if (!genre || !genre.value) {
                showStepError(genre, 'Step 1: Please select a primary genre.');
                return false;
            }

            var language = document.getElementById('language');
            if (!language || !language.value) {
                showStepError(language, 'Step 1: Please select the lyrics language.');
                return false;
            }

            var copyright = document.getElementById('copyright_info');
            if (!copyright || !copyright.value.trim()) {
                showStepError(copyright, 'Step 1: Please enter the copyright holder details.');
                return false;
            }

            return true;
        }

        // Step 2: Cover Art & Scheduling
        if (stepNumber === 2) {
            var coverInput = document.getElementById('cover_image');
            if (!coverInput || !coverInput.files || coverInput.files.length === 0) {
                showStepError(coverInput, 'Step 2: Please upload a cover artwork image.');
                return false;
            }

            var coverFile = coverInput.files[0];
            var validCoverTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validCoverTypes.includes(coverFile.type) && !/\.(jpe?g|png)$/i.test(coverFile.name)) {
                showStepError(coverInput, 'Step 2: Cover artwork must be in JPEG or PNG format.');
                return false;
            }

            if (coverFile.size > 3 * 1024 * 1024) {
                showStepError(coverInput, 'Step 2: Cover artwork file size exceeds the 3MB maximum limit.');
                return false;
            }

            var schedulingType = document.getElementById('scheduling_type').value;
            if (schedulingType === 'scheduled') {
                var releaseDate = document.getElementById('release_date');
                if (!releaseDate || !releaseDate.value) {
                    showStepError(releaseDate, 'Step 2: Please select a scheduled future release date.');
                    return false;
                }
                var today = new Date().toISOString().split('T')[0];
                if (releaseDate.value < today) {
                    showStepError(releaseDate, 'Step 2: Scheduled release date cannot be in the past.');
                    return false;
                }
            }

            return true;
        }

        // Step 3: Tracks & Audio Files
        if (stepNumber === 3) {
            var releaseType = document.getElementById('type').value;
            var trackRows = document.querySelectorAll('.track-row');

            if (trackRows.length === 0) {
                showStepNotification('Step 3: Please add at least one audio track to the release.');
                return false;
            }

            if (releaseType === 'single' && trackRows.length !== 1) {
                showStepNotification('Step 3: A Single release must contain exactly 1 track.');
                return false;
            }

            if (releaseType === 'ep' && (trackRows.length < 2 || trackRows.length > 6)) {
                showStepNotification('Step 3: An EP must contain between 2 and 6 tracks. (Currently: ' + trackRows.length + ')');
                return false;
            }

            if (releaseType === 'album' && trackRows.length < 6) {
                showStepNotification('Step 3: An Album must contain at least 6 tracks. (Currently: ' + trackRows.length + ')');
                return false;
            }

            for (var i = 0; i < trackRows.length; i++) {
                var row = trackRows[i];
                var trackNum = i + 1;

                var titleInput = row.querySelector('input[name="track_title[]"]');
                if (!titleInput || !titleInput.value.trim()) {
                    showStepError(titleInput, 'Step 3: Please enter the song title for Track #' + trackNum + '.');
                    return false;
                }

                var audioInput = row.querySelector('input[name="track_file[]"]');
                if (!audioInput || !audioInput.files || audioInput.files.length === 0) {
                    showStepError(audioInput, 'Step 3: Please upload an audio file for Track #' + trackNum + ' before proceeding.');
                    return false;
                }

                var audioFile = audioInput.files[0];
                var validAudioExt = /\.(mp3|wav|flac)$/i.test(audioFile.name);
                if (!validAudioExt) {
                    showStepError(audioInput, 'Step 3: Track #' + trackNum + ' audio must be MP3, WAV, or FLAC.');
                    return false;
                }

                if (audioFile.size > 20 * 1024 * 1024) {
                    showStepError(audioInput, 'Step 3: Track #' + trackNum + ' audio file exceeds the 20MB limit.');
                    return false;
                }

                var composerInput = row.querySelector('input[name="track_composer[]"]');
                if (!composerInput || !composerInput.value.trim()) {
                    showStepError(composerInput, 'Step 3: Please enter the composer name for Track #' + trackNum + '.');
                    return false;
                }

                var songwriterInput = row.querySelector('input[name="track_songwriter[]"]');
                if (!songwriterInput || !songwriterInput.value.trim()) {
                    showStepError(songwriterInput, 'Step 3: Please enter the songwriter name for Track #' + trackNum + '.');
                    return false;
                }
            }

            return true;
        }

        // Step 4: Digital Stores
        if (stepNumber === 4) {
            var selectedStores = document.querySelectorAll('.store-checkbox:checked');
            if (selectedStores.length === 0) {
                showStepNotification('Step 4: Please select at least one digital platform or store to distribute your music.');
                return false;
            }
            return true;
        }

        return true;
    }

    function handleStepClick(targetStep) {
        goToStep(targetStep);
    }

    function goToStep(targetStep) {
        if (targetStep === currentStep) return;

        // If moving forward, validate all preceding steps sequentially
        if (targetStep > currentStep) {
            for (var s = currentStep; s < targetStep; s++) {
                if (!validateStep(s)) {
                    if (currentStep !== s) {
                        switchStepDisplay(s);
                    }
                    return;
                }
            }
        }

        // If validation passed or moving backward, switch display
        clearValidationErrors();
        switchStepDisplay(targetStep);
    }

    function switchStepDisplay(targetStep) {
        document.getElementById('stepSection' + currentStep).style.display = 'none';

        currentStep = targetStep;
        document.getElementById('stepSection' + currentStep).style.display = 'block';

        // Update step indicators
        for (var i = 1; i <= 4; i++) {
            var indicator = document.getElementById('stepIndicator' + i);
            if (indicator) {
                if (i < currentStep) {
                    indicator.classList.remove('active');
                    indicator.classList.add('completed');
                } else if (i === currentStep) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                } else {
                    indicator.classList.remove('active', 'completed');
                }
            }
        }

        window.scrollTo({ top: document.querySelector('.card').offsetTop - 30, behavior: 'smooth' });
    }

    function previewArtwork(input) {
        var preview = document.getElementById('artworkPreview');
        if (input.files && input.files[0]) {
            var file = input.files[0];
            if (file.size > 3 * 1024 * 1024) {
                showStepError(input, 'Cover artwork file exceeds 3MB. Please choose a smaller file.');
                input.value = '';
                preview.innerHTML = '<i class="fa-regular fa-image" style="font-size: 3rem; color: var(--text-muted);"></i>';
                return;
            }
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="artwork-img" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-md);">';
            }
            reader.readAsDataURL(file);
            input.classList.remove('is-invalid');
            var alertBox = document.getElementById('wizardAlertBox');
            if (alertBox) alertBox.style.display = 'none';
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
            dateInput.classList.remove('is-invalid');
        }
    }

    function toggleStoreCard(card) {
        var checkbox = card.querySelector('.store-checkbox');
        checkbox.checked = !checkbox.checked;
        card.classList.toggle('selected', checkbox.checked);
        var alertBox = document.getElementById('wizardAlertBox');
        if (alertBox && document.querySelectorAll('.store-checkbox:checked').length > 0) {
            alertBox.style.display = 'none';
        }
    }

    function selectAllStores(select) {
        var cards = document.querySelectorAll('.platform-card');
        cards.forEach(function(card) {
            var checkbox = card.querySelector('.store-checkbox');
            checkbox.checked = select;
            card.classList.toggle('selected', select);
        });
        var alertBox = document.getElementById('wizardAlertBox');
        if (alertBox && select) {
            alertBox.style.display = 'none';
        }
    }

    function adjustTrackLimits() {
        var type = document.getElementById('type').value;
        var addBtn = document.getElementById('addTrackBtn');
        var genreLabel = document.getElementById('genreLabel');
        var genreHint = document.getElementById('genreHint');
        
        if (type === 'single') {
            var rows = document.querySelectorAll('.track-row');
            for (var i = 1; i < rows.length; i++) {
                rows[i].remove();
            }
            addBtn.style.display = 'none';
            updateTrackLabels();
            // Hide per-track genre for singles
            document.querySelectorAll('.track-genre-group').forEach(function(g) { g.style.display = 'none'; });
            genreLabel.textContent = 'Primary Genre';
            genreHint.style.display = 'none';
        } else {
            addBtn.style.display = 'inline-flex';
            // Show per-track genre for EP/Album
            document.querySelectorAll('.track-genre-group').forEach(function(g) { g.style.display = 'block'; });
            genreLabel.textContent = 'Primary Genre (Default for Tracks)';
            genreHint.style.display = 'block';
            // Sync defaults
            syncTrackGenreDefaults();
        }
    }

    function addTrackRow() {
        var type = document.getElementById('type').value;
        var rows = document.querySelectorAll('.track-row');
        
        if (type === 'ep' && rows.length >= 6) {
            showStepNotification('An EP cannot have more than 6 tracks. To add more tracks, change Release Type to Album in Step 1.');
            return;
        }

        var container = document.getElementById('tracklistContainer');
        var newId = Date.now();
        var releaseGenre = document.getElementById('genre').value;
        var showGenre = (type !== 'single') ? 'block' : 'none';
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
                            <input type="file" name="track_file[]" class="form-input track-audio-input" accept="audio/mp3,audio/wav,audio/flac" required>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Supported Formats: MP3, WAV, FLAC (Max 20MB)</small>
                        </div>
                    </div>

                    <div class="track-genre-group" style="display: ${showGenre}; margin-bottom: 1rem;">
                        <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-tags" style="color: var(--primary);"></i> Track Genre / Category
                        </label>
                        <select name="track_genre[]" class="form-select track-genre-select">
                            <option value="Pop" ${releaseGenre === 'Pop' ? 'selected' : ''}>Pop</option>
                            <option value="Hip-Hop/Rap" ${releaseGenre === 'Hip-Hop/Rap' ? 'selected' : ''}>Hip-Hop/Rap</option>
                            <option value="Rock" ${releaseGenre === 'Rock' ? 'selected' : ''}>Rock</option>
                            <option value="Electronic/Dance" ${releaseGenre === 'Electronic/Dance' ? 'selected' : ''}>Electronic/Dance</option>
                            <option value="R&B/Soul" ${releaseGenre === 'R&B/Soul' ? 'selected' : ''}>R&B/Soul</option>
                            <option value="Afrobeats" ${releaseGenre === 'Afrobeats' ? 'selected' : ''}>Afrobeats</option>
                            <option value="Reggae" ${releaseGenre === 'Reggae' ? 'selected' : ''}>Reggae</option>
                            <option value="Jazz" ${releaseGenre === 'Jazz' ? 'selected' : ''}>Jazz</option>
                            <option value="Classical" ${releaseGenre === 'Classical' ? 'selected' : ''}>Classical</option>
                            <option value="Bongo Flava" ${releaseGenre === 'Bongo Flava' ? 'selected' : ''}>Bongo Flava</option>
                            <option value="Gospel" ${releaseGenre === 'Gospel' ? 'selected' : ''}>Gospel</option>
                            <option value="Country" ${releaseGenre === 'Country' ? 'selected' : ''}>Country</option>
                            <option value="Latin" ${releaseGenre === 'Latin' ? 'selected' : ''}>Latin</option>
                            <option value="Indie" ${releaseGenre === 'Indie' ? 'selected' : ''}>Indie</option>
                        </select>
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Choose the genre for this specific track (can differ from the release genre)</small>
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

        var deleteBtns = document.querySelectorAll('.delete-track-btn');
        if (labels.length <= 1) {
            deleteBtns.forEach(function(btn) { btn.style.display = 'none'; });
        } else {
            deleteBtns.forEach(function(btn) { btn.style.display = 'block'; });
        }
    }

    // Sync all track genre dropdowns that haven't been manually changed to the release genre default
    function syncTrackGenreDefaults() {
        var releaseGenre = document.getElementById('genre').value;
        var type = document.getElementById('type').value;
        if (type === 'single') return;
        
        document.querySelectorAll('.track-genre-select').forEach(function(sel) {
            // Only auto-sync if user hasn't manually changed it (first load)
            if (!sel.dataset.userChanged) {
                sel.value = releaseGenre;
            }
        });
    }

    // Mark track genre dropdowns as manually changed when user interacts
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('track-genre-select')) {
            e.target.dataset.userChanged = 'true';
        }
    });
</script>
@endsection
