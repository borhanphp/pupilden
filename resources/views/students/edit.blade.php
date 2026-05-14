@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-edit"></i> Edit Student
                        </h4>
                        <div>
                            <a href="{{ route('students.show', $student) }}" class="btn btn-info me-2">
                                <i class="fas fa-eye"></i> View Student
                            </a>
                            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Students
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Left column --}}
                            <div class="col-md-8">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ old('name', $student->name) }}"
                                               class="form-control @error('name') is-invalid @enderror"
                                               placeholder="Enter full name" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                                        <input type="text" name="username" value="{{ old('username', $student->username) }}"
                                               class="form-control @error('username') is-invalid @enderror"
                                               placeholder="Enter username" required>
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" value="{{ old('email', $student->email) }}"
                                               class="form-control @error('email') is-invalid @enderror"
                                               placeholder="Enter email address" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Organization <span class="text-danger">*</span></label>
                                        <select name="organization_id" class="form-select @error('organization_id') is-invalid @enderror" required>
                                            <option value="">-- Select Organization --</option>
                                            @foreach($organizations as $org)
                                                <option value="{{ $org->id }}" {{ old('organization_id', $student->organization_id) == $org->id ? 'selected' : '' }}>
                                                    {{ $org->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('organization_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">New Password</label>
                                        <input type="password" name="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               placeholder="Leave blank to keep current password"
                                               autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Confirm New Password</label>
                                        <input type="password" name="password_confirmation"
                                               class="form-control"
                                               placeholder="Confirm new password"
                                               autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Contact Number</label>
                                        <input type="text" name="contact_number" value="{{ old('contact_number', $student->contact_number) }}"
                                               class="form-control @error('contact_number') is-invalid @enderror"
                                               placeholder="e.g. +8801XXXXXXXXX">
                                        @error('contact_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Alt Contact Number</label>
                                        <input type="text" name="alt_contact_number" value="{{ old('alt_contact_number', $student->alt_contact_number) }}"
                                               class="form-control @error('alt_contact_number') is-invalid @enderror"
                                               placeholder="e.g. +8801XXXXXXXXX">
                                        @error('alt_contact_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Bio</label>
                                    <textarea name="bio" rows="4"
                                              class="form-control @error('bio') is-invalid @enderror"
                                              placeholder="Short bio about the student...">{{ old('bio', $student->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="1" {{ old('is_active', $student->is_active) ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ !old('is_active', $student->is_active) ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Right column: profile picture --}}
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Profile Picture</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($student->profile_picture)
                                            <img id="profilePreview"
                                                 src="{{ \Storage::disk('r2')->url($student->organization_id . '/profile_pictures/' . $student->profile_picture) }}"
                                                 alt="{{ $student->name }}"
                                                 class="img-fluid rounded mb-3"
                                                 style="max-height: 200px; object-fit: cover;">
                                        @else
                                            <div id="profilePreviewPlaceholder"
                                                 class="bg-light d-flex align-items-center justify-content-center rounded mb-3"
                                                 style="height: 200px;">
                                                <div class="text-muted">
                                                    <i class="fas fa-user fa-3x mb-2"></i>
                                                    <p class="mb-0">No picture</p>
                                                </div>
                                            </div>
                                            <img id="profilePreview" src="#" alt="Preview"
                                                 class="img-fluid rounded mb-3 d-none"
                                                 style="max-height: 200px; object-fit: cover;">
                                        @endif

                                        <div class="mb-2">
                                            <input type="file" name="profile_picture" id="profilePictureInput"
                                                   class="form-control @error('profile_picture') is-invalid @enderror"
                                                   accept="image/jpeg,image/png,image/jpg,image/gif">
                                            @error('profile_picture')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">JPEG, PNG, GIF · Max 2 MB</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('profilePictureInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (ev) {
            const preview = document.getElementById('profilePreview');
            const placeholder = document.getElementById('profilePreviewPlaceholder');
            if (preview) {
                preview.src = ev.target.result;
                preview.classList.remove('d-none');
            }
            if (placeholder) {
                placeholder.classList.add('d-none');
            }
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
