<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('People Development') }} {{ $user->nama }}
        </h2>
    </x-slot>

    @php
        function formatValue($value, $tipe_kpi)
        {
            if ($tipe_kpi === 'persen') {
                return number_format($value, 2) . '%';
            } elseif ($tipe_kpi === 'uang') {
                return 'Rp. ' . number_format($value, 0, ',', '.');
            } else {
                return number_format($value, 2, ',', '.');
            }
        }
    @endphp

    <div class="container mx-auto px-4 py-8">
        @hasPermission('Tambah People Development')
            <div class="flex justify-end mb-4">
                <a href="{{ route('people_development.create', $user) }}"
                    class="flex items-center px-4 py-2 text-white bg-gray-800 rounded-md hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                        </path>
                    </svg>
                    Tambah People Development
                </a>
            </div>
        @endhasPermission

        @if ($user->peopleDevelopment->count())
            @foreach ($user->peopleDevelopment as $development)
                @php
                    $totalNilai = $development->objectives->sum(function ($objective) {
                        return $objective->kpis->sum(function ($kpi) {
                            if ($kpi->target == 0) {
                                return 0;
                            }
                            $kontribusi = ($kpi->realisasi / $kpi->target) * $kpi->bobot;
                            return min($kontribusi, $kpi->bobot);
                        });
                    });
                    $totalFormatted = number_format($totalNilai, 2);
                @endphp

                <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-center w-full">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">
                                    Jabatan: {{ $development->jabatan }}
                                </h2>
                                <p class="text-sm text-gray-600">
                                    Periode: {{ $development->periode_mulai->format('d M Y') }} -
                                    {{ $development->periode_selesai->format('d M Y') }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <span class="font-semibold">Total Nilai:</span>
                                    <span class="text-xl font-bold text-blue-600">{{ $totalFormatted }}%</span>
                                </div>
                                @hasPermission('Edit People Development')
                                    <a href="{{ route('people_development.edit', [$user, $development]) }}"
                                        class="text-yellow-600 hover:text-yellow-900"><svg class="w-8 h-8" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                @endhasPermission
                                @hasPermission('Hapus People Development')
                                    <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                        onclick="deleteDevelopment({{ $user->id }}, {{ $development->id }}, '{{ $development->jabatan }}')">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                @endhasPermission
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        @if ($development->objectives->count())
                            @foreach ($development->objectives as $objective)
                                @php
                                    $nilaiAkhirObjective = $objective->kpis->sum(function ($kpi) {
                                        if ($kpi->target == 0) {
                                            return 0;
                                        }

                                        // Special handling for percentage type
                                        if ($kpi->tipe_kpi === 'persen') {
                                            $realisasi = min($kpi->realisasi, 100);
                                            return ($realisasi / $kpi->target) * $kpi->bobot;
                                        }

                                        // Normal calculation for other types
                                        $kontribusi = ($kpi->realisasi / $kpi->target) * $kpi->bobot;
                                        return min($kontribusi, $kpi->bobot);
                                    });

                                    $nilaiFormatted = number_format($nilaiAkhirObjective, 2);
                                    $isObjectiveAchieved = $nilaiAkhirObjective >= $objective->kpis->sum('bobot');
                                @endphp

                                <div class="mb-6">
                                    <h3 class="font-medium text-lg mb-2">{{ $objective->objective }}</h3>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        KPI</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Target</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Realisasi</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Bobot</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Status</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Kontribusi</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Nilai Objective</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($objective->kpis as $index => $kpi)
                                                    @php
                                                        if ($kpi->target == 0) {
                                                            $kontribusi = 0;
                                                        } else {
                                                            // Special handling for percentage type
                                                            if ($kpi->tipe_kpi === 'persen') {
                                                                $realisasi = min($kpi->realisasi, 100);
                                                                $kontribusi = ($realisasi / $kpi->target) * $kpi->bobot;
                                                            } else {
                                                                $kontribusi =
                                                                    ($kpi->realisasi / $kpi->target) * $kpi->bobot;
                                                            }
                                                            $kontribusi = min($kontribusi, $kpi->bobot);
                                                        }
                                                        $kontribusiFormatted = number_format($kontribusi, 2);
                                                    @endphp
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                            {{ $kpi->kpi }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                            {{ formatValue($kpi->target, $kpi->tipe_kpi) }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                            {{ formatValue($kpi->realisasi, $kpi->tipe_kpi) }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                            {{ $kpi->bobot }}%</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                            @if ($kpi->realisasi >= $kpi->target)
                                                                <span
                                                                    class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                                                                    Tercapai
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">
                                                                    Tidak Tercapai
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                            {{ $kontribusiFormatted }}%</td>
                                                        @if ($index === 0)
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-{{ $isObjectiveAchieved ? 'green' : 'red' }}-700"
                                                                rowspan="{{ $objective->kpis->count() }}">
                                                                {{ $nilaiFormatted }}%
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500 italic">Tidak ada Objective</p>
                        @endif
                    </div>

                    <div class="p-6 border-t">
                        <h2 class="text-lg font-semibold text-gray-800 mb-2">Keterangan</h2>
                        <textarea
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none sm:text-sm cursor-auto"
                            readonly>{{ $development->keterangan }}</textarea>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-gray-500 italic">Tidak ada People Development untuk pengguna ini.</p>
        @endif
    </div>

    <script>
        function deleteDevelopment(userId, developmentId, jabatan) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data " + jabatan + " ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/people-development/${userId}/${developmentId}`;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
