@extends('layouts.app')

@section('title', 'Course Module File Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-file"></i> Course Module File Details
                        </h4>
                        <div>
                            <a href="{{ route('course-module-files.download', $courseModuleFile) }}" class="btn btn-primary me-2">
                                <i class="fas fa-download"></i> Download File
                            </a>
                            <a href="{{ route('course-module-files.edit', $courseModuleFile) }}" class="btn btn-secondary me-2">
                                <i class="fas fa-edit"></i> Edit File
                            </a>
                            <a href="{{ route('course-module-files.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Files
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
                                        <label class="form-label fw-bold">File Name</label>
                                        <p class="form-control-plaintext">{{ $courseModuleFile->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">File Type</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-secondary">{{ ucfirst($courseModuleFile->file_type) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">File Extension</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-info">{{ strtoupper($courseModuleFile->file_extension) }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">File Size</label>
                                        <p class="form-control-plaintext">
                                            <span class="text-info">{{ number_format($courseModuleFile->file_size / 1024, 1) }} KB</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">MIME Type</label>
                                        <p class="form-control-plaintext">
                                            <code>{{ $courseModuleFile->file_mime_type }}</code>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Course Module</label>
                                        <p class="form-control-plaintext">
                                            @if($courseModuleFile->courseModule)
                                                <div>
                                                    <span class="badge bg-primary">{{ $courseModuleFile->courseModule->course->name }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $courseModuleFile->courseModule->name }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">No module assigned</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Created By</label>
                                        <p class="form-control-plaintext">
                                            @if($courseModuleFile->creator)
                                                {{ $courseModuleFile->creator->name }}
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
                                            @if($courseModuleFile->updater)
                                                {{ $courseModuleFile->updater->name }}
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
                                        <p class="form-control-plaintext">{{ $courseModuleFile->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Updated At</label>
                                        <p class="form-control-plaintext">{{ $courseModuleFile->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">File URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $courseModuleFile->file_url }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $courseModuleFile->file_url }}')">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">File Preview</h6>
                                </div>
                                <div class="card-body text-center">
                                    @switch($courseModuleFile->file_type)
                                        @case('image')
                                            <img src="{{ $courseModuleFile->file_url }}" 
                                                 alt="{{ $courseModuleFile->name }}" 
                                                 class="img-fluid rounded" 
                                                 style="max-height: 200px;">
                                            @break
                                        @case('video')
                                            <i class="fas fa-video text-danger fa-4x mb-3"></i>
                                            <p class="text-muted">Video File</p>
                                            @break
                                        @case('audio')
                                            <i class="fas fa-music text-success fa-4x mb-3"></i>
                                            <p class="text-muted">Audio File</p>
                                            @break
                                        @case('document')
                                            <i class="fas fa-file-alt text-info fa-4x mb-3"></i>
                                            <p class="text-muted">Document File</p>
                                            @break
                                        @case('presentation')
                                            <i class="fas fa-presentation text-warning fa-4x mb-3"></i>
                                            <p class="text-muted">Presentation File</p>
                                            @break
                                        @case('spreadsheet')
                                            <i class="fas fa-table text-success fa-4x mb-3"></i>
                                            <p class="text-muted">Spreadsheet File</p>
                                            @break
                                        @default
                                            <i class="fas fa-file text-secondary fa-4x mb-3"></i>
                                            <p class="text-muted">File</p>
                                    @endswitch
                                    
                                    <h6>{{ $courseModuleFile->name }}</h6>
                                    <p class="text-muted mb-3">
                                        {{ strtoupper($courseModuleFile->file_extension) }} • 
                                        {{ number_format($courseModuleFile->file_size / 1024, 1) }} KB
                                    </p>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('course-module-files.download', $courseModuleFile) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-download"></i> Download File
                                        </a>
                                        <a href="{{ route('course-module-files.edit', $courseModuleFile) }}" 
                                           class="btn btn-outline-secondary">
                                            <i class="fas fa-edit"></i> Edit File
                                        </a>
                                    </div>
                                </div>
                            </div>

                            @if($courseModuleFile->courseModule)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Module Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Course:</strong><br>
                                            <span class="badge bg-primary">{{ $courseModuleFile->courseModule->course->name }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Module:</strong><br>
                                            <span class="text-muted">{{ $courseModuleFile->courseModule->name }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Module Status:</strong><br>
                                            <span class="badge {{ $courseModuleFile->courseModule->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($courseModuleFile->courseModule->status) }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Module Order:</strong><br>
                                            <span class="badge bg-secondary">{{ $courseModuleFile->courseModule->order }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show a temporary success message
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Copied!';
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-secondary');
                
                setTimeout(function() {
                    button.innerHTML = originalHTML;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-secondary');
                }, 2000);
            });
        }
    </script>
@endsection

