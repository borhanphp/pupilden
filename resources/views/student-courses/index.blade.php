@extends('layouts.app')

@section('title', 'Course Enrollment Requests')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-graduate"></i> Course Enrollment Requests
                        </h4>
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

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" class="d-flex gap-2">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="disapproved" {{ request('status') == 'disapproved' ? 'selected' : '' }}>Disapproved</option>
                                </select>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="GET" class="d-flex gap-2">
                                <select name="course_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="GET" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Search by student name, email..." value="{{ request('search') }}">
                                @if(request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                                @if(request('course_id'))
                                    <input type="hidden" name="course_id" value="{{ request('course_id') }}">
                                @endif
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search') || request('status') || request('course_id'))
                                    <a href="{{ route('student-courses.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Requested At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $request->student->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $request->student->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $request->course->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $request->course->courseCategory->name ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($request->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($request->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Disapproved</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ Carbon\Carbon::parse($request->created_at)->format('M j, Y g:i A') }}</small>
                                        </td>
                                       
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('student-courses.show', $request) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                @if($request->status == 'pending')
                                                    <form action="{{ route('student-courses.approve', $request) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Are you sure you want to approve this request?')">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#disapproveModal{{ $request->id }}">
                                                        <i class="fas fa-times"></i> Disapprove
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Disapprove Modal -->
                                    @if($request->status == 'pending')
                                        <div class="modal fade" id="disapproveModal{{ $request->id }}" tabindex="-1" aria-labelledby="disapproveModalLabel{{ $request->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('student-courses.disapprove', $request) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="disapproveModalLabel{{ $request->id }}">Disapprove Enrollment Request</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to disapprove this enrollment request?</p>
                                                            <div class="mb-3">
                                                                <label for="rejection_reason{{ $request->id }}" class="form-label">Rejection Reason (Optional)</label>
                                                                <textarea name="rejection_reason" id="rejection_reason{{ $request->id }}" class="form-control" rows="3" placeholder="Enter reason for rejection..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Disapprove</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-user-graduate fa-3x mb-3"></i>
                                                <p>No enrollment requests found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

