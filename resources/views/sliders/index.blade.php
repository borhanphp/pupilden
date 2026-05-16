@extends('layouts.app')

@section('title', 'Sliders')

@section('content')
    <div class="row">
        <div class="col-md-12">

            {{-- ── Design Picker ── --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-paint-brush"></i>
                    <h5 class="card-title mb-0">Slider Design</h5>
                    <span class="text-muted small ms-2">Choose how sliders appear on your homepage</span>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @php $currentDesign = $setting?->slider_design ?? 'classic'; @endphp
                    <form action="{{ route('sliders.save-design') }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">

                            {{-- Design 1: Classic --}}
                            <div class="col-md-4">
                                <label class="d-block cursor-pointer h-100">
                                    <input type="radio" name="slider_design" value="classic" class="d-none design-radio"
                                           {{ $currentDesign === 'classic' ? 'checked' : '' }}>
                                    <div class="design-card border rounded-3 overflow-hidden h-100 {{ $currentDesign === 'classic' ? 'border-primary border-2 shadow' : '' }}"
                                         style="cursor:pointer;">
                                        {{-- Mini preview --}}
                                        <div class="position-relative" style="height:120px; background:linear-gradient(135deg,#1a1a2e,#16213e); overflow:hidden;">
                                            <div class="position-absolute inset-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white p-2" style="top:0;left:0;">
                                                <div class="bg-white rounded mb-1" style="width:60%;height:8px;opacity:.9;"></div>
                                                <div class="bg-white rounded mb-2" style="width:40%;height:5px;opacity:.6;"></div>
                                                <div class="bg-primary rounded" style="width:28%;height:14px;border-radius:6px !important;"></div>
                                            </div>
                                            <div class="position-absolute bottom-0 start-0 end-0 d-flex justify-content-center gap-1 pb-2">
                                                <span class="rounded-circle bg-primary" style="width:7px;height:7px;display:inline-block;"></span>
                                                <span class="rounded-circle bg-white opacity-50" style="width:7px;height:7px;display:inline-block;"></span>
                                                <span class="rounded-circle bg-white opacity-50" style="width:7px;height:7px;display:inline-block;"></span>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>Classic</strong>
                                                    <p class="text-muted small mb-0">Full-width image with centred overlay text and dot indicators.</p>
                                                </div>
                                                @if($currentDesign === 'classic')
                                                    <span class="badge bg-primary ms-2 flex-shrink-0">Active</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            {{-- Design 2: Split --}}
                            <div class="col-md-4">
                                <label class="d-block cursor-pointer h-100">
                                    <input type="radio" name="slider_design" value="split" class="d-none design-radio"
                                           {{ $currentDesign === 'split' ? 'checked' : '' }}>
                                    <div class="design-card border rounded-3 overflow-hidden h-100 {{ $currentDesign === 'split' ? 'border-primary border-2 shadow' : '' }}"
                                         style="cursor:pointer;">
                                        <div class="position-relative d-flex" style="height:120px; overflow:hidden;">
                                            <div class="d-flex flex-column justify-content-center p-2" style="width:50%;background:#1a1a2e;">
                                                <div class="bg-white rounded mb-1" style="width:80%;height:7px;opacity:.9;"></div>
                                                <div class="bg-white rounded mb-2" style="width:60%;height:5px;opacity:.6;"></div>
                                                <div class="bg-primary rounded" style="width:45%;height:13px;border-radius:5px !important;"></div>
                                            </div>
                                            <div style="width:50%;background:linear-gradient(135deg,#667eea,#764ba2); position:relative;">
                                                <div class="position-absolute top-50 start-50 translate-middle text-white" style="font-size:2rem;opacity:.4;">🖼</div>
                                            </div>
                                            {{-- Arrows --}}
                                            <div class="position-absolute top-50 start-0 translate-middle-y ms-1 text-white" style="font-size:.75rem;opacity:.7;">❮</div>
                                            <div class="position-absolute top-50 end-0 translate-middle-y me-1 text-white" style="font-size:.75rem;opacity:.7;">❯</div>
                                        </div>
                                        <div class="p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>Split</strong>
                                                    <p class="text-muted small mb-0">Text on the left, image on the right with arrow navigation.</p>
                                                </div>
                                                @if($currentDesign === 'split')
                                                    <span class="badge bg-primary ms-2 flex-shrink-0">Active</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            {{-- Design 3: Cinematic --}}
                            <div class="col-md-4">
                                <label class="d-block cursor-pointer h-100">
                                    <input type="radio" name="slider_design" value="cinematic" class="d-none design-radio"
                                           {{ $currentDesign === 'cinematic' ? 'checked' : '' }}>
                                    <div class="design-card border rounded-3 overflow-hidden h-100 {{ $currentDesign === 'cinematic' ? 'border-primary border-2 shadow' : '' }}"
                                         style="cursor:pointer;">
                                        <div class="position-relative" style="height:120px; background:linear-gradient(to right,#0f0c29,#302b63,#24243e); overflow:hidden;">
                                            <div class="position-absolute inset-0 d-flex flex-column justify-content-center ps-3" style="top:0;left:0;width:65%;">
                                                <div class="bg-primary rounded mb-1" style="width:30%;height:4px;opacity:.8;"></div>
                                                <div class="bg-white rounded mb-1" style="width:90%;height:10px;opacity:.95;"></div>
                                                <div class="bg-white rounded mb-2" style="width:70%;height:6px;opacity:.6;"></div>
                                                <div class="border border-white rounded" style="width:35%;height:14px;border-radius:5px !important;"></div>
                                            </div>
                                            <div class="position-absolute top-0 end-0 h-100 d-flex align-items-center pe-2 text-white opacity-50" style="font-size:1.5rem;">▶</div>
                                            <div class="position-absolute bottom-0 end-0 text-white pe-2 pb-1" style="font-size:.65rem;opacity:.6;">01 / 03</div>
                                        </div>
                                        <div class="p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>Cinematic</strong>
                                                    <p class="text-muted small mb-0">Wide full-height hero, left-side text, slide counter &amp; arrow nav.</p>
                                                </div>
                                                @if($currentDesign === 'cinematic')
                                                    <span class="badge bg-primary ms-2 flex-shrink-0">Active</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Design
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Slider List ── --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-images"></i> Sliders
                        </h4>
                        <a href="{{ route('sliders.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Slider
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width:120px;">Image</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Link</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sliders as $slider)
                                    <tr>
                                        <td>
                                            @if($slider->image)
                                                <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}" class="img-thumbnail"
                                                     style="max-height:72px;max-width:120px;object-fit:cover;">
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $slider->title }}</strong></td>
                                        <td>
                                            @if($slider->description)
                                                <span class="text-muted">{{ Str::limit(strip_tags($slider->description), 80) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($slider->link)
                                                <a href="{{ $slider->link }}" target="_blank" rel="noopener noreferrer"
                                                   class="small text-break">{{ Str::limit($slider->link, 48) }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $slider->sort_order }}</td>
                                        <td>
                                            @if($slider->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sliders.edit', $slider) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('sliders.destroy', $slider) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Delete this slider?')">
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
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-images fa-3x mb-3 d-block"></i>
                                            <p>No sliders yet.</p>
                                            <a href="{{ route('sliders.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Add your first slider
                                            </a>
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

    <style>
        .design-card { transition: border-color .2s, box-shadow .2s; }
        .design-card:hover { border-color: var(--bs-primary) !important; box-shadow: 0 0 0 .15rem rgba(var(--bs-primary-rgb),.25); }
        .design-radio:checked + .design-card { border-color: var(--bs-primary) !important; border-width: 2px !important; box-shadow: 0 0 0 .15rem rgba(var(--bs-primary-rgb),.35); }
        label { cursor: pointer; }
    </style>
    <script>
        // Highlight selected card in real-time
        document.querySelectorAll('.design-radio').forEach(radio => {
            radio.addEventListener('change', () => {
                document.querySelectorAll('.design-card').forEach(card => {
                    card.classList.remove('border-primary', 'border-2', 'shadow');
                });
                if (radio.checked) {
                    radio.nextElementSibling.classList.add('border-primary', 'border-2', 'shadow');
                }
            });
        });
    </script>
@endsection
