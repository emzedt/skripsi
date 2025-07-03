<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Status Karyawan') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-md overflow-hidden">
            <div class="mt-4 mb-4 p-6">
                <form method="POST" action="{{ route('status_karyawan.update', $statusKaryawan->id) }}">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="status_karyawan" :value="__('Status Karyawan')" />
                        <x-text-input id="status_karyawan" class="block mt-1 w-full" type="text" name="status_karyawan"
                            :value="$statusKaryawan->status_karyawan" />
                        <x-input-error :messages="$errors->get('status_karyawan')" class="mt-2" />
                    </div>
                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('status_karyawan.index') }}"
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
    </div>
</x-app-layout>
