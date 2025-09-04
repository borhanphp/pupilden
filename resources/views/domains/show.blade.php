@extends('layouts.app')

@section('title', 'Domain Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Domain Information</h4>
                        <div class="btn-group" role="group">
                            <a href="{{ route('domains.edit', $domain) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Domain
                            </a>
                            <a href="{{ route('domains.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Domain Name</label>
                                        <p class="form-control-plaintext">{{ $domain->domain_name }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <div>
                                            @if($domain->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Primary Domain</label>
                                        <div>
                                            @if($domain->is_primary)
                                                <span class="badge bg-primary">Primary</span>
                                            @else
                                                <span class="badge bg-secondary">Secondary</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Verification & Dates</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Verification Status</label>
                                        <div>
                                            @if($domain->is_verified)
                                                <span class="badge bg-success">Verified</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending Verification</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Expiration Status</label>
                                        <div>
                                            @if($domain->is_expired)
                                                <span class="badge bg-danger">Expired</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($domain->activation_date)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Activation Date</label>
                                            <p class="form-control-plaintext">{{ $domain->activation_date }}</p>
                                        </div>
                                    @endif
                                    @if($domain->expiry_date)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Expiry Date</label>
                                            <p class="form-control-plaintext">{{ $domain->expiry_date }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Timestamps</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Created At</label>
                                            <p class="form-control-plaintext">{{ $domain->created_at->format('F j, Y g:i A') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Last Updated</label>
                                            <p class="form-control-plaintext">{{ $domain->updated_at->format('F j, Y g:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
