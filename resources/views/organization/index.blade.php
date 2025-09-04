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
                    <form action="{{ route('organizations.update', $organization) }}" method="POST" enctype="multipart/form-data">
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
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleEditMode() {
            const inputs = document.querySelectorAll('input, textarea');
            const actionButtons = document.getElementById('actionButtons');
            
            inputs.forEach(input => {
                if (input.type !== 'hidden') {
                    input.readOnly = !input.readOnly;
                }
            });
            
            if (actionButtons.style.display === 'none' || actionButtons.style.display === '') {
                actionButtons.style.display = 'flex';
                document.querySelector('.btn-primary').innerHTML = '<i class="fas fa-eye"></i> View Only';
            } else {
                actionButtons.style.display = 'none';
                document.querySelector('.btn-primary').innerHTML = '<i class="fas fa-edit"></i> Edit Profile';
            }
        }

        function cancelEdit() {
            const inputs = document.querySelectorAll('input, textarea');
            const actionButtons = document.getElementById('actionButtons');
            
            inputs.forEach(input => {
                if (input.type !== 'hidden') {
                    input.readOnly = true;
                }
            });
            
            actionButtons.style.display = 'none';
            document.querySelector('.btn-primary').innerHTML = '<i class="fas fa-edit"></i> Edit Profile';
        }
    </script>
@endsection
