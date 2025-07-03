<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detail Penggajian') }}
        </h2>
    </x-slot>
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            <a href="{{ route('penggajian.slip', $penggajian->id) }}" target="_blank"
                                class="inline-flex items-cente px-4 py-2 border border-transparent font-medium rounded shadow-sm text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cetak Slip
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Karyawan</h3>
                            <div class="space-y-2">
                                <div class="flex">
                                    <span class="text-gray-600 w-32">Nama</span>
                                    <span class="text-gray-900">{{ $penggajian->user->nama }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-32">Status</span>
                                    <span class="text-gray-900">
                                        {{ $penggajian->user->isKaryawanTetap() ? 'Karyawan Tetap' : 'Karyawan Harian' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Periode Penggajian</h3>
                            <div class="space-y-2">
                                <div class="flex">
                                    <span class="text-gray-600 w-32">Periode</span>
                                    <span class="text-gray-900">
                                        {{ \Carbon\Carbon::parse($penggajian->periode_mulai)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($penggajian->periode_selesai)->format('d M Y') }}
                                    </span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-32">Tanggal Dibuat</span>
                                    <span
                                        class="text-gray-900">{{ $penggajian->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-3">Rincian Gaji</h3>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if ($penggajian->user->isKaryawanTetap())
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 w-1/3">
                                            Gaji
                                            Bulanan</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-right">Rp
                                            {{ number_format($penggajian->user->gajiBulanan->gaji_bulanan ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 w-1/3">
                                            Gaji
                                            Harian</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-right">Rp
                                            {{ number_format($penggajian->user->gajiHarian->gaji_harian ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 w-1/3">
                                            Uang
                                            Makan</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-right">Rp
                                            {{ number_format($penggajian->user->gajiHarian->upah_makan_harian ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 w-1/3">
                                            Uang
                                            Lembur</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-right">Rp
                                            {{ number_format($penggajian->lembur, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 w-1/3">
                                            Jumlah Lembur Over (> 5 Jam)
                                            @php
                                                $overtimeOver5Hours = $penggajian->user->permintaanLembur
                                                    ->where('status', 'Disetujui')
                                                    ->filter(function ($lembur) {
                                                        return $lembur->lama_lembur > 5;
                                                    })
                                                    ->count();
                                            @endphp
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-right">
                                            {{ $overtimeOver5Hours }} x</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 w-1/3">
                                            Total Jumlah Jam Lembur
                                            @php
                                                $totalJamLembur =
                                                    $penggajian->user->permintaanLembur
                                                        ->where('status', 'Disetujui')
                                                        ->sum('lama_lembur') / 60;
                                            @endphp
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-right">Rp
                                            {{ $totalJamLembur }} jam</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 w-1/3">
                                        Potongan
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-right">
                                        Rp
                                        {{ number_format($penggajian->potongan_gaji, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">Total Gaji
                                        Diterima</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                        Rp
                                        {{ number_format($penggajian->gaji_diterima, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol Kembali -->
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('penggajian.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
