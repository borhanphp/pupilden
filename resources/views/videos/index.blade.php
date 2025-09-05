@extends('layouts.app')

@section('title', 'Videos')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-video"></i> Videos
                            @if(isset($courseId))
                                <small class="text-muted">for {{ $videos->first()->course->name ?? 'Course' }}</small>
                            @endif
                        </h4>
                        <div>
                            @if(isset($courseId))
                                <a href="{{ route('videos.create', $courseId) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Video to Course
                                </a>
                            @else
                                <a href="{{ route('videos.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Video
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Type</th>
                                    <th>Duration</th>
                                    <th>Preview</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="videos-tbody">
                                @forelse($videos as $video)
                                    <tr data-video-id="{{ $video->id }}">
                                        <td>
                                            <span class="badge bg-secondary">{{ $video->order }}</span>
                                        </td>
                                        <td>
                                            @if($video->thumbnail_url)
                                                <img src="{{ $video->thumbnail_url }}" 
                                                     alt="{{ $video->title }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 80px; height: 45px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 80px; height: 45px;">
                                                    <i class="fas fa-video text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $video->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($video->video_url, 50) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($video->course)
                                                <a href="{{ route('courses.show', $video->course) }}" class="text-decoration-none">
                                                    <span class="badge bg-primary">{{ $video->course->name }}</span>
                                                </a>
                                            @else
                                                <span class="text-muted">No course</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $video->video_type === 0 ? 'info' : 'danger' }}">
                                                {{ $video->video_type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $video->formatted_duration }}</span>
                                        </td>
                                        <td>
                                            @if($video->is_preview)
                                                <span class="badge bg-success">Preview</span>
                                            @else
                                                <span class="badge bg-secondary">No Preview</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('videos.edit', $video) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('videos.show', $video) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form action="{{ route('videos.destroy', $video) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this video?')">
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
                                        <td colspan="8" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-video fa-3x mb-3"></i>
                                                <p>No videos found.</p>
                                                @if(isset($courseId))
                                                    <a href="{{ route('videos.create', $courseId) }}" class="btn btn-primary">
                                                        <i class="fas fa-plus"></i> Add your first video to this course
                                                    </a>
                                                @else
                                                    <a href="{{ route('videos.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus"></i> Add your first video
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($videos->count() > 1)
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-secondary" onclick="saveOrder()">
                                <i class="fas fa-save"></i> Save Order
                            </button>
                            <small class="text-muted ms-2">Drag rows to reorder videos</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($videos->count() > 1)
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            // Initialize drag and drop sorting
            const tbody = document.getElementById('videos-tbody');
            if (tbody) {
                new Sortable(tbody, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function() {
                        updateOrderNumbers();
                    }
                });
            }

            function updateOrderNumbers() {
                const rows = tbody.querySelectorAll('tr[data-video-id]');
                rows.forEach((row, index) => {
                    const orderBadge = row.querySelector('.badge.bg-secondary');
                    if (orderBadge) {
                        orderBadge.textContent = index + 1;
                    }
                });
            }

            function saveOrder() {
                const rows = tbody.querySelectorAll('tr[data-video-id]');
                const videos = Array.from(rows).map((row, index) => ({
                    id: row.getAttribute('data-video-id'),
                    order: index + 1
                }));

                fetch('{{ route("videos.update-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ videos })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.querySelector('.card-body').insertBefore(alert, document.querySelector('.table-responsive'));
                        
                        // Remove alert after 3 seconds
                        setTimeout(() => {
                            alert.remove();
                        }, 3000);
                    } else {
                        alert('Error updating order: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating order');
                });
            }
        </script>
    @endif
@endsection
