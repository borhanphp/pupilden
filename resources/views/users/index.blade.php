<x-layout.default>
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Users</h5>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 4V20M20 12H4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="ml-2">Add New User</span>
                </span>
            </a>
        </div>

        @if(session('success'))
            <script>
                showMessage('{{ session('success') }}', 'top-end', true, '', 4000);
            </script>
        @endif

        @if(session('error'))
            <script>
                coloredToast('danger', '{{ session('error') }}');
            </script>
        @endif

        <div class="table-responsive">
            <table class="table-striped table-hover">
                <thead>
                    <tr>
                        <th class="font-bold p-4 text-left">Name</th>
                        <th class="font-bold p-4 text-left">Email</th>
                        <th class="font-bold p-4 text-left">Role</th>
                        <th class="font-bold p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="p-4">{{ $user->name }}</td>
                            <td class="p-4">{{ $user->email }}</td>
                            <td class="p-4">{{ $user->roles->pluck('name')->join(', ') }}</td>
                            <td class="p-4 text-center">
                                <ul class="flex items-center justify-center gap-2">
                                    <li>
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-success">
                                            <x-icons.navigation.eye-icon class="w-4 h-4" />
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                            <x-icons.edit-icon class="w-4 h-4" />
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <x-icons.delete-icon class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-4">
                {{ $users->links() }}
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
