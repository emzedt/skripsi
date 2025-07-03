<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Absensi Sales') }}
            </h2>
        </x-slot>

        <div class="py-8" x-data="{
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
                if (!this.fotoBase64) {
                    Swal.fire({
                        title: 'Foto belum diambil!',
                        text: 'Tetap simpan absensi tanpa foto?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, simpan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$refs.formAbsensi.submit();
                        }
                    });
                } else {
                    this.$refs.formAbsensi.submit();
                }
            }
        }" x-init="initCamera()">
            <form x-ref="formAbsensi" enctype="multipart/form-data" method="POST" @submit.prevent="handleSubmit"
                action="{{ route('absensi_sales.store') }}" class="bg-white shadow-sm rounded-lg p-6">
                @csrf

                <!-- Tanggal -->
                <div class="mb-6">
                    <x-input-label for="tanggal" :value="__('Tanggal')" />
                    <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal"
                        value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly />
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                </div>

                <div class="mb-6">
                    <x-input-label for="jam" :value="__('Jam')" />
                    <x-text-input id="jam" class="block mt-1 w-full" type="time" name="jam"
                        value="{{ \Carbon\Carbon::now()->format('H:i') }}" readonly />
                    <x-input-error :messages="$errors->get('jam')" class="mt-2" />
                </div>

                <input type="hidden" name="foto_base64" x-model="fotoBase64">
                <!-- Foto -->
                <div class="mb-6">
                    <x-input-label :value="__('Foto Selfie Absensi Sales')" />
                    <div class="flex flex-col items-center gap-4">
                        <!-- Video live -->
                        <video id="video" autoplay playsinline class="border rounded-lg" width="500"></video>

                        <!-- Tombol ambil foto -->
                        <button type="button" @click="capturePhoto()"
                            class="mt-2 px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">Ambil Foto</button>


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

                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status"
                        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300 focus:ring-opacity-50"
                        required>
                        <option value="" selected disabled>-- Pilih Status --</option>
                        <option value="Titip Brosur">Titip Brosur</option>
                        <option value="Meeting">Meeting</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        placeholder="Keterangan absensi" required>{{ old('deskripsi') }}</textarea>
                    <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
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
                @keydown.escape.window="showModal = false" style="display: none;">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Overlay -->
                    <div x-show="showModal" x-transition.opacity
                        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false">
                    </div>
                    <!-- Modal Content -->
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
</x-app-layout>
