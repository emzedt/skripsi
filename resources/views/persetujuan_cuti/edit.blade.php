<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 py-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Persetujuan Cuti') }}
            </h2>
        </x-slot>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form enctype="multipart/form-data" method="POST" action="{{ route('persetujuan_cuti.update', $cuti->id) }}">
                @csrf
                @method('PUT')

                <!-- Informasi Cuti (Read-only) -->
                <div class="space-y-4 mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Pengajuan Cuti</h3>

                    <div>
                        <x-input-label for="nama" :value="__('Nama Karyawan')" />
                        <x-text-input id="nama" class="block mt-1 w-full cursor-auto" type="text"
                            value="{{ $cuti->user->nama }}" readonly />
                    </div>

                    <div>
                        <x-input-label for="nama_cuti" :value="__('Nama Cuti')" />
                        <x-text-input id="nama_cuti" class="block mt-1 w-full cursor-auto" type="text"
                            value="{{ $cuti->nama_cuti }}" readonly />
                    </div>

                    {{-- Jenis Cuti --}}
                    <div>
                        <x-input-label for="jenis_cuti" :value="__('Jenis Cuti')" />
                        <x-text-input id="jenis_cuti" class="block mt-1 w-full cursor-auto" type="text"
                            name="jenis_cuti" value="{{ $cuti->jenis_cuti }}" readonly />
                        <x-input-error :messages="$errors->get('jenis_cuti')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="tanggal_mulai_cuti" :value="__('Tanggal Mulai')" />
                            <x-text-input id="tanggal_mulai_cuti" class="block mt-1 w-full cursor-auto" type="text"
                                value="{{ \Carbon\Carbon::parse($cuti->tanggal_mulai_cuti)->translatedFormat('d F Y') }}"
                                readonly onchange="hitungHariCuti()" />
                        </div>
                        <div>
                            <x-input-label for="tanggal_selesai_cuti" :value="__('Tanggal Selesai')" />
                            <x-text-input id="tanggal_selesai_cuti" class="block mt-1 w-full cursor-auto" type="text"
                                value="{{ \Carbon\Carbon::parse($cuti->tanggal_selesai_cuti)->translatedFormat('d F Y') }}"
                                readonly onchange="hitungHariCuti()" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="lama_cuti" :value="__('Lama Cuti')" />
                        <x-text-input id="lama_cuti" class="block mt-1 w-full cursor-auto" type="text"
                            value="{{ $days }} hari" readonly />
                    </div>

                    <div>
                        <x-input-label for="alasan_cuti" :value="__('Alasan Cuti')" />
                        <textarea id="alasan_cuti" rows="3" readonly class="block mt-1 w-full cursor-auto rounded-md border-gray-300">{{ $cuti->alasan_cuti }}</textarea>
                    </div>

                    <div>
                        <x-input-label for="sisa_hak_cuti" :value="__('Sisa Hak Cuti')" />
                        <x-text-input id="sisa_hak_cuti" class="block mt-1 w-full cursor-auto" type="text"
                            value="{{ $cuti->user->sisa_hak_cuti }} hari" readonly />
                    </div>

                    <div>
                        <x-input-label :value="__('Foto Cuti')" />
                        @if ($cuti->foto_cuti)
                            <div class="mt-2">
                                <img src="{{ asset('storage/cuti/' . $cuti->foto_cuti) }}" alt="Dokumen Cuti"
                                    class="h-32 object-contain rounded cursor-pointer hover:opacity-80 transition"
                                    onclick="showImageModal('{{ asset('storage/cuti/' . $cuti->foto_cuti) }}')">
                                <p class="text-sm text-gray-500 mt-1">Klik foto untuk memperbesar</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mt-2">Tidak ada foto</p>
                        @endif
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
                                {{ old('status', $cuti->status) == 'Disetujui' ? 'selected' : '' }}>
                                Disetujui
                            </option>
                            <option value="Ditolak" {{ old('status', $cuti->status) == 'Ditolak' ? 'selected' : '' }}>
                                Ditolak
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="alasan_persetujuan_cuti" :value="__('Alasan/Keterangan *')" />
                        <textarea id="alasan_persetujuan_cuti" name="alasan_persetujuan_cuti" rows="3" required
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            placeholder="Berikan alasan persetujuan/penolakan">{{ old('alasan_persetujuan_cuti', $cuti->alasan_persetujuan_cuti) }}</textarea>
                        <x-input-error :messages="$errors->get('alasan_persetujuan_cuti')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <a href="{{ route('persetujuan_cuti.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-75 p-4">
        <div class="relative max-w-4xl w-full">
            <button onclick="hideImageModal()" class="absolute top-2 right-2 text-white hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img id="modalImage" src="" alt="Dokumen Cuti" class="max-h-[80vh] w-full object-contain rounded">
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
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

        document.addEventListener('DOMContentLoaded', hitungHariCuti);
    </script>
</x-app-layout>
