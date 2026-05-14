@extends('layouts.app')

@section('title', 'Course Modules')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-layer-group"></i> Course Modules
                        </h4>
                        <a href="{{ route('course-modules.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Module
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
                                <select name="course_id" class="form-select me-2">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->name }}
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
                                <input type="hidden" name="course_id" value="{{ request('course_id') }}">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="Search modules..." value="{{ request('search') }}">
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
                                    <th>Order</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Files</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courseModules as $module)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $module->order }}</span>
                                        </td>
                                        <td>
                                            @if($module->image)
                                                <img src="{{ \Storage::disk('r2')->url(auth()->user()->organization_id . '/course_modules/' . $module->image) }}" 
                                                     alt="{{ $module->name }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-layer-group text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $module->name }}</strong>
                                            </div>
                                            <small class="text-muted">{{ Str::limit($module->description, 50) }}</small>
                                        </td>
                                        <td>
                                            @if($module->course)
                                                <span class="badge bg-primary">{{ $module->course->name }}</span>
                                            @else
                                                <span class="text-muted">No course</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($module->duration && $module->duration_value)
                                                <span class="text-info">
                                                    {{ $module->duration_value }} {{ $module->duration_type }}
                                                </span>
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('course-modules.toggle-status', $module) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $module->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                                                    {{ ucfirst($module->status) }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $module->files->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('course-modules.edit', $module) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('course-modules.show', $module) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form action="{{ route('course-modules.destroy', $module) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this module?')">
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
                                        <td colspan="8" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-layer-group fa-3x mb-3"></i>
                                                <p>No course modules found.</p>
                                                <a href="{{ route('course-modules.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add your first module
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($courseModules->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $courseModules->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
