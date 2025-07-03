<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Absensi') }} {{ $absensi->user->nama }}
            </h2>
        </x-slot>

        <div class="mt-4 bg-white shadow-sm rounded-lg p-6" x-data="{
            currentFotoMasukUrl: '{{ $absensi->foto_masuk ? asset('storage/' . $absensi->foto_masuk) : '' }}',
            newFotoMasukUrl: null,
            currentFotoKeluarUrl: '{{ $absensi->foto_keluar ? asset('storage/' . $absensi->foto_keluar) : '' }}',
            newFotoKeluarUrl: null,
            showModal: false,
            modalImageUrl: '',
            openModal(url) {
                this.modalImageUrl = url;
                this.showModal = true;
            }
        }">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informasi Dasar -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Informasi Absensi</h3>
                        <div class="mt-2 border-t border-gray-200 pt-2">
                            <dl class="divide-y divide-gray-200">
                                <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                        {{ $absensi->tanggal->format('d F Y') }}
                                    </dd>
                                </div>
                                <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                        @if ($absensi->status == 'Hadir')
                                            <span
                                                class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                                                Hadir
                                            </span>
                                        @elseif ($absensi->status == 'Sakit')
                                            <span
                                                class="inline-flex px-2 text-xs font-semibold leading-5 text-yellow-800 bg-yellow-100 rounded-full">
                                                Sakit
                                            </span>
                                        @elseif ($absensi->status == 'Cuti')
                                            <span
                                                class="inline-flex px-2 text-xs font-semibold leading-5 text-indigo-800 bg-indigo-100 rounded-full">
                                                Cuti
                                            </span>
                                        @elseif ($absensi->status == 'Izin')
                                            <span
                                                class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">
                                                Izin
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">
                                                {{ $absensi->status }}
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                        {{ $absensi->lokasi->nama ?? '-' }}
                                    </dd>
                                </div>
                                <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Nama Karyawan</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                        {{ $absensi->user->nama }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Foto Absensi -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">Foto Absensi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if ($absensi->foto_masuk)
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-2">Foto Masuk</p>
                                <!-- Current Image -->
                                <template x-if="currentFotoMasukUrl">
                                    <img :src="currentFotoMasukUrl"
                                        class="h-40 w-full object-cover rounded-md cursor-pointer border border-gray-200"
                                        @click="openModal(currentFotoMasukUrl)" alt="Foto Masuk Saat Ini">
                                </template>
                                <p class="text-xs text-gray-500 mt-1">
                                    Jam: {{ $absensi->jam_masuk }}<br>
                                    Lokasi: {{ $absensi->latitude_masuk }}, {{ $absensi->longitude_masuk }}
                                </p>
                            </div>
                        @endif

                        @if ($absensi->foto_keluar)
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-2">Foto Keluar</p>
                                <template x-if="currentFotoKeluarUrl">
                                    <img :src="currentFotoKeluarUrl"
                                        class="h-40 w-full object-cover rounded-md cursor-pointer border border-gray-200"
                                        @click="openModal(currentFotoKeluarUrl)" alt="Foto Keluar Saat Ini">
                                </template>
                                <p class="text-xs text-gray-500 mt-1">
                                    Jam: {{ $absensi->jam_keluar }}<br>
                                    Lokasi: {{ $absensi->latitude_keluar }}, {{ $absensi->longitude_keluar }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detail Waktu dan Lokasi -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Absensi Masuk -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Absensi Masuk</h4>
                    <dl class="space-y-3">
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Jam Masuk</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                {{ $absensi->jam_masuk ?? '-' }}
                            </dd>
                        </div>
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Koordinat</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                @if ($absensi->latitude_masuk && $absensi->longitude_masuk)
                                    <a href="https://www.google.com/maps?q={{ $absensi->latitude_masuk }},{{ $absensi->longitude_masuk }}"
                                        target="_blank" class="text-blue-600 hover:underline">
                                        {{ $absensi->latitude_masuk }}, {{ $absensi->longitude_masuk }}
                                    </a>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Absensi Keluar -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Absensi Keluar</h4>
                    <dl class="space-y-3">
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Jam Keluar</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                {{ $absensi->jam_keluar ?? '-' }}
                            </dd>
                        </div>
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Koordinat</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                                @if ($absensi->latitude_keluar && $absensi->longitude_keluar)
                                    <a href="https://www.google.com/maps?q={{ $absensi->latitude_keluar }},{{ $absensi->longitude_keluar }}"
                                        target="_blank" class="text-blue-600 hover:underline">
                                        {{ $absensi->latitude_keluar }}, {{ $absensi->longitude_keluar }}
                                    </a>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto"
                @keydown.escape.window="showModal = false">
                <!-- Overlay -->
                <div x-show="showModal" x-transition.opacity
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false">
                </div>

                <!-- Modal Content - Pusatkan vertikal dan horizontal -->
                <div class="flex items-center justify-center min-h-screen">
                    <div x-show="showModal" x-transition.opacity style="display: none;"
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                        Preview Foto Absensi
                                    </h3>
                                    <div class="mt-2 flex justify-center">
                                        <img :src="modalImageUrl" class="max-w-full max-h-[70vh] object-contain"
                                            alt="Foto Absensi Preview">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" @click="showModal = false"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Kembali -->
            <div class="mt-6 flex justify-end">
                <a href="{{ route('absensi.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
