@extends('layouts.app')

@section('title', isset($organizationTheme) ? 'Edit Organization Theme' : 'Add New Organization Theme')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($organizationTheme) ? 'Edit Organization Theme' : 'Add New Organization Theme' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($organizationTheme) ? route('organization-themes.update', $organizationTheme) : route('organization-themes.store') }}" method="POST">
                        @csrf
                        @if(isset($organizationTheme))
                            @method('PUT')
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="theme_id" class="form-label">Theme <span class="text-danger">*</span></label>
                                    <select name="theme_id" 
                                            id="theme_id" 
                                            class="form-select @error('theme_id') is-invalid @enderror"
                                            required>
                                        <option value="">Select a theme</option>
                                        @foreach($themes as $theme)
                                            <option value="{{ $theme->id }}" 
                                                    {{ old('theme_id', isset($organizationTheme) ? $organizationTheme->theme_id : '') == $theme->id ? 'selected' : '' }}>
                                                {{ $theme->name }} ({{ $theme->slug }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('theme_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="custom_settings" class="form-label">Custom Settings (JSON)</label>
                                    <textarea name="custom_settings" 
                                              id="custom_settings" 
                                              rows="10"
                                              class="form-control @error('custom_settings') is-invalid @enderror"
                                              placeholder='{"primary_color": "#007bff", "secondary_color": "#6c757d", "font_family": "Arial"}'>{!! old('custom_settings', isset($organizationTheme) && $organizationTheme->custom_settings ? json_encode($organizationTheme->custom_settings, JSON_PRETTY_PRINT) : '') !!}</textarea>
                                    @error('custom_settings')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Enter custom settings as JSON. Example: {"primary_color": "#007bff", "secondary_color": "#6c757d", "font_family": "Arial"}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Help</h6>
                                        <p class="card-text small">
                                            <strong>Theme:</strong> Select the theme you want to apply to your organization.
                                        </p>
                                        <p class="card-text small">
                                            <strong>Custom Settings:</strong> You can customize the theme appearance by providing JSON settings. This is optional.
                                        </p>
                                        <p class="card-text small text-muted">
                                            <strong>Note:</strong> Only one theme can be assigned per organization. If you already have a theme, you can update it instead of creating a new one.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('organization-themes.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> {{ isset($organizationTheme) ? 'Update' : 'Create' }} Organization Theme
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validate JSON on blur
        document.getElementById('custom_settings').addEventListener('blur', function() {
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

