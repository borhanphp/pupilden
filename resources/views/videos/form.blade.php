@extends('layouts.app')

@section('title', isset($video) ? 'Edit Video' : 'Add New Video')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($video) ? 'Edit Video' : 'Add New Video' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($video) ? route('videos.update', $video) : route('videos.store') }}" method="POST">
                        @csrf
                        @if(isset($video))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Course</label>
                                    <select name="course_id" 
                                            id="course_id" 
                                            class="form-select @error('course_id') is-invalid @enderror"
                                            {{ isset($courseId) ? 'readonly' : '' }}>
                                        <option value="">Select a course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                    {{ old('course_id', $video->course_id ?? $courseId ?? '') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Video Title</label>
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           value="{{ old('title', $video->title ?? '') }}"
                                           class="form-control @error('title') is-invalid @enderror"
                                           placeholder="Enter video title"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="video_url" class="form-label">Video URL</label>
                                    <input type="url" 
                                           name="video_url" 
                                           id="video_url" 
                                           value="{{ old('video_url', $video->video_url ?? '') }}"
                                           class="form-control @error('video_url') is-invalid @enderror"
                                           placeholder="Enter video URL (YouTube or S3)"
                                           required>
                                    @error('video_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        For YouTube: https://www.youtube.com/watch?v=VIDEO_ID<br>
                                        For S3: https://your-bucket.s3.amazonaws.com/video.mp4
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="video_type" class="form-label">Video Type</label>
                                            <select name="video_type" 
                                                    id="video_type" 
                                                    class="form-select @error('video_type') is-invalid @enderror">
                                                <option value="1" {{ old('video_type', $video->video_type ?? 1) == 1 ? 'selected' : '' }}>YouTube</option>
                                                <option value="0" {{ old('video_type', $video->video_type ?? 1) == 0 ? 'selected' : '' }}>S3</option>
                                            </select>
                                            @error('video_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Duration (seconds)</label>
                                            <input type="number" 
                                                   name="duration" 
                                                   id="duration" 
                                                   value="{{ old('duration', $video->duration ?? '') }}"
                                                   class="form-control @error('duration') is-invalid @enderror"
                                                   placeholder="e.g., 3600 for 1 hour"
                                                   min="0">
                                            @error('duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave empty if unknown</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="order" class="form-label">Order</label>
                                            <input type="number" 
                                                   name="order" 
                                                   id="order" 
                                                   value="{{ old('order', $video->order ?? 0) }}"
                                                   class="form-control @error('order') is-invalid @enderror"
                                                   placeholder="0"
                                                   min="0">
                                            @error('order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Lower numbers appear first</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check mt-4">
                                                <input type="checkbox" 
                                                       name="is_preview" 
                                                       id="is_preview" 
                                                       value="1"
                                                       {{ old('is_preview', $video->is_preview ?? false) ? 'checked' : '' }}
                                                       class="form-check-input">
                                                <label for="is_preview" class="form-check-label">
                                                    Preview Video
                                                </label>
                                            </div>
                                            <div class="form-text">Preview videos are free to watch</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Video Preview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="video-preview">
                                            @if(isset($video) && $video->thumbnail_url)
                                                <img src="{{ $video->thumbnail_url }}" 
                                                     alt="{{ $video->title }}" 
                                                     class="img-fluid rounded mb-2">
                                                <p class="text-muted small">{{ $video->title }}</p>
                                            @else
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-video fa-3x mb-2"></i>
                                                    <p>Video preview will appear here</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-light mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Help</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled small">
                                            <li><strong>YouTube:</strong> Use the full YouTube URL</li>
                                            <li><strong>S3:</strong> Use the direct S3 URL</li>
                                            <li><strong>Duration:</strong> Enter in seconds (3600 = 1 hour)</li>
                                            <li><strong>Order:</strong> Lower numbers appear first</li>
                                            <li><strong>Preview:</strong> Free videos for non-enrolled students</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ isset($video) ? route('videos.index', $video->course_id) : route('videos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($video) ? 'Update Video' : 'Create Video' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-detect video type from URL
        document.getElementById('video_url').addEventListener('input', function() {
            const url = this.value;
            const videoTypeSelect = document.getElementById('video_type');
            
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                videoTypeSelect.value = '1'; // YouTube
            } else if (url.includes('s3.amazonaws.com') || url.includes('amazonaws.com')) {
                videoTypeSelect.value = '0'; // S3
            }
        });

        // Update preview when URL changes
        document.getElementById('video_url').addEventListener('input', function() {
            const url = this.value;
            const previewDiv = document.getElementById('video-preview');
            
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                // Extract YouTube video ID
                const videoId = extractYouTubeVideoId(url);
                if (videoId) {
                    previewDiv.innerHTML = `
                        <img src="https://img.youtube.com/vi/${videoId}/maxresdefault.jpg" 
                             alt="YouTube Thumbnail" 
                             class="img-fluid rounded mb-2">
                        <p class="text-muted small">YouTube Video</p>
                    `;
                }
            } else {
                previewDiv.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="fas fa-video fa-3x mb-2"></i>
                        <p>Video preview will appear here</p>
                    </div>
                `;
            }
        });

        function extractYouTubeVideoId(url) {
            const pattern = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
            if (pattern.test(url)) {
                return url.match(pattern)[1];
            }
            return null;
        }
    </script>
@endsection
