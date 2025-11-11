@extends('layouts.app')

@section('title', isset($coupon) ? 'Edit Coupon' : 'Add New Coupon')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($coupon) ? 'Edit Coupon' : 'Add New Coupon' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($coupon) ? route('coupons.update', $coupon) : route('coupons.store') }}" method="POST">
                        @csrf
                        @if(isset($coupon))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Coupon Code</label>
                                            <input type="text" 
                                                   name="code" 
                                                   id="code" 
                                                   value="{{ old('code', isset($coupon) ? $coupon->code : '') }}"
                                                   class="form-control @error('code') is-invalid @enderror"
                                                   placeholder="SAVE20"
                                                   required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Code will be automatically converted to uppercase</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Coupon Name</label>
                                            <input type="text" 
                                                   name="name" 
                                                   id="name" 
                                                   value="{{ old('name', isset($coupon) ? $coupon->name : '') }}"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   placeholder="20% Off Discount"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" 
                                              id="description" 
                                              rows="3"
                                              class="form-control @error('description') is-invalid @enderror"
                                              placeholder="Describe what this coupon offers...">{{ old('description', isset($coupon) ? $coupon->description : '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Discount Type</label>
                                            <select name="type" 
                                                    id="type" 
                                                    class="form-select @error('type') is-invalid @enderror"
                                                    onchange="toggleValueLabel()"
                                                    required>
                                                <option value="percentage" {{ old('type', isset($coupon) ? $coupon->type : '') == 'percentage' ? 'selected' : '' }}>Percentage Discount</option>
                                                <option value="fixed" {{ old('type', isset($coupon) ? $coupon->type : '') == 'fixed' ? 'selected' : '' }}>Fixed Amount Discount</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="value" class="form-label" id="value-label">Discount Value</label>
                                            <div class="input-group">
                                                <input type="number" 
                                                       name="value" 
                                                       id="value" 
                                                       value="{{ old('value', isset($coupon) ? $coupon->value : '') }}"
                                                       class="form-control @error('value') is-invalid @enderror"
                                                       placeholder="20"
                                                       min="0"
                                                       step="0.01"
                                                       required>
                                                <span class="input-group-text" id="value-unit">%</span>
                                            </div>
                                            @error('value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="minimum_amount" class="form-label">Minimum Order Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" 
                                                       name="minimum_amount" 
                                                       id="minimum_amount" 
                                                       value="{{ old('minimum_amount', isset($coupon) ? $coupon->minimum_amount : '') }}"
                                                       class="form-control @error('minimum_amount') is-invalid @enderror"
                                                       placeholder="0"
                                                       min="0"
                                                       step="0.01">
                                            </div>
                                            @error('minimum_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave empty for no minimum</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="maximum_discount" class="form-label">Maximum Discount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" 
                                                       name="maximum_discount" 
                                                       id="maximum_discount" 
                                                       value="{{ old('maximum_discount', isset($coupon) ? $coupon->maximum_discount : '') }}"
                                                       class="form-control @error('maximum_discount') is-invalid @enderror"
                                                       placeholder="0"
                                                       min="0"
                                                       step="0.01">
                                            </div>
                                            @error('maximum_discount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave empty for no maximum</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="usage_limit" class="form-label">Total Usage Limit</label>
                                            <input type="number" 
                                                   name="usage_limit" 
                                                   id="usage_limit" 
                                                   value="{{ old('usage_limit', isset($coupon) ? $coupon->usage_limit : '') }}"
                                                   class="form-control @error('usage_limit') is-invalid @enderror"
                                                   placeholder="100"
                                                   min="1">
                                            @error('usage_limit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave empty for unlimited usage</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="usage_limit_per_user" class="form-label">Usage Limit Per User</label>
                                            <input type="number" 
                                                   name="usage_limit_per_user" 
                                                   id="usage_limit_per_user" 
                                                   value="{{ old('usage_limit_per_user', isset($coupon) ? $coupon->usage_limit_per_user : '') }}"
                                                   class="form-control @error('usage_limit_per_user') is-invalid @enderror"
                                                   placeholder="1"
                                                   min="1">
                                            @error('usage_limit_per_user')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave empty for unlimited per user</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="applicable_courses" class="form-label">Applicable Courses</label>
                                    <select name="applicable_courses[]" 
                                            id="applicable_courses" 
                                            class="form-select @error('applicable_courses') is-invalid @enderror"
                                            multiple>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                    {{ in_array($course->id, old('applicable_courses', isset($coupon) && $coupon->applicable_courses ? $coupon->applicable_courses : [])) ? 'selected' : '' }}>
                                                {{ $course->name }} - ${{ $course->price }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('applicable_courses')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Hold Ctrl/Cmd to select multiple courses. Leave empty to apply to all courses.</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="starts_at" class="form-label">Start Date</label>
                                            <input type="datetime-local" 
                                                   name="starts_at" 
                                                   id="starts_at" 
                                                   value="{{ old('starts_at', isset($coupon) && $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}"
                                                   class="form-control @error('starts_at') is-invalid @enderror">
                                            @error('starts_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave empty to start immediately</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="expires_at" class="form-label">Expiry Date</label>
                                            <input type="datetime-local" 
                                                   name="expires_at" 
                                                   id="expires_at" 
                                                   value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                                                   class="form-control @error('expires_at') is-invalid @enderror">
                                            @error('expires_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave empty for no expiry</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               value="1"
                                               name="is_active" 
                                               id="is_active" 
                                               class="form-check-input"
                                               {{ old('is_active', isset($coupon) ? $coupon->is_active : true) ? 'checked' : '' }}>
                                        <label for="is_active" class="form-check-label">
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-text">Uncheck to deactivate this coupon</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Coupon Types</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Percentage Discount</label>
                                            <ul class="list-unstyled small">
                                                <li>• Discount is calculated as percentage of order total</li>
                                                <li>• Example: 20% off $100 = $20 discount</li>
                                                <li>• Maximum value: 100%</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Fixed Amount Discount</label>
                                            <ul class="list-unstyled small">
                                                <li>• Fixed dollar amount discount</li>
                                                <li>• Example: $10 off any order</li>
                                                <li>• Cannot exceed order total</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-light mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Usage Guidelines</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled small">
                                            <li><i class="fas fa-lightbulb text-warning me-2"></i>Use clear, memorable codes</li>
                                            <li><i class="fas fa-lightbulb text-warning me-2"></i>Set reasonable usage limits</li>
                                            <li><i class="fas fa-lightbulb text-warning me-2"></i>Test coupons before publishing</li>
                                            <li><i class="fas fa-lightbulb text-warning me-2"></i>Monitor usage regularly</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('coupons.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleValueLabel() {
            const type = document.getElementById('type').value;
            const valueLabel = document.getElementById('value-label');
            const valueUnit = document.getElementById('value-unit');
            const valueInput = document.getElementById('value');

            if (type === 'percentage') {
                valueLabel.textContent = 'Discount Percentage';
                valueUnit.textContent = '%';
                valueInput.max = 100;
                valueInput.placeholder = '20';
            } else {
                valueLabel.textContent = 'Discount Amount';
                valueUnit.textContent = '$';
                valueInput.removeAttribute('max');
                valueInput.placeholder = '10.00';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleValueLabel();
        });
    </script>
@endsection
