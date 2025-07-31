<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Permintaan Lembur ') }} {{ $user->nama }}
        </h2>
    </x-slot>
    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-md overflow-hidden">
            <div class="p-6"x-data="{
                currentImageUrl: '{{ $permintaanLembur->foto ? asset('storage/' . $permintaanLembur->foto) : '' }}',
                newImageUrl: null,
                showModal: false,
                modalImageUrl: '',
                openModal(url) {
                    this.modalImageUrl = url;
                    this.showModal = true;
                }
            }" x-cloak>
                <form action="{{ route('permintaan_lembur.update', $permintaanLembur->id) }}" method="POST"
                    enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="user_name" :value="__('Nama Karyawan')" />
                        <x-text-input id="user_name" class="block mt-1 w-full" type="text"
                            value="{{ $user->nama }}" readonly />
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                    </div>
                    <!-- Foto -->
                    <div class="mb-6">
                        <x-input-label for="foto" :value="__('Foto Permintaan Lembur')" />
                        <input id="foto" class="block mt-1 w-full border p-2 rounded-md" type="file"
                            name="foto" accept="image/*"
                            @change="newImageUrl = URL.createObjectURL($event.target.files[0])" />
                        <x-input-error :messages="$errors->get('foto')" class="mt-2" />

                        <!-- Current Image -->
                        <template x-if="currentImageUrl">
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-2">Foto Saat Ini:</p>
                                <img :src="currentImageUrl"
                                    class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                    @click="openModal(currentImageUrl)" alt="Foto Permintaan Lembur Saat Ini">
                            </div>
                        </template>

                        <!-- New Image Preview -->
                        <template x-if="newImageUrl">
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-2">Preview Foto Baru:</p>
                                <img :src="newImageUrl"
                                    class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                    @click="openModal(newImageUrl)" alt="Preview Foto Permintaan Lembur Baru">
                            </div>
                        </template>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_mulai" class="block text-gray-700 text-sm font-bold mb-2">Tanggal
                                Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                value="{{ $permintaanLembur->tanggal_mulai->format('Y-m-d') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required onchange="hitungLamaHari()">
                            <div class="text-xs text-gray-500 mt-1" id="lama_hari_text">- hari</div>
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-gray-700 text-sm font-bold mb-2">Tanggal
                                Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                value="{{ $permintaanLembur->tanggal_selesai->format('Y-m-d') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required onchange="hitungLamaHari()">
                        </div>
                        <div>
                            <label for="jam_mulai" class="block text-gray-700 text-sm font-bold mb-2">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="jam_mulai"
                                value="{{ $permintaanLembur->jam_mulai }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                        </div>
                        <div>
                            <label for="jam_akhir" class="block text-gray-700 text-sm font-bold mb-2">Jam Akhir</label>
                            <input type="time" name="jam_akhir" id="jam_akhir"
                                value="{{ $permintaanLembur->jam_akhir }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                        </div>
                        <div>
                            <label for="lama_lembur" class="block text-gray-700 text-sm font-bold mb-2">Lama
                                Lembur</label>
                            <input type="text" name="lama_lembur" id="lama_lembur" readonly
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 focus:outline-none focus:shadow-outline">
                        </div>
                        <!-- Jika select status disabled, kita tetap perlu mengirim nilainya -->
                        @if (!auth()->user()->isBossOf($permintaanLembur->user))
                            <input type="hidden" name="status" value="{{ $permintaanLembur->status }}">
                        @endif
                        <div>
                            <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                            <select name="status" id="status"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required @if (!auth()->user()->isBossOf($permintaanLembur->user)) disabled @endif>
                                <option value="Disetujui"
                                    {{ $permintaanLembur->status == 'Disetujui' ? 'selected' : '' }}>Disetujui
                                </option>
                                <option value="Ditolak" {{ $permintaanLembur->status == 'Ditolak' ? 'selected' : '' }}>
                                    Ditolak</option>
                                <option value="Menunggu"
                                    {{ $permintaanLembur->status == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                            </select>
                        </div>
                        <div class="col-span-full">
                            <label for="tugas" class="block text-gray-700 text-sm font-bold mb-2">Tugas</label>
                            <textarea name="tugas" id="tugas" rows="3"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>{{ $permintaanLembur->tugas }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('permintaan_lembur.index') }}"
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
                                            Preview Foto Permintaan Lembur
                                        </h3>
                                        <div class="mt-2 flex justify-center">
                                            <img :src="modalImageUrl" class="max-w-full max-h-[70vh] object-contain"
                                                alt="Foto Permintaan Lembur Preview">
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
        function hitungLamaLembur() {
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamAkhir = document.getElementById('jam_akhir').value;

            if (jamMulai && jamAkhir) {
                // Pisahkan jam dan menit
                const [mulaiJam, mulaiMenit] = jamMulai.split(':').map(Number);
                const [akhirJam, akhirMenit] = jamAkhir.split(':').map(Number);

                // Hitung total menit
                let totalMenit = (akhirJam * 60 + akhirMenit) - (mulaiJam * 60 + mulaiMenit);

                // Jika melewati tengah malam
                if (totalMenit < 0) {
                    totalMenit += 24 * 60; // Tambahkan 24 jam
                }

                // Hitung jam dan menit
                const jam = Math.floor(totalMenit / 60);
                const menit = totalMenit % 60;

                // Format output
                let hasil = '';
                if (jam > 0) hasil += `${jam} jam `;
                if (menit > 0) hasil += `${menit} menit`;
                if (jam === 0 && menit === 0) hasil = '0 jam';

                document.getElementById('lama_lembur').value = hasil;
            } else {
                document.getElementById('lama_lembur').value = '';
            }
        }

        function hitungLamaHari() {
            const tanggalMulai = document.getElementById('tanggal_mulai').value;
            const tanggalSelesai = document.getElementById('tanggal_selesai').value;

            if (tanggalMulai && tanggalSelesai) {
                const startDate = new Date(tanggalMulai);
                const endDate = new Date(tanggalSelesai);
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                document.getElementById('lama_hari_text').textContent = 'Lama hari: ' + diffDays + ' hari';
            } else {
                document.getElementById('lama_hari_text').textContent = '- hari';
            }
        }

        // Panggil fungsi saat halaman dimuat jika ada nilai default
        document.addEventListener('DOMContentLoaded', function() {
            hitungLamaLembur(), hitungLamaHari();
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</x-app-layout>
