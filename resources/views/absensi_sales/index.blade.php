<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Absensi Sales Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('absensi_sales.create') }}"
                            class="flex items-center px-4 py-2 text-white bg-gray-800 rounded-md hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Absensi Sales
                        </a>
                    </div>

                    {{-- Struktur Tabel untuk DataTables --}}
                    <table id="absensi-sales-table" class="min-w-full dt-tailwindcss divide-y divide-gray-200">
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
                                    Tanggal</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Jam</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status Persetujuan</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
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
            function confirmDelete(id, name, tanggal) {
                Swal.fire({
                    title: 'Yakin ingin menghapus absensi?',
                    text: `Absensi sales ${name} pada tanggal ${tanggal} akan dihapus!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/absensi-sales/${id}`, { // Sesuaikan URL dengan route Anda
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                            if (response.ok) {
                                window.absensiSalesTable.ajax.reload(null, false);
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data absensi berhasil dihapus.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire('Gagal!', 'Gagal menghapus data.', 'error');
                            }
                        }).catch(error => Swal.fire('Error!', 'Terjadi kesalahan.', 'error'));
                    }
                });
            }

            $(document).ready(function() {
                // Menyimpan instance DataTable ke window object agar bisa diakses dari luar jika diperlukan
                window.absensiSalesTable = $('#absensi-sales-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('absensi_sales.index') }}", // Sesuaikan dengan nama route Anda

                    // Konfigurasi bahasa dan placeholder
                    language: {
                        lengthMenu: '_MENU_',
                        search: '',
                        searchPlaceholder: "Cari..."
                    },

                    // Mengatur struktur DOM dari DataTables (posisi search, pagination, dll)
                    dom: '<"flex flex-col md:flex-row md:items-center md:justify-between gap-4 py-4"lf>rt<"flex flex-col md:flex-row md:items-center md:justify-between gap-4 py-4"ip>',

                    // Callback function setelah DataTables selesai diinisialisasi
                    initComplete: function() {
                        $('.dt-length select').addClass('!bg-white !text-gray-700 !border-gray-300 w-16');
                        $('.dt-search input[type="search"]').addClass(
                            'bg-white text-gray-700 border-gray-300');
                    },

                    // Definisi kolom-kolom tabel
                    columns: [{
                            data: 'id',
                            name: 'id',
                            className: 'text-center'
                        },
                        {
                            data: 'user.nama',
                            name: 'user.nama',
                            // className: 'text-center' // Dihilangkan agar nama tidak center, lebih mudah dibaca
                        },
                        {
                            data: 'tanggal',
                            name: 'tanggal',
                            className: 'text-center'
                        },
                        {
                            data: 'jam',
                            name: 'jam',
                            className: 'text-center'
                        },
                        {
                            data: 'status_persetujuan',
                            name: 'status_persetujuan',
                            className: 'text-center',
                            // Fungsi render untuk mengubah tampilan data (membuat badge status)
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
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ]
                    // Hapus }; yang menyebabkan error sintaks di sini
                });
            });

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}',
                });
            @endif
        </script>
    @endpush
</x-app-layout>
