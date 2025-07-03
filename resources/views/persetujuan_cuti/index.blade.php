<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Persetujuan Cuti Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table id="persetujuan-cuti-table" class="min-w-full dt-tailwindcss divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    ID</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Nama Karyawan</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Nama Cuti</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    Jenis Cuti</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Mulai</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Selesai</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    Sisa Cuti</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            {{-- Diisi oleh DataTables --}}
                        </tbody>
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
            $(document).ready(function() {
                // Inisialisasi DataTables
                window.persetujuanCutiTable = $('#persetujuan-cuti-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('persetujuan_cuti.index') }}", // Sesuaikan nama route jika perlu
                    language: {
                        lengthMenu: '_MENU_',
                        search: '',
                        searchPlaceholder: "Cari..."
                    },
                    dom: '<"flex flex-col md:flex-row md:items-center md:justify-between gap-4 py-4"lf>rt<"flex flex-col md:flex-row md:items-center md:justify-between gap-4 py-4"ip>',
                    initComplete: function() {
                        $('.dt-length select').addClass('!bg-white !text-gray-700 !border-gray-300 w-16');
                        $('.dt-search input[type="search"]').addClass(
                            'bg-white text-gray-700 border-gray-300');
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            className: 'px-6 py-4 text-sm text-gray-900'
                        },
                        {
                            data: 'user.nama',
                            name: 'user.nama',
                            className: 'px-6 py-4 text-sm text-gray-900'
                        },
                        {
                            data: 'nama_cuti',
                            name: 'nama_cuti',
                            className: 'px-6 py-4 text-sm text-gray-900'
                        },
                        {
                            data: 'jenis_cuti',
                            name: 'jenis_cuti',
                            className: 'px-6 py-4 text-sm text-gray-900 text-center',
                            render: function(data, type, row) {
                                let badgeClass = '';

                                switch (data) {
                                    case 'Cuti Biasa':
                                        badgeClass = 'text-yellow-800 bg-yellow-100';
                                        break;
                                    case 'Cuti Spesial':
                                        // FIX: Diubah dari text-indigo-800 menjadi text-red-800 agar konsisten
                                        badgeClass = 'text-red-800 bg-red-100';
                                        break;
                                }
                                // Mengembalikan elemen <span> dengan class yang sesuai
                                return `<span class="inline-flex px-2 text-xs font-semibold leading-5 ${badgeClass} rounded-full">${data}</span>`;
                            }
                        },
                        {
                            data: 'tanggal_mulai_cuti',
                            name: 'tanggal_mulai_cuti',
                            className: 'px-6 py-4 text-sm text-gray-900'
                        },
                        {
                            data: 'tanggal_selesai_cuti',
                            name: 'tanggal_selesai_cuti',
                            className: 'px-6 py-4 text-sm text-gray-900'
                        },
                        {
                            data: 'sisa_hak_cuti',
                            name: 'user.sisa_hak_cuti',
                            className: 'px-6 py-4 text-sm text-gray-900 text-center'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'px-6 py-4 text-center',
                            render: function(data, type, row) {
                                let badgeClass = '';

                                switch (data) {
                                    case 'Disetujui':
                                        badgeClass = 'text-green-800 bg-green-100';
                                        break;
                                    case 'Menunggu':
                                        badgeClass = 'text-yellow-800 bg-yellow-100';
                                        break;
                                    case 'Ditolak':
                                        // FIX: Diubah dari text-indigo-800 menjadi text-red-800 agar konsisten
                                        badgeClass = 'text-red-800 bg-red-100';
                                        break;
                                }
                                // Mengembalikan elemen <span> dengan class yang sesuai
                                return `<span class="inline-flex px-2 text-xs font-semibold leading-5 ${badgeClass} rounded-full">${data}</span>`;
                            }
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            className: 'px-6 py-4 text-center',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                // Notifikasi dari session
                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: '{{ session('error') }}',
                        confirmButtonText: 'OK'
                    });
                @endif
            });
        </script>
    @endpush
</x-app-layout>
