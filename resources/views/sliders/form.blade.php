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

                                <div class="mb-3 slider-summernote-wrap">
                                    <label for="description" class="form-label d-flex flex-wrap align-items-center gap-2">
                                        <span>Description</span>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                id="slider-desc-upload-image">
                                            Insert image (upload)
                                        </button>
                                    </label>
                                    <textarea name="description"
                                              id="description"
                                              class="form-control summernote-desc @error('description') is-invalid @enderror"
                                              rows="15"
                                              placeholder="Optional description">{{ old('description', $slider->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="link" class="form-label">Link URL</label>
                                    <input type="text"
                                           name="link"
                                           id="link"
                                           value="{{ old('link', $slider->link ?? '') }}"
                                           class="form-control @error('link') is-invalid @enderror"
                                           placeholder="https://… or /path (optional)"
                                           maxlength="600"
                                           inputmode="url"
                                           autocomplete="off">
                                    <div class="form-text">Optional. Opens when the slide is clicked on the site.</div>
                                    @error('link')
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

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/summernote-bs5.min.css') }}">
    <style>
        /* Toolbar fonts + dropdowns: local CSS uses ./font/ next to this file */
        .slider-summernote-wrap .note-editor .note-toolbar .dropdown-menu {
            z-index: 2500;
        }
        .slider-summernote-wrap .note-popover.popover {
            z-index: 2500;
        }
        .slider-summernote-wrap .note-modal {
            z-index: 2600;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/plugin/summernote/summernote-bs5.min.js') }}"></script>
    <script>
        jQuery(function ($) {
            var uploadUrl = @json(route('sliders.upload-description-image'));
            var csrfToken = @json(csrf_token());
            var $desc = $('#description');

            if (!$desc.length || typeof $.fn.summernote !== 'function') {
                console.error('Summernote: textarea #description missing or summernote script not loaded.');
                return;
            }

            function uploadImage(file, $editor) {
                var data = new FormData();
                data.append('file', file);
                data.append('_token', csrfToken);
                $.ajax({
                    url: uploadUrl,
                    method: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if (res && res.url) {
                            $editor.summernote('insertImage', res.url);
                        }
                    },
                    error: function (xhr) {
                        var msg = 'Image upload failed.';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.file) {
                            msg = xhr.responseJSON.errors.file[0];
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        alert(msg);
                    }
                });
            }

            $('#slider-desc-upload-image').on('click', function () {
                var input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/jpeg,image/png,image/gif,image/webp';
                input.onchange = function () {
                    var file = input.files && input.files[0];
                    if (file) {
                        uploadImage(file, $desc);
                    }
                };
                input.click();
            });

            $desc.closest('form').on('submit', function () {
                if ($desc.next('.note-editor').length) {
                    $desc.val($desc.summernote('code'));
                }
            });

            try {
                $desc.summernote({
                    height: 320,
                    placeholder: 'Optional description',
                    disableDragAndDrop: false,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'hr']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onImageUpload: function (files) {
                            for (var i = 0; i < files.length; i++) {
                                uploadImage(files[i], $desc);
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Summernote init failed:', e);
            }
        });
    </script>
@endpush
