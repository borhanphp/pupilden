@extends('layouts.app')

@section('title', 'Course Module Files')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-file"></i> Course Module Files
                        </h4>
                        <a href="{{ route('course-module-files.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Upload New File
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filter and Search -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <select name="course_module_id" class="form-select me-2">
                                    <option value="">All Modules</option>
                                    @foreach($courseModules as $module)
                                        <option value="{{ $module->id }}" {{ request('course_module_id') == $module->id ? 'selected' : '' }}>
                                            {{ $module->course->name }} - {{ $module->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="hidden" name="course_module_id" value="{{ request('course_module_id') }}">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="Search files..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>File Icon</th>
                                    <th>Name</th>
                                    <th>Course Module</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courseModuleFiles as $file)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                @switch($file->file_type)
                                                    @case('image')
                                                        <i class="fas fa-image text-primary fa-lg"></i>
                                                        @break
                                                    @case('video')
                                                        <i class="fas fa-video text-danger fa-lg"></i>
                                                        @break
                                                    @case('audio')
                                                        <i class="fas fa-music text-success fa-lg"></i>
                                                        @break
                                                    @case('document')
                                                        <i class="fas fa-file-alt text-info fa-lg"></i>
                                                        @break
                                                    @case('presentation')
                                                        <i class="fas fa-presentation text-warning fa-lg"></i>
                                                        @break
                                                    @case('spreadsheet')
                                                        <i class="fas fa-table text-success fa-lg"></i>
                                                        @break
                                                    @default
                                                        <i class="fas fa-file text-secondary fa-lg"></i>
                                                @endswitch
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $file->name }}</strong>
                                            </div>
                                            <small class="text-muted">{{ strtoupper($file->file_extension) }}</small>
                                        </td>
                                        <td>
                                            @if($file->courseModule)
                                                <div>
                                                    <span class="badge bg-primary">{{ $file->courseModule->course->name }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $file->courseModule->name }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">No module</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($file->file_type) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-info">{{ number_format($file->file_size / 1024, 1) }} KB</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $file->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('course-module-files.download', $file) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                <a href="{{ route('course-module-files.edit', $file) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('course-module-files.show', $file) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form action="{{ route('course-module-files.destroy', $file) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-file fa-3x mb-3"></i>
                                                <p>No files found.</p>
                                                <a href="{{ route('course-module-files.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Upload your first file
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($courseModuleFiles->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $courseModuleFiles->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

