<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Karyawan') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-md overflow-hidden">
            <div class="mt-4 mb-4 p-6">
                <form method="POST" action="{{ route('karyawan.store') }}" class="space-y-2">
                    @csrf
                    <div>
                        <x-input-label for="nama" :value="__('Nama')" />
                        <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama"
                            :value="old('nama')" />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="text" name="email"
                            :value="old('email')" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="text" name="password"
                            :value="old('password')" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="no_hp" :value="__('No. HP')" />
                        <x-text-input id="no_hp" class="block mt-1 w-full" type="number" name="no_hp"
                            :value="old('no_hp')" />
                        <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="no_rekening" :value="__('No. Rekening')" />
                        <x-text-input id="no_rekening" class="block mt-1 w-full" type="number" name="no_rekening"
                            :value="old('no_rekening')" />
                        <x-input-error :messages="$errors->get('no_rekening')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="jabatan" :value="__('Nama Jabatan')" />
                        <select id="jabatan" name="jabatan_id"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300 focus:ring-opacity-50"
                            required>
                            <option value="" disabled selected>-- Pilih Jabatan --</option>
                            @foreach ($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}">{{ $jabatan->nama }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('jabatan_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="hak_cuti" :value="__('Hak Cuti')" />
                        <select id="hak_cuti" name="hak_cuti_id"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300 focus:ring-opacity-50"
                            required>
                            <option value="" disabled selected>-- Pilih Hak Cuti --</option>
                            @foreach ($hakCutis as $hakCuti)
                                <option value="{{ $hakCuti->id }}">
                                    {{ 'Hak Cuti: ' . $hakCuti->hak_cuti . ' hari dan ' . 'Hak Cuti Bersama: ' . $hakCuti->hak_cuti_bersama }}
                                    hari</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('hak_cuti_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="status_karyawan" :value="__('Status Karyawan')" />
                        <select id="status_karyawan" name="status_karyawan_id"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300 focus:ring-opacity-50"
                            required>
                            <option value="" disabled selected>-- Pilih Status Karyawan --</option>
                            @foreach ($statusKaryawans as $status)
                                <option value="{{ $status->id }}">{{ $status->status_karyawan }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status_karyawan_id')" class="mt-2" />
                    </div>
                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('karyawan.index') }}"
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
</x-app-layout>
