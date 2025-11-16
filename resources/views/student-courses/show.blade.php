@extends('layouts.app')

@section('title', 'Enrollment Request Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-graduate"></i> Enrollment Request Details
                        </h4>
                        <div>
                            <a href="{{ route('student-courses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Student Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Name</th>
                                    <td>{{ $courseStudent->student->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $courseStudent->student->email }}</td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <td>{{ $courseStudent->student->username }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <td>{{ $courseStudent->student->contact_number ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Course Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Course Name</th>
                                    <td>{{ $courseStudent->course->name }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>{{ $courseStudent->course->courseCategory->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Price</th>
                                    <td>${{ number_format($courseStudent->course->price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Duration</th>
                                    <td>{{ $courseStudent->course->duration ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">Request Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>
                                        @if($courseStudent->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($courseStudent->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Disapproved</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Requested At</th>
                                    <td>{{ $courseStudent->created_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                                @if($courseStudent->approved_at)
                                    <tr>
                                        <th>Approved At</th>
                                        <td>{{ $courseStudent->approved_at->format('F j, Y, g:i a') }}</td>
                                    </tr>
                                @endif
                                @if($courseStudent->disapproved_at)
                                    <tr>
                                        <th>Disapproved At</th>
                                        <td>{{ $courseStudent->disapproved_at->format('F j, Y, g:i a') }}</td>
                                    </tr>
                                @endif
                                @if($courseStudent->rejection_reason)
                                    <tr>
                                        <th>Rejection Reason</th>
                                        <td>{{ $courseStudent->rejection_reason }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('student-courses.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                @if($courseStudent->status == 'pending')
                                    <div>
                                        <form action="{{ route('student-courses.approve', $courseStudent) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this request?')">
                                                <i class="fas fa-check"></i> Approve Request
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#disapproveModal">
                                            <i class="fas fa-times"></i> Disapprove Request
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disapprove Modal -->
    @if($courseStudent->status == 'pending')
        <div class="modal fade" id="disapproveModal" tabindex="-1" aria-labelledby="disapproveModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('student-courses.disapprove', $courseStudent) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="disapproveModalLabel">Disapprove Enrollment Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to disapprove this enrollment request?</p>
                            <div class="mb-3">
                                <label for="rejection_reason" class="form-label">Rejection Reason (Optional)</label>
                                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" placeholder="Enter reason for rejection..."></textarea>
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
@endsection

