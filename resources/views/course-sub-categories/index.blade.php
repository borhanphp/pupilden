@extends('layouts.app')

@section('title', 'Course Sub-Categories')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-list"></i> Course Sub-Categories
                        </h4>
                        <a href="{{ route('course-sub-categories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Sub-Category
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
                                    <th>Name</th>
                                    <th>Parent Category</th>
                                    <th>Slug</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Courses</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subCategories as $subCategory)
                                    <tr>
                                        <td>
                                            <strong>{{ $subCategory->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $subCategory->courseCategory->name }}</span>
                                        </td>
                                        <td>
                                            <code>{{ $subCategory->slug }}</code>
                                        </td>
                                        <td>
                                            @if($subCategory->description)
                                                <span class="text-muted">{{ Str::limit($subCategory->description, 50) }}</span>
                                            @else
                                                <span class="text-muted">No description</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($subCategory->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $subCategory->courses->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('course-sub-categories.edit', $subCategory) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('course-sub-categories.show', $subCategory) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form action="{{ route('course-sub-categories.destroy', $subCategory) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this sub-category?')">
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
                                                <i class="fas fa-folder-open fa-3x mb-3"></i>
                                                <p>No course sub-categories found.</p>
                                                <a href="{{ route('course-sub-categories.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add your first sub-category
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
