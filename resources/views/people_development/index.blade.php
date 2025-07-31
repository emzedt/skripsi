<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('People Development') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Struktur Tabel untuk DataTables --}}
                    <table id="people-development-table" class="min-w-full dt-tailwindcss divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Karyawan
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jabatan
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah Development
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
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

        <script>
            $(document).ready(function() {
                const table = $('#people-development-table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    scrollX: true,
                    ajax: "{{ route('people_development.index') }}",
                    language: {
                        lengthMenu: '_MENU_',
                        search: '',
                        searchPlaceholder: "Cari..."
                    },
                    dom: '<"flex flex-row items-center justify-between gap-4 py-4"lf>t<"flex flex-row items-center justify-between gap-4 py-4"ip>',
                    drawCallback: function(settings) {
                        // Memaksa penyesuaian ulang lebar kolom setiap kali tabel digambar ulang (misal: setelah search)
                        this.api().columns.adjust();
                    },
                    initComplete: function() {
                        $('.dt-length select, .dt-search input').addClass(
                            '!bg-white !text-gray-700 !border-gray-300');
                        $('.dt-length select').addClass('w-16');
                    },
                    columns: [{
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'jabatan.nama',
                            name: 'jabatan.nama',
                            defaultContent: '-'
                        },
                        {
                            data: 'people_development_count',
                            name: 'people_development_count',
                            className: 'text-center'
                        }
                    ],
                    // Menambahkan kursor pointer ke setiap baris di body tabel
                    "createdRow": function(row, data, dataIndex) {
                        $(row).addClass('cursor-pointer hover:bg-gray-50');
                    }
                });

                // Menambahkan event listener untuk klik pada baris
                $('#people-development-table tbody').on('click', 'tr', function() {
                    const data = table.row(this).data();
                    if (data) {
                        // Arahkan ke halaman detail user
                        const url = "{{ route('people_development.show', ':id') }}".replace(':id', data.id);
                        window.location.href = url;
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
