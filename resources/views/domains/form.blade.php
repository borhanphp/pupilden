@extends('layouts.app')

@section('title', isset($domain) ? 'Edit Domain' : 'Add New Domain')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($domain) ? 'Edit Domain' : 'Add New Domain' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($domain) ? route('domains.update', $domain) : route('domains.store') }}" method="POST">
                        @csrf
                        @if(isset($domain))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Domain Name</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $domain->domain_name ?? '') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Enter domain name (e.g., example.com)"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       name="is_primary" 
                                       id="is_primary" 
                                       value="1"
                                       {{ old('is_primary', $domain->is_primary ?? false) ? 'checked' : '' }}
                                       class="form-check-input">
                                <label for="is_primary" class="form-check-label">
                                    Set as Primary Domain
                                </label>
                            </div>
                            <div class="form-text">
                                Primary domains are used as the main domain for your organization.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('domains.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($domain) ? 'Update Domain' : 'Create Domain' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
