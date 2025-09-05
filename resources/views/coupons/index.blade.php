@extends('layouts.app')

@section('title', 'Coupons')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-ticket-alt"></i> Coupons
                        </h4>
                        <div>
                            <a href="{{ route('coupons.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Coupon
                            </a>
                        </div>
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
                        <div class="col-md-6">
                            <form method="GET" class="d-flex gap-2">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="usage_limit_reached" {{ request('status') == 'usage_limit_reached' ? 'selected' : '' }}>Usage Limit Reached</option>
                                </select>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Search by code or name..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('coupons.index') }}" class="btn btn-outline-secondary">
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
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Usage</th>
                                    <th>Status</th>
                                    <th>Validity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong class="text-primary">{{ $coupon->code }}</strong>
                                                <br>
                                                <small class="text-muted">Created {{ $coupon->created_at->format('M j, Y') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $coupon->name }}</strong>
                                                @if($coupon->description)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($coupon->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $coupon->type === 'percentage' ? 'bg-info' : 'bg-warning' }}">
                                                {{ $coupon->type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $coupon->type === 'percentage' ? $coupon->value . '%' : '$' . $coupon->value }}</strong>
                                                @if($coupon->maximum_discount)
                                                    <br>
                                                    <small class="text-muted">Max: ${{ $coupon->maximum_discount }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="badge bg-secondary">{{ $coupon->used_count }}</span>
                                                @if($coupon->usage_limit)
                                                    <span class="text-muted">/ {{ $coupon->usage_limit }}</span>
                                                @else
                                                    <span class="text-muted">/ ∞</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $coupon->status_badge_class }}">
                                                {{ $coupon->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                @if($coupon->starts_at)
                                                    <small class="text-muted">From: {{ $coupon->starts_at->format('M j, Y') }}</small>
                                                    <br>
                                                @endif
                                                @if($coupon->expires_at)
                                                    <small class="text-muted">Until: {{ $coupon->expires_at->format('M j, Y') }}</small>
                                                @else
                                                    <small class="text-muted">No expiry</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('coupons.show', $coupon) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                @if($coupon->used_count == 0)
                                                    <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endif
                                                <form action="{{ route('coupons.toggle-active', $coupon) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $coupon->is_active ? 'warning' : 'success' }}" title="{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas fa-{{ $coupon->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('coupons.duplicate', $coupon) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Duplicate Coupon">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </form>
                                                @if($coupon->used_count == 0)
                                                    <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                                                <p>No coupons found.</p>
                                                <a href="{{ route('coupons.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create your first coupon
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
