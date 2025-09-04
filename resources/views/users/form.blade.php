<x-layout.default>
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">
                {{ $formType == 'edit' ? 'Edit User' : 'Create User' }}
            </h5>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        <form method="POST"
              action="{{ $formType == 'edit' ? route('users.update', $user->id) : route('users.store') }}"
              class="space-y-5">
            @csrf
            @if($formType == 'edit')
                @method('PUT')
            @endif

            <div>
                <label for="name" class="form-label">Name</label>
                <input type="text"
                id="name"
                name="name"
                class="form-input @error('name') border-danger @enderror"
                value="{{ $formType == 'edit' ? $user->name : old('name') }}"
                required />
                @error('name')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="username" class="form-label">User Name</label>
                <input type="text"
                       id="username"
                       name="username"
                       class="form-input @error('username') border-danger @enderror"
                       value="{{ $formType == 'edit' ? $user->username : old('username') }}"
                       required />
                @error('username')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input @error('email') border-danger @enderror"
                       value="{{ $formType == 'edit' ? $user->email : old('email') }}"
                       required />
                @error('email')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password" class="form-label">
                    {{ $formType == 'edit' ? 'New Password (leave blank to keep current)' : 'Password' }}
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-input @error('password') border-danger @enderror"
                       {{ $formType == 'create' ? 'required' : '' }} />
                @error('password')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-input @error('password_confirmation') border-danger @enderror"
                       {{ $formType == 'create' ? 'required' : '' }} />
                @error('password_confirmation')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="form-label">Roles</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $role->id }}"
                                   class="form-checkbox"
                                   {{ $formType == 'edit' && $user->hasRole($role) ? 'checked' : '' }}>
                            <span class="ml-2">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-outline-danger">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    {{ $formType == 'edit' ? 'Update User' : 'Create User' }}
                </button>
            </div>
        </form>
    </div>
</x-layout.default>
