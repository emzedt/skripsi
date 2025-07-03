<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pengajuan Sakit') }}
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
                <form action="{{ route('sakit.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <x-input-label for="tanggal_mulai" :value="__('Tanggal Mulai')" />
                        <x-text-input id="tanggal_mulai" class="block mt-1 w-full" type="date" name="tanggal_mulai"
                            :value="old('tanggal_mulai')" onchange="HitungHariSakit()" />
                        <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="tanggal_selesai" :value="__('Tanggal Selesai')" />
                        <x-text-input id="tanggal_selesai" class="block mt-1 w-full" type="date"
                            name="tanggal_selesai" :value="old('tanggal_selesai')" onchange="HitungHariSakit()" />
                        <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                    </div>

                    <div id="jumlah-hari-container" class="hidden">
                        <p class="text-sm text-gray-600 mt-2">
                            Lama sakit: <span id="jumlah-hari">0</span> hari
                        </p>
                    </div>

                    <div class="mb-6 mt-3">
                        <x-input-label for="surat_dokter" :value="__('Foto Surat Dokter')" />
                        <input id="surat_dokter" class="block mt-1 w-full border p-2 rounded-md" type="file"
                            name="surat_dokter" accept="image/*" required
                            @change="newImageUrl = URL.createObjectURL($event.target.files[0])" />
                        <x-input-error :messages="$errors->get('surat_dokter')" class="mt-2" />

                        <!-- New Image Preview -->
                        <template x-if="newImageUrl">
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-2">Preview Foto:</p>
                                <img :src="newImageUrl"
                                    class="h-24 w-24 object-cover rounded-md cursor-pointer border border-gray-200"
                                    @click="openModal(newImageUrl)" alt="Preview Foto Surat Dokter">
                            </div>
                        </template>
                    </div>

                    <div>
                        <x-input-label for="diagnosa" :value="__('Diagnosa')" />
                        <textarea id="diagnosa" name="diagnosa" rows="3"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('diagnosa') }}</textarea>
                        <x-input-error :messages="$errors->get('diagnosa')" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('sakit.index') }}"
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
                                                    alt="Foto Surat Dokter Preview">
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
        function HitungHariSakit() {
            const mulai = document.getElementById('tanggal_mulai').value;
            const selesai = document.getElementById('tanggal_selesai').value;
            const container = document.getElementById('jumlah-hari-container');

            if (mulai && selesai) {
                const date1 = new Date(mulai);
                const date2 = new Date(selesai);

                // Hitung selisih hari (termasuk hari terakhir)
                const timeDiff = date2.getTime() - date1.getTime();
                const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;

                document.getElementById('jumlah-hari').textContent = dayDiff;
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
