<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Pengajuan Cuti') }}
            </h2>
        </x-slot>

        <div class="container mx-auto py-8">
            <div class="bg-white shadow-md rounded-md overflow-hidden">
                <div class="mt-4 mb-4 p-6">
                    <form id="cutiForm" enctype="multipart/form-data" method="POST"
                        action="{{ route('pengajuan_cuti.store') }}">
                        @csrf
                        {{-- Nama Cuti --}}
                        <div>
                            <x-input-label for="nama_cuti" :value="__('Nama Cuti')" />
                            <x-text-input id="nama_cuti" class="block mt-1 w-full" type="text" name="nama_cuti"
                                :value="old('nama_cuti')" required />
                            <x-input-error :messages="$errors->get('nama_cuti')" class="mt-2" />
                        </div>

                        {{-- Jenis Cuti --}}
                        <div class="mt-4">
                            <x-input-label for="jenis_cuti" :value="__('Pilih Cuti *')" />
                            <select name="jenis_cuti" id="jenis_cuti" required
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                onchange="toggleCutiWarning()">
                                <option value="" disabled selected>-- Pilih Cuti --</option>
                                <option value="Cuti Biasa">Cuti Biasa</option>
                                <option value="Cuti Spesial">Cuti Spesial</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_cuti')" class="mt-2" />
                            <div id="cuti-warning" class="hidden text-sm text-yellow-600 mt-2">
                                Catatan: Cuti Spesial tidak mengurangi hak cuti Anda
                            </div>
                        </div>

                        {{-- Tanggal Mulai Cuti --}}
                        <div class="mt-4">
                            <x-input-label for="tanggal_mulai_cuti" :value="__('Tanggal Mulai Cuti')" />
                            <x-text-input id="tanggal_mulai_cuti" class="block mt-1 w-full" type="date"
                                name="tanggal_mulai_cuti" :value="old('tanggal_mulai_cuti')" required onchange="hitungHariCuti()" />
                            <x-input-error :messages="$errors->get('tanggal_mulai_cuti')" class="mt-2" />
                        </div>

                        {{-- Tanggal Selesai Cuti --}}
                        <div class="mt-4">
                            <x-input-label for="tanggal_selesai_cuti" :value="__('Tanggal Selesai Cuti')" />
                            <x-text-input id="tanggal_selesai_cuti" class="block mt-1 w-full" type="date"
                                name="tanggal_selesai_cuti" :value="old('tanggal_selesai_cuti')" required onchange="hitungHariCuti()" />
                            <x-input-error :messages="$errors->get('tanggal_selesai_cuti')" class="mt-2" />
                        </div>

                        {{-- Tampilan Jumlah Hari --}}
                        <div id="jumlah-hari-container" class="hidden mt-4">
                            <p class="text-sm text-gray-600">
                                Lama cuti: <span id="jumlah-hari">0</span> hari
                            </p>
                            <p id="sisa-cuti-info" class="text-sm font-medium"></p>
                        </div>

                        {{-- Foto Cuti --}}
                        <div class="mt-4">
                            <x-input-label for="foto_cuti" :value="__('Foto Cuti (Opsional)')" />
                            <input id="foto_cuti" name="foto_cuti" type="file" class="block mt-1 w-full border p-2"
                                accept="image/*">
                            <x-input-error :messages="$errors->get('foto_cuti')" class="mt-2" />
                        </div>

                        {{-- Alasan Cuti --}}
                        <div class="mt-4">
                            <x-input-label for="alasan_cuti" :value="__('Alasan Cuti')" />
                            <textarea id="alasan_cuti" name="alasan_cuti" rows="3" required
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alasan_cuti') }}</textarea>
                            <x-input-error :messages="$errors->get('alasan_cuti')" class="mt-2" />
                        </div>

                        {{-- Sisa Hak Cuti --}}
                        <div class="mt-4">
                            <x-input-label for="sisa_hak_cuti" :value="__('Sisa Hak Cuti')" />
                            <div class="block mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                                {{ auth()->user()->sisa_hak_cuti }} hari
                            </div>
                            <input type="hidden" name="sisa_hak_cuti" value="{{ auth()->user()->sisa_hak_cuti }}">
                            <x-input-error :messages="$errors->get('sisa_hak_cuti')" class="mt-2" />
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <a href="{{ route('pengajuan_cuti.index') }}"
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
        </div>

        <script>
            function hitungHariCuti() {
                const mulai = document.getElementById('tanggal_mulai_cuti').value;
                const selesai = document.getElementById('tanggal_selesai_cuti').value;
                const container = document.getElementById('jumlah-hari-container');
                const jenisCuti = document.getElementById('jenis_cuti').value;

                if (mulai && selesai) {
                    const date1 = new Date(mulai);
                    const date2 = new Date(selesai);

                    // Hitung selisih hari (termasuk hari terakhir)
                    const timeDiff = date2.getTime() - date1.getTime();
                    const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;

                    document.getElementById('jumlah-hari').textContent = dayDiff;
                    container.classList.remove('hidden');

                    // Update sisa cuti info for regular leave
                    if (jenisCuti === 'Cuti Biasa') {
                        const sisaCuti = {{ auth()->user()->sisa_hak_cuti }};
                        const sisaInfo = document.getElementById('sisa-cuti-info');

                        if (dayDiff > sisaCuti) {
                            sisaInfo.textContent =
                                `Peringatan: Anda membutuhkan ${dayDiff} hari, sisa cuti hanya ${sisaCuti} hari`;
                            sisaInfo.className = 'text-sm font-medium text-red-600';
                        } else {
                            sisaInfo.textContent = `Sisa cuti setelah pengajuan: ${sisaCuti - dayDiff} hari`;
                            sisaInfo.className = 'text-sm font-medium text-green-600';
                        }
                    }
                } else {
                    container.classList.add('hidden');
                }
            }

            function toggleCutiWarning() {
                const jenisCuti = document.getElementById('jenis_cuti').value;
                const warningDiv = document.getElementById('cuti-warning');

                if (jenisCuti === 'Cuti Spesial') {
                    warningDiv.classList.remove('hidden');
                    document.getElementById('sisa-cuti-info').textContent = '';
                } else {
                    warningDiv.classList.add('hidden');
                    // Recalculate days if dates are already selected
                    if (document.getElementById('tanggal_mulai_cuti').value &&
                        document.getElementById('tanggal_selesai_cuti').value) {
                        hitungHariCuti();
                    }
                }
            }
        </script>
</x-app-layout>
