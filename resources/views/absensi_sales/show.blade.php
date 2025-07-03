<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Absensi Sales') }}
            </h2>
        </x-slot>

        <div class="mt-4 bg-white shadow-sm rounded-lg p-6" x-data="{
            showModal: false,
            modalImageUrl: '',
            openModal(url) {
                this.modalImageUrl = url;
                this.showModal = true;
            }
        }" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Informasi Utama -->
                <div class="space-y-4">
                    <!-- Informasi Utama -->
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Informasi Absensi</h3>
                            <div class="mt-4 space-y-4">
                                <!-- Nama Karyawan -->
                                <div>
                                    <x-input-label for="karyawan" :value="__('Nama Karyawan')" />
                                    <x-text-input id="karyawan" class="block mt-1 w-full bg-gray-100" type="text"
                                        value="{{ $absensiSales->user->nama }}" readonly />
                                </div>

                                <!-- Tanggal -->
                                <div>
                                    <x-input-label for="tanggal" :value="__('Tanggal')" />
                                    <x-text-input id="tanggal" class="block mt-1 w-full bg-gray-100" type="text"
                                        value="{{ $absensiSales->tanggal->format('d F Y') }}" readonly />
                                </div>

                                <!-- Jam -->
                                <div>
                                    <x-input-label for="jam" :value="__('Jam')" />
                                    <x-text-input id="jam" class="block mt-1 w-full bg-gray-100" type="text"
                                        value="{{ $absensiSales->jam ? \Carbon\Carbon::parse($absensiSales->jam)->format('H:i') : '-' }}"
                                        readonly />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <x-text-input id="status" class="block mt-1 w-full bg-gray-100" type="text"
                                        value="{{ $absensiSales->status }}" readonly />
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                            <div class="mt-1 p-3 bg-gray-50 rounded-md border border-gray-200 min-h-[100px]">
                                {{ $absensiSales->deskripsi ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Foto Absensi -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">Foto Absensi</h3>
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if ($absensiSales->foto)
                            <div class="flex-1">
                                <x-input-label :value="__('Foto Absensi Sales')" />
                                <div class="mt-1 border rounded-md p-2">
                                    <img src="{{ asset('storage/absensi_sales/' . $absensiSales->foto) }}"
                                        alt="Foto Absensi Sales" class="w-full h-48 object-contain cursor-pointer"
                                        @click="openModal('{{ asset('storage/absensi_sales/' . $absensiSales->foto) }}')">
                                </div>
                            </div>
                        @else
                            <div class="flex-1">
                                <x-input-label :value="__('Foto Absensi Sales')" />
                                <div class="mt-1 p-8 bg-gray-100 rounded-md text-center text-gray-500">
                                    Tidak ada foto
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tombol Kembali -->
            <div class="mt-6 flex justify-end">
                <a href="{{ route('absensi_sales.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                    Kembali
                </a>
            </div>
        </div>
    </div>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>
