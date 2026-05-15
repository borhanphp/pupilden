@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-graduate"></i> Students
                        </h4>
                        <a href="{{ route('students.export', request()->only('search', 'organization_id')) }}"
                           class="btn btn-success">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
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

                    <!-- Filter and Search -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <select name="organization_id" class="form-select me-2">
                                    <option value="">All Organizations</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" {{ request('organization_id') == $organization->id ? 'selected' : '' }}>
                                            {{ $organization->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="hidden" name="organization_id" value="{{ request('organization_id') }}">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="Search students..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Organization</th>
                                    <th>Courses</th>
                                    <th>Payments</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>
                                            @if($student->profile_picture)
                                                <img src="{{ asset('storage/' . $student->profile_picture) }}" 
                                                     alt="{{ $student->name }}" 
                                                     class="img-thumbnail rounded-circle" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded-circle" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $student->name }}</strong>
                                            </div>
                                            @if($student->contact_number)
                                                <small class="text-muted">{{ $student->contact_number }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-primary">{{ $student->email }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $student->username }}</span>
                                        </td>
                                        <td>
                                            @if($student->organization)
                                                <span class="badge bg-primary">{{ $student->organization->name }}</span>
                                            @else
                                                <span class="text-muted">No organization</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $student->courses->count() }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="badge bg-success">{{ $student->coursePurchases->where('payment_status', 'completed')->count() }} Completed</span>
                                                <span class="badge bg-warning text-dark">{{ $student->coursePurchases->where('payment_status', 'pending')->count() }} Pending</span>
                                            </div>
                                        </td>
                                        <td>
                                            <form action="{{ route('students.toggle-status', $student) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $student->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                    {{ $student->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('students.payments', $student) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-credit-card"></i> Payments
                                                </a>
                                                <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this student?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-user-graduate fa-3x mb-3"></i>
                                                <p>No students found.</p>
                                                {{-- <a href="{{ route('students.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add your first student
                                                </a> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($students->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $students->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
