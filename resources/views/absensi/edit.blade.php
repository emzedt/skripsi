<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Absensi') }}
            </h2>
        </x-slot>

        <div class="py-8" x-data="{
            currentFotoMasukUrl: '{{ $absensi->foto_masuk ? asset('storage/' . $absensi->foto_masuk) : '' }}',
            newFotoMasukUrl: null,
            currentFotoKeluarUrl: '{{ $absensi->foto_keluar ? asset('storage/' . $absensi->foto_keluar) : '' }}',
            newFotoKeluarUrl: null,
            showModal: false,
            modalImageUrl: '',
            openModal(url) {
                this.modalImageUrl = url;
                this.showModal = true;
            }
        }">
            <form enctype="multipart/form-data" method="POST" action="{{ route('absensi.update', $absensi->id) }}"
                class="bg-white shadow-sm rounded-lg p-6">
                @csrf
                @method('PUT')

                <!-- Tanggal -->
                <div class="mb-6">
                    <x-input-label for="tanggal" :value="__('Tanggal')" />
                    <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal"
                        value="{{ old('tanggal', $absensi->tanggal->format('Y-m-d')) }}" readonly />
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                </div>

                <!-- Foto Masuk -->
                <div class="mb-6">
                    <x-input-label for="foto_masuk" :value="__('Foto Masuk')" />
                    <input id="foto_masuk" class="block mt-1 w-full border p-2 rounded-md" type="file"
                        name="foto_masuk" accept="image/*"
                        @change="newFotoMasukUrl = URL.createObjectURL($event.target.files[0])" />
                    <x-input-error :messages="$errors->get('foto_masuk')" class="mt-2" />

                    <!-- Current Image -->
                    <template x-if="currentFotoMasukUrl">
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">Foto Saat Ini:</p>
                            <img :src="currentFotoMasukUrl"
                                class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                @click="openModal(currentFotoMasukUrl)" alt="Foto Masuk Saat Ini">
                        </div>
                    </template>

                    <!-- New Image Preview -->
                    <template x-if="newFotoMasukUrl">
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">Preview Foto Baru:</p>
                            <img :src="newFotoMasukUrl"
                                class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                @click="openModal(newFotoMasukUrl)" alt="Preview Foto Masuk Baru">
                        </div>
                    </template>
                </div>

                <!-- Jam Masuk -->
                <div class="mb-6">
                    <x-input-label for="jam_masuk" :value="__('Jam Masuk')" />
                    <x-text-input id="jam_masuk" class="block mt-1 w-full" type="time" name="jam_masuk"
                        value="{{ old('jam_masuk', $absensi->jam_masuk) }}" />
                    <x-input-error :messages="$errors->get('jam_masuk')" class="mt-2" />
                </div>

                <!-- Koordinat Masuk -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <x-input-label for="latitude_masuk" :value="__('Latitude Masuk')" />
                        <x-text-input id="latitude_masuk" class="block mt-1 w-full" type="text" name="latitude_masuk"
                            value="{{ old('latitude_masuk', $absensi->latitude_masuk) }}" />
                        <x-input-error :messages="$errors->get('latitude_masuk')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="longitude_masuk" :value="__('Longitude Masuk')" />
                        <x-text-input id="longitude_masuk" class="block mt-1 w-full" type="text"
                            name="longitude_masuk" value="{{ old('longitude_masuk', $absensi->longitude_masuk) }}" />
                        <x-input-error :messages="$errors->get('longitude_masuk')" class="mt-2" />
                    </div>
                </div>

                <!-- Foto Keluar -->
                <div class="mb-6">
                    <x-input-label for="foto_keluar" :value="__('Foto Keluar')" />
                    <input id="foto_keluar" class="block mt-1 w-full border p-2 rounded-md" type="file"
                        name="foto_keluar" accept="image/*"
                        @change="newFotoKeluarUrl = URL.createObjectURL($event.target.files[0])" />
                    <x-input-error :messages="$errors->get('foto_keluar')" class="mt-2" />

                    <!-- Current Image -->
                    <template x-if="currentFotoKeluarUrl">
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">Foto Saat Ini:</p>
                            <img :src="currentFotoKeluarUrl"
                                class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                @click="openModal(currentFotoKeluarUrl)" alt="Foto Keluar Saat Ini">
                        </div>
                    </template>

                    <!-- New Image Preview -->
                    <template x-if="newFotoKeluarUrl">
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">Preview Foto Baru:</p>
                            <img :src="newFotoKeluarUrl"
                                class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                @click="openModal(newFotoKeluarUrl)" alt="Preview Foto Keluar Baru">
                        </div>
                    </template>
                </div>

                <!-- Jam Keluar -->
                <div class="mb-6">
                    <x-input-label for="jam_keluar" :value="__('Jam Keluar')" />
                    <x-text-input id="jam_keluar" class="block mt-1 w-full" type="time" name="jam_keluar"
                        value="{{ old('jam_keluar', $absensi->jam_keluar) }}" />
                    <x-input-error :messages="$errors->get('jam_keluar')" class="mt-2" />
                </div>

                <!-- Koordinat Keluar -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <x-input-label for="latitude_keluar" :value="__('Latitude Keluar')" />
                        <x-text-input id="latitude_keluar" class="block mt-1 w-full" type="text"
                            name="latitude_keluar" value="{{ old('latitude_keluar', $absensi->latitude_keluar) }}" />
                        <x-input-error :messages="$errors->get('latitude_keluar')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="longitude_keluar" :value="__('Longitude Keluar')" />
                        <x-text-input id="longitude_keluar" class="block mt-1 w-full" type="text"
                            name="longitude_keluar"
                            value="{{ old('longitude_keluar', $absensi->longitude_keluar) }}" />
                        <x-input-error :messages="$errors->get('longitude_keluar')" class="mt-2" />
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="Hadir" {{ old('status', $absensi->status) == 'Hadir' ? 'selected' : '' }}>
                            Hadir</option>
                        <option value="Alfa" {{ old('status', $absensi->status) == 'Alfa' ? 'selected' : '' }}>Alfa
                        </option>
                        <option value="Izin" {{ old('status', $absensi->status) == 'Izin' ? 'selected' : '' }}>Izin
                        </option>
                        <option value="Cuti" {{ old('status', $absensi->status) == 'Cuti' ? 'selected' : '' }}>Cuti
                        </option>
                        <option value="Telat" {{ old('status', $absensi->status) == 'Telat' ? 'selected' : '' }}>
                            Telat</option>
                        <option value="Belum Absen"
                            {{ old('status', $absensi->status) == 'Belum Absen' ? 'selected' : '' }}>Belum Absen
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <!-- Lokasi -->
                <div class="mb-6">
                    <x-input-label for="lokasi_id" :value="__('Lokasi')" />
                    <select id="lokasi_id" name="lokasi_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="" disabled>-- Pilih Lokasi --</option>
                        @foreach ($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}"
                                {{ old('lokasi_id', $absensi->lokasi_id) == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->nama }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('lokasi_id')" class="mt-2" />
                </div>

                <!-- Tombol Submit -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('absensi.index') }}"
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
                @keydown.escape.window="showModal = false" style="display: none;">
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
                                        Preview Foto Absensi
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
</x-app-layout>
