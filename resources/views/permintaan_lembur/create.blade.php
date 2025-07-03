<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Tambah Permintaan Lembur') }}
        </h2>
    </x-slot>
    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-md overflow-hidden">
            <div class="mt-4 mb-4 p-6" x-data="{
                newImageUrl: null,
                fotoBase64: '',
                latitude: '',
                longitude: '',
                locationName: '',
                namaUser: '{{ auth()->user()->nama }}',
                showModal: false,
                modalImageUrl: '',
                async initCamera() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                        document.getElementById('video').srcObject = stream;
                    } catch (e) {
                        alert('Gagal akses kamera: ' + e.message);
                    }
                },
                async capturePhoto() {
                    const video = document.getElementById('video');
                    const canvas = document.getElementById('canvas');
                    const ctx = canvas.getContext('2d');

                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const now = new Date();
                    const jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    const tanggal = now.toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' });
                    const hari = now.toLocaleDateString('id-ID', { weekday: 'long' });

                    await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(async pos => {
                            this.latitude = pos.coords.latitude;
                            this.longitude = pos.coords.longitude;

                            try {
                                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.latitude}&lon=${this.longitude}`);
                                const data = await response.json();
                                this.locationName = data.display_name;
                            } catch (error) {
                                console.error('Gagal ambil nama lokasi:', error);
                                this.locationName = '';
                            }

                            resolve();
                        }, err => {
                            alert('Gagal ambil lokasi: ' + err.message);
                            reject();
                        });
                    });

                    // Tulis teks ke canvas
                    ctx.fillStyle = 'white';
                    ctx.font = '40px Helvetica';

                    ctx.strokeText(jam, 20, 60);
                    ctx.fillText(jam, 20, 60);

                    ctx.font = '24px Helvetica';
                    ctx.strokeText(`${tanggal} ${hari}`, 20, 100);
                    ctx.fillText(`${tanggal} ${hari}`, 20, 100);

                    ctx.font = '15px Helvetica';
                    const lokasiY = canvas.height - 30;
                    ctx.strokeText(this.locationName, 20, lokasiY);
                    ctx.fillText(this.locationName, 20, lokasiY);

                    const userY = canvas.height - 60;
                    ctx.strokeText(this.namaUser, 20, userY);
                    ctx.fillText(this.namaUser, 20, userY);

                    const dataUrl = canvas.toDataURL('image/jpeg');
                    this.newImageUrl = dataUrl;
                    this.fotoBase64 = dataUrl;
                },
                openModal(imageUrl) {
                    this.modalImageUrl = imageUrl;
                    this.showModal = true;
                },
                handleSubmit() {
                    if (this.fotoBase64) {
                        this.$refs.formAbsensi.submit();
                    };
                }
            }" x-init="initCamera()">
                <form action="{{ route('permintaan_lembur.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="user_name" :value="__('Nama Karyawan')" />
                        <x-text-input id="user_name" class="block mt-1 w-full" type="text"
                            value="{{ $currentUser->nama }}" readonly />
                        <input type="hidden" name="user_id" value="{{ $currentUser->id }}">
                        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                    </div>
                    <input type="hidden" name="foto_base64" x-model="fotoBase64">
                    <!-- Foto -->
                    <div class="mb-6">
                        <x-input-label :value="__('Foto Selfie Permintaan Lembur')" />
                        <div class="flex flex-col items-center gap-4">
                            <!-- Video live -->
                            <video id="video" autoplay playsinline class="border rounded-lg" width="500"></video>

                            <!-- Tombol ambil foto -->
                            <button type="button" @click="capturePhoto()"
                                class="mt-2 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded">Ambil
                                Foto</button>


                            <!-- Canvas tersembunyi -->
                            <canvas id="canvas" style="display:none;"></canvas>

                            <!-- Preview foto -->
                            <template x-if="newImageUrl">
                                <div class="mt-4">
                                    <h3 class="text-lg font-semibold mb-2">Preview Foto</h3>
                                    <img :src="newImageUrl"
                                        class="border rounded-lg cursor-pointer hover:scale-105 transition w-48 h-48 object-cover"
                                        @click="openModal(newImageUrl)" alt="Hasil Foto">
                                </div>
                            </template>
                        </div>
                        <x-input-error :messages="$errors->get('foto_base64')" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_mulai" class="block text-gray-700 text-sm font-bold mb-2">Tanggal
                                Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required onchange="hitungLamaHari()">
                            <div class="text-xs text-gray-500 mt-1" id="lama_hari_text">- hari</div>
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-gray-700 text-sm font-bold mb-2">Tanggal
                                Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required onchange="hitungLamaHari()">
                        </div>
                        <div>
                            <label for="jam_mulai" class="block text-gray-700 text-sm font-bold mb-2">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="jam_mulai"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required onchange="hitungLamaLembur()">
                        </div>
                        <div>
                            <label for="jam_akhir" class="block text-gray-700 text-sm font-bold mb-2">Jam Akhir</label>
                            <input type="time" name="jam_akhir" id="jam_akhir"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required onchange="hitungLamaLembur()">
                        </div>
                        <div>
                            <label for="lama_lembur" class="block text-gray-700 text-sm font-bold mb-2">Lama
                                Lembur</label>
                            <input type="text" name="lama_lembur" id="lama_lembur" readonly
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="col-span-full">
                            <label for="tugas" class="block text-gray-700 text-sm font-bold mb-2">Tugas</label>
                            <textarea name="tugas" id="tugas" rows="3"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required></textarea>
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
                                                Preview Foto Selfie Permintaan Lembur
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
</x-app-layout>
