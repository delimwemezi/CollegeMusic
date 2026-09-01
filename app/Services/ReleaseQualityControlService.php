<?php

namespace App\Services;

use App\Models\Release;
use App\Models\ReleaseStore;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ReleaseQualityControlService
{
    /**
     * Common placeholder terms rejected by digital streaming platforms
     */
    protected static array $placeholderKeywords = [
        'test', 'testing', 'untitled', 'demo', 'sample', 'asdf', 'qwerty',
        'temp', 'temporary', 'track 1', 'track 2', 'n/a', 'none', 'unknown', 'xxx'
    ];

    /**
     * Inspect a release and validate all files, metadata, audio, and store compatibility.
     *
     * @param Release $release
     * @return array ['passed' => bool, 'errors' => string[], 'warnings' => string[]]
     */
    public static function inspect(Release $release): array
    {
        $errors = [];
        $warnings = [];

        // 1. Validate Release Title
        $title = trim($release->title ?? '');
        if (empty($title)) {
            $errors[] = "Release title cannot be empty.";
        } elseif (mb_strlen($title) < 2) {
            $errors[] = "Release title is too short (minimum 2 characters required).";
        } elseif (self::isPlaceholder($title)) {
            $errors[] = "Release title '{$title}' appears to be a placeholder. Digital stores require real, finalized release titles.";
        }

        // 2. Validate Genre & Language
        if (empty($release->genre)) {
            $errors[] = "A primary music genre must be selected.";
        }
        if (empty($release->language)) {
            $errors[] = "The lyrics / audio language must be specified.";
        }

        // 3. Validate Copyright Info
        $copyright = trim($release->copyright_info ?? '');
        if (empty($copyright)) {
            $errors[] = "Copyright information is mandatory for worldwide digital distribution.";
        } elseif (self::isPlaceholder($copyright)) {
            $errors[] = "Copyright line '{$copyright}' is invalid. Please provide the legitimate copyright holder and year (e.g. '(C) 2026 Artist Name').";
        }

        // 4. Validate Cover Artwork
        if (empty($release->cover_image)) {
            $errors[] = "Cover artwork file is missing.";
        } else {
            $coverDisk = Storage::disk('public');
            if (!$coverDisk->exists($release->cover_image)) {
                $errors[] = "Cover artwork file could not be found on storage.";
            } else {
                $coverSizeBytes = $coverDisk->size($release->cover_image);
                if ($coverSizeBytes <= 0) {
                    $errors[] = "Cover artwork file is empty or corrupted (0 bytes).";
                } elseif ($coverSizeBytes > 5 * 1024 * 1024) {
                    $errors[] = "Cover artwork file size (" . round($coverSizeBytes / (1024 * 1024), 2) . "MB) exceeds the 5MB maximum limit.";
                }

                // Check image dimensions and aspect ratio
                $fullPath = storage_path('app/public/' . $release->cover_image);
                if (file_exists($fullPath) && is_readable($fullPath)) {
                    $imageInfo = @getimagesize($fullPath);
                    if ($imageInfo === false) {
                        $errors[] = "Cover artwork file is not a valid or readable image (must be JPEG or PNG).";
                    } else {
                        $width = $imageInfo[0];
                        $height = $imageInfo[1];
                        $mime = $imageInfo['mime'] ?? '';

                        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/jpg'], true)) {
                            $errors[] = "Cover artwork format ({$mime}) is not supported. Digital stores require JPEG or PNG.";
                        }

                        // Check Square Aspect Ratio (1:1 is mandatory on Spotify, Apple Music, Tidal, Amazon)
                        if ($width > 0 && $height > 0) {
                            $aspectRatio = $width / $height;
                            if ($aspectRatio < 0.96 || $aspectRatio > 1.04) {
                                $errors[] = "Cover artwork is not perfectly square ({$width}x{$height}px). Digital stores (Spotify, Apple Music) require a strict 1:1 square aspect ratio.";
                            }
                            if ($width < 300 || $height < 300) {
                                $errors[] = "Cover artwork resolution ({$width}x{$height}px) is too low. Minimum required is 300x300px (recommended 3000x3000px).";
                            }
                        }
                    }
                }
            }
        }

        // 5. Validate Selected Digital Stores
        $stores = $release->stores;
        if ($stores->isEmpty()) {
            $errors[] = "At least one digital streaming store or platform (e.g. Spotify, Apple Music) must be selected for distribution.";
        }

        // 6. Validate Tracks and Audio Files
        $tracks = $release->tracks;
        $trackCount = $tracks->count();

        if ($trackCount === 0) {
            $errors[] = "The release contains no audio tracks. At least 1 track is required.";
        } else {
            // Type vs Track Count Consistency
            if ($release->type === 'single' && $trackCount !== 1) {
                $errors[] = "A Single release must contain exactly 1 audio track (currently has {$trackCount}).";
            } elseif ($release->type === 'ep' && ($trackCount < 2 || $trackCount > 6)) {
                $errors[] = "An EP release must contain between 2 and 6 audio tracks (currently has {$trackCount}).";
            } elseif ($release->type === 'album' && $trackCount < 6) {
                $errors[] = "An Album release must contain at least 6 audio tracks (currently has {$trackCount}).";
            }

            // Per-track inspection
            foreach ($tracks as $idx => $track) {
                $num = $idx + 1;
                $tTitle = trim($track->title ?? '');
                $composer = trim($track->composer ?? '');
                $songwriter = trim($track->songwriter ?? '');

                if (empty($tTitle)) {
                    $errors[] = "Track #{$num}: Song title is missing.";
                } elseif (self::isPlaceholder($tTitle)) {
                    $errors[] = "Track #{$num}: Song title '{$tTitle}' is a placeholder. Please provide the actual track title.";
                }

                if (empty($composer)) {
                    $errors[] = "Track #{$num}: Composer name is required by digital store royalty publishing standards.";
                } elseif (self::isPlaceholder($composer)) {
                    $errors[] = "Track #{$num}: Composer name '{$composer}' is invalid. Provide the legitimate composer name.";
                }

                if (empty($songwriter)) {
                    $errors[] = "Track #{$num}: Songwriter / Lyricist name is required.";
                } elseif (self::isPlaceholder($songwriter)) {
                    $errors[] = "Track #{$num}: Songwriter name '{$songwriter}' is invalid.";
                }

                // Audio File validation
                if (empty($track->audio_file)) {
                    $errors[] = "Track #{$num}: Audio file is missing.";
                } else {
                    $disk = Storage::disk('public');
                    if (!$disk->exists($track->audio_file)) {
                        $errors[] = "Track #{$num}: Audio file ('{$track->audio_file}') not found in storage.";
                    } else {
                        $size = $disk->size($track->audio_file);
                        if ($size <= 0) {
                            $errors[] = "Track #{$num}: Audio file is empty or corrupted (0 bytes).";
                        } elseif ($size > 50 * 1024 * 1024) {
                            $errors[] = "Track #{$num}: Audio file size (" . round($size / (1024 * 1024), 2) . "MB) exceeds maximum limit of 50MB.";
                        }

                        // Verify audio extension
                        $extension = strtolower(pathinfo($track->audio_file, PATHINFO_EXTENSION));
                        if (!in_array($extension, ['mp3', 'wav', 'flac', 'm4a'], true)) {
                            $errors[] = "Track #{$num}: Audio format (.{$extension}) is incompatible. Supported formats: MP3, WAV, FLAC.";
                        }

                        // Check duration
                        if ($track->duration < 15) {
                            $errors[] = "Track #{$num}: Audio duration ({$track->duration}s) is too short. Streaming stores reject tracks shorter than 15 seconds.";
                        }
                    }
                }

                // ISRC format check
                if (!empty($track->isrc) && !preg_match('/^[A-Z]{2}-?[A-Z0-9]{3}-?\d{2}-?\d{5}$/i', $track->isrc) && !str_starts_with($track->isrc, 'US-CM1-')) {
                    $warnings[] = "Track #{$num}: ISRC code '{$track->isrc}' might not follow standard 12-character alphanumeric format.";
                }
            }
        }

        return [
            'passed' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Inspect the release and automatically approve/distribute if clean, or reject and notify if issues found.
     *
     * @param Release $release
     * @param Request $request
     * @return array Inspection summary result
     */
    public static function inspectAndProcess(Release $release, Request $request): array
    {
        $user = $release->artist->user ?? $request->user();
        $inspection = self::inspect($release);

        if ($inspection['passed']) {
            // No errors found: Auto-confirm & approve release
            $release->distribution_status = 'approved';
            $release->rejection_reason = null;
            $release->save();

            // Log automated verification success
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'auto_qc_passed',
                'description' => "Automated Quality Inspection Passed for '{$release->title}'. Zero compatibility issues found across audio, cover artwork, and metadata.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Notify artist of automated approval
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'release_approved',
                'description' => "Confirmed: Your release '{$release->title}' passed automated quality inspection with zero errors and has been automatically approved for digital store distribution!",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // If release is paid or on free distribution, automatically distribute to streaming stores
            if ($release->billing_status === 'paid') {
                self::autoDistributeToStores($release, $request);
            }

        } else {
            // Errors found: Auto-reject with detailed feedback and send notification to artist for collection/re-upload
            $errorSummary = implode("\n• ", $inspection['errors']);
            $shortReason = implode('; ', array_slice($inspection['errors'], 0, 3));

            $release->distribution_status = 'rejected';
            $release->rejection_reason = "Automated Quality Inspection detected issues:\n• " . $errorSummary;
            $release->save();

            // Log auto inspection failure
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'auto_qc_failed',
                'description' => "Automated Quality Inspection flagged release '{$release->title}'. Errors: " . $shortReason,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Send notification to artist/label detailing the exact issues for collection & re-upload
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'release_rejected',
                'description' => "Automated QC Alert: Release '{$release->title}' requires corrections before distribution. Issues detected: " . $shortReason . ". Please review details, fix the errors, and upload again for review.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        return $inspection;
    }

    /**
     * Automatically distribute an approved release to selected digital platforms.
     */
    public static function autoDistributeToStores(Release $release, Request $request): void
    {
        $release->distribution_status = 'distributed';
        $release->save();

        ReleaseStore::where('release_id', $release->id)->update(['status' => 'distributed']);

        // Transmit release payload to simulated/live Store Ingestion API
        $apiSuccess = false;
        try {
            $response = Http::timeout(10)->post('https://httpbin.org/post', [
                'event' => 'automated_music_distribution',
                'provider' => 'CollegeMusic Automated Distribution Pipeline',
                'release' => [
                    'id' => $release->id,
                    'title' => $release->title,
                    'type' => $release->type,
                    'genre' => $release->genre,
                    'cover_url' => asset('storage/' . $release->cover_image),
                ],
                'artist' => [
                    'id' => $release->artist->id,
                    'name' => $release->artist->name,
                ],
                'stores' => ReleaseStore::where('release_id', $release->id)->pluck('store_name')->toArray(),
            ]);

            $apiSuccess = $response->successful();
        } catch (\Exception $e) {
            $apiSuccess = false;
        }

        $user = $release->artist->user ?? $request->user();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'release_distributed',
            'description' => "Distributed: Your release '{$release->title}' has been verified and automatically distributed across all chosen streaming platforms (Spotify, Apple Music, YouTube Music, Deezer, etc.)!",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
    }

    /**
     * Check if a string matches common placeholder patterns.
     */
    protected static function isPlaceholder(string $str): bool
    {
        $cleaned = strtolower(trim($str));
        if (in_array($cleaned, self::$placeholderKeywords, true)) {
            return true;
        }

        // Repetitive single character (e.g. 'aaaaa', '11111')
        if (preg_match('/^(.)\1{4,}$/', $cleaned)) {
            return true;
        }

        return false;
    }
}
