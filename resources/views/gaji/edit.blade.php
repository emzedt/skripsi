<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Data Gaji ') }} {{ $user->nama }}
            </h2>
        </x-slot>

        <form action="{{ route('gaji.update', $user->id) }}" method="POST"
            class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @if (isset($user))
                @method('PUT')
            @endif
            <div>
                <x-input-label for="user_id" :value="__('Nama Karyawan')" />
                <x-text-input id="user_id" class="block mt-1 w-full" type="text" name="user_id" :value="$user->nama"
                    readonly />
                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
            </div>

            @if ($user->isKaryawanHarian())
                <!-- Show monthly wage for permanent employees -->
                <div id="gaji-harian-section" class="mb-4">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="gaji_harian">
                            Gaji Harian
                        </label>
                        <input type="text" name="gaji_harian" id="gaji_harian"
                            value="{{ old('gaji_harian', $user->gajiHarian->gaji_harian) }}"
                            class="uang shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="upah_makan_harian">
                            Upah Makan Harian
                        </label>
                        <input type="text" name="upah_makan_harian" id="upah_makan_harian"
                            value="{{ old('upah_makan_harian', $user->gajiHarian->upah_makan_harian) }}"
                            class="uang shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-2">Upah Lembur</h3>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="upah_lembur_per_jam">
                            Upah Lembur per Jam
                        </label>
                        <input type="text" name="upah_lembur_per_jam" id="upah_lembur_per_jam"
                            value="{{ old('upah_lembur_per_jam', $user->lembur->upah_lembur_per_jam) }}"
                            class="uang shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="upah_lembur_over_5_jam">
                            Upah Lembur >5 Jam
                        </label>
                        <input type="text" name="upah_lembur_over_5_jam" id="upah_lembur_over_5_jam"
                            value="{{ old('upah_lembur_over_5_jam', $user->lembur->upah_lembur_over_5_jam) }}"
                            class="uang shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>
                </div>
            @else
                <div id="gaji-bulanan-section" class="mb-4">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="gaji_bulanan">
                            Gaji Bulanan
                        </label>
                        <input type="text" name="gaji_bulanan" id="gaji_bulanan"
                            value="{{ old('gaji_bulanan', $user->gajiBulanan->gaji_bulanan) }}"
                            class="uang shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>
            @endif

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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.uang').mask('000.000.000.000.000', {
                reverse: true
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const userSelect = document.getElementById('user_id');
            const gajiBulananSection = document.getElementById('gaji-bulanan-section');
            const gajiHarianSection = document.getElementById('gaji-harian-section');

            function toggleGajiFields() {
                const selectedOption = userSelect.options[userSelect.selectedIndex];
                const isKaryawanTetap = selectedOption.textContent.includes('Karyawan Tetap');

                if (isKaryawanTetap) {
                    gajiBulananSection.classList.remove('hidden');
                    gajiHarianSection.classList.add('hidden');
                } else {
                    gajiBulananSection.classList.add('hidden');
                    gajiHarianSection.classList.remove('hidden');
                }
            }

            // Initial toggle based on selected user (for edit mode)
            if (userSelect.value) {
                toggleGajiFields();
            }

            // Toggle when user selection changes
            userSelect.addEventListener('change', toggleGajiFields);
        });
    </script>
</x-app-layout>
