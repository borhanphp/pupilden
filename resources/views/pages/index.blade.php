@extends('layouts.app')

@section('title', 'Pages')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-file-alt"></i> Pages
                        </h4>
                        <div>
                            <a href="{{ route('pages.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Page
                            </a>
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
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Sections</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pages as $page)
                                    <tr>
                                        <td>
                                            <strong>{{ $page->title }}</strong>
                                        </td>
                                        <td>
                                            <code>{{ $page->slug }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-capitalize">{{ $page->type }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $page->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $page->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $page->sections->count() }} sections</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sections.index', $page) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-th"></i> Sections
                                                </a>
                                                <a href="{{ route('pages.edit', $page) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this page?')">
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
                                        <td colspan="6" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-file-alt fa-3x mb-3"></i>
                                                <p>No pages found.</p>
                                                <a href="{{ route('pages.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create your first page
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

