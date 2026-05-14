@extends('layouts.app')

@section('title', 'Courses')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-graduation-cap"></i> Courses
                        </h4>
                        <a href="{{ route('courses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Course
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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Sub-Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Students</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courses as $course)
                                    <tr>
                                        <td>
                                            @if($course->image)
                                                <img src="{{ \Storage::disk('r2')->url(auth()->user()->organization_id . '/course_images/' . $course->image) }}" 
                                                     alt="{{ $course->name }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $course->name }}</strong>
                                                @if($course->is_featured)
                                                    <span class="badge bg-warning text-dark ms-1">Featured</span>
                                                @endif
                                                @if($course->is_archived)
                                                    <span class="badge bg-secondary ms-1">Archived</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ Str::limit($course->description, 50) }}</small>
                                        </td>
                                        <td>
                                            @if($course->courseCategory)
                                                <span class="badge bg-primary">{{ $course->courseCategory->name }}</span>
                                            @else
                                                <span class="text-muted">No category</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($course->courseSubCategory)
                                                <span class="badge bg-info">{{ $course->courseSubCategory->name }}</span>
                                            @else
                                                <span class="text-muted">No sub-category</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($course->price > 0)
                                                <span class="text-success fw-bold">${{ number_format($course->price, 2) }}</span>
                                            @else
                                                <span class="badge bg-success">Free</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                @if($course->is_published)
                                                    <span class="badge bg-success">Published</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Draft</span>
                                                @endif
                                                @if($course->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $course->students->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?')">
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
                                                <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                                                <p>No courses found.</p>
                                                <a href="{{ route('courses.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add your first course
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
