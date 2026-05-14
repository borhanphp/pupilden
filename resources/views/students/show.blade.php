@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-graduate"></i> Student Details
                        </h4>
                        <div>
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-primary me-2">
                                <i class="fas fa-edit"></i> Edit Student
                            </a>
                            <a href="{{ route('students.payments', $student) }}" class="btn btn-warning me-2">
                                <i class="fas fa-credit-card"></i> Payment History
                            </a>
                            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Students
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Full Name</label>
                                        <p class="form-control-plaintext">{{ $student->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Username</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-secondary">{{ $student->username }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email</label>
                                        <p class="form-control-plaintext">
                                            <span class="text-primary">{{ $student->email }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Organization</label>
                                        <p class="form-control-plaintext">
                                            @if($student->organization)
                                                <span class="badge bg-primary">{{ $student->organization->name }}</span>
                                            @else
                                                <span class="text-muted">No organization</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Contact Number</label>
                                        <p class="form-control-plaintext">
                                            {{ $student->contact_number ?: 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Alt Contact Number</label>
                                        <p class="form-control-plaintext">
                                            {{ $student->alt_contact_number ?: 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge {{ $student->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Member Since</label>
                                        <p class="form-control-plaintext">{{ $student->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Bio</label>
                                <div class="form-control-plaintext">
                                    @if($student->bio)
                                        {{ $student->bio }}
                                    @else
                                        <span class="text-muted">No bio provided</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Profile Picture</h6>
                                </div>
                                <div class="card-body text-center">
                                    @if($student->profile_picture)
                                        <img src="{{ \Storage::disk('r2')->url($student->organization_id . '/profile_pictures/' . $student->profile_picture) }}"
                                             alt="{{ $student->name }}"
                                             class="img-fluid rounded"
                                             style="max-height: 200px;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                             style="height: 200px;">
                                            <div class="text-muted">
                                                <i class="fas fa-user fa-3x mb-2"></i>
                                                <p>No profile picture</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Statistics -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h5 class="text-primary">{{ $student->courses?->count() }}</h5>
                                            <small class="text-muted">Courses</small>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="text-success">{{ $student->coursePurchases?->count() }}</h5>
                                            <small class="text-muted">Purchases</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h5 class="text-success">{{ $student->coursePurchases?->where('payment_status', 'completed')->count() }}</h5>
                                            <small class="text-muted">Completed</small>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="text-warning">{{ $student->coursePurchases?->where('payment_status', 'pending')->count() }}</h5>
                                            <small class="text-muted">Pending</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <h5 class="text-info">${{ number_format($student->coursePurchases?->where('payment_status', 'completed')->sum('final_price'), 2) }}</h5>
                                        <small class="text-muted">Total Spent</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enrolled Courses -->
                    @if($student->courses?->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>Enrolled Courses</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Course</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Enrolled Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($student->courses as $course)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $course->name }}</strong>
                                                            @if($course->is_featured)
                                                                <span class="badge bg-warning text-dark ms-1">Featured</span>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted">{{ Str::limit($course->description, 50) }}</small>
                                                    </td>
                                                    <td>
                                                        @if($course->courseCategory)
                                                            <span class="badge bg-primary">{{ $course->courseCategory->name }}</span>
                                                        @else
                                                            <span class="text-muted">No category</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($course->price > 0)
                                                            <span class="text-success fw-bold">${{ number_format($course->price, 2) }}</span>
                                                        @else
                                                            <span class="badge bg-success">Free</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $course->pivot?->created_at?->format('M d, Y') }}</small>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye"></i> View Course
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recent Payments -->
                    @if($student->coursePurchases->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Recent Payments</h5>
                                    <a href="{{ route('students.payments', $student) }}" class="btn btn-sm btn-outline-primary">
                                        View All Payments
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Course</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($student->coursePurchases->take(5) as $purchase)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $purchase->course->name }}</strong>
                                                        @if($purchase->coupon)
                                                            <br>
                                                            <small class="text-muted">Coupon: {{ $purchase->coupon->code }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold text-primary">${{ number_format($purchase->final_price, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $purchase->payment_status_badge_class }}">
                                                            {{ $purchase->payment_status_label }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $purchase->created_at->format('M d, Y') }}</small>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#updatePaymentModal{{ $purchase->id }}">
                                                            <i class="fas fa-edit"></i> Update
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
