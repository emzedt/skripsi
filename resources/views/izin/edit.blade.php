<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Pengajuan Izin') }}
            </h2>
        </x-slot>

        <div class="container mx-auto py-8" x-data="{
            currentImageUrl: '{{ $izin->dokumen_pendukung ? asset('storage/' . $izin->dokumen_pendukung) : '' }}',
            newImageUrl: null,
            showModal: false,
            modalImageUrl: '',
            openModal(url) {
                this.modalImageUrl = url;
                this.showModal = true;
            }
        }">

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                <form action="{{ route('izin.update', $izin->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Pengajuan Izin</h3>
                        <div>
                            <x-input-label for="nama" :value="__('Nama Karyawan')" />
                            <x-text-input id="nama" class="block mt-1 w-full bg-gray-100" type="text"
                                value="{{ $izin->user->nama }}" readonly />
                        </div>
                        <div>
                            <x-input-label for="tanggal" :value="__('Tanggal')" />
                            <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal"
                                :value="old('tanggal', $izin->tanggal->format('Y-m-d'))" onchange="HitungHariIzin()" />
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>

                        <div id="jumlah-hari-container" class="hidden">
                            <p class="text-sm text-gray-600 mt-2">
                                Lama izin: <span id="jumlah-hari">0</span> hari
                            </p>
                        </div>

                        <div>
                            <x-input-label for="jenis_izin" :value="__('Jenis Izin')" />
                            <select id="jenis_izin" name="jenis_izin"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300 focus:ring-opacity-50"
                                required>
                                <option value="" disabled selected>-- Pilih Izin --</option>
                                <option value="Satu Hari"
                                    {{ old('jenis_izin', $izin->jenis_izin) == 'Satu Hari' ? 'selected' : '' }}>
                                    Satu Hari (08:00-17:00)</option>
                                <option value="Setengah Hari Pagi"
                                    {{ old('jenis_izin', $izin->jenis_izin) == 'Setengah Hari Pagi' ? 'selected' : '' }}>
                                    Setengah Hari Pagi (08:00-12:00)</option>
                                <option value="Setengah Hari Siang"
                                    {{ old('jenis_izin', $izin->jenis_izin) == 'Setengah Hari Siang' ? 'selected' : '' }}>
                                    Setengah Hari Siang (13:00-17:00)</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_izin')" class="mt-2" />
                        </div>

                        <!-- Foto -->
                        <div class="mb-6">
                            <x-input-label for="dokumen_pendukung" :value="__('Foto Dokumen Pendukung (Opsional)')" />
                            <input id="dokumen_pendukung" class="block mt-1 w-full border p-2 rounded-md" type="file"
                                name="dokumen_pendukung" accept="image/*"
                                @change="newImageUrl = URL.createObjectURL($event.target.files[0])" />
                            <x-input-error :messages="$errors->get('dokumen_pendukung')" class="mt-2" />

                            <!-- Current Image -->
                            <template x-if="currentImageUrl">
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 mb-2">Foto Surat Dokter Saat Ini:</p>
                                    <img :src="currentImageUrl"
                                        class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                        @click="openModal(currentImageUrl)" alt="Foto Dokumen Pendukung Saat Ini">
                                </div>
                            </template>

                            <!-- New Image Preview -->
                            <template x-if="newImageUrl">
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 mb-2">Preview Foto Surat Dokter Baru:</p>
                                    <img :src="newImageUrl"
                                        class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                        @click="openModal(newImageUrl)" alt="Preview Foto Dokumen Pendukung Baru">
                                </div>
                            </template>
                        </div>

                        <div>
                            <x-input-label for="alasan" :value="__('Alasan')" />
                            <textarea id="alasan" name="alasan" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alasan', $izin->alasan) }}</textarea>
                            <x-input-error :messages="$errors->get('alasan')" class="mt-2" />
                        </div>
                    </div>
                    <!-- Form Persetujuan -->
                    <div class="space-y-4 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900">Form Persetujuan</h3>
                        <div>
                            <x-input-label for="status" :value="__('Status Persetujuan *')" />
                            <select name="status" id="status" required
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" disabled>-- Pilih Status --</option>
                                <option value="Disetujui"
                                    {{ old('status', $izin->status) == 'Disetujui' ? 'selected' : '' }}>
                                    Disetujui
                                </option>
                                <option value="Ditolak"
                                    {{ old('status', $izin->status) == 'Ditolak' ? 'selected' : '' }}>
                                    Ditolak
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="alasan_persetujuan" :value="__('Alasan/Keterangan *')" />
                            <textarea id="alasan_persetujuan" name="alasan_persetujuan" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Berikan alasan persetujuan/penolakan">{{ old('alasan_persetujuan', $izin->alasan_persetujuan) }}</textarea>
                            <x-input-error :messages="$errors->get('alasan_persetujuan')" class="mt-2" />
                        </div>
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
                                            Preview Foto Surat Dokterr
                                        </h3>
                                        <div class="mt-2 flex justify-center">
                                            <img :src="modalImageUrl" class="max-w-full max-h-[70vh] object-contain"
                                                alt="Foto Surat Dokter Preview">
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
