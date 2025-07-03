<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Permintaan Lembur') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-md overflow-hidden">
            <div class="p-6" x-data="{
                showModal: false,
                modalImageUrl: '',
                openModal(url) {
                    this.modalImageUrl = url;
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                }
            }" x-cloak>
                <!-- Informasi Karyawan -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Karyawan</h3>
                    <div class="space-y-4">
                        <div>
                            <x-input-label :value="__('Nama Karyawan')" />
                            <x-text-input class="block mt-1 w-full bg-gray-100"
                                value="{{ $permintaanLembur->user->nama }}" readonly />
                        </div>
                    </div>
                </div>
                <!-- Detail Lembur -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 mt-4">Detail Lembur</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label :value="__('Tanggal Mulai')" />
                                <x-text-input class="block mt-1 w-full bg-gray-100"
                                    value="{{ $permintaanLembur->tanggal_mulai->format('d F Y') }}" readonly />
                            </div>
                            <div>
                                <x-input-label :value="__('Tanggal Selesai')" />
                                <x-text-input class="block mt-1 w-full bg-gray-100"
                                    value="{{ $permintaanLembur->tanggal_selesai->format('d F Y') }}" readonly />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label :value="__('Jam Mulai')" />
                                <x-text-input class="block mt-1 w-full bg-gray-100"
                                    value="{{ $permintaanLembur->jam_mulai ? \Carbon\Carbon::parse($permintaanLembur->jam_mulai)->format('H:i') : '-' }}"
                                    readonly />
                            </div>
                            <div>
                                <x-input-label :value="__('Jam Selesai')" />
                                <x-text-input type="text" class="block mt-1 w-full bg-gray-100"
                                    value="{{ $permintaanLembur->jam_akhir ? \Carbon\Carbon::parse($permintaanLembur->jam_akhir)->format('H:i') : '-' }}"
                                    readonly />
                            </div>
                        </div>
                        <div>
                            <x-input-label :value="__('Lama Lembur')" />
                            <x-text-input class="block mt-1 w-full bg-gray-100"
                                value="{{ $permintaanLembur->lama_lembur / 60 }} jam" readonly />
                        </div>
                        <div>
                            <x-input-label :value="__('Status')" />
                            <x-text-input class="block mt-1 w-full bg-gray-100" value="{{ $permintaanLembur->status }}"
                                readonly />
                        </div>
                    </div>
                </div>

                <!-- Foto dan Tugas -->
                <div class="col-span-full">
                    <div>
                        <x-input-label :value="__('Foto Lembur')" />
                        @if ($permintaanLembur->foto)
                            <div class="mt-2">
                                <img src="{{ asset('storage/lembur/' . $permintaanLembur->foto) }}" alt="Dokumen Cuti"
                                    class="h-32 object-contain rounded cursor-pointer hover:opacity-80 transition"
                                    onclick="showImageModal('{{ asset('storage/lembur/' . $permintaanLembur->foto) }}')">
                                <p class="text-sm text-gray-500 mt-1">Klik foto untuk memperbesar</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mt-2">Tidak ada foto</p>
                        @endif
                    </div>

                    <div>
                        <x-input-label :value="__('Tugas')" />
                        <textarea rows="4" class="block mt-1 w-full border-gray-300 bg-gray-100 rounded-md shadow-sm" readonly>{{ $permintaanLembur->tugas }}</textarea>
                    </div>
                </div>

            </div>
            <div class="flex justify-end mt-6 px-6 pb-6">
                <a href="{{ route('permintaan_lembur.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                    Kembali
                </a>
            </div>


        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75 p-4 flex items-center justify-center">
        <div
            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full max-w-2xl">
            <!-- Tombol close -->
            <button onclick="hideImageModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Konten Modal -->
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <div class="text-center sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Preview Foto Selfie Permintaan Lembur
                    </h3>
                    <div class="mt-2 flex justify-center">
                        <img id="modalImage" src="" alt="Foto Permintaan Lembur"
                            class="max-w-full max-h-[70vh] object-contain rounded">
                    </div>
                </div>
            </div>

            <!-- Tombol Tutup -->
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button onclick="hideImageModal()"
                    class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script>
        // Fungsi untuk menampilkan modal gambar
        function showImageModal(imageUrl) {
            document.getElementById('modalImage').src = imageUrl;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        // Fungsi untuk menyembunyikan modal gambar
        function hideImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Tutup modal saat klik di luar gambar
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideImageModal();
            }
        });

        // Tutup modal dengan tombol ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideImageModal();
            }
        });
    </script>
</x-app-layout>
