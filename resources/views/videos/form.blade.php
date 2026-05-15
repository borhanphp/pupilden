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
                    <form action="{{ isset($video) ? route('videos.update', $video) : route('videos.store') }}" method="POST" enctype="multipart/form-data" id="videoForm">
                        @csrf
                        @if(isset($video))
                            @method('PUT')
                        @endif
                        
                        <!-- Upload Progress Overlay -->
                        <div id="uploadOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; justify-content: center; align-items: center;">
                            <div style="background: white; padding: 30px; border-radius: 10px; text-align: center; max-width: 500px; width: 90%;">
                                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h4 class="mb-3">Uploading Video...</h4>
                                <p class="text-muted mb-3">Please wait while your video is being uploaded. This may take several minutes depending on the file size.</p>
                                <div class="progress mb-3" style="height: 25px;">
                                    <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Do not close this window or navigate away
                                </small>
                            </div>
                        </div>

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
                                        Maximum file size: 1GB
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
            
            // Load course modules whenever a course is already selected (create or edit)
            const courseSelect = document.getElementById('course_id');
            if (courseSelect.value) {
                getCourseModules(courseSelect.value, isEditMode ? selectedModuleId : null);
            }

            // Load modules whenever user changes the selected course
            courseSelect.addEventListener('change', function() {
                getCourseModules(this.value, null);
            });
            
            // Handle form submission with upload progress
            const form = document.getElementById('videoForm');
            const uploadOverlay = document.getElementById('uploadOverlay');
            const progressBar = document.getElementById('uploadProgressBar');
            
            form.addEventListener('submit', async function(e) {
                const videoType = document.getElementById('video_type').value;
                const videoFile = document.getElementById('video_file').files[0];
                const title = document.getElementById('title').value;
                
                // Handle Cloudflare direct upload for large files (> 500MB)
                if (videoType == '2' && videoFile) {
                    const fileSizeMB = videoFile.size / (1024 * 1024);
                    const largeFileThreshold = 500; // 500MB — matches server-side limit in VideoController

                    if (fileSizeMB > largeFileThreshold) {
                        // Use direct upload for large files
                        e.preventDefault();
                        await handleDirectUpload(videoFile, title);
                        return false;
                    } else {
                        // Show progress for regular upload
                        uploadOverlay.style.display = 'flex';
                        
                        // Simulate progress
                        let progress = 0;
                        const interval = setInterval(function() {
                            if (progress < 90) {
                                progress += Math.random() * 10;
                                if (progress > 90) progress = 90;
                                progressBar.style.width = progress + '%';
                                progressBar.textContent = Math.round(progress) + '%';
                                progressBar.setAttribute('aria-valuenow', progress);
                            }
                        }, 500);
                        
                        form.dataset.progressInterval = interval;
                    }
                }
            });
            
            // Handle direct upload to Cloudflare
            async function handleDirectUpload(file, title) {
                try {
                    // Show upload overlay
                    uploadOverlay.style.display = 'flex';
                    progressBar.style.width = '5%';
                    progressBar.textContent = '5%';
                    progressBar.setAttribute('aria-valuenow', 5);
                    
                    // Step 1: Get direct upload URL
                    progressBar.textContent = 'Getting upload URL...';
                    const response = await fetch('{{ route("videos.direct-upload-url") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            title: title,
                            file_size: file.size
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to get upload URL');
                    }
                    
                    // Step 2: Upload directly to Cloudflare with progress tracking
                    progressBar.textContent = 'Uploading to Cloudflare...';
                    progressBar.style.width = '10%';
                    
                    // Function to upload file
                    const uploadFile = async (useFormData = false) => {
                        return new Promise((resolve, reject) => {
                            const xhr = new XMLHttpRequest();
                            
                            // Track upload progress
                            xhr.upload.addEventListener('progress', function(e) {
                                if (e.lengthComputable) {
                                    const percentComplete = Math.round((e.loaded / e.total) * 90) + 10; // 10-100%
                                    progressBar.style.width = percentComplete + '%';
                                    progressBar.textContent = percentComplete + '%';
                                    progressBar.setAttribute('aria-valuenow', percentComplete);
                                }
                            });
                            
                            xhr.addEventListener('load', function() {
                                console.log('Upload response status:', xhr.status);
                                console.log('Upload response:', xhr.responseText);
                                
                                if (xhr.status >= 200 && xhr.status < 300) {
                                    try {
                                        const responseText = xhr.responseText;
                                        if (responseText) {
                                            const result = JSON.parse(responseText);
                                            console.log('Parsed upload result:', result);
                                            resolve(result);
                                        } else {
                                            resolve(null);
                                        }
                                    } catch (e) {
                                        console.log('Upload response (non-JSON):', xhr.responseText);
                                        resolve(null);
                                    }
                                } else {
                                    let errorMsg = 'Upload failed with status: ' + xhr.status;
                                    let errorDetails = '';
                                    
                                    try {
                                        const errorResponse = JSON.parse(xhr.responseText);
                                        console.error('Cloudflare upload error:', errorResponse);
                                        
                                        if (errorResponse.errors && errorResponse.errors.length > 0) {
                                            errorDetails = errorResponse.errors.map(e => e.message || e).join(', ');
                                            errorMsg += ' - ' + errorDetails;
                                        } else if (errorResponse.message) {
                                            errorMsg += ' - ' + errorResponse.message;
                                        } else {
                                            errorMsg += ' - ' + JSON.stringify(errorResponse);
                                        }
                                    } catch (e) {
                                        errorMsg += ' - ' + xhr.responseText;
                                    }
                                    
                                    console.error('Upload failed:', errorMsg);
                                    reject(new Error(errorMsg));
                                }
                            });
                            
                            xhr.addEventListener('error', function() {
                                reject(new Error('Network error during upload'));
                            });
                            
                            xhr.addEventListener('abort', function() {
                                reject(new Error('Upload aborted'));
                            });
                            
                            xhr.addEventListener('timeout', function() {
                                reject(new Error('Upload timeout - file may be too large'));
                            });
                            
                            // Set timeout for large files (2 hours)
                            xhr.timeout = 7200000; // 2 hours in milliseconds
                            
                            // Open connection
                            xhr.open('POST', data.upload_url);
                            
                            // Try different upload methods
                            if (useFormData) {
                                // Method 1: FormData (multipart/form-data)
                                const formData = new FormData();
                                formData.append('file', file);
                                xhr.send(formData);
                            } else {
                                // Method 2: Raw file (binary)
                                // Don't set Content-Type - let browser set it
                                xhr.send(file);
                            }
                        });
                    };
                    
                    // Wait for upload to complete - try raw file first, then FormData if it fails
                    let uploadResult = null;
                    try {
                        uploadResult = await uploadFile(false); // Try raw file first
                    } catch (error) {
                        console.log('Raw file upload failed, trying FormData:', error.message);
                        // If raw file fails with 400, try FormData
                        if (error.message.includes('400')) {
                            try {
                                uploadResult = await uploadFile(true); // Try FormData
                            } catch (formDataError) {
                                throw new Error('Upload failed with both methods: ' + formDataError.message);
                            }
                        } else {
                            throw error;
                        }
                    }
                    
                    // Step 3: Get video ID from upload response or use the one from initial request
                    progressBar.textContent = 'Processing video...';
                    progressBar.style.width = '95%';
                    
                    // Get video ID - prefer the one from upload response if available
                    let videoId = data.video_id;
                    if (uploadResult) {
                        if (uploadResult.uid) {
                            videoId = uploadResult.uid;
                        } else if (uploadResult.result && uploadResult.result.uid) {
                            videoId = uploadResult.result.uid;
                        }
                    }
                    
                    // Wait a moment for Cloudflare to process
                    await new Promise(resolve => setTimeout(resolve, 2000));
                    
                    // Step 4: Submit form with Cloudflare video ID
                    progressBar.textContent = 'Finalizing...';
                    progressBar.style.width = '100%';
                    
                    // Add hidden input with Cloudflare video ID
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'cloudflare_upload_id';
                    hiddenInput.value = videoId;
                    form.appendChild(hiddenInput);
                    
                    // Remove file input to prevent double upload
                    const fileInput = document.getElementById('video_file');
                    fileInput.value = '';
                    
                    // Submit form
                    form.submit();
                    
                } catch (error) {
                    console.error('Direct upload error:', error);
                    uploadOverlay.style.display = 'none';
                    alert('Upload failed: ' + error.message + '\n\nPlease try again or use a smaller file.');
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
            if (!courseId) return;

            const select = document.getElementById('course_module_id');
            select.innerHTML = '<option value="">Loading...</option>';

            fetch(`{{ route('course-modules.index') }}?course_id=${encodeURIComponent(courseId)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(function(res) {
                if (!res.ok) throw new Error('Server returned ' + res.status);
                return res.json();
            })
            .then(function(modules) {
                select.innerHTML = '<option value="">Select a course module</option>';
                if (modules.length === 0) {
                    select.innerHTML = '<option value="">No modules found for this course</option>';
                    return;
                }
                modules.forEach(function(module) {
                    const opt = document.createElement('option');
                    opt.value = module.id;
                    opt.textContent = module.name;
                    if (selectedModuleId && module.id == selectedModuleId) opt.selected = true;
                    select.appendChild(opt);
                });
            })
            .catch(function(err) {
                select.innerHTML = '<option value="">Failed to load modules</option>';
                console.error('getCourseModules error:', err);
            });
        }
    </script>
@endsection
