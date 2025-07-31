<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hak Akses Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Struktur Tabel untuk DataTables --}}
                    <table id="hak-akses-table" class="min-w-full dt-tailwindcss divide-y divide-gray-200 sm:w-1/2">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Jabatan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Isi tabel akan di-generate oleh DataTables secara dinamis --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- CDN untuk library yang dibutuhkan --}}
        <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.tailwindcss.min.css">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.3.2/js/dataTables.tailwindcss.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#hak-akses-table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    scrollX: true,
                    ajax: {
                        // Pastikan route ini ada dan mengembalikan data JSON
                        url: "{{ route('hak-akses.index') }}",
                    },
                    language: {
                        lengthMenu: '_MENU_',
                        search: '',
                        searchPlaceholder: "Cari..."
                    },
                    initComplete: function() {
                        $('.dt-length select').addClass('!bg-white !text-gray-700 !border-gray-300 w-16');
                        $('.dt-search input[type="search"]').addClass(
                            'bg-white text-gray-700 border-gray-300');
                    },
                    dom: '<"flex flex-row items-center justify-between gap-4 py-4"lf>t<"flex flex-row items-center justify-between gap-4 py-4"ip>',
                    drawCallback: function(settings) {
                        // Memaksa penyesuaian ulang lebar kolom setiap kali tabel digambar ulang (misal: setelah search)
                        this.api().columns.adjust();
                    },
                    columns: [{
                            data: 'nama',
                            name: 'nama',
                            className: 'px-6 py-4 whitespace-nowrap text-sm text-gray-900'
                        },
                        {
                            data: 'id', // Menggunakan ID untuk membuat link
                            name: 'aksi',
                            className: 'px-6 py-4 whitespace-nowrap text-sm',
                            orderable: false,
                            searchable: false,
                            width: '25%',
                            render: function(data, type, row) {
                                // 'data' berisi 'id' dari jabatan
                                let kelolaUrl = "{{ route('hak-akses.edit', ':id') }}".replace(':id',
                                    data);
                                return `<a href="${kelolaUrl}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md">
                                            Kelola Hak Akses
                                        </a>`;
                            }
                        }
                    ],
                });
            });
        </script>
    @endpush
</x-app-layout>
