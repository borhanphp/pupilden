@extends('layouts.app')

@section('title', 'Upload Course Module File')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-upload"></i> Upload Course Module File
                        </h4>
                        <a href="{{ route('course-module-files.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Files
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('course-module-files.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="course_module_id" class="form-label">Course Module <span class="text-danger">*</span></label>
                                    <select class="form-select @error('course_module_id') is-invalid @enderror" id="course_module_id" name="course_module_id" required>
                                        <option value="">Select Course Module</option>
                                        @foreach($courseModules as $module)
                                            <option value="{{ $module->id }}" {{ old('course_module_id', $selectedCourseModuleId) == $module->id ? 'selected' : '' }}>
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
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Enter a descriptive name for this file</div>
                                </div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">Select File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                           id="file" name="file" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Max size: 10MB. Supported formats: All file types</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">File Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Supported File Types</label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">
                                                        <strong>Images:</strong><br>
                                                        JPG, PNG, GIF, SVG, WebP
                                                    </small>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">
                                                        <strong>Videos:</strong><br>
                                                        MP4, AVI, MOV, WebM
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <small class="text-muted">
                                                        <strong>Audio:</strong><br>
                                                        MP3, WAV, OGG, AAC
                                                    </small>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">
                                                        <strong>Documents:</strong><br>
                                                        PDF, DOC, DOCX, TXT
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <small class="text-muted">
                                                        <strong>Presentations:</strong><br>
                                                        PPT, PPTX, ODP
                                                    </small>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">
                                                        <strong>Spreadsheets:</strong><br>
                                                        XLS, XLSX, CSV, ODS
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <small>
                                                <i class="fas fa-info-circle"></i>
                                                <strong>Note:</strong> Files will be automatically categorized based on their extension.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Upload Guidelines</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-1">
                                                <i class="fas fa-check text-success"></i>
                                                <small>Maximum file size: 10MB</small>
                                            </li>
                                            <li class="mb-1">
                                                <i class="fas fa-check text-success"></i>
                                                <small>All file types supported</small>
                                            </li>
                                            <li class="mb-1">
                                                <i class="fas fa-check text-success"></i>
                                                <small>Automatic file categorization</small>
                                            </li>
                                            <li class="mb-1">
                                                <i class="fas fa-check text-success"></i>
                                                <small>Secure file storage</small>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('course-module-files.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload File
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

