<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Data Gaji Karyawan') }}
            </h2>
        </x-slot>

        <form action="{{ route('gaji.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @if (isset($user))
                @method('POST')
            @endif

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">
                    Pilih Karyawan
                </label>
                <select name="user_id" id="user_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" {{ isset($user) && $user->id == $u->id ? 'selected' : '' }}>
                            {{ $u->nama }} ({{ $u->statusKaryawan->status_karyawan }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="gaji-bulanan-section" class="mb-4 hidden">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="gaji_bulanan">
                        Gaji Bulanan
                    </label>
                    <input type="text" name="gaji_bulanan" id="gaji_bulanan"
                        value="{{ isset($user) && $user->gajiBulanan ? number_format($user->gajiBulanan->gaji_bulanan, 0, ',', '.') : '' }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        data-type="currency">
                </div>
            </div>

            <div id="gaji-harian-section" class="mb-4 hidden">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="gaji_harian">
                        Gaji Harian
                    </label>
                    <input type="text" name="gaji_harian" id="gaji_harian"
                        value="{{ isset($user) && $user->gajiHarian ? number_format($user->gajiHarian->gaji_harian, 0, ',', '.') : '' }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        data-type="currency">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="upah_makan_harian">
                        Upah Makan Harian
                    </label>
                    <input type="text" name="upah_makan_harian" id="upah_makan_harian"
                        value="{{ isset($user) && $user->gajiHarian ? number_format($user->gajiHarian->upah_makan_harian, 0, ',', '.') : '' }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        data-type="currency">
                </div>
            </div>


            <div id="lembur-section" class="mb-4 hidden">
                <h3 class="text-lg font-semibold mb-2">Upah Lembur</h3>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="upah_lembur_per_jam">
                        Upah Lembur per Jam
                    </label>
                    <input type="text" name="upah_lembur_per_jam" id="upah_lembur_per_jam"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        data-type="currency">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="upah_lembur_over_5_jam">
                        Upah Lembur >5 Jam
                    </label>
                    <input type="text" name="upah_lembur_over_5_jam" id="upah_lembur_over_5_jam"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        data-type="currency">
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <a href="{{ route('gaji.index') }}"
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userSelect = document.getElementById('user_id');
            const gajiBulananSection = document.getElementById('gaji-bulanan-section');
            const gajiHarianSection = document.getElementById('gaji-harian-section');
            const lemburSection = document.getElementById('lembur-section');

            function toggleGajiFields() {
                const selectedOption = userSelect.options[userSelect.selectedIndex];
                const isKaryawanTetap = selectedOption.textContent.includes('Karyawan Tetap');

                if (isKaryawanTetap) {
                    gajiBulananSection.classList.remove('hidden');
                    gajiHarianSection.classList.add('hidden');
                    lemburSection.classList.add('hidden');
                } else {
                    gajiBulananSection.classList.add('hidden');
                    gajiHarianSection.classList.remove('hidden');
                    lemburSection.classList.remove('hidden');
                }
            }

            // Initial toggle based on selected user (for edit mode)
            if (userSelect.value) {
                toggleGajiFields();
            }

            // Toggle when user selection changes
            userSelect.addEventListener('change', toggleGajiFields);

            const currencyInputs = document.querySelectorAll('input[data-type="currency"]');

            function formatRupiah(value, prefix = 'Rp ') {
                const numberString = value.replace(/[^,\d]/g, '').toString();
                const split = numberString.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                const ribuan = split[0].substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    const separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix + rupiah;
            }

            currencyInputs.forEach(function(input) {
                input.addEventListener('keyup', function() {
                    this.value = formatRupiah(this.value);
                });

                input.addEventListener('blur', function() {
                    this.value = formatRupiah(this.value);
                });
            });
        });
    </script>
</x-app-layout>
