@extends('layouts.app')

@section('title', 'Organization Profile')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-building"></i> Organization Profile
                        </h4>
                        <button class="btn btn-primary" onclick="toggleEditMode()">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="OrganizationProfileForm" action="{{ route('organizations.update', $organization) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Organization Name</label>
                                            <input type="text" 
                                                   name="name" 
                                                   id="name" 
                                                   value="{{ old('name', $organization->name) }}"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   readonly>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="slug" class="form-label">Organization Slug</label>
                                            <input type="text" 
                                                   name="slug" 
                                                   id="slug" 
                                                   value="{{ old('slug', $organization->slug) }}"
                                                   class="form-control @error('slug') is-invalid @enderror"
                                                   readonly>
                                            @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="custom_domain" class="form-label">Custom Domain</label>
                                            <input type="text" 
                                                   name="custom_domain" 
                                                   id="custom_domain" 
                                                   value="{{ old('custom_domain', $organization->custom_domain) }}"
                                                   class="form-control @error('custom_domain') is-invalid @enderror"
                                                   placeholder="example.com"
                                                   readonly>
                                            @error('custom_domain')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea name="address" 
                                                      id="address" 
                                                      class="form-control @error('address') is-invalid @enderror"
                                                      rows="3"
                                                      readonly>{{ old('address', $organization->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Contact Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" 
                                                   name="phone" 
                                                   id="phone" 
                                                   value="{{ old('phone', $organization->phone) }}"
                                                   class="form-control @error('phone') is-invalid @enderror"
                                                   placeholder="+1234567890"
                                                   readonly>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" 
                                                   name="email" 
                                                   id="email" 
                                                   value="{{ old('email', $organization->email) }}"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   placeholder="contact@example.com"
                                                   readonly>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="url" 
                                                   name="website" 
                                                   id="website" 
                                                   value="{{ old('website', $organization->website) }}"
                                                   class="form-control @error('website') is-invalid @enderror"
                                                   placeholder="https://example.com"
                                                   readonly>
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Links -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Social Media Links</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="facebook" class="form-label">
                                                        <i class="fab fa-facebook text-primary"></i> Facebook
                                                    </label>
                                                    <input type="url" 
                                                           name="facebook" 
                                                           id="facebook" 
                                                           value="{{ old('facebook', $organization->facebook) }}"
                                                           class="form-control @error('facebook') is-invalid @enderror"
                                                           placeholder="https://facebook.com/yourpage"
                                                           readonly>
                                                    @error('facebook')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="twitter" class="form-label">
                                                        <i class="fab fa-twitter text-info"></i> Twitter
                                                    </label>
                                                    <input type="url" 
                                                           name="twitter" 
                                                           id="twitter" 
                                                           value="{{ old('twitter', $organization->twitter) }}"
                                                           class="form-control @error('twitter') is-invalid @enderror"
                                                           placeholder="https://twitter.com/yourhandle"
                                                           readonly>
                                                    @error('twitter')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="instagram" class="form-label">
                                                        <i class="fab fa-instagram text-danger"></i> Instagram
                                                    </label>
                                                    <input type="url" 
                                                           name="instagram" 
                                                           id="instagram" 
                                                           value="{{ old('instagram', $organization->instagram) }}"
                                                           class="form-control @error('instagram') is-invalid @enderror"
                                                           placeholder="https://instagram.com/yourhandle"
                                                           readonly>
                                                    @error('instagram')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="linkedin" class="form-label">
                                                        <i class="fab fa-linkedin text-primary"></i> LinkedIn
                                                    </label>
                                                    <input type="url" 
                                                           name="linkedin" 
                                                           id="linkedin" 
                                                           value="{{ old('linkedin', $organization->linkedin) }}"
                                                           class="form-control @error('linkedin') is-invalid @enderror"
                                                           placeholder="https://linkedin.com/company/yourcompany"
                                                           readonly>
                                                    @error('linkedin')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="youtube" class="form-label">
                                                        <i class="fab fa-youtube text-danger"></i> YouTube
                                                    </label>
                                                    <input type="url" 
                                                           name="youtube" 
                                                           id="youtube" 
                                                           value="{{ old('youtube', $organization->youtube) }}"
                                                           class="form-control @error('youtube') is-invalid @enderror"
                                                           placeholder="https://youtube.com/yourchannel"
                                                           readonly>
                                                    @error('youtube')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="tiktok" class="form-label">
                                                        <i class="fab fa-tiktok text-dark"></i> TikTok
                                                    </label>
                                                    <input type="url" 
                                                           name="tiktok" 
                                                           id="tiktok" 
                                                           value="{{ old('tiktok', $organization->tiktok) }}"
                                                           class="form-control @error('tiktok') is-invalid @enderror"
                                                           placeholder="https://tiktok.com/@yourhandle"
                                                           readonly>
                                                    @error('tiktok')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="pinterest" class="form-label">
                                                        <i class="fab fa-pinterest text-danger"></i> Pinterest
                                                    </label>
                                                    <input type="url" 
                                                           name="pinterest" 
                                                           id="pinterest" 
                                                           value="{{ old('pinterest', $organization->pinterest) }}"
                                                           class="form-control @error('pinterest') is-invalid @enderror"
                                                           placeholder="https://pinterest.com/yourprofile"
                                                           readonly>
                                                    @error('pinterest')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Organization Status -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Organization Status</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h6>Status</h6>
                                                    @if($organization->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h6>Plan Type</h6>
                                                    <span class="badge bg-info">{{ $organization->plan_type ?? 'Free' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h6>Created</h6>
                                                    <small class="text-muted">{{ $organization->created_at->format('M j, Y') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h6>Last Updated</h6>
                                                    <small class="text-muted">{{ $organization->updated_at->format('M j, Y') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2" id="actionButtons" style="display: none;">
                                    <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Organization Settings Form -->
                    <form id="OrganizationSettingsForm" action="{{ route('organizations.settings.update') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-cog"></i> Organization Settings
                                    </h4>
                                    <button type="button" class="btn btn-primary" onclick="toggleSettingsEditMode()">
                                        <i class="fas fa-edit"></i> Edit Settings
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Branding & Design -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Branding & Design</h5>

                                        <div class="mb-3">
                                            <label for="logo" class="form-label">Logo</label>
                                            @if(isset($settings) && $settings->logo)
                                                <div class="mb-2">
                                                    <img src="{{ asset('uploads/' . $settings->logo) }}" alt="Logo" class="img-thumbnail" style="max-width: 200px;">
                                                </div>
                                            @endif
                                            <input type="file" 
                                                   name="logo" 
                                                   id="logo" 
                                                   class="form-control @error('logo') is-invalid @enderror"
                                                   accept="image/*"
                                                   onchange="previewImage(this, 'logo-preview')"
                                                   readonly>
                                            @error('logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="logo-preview" class="mt-2"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="favicon" class="form-label">Favicon</label>
                                            @if(isset($settings) && $settings->favicon)
                                                <div class="mb-2">
                                                    <img src="{{ asset('uploads/' . $settings->favicon) }}" alt="Favicon" class="img-thumbnail" style="max-width: 100px;">
                                                </div>
                                            @endif
                                            <input type="file" 
                                                   name="favicon" 
                                                   id="favicon" 
                                                   class="form-control @error('favicon') is-invalid @enderror"
                                                   accept="image/*"
                                                   onchange="previewImage(this, 'favicon-preview')"
                                                   readonly>
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
                                                   value="{{ old('template', $settings->template ?? '') }}"
                                                   class="form-control @error('template') is-invalid @enderror"
                                                   placeholder="default"
                                                   readonly>
                                            @error('template')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="primary_color" class="form-label">Primary Color</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="color" 
                                                       name="primary_color" 
                                                       id="primary_color" 
                                                       value="{{ old('primary_color', $settings->primary_color ?? '#007bff') }}"
                                                       class="form-control form-control-color @error('primary_color') is-invalid @enderror"
                                                       onchange="updateColorPreview('primary_color', 'primary_color_preview')"
                                                       readonly>
                                                <div id="primary_color_preview" 
                                                     class="color-preview-box" 
                                                     style="width: 60px; height: 38px; border: 1px solid #ddd; border-radius: 4px; background-color: {{ old('primary_color', $settings->primary_color ?? '#007bff') }}; cursor: pointer;"
                                                     onclick="document.getElementById('primary_color').click()"
                                                     title="Click to change color"></div>
                                                <span id="primary_color_value" class="text-muted small">{{ old('primary_color', $settings->primary_color ?? '#007bff') }}</span>
                                            </div>
                                            @error('primary_color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="footer_color" class="form-label">Footer Color</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="color" 
                                                       name="footer_color" 
                                                       id="footer_color" 
                                                       value="{{ old('footer_color', $settings->footer_color ?? '#343a40') }}"
                                                       class="form-control form-control-color @error('footer_color') is-invalid @enderror"
                                                       onchange="updateColorPreview('footer_color', 'footer_color_preview')"
                                                       readonly>
                                                <div id="footer_color_preview" 
                                                     class="color-preview-box" 
                                                     style="width: 60px; height: 38px; border: 1px solid #ddd; border-radius: 4px; background-color: {{ old('footer_color', $settings->footer_color ?? '#343a40') }}; cursor: pointer;"
                                                     onclick="document.getElementById('footer_color').click()"
                                                     title="Click to change color"></div>
                                                <span id="footer_color_value" class="text-muted small">{{ old('footer_color', $settings->footer_color ?? '#343a40') }}</span>
                                            </div>
                                            @error('footer_color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="footer_design" class="form-label">Footer Design</label>
                                            <input type="text" 
                                                   name="footer_design" 
                                                   id="footer_design" 
                                                   value="{{ old('footer_design', $settings->footer_design ?? '') }}"
                                                   class="form-control @error('footer_design') is-invalid @enderror"
                                                   placeholder="simple, modern, etc."
                                                   readonly>
                                            @error('footer_design')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Content & Information -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Content & Information</h5>

                                        <div class="mb-3">
                                            <label for="banner" class="form-label">Banner</label>
                                            @if(isset($settings) && $settings->banner)
                                                <div class="mb-2">
                                                    <img src="{{ asset('uploads/' . $settings->banner) }}" alt="Banner" class="img-thumbnail" style="max-width: 200px;">
                                                </div>
                                            @endif
                                            <input type="file" 
                                                   name="banner" 
                                                   id="banner" 
                                                   class="form-control @error('banner') is-invalid @enderror"
                                                   accept="image/*"
                                                   onchange="previewImage(this, 'banner-preview')"
                                                   readonly>
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
                                                      placeholder="Welcome to our platform..."
                                                      readonly>{{ old('hero_text', $settings->hero_text ?? '') }}</textarea>
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
                                                      placeholder="About our organization..."
                                                      readonly>{{ old('about_us_content', $settings->about_us_content ?? '') }}</textarea>
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
                                                      placeholder="Privacy policy text..."
                                                      readonly>{{ old('privacy_policy_content', $settings->privacy_policy_content ?? '') }}</textarea>
                                            @error('privacy_policy_content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="copyright_text" class="form-label">Copyright Text</label>
                                            <input type="text" 
                                                   name="copyright_text" 
                                                   id="copyright_text" 
                                                   value="{{ old('copyright_text', $settings->copyright_text ?? '') }}"
                                                   class="form-control @error('copyright_text') is-invalid @enderror"
                                                   placeholder="© 2024 Organization Name. All rights reserved."
                                                   readonly>
                                            @error('copyright_text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="business_email" class="form-label">Business Email</label>
                                            <input type="email" 
                                                   name="business_email" 
                                                   id="business_email" 
                                                   value="{{ old('business_email', $settings->business_email ?? '') }}"
                                                   class="form-control @error('business_email') is-invalid @enderror"
                                                   placeholder="contact@organization.com"
                                                   readonly>
                                            @error('business_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Gateway Numbers -->
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
                                                           value="{{ old('baksh_number', $settings->baksh_number ?? '') }}"
                                                           class="form-control @error('baksh_number') is-invalid @enderror"
                                                           placeholder="01XXXXXXXXX"
                                                           readonly>
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
                                                           value="{{ old('ngad_number', $settings->ngad_number ?? '') }}"
                                                           class="form-control @error('ngad_number') is-invalid @enderror"
                                                           placeholder="01XXXXXXXXX"
                                                           readonly>
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
                                                           value="{{ old('rocket_number', $settings->rocket_number ?? '') }}"
                                                           class="form-control @error('rocket_number') is-invalid @enderror"
                                                           placeholder="01XXXXXXXXX"
                                                           readonly>
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
                                                           value="{{ old('celfin_number', $settings->celfin_number ?? '') }}"
                                                           class="form-control @error('celfin_number') is-invalid @enderror"
                                                           placeholder="01XXXXXXXXX"
                                                           readonly>
                                                    @error('celfin_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Settings Action Buttons -->
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-end gap-2" id="settingsActionButtons" style="display: none;">
                                            <button type="button" class="btn btn-secondary" onclick="cancelSettingsEdit()">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Save Settings
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleEditMode() {
            const form = document.querySelector('#OrganizationProfileForm');
            const inputs = form.querySelectorAll('input, textarea');
            const actionButtons = document.getElementById('actionButtons');
            
            inputs.forEach(input => {
                if (input.type !== 'hidden' && input.type !== 'file') {
                    input.readOnly = !input.readOnly;
                }
            });
            
            if (actionButtons.style.display === 'none' || actionButtons.style.display === '') {
                actionButtons.style.display = 'flex';
                form.querySelector('.btn-primary').innerHTML = '<i class="fas fa-eye"></i> View Only';
            } else {
                actionButtons.style.display = 'none';
                form.querySelector('.btn-primary').innerHTML = '<i class="fas fa-edit"></i> Edit Profile';
            }
        }

        function cancelEdit() {
            const form = document.querySelector('#OrganizationProfileForm');
            const inputs = form.querySelectorAll('input, textarea');
            const actionButtons = document.getElementById('actionButtons');
            
            inputs.forEach(input => {
                if (input.type !== 'hidden' && input.type !== 'file') {
                    input.readOnly = true;
                }
            });
            
            actionButtons.style.display = 'none';
            form.querySelector('.btn-primary').innerHTML = '<i class="fas fa-edit"></i> Edit Profile';
        }

        function toggleSettingsEditMode() {
            const form = document.querySelector('#OrganizationSettingsForm');
            const inputs = form.querySelectorAll('input, textarea');
            const actionButtons = document.getElementById('settingsActionButtons');
            
            inputs.forEach(input => {
                if (input.type !== 'hidden') {
                    input.readOnly = !input.readOnly;
                }
            });
            
            if (actionButtons.style.display === 'none' || actionButtons.style.display === '') {
                actionButtons.style.display = 'flex';
                form.querySelector('.btn-primary').innerHTML = '<i class="fas fa-eye"></i> View Only';
            } else {
                actionButtons.style.display = 'none';
                form.querySelector('.btn-primary').innerHTML = '<i class="fas fa-edit"></i> Edit Settings';
            }
        }

        function cancelSettingsEdit() {
            const form = document.querySelector('#OrganizationSettingsForm');
            const inputs = form.querySelectorAll('input, textarea');
            const actionButtons = document.getElementById('settingsActionButtons');
            
            inputs.forEach(input => {
                if (input.type !== 'hidden') {
                    input.readOnly = true;
                }
            });
            
            actionButtons.style.display = 'none';
            form.querySelector('.btn-primary').innerHTML = '<i class="fas fa-edit"></i> Edit Settings';
        }

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

        function updateColorPreview(colorInputId, previewBoxId) {
            const colorInput = document.getElementById(colorInputId);
            const previewBox = document.getElementById(previewBoxId);
            const valueSpan = document.getElementById(colorInputId + '_value');
            
            if (colorInput && previewBox) {
                const selectedColor = colorInput.value;
                previewBox.style.backgroundColor = selectedColor;
                if (valueSpan) {
                    valueSpan.textContent = selectedColor;
                }
            }
        }

        // Initialize color previews on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateColorPreview('primary_color', 'primary_color_preview');
            updateColorPreview('footer_color', 'footer_color_preview');
        });
    </script>
@endsection
