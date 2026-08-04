@extends('layouts.app')

@section('title', 'Edit Release')
@section('header_title', 'Edit Release')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Release: {{ $release->title }}</h1>
        <p class="page-subtitle">Update your release information and resubmit for administrator review</p>
    </div>
    <div>
        <a href="{{ route('releases.show', $release->id) }}" class="btn btn-secondary">
            <i class="fa-solid fa-xmark"></i> Cancel Edit
        </a>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-pen-to-square"></i> Release Details</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('releases.update', $release->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="title">Release Title</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title', $release->title) }}" required>
                @error('title')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid-cols-2">
                <div class="form-group">
                    <label class="form-label" for="genre">Primary Genre</label>
                    <select id="genre" name="genre" class="form-select" required>
                        <option value="Pop" {{ $release->genre == 'Pop' ? 'selected' : '' }}>Pop</option>
                        <option value="Hip-Hop/Rap" {{ $release->genre == 'Hip-Hop/Rap' ? 'selected' : '' }}>Hip-Hop/Rap</option>
                        <option value="Rock" {{ $release->genre == 'Rock' ? 'selected' : '' }}>Rock</option>
                        <option value="Electronic/Dance" {{ $release->genre == 'Electronic/Dance' ? 'selected' : '' }}>Electronic/Dance</option>
                        <option value="R&B/Soul" {{ $release->genre == 'R&B/Soul' ? 'selected' : '' }}>R&B/Soul</option>
                        <option value="Afrobeats" {{ $release->genre == 'Afrobeats' ? 'selected' : '' }}>Afrobeats</option>
                        <option value="Reggae" {{ $release->genre == 'Reggae' ? 'selected' : '' }}>Reggae</option>
                        <option value="Jazz" {{ $release->genre == 'Jazz' ? 'selected' : '' }}>Jazz</option>
                        <option value="Classical" {{ $release->genre == 'Classical' ? 'selected' : '' }}>Classical</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="language">Language</label>
                    <select id="language" name="language" class="form-select" required>
                        <option value="English" {{ $release->language == 'English' ? 'selected' : '' }}>English</option>
                        <option value="French" {{ $release->language == 'French' ? 'selected' : '' }}>French</option>
                        <option value="Spanish" {{ $release->language == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                        <option value="Yoruba" {{ $release->language == 'Yoruba' ? 'selected' : '' }}>Yoruba</option>
                        <option value="Igbo" {{ $release->language == 'Igbo' ? 'selected' : '' }}>Igbo</option>
                        <option value="Swahili" {{ $release->language == 'Swahili' ? 'selected' : '' }}>Swahili</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="copyright_info">Copyright Information</label>
                <input type="text" id="copyright_info" name="copyright_info" class="form-input" value="{{ old('copyright_info', $release->copyright_info) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Album Cover Artwork</label>
                <div style="display: flex; gap: 1.5rem; align-items: center;">
                    <div class="artwork-preview" id="artworkPreview" style="width: 120px; height: 120px;">
                        @if($release->cover_image)
                            <img src="{{ asset('storage/' . $release->cover_image) }}" class="artwork-img">
                        @else
                            <i class="fa-regular fa-image" style="font-size: 2.5rem; color: var(--text-muted);"></i>
                        @endif
                    </div>
                    <div>
                        <input type="file" id="cover_image" name="cover_image" class="form-input" accept="image/jpeg,image/png" onchange="previewArtwork(this)">
                        <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.5rem;">
                            Keep empty to retain existing artwork. Square format JPEG/PNG (Max 3MB).
                        </small>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                <i class="fa-solid fa-circle-check"></i> Resubmit Release for Review
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewArtwork(input) {
        var preview = document.getElementById('artworkPreview');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="artwork-img">';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
