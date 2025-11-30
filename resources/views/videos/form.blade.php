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
                    <!-- show form validation errors -->
                    @if(isset($errors) && $errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ isset($video) ? route('videos.update', $video) : route('videos.store') }}" method="POST" enctype="multipart/form-data">
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
                                            onchange="getCourseModules(this.value)"
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
                                    <label for="course_module_id" class="form-label">Course Module</label>
                                    <select name="course_module_id" 
                                            id="course_module_id" 
                                            class="form-select @error('course_module_id') is-invalid @enderror">
                                        <option value="">Select a course module</option>
                                    </select>
                                    @error('course_module_id')
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

                                <!-- Video File Upload (for Cloudflare) -->
                                <div class="mb-3" id="video_file_container" style="display: none;">
                                    <label for="video_file" class="form-label">Video File</label>
                                    <input type="file" 
                                           name="video_file" 
                                           id="video_file" 
                                           class="form-control @error('video_file') is-invalid @enderror"
                                           accept="video/mp4,video/avi,video/mov,video/wmv,video/flv,video/webm,video/mkv">
                                    @error('video_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Supported formats: MP4, AVI, MOV, WMV, FLV, WebM, MKV<br>
                                        Maximum file size: 50GB
                                        @if(isset($video))
                                            <br><strong>Leave empty to keep the current video file</strong>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="video_type" class="form-label">Video Type</label>
                                            <select name="video_type" 
                                                    id="video_type" 
                                                    class="form-select @error('video_type') is-invalid @enderror"
                                                    onchange="toggleVideoInputs()">
                                                <option value="1" {{ old('video_type', $video->video_type ?? 1) == 1 ? 'selected' : '' }}>YouTube</option>
                                                <option value="0" {{ old('video_type', $video->video_type ?? 1) == 0 ? 'selected' : '' }}>S3</option>
                                                <option value="2" {{ old('video_type', $video->video_type ?? 1) == 2 ? 'selected' : '' }}>Cloudflare</option>
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

                                <!-- Video URL (for YouTube/S3) -->
                                <div class="mb-3" id="video_url_container">
                                    <label for="video_url" class="form-label">Video URL</label>
                                    <input type="url" 
                                           name="video_url" 
                                           id="video_url" 
                                           value="{{ old('video_url', $video->video_url ?? '') }}"
                                           class="form-control @error('video_url') is-invalid @enderror"
                                           placeholder="Enter video URL (YouTube or S3)">
                                    @error('video_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text" id="video_url_help">
                                        For YouTube: https://www.youtube.com/watch?v=VIDEO_ID<br>
                                        For S3: https://your-bucket.s3.amazonaws.com/video.mp4
                                    </div>
                                </div>

                                <!-- Preview Image Upload -->
                                <div class="mb-3">
                                    <label for="preview_image" class="form-label">Preview Image</label>
                                    <input type="file" 
                                           name="preview_image" 
                                           id="preview_image" 
                                           class="form-control @error('preview_image') is-invalid @enderror"
                                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                           onchange="previewImage(this)">
                                    @error('preview_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Upload a custom thumbnail/preview image (JPEG, PNG, JPG, GIF, WebP)<br>
                                        Recommended size: 1280x720px or 16:9 aspect ratio
                                    </div>
                                    
                                    <!-- Current preview image display -->
                                    @if(isset($video) && $video->preview_image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $video->preview_image) }}" 
                                                 alt="Current preview" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px; max-height: 120px;">
                                            <div class="form-text">Current preview image</div>
                                        </div>
                                    @endif
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

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       name="is_published" 
                                                       id="is_published" 
                                                       value="1"
                                                       {{ old('is_published', $video->is_published ?? true) ? 'checked' : '' }}
                                                       class="form-check-input">
                                                <label for="is_published" class="form-check-label">
                                                    Published
                                                </label>
                                            </div>
                                            <div class="form-text">Published videos are visible to students</div>
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
                                            <li><strong>Cloudflare:</strong> Upload video file directly</li>
                                            <li><strong>Duration:</strong> Enter in seconds (3600 = 1 hour)</li>
                                            <li><strong>Order:</strong> Lower numbers appear first</li>
                                            <li><strong>Preview:</strong> Free videos for non-enrolled students</li>
                                            <li><strong>Published:</strong> Visible to enrolled students</li>
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
        // Detect if we're in edit mode
        const isEditMode = {{ isset($video) ? 'true' : 'false' }};
        const selectedModuleId = {{ isset($video) && $video->course_module_id ? $video->course_module_id : 'null' }};
        
        // Initialize form on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleVideoInputs();
            
            // Load course modules if editing and course is selected
            if (isEditMode) {
                const courseId = document.getElementById('course_id').value;
                if (courseId) {
                    getCourseModules(courseId, selectedModuleId);
                }
            }
        });

        // Toggle video input fields based on video type
        function toggleVideoInputs() {
            const videoType = document.getElementById('video_type').value;
            const videoFileContainer = document.getElementById('video_file_container');
            const videoUrlContainer = document.getElementById('video_url_container');
            const videoUrlInput = document.getElementById('video_url');
            const videoFileInput = document.getElementById('video_file');
            const videoUrlHelp = document.getElementById('video_url_help');

            if (videoType == '2') {
                // Cloudflare - show file upload, hide URL
                videoFileContainer.style.display = 'block';
                videoUrlContainer.style.display = 'none';
                videoFileInput.required = !isEditMode;
                videoUrlInput.required = false;
                videoUrlInput.value = '';
            } else {
                // YouTube/S3 - show URL, hide file upload
                videoFileContainer.style.display = 'none';
                videoUrlContainer.style.display = 'block';
                videoFileInput.required = false;
                videoUrlInput.required = true;
                videoFileInput.value = '';
                
                // Update help text based on type
                if (videoType == '1') {
                    videoUrlHelp.innerHTML = 'For YouTube: https://www.youtube.com/watch?v=VIDEO_ID';
                } else if (videoType == '0') {
                    videoUrlHelp.innerHTML = 'For S3: https://your-bucket.s3.amazonaws.com/video.mp4';
                }
            }
        }

        // Auto-detect video type from URL
        document.getElementById('video_url').addEventListener('input', function() {
            const url = this.value;
            const videoTypeSelect = document.getElementById('video_type');
            
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                videoTypeSelect.value = '1'; // YouTube
                toggleVideoInputs();
            } else if (url.includes('s3.amazonaws.com') || url.includes('amazonaws.com')) {
                videoTypeSelect.value = '0'; // S3
                toggleVideoInputs();
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
            } else if (url.includes('s3.amazonaws.com') || url.includes('amazonaws.com')) {
                previewDiv.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="fas fa-cloud fa-3x mb-2"></i>
                        <p>S3 Video</p>
                    </div>
                `;
            } else {
                previewDiv.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="fas fa-video fa-3x mb-2"></i>
                        <p>Video preview will appear here</p>
                    </div>
                `;
            }
        });

        // Update preview when file is selected
        document.getElementById('video_file').addEventListener('change', function() {
            const file = this.files[0];
            const previewDiv = document.getElementById('video-preview');
            
            if (file) {
                previewDiv.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="fas fa-cloud fa-3x mb-2"></i>
                        <p>Cloudflare Video</p>
                        <small>File: ${file.name}</small><br>
                        <small>Size: ${formatFileSize(file.size)}</small>
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

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create or update preview image
                    let previewDiv = document.getElementById('image-preview');
                    if (!previewDiv) {
                        previewDiv = document.createElement('div');
                        previewDiv.id = 'image-preview';
                        previewDiv.className = 'mt-2';
                        input.parentNode.appendChild(previewDiv);
                    }
                    
                    previewDiv.innerHTML = `
                        <img src="${e.target.result}" 
                             alt="Preview" 
                             class="img-thumbnail" 
                             style="max-width: 200px; max-height: 120px;">
                        <div class="form-text">New preview image</div>
                    `;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function getCourseModules(courseId, selectedModuleId = null) {
            $.ajax({
                url: "{{ route('course-modules.index') }}",
                type: "GET",
                data: { course_id: courseId },
                success: function(response) {
                    console.log(response);
                    $('#course_module_id').html('');
                    $('#course_module_id').append('<option value="">Select a course module</option>');
                    response.forEach(function(module) {
                        const selected = selectedModuleId && module.id == selectedModuleId ? ' selected' : '';
                        $('#course_module_id').append('<option value="' + module.id + '"' + selected + '>' + module.name + '</option>');
                    });
                }
            });
        }
    </script>
@endsection
