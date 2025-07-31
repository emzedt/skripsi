<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Gaji Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @hasPermission('Tambah Gaji')
                <div class="mb-6 flex justify-end">
                    <a href="{{ route('gaji.create') }}"
                        class="flex items-center px-4 py-2 text-white bg-gray-800 rounded-md hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Data Gaji
                    </a>
                </div>
            @endhasPermission

            {{-- Tabel Karyawan Tetap --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-xl font-semibold mb-4">Karyawan Tetap</h3>
                    <table id="gaji-tetap-table" class="min-w-full dt-tailwindcss divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gaji Bulanan
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel Karyawan Harian --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-xl font-semibold mb-4">Karyawan Harian</h3>
                    <table id="gaji-harian-table" class="min-w-full dt-tailwindcss divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gaji Harian
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Upah Makan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lembur/Jam
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lembur >5
                                    Jam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- CDN Libraries --}}
        <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.tailwindcss.min.css">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.3.2/js/dataTables.tailwindcss.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Fungsi delete sekarang menerima ID tabel untuk di-reload
            function confirmDelete(id, nama, tableId) {
                Swal.fire({
                    title: 'Yakin ingin menghapus gaji?',
                    text: `Data gaji untuk ${nama} akan dihapus!`,
                    icon: 'warning',
                    // ... (opsi SweetAlert lainnya tetap sama) ...
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/gaji/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                            if (response.ok) {
                                $(tableId).DataTable().ajax.reload(null, false); // Reload tabel yang spesifik
                                Swal.fire('Berhasil!', 'Data gaji telah dihapus.', 'success');
                            } else {
                                Swal.fire('Gagal!', 'Gagal menghapus data.', 'error');
                            }
                        }).catch(error => Swal.fire('Error!', 'Terjadi kesalahan.', 'error'));
                    }
                });
            }

            $(document).ready(function() {
                // ... (konfigurasi umum tetap sama) ...
                const commonConfig = {
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    scrollX: true,
                    language: {
                        lengthMenu: '_MENU_',
                        search: '',
                        searchPlaceholder: "Cari..."
                    },
                    dom: '<"flex flex-row items-center justify-between gap-1 py-4"lf>t<"flex flex-row items-center justify-between gap-4 py-4"ip>',
                    drawCallback: function(settings) {
                        // Memaksa penyesuaian ulang lebar kolom setiap kali tabel digambar ulang (misal: setelah search)
                        this.api().columns.adjust();
                    },
                    initComplete: function() {
                        const tableId = this.api().table().node().id;
                        $(`#${tableId}_wrapper .dt-length select`).addClass(
                            '!bg-white !text-gray-700 !border-gray-300 w-16');
                        $(`#${tableId}_wrapper .dt-search input[type="search"]`).addClass(
                            'bg-white text-gray-700 border-gray-300');
                    }
                };

                // Inisialisasi Tabel Karyawan Tetap
                $('#gaji-tetap-table').DataTable({
                    ...commonConfig,
                    // PERUBAHAN DI SINI: Arahkan ke route index dengan parameter type
                    ajax: "{{ route('gaji.index', ['type' => 'tetap']) }}",
                    columns: [{
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'gaji_bulanan',
                            name: 'gajiBulanan.gaji_bulanan'
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                // Inisialisasi Tabel Karyawan Harian
                $('#gaji-harian-table').DataTable({
                    ...commonConfig,
                    // PERUBAHAN DI SINI: Arahkan ke route index dengan parameter type
                    ajax: "{{ route('gaji.index', ['type' => 'harian']) }}",
                    columns: [{
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'gaji_harian',
                            name: 'gajiHarian.gaji_harian'
                        },
                        {
                            data: 'upah_makan_harian',
                            name: 'gajiHarian.upah_makan_harian'
                        },
                        {
                            data: 'lembur_per_jam',
                            name: 'lembur.upah_lembur_per_jam'
                        },
                        {
                            data: 'lembur_over_5_jam',
                            name: 'lembur.upah_lembur_over_5_jam'
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            });
        </script>
    @endpush
</x-app-layout>
