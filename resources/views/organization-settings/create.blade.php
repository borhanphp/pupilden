@extends('layouts.app')

@section('title', 'Create Organization Settings')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create Organization Settings</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('organization-settings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Branding & Design</h5>

                                <div class="mb-3">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input type="file" 
                                           name="logo" 
                                           id="logo" 
                                           class="form-control @error('logo') is-invalid @enderror"
                                           accept="image/*"
                                           onchange="previewImage(this, 'logo-preview')">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="logo-preview" class="mt-2"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="favicon" class="form-label">Favicon</label>
                                    <input type="file" 
                                           name="favicon" 
                                           id="favicon" 
                                           class="form-control @error('favicon') is-invalid @enderror"
                                           accept="image/*"
                                           onchange="previewImage(this, 'favicon-preview')">
                                    @error('favicon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="favicon-preview" class="mt-2"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="template" class="form-label">Template</label>
                                    <input type="text" 
                                           name="template" 
                                           id="template" 
                                           value="{{ old('template') }}"
                                           class="form-control @error('template') is-invalid @enderror"
                                           placeholder="default">
                                    @error('template')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">Primary Color</label>
                                    <input type="color" 
                                           name="primary_color" 
                                           id="primary_color" 
                                           value="{{ old('primary_color', '#007bff') }}"
                                           class="form-control form-control-color @error('primary_color') is-invalid @enderror">
                                    @error('primary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="footer_color" class="form-label">Footer Color</label>
                                    <input type="color" 
                                           name="footer_color" 
                                           id="footer_color" 
                                           value="{{ old('footer_color', '#343a40') }}"
                                           class="form-control form-control-color @error('footer_color') is-invalid @enderror">
                                    @error('footer_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="footer_design" class="form-label">Footer Design</label>
                                    <input type="text" 
                                           name="footer_design" 
                                           id="footer_design" 
                                           value="{{ old('footer_design') }}"
                                           class="form-control @error('footer_design') is-invalid @enderror"
                                           placeholder="simple, modern, etc.">
                                    @error('footer_design')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3">Content & Information</h5>

                                <div class="mb-3">
                                    <label for="banner" class="form-label">Banner</label>
                                    <input type="file" 
                                           name="banner" 
                                           id="banner" 
                                           class="form-control @error('banner') is-invalid @enderror"
                                           accept="image/*"
                                           onchange="previewImage(this, 'banner-preview')">
                                    @error('banner')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="banner-preview" class="mt-2"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="hero_text" class="form-label">Hero Text</label>
                                    <textarea name="hero_text" 
                                              id="hero_text" 
                                              rows="3"
                                              class="form-control @error('hero_text') is-invalid @enderror"
                                              placeholder="Welcome to our platform...">{{ old('hero_text') }}</textarea>
                                    @error('hero_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="about_us_content" class="form-label">About Us Content</label>
                                    <textarea name="about_us_content" 
                                              id="about_us_content" 
                                              rows="5"
                                              class="form-control @error('about_us_content') is-invalid @enderror"
                                              placeholder="About our organization...">{{ old('about_us_content') }}</textarea>
                                    @error('about_us_content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="privacy_policy_content" class="form-label">Privacy Policy Content</label>
                                    <textarea name="privacy_policy_content" 
                                              id="privacy_policy_content" 
                                              rows="5"
                                              class="form-control @error('privacy_policy_content') is-invalid @enderror"
                                              placeholder="Privacy policy text...">{{ old('privacy_policy_content') }}</textarea>
                                    @error('privacy_policy_content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="copyright_text" class="form-label">Copyright Text</label>
                                    <input type="text" 
                                           name="copyright_text" 
                                           id="copyright_text" 
                                           value="{{ old('copyright_text') }}"
                                           class="form-control @error('copyright_text') is-invalid @enderror"
                                           placeholder="© 2024 Organization Name. All rights reserved.">
                                    @error('copyright_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="business_email" class="form-label">Business Email</label>
                                    <input type="email" 
                                           name="business_email" 
                                           id="business_email" 
                                           value="{{ old('business_email') }}"
                                           class="form-control @error('business_email') is-invalid @enderror"
                                           placeholder="contact@organization.com">
                                    @error('business_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5 class="mb-3">Payment Gateway Numbers</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="baksh_number" class="form-label">Baksh Number</label>
                                            <input type="text" 
                                                   name="baksh_number" 
                                                   id="baksh_number" 
                                                   value="{{ old('baksh_number') }}"
                                                   class="form-control @error('baksh_number') is-invalid @enderror"
                                                   placeholder="01XXXXXXXXX">
                                            @error('baksh_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="ngad_number" class="form-label">Nagad Number</label>
                                            <input type="text" 
                                                   name="ngad_number" 
                                                   id="ngad_number" 
                                                   value="{{ old('ngad_number') }}"
                                                   class="form-control @error('ngad_number') is-invalid @enderror"
                                                   placeholder="01XXXXXXXXX">
                                            @error('ngad_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="rocket_number" class="form-label">Rocket Number</label>
                                            <input type="text" 
                                                   name="rocket_number" 
                                                   id="rocket_number" 
                                                   value="{{ old('rocket_number') }}"
                                                   class="form-control @error('rocket_number') is-invalid @enderror"
                                                   placeholder="01XXXXXXXXX">
                                            @error('rocket_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="celfin_number" class="form-label">Celfin Number</label>
                                            <input type="text" 
                                                   name="celfin_number" 
                                                   id="celfin_number" 
                                                   value="{{ old('celfin_number') }}"
                                                   class="form-control @error('celfin_number') is-invalid @enderror"
                                                   placeholder="01XXXXXXXXX">
                                            @error('celfin_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('organization-settings.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Settings
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
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection

