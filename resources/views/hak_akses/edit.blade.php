<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <x-slot name="header">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Hak Akses') }} {{ $jabatan->nama }}
                </h2>
            </x-slot>

            <form action="{{ route('hak-akses.update', $jabatan) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                        <input type="text" name="nama" value="{{ $jabatan->nama }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200"
                            disabled>
                    </div>
                    <div class="col-span-2 col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Atasan</label>
                        <div class="space-y-2">
                            @foreach ($jabatan->parentJabatans as $parent)
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-700">{{ $parent->nama }}</span>
                                </div>
                            @endforeach
                        </div>
                        <select name="parent_jabatan_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200">
                            <option value="" disabled>-- Pilih Atasan --</option>
                            <option value="Tidak Ada" {{ $jabatan->parentJabatans->isEmpty() ? 'selected' : '' }}>Tidak
                                Ada </option>
                            @foreach ($parentJabatans as $parent)
                                @if ($parent->id !== $jabatan->id)
                                    <option value="{{ $parent->id }}"
                                        {{ $jabatan->parent_jabatan_id == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->nama }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Anak Buah</label>
                        <div class="space-y-2">
                            @foreach ($jabatan->childJabatans as $child)
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-700">{{ $child->nama }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center mb-4">
                    <input type="checkbox" id="select-all" class="h-4 w-4 text-blue-600">
                    <label for="select-all" class="ml-2 text-sm text-gray-700">Select All</label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($permissions as $group => $groupPermissions)
                        <div class="bg-gray-50 p-4 rounded-lg shadow">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-900">{{ $group }}</h3>
                                <div class="flex items-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="group-select sr-only peer"
                                            data-group="{{ Str::slug($group) }}">
                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600
                                                    after:content-[''] after:absolute after:top-0.5 after:left-[4px]
                                                    after:bg-white after:border after:rounded-full after:h-5 after:w-5
                                                    after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                @foreach ($groupPermissions as $permission)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            id="permission-{{ $permission->id }}"
                                            class="h-4 w-4 text-blue-600 permission-checkbox {{ Str::slug($group) }}"
                                            {{ $jabatan->permissions->contains($permission) ? 'checked' : '' }}>
                                        <label for="permission-{{ $permission->id }}"
                                            class="ml-2 text-sm text-gray-700">
                                            {{ $permission->nama }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <a href="{{ route('hak-akses.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
            const groupToggles = document.querySelectorAll('.group-select');

            // Select All → centang semua
            selectAll.addEventListener('change', function() {
                permissionCheckboxes.forEach(cb => cb.checked = this.checked);
                groupToggles.forEach(toggle => toggle.checked = this.checked);
            });

            // Per grup toggle → centang semua dalam grup
            groupToggles.forEach(toggle => {
                const groupName = toggle.dataset.group;
                const groupCheckboxes = document.querySelectorAll(`.permission-checkbox.${groupName}`);

                toggle.addEventListener('change', function() {
                    groupCheckboxes.forEach(cb => cb.checked = this.checked);
                    updateSelectAll();
                });
            });

            // Per permission → update grup dan select all
            permissionCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateGroupToggle(this);
                    updateSelectAll();
                });
            });

            function updateGroupToggle(cb) {
                const classes = Array.from(cb.classList);
                const groupName = classes.find(c => c !== 'permission-checkbox');
                const groupCheckboxes = document.querySelectorAll(`.permission-checkbox.${groupName}`);
                const groupToggle = document.querySelector(`.group-select[data-group="${groupName}"]`);
                const allChecked = Array.from(groupCheckboxes).every(c => c.checked);
                if (groupToggle) {
                    groupToggle.checked = allChecked;
                }
            }

            function updateSelectAll() {
                const allChecked = Array.from(permissionCheckboxes).every(c => c.checked);
                selectAll.checked = allChecked;
            }

            // Inisialisasi semua saat load
            groupToggles.forEach(toggle => {
                const groupName = toggle.dataset.group;
                const groupCheckboxes = document.querySelectorAll(`.permission-checkbox.${groupName}`);
                const allChecked = Array.from(groupCheckboxes).every(c => c.checked);
                toggle.checked = allChecked;
            });
            updateSelectAll();
        });
    </script>
</x-app-layout>
