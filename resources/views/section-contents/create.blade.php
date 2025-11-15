@extends('layouts.app')

@section('title', 'Create Section Content')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create New Section Content</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('section-contents.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                            <select name="section_id" 
                                    id="section_id" 
                                    class="form-select @error('section_id') is-invalid @enderror"
                                    required>
                                <option value="">Select a section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" 
                                            {{ old('section_id', $sectionId) == $section->id ? 'selected' : '' }}>
                                        {{ $section->page->title ?? 'N/A' }} - {{ $section->title ?? 'Section ' . $section->id }} ({{ $section->section_type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="key" class="form-label">Key <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="key" 
                                   id="key" 
                                   value="{{ old('key') }}"
                                   class="form-control @error('key') is-invalid @enderror"
                                   placeholder="heading, text, button_text, etc."
                                   required>
                            @error('key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Identifier for this content block (e.g., heading, description, button_text)</div>
                        </div>

                        <div class="mb-3">
                            <label for="block_index" class="form-label">Block Index <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="block_index" 
                                   id="block_index" 
                                   value="{{ old('block_index', 0) }}"
                                   class="form-control @error('block_index') is-invalid @enderror"
                                   min="0"
                                   required>
                            @error('block_index')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Position of this content in the grid (0, 1, 2, etc.)</div>
                        </div>

                        <div class="mb-3">
                            <label for="value" class="form-label">Value</label>
                            <textarea name="value" 
                                      id="value" 
                                      rows="4"
                                      class="form-control @error('value') is-invalid @enderror"
                                      placeholder="Content text, HTML, or data">{{ old('value') }}</textarea>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="style" class="form-label">Style (JSON)</label>
                            <textarea name="style" 
                                      id="style" 
                                      rows="3"
                                      class="form-control @error('style') is-invalid @enderror"
                                      placeholder='{"color": "#000", "fontSize": "16px"}'>{{ old('style') }}</textarea>
                            @error('style')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional: JSON object for styling (e.g., {"color": "#000", "fontSize": "16px"})</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('section-contents.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Content
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validate JSON on blur
        document.getElementById('style').addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                try {
                    JSON.parse(value);
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } catch (e) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    </script>
@endsection

