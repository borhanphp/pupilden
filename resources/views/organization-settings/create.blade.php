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

                        {{-- Branding & Content --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Branding & Design</h5>

                                <div class="mb-3">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input type="file" name="logo" id="logo"
                                           class="form-control @error('logo') is-invalid @enderror"
                                           accept="image/*" onchange="previewImage(this, 'logo-preview')">
                                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div id="logo-preview" class="mt-2"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="favicon" class="form-label">Favicon</label>
                                    <input type="file" name="favicon" id="favicon"
                                           class="form-control @error('favicon') is-invalid @enderror"
                                           accept="image/*" onchange="previewImage(this, 'favicon-preview')">
                                    @error('favicon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div id="favicon-preview" class="mt-2"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="template" class="form-label">Template</label>
                                    <input type="text" name="template" id="template"
                                           value="{{ old('template') }}"
                                           class="form-control @error('template') is-invalid @enderror"
                                           placeholder="default">
                                    @error('template')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">Primary Color</label>
                                    <input type="color" name="primary_color" id="primary_color"
                                           value="{{ old('primary_color', '#007bff') }}"
                                           class="form-control form-control-color @error('primary_color') is-invalid @enderror">
                                    @error('primary_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="footer_color" class="form-label">Footer Color</label>
                                    <input type="color" name="footer_color" id="footer_color"
                                           value="{{ old('footer_color', '#343a40') }}"
                                           class="form-control form-control-color @error('footer_color') is-invalid @enderror">
                                    @error('footer_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="footer_design" class="form-label">Footer Design</label>
                                    <input type="text" name="footer_design" id="footer_design"
                                           value="{{ old('footer_design') }}"
                                           class="form-control @error('footer_design') is-invalid @enderror"
                                           placeholder="simple, modern, etc.">
                                    @error('footer_design')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3">Content & Information</h5>

                                <div class="mb-3">
                                    <label for="banner" class="form-label">Banner</label>
                                    <input type="file" name="banner" id="banner"
                                           class="form-control @error('banner') is-invalid @enderror"
                                           accept="image/*" onchange="previewImage(this, 'banner-preview')">
                                    @error('banner')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div id="banner-preview" class="mt-2"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="hero_text" class="form-label">Hero Text</label>
                                    <textarea name="hero_text" id="hero_text" rows="3"
                                              class="form-control @error('hero_text') is-invalid @enderror"
                                              placeholder="Welcome to our platform...">{{ old('hero_text') }}</textarea>
                                    @error('hero_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="about_us_content" class="form-label">About Us Content</label>
                                    <textarea name="about_us_content" id="about_us_content" rows="4"
                                              class="form-control @error('about_us_content') is-invalid @enderror"
                                              placeholder="About our organization...">{{ old('about_us_content') }}</textarea>
                                    @error('about_us_content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="copyright_text" class="form-label">Copyright Text</label>
                                    <input type="text" name="copyright_text" id="copyright_text"
                                           value="{{ old('copyright_text') }}"
                                           class="form-control @error('copyright_text') is-invalid @enderror"
                                           placeholder="© 2025 Organization Name. All rights reserved.">
                                    @error('copyright_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="business_email" class="form-label">Business Email</label>
                                    <input type="email" name="business_email" id="business_email"
                                           value="{{ old('business_email') }}"
                                           class="form-control @error('business_email') is-invalid @enderror"
                                           placeholder="contact@organization.com">
                                    @error('business_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Payment Gateway --}}
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5 class="mb-3">Payment Gateway Numbers</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="baksh_number" class="form-label">Bkash Number</label>
                                            <input type="text" name="baksh_number" id="baksh_number"
                                                   value="{{ old('baksh_number') }}"
                                                   class="form-control @error('baksh_number') is-invalid @enderror"
                                                   placeholder="01XXXXXXXXX">
                                            @error('baksh_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="ngad_number" class="form-label">Nagad Number</label>
                                            <input type="text" name="ngad_number" id="ngad_number"
                                                   value="{{ old('ngad_number') }}"
                                                   class="form-control @error('ngad_number') is-invalid @enderror"
                                                   placeholder="01XXXXXXXXX">
                                            @error('ngad_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="rocket_number" class="form-label">Rocket Number</label>
                                            <input type="text" name="rocket_number" id="rocket_number"
                                                   value="{{ old('rocket_number') }}"
                                                   class="form-control @error('rocket_number') is-invalid @enderror"
                                                   placeholder="01XXXXXXXXX">
                                            @error('rocket_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="celfin_number" class="form-label">Cellfin Number</label>
                                            <input type="text" name="celfin_number" id="celfin_number"
                                                   value="{{ old('celfin_number') }}"
                                                   class="form-control @error('celfin_number') is-invalid @enderror"
                                                   placeholder="01XXXXXXXXX">
                                            @error('celfin_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Currency --}}
                        <div class="row mt-4">
                            <div class="col-md-12"><h5 class="mb-3">Currency</h5></div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="currency_symbol" class="form-label">Currency Symbol</label>
                                    <input type="text" name="currency_symbol" id="currency_symbol"
                                           value="{{ old('currency_symbol', 'Tk') }}"
                                           class="form-control @error('currency_symbol') is-invalid @enderror"
                                           placeholder="Tk" maxlength="10">
                                    <small class="text-muted">e.g. Tk, ৳, $, £, €</small>
                                    @error('currency_symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- SEO / Meta Tags --}}
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="mb-1">SEO & Meta Tags</h5>
                                <p class="text-muted small mb-3">Used by search engines and social media previews.</p>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Title Tag <span class="text-muted small">(max 60 chars)</span></label>
                                    <input type="text" name="meta_title" id="meta_title"
                                           value="{{ old('meta_title') }}"
                                           class="form-control @error('meta_title') is-invalid @enderror"
                                           placeholder="My Academy — Learn, Grow, Succeed"
                                           maxlength="255"
                                           oninput="updateCharCount(this,'meta_title_count',60)">
                                    <small class="text-muted"><span id="meta_title_count">0</span>/60</small>
                                    @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="og_image" class="form-label">OG / Social Share Image <span class="text-muted small">(1200×630)</span></label>
                                    <input type="file" name="og_image" id="og_image"
                                           class="form-control @error('og_image') is-invalid @enderror"
                                           accept="image/*" onchange="previewImage(this,'og-image-preview')">
                                    @error('og_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div id="og-image-preview" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description <span class="text-muted small">(max 160 chars)</span></label>
                                    <textarea name="meta_description" id="meta_description" rows="3"
                                              class="form-control @error('meta_description') is-invalid @enderror"
                                              placeholder="Access high-quality courses, earn certificates..."
                                              maxlength="500"
                                              oninput="updateCharCount(this,'meta_desc_count',160)">{{ old('meta_description') }}</textarea>
                                    <small class="text-muted"><span id="meta_desc_count">0</span>/160</small>
                                    @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords <span class="text-muted small">(comma-separated)</span></label>
                                    <input type="text" name="meta_keywords" id="meta_keywords"
                                           value="{{ old('meta_keywords') }}"
                                           class="form-control @error('meta_keywords') is-invalid @enderror"
                                           placeholder="LMS, online learning, courses, education">
                                    @error('meta_keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Site Identity & Contact --}}
                        <div class="row mt-4">
                            <div class="col-md-12"><h5 class="mb-3">Site Identity & Contact</h5></div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="site_name" class="form-label">Site Name</label>
                                    <input type="text" name="site_name" id="site_name"
                                           value="{{ old('site_name') }}"
                                           class="form-control @error('site_name') is-invalid @enderror"
                                           placeholder="My LMS Platform">
                                    @error('site_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" name="phone" id="phone"
                                           value="{{ old('phone') }}"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="+880 1700-000000">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" name="address" id="address"
                                           value="{{ old('address') }}"
                                           class="form-control @error('address') is-invalid @enderror"
                                           placeholder="123 Main Street, Dhaka">
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Social Media Links --}}
                        <div class="row mt-2">
                            <div class="col-md-12"><h5 class="mb-3">Social Media Links</h5></div>
                            @foreach([
                                ['facebook_url','fab fa-facebook text-primary','Facebook','https://facebook.com/yourpage'],
                                ['twitter_url','fab fa-twitter text-info','Twitter / X','https://twitter.com/yourhandle'],
                                ['instagram_url','fab fa-instagram text-danger','Instagram','https://instagram.com/yourprofile'],
                                ['linkedin_url','fab fa-linkedin text-primary','LinkedIn','https://linkedin.com/company/yourcompany'],
                                ['youtube_url','fab fa-youtube text-danger','YouTube','https://youtube.com/@yourchannel'],
                                ['tiktok_url','fab fa-tiktok','TikTok','https://tiktok.com/@yourprofile'],
                                ['pinterest_url','fab fa-pinterest text-danger','Pinterest','https://pinterest.com/yourprofile'],
                            ] as [$name,$icon,$label,$placeholder])
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="{{ $name }}" class="form-label"><i class="{{ $icon }}"></i> {{ $label }} URL</label>
                                    <input type="url" name="{{ $name }}" id="{{ $name }}"
                                           value="{{ old($name) }}"
                                           class="form-control @error($name) is-invalid @enderror"
                                           placeholder="{{ $placeholder }}">
                                    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            @endforeach
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
                reader.onload = e => {
                    document.getElementById(previewId).innerHTML =
                        '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width:200px;max-height:200px;">';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function updateCharCount(el, counterId, limit) {
            const counter = document.getElementById(counterId);
            if (counter) {
                counter.textContent = el.value.length;
                counter.style.color = el.value.length > limit ? '#dc3545' : '';
            }
        }
    </script>
@endsection
