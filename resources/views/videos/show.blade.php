@extends('layouts.app')

@section('title', 'Video Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Video Information</h4>
                        <div class="btn-group" role="group">
                            <a href="{{ route('videos.edit', $video) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Video
                            </a>
                            <a href="{{ route('videos.index', $video->course_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Video Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Title</label>
                                        <h5 class="mb-0">{{ $video->title }}</h5>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Video URL</label>
                                        <p class="form-control-plaintext">
                                            <a href="{{ $video->video_url }}" target="_blank" class="text-decoration-none">
                                                {{ $video->video_url }}
                                                <i class="fas fa-external-link-alt ms-1"></i>
                                            </a>
                                        </p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Video Type</label>
                                                <div>
                                                    <span class="badge bg-{{ $video->video_type === 0 ? 'info' : 'danger' }} fs-6">
                                                        {{ $video->video_type_label }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Duration</label>
                                                <p class="form-control-plaintext">{{ $video->formatted_duration }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Order</label>
                                                <p class="form-control-plaintext">{{ $video->order }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Preview Status</label>
                                                <div>
                                                    @if($video->is_preview)
                                                        <span class="badge bg-success fs-6">Preview Available</span>
                                                    @else
                                                        <span class="badge bg-secondary fs-6">No Preview</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Video Thumbnail</h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($video->thumbnail_url)
                                        <img src="{{ $video->thumbnail_url }}" 
                                             alt="{{ $video->title }}" 
                                             class="img-fluid rounded" 
                                             style="max-width: 100%; height: auto;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="fas fa-video fa-3x text-muted"></i>
                                        </div>
                                        <p class="text-muted mt-2">No thumbnail available</p>
                                    @endif
                                </div>
                            </div>

                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Course Information</h5>
                                </div>
                                <div class="card-body">
                                    @if($video->course)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Course</label>
                                            <div>
                                                <a href="{{ route('courses.show', $video->course) }}" class="text-decoration-none">
                                                    <span class="badge bg-primary fs-6">{{ $video->course->name }}</span>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Course Category</label>
                                            <div>
                                                @if($video->course->courseCategory)
                                                    <span class="badge bg-info fs-6">{{ $video->course->courseCategory->name }}</span>
                                                @else
                                                    <span class="text-muted">No category</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted">No course assigned</p>
                                    @endif
                                </div>
                            </div>

                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Timestamps</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Created</label>
                                        <p class="form-control-plaintext">{{ $video->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Last Updated</label>
                                        <p class="form-control-plaintext">{{ $video->updated_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($video->creator || $video->updater)
                                <div class="card bg-light mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">User Information</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($video->creator)
                                            <div class="mb-2">
                                                <label class="form-label fw-bold">Created By</label>
                                                <p class="form-control-plaintext">{{ $video->creator->name }}</p>
                                            </div>
                                        @endif
                                        @if($video->updater)
                                            <div class="mb-2">
                                                <label class="form-label fw-bold">Last Updated By</label>
                                                <p class="form-control-plaintext">{{ $video->updater->name }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($video->video_type === 1)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Video Player</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="ratio ratio-16x9">
                                            @php
                                                $videoId = $video->extractYouTubeVideoId($video->video_url);
                                                $embedUrl = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
                                            @endphp
                                            @if($embedUrl)
                                                <iframe src="{{ $embedUrl }}" 
                                                        title="{{ $video->title }}"
                                                        frameborder="0" 
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                        allowfullscreen>
                                                </iframe>
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-dark text-white">
                                                    <div class="text-center">
                                                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                                        <p>Unable to load video player</p>
                                                        <a href="{{ $video->video_url }}" target="_blank" class="btn btn-primary">
                                                            Watch on YouTube
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
