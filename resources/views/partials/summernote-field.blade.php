{{--
    Rich text field (Summernote BS5) with image upload.
    Expects: $name, $id, $label, $value, $rows (optional), $placeholder (optional)
--}}
@php
    $name = $name ?? 'description';
    $id = $id ?? 'description';
    $label = $label ?? 'Description';
    $rows = isset($rows) ? (int) $rows : 4;
    $placeholderAttr = isset($placeholder) && $placeholder !== '' ? ' placeholder="'.e($placeholder).'"' : '';
    $uploadButtonId = $id.'-image-upload-btn';
@endphp

<div class="mb-3 summernote-field-wrap"
     data-summernote-id="{{ $id }}"
     data-upload-btn="{{ $uploadButtonId }}">
    <label for="{{ $id }}" class="form-label d-flex flex-wrap align-items-center gap-2">
        <span>{{ $label }}</span>
        <button type="button"
                class="btn btn-sm btn-outline-secondary"
                id="{{ $uploadButtonId }}">
            Insert image (upload)
        </button>
    </label>
    <textarea name="{{ $name }}"
              id="{{ $id }}"
              class="form-control summernote-editor @error($name) is-invalid @enderror"
              rows="{{ $rows }}"{!! $placeholderAttr !!}>{{ $value ?? '' }}</textarea>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/summernote-bs5.min.css') }}">
        <style>
            .summernote-field-wrap .note-editor .note-toolbar .dropdown-menu {
                z-index: 2500;
            }
            .summernote-field-wrap .note-popover.popover {
                z-index: 2500;
            }
            .summernote-field-wrap .note-modal {
                z-index: 2600;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/plugin/summernote/summernote-bs5.min.js') }}"></script>
        <script>
            jQuery(function ($) {
                var uploadUrl = @json(route('editor.upload-image'));
                var csrfToken = @json(csrf_token());

                if (typeof $.fn.summernote !== 'function') {
                    console.error('Summernote script not loaded.');
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

                function bindOne($wrap) {
                    var id = $wrap.data('summernote-id');
                    var uploadBtnId = $wrap.data('upload-btn');
                    var $ta = $('#' + id);
                    if (!$ta.length) {
                        return;
                    }

                    $('#' + uploadBtnId).on('click', function () {
                        var input = document.createElement('input');
                        input.type = 'file';
                        input.accept = 'image/jpeg,image/png,image/gif,image/webp';
                        input.onchange = function () {
                            var file = input.files && input.files[0];
                            if (file) {
                                uploadImage(file, $ta);
                            }
                        };
                        input.click();
                    });

                    $ta.closest('form').on('submit', function () {
                        if ($ta.next('.note-editor').length) {
                            $ta.val($ta.summernote('code'));
                        }
                    });

                    try {
                        $ta.summernote({
                            height: 320,
                            placeholder: $ta.attr('placeholder') || '',
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
                                        uploadImage(files[i], $ta);
                                    }
                                }
                            }
                        });
                    } catch (e) {
                        console.error('Summernote init failed:', e);
                    }
                }

                $('.summernote-field-wrap').each(function () {
                    bindOne($(this));
                });
            });
        </script>
    @endpush
@endonce
