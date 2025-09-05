@extends('layouts.app')

@section('title', 'Coupon Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Coupon Information</h4>
                        <div class="btn-group" role="group">
                            @if($coupon->used_count == 0)
                                <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Coupon
                                </a>
                            @endif
                            <form action="{{ route('coupons.toggle-active', $coupon) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-{{ $coupon->is_active ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $coupon->is_active ? 'pause' : 'play' }}"></i> 
                                    {{ $coupon->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form action="{{ route('coupons.duplicate', $coupon) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-copy"></i> Duplicate
                                </button>
                            </form>
                            <a href="{{ route('coupons.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Coupon Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Coupon Code</label>
                                                <div class="border p-3 bg-white rounded">
                                                    <h4 class="text-primary mb-0">{{ $coupon->code }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Coupon Name</label>
                                                <p class="form-control-plaintext">{{ $coupon->name }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($coupon->description)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Description</label>
                                            <div class="border p-3 bg-white rounded">
                                                {{ $coupon->description }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Discount Type</label>
                                                <div>
                                                    <span class="badge {{ $coupon->type === 'percentage' ? 'bg-info' : 'bg-warning' }} fs-6">
                                                        {{ $coupon->type_label }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Discount Value</label>
                                                <p class="form-control-plaintext">
                                                    <strong class="fs-5">
                                                        {{ $coupon->type === 'percentage' ? $coupon->value . '%' : '$' . $coupon->value }}
                                                    </strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Minimum Order Amount</label>
                                                <p class="form-control-plaintext">
                                                    {{ $coupon->minimum_amount ? '$' . $coupon->minimum_amount : 'No minimum' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Maximum Discount</label>
                                                <p class="form-control-plaintext">
                                                    {{ $coupon->maximum_discount ? '$' . $coupon->maximum_discount : 'No maximum' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Total Usage Limit</label>
                                                <p class="form-control-plaintext">
                                                    {{ $coupon->usage_limit ? $coupon->usage_limit : 'Unlimited' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Usage Limit Per User</label>
                                                <p class="form-control-plaintext">
                                                    {{ $coupon->usage_limit_per_user ? $coupon->usage_limit_per_user : 'Unlimited' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($coupon->applicable_courses)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Applicable Courses</label>
                                            <div class="border p-3 bg-white rounded">
                                                @php
                                                    $applicableCourses = \App\Models\Course::whereIn('id', $coupon->applicable_courses)->get();
                                                @endphp
                                                @foreach($applicableCourses as $course)
                                                    <span class="badge bg-primary me-2 mb-2">{{ $course->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Applicable Courses</label>
                                            <p class="form-control-plaintext">
                                                <span class="badge bg-success">All Courses</span>
                                            </p>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Start Date</label>
                                                <p class="form-control-plaintext">
                                                    {{ $coupon->starts_at ? $coupon->starts_at->format('M j, Y g:i A') : 'Immediate' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Expiry Date</label>
                                                <p class="form-control-plaintext">
                                                    {{ $coupon->expires_at ? $coupon->expires_at->format('M j, Y g:i A') : 'No expiry' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Status & Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <div>
                                            <span class="badge {{ $coupon->status_badge_class }} fs-6">
                                                {{ $coupon->status }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-0">{{ $coupon->used_count }}</h4>
                                                <small class="text-muted">Used</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success mb-0">{{ $coupon->usage_limit ? $coupon->usage_limit - $coupon->used_count : '∞' }}</h4>
                                            <small class="text-muted">Remaining</small>
                                        </div>
                                    </div>

                                    @if($coupon->usage_limit)
                                        <div class="mt-3">
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ ($coupon->used_count / $coupon->usage_limit) * 100 }}%"
                                                     aria-valuenow="{{ $coupon->used_count }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="{{ $coupon->usage_limit }}">
                                                </div>
                                            </div>
                                            <small class="text-muted">Usage Progress</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Timestamps</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Created</label>
                                        <p class="form-control-plaintext">{{ $coupon->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Last Updated</label>
                                        <p class="form-control-plaintext">{{ $coupon->updated_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($coupon->coursePurchases->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Purchase History ({{ $coupon->coursePurchases->count() }})</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Student</th>
                                                        <th>Course</th>
                                                        <th>Original Price</th>
                                                        <th>Discount</th>
                                                        <th>Final Price</th>
                                                        <th>Purchase Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($coupon->coursePurchases->take(10) as $purchase)
                                                        <tr>
                                                            <td>{{ $purchase->student->name ?? 'Unknown Student' }}</td>
                                                            <td>{{ $purchase->course->name ?? 'Unknown Course' }}</td>
                                                            <td>${{ $purchase->original_price }}</td>
                                                            <td class="text-success">-${{ $purchase->discount_amount }}</td>
                                                            <td class="fw-bold">${{ $purchase->final_price }}</td>
                                                            <td>{{ $purchase->purchased_at ? $purchase->purchased_at->format('M j, Y') : 'Pending' }}</td>
                                                            <td>
                                                                <span class="badge {{ $purchase->payment_status_badge_class }}">
                                                                    {{ $purchase->payment_status_label }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($coupon->coursePurchases->count() > 10)
                                            <div class="text-center mt-3">
                                                <small class="text-muted">Showing first 10 purchases</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
