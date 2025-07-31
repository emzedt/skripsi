<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Penggajian') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @hasPermission('Tambah Penggajian')
                        <div class="flex justify-end mb-4">
                            <a href="{{ route('penggajian.create') }}"
                                class="flex items-center px-4 py-2 text-white bg-gray-800 rounded-md hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Penggajian
                            </a>
                        </div>
                    @endhasPermission

                    <table id="penggajian-table" class="min-w-full dt-tailwindcss divide-y divide-gray-200">
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
                                    Periode</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Total Gaji</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Lembur</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Potongan</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    Aksi</th>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            function confirmDelete(id, nama, periode) {
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    html: `Data gaji untuk <strong>${nama}</strong> periode <strong>${periode}</strong> akan dihapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/penggajian/${id}`, { // Sesuaikan URL dengan route Anda
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                            if (response.ok) {
                                window.penggajianTable.ajax.reload(null, false);
                                Swal.fire('Berhasil!', 'Data penggajian telah dihapus.', 'success');
                            } else {
                                Swal.fire('Gagal!', 'Gagal menghapus data.', 'error');
                            }
                        }).catch(error => Swal.fire('Error!', 'Terjadi kesalahan.', 'error'));
                    }
                });
            }

            $(document).ready(function() {
                window.penggajianTable = $('#penggajian-table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    scrollX: true,
                    ajax: "{{ route('penggajian.index') }}",
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
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'user.nama',
                            name: 'user.nama'
                        },
                        {
                            data: 'periode',
                            name: 'periode_mulai'
                        }, // Sorting berdasarkan periode_mulai
                        {
                            data: 'gaji_diterima',
                            name: 'gaji_diterima'
                        },
                        {
                            data: 'lembur',
                            name: 'lembur'
                        },
                        {
                            data: 'potongan_gaji',
                            name: 'potongan_gaji'
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            className: 'text-center',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            });
        </script>
    @endpush
</x-app-layout>
