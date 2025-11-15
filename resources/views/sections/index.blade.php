@extends('layouts.app')

@section('title', 'Page Sections')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-th"></i> Sections for: <strong>{{ $page->title }}</strong>
                        </h4>
                        <div>
                            <a href="{{ route('pages.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Pages
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                                <i class="fas fa-plus"></i> Add Section
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Layout</th>
                                    <th>Contents</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sections as $section)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $section->order }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $section->title ?? 'Untitled Section' }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $section->section_type }}</span>
                                        </td>
                                        <td>
                                            @if($section->layout)
                                                <span class="badge bg-primary">{{ $section->layout->name }}</span>
                                            @else
                                                <span class="text-muted">No layout</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $section->contents->count() }} items</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $section->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $section->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('contents.index', $section) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Content
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#editSectionModal{{ $section->id }}">
                                                    <i class="fas fa-cog"></i> Edit
                                                </button>
                                                <form action="{{ route('sections.destroy', $section) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this section?')">
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
                                                <i class="fas fa-th fa-3x mb-3"></i>
                                                <p>No sections found for this page.</p>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                                                    <i class="fas fa-plus"></i> Add your first section
                                                </button>
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

    <!-- Add Section Modal -->
    <div class="modal fade" id="addSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('sections.store', $page) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="section_type" class="form-label">Section Type <span class="text-danger">*</span></label>
                            <select name="section_type" id="section_type" class="form-select" required>
                                <option value="">Select type</option>
                                <option value="hero">Hero</option>
                                <option value="about">About</option>
                                <option value="features">Features</option>
                                <option value="testimonials">Testimonials</option>
                                <option value="contact">Contact</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="layout_id" class="form-label">Layout</label>
                            <select name="layout_id" id="layout_id" class="form-select">
                                <option value="">No layout</option>
                                @foreach($layouts as $layout)
                                    <option value="{{ $layout->id }}">{{ $layout->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Section title">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Section Modals -->
    @foreach($sections as $section)
        <div class="modal fade" id="editSectionModal{{ $section->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('sections.update', $section) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Section</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title{{ $section->id }}" class="form-label">Title</label>
                                <input type="text" name="title" id="title{{ $section->id }}" class="form-control" value="{{ $section->title }}">
                            </div>
                            <div class="mb-3">
                                <label for="layout_id{{ $section->id }}" class="form-label">Layout</label>
                                <select name="layout_id" id="layout_id{{ $section->id }}" class="form-select">
                                    <option value="">No layout</option>
                                    @foreach($layouts as $layout)
                                        <option value="{{ $layout->id }}" {{ $section->layout_id == $layout->id ? 'selected' : '' }}>
                                            {{ $layout->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="order{{ $section->id }}" class="form-label">Order</label>
                                <input type="number" name="order" id="order{{ $section->id }}" class="form-control" value="{{ $section->order }}">
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active{{ $section->id }}" {{ $section->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active{{ $section->id }}">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Section</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

