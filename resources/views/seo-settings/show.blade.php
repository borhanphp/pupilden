@extends('layouts.app')

@section('title', 'SEO Setting Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-search"></i> SEO Setting Details
                        </h4>
                        <div>
                            <a href="{{ route('seo-settings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('seo-settings.edit', $seoSetting) }}" class="btn btn-primary">
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
                                    <td>{{ $seoSetting->id }}</td>
                                </tr>
                                <tr>
                                    <th>Page</th>
                                    <td>
                                        <strong>{{ $seoSetting->page->title ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $seoSetting->page->slug ?? '' }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $seoSetting->created_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $seoSetting->updated_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">SEO Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Meta Title</th>
                                    <td>{{ $seoSetting->meta_title ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Meta Description</th>
                                    <td>{{ $seoSetting->meta_description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Keywords</th>
                                    <td>{{ $seoSetting->keywords ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">Preview</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong class="text-primary" style="font-size: 18px;">
                                            {{ $seoSetting->meta_title ?? $seoSetting->page->title ?? 'Page Title' }}
                                        </strong>
                                    </div>
                                    <div class="text-muted mb-2" style="font-size: 14px;">
                                        {{ $seoSetting->page->slug ? url('/') . '/' . $seoSetting->page->slug : url('/') }}
                                    </div>
                                    <div class="text-secondary" style="font-size: 14px;">
                                        {{ $seoSetting->meta_description ?? 'No description available.' }}
                                    </div>
                                    @if($seoSetting->keywords)
                                        <div class="mt-2">
                                            <small class="text-muted">Keywords: {{ $seoSetting->keywords }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-text mt-2">
                                <i class="fas fa-info-circle"></i> This is how your page might appear in search engine results.
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('seo-settings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <div>
                                    <a href="{{ route('seo-settings.edit', $seoSetting) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('seo-settings.destroy', $seoSetting) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this SEO setting?')">
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

