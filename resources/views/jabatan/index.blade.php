<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jabatan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Tombol "Tambah Jabatan" --}}
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('jabatan.create') }}"
                            class="flex items-center px-4 py-2 text-white bg-gray-800 rounded-md hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Jabatan
                        </a>
                    </div>

                    {{-- Menghapus search & pagination manual, akan dibuat oleh DataTables --}}

                    {{-- Struktur Tabel untuk DataTables --}}
                    <table id="jabatan-table" class="min-w-full dt-tailwindcss divide-y divide-gray-200"
                        style="width:100%">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    ID</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Nama Jabatan</th>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {
                var table = $('#jabatan-table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    scrollX: true,
                    ajax: {
                        url: "{{ route('jabatan.index') }}", // Pastikan route ini ada dan mengembalikan data JSON untuk DataTables
                    },
                    language: {
                        lengthMenu: '_MENU_',
                        search: '',
                        searchPlaceholder: "Cari..."
                    },
                    initComplete: function() {
                        // Targetkan dropdown 'entries per page'
                        $('.dt-length select').addClass('!bg-white !text-gray-700 !border-gray-300 w-16');

                        $('.dt-search input[type="search"]')
                            .removeClass('dt-input') // WAJIB: Hapus kelas default DataTables
                            .addClass(
                                'bg-white text-gray-700 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 md:w-64 w-48'
                            );
                    },
                    dom: '<"flex flex-row items-center justify-between gap-1 py-4"lf>t<"flex flex-row items-center justify-between gap-4 py-4"ip>',
                    drawCallback: function(settings) {
                        // Memaksa penyesuaian ulang lebar kolom setiap kali tabel digambar ulang (misal: setelah search)
                        this.api().columns.adjust();
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            className: 'px-6 py-4 whitespace-nowrap text-gray-900',
                        },
                        {
                            data: 'nama',
                            name: 'nama',
                            className: 'px-6 py-4 whitespace-nowrap text-gray-900'
                        },
                        {
                            data: 'id',
                            name: 'aksi',
                            className: 'px-6 py-4 whitespace-nowrap',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let editUrl = "{{ route('jabatan.edit', ':id') }}".replace(':id', data);
                                return `<div class="flex space-x-2">
                                            <a href="${editUrl}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <button type="button" onclick="confirmDelete(${data}, '${row.nama}')" class="text-red-600 hover:text-red-900" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>`;
                            }
                        }
                    ],
                });

                window.jabatanTable = table;
            });

            function confirmDelete(id, name) {
                Swal.fire({
                    title: 'Hapus Data',
                    text: `Anda yakin ingin menghapus jabatan ${name}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/jabatan/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                            if (response.ok) {
                                window.jabatanTable.ajax.reload(null, false); // Reload tabel tanpa refresh
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data berhasil dihapus.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire('Gagal!', 'Gagal menghapus data.', 'error');
                            }
                        }).catch(error => {
                            Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
                        });
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
