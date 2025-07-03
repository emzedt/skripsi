<x-app-layout>
    <div class="container mx-auto py-8">
        <x-slot name="header">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Edit Data Penggajian') }}
            </h2>
        </x-slot>

        <form action="{{ route('penggajian.update', $penggajian->id) }}" method="POST"
            class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">
                    Pilih Karyawan
                </label>
                <select name="user_id" id="user_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ $penggajian->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->nama }} ({{ $user->statusKaryawan->status_karyawan }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="periode_mulai">
                    Periode Mulai
                </label>
                <input type="date" name="periode_mulai" id="periode_mulai"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ $penggajian->periode_mulai->format('Y-m-d') }}" required>
                @error('periode_mulai')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="periode_selesai">
                    Periode Selesai
                </label>
                <input type="date" name="periode_selesai" id="periode_selesai"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ $penggajian->periode_selesai->format('Y-m-d') }}" required>
                @error('periode_selesai')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="gaji_diterima">
                    Gaji Diterima
                </label>
                <input type="number" name="gaji_diterima" id="gaji_diterima"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ $penggajian->gaji_diterima }}" required>
                @error('gaji_diterima')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="lembur">
                    Lembur
                </label>
                <input type="number" name="lembur" id="lembur"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ $penggajian->lembur }}">
                @error('lembur')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end space-x-2 mt-4">
                <a href="{{ route('penggajian.index') }}"
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
</x-app-layout>
