@extends('layouts.app')

@section('title', 'Payment Gateways')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-credit-card"></i> Payment Gateways
                        </h4>
                        <div>
                            <a href="{{ route('payment-gateways.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Gateway
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="="alert">
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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Gateway Name</th>
                                    <th>Display Name</th>
                                    <th>Status</th>
                                    <th>Default</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentGateways as $gateway)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ ucfirst($gateway->gateway_name) }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $gateway->display_name }}</td>
                                        <td>
                                            @if($gateway->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($gateway->is_default)
                                                <span class="badge bg-primary">Default</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $gateway->created_at->format('M j, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('payment-gateways.show', $gateway) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('payment-gateways.edit', $gateway) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('payment-gateways.toggle-active', $gateway) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $gateway->is_active ? 'warning' : 'success' }}">
                                                        <i class="fas fa-{{ $gateway->is_active ? 'pause' : 'play' }}"></i> {{ $gateway->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                                @if(!$gateway->is_default)
                                                    <form action="{{ route('payment-gateways.set-default', $gateway) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-star"></i> Set Default
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('payment-gateways.destroy', $gateway) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment gateway?')">
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
                                        <td colspan="6" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-credit-card fa-3x mb-3"></i>
                                                <p>No payment gateways found.</p>
                                                <a href="{{ route('payment-gateways.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create your first payment gateway
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

