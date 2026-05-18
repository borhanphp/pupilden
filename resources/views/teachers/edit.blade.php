@extends('layouts.app')

@section('title', 'Edit Teacher')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="fas fa-user-edit me-2"></i> Edit Teacher — {{ $teacher->name }}
                </h4>
                <a href="{{ route('teachers.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">Account Details</h6>

                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $teacher->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $teacher->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">New Password <span class="text-muted small">(leave blank to keep current)</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Min 8 characters">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Role in Organization</label>
                                <select name="role" class="form-select">
                                    <option value="contributor" {{ old('role', $pivot?->role) === 'contributor' ? 'selected' : '' }}>Contributor</option>
                                    <option value="lead" {{ old('role', $pivot?->role) === 'lead' ? 'selected' : '' }}>Lead Teacher</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Account Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                           {{ old('is_active', $teacher->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">Profile Info</h6>

                            <div class="mb-3">
                                <label class="form-label">Profile Photo</label>
                                @if($teacher->profile_image)
                                    <div class="mb-2">
                                        <img src="{{ \Storage::disk('r2')->url($teacher->profile_image) }}"
                                             class="rounded-circle" width="60" height="60" style="object-fit:cover;" id="current-avatar">
                                    </div>
                                @endif
                                <input type="file" name="profile_image" class="form-control"
                                       accept="image/*" onchange="previewAvatar(this)">
                                <div id="avatar-preview" class="mt-2"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                       value="{{ old('phone', $teacher->phone) }}" placeholder="+880…">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Website</label>
                                <input type="url" name="website" class="form-control"
                                       value="{{ old('website', $teacher->website) }}" placeholder="https://…">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="4">{{ old('bio', $teacher->bio) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Stats strip --}}
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="border rounded p-3 text-center">
                                <div class="fs-4 fw-bold text-primary">{{ $teacher->courses()->count() }}</div>
                                <div class="small text-muted">Courses</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3 text-center">
                                <div class="fs-4 fw-bold text-success">{{ $teacher->organizations()->count() }}</div>
                                <div class="small text-muted">Organizations</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3 text-center">
                                <div class="fs-4 fw-bold text-secondary">{{ $teacher->created_at?->format('M Y') }}</div>
                                <div class="small text-muted">Joined</div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <form action="{{ route('teachers.destroy', $teacher) }}" method="POST"
                              onsubmit="return confirm('Remove this teacher from your organization?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-user-minus me-1"></i> Remove from Org
                            </button>
                        </form>
                        <div>
                            <a href="{{ route('teachers.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatar-preview').innerHTML =
                `<img src="${e.target.result}" class="rounded-circle" width="80" height="80" style="object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
