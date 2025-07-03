<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Pengajuan Cuti') }}
            </h2>
        </x-slot>

        <div class="container mx-auto py-8" x-data="{
            currentImageUrl: '{{ $cuti->foto_cuti ? asset('storage/cuti/' . $cuti->foto_cuti) : '' }}',
            newImageUrl: null,
            showModal: false,
            modalImageUrl: '',
            openModal(url) {
                this.modalImageUrl = url;
                this.showModal = true;
            }
        }" x-cloak>
            <div class="bg-white shadow-md rounded-md overflow-hidden">
                <div class="mt-4 mb-4 p-6">
                    <form enctype="multipart/form-data" method="POST"
                        action="{{ route('pengajuan_cuti.update', $cuti->id) }}">
                        @csrf
                        @method('PUT')
                        {{-- Nama Cuti --}}
                        <div>
                            <x-input-label for="nama_cuti" :value="__('Nama Cuti')" />
                            <x-text-input id="nama_cuti" class="block mt-1 w-full" type="text" name="nama_cuti"
                                :value="old('nama_cuti', $cuti->nama_cuti)" />
                            <x-input-error :messages="$errors->get('nama_cuti')" class="mt-2" />
                        </div>

                        {{-- Tanggal Mulai Cuti --}}
                        <div>
                            <x-input-label for="tanggal_mulai_cuti" :value="__('Tanggal Mulai Cuti')" />
                            <x-text-input id="tanggal_mulai_cuti" class="block mt-1 w-full" type="date"
                                name="tanggal_mulai_cuti" :value="old(
                                    'tanggal_mulai_cuti',
                                    $cuti->tanggal_mulai_cuti
                                        ? \Carbon\Carbon::parse($cuti->tanggal_mulai_cuti)->format('Y-m-d')
                                        : '',
                                )" />
                            <x-input-error :messages="$errors->get('tanggal_mulai_cuti')" class="mt-2" />
                        </div>

                        {{-- Tanggal Selesai Cuti --}}
                        <div>
                            <x-input-label for="tanggal_selesai_cuti" :value="__('Tanggal Selesai Cuti')" />
                            <x-text-input id="tanggal_selesai_cuti" class="block mt-1 w-full" type="date"
                                name="tanggal_selesai_cuti" :value="old(
                                    'tanggal_selesai_cuti',
                                    $cuti->tanggal_selesai_cuti
                                        ? \Carbon\Carbon::parse($cuti->tanggal_selesai_cuti)->format('Y-m-d')
                                        : '',
                                )" />
                            <x-input-error :messages="$errors->get('tanggal_selesai_cuti')" class="mt-2" />
                        </div>

                        <!-- Foto -->
                        <div class="mb-6">
                            <x-input-label for="foto_cuti" :value="__('Foto Cuti')" />
                            <input id="foto_cuti" class="block mt-1 w-full border p-2 rounded-md" type="file"
                                name="foto_cuti" accept="image/*"
                                @change="newImageUrl = URL.createObjectURL($event.target.files[0])" />
                            <x-input-error :messages="$errors->get('foto_cuti')" class="mt-2" />

                            <!-- Current Image -->
                            <template x-if="currentImageUrl">
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 mb-2">Foto Saat Ini:</p>
                                    <img :src="currentImageUrl"
                                        class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                        @click="openModal(currentImageUrl)" alt="Foto Cuti Saat Ini">
                                </div>
                            </template>

                            <!-- New Image Preview -->
                            <template x-if="newImageUrl">
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 mb-2">Preview Foto Baru:</p>
                                    <img :src="newImageUrl"
                                        class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                        @click="openModal(newImageUrl)" alt="Preview Foto Cuti Baru">
                                </div>
                            </template>
                        </div>

                        {{-- Alasan Cuti --}}
                        <div>
                            <x-input-label for="alasan_cuti" :value="__('Alasan Cuti')" />
                            <textarea id="alasan_cuti" name="alasan_cuti" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alasan_cuti', $cuti->alasan_cuti) }}</textarea>
                            <x-input-error :messages="$errors->get('alasan_cuti')" class="mt-2" />
                        </div>

                        {{-- Sisa Hak Cuti --}}
                        <div>
                            <x-input-label for="sisa_hak_cuti" :value="__('Sisa Hak Cuti')" />
                            <x-text-input id="sisa_hak_cuti" class="block mt-1 w-full" type="number"
                                name="sisa_hak_cuti" :value="$cuti->user->sisa_hak_cuti" disabled />
                            <x-input-error :messages="$errors->get('sisa_hak_cuti')" class="mt-2" />
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <a href="{{ route('pengajuan_cuti.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Simpan
                            </button>
                        </div>
                    </form>
                    <!-- Modal untuk preview gambar -->
                    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto"
                        @keydown.escape.window="showModal = false">
                        <!-- Overlay -->
                        <div x-show="showModal" x-transition.opacity
                            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                            @click="showModal = false">
                        </div>

                        <!-- Modal Content - Pusatkan vertikal dan horizontal -->
                        <div class="flex items-center justify-center min-h-screen">
                            <div x-show="showModal" x-transition
                                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                                Preview Foto Cuti
                                            </h3>
                                            <div class="mt-2 flex justify-center">
                                                <img :src="modalImageUrl"
                                                    class="max-w-full max-h-[70vh] object-contain"
                                                    alt="Foto Cuti Preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                    <button type="button" @click="showModal = false"
                                        class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>
