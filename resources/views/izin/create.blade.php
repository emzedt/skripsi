<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Pengajuan Izin') }}
            </h2>
        </x-slot>

        <div class="container mx-auto py-8" x-data="{
            newImageUrl: null,
            showModal: false,
            modalImageUrl: '',
            openModal(url) {
                this.modalImageUrl = url;
                this.showModal = true;
            }
        }">
            <div class="bg-white shadow-md rounded-md overflow-hidden">
                <div class="mt-4 mb-4 p-6">
                    <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <x-input-label for="tanggal" :value="__('Tanggal')" />
                            <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal"
                                :value="old('tanggal')" onchange="HitungHariIzin()" />
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>

                        <div id="jumlah-hari-container" class="hidden">
                            <p class="text-sm text-gray-600 mt-2">
                                Lama izin: <span id="jumlah-hari">0</span> hari
                            </p>
                        </div>

                        <div class="mt-3">
                            <x-input-label for="jenis_izin" :value="__('Jenis Izin')" />
                            <select id="jenis_izin" name="jenis_izin"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300 focus:ring-opacity-50"
                                required>
                                <option value="" disabled selected>-- Pilih Izin --</option>
                                <option value="Satu Hari">Satu Hari (08:00-17:00)</option>
                                <option value="Setengah Hari Pagi">Setengah Hari Pagi (08:00-12:00)</option>
                                <option value="Setengah Hari Siang">Setengah Hari Siang (13:00-17:00)</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_izin')" class="mt-2" />
                        </div>

                        <div class="mb-6 mt-3">
                            <x-input-label for="dokumen_pendukung" :value="__('Foto Dokumen Pendukung (Opsional)')" />
                            <p class="block font-medium text-xs text-gray-600">Format yang diterima png, jpg, dan jpeg
                            </p>
                            <input id="dokumen_pendukung" class="block mt-1 w-full border p-2 rounded-md" type="file"
                                name="dokumen_pendukung" accept="image/*"
                                @change="newImageUrl = URL.createObjectURL($event.target.files[0])" />
                            <x-input-error :messages="$errors->get('dokumen_pendukung')" class="mt-2" />

                            <!-- New Image Preview -->
                            <template x-if="newImageUrl">
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 mb-2">Preview Foto:</p>
                                    <img :src="newImageUrl"
                                        class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                        @click="openModal(newImageUrl)" alt="Preview Foto Absensi">
                                </div>
                            </template>
                        </div>

                        <div>
                            <x-input-label for="alasan" :value="__('Alasan')" />
                            <textarea id="alasan" name="alasan" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alasan') }}</textarea>
                            <x-input-error :messages="$errors->get('alasan')" class="mt-2" />
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <a href="{{ route('izin.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Simpan
                            </button>
                        </div>
                    </form>

                    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto"
                        @keydown.escape.window="showModal = false" style="display: none;">
                        <div
                            class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <!-- Overlay -->
                            <div x-show="showModal" x-transition.opacity
                                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                @click="showModal = false">
                            </div>

                            <!-- Modal Content -->
                            <div class="flex items-center justify-center min-h-screen">
                                <div x-show="showModal" x-transition
                                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                                        <div class="sm:flex sm:items-start">
                                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                                    Preview Foto Surat Dokter
                                                </h3>
                                                <div class="mt-2 flex justify-center">
                                                    <img :src="modalImageUrl"
                                                        class="max-w-full max-h-[70vh] object-contain"
                                                        alt="Foto Absensi Preview">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                        <button type="button" @click="showModal = false"
                                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
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
    </div>
    <script>
        function HitungHariIzin() {
            const tanggal = document.getElementById('tanggal').value;
            const container = document.getElementById('jumlah-hari-container');
            const jumlahHariElement = document.getElementById('jumlah-hari');

            if (tanggal) {
                jumlahHariElement.textContent = 1; // Jika satu tanggal dipilih, lama izin adalah 1 hari
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
                jumlahHariElement.textContent = '0'; // Atur kembali ke 0 atau kosong jika tanggal tidak dipilih
            }
        }
    </script>
</x-app-layout>
