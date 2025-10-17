@extends('layouts.app')

@section('title', 'Course Module Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-layer-group"></i> Course Module Details
                        </h4>
                        <div>
                            <a href="{{ route('course-modules.edit', $courseModule) }}" class="btn btn-primary me-2">
                                <i class="fas fa-edit"></i> Edit Module
                            </a>
                            <a href="{{ route('course-modules.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Modules
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Module Name</label>
                                        <p class="form-control-plaintext">{{ $courseModule->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge {{ $courseModule->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($courseModule->status) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Course</label>
                                        <p class="form-control-plaintext">
                                            @if($courseModule->course)
                                                <span class="badge bg-primary">{{ $courseModule->course->name }}</span>
                                            @else
                                                <span class="text-muted">No course assigned</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Order</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-secondary">{{ $courseModule->order }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Duration</label>
                                        <p class="form-control-plaintext">
                                            @if($courseModule->duration_value && $courseModule->duration_type)
                                                <span class="text-info">
                                                    {{ $courseModule->duration_value }} {{ $courseModule->duration_type }}
                                                </span>
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Files Count</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-info">{{ $courseModule->files->count() }} files</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <div class="form-control-plaintext">
                                    @if($courseModule->description)
                                        {{ $courseModule->description }}
                                    @else
                                        <span class="text-muted">No description provided</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Created By</label>
                                        <p class="form-control-plaintext">
                                            @if($courseModule->creator)
                                                {{ $courseModule->creator->name }}
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Updated By</label>
                                        <p class="form-control-plaintext">
                                            @if($courseModule->updater)
                                                {{ $courseModule->updater->name }}
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Created At</label>
                                        <p class="form-control-plaintext">{{ $courseModule->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Updated At</label>
                                        <p class="form-control-plaintext">{{ $courseModule->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Module Image</h6>
                                </div>
                                <div class="card-body text-center">
                                    @if($courseModule->image)
                                        <img src="{{ asset('uploads/' . auth()->user()->organization_id . '/course_modules/' . $courseModule->image) }}" 
                                             alt="{{ $courseModule->name }}" 
                                             class="img-thumbnail" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 100px; height: 100px;">
                                            <i class="fas fa-layer-group text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($courseModule->files->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Module Files</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($courseModule->files as $file)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                                <div>
                                                    <strong>{{ $file->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $file->file_extension }} • {{ number_format($file->file_size / 1024, 1) }} KB</small>
                                                </div>
                                                <a href="{{ route('course-module-files.download', $file) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

