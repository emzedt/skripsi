<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil Lainnya') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Informasi status karyawan, gaji, lembur, dan sisa hak cuti pada akun Anda') }}
        </p>
    </header>

    <form method="get" class="mt-6 space-y-6">
        @csrf

        <div>
            <x-input-label for="status_karyawan" :value="__('Status Karyawan')" />
            <x-text-input id="status_karyawan" name="status_karyawan" type="text" class="mt-1 block w-full"
                :value="old('status_karyawan', $user->status_karyawan ?? 'Tidak ada')" required autofocus autocomplete="status_karyawan" disabled />
            <x-input-error class="mt-2" :messages="$errors->get('status_karyawan')" />
        </div>

        @if ($user->statusKaryawan && $user->statusKaryawan->status_karyawan === 'Karyawan Tetap')
            <div>
                <x-input-label for="gaji_bulanan" :value="__('Gaji Bulanan')" />
                <x-text-input id="gaji_bulanan" name="gaji_bulanan" type="text" class="mt-1 block w-full"
                    :value="old('gaji_bulanan', optional($user->gajiBulanan)->gaji_bulanan ?? 'Tidak ada')" required autofocus autocomplete="gaji_bulanan" disabled />
                <x-input-error class="mt-2" :messages="$errors->get('gaji_bulanan')" />
            </div>
        @else
            <div>
                <x-input-label for="gaji_harian" :value="__('Gaji Harian')" />
                <x-text-input id="gaji_harian" class="block mt-1 w-full" type="number" name="gaji_harian"
                    :value="old('gaji_harian', optional($user->gajiHarian)->gaji_harian ?? 'Tidak ada')" disabled />
                <x-input-error :messages="$errors->get('gaji_harian')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="upah_lembur_per_jam" :value="__('Upah Lembur per Jam')" />
                <x-text-input id="upah_lembur_per_jam" class="block mt-1 w-full" type="number"
                    name="upah_lembur_per_jam" :value="old('upah_lembur_per_jam', optional($user->lembur)->upah_lembur_per_jam ?? 'Tidak ada')" disabled />
                <x-input-error :messages="$errors->get('upah_lembur_per_jam')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="upah_lembur_over_5_jam" :value="__('Upah Lembur Over 5 Jam')" />
                <x-text-input id="upah_lembur_over_5_jam" class="block mt-1 w-full" type="number"
                    name="upah_lembur_over_5_jam" :value="old('upah_lembur_over_5_jam', $user->upah_lembur_over_5_jam ?? 'Tidak ada')" disabled />
                <x-input-error :messages="$errors->get('upah_lembur_over_5_jam')" class="mt-2" />
            </div>
        @endif

        <div>
            <x-input-label for="sisa_hak_cuti" :value="__('Sisa Hak Cuti')" />
            <x-text-input id="sisa_hak_cuti" class="block mt-1 w-full" type="number" name="sisa_hak_cuti"
                :value="old('sisa_hak_cuti', $user->sisa_hak_cuti ?? 'Tidak ada')" disabled />
            <x-input-error :messages="$errors->get('sisa_hak_cuti')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="sisa_hak_cuti_bersama" :value="__('Sisa Hak Cuti Bersama')" />
            <x-text-input id="sisa_hak_cuti_bersama" class="block mt-1 w-full" type="number"
                name="sisa_hak_cuti_bersama" :value="old('sisa_hak_cuti_bersama', $user->sisa_hak_cuti_bersama ?? 'Tidak ada')" disabled />
            <x-input-error :messages="$errors->get('sisa_hak_cuti_bersama')" class="mt-2" />
        </div>
    </form>
</section>
