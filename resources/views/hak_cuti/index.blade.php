<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reset Cuti') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-md overflow-hidden">
            <div class="mt-4 mb-4 p-6">
                <form method="POST" action="{{ route('hak_cuti.store') }}" class="space-y-2">
                    @csrf

                    <div>
                        <x-input-label for="hak_cuti" :value="__('Hak Cuti')" />
                        <x-text-input id="hak_cuti" class="block mt-1 w-full" type="number" name="hak_cuti"
                            :value="old('hak_cuti', $hakCuti->hak_cuti ?? '')" />
                        <x-input-error :messages="$errors->get('hak_cuti')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="hak_cuti_bersama" :value="__('Hak Cuti Bersama')" />
                        <x-text-input id="hak_cuti_bersama" class="block mt-1 w-full" type="number"
                            name="hak_cuti_bersama" :value="old('hak_cuti_bersama', $hakCuti->hak_cuti_bersama ?? '')" />
                        <x-input-error :messages="$errors->get('hak_cuti_bersama')" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
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
