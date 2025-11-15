@extends('layouts.app')

@section('title', 'Edit SEO Setting')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit SEO Setting</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('seo-settings.update', $seoSetting) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="page_id" class="form-label">Page <span class="text-danger">*</span></label>
                            <select name="page_id" 
                                    id="page_id" 
                                    class="form-select @error('page_id') is-invalid @enderror"
                                    required>
                                <option value="">Select a page</option>
                                @foreach($pages as $page)
                                    <option value="{{ $page->id }}" 
                                            {{ old('page_id', $seoSetting->page_id) == $page->id ? 'selected' : '' }}>
                                        {{ $page->title }} ({{ $page->slug }})
                                    </option>
                                @endforeach
                            </select>
                            @error('page_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Each page can only have one SEO setting</div>
                        </div>

                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" 
                                   name="meta_title" 
                                   id="meta_title" 
                                   value="{{ old('meta_title', $seoSetting->meta_title) }}"
                                   class="form-control @error('meta_title') is-invalid @enderror"
                                   placeholder="Page Title for SEO"
                                   maxlength="255">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Recommended: 50-60 characters. <span id="title-count">0</span>/255</div>
                        </div>

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea name="meta_description" 
                                      id="meta_description" 
                                      rows="3"
                                      class="form-control @error('meta_description') is-invalid @enderror"
                                      placeholder="Brief description of the page for search engines"
                                      maxlength="500">{{ old('meta_description', $seoSetting->meta_description) }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Recommended: 150-160 characters. <span id="desc-count">0</span>/500</div>
                        </div>

                        <div class="mb-3">
                            <label for="keywords" class="form-label">Keywords</label>
                            <input type="text" 
                                   name="keywords" 
                                   id="keywords" 
                                   value="{{ old('keywords', $seoSetting->keywords) }}"
                                   class="form-control @error('keywords') is-invalid @enderror"
                                   placeholder="keyword1, keyword2, keyword3"
                                   maxlength="255">
                            @error('keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Comma-separated keywords for SEO</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('seo-settings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update SEO Setting
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Character counters
        document.getElementById('meta_title').addEventListener('input', function() {
            document.getElementById('title-count').textContent = this.value.length;
        });
        document.getElementById('meta_description').addEventListener('input', function() {
            document.getElementById('desc-count').textContent = this.value.length;
        });
        
        // Initialize counts
        document.getElementById('title-count').textContent = document.getElementById('meta_title').value.length;
        document.getElementById('desc-count').textContent = document.getElementById('meta_description').value.length;
    </script>
@endsection

