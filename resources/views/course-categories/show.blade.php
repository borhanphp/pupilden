@extends('layouts.app')

@section('title', 'Course Category Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Category Information</h4>
                        <div class="btn-group" role="group">
                            <a href="{{ route('course-categories.edit', $courseCategory) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Category
                            </a>
                            <a href="{{ route('course-categories.index') }}" class="btn btn-secondary">
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
                                    <h5 class="card-title mb-0">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Category Name</label>
                                        <div class="d-flex align-items-center">
                                            @if($courseCategory->icon)
                                                <i class="{{ $courseCategory->icon }} me-2 fa-2x"></i>
                                            @endif
                                            <h5 class="mb-0">{{ $courseCategory->name }}</h5>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Slug</label>
                                        <p class="form-control-plaintext"><code>{{ $courseCategory->slug }}</code></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <p class="form-control-plaintext">
                                            {{ $courseCategory->description ?: 'No description provided' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Status & Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <div>
                                            @if($courseCategory->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Total Courses</label>
                                        <div>
                                            <span class="badge bg-info fs-6">{{ $courseCategory->courses->count() }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Sub Categories</label>
                                        <div>
                                            <span class="badge bg-warning text-dark fs-6">{{ $courseCategory->subCategories->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Timestamps</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Created</label>
                                        <p class="form-control-plaintext">{{ $courseCategory->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Last Updated</label>
                                        <p class="form-control-plaintext">{{ $courseCategory->updated_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($courseCategory->courses->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Courses in this Category</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Course Name</th>
                                                        <th>Status</th>
                                                        <th>Students</th>
                                                        <th>Created</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($courseCategory->courses->take(5) as $course)
                                                        <tr>
                                                            <td>{{ $course->title }}</td>
                                                            <td>
                                                                @if($course->is_active)
                                                                    <span class="badge bg-success">Active</span>
                                                                @else
                                                                    <span class="badge bg-danger">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $course->students->count() }}</td>
                                                            <td>{{ $course->created_at->format('M j, Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($courseCategory->courses->count() > 5)
                                            <div class="text-center mt-2">
                                                <small class="text-muted">Showing 5 of {{ $courseCategory->courses->count() }} courses</small>
                                            </div>
                                        @endif
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
