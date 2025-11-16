@extends('layouts.app')

@section('title', 'Edit Payment Gateway')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Payment Gateway</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('payment-gateways.update', $paymentGateway) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gateway_name" class="form-label">Gateway Type <span class="text-danger">*</span></label>
                                    <select name="gateway_name" 
                                            id="gateway_name" 
                                            class="form-select @error('gateway_name') is-invalid @enderror"
                                            required>
                                        <option value="">Select Gateway Type</option>
                                        @foreach($gatewayTypes as $key => $name)
                                            <option value="{{ $key }}" {{ old('gateway_name', $paymentGateway->gateway_name) == $key ? 'selected' : '' }}>
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
                                           value="{{ old('display_name', $paymentGateway->display_name) }}"
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
                                              placeholder="Optional description...">{{ old('description', $paymentGateway->description) }}</textarea>
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
                                               {{ old('is_active', $paymentGateway->is_active) ? 'checked' : '' }}>
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
                                               {{ old('is_manual', $paymentGateway->is_manual) ? 'checked' : '' }}>
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
                                               {{ old('is_default', $paymentGateway->is_default) ? 'checked' : '' }}>
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
                                              placeholder='{"api_key": "sk_test_...", "secret_key": "sk_test_..."}'
                                              required>{{ old('credentials', $paymentGateway->credentials ? json_encode($paymentGateway->credentials, JSON_PRETTY_PRINT) : '') }}</textarea>
                                    @error('credentials')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Store API keys, merchant IDs, and other credentials as JSON</div>
                                </div>

                                <div class="mb-3">
                                    <label for="settings" class="form-label">Settings (JSON)</label>
                                    <textarea name="settings" 
                                              id="settings" 
                                              rows="5"
                                              class="form-control @error('settings') is-invalid @enderror"
                                              placeholder='{"currency": "USD", "mode": "sandbox"}'>{{ old('settings', $paymentGateway->settings ? json_encode($paymentGateway->settings, JSON_PRETTY_PRINT) : '') }}</textarea>
                                    @error('settings')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Additional settings like currency, mode, timeout, etc.</div>
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
                                        <i class="fas fa-save"></i> Update Gateway
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

