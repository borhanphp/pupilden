<x-layout.default>
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Roles</h5>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 4V20M20 12H4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="ml-2">Add New Role</span>
                </span>
            </a>
        </div>

      

        <div class="table-responsive">
            <table class="table-striped table-hover">
                <thead>
                    <tr>
                        <th class="font-bold p-4 text-left">Name</th>
                        <th class="font-bold p-4 text-left">Guard Name</th>
                        <th class="font-bold p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="p-4">{{ $role->name }}</td>
                            <td class="p-4">{{ $role->guard_name }}</td>
                            <td class="p-4 text-center">
                                <ul class="flex items-center justify-center gap-2">
                                    <li>
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                                            <x-icons.edit-icon class="w-4 h-4" />
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this role?')">
                                                <x-icons.delete-icon class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($roles->hasPages())
            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</x-layout.default>

@push('scripts')
<script>
    showMessage = (msg = 'Example notification text.', position = 'bottom-start', showCloseButton = true, closeButtonHtml = '', duration = 3000) => {
        const toast = window.Swal.mixin({
            toast: true,
            position: position || 'bottom-start',
            showConfirmButton: false,
            timer: duration,
            showCloseButton: showCloseButton,
        });
        toast.fire({
            title: msg,
        });
    };

    coloredToast = (color, msg) => {
        const toast = window.Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            showCloseButton: true,
            animation: false,
            customClass: {
                popup: `color-${color}`
            }
        });
        toast.fire({
            title: msg,
        });
    };
</script>
@endpush
