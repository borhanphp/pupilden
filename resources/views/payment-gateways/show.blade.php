@extends('layouts.app')

@section('title', 'Payment Gateway Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-credit-card"></i> Payment Gateway Details
                        </h4>
                        <div>
                            <a href="{{ route('payment-gateways.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('payment-gateways.edit', $paymentGateway) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">ID</th>
                                    <td>{{ $paymentGateway->id }}</td>
                                </tr>
                                <tr>
                                    <th>Gateway Type</th>
                                    <td><strong>{{ ucfirst($paymentGateway->gateway_name) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Display Name</th>
                                    <td>{{ $paymentGateway->display_name }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($paymentGateway->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Default Gateway</th>
                                    <td>
                                        @if($paymentGateway->is_default)
                                            <span class="badge bg-primary">Yes</span>
                                        @else
                                            <span class="text-muted">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $paymentGateway->created_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $paymentGateway->updated_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Description</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p>{{ $paymentGateway->description ?? 'No description provided.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Credentials</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    @if($paymentGateway->credentials)
                                        <pre class="mb-0" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($paymentGateway->credentials, JSON_PRETTY_PRINT) }}</code></pre>
                                    @else
                                        <p class="text-muted mb-0">No credentials configured</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Settings</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    @if($paymentGateway->settings)
                                        <pre class="mb-0" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($paymentGateway->settings, JSON_PRETTY_PRINT) }}</code></pre>
                                    @else
                                        <p class="text-muted mb-0">No settings configured</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('payment-gateways.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <div>
                                    <a href="{{ route('payment-gateways.edit', $paymentGateway) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @if(!$paymentGateway->is_default)
                                        <form action="{{ route('payment-gateways.set-default', $paymentGateway) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline-secondary">
                                                <i class="fas fa-star"></i> Set as Default
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('payment-gateways.destroy', $paymentGateway) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment gateway?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

