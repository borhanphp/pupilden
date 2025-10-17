@extends('layouts.app')

@section('title', 'Edit Course Module File')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-edit"></i> Edit Course Module File
                        </h4>
                        <a href="{{ route('course-module-files.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Files
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('course-module-files.update', $courseModuleFile) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="course_module_id" class="form-label">Course Module <span class="text-danger">*</span></label>
                                    <select class="form-select @error('course_module_id') is-invalid @enderror" id="course_module_id" name="course_module_id" required>
                                        <option value="">Select Course Module</option>
                                        @foreach($courseModules as $module)
                                            <option value="{{ $module->id }}" {{ old('course_module_id', $courseModuleFile->course_module_id) == $module->id ? 'selected' : '' }}>
                                                {{ $module->course->name }} - {{ $module->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_module_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">File Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $courseModuleFile->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Enter a descriptive name for this file</div>
                                </div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">Replace File</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                           id="file" name="file">
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave empty to keep current file. Max size: 10MB</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Current File</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center">
                                            @switch($courseModuleFile->file_type)
                                                @case('image')
                                                    <i class="fas fa-image text-primary fa-3x mb-2"></i>
                                                    @break
                                                @case('video')
                                                    <i class="fas fa-video text-danger fa-3x mb-2"></i>
                                                    @break
                                                @case('audio')
                                                    <i class="fas fa-music text-success fa-3x mb-2"></i>
                                                    @break
                                                @case('document')
                                                    <i class="fas fa-file-alt text-info fa-3x mb-2"></i>
                                                    @break
                                                @case('presentation')
                                                    <i class="fas fa-presentation text-warning fa-3x mb-2"></i>
                                                    @break
                                                @case('spreadsheet')
                                                    <i class="fas fa-table text-success fa-3x mb-2"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-file text-secondary fa-3x mb-2"></i>
                                            @endswitch
                                            
                                            <h6>{{ $courseModuleFile->name }}</h6>
                                            <p class="text-muted mb-2">
                                                {{ strtoupper($courseModuleFile->file_extension) }} • 
                                                {{ number_format($courseModuleFile->file_size / 1024, 1) }} KB
                                            </p>
                                            <p class="text-muted mb-3">
                                                <small>{{ ucfirst($courseModuleFile->file_type) }}</small>
                                            </p>
                                            
                                            <a href="{{ route('course-module-files.download', $courseModuleFile) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">File Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">
                                                    <strong>Uploaded:</strong><br>
                                                    {{ $courseModuleFile->created_at->format('M d, Y') }}
                                                </small>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">
                                                    <strong>Last Updated:</strong><br>
                                                    {{ $courseModuleFile->updated_at->format('M d, Y') }}
                                                </small>
                                            </div>
                                        </div>
                                        
                                        @if($courseModuleFile->courseModule)
                                            <hr>
                                            <div>
                                                <small class="text-muted">
                                                    <strong>Course:</strong><br>
                                                    {{ $courseModuleFile->courseModule->course->name }}
                                                </small>
                                            </div>
                                            <div>
                                                <small class="text-muted">
                                                    <strong>Module:</strong><br>
                                                    {{ $courseModuleFile->courseModule->name }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('course-module-files.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update File
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

