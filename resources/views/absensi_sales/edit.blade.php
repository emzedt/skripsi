<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Absensi Sales') }}
            </h2>
        </x-slot>

        <div class="py-8" x-data="{
            currentImageUrl: '{{ $absensiSales->foto ? asset('storage/absensi_sales/' . $absensiSales->foto) : '' }}',
            newImageUrl: null,
            showModal: false,
            modalImageUrl: '',
            openModal(url) {
                this.modalImageUrl = url;
                this.showModal = true;
            }
        }" x-cloak>
            <form enctype="multipart/form-data" method="POST"
                action="{{ route('absensi_sales.update', $absensiSales->id) }}" class="bg-white shadow-sm rounded-lg p-6">
                @csrf
                @method('PUT')

                <!-- Tanggal -->
                <div class="mb-6">
                    <x-input-label for="tanggal" :value="__('Tanggal')" />
                    <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal"
                        value="{{ old('tanggal', $absensiSales->tanggal->format('Y-m-d')) }}" />
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                </div>

                <div class="mb-6">
                    <x-input-label for="jam" :value="__('Jam')" />
                    <x-text-input id="jam" class="block mt-1 w-full" type="time" name="jam"
                        value="{{ old('jam', $absensiSales->jam ? \Carbon\Carbon::parse($absensiSales->jam)->format('H:i') : '') }}" />
                    <x-input-error :messages="$errors->get('jam')" class="mt-2" />
                </div>

                <!-- Foto -->
                <div class="mb-6">
                    <x-input-label for="foto" :value="__('Foto Absensi Sales')" />
                    <input id="foto" class="block mt-1 w-full border p-2 rounded-md" type="file" name="foto"
                        accept="image/*" @change="newImageUrl = URL.createObjectURL($event.target.files[0])" />
                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />

                    <!-- Current Image -->
                    <template x-if="currentImageUrl">
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">Foto Saat Ini:</p>
                            <img :src="currentImageUrl"
                                class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                @click="openModal(currentImageUrl)" alt="Foto Absensi Saat Ini">
                        </div>
                    </template>

                    <!-- New Image Preview -->
                    <template x-if="newImageUrl">
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">Preview Foto Baru:</p>
                            <img :src="newImageUrl"
                                class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                @click="openModal(newImageUrl)" alt="Preview Foto Absensi Baru">
                        </div>
                    </template>
                </div>

                <div class="mb-6">
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="Titip Brosur"
                            {{ old('status', $absensiSales->status) == 'Titip Brosur' ? 'selected' : '' }}>
                            Titip Brosur</option>
                        <option value="Meeting"
                            {{ old('status', $absensiSales->status) == 'Meeting' ? 'selected' : '' }}>
                            Meeting</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('deskripsi', $absensiSales->deskripsi) }}</textarea>
                    <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                </div>

                <!-- Status Persetujuan -->
                <div class="mb-6">
                    <x-input-label for="status_persetujuan" :value="__('Status Persetujuan')" />
                    <select id="status_persetujuan" name="status_persetujuan"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="Menunggu"
                            {{ old('status_persetujuan', $absensiSales->status_persetujuan) == 'Menunggu' ? 'selected' : '' }}>
                            Menunggu</option>
                        <option value="Disetujui"
                            {{ old('status_persetujuan', $absensiSales->status_persetujuan) == 'Disetujui' ? 'selected' : '' }}>
                            Disetujui</option>
                        <option value="Ditolak"
                            {{ old('status_persetujuan', $absensiSales->status_persetujuan) == 'Ditolak' ? 'selected' : '' }}>
                            Ditolak</option>
                    </select>
                    <x-input-error :messages="$errors->get('status_persetujuan')" class="mt-2" />
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <a href="{{ route('absensi_sales.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Batal
                    </a>
                    <button type="submit" @click="handleSubmit"
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
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false">
                </div>

                <!-- Modal Content - Pusatkan vertikal dan horizontal -->
                <div class="flex items-center justify-center min-h-screen">
                    <div x-show="showModal" x-transition
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                        Preview Foto Absensi Sales
                                    </h3>
                                    <div class="mt-2 flex justify-center">
                                        <img :src="modalImageUrl" class="max-w-full max-h-[70vh] object-contain"
                                            alt="Foto Absensi Preview">
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
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>
