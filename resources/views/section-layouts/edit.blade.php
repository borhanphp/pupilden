@extends('layouts.app')

@section('title', 'Edit Section Layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Section Layout</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('section-layouts.update', $sectionLayout) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Layout Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $sectionLayout->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="3-Column Grid"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="slug" 
                                   id="slug" 
                                   value="{{ old('slug', $sectionLayout->slug) }}"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="grid-3-col"
                                   required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">URL-friendly version of the layout name (e.g., grid-3-col)</div>
                        </div>

                        <div class="mb-3">
                            <label for="layout_config" class="form-label">Layout Config (JSON) <span class="text-danger">*</span></label>
                            <textarea name="layout_config" 
                                      id="layout_config" 
                                      rows="10"
                                      class="form-control @error('layout_config') is-invalid @enderror"
                                      placeholder='{"grid": "grid-cols-3", "gap": "gap-4", "responsive": {"md": "md:grid-cols-2", "lg": "lg:grid-cols-3"}}'
                                      required>{{ old('layout_config', $sectionLayout->layout_config ? json_encode($sectionLayout->layout_config, JSON_PRETTY_PRINT) : '') }}</textarea>
                            @error('layout_config')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Enter layout configuration as JSON. Example: {"grid": "grid-cols-3", "gap": "gap-4", "responsive": {"md": "md:grid-cols-2"}}
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('section-layouts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Layout
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validate JSON on blur
        document.getElementById('layout_config').addEventListener('blur', function() {
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

