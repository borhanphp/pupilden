<x-layout.default>
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Permissions Management</h5>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($permissions as $head => $permissions)
                <div class="bg-white dark:bg-[#1b2e4b] rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 border-b border-[#e0e6ed] dark:border-[#17263c]">
                        <h2 class="text-lg font-semibold text-primary">{{ $head }}</h2>
                    </div>
                    <div class="p-4">
                        <ul class="space-y-2">
                            @foreach ($permissions as $permission)
                                <li class="flex items-center justify-between">
                                    <span class="text-[#515365] dark:text-white-dark">{{ $permission->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layout.default>