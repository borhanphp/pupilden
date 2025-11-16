@extends('layouts.app')

@section('title', 'Create Payment Gateway')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create New Payment Gateway</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('payment-gateways.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gateway_name" class="form-label">Gateway Type <span class="text-danger">*</span></label>
                                    <select name="gateway_name" 
                                            id="gateway_name" 
                                            class="form-select @error('gateway_name') is-invalid @enderror"
                                            required
                                            onchange="updateCredentialsTemplate()">
                                        <option value="">Select Gateway Type</option>
                                        @foreach($gatewayTypes as $key => $name)
                                            <option value="{{ $key }}" {{ old('gateway_name') == $key ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('gateway_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="display_name" 
                                           id="display_name" 
                                           value="{{ old('display_name') }}"
                                           class="form-control @error('display_name') is-invalid @enderror"
                                           placeholder="e.g., Stripe Payment"
                                           required>
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" 
                                              id="description" 
                                              rows="3"
                                              class="form-control @error('description') is-invalid @enderror"
                                              placeholder="Optional description...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active" 
                                               value="1"
                                               class="form-check-input"
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="is_manual" 
                                               id="is_manual" 
                                               value="1"
                                               class="form-check-input"
                                               {{ old('is_manual') ? 'checked' : '' }}>
                                    </div>
                                    <label class="form-check-label" for="is_manual">
                                        Manual Mode
                                    </label>
                                </div>
                                <small class="form-text text-muted">Manual mode is used for that user can manually input the payment details</small>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="is_default" 
                                               id="is_default" 
                                               value="1"
                                               class="form-check-input"
                                               {{ old('is_default') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_default">
                                            Set as Default Gateway
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Only one gateway can be set as default</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="credentials" class="form-label">Credentials (JSON) <span class="text-danger">*</span></label>
                                    <textarea name="credentials" 
                                              id="credentials" 
                                              rows="8"
                                              class="form-control @error('credentials') is-invalid @enderror"
                                              placeholder='{"api_key": "sk_test_...", "secret_key": "sk_test_...", "webhook_secret": "whsec_..."}'
                                              required></textarea>
                                    @error('credentials')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <strong>Example for Stripe:</strong><br>
                                        <code>{"api_key": "sk_test_...", "secret_key": "sk_test_...", "webhook_secret": "whsec_..."}</code><br><br>
                                        <strong>Example for PayPal:</strong><br>
                                        <code>{"client_id": "...", "client_secret": "...", "mode": "sandbox"}</code>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="settings" class="form-label">Settings (JSON)</label>
                                    <textarea name="settings" 
                                              id="settings" 
                                              rows="5"
                                              class="form-control @error('settings') is-invalid @enderror"
                                              placeholder='{"currency": "USD", "mode": "sandbox", "timeout": 30}'></textarea>
                                    @error('settings')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <strong>Example:</strong><br>
                                        <code>{"currency": "USD", "mode": "sandbox", "timeout": 30}</code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('payment-gateways.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Gateway
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateCredentialsTemplate() {
            const gatewayName = document.getElementById('gateway_name').value;
            const credentialsTextarea = document.getElementById('credentials');
            
            const templates = {
                'stripe': '{\n  "api_key": "sk_test_...",\n  "secret_key": "sk_test_...",\n  "webhook_secret": "whsec_..."\n}',
                'paypal': '{\n  "client_id": "...",\n  "client_secret": "...",\n  "mode": "sandbox"\n}',
                'razorpay': '{\n  "key_id": "...",\n  "key_secret": "...",\n  "webhook_secret": "..."\n}',
                'sslcommerz': '{\n  "store_id": "...",\n  "store_password": "...",\n  "sandbox": true\n}',
                'bkash': '{\n  "app_key": "...",\n  "app_secret": "...",\n  "username": "...",\n  "password": "...",\n  "sandbox": true\n}',
                'nagad': '{\n  "merchant_id": "...",\n  "merchant_number": "...",\n  "api_key": "...",\n  "sandbox": true\n}',
                'rocket': '{\n  "merchant_id": "...",\n  "merchant_number": "...",\n  "api_key": "...",\n  "sandbox": true\n}',
                'bank_transfer': '{\n  "bank_name": "...",\n  "account_number": "...",\n  "account_name": "...",\n  "branch": "...",\n  "routing_number": "..."\n}'
            };

            if (templates[gatewayName] && !credentialsTextarea.value) {
                credentialsTextarea.value = templates[gatewayName];
            }
        }

        // Validate JSON on blur
        document.getElementById('credentials').addEventListener('blur', function() {
            validateJSON(this);
        });

        document.getElementById('settings').addEventListener('blur', function() {
            validateJSON(this);
        });

        function validateJSON(textarea) {
            const value = textarea.value.trim();
            if (value) {
                try {
                    JSON.parse(value);
                    textarea.classList.remove('is-invalid');
                    textarea.classList.add('is-valid');
                } catch (e) {
                    textarea.classList.remove('is-valid');
                    textarea.classList.add('is-invalid');
                }
            } else {
                textarea.classList.remove('is-invalid', 'is-valid');
            }
        }
    </script>
@endsection

