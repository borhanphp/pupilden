@extends('layouts.app')

@section('title', 'Student Payments')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-credit-card"></i> Payment History - {{ $student->name }}
                        </h4>
                        <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Student
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

                    <!-- Student Info -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>Total Purchases</h5>
                                    <h3>{{ $payments->total() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>Completed</h5>
                                    <h3>{{ $payments->where('payment_status', 'completed')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h5>Pending</h5>
                                    <h3>{{ $payments->where('payment_status', 'pending')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>Total Spent</h5>
                                    <h3>${{ number_format($payments->where('payment_status', 'completed')->sum('final_price'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Original Price</th>
                                    <th>Discount</th>
                                    <th>Final Price</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $payment->course->name }}</strong>
                                                @if($payment->coupon)
                                                    <br>
                                                    <small class="text-muted">Coupon: {{ $payment->coupon->code }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">${{ number_format($payment->original_price, 2) }}</span>
                                        </td>
                                        <td>
                                            @if($payment->discount_amount > 0)
                                                <span class="text-success">-${{ number_format($payment->discount_amount, 2) }}</span>
                                            @else
                                                <span class="text-muted">$0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">${{ number_format($payment->final_price, 2) }}</span>
                                        </td>
                                        <td>
                                            @if($payment->payment_method)
                                                <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $payment->payment_status_badge_class }}">
                                                {{ $payment->payment_status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($payment->transaction_id)
                                                <code>{{ $payment->transaction_id }}</code>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $payment->created_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#updatePaymentModal{{ $payment->id }}">
                                                <i class="fas fa-edit"></i> Update
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Update Payment Modal -->
                                    <div class="modal fade" id="updatePaymentModal{{ $payment->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Payment Status</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('course-purchases.update-status', $payment) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Course</label>
                                                            <p class="form-control-plaintext">{{ $payment->course->name }}</p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="payment_status{{ $payment->id }}" class="form-label">Payment Status</label>
                                                            <select class="form-select" id="payment_status{{ $payment->id }}" name="payment_status" required>
                                                                <option value="pending" {{ $payment->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="completed" {{ $payment->payment_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                                <option value="failed" {{ $payment->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                                                <option value="refunded" {{ $payment->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="transaction_id{{ $payment->id }}" class="form-label">Transaction ID</label>
                                                            <input type="text" class="form-control" id="transaction_id{{ $payment->id }}" 
                                                                   name="transaction_id" value="{{ $payment->transaction_id }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="payment_method{{ $payment->id }}" class="form-label">Payment Method</label>
                                                            <input type="text" class="form-control" id="payment_method{{ $payment->id }}" 
                                                                   name="payment_method" value="{{ $payment->payment_method }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="notes{{ $payment->id }}" class="form-label">Admin Notes</label>
                                                            <textarea class="form-control" id="notes{{ $payment->id }}" name="notes" rows="3">{{ $payment->payment_details['admin_notes'] ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-credit-card fa-3x mb-3"></i>
                                                <p>No payment history found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($payments->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $payments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
