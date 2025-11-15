@extends('layouts.app')

@section('title', 'Section Contents')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-edit"></i> Contents for: <strong>{{ $section->title ?? 'Section ' . $section->id }}</strong>
                        </h4>
                        <div>
                            <a href="{{ route('sections.index', $section->page) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Sections
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContentModal">
                                <i class="fas fa-plus"></i> Add Content
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
                                    <th>Block Index</th>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Style</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contents as $content)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $content->block_index }}</span>
                                        </td>
                                        <td>
                                            <code>{{ $content->key }}</code>
                                        </td>
                                        <td>
                                            <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                                {{ Str::limit($content->value, 50) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($content->style)
                                                <span class="badge bg-info">Has styles</span>
                                            @else
                                                <span class="text-muted">No styles</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#editContentModal{{ $content->id }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('contents.destroy', $content) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this content?')">
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
                                        <td colspan="5" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-edit fa-3x mb-3"></i>
                                                <p>No content found for this section.</p>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContentModal">
                                                    <i class="fas fa-plus"></i> Add your first content
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

    <!-- Add Content Modal -->
    <div class="modal fade" id="addContentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('contents.store', $section) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Content</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="key" class="form-label">Key <span class="text-danger">*</span></label>
                            <input type="text" name="key" id="key" class="form-control" placeholder="heading, text, button_text, etc." required>
                            <div class="form-text">Identifier for this content block (e.g., heading, description, button_text)</div>
                        </div>
                        <div class="mb-3">
                            <label for="block_index" class="form-label">Block Index <span class="text-danger">*</span></label>
                            <input type="number" name="block_index" id="block_index" class="form-control" value="0" required>
                            <div class="form-text">Position of this content in the grid (0, 1, 2, etc.)</div>
                        </div>
                        <div class="mb-3">
                            <label for="value" class="form-label">Value</label>
                            <textarea name="value" id="value" class="form-control" rows="4" placeholder="Content text, HTML, or data"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="style" class="form-label">Style (JSON)</label>
                            <textarea name="style" id="style" class="form-control" rows="3" placeholder='{"color": "#000", "fontSize": "16px"}'></textarea>
                            <div class="form-text">Optional: JSON object for styling (e.g., {"color": "#000", "fontSize": "16px"})</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Content</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Content Modals -->
    @foreach($contents as $content)
        <div class="modal fade" id="editContentModal{{ $content->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('contents.update', $content) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Content</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="key{{ $content->id }}" class="form-label">Key <span class="text-danger">*</span></label>
                                <input type="text" name="key" id="key{{ $content->id }}" class="form-control" value="{{ $content->key }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="block_index{{ $content->id }}" class="form-label">Block Index <span class="text-danger">*</span></label>
                                <input type="number" name="block_index" id="block_index{{ $content->id }}" class="form-control" value="{{ $content->block_index }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="value{{ $content->id }}" class="form-label">Value</label>
                                <textarea name="value" id="value{{ $content->id }}" class="form-control" rows="4">{{ $content->value }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="style{{ $content->id }}" class="form-label">Style (JSON)</label>
                                <textarea name="style" id="style{{ $content->id }}" class="form-control" rows="3">{{ $content->style ? json_encode($content->style, JSON_PRETTY_PRINT) : '' }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Content</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

