@extends('layouts.app')

@section('title', 'Section Content Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-edit"></i> Section Content Details
                        </h4>
                        <div>
                            <a href="{{ route('section-contents.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('section-contents.edit', $sectionContent) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">ID</th>
                                    <td>{{ $sectionContent->id }}</td>
                                </tr>
                                <tr>
                                    <th>Section</th>
                                    <td>
                                        <strong>{{ $sectionContent->section->page->title ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $sectionContent->section->title ?? 'Section ' . $sectionContent->section_id }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Block Index</th>
                                    <td>
                                        <span class="badge bg-secondary">{{ $sectionContent->block_index }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Key</th>
                                    <td><code>{{ $sectionContent->key }}</code></td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $sectionContent->created_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $sectionContent->updated_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Content Value</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <pre style="max-height: 200px; overflow-y: auto; white-space: pre-wrap;">{{ $sectionContent->value ?? '(empty)' }}</pre>
                                </div>
                            </div>

                            <h5 class="mb-3 mt-4">Style Configuration</h5>
                            @if($sectionContent->style)
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <pre class="mb-0" style="max-height: 200px; overflow-y: auto;">{{ json_encode($sectionContent->style, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No style configuration set.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('section-contents.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <div>
                                    <a href="{{ route('section-contents.edit', $sectionContent) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('section-contents.destroy', $sectionContent) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this content?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

