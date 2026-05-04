@extends('layouts.app')

@section('title', isset($slider) ? 'Edit Slider' : 'Add Slider')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($slider) ? 'Edit Slider' : 'Add Slider' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($slider) ? route('sliders.update', $slider) : route('sliders.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($slider))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="title"
                                           id="title"
                                           value="{{ old('title', $slider->title ?? '') }}"
                                           class="form-control @error('title') is-invalid @enderror"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description"
                                              id="description"
                                              class="form-control @error('description') is-invalid @enderror"
                                              rows="5"
                                              placeholder="Optional description">{{ old('description', $slider->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image @if(!isset($slider))<span class="text-danger">*</span>@endif</label>
                                    <input type="file"
                                           name="image"
                                           id="image"
                                           class="form-control @error('image') is-invalid @enderror"
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           {{ !isset($slider) ? 'required' : '' }}>
                                    <div class="form-text">JPEG, PNG, GIF, WebP. Max 5 MB.</div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(isset($slider) && $slider->image)
                                    <div class="mb-3">
                                        <label class="form-label">Current image</label>
                                        <div>
                                            <img src="{{ $slider->image_url }}" alt="" class="img-fluid rounded border" style="max-height: 180px;">
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort order</label>
                                    <input type="number"
                                           name="sort_order"
                                           id="sort_order"
                                           min="0"
                                           value="{{ old('sort_order', $slider->sort_order ?? 0) }}"
                                           class="form-control @error('sort_order') is-invalid @enderror">
                                    <div class="form-text">Lower numbers appear first.</div>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               name="is_active"
                                               id="is_active"
                                               value="1"
                                               {{ old('is_active', $slider->is_active ?? true) ? 'checked' : '' }}
                                               class="form-check-input">
                                        <label for="is_active" class="form-check-label">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($slider) ? 'Update' : 'Create' }}
                            </button>
                            <a href="{{ route('sliders.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
