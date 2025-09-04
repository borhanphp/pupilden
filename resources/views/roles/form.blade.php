<x-layout.default>
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">
                {{ $formType == 'edit' ? 'Edit Role' : 'Create Role' }}
            </h5>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" 
              action="{{ $formType == 'edit' ? route('roles.update', $role->id) : route('roles.store') }}" 
              class="space-y-5">
            @csrf
            @if($formType == 'edit')
                @method('PUT')
            @endif

            <div>
                <label for="name" class="form-label">Role Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-input @error('name') border-danger @enderror" 
                       value="{{ $formType == 'edit' ? $role->name : old('name') }}" 
                       required />
                <input type="hidden" name="guard_name" value="web">
                @error('name')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- <div>
                <label for="guard_name" class="form-label">Guard Name</label>
                <select id="guard_name" 
                        name="guard_name" 
                        class="form-select @error('guard_name') border-danger @enderror">
                    <option value="web" {{ ($formType == 'edit' && $role->guard_name == 'web') || old('guard_name') == 'web' ? 'selected' : '' }}>Web</option>
                    <option value="api" {{ ($formType == 'edit' && $role->guard_name == 'api') || old('guard_name') == 'api' ? 'selected' : '' }}>API</option>
                </select>
                @error('guard_name')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div> --}}

            <div class="mt-8">
                <h6 class="text-base font-semibold mb-4">Permissions</h6>
                
                <!-- Master Checkbox -->
                <div class="mb-4">
                    <label class="inline-flex">
                        <input type="checkbox" class="form-checkbox" id="checkAll">
                        <span class="text-lg font-semibold ml-2">Select All</span>
                    </label>
                </div>

                <!-- Permission Groups -->
                <div class="space-y-4">
                    @foreach($permissions as $head => $permissions)
                        <div class="permission-module border dark:border-[#191e3a] rounded-md">
                            {{-- <!-- Module Header -->
                            <div class="p-4 bg-white dark:bg-[#1b2e4b] cursor-pointer module-header">
                                <label class="inline-flex items-center w-full">
                                    <input type="checkbox" 
                                           class="form-checkbox module-checkbox" 
                                           data-module="{{ $head }}">
                                    <span class="text-base font-semibold ml-2">{{ ucfirst($head) }}</span>
                                    <svg class="w-5 h-5 ml-auto transform transition-transform duration-300" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </label>
                            </div> --}}
                            <div class="p-4 bg-white dark:bg-[#1b2e4b] cursor-pointer module-header">
                                <div class="flex items-center justify-between w-full"> <!-- Added justify-between -->
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" 
                                               class="form-checkbox module-checkbox" 
                                               data-module="{{ $head }}">
                                        <span class="text-base font-semibold ml-2">{{ ucfirst($head) }}</span>
                                    </label>
                                    <svg class="w-5 h-5 transform transition-transform duration-300" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Module Permissions -->
                            <div class="p-4 border-t dark:border-[#191e3a] hidden module-permissions">
                                <div class="space-y-2 ml-6">
                                    @foreach($permissions as $permission)
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   class="form-checkbox permission-checkbox"
                                                   data-module="{{ $head }}"
                                                   {{ $formType == 'edit' && isset($role) && $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                            <span class="ml-2">{{ str_replace($head . '-', '', $permission->name) }}</span>
                                        </label>
                                        <br>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('roles.index') }}" class="btn btn-outline-danger">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    {{ $formType == 'edit' ? 'Update Role' : 'Create Role' }}
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkAll');
            const moduleHeaders = document.querySelectorAll('.module-header');
            const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

            // Toggle module permissions visibility
            moduleHeaders.forEach(header => {
                header.addEventListener('click', (e) => {
                    if (!e.target.matches('input')) {
                        const permissionsDiv = header.nextElementSibling;
                        const arrow = header.querySelector('svg');
                        permissionsDiv.classList.toggle('hidden');
                        arrow.style.transform = permissionsDiv.classList.contains('hidden') 
                            ? 'rotate(0deg)' 
                            : 'rotate(180deg)';
                    }
                });
            });

            // Check All functionality
            checkAll.addEventListener('change', function() {
                const isChecked = this.checked;
                moduleCheckboxes.forEach(checkbox => checkbox.checked = isChecked);
                permissionCheckboxes.forEach(checkbox => checkbox.checked = isChecked);
            });

            // Module checkbox functionality
            moduleCheckboxes.forEach(moduleCheckbox => {
                moduleCheckbox.addEventListener('change', function(e) {
                    e.stopPropagation();
                    const module = this.dataset.module;
                    const relatedPermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
                    relatedPermissions.forEach(checkbox => checkbox.checked = this.checked);
                    updateCheckAllState();
                });
            });

            // Individual permission checkbox functionality
            permissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const module = this.dataset.module;
                    const moduleCheckbox = document.querySelector(`.module-checkbox[data-module="${module}"]`);
                    const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
                    
                    moduleCheckbox.checked = Array.from(modulePermissions).every(p => p.checked);
                    updateCheckAllState();
                });
            });

            function updateCheckAllState() {
                checkAll.checked = Array.from(permissionCheckboxes).every(checkbox => checkbox.checked);
            }

            // Initial state setup
            initializeModuleCheckboxes(); 
            initializeModuleCollapse(); 
            updateCheckAllState();
            function initializeModuleCheckboxes() {
                moduleCheckboxes.forEach(moduleCheckbox => {
                    const module = moduleCheckbox.dataset.module;
                    const permissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
                    moduleCheckbox.checked = Array.from(permissions).every(checkbox => checkbox.checked);
                });
            }
            function initializeModuleCollapse() {
                moduleHeaders.forEach(header => {
                    const permissionsDiv = header.nextElementSibling;
                    if (permissionsDiv.classList.contains('hidden')) {
                        permissionsDiv.classList.remove('hidden');
                    }
                });
            }   
        });
    </script>
</x-layout.default> 