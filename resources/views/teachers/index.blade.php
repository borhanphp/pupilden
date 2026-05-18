@extends('layouts.app')

@section('title', 'Teachers')

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i> Teachers
                </h4>
                <a href="{{ route('teachers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add Teacher
                </a>
            </div>

            <div class="card-body">
                {{-- Search --}}
                <form method="GET" class="mb-4">
                    <div class="input-group" style="max-width:360px">
                        <input type="text" name="search" class="form-control" placeholder="Search name or email…" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Teacher</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Courses</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($teacher->profile_image)
                                            <img src="{{ \Storage::disk('r2')->url($teacher->profile_image) }}"
                                                 class="rounded-circle" width="36" height="36" style="object-fit:cover;">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                                                 style="width:36px;height:36px;font-size:14px;flex-shrink:0;">
                                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $teacher->name }}</div>
                                            @if($teacher->bio)
                                                <small class="text-muted">{{ Str::limit($teacher->bio, 40) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $teacher->email }}</td>
                                <td>
                                    @php $role = $teacher->organizations->first()?->pivot->role ?? 'contributor'; @endphp
                                    <span class="badge {{ $role === 'lead' ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ ucfirst($role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $teacher->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $teacher->courses()->count() }}</td>
                                <td class="text-end">
                                    <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Remove this teacher from your organization?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-chalkboard-teacher fa-2x mb-3 d-block opacity-25"></i>
                                    No teachers found. <a href="{{ route('teachers.create') }}">Add one now</a>.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $teachers->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
