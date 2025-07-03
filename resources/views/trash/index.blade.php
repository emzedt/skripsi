<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sampah Data (Trash)') }}
        </h2>
    </x-slot>

    {{-- Alpine.js sekarang hanya mengelola state tab yang aktif --}}
    <div class="py-6" x-data="{ activeTab: '{{ request('tab', array_key_first($data)) }}' }" x-init="initializeTrashTable(activeTab)">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach ($data as $key => $info)
                            <button {{-- TAMBAHKAN ID UNIK DI SINI --}} id="tab-button-{{ $key }}"
                                @click="activeTab = '{{ $key }}'; initializeTrashTable('{{ $key }}')"
                                :class="activeTab === '{{ $key }}' ? 'bg-gray-800 text-white' :
                                    'bg-gray-200 text-gray-800'"
                                class="px-4 py-2 rounded-md text-sm font-medium">
                                {{ $info['label'] }} ({{ $info['total'] }})
                            </button>
                        @endforeach
                    </div>

                    @foreach ($data as $key => $info)
                        <div x-show="activeTab === '{{ $key }}'" x-cloak>
                            <table id="trash-table-{{ $key }}"
                                class="min-w-full dt-tailwindcss divide-y divide-gray-200 text-sm" style="width:100%">
                                {{-- Kita bisa membuat header secara dinamis di sini karena ini adalah Blade, bukan JS --}}
                                <thead class="bg-gray-50">
                                    <tr>
                                        @foreach ($info['columns'] as $columnLabel)
                                            <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">
                                                {{ $columnLabel }}</th>
                                        @endforeach
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200"></tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- CDN Libraries --}}
        <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.tailwindcss.min.css">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.3.2/js/dataTables.tailwindcss.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // State untuk melacak tabel mana yang sudah diinisialisasi
            let initializedTables = {};

            // Konfigurasi kolom yang sudah dirapikan agar cocok dengan controller
            window.trashTableColumns = {
                'karyawan': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'jabatan': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'cuti': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'nama_cuti',
                        name: 'nama_cuti'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'absensi': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'izin': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jenis_izin',
                        name: 'jenis_izin'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'sakit': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'diagnosa',
                        name: 'diagnosa'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'lembur': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'upah_lembur_per_jam',
                        name: 'upah_lembur_per_jam'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'people_development': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'development_kpi': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'kpi',
                        name: 'kpi'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'development_objective': [ // ← ini yang diperbaiki
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'objective',
                        name: 'objective'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'gaji_bulanan': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'gaji_bulanan',
                        name: 'gaji_bulanan'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'gaji_harian': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'gaji_harian',
                        name: 'gaji_harian'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'penggajian': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'periode_mulai',
                        name: 'periode_mulai'
                    },
                    {
                        data: 'gaji_diterima',
                        name: 'gaji_diterima'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'lokasi': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'radius',
                        name: 'radius'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'kalender': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'hak_cuti': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'hak_cuti',
                        name: 'hak_cuti'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'absensi_sales': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'status_persetujuan',
                        name: 'status_persetujuan'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'permintaan_lembur': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'tugas',
                        name: 'tugas'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ],
                'status_karyawan': [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'status_karyawan',
                        name: 'status_karyawan'
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    }
                ]
            };

            // Kode ini akan secara otomatis menambahkan kolom 'Aksi' ke setiap konfigurasi di atas
            for (const key in window.trashTableColumns) {
                if (window.trashTableColumns.hasOwnProperty(key)) {
                    window.trashTableColumns[key].push({
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    });
                }
            }


            // Fungsi untuk konfirmasi restore menggunakan AJAX
            function confirmRestore(restoreUrl, modelName, tableId, modelKey) {
                Swal.fire({
                    title: 'Pulihkan Data?',
                    text: `Anda yakin ingin memulihkan data ${modelName} ini?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563EB',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Pulihkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(restoreUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json().then(data => ({
                                ok: response.ok,
                                data
                            })))
                            .then(({
                                ok,
                                data
                            }) => {
                                if (ok) {
                                    // 1. Reload tabelnya
                                    $(tableId).DataTable().ajax.reload(null, false);

                                    // 2. Tampilkan notifikasi
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: data.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    // 3. LOGIKA BARU: Perbarui angka di tombol tab
                                    const tabButton = document.getElementById(`tab-button-${modelKey}`);
                                    if (tabButton) {
                                        const label = tabButton.textContent.split('(')[0].trim();
                                        // Gunakan newTotal dari respons JSON
                                        tabButton.textContent = `${label} (${data.newTotal})`;
                                    }
                                } else {
                                    Swal.fire('Gagal!', data.message || 'Gagal memulihkan data.', 'error');
                                }
                            }).catch(error => Swal.fire('Error!', 'Terjadi kesalahan.', 'error'));
                    }
                });
            }

            // Fungsi utama untuk inisialisasi tabel
            function initializeTrashTable(tab) {
                if (initializedTables[tab]) {
                    return;
                }
                const tableId = `#trash-table-${tab}`;
                const columnsConfig = window.trashTableColumns[tab];
                const ajaxUrl = `{{ route('trash.data') }}?model=${tab}`;
                if (!columnsConfig) {
                    console.error(`Konfigurasi kolom untuk tab '${tab}' tidak ditemukan.`);
                    return;
                }
                $(tableId).DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: ajaxUrl,
                    language: {
                        lengthMenu: '_MENU_',
                        search: '',
                        searchPlaceholder: "Cari..."
                    },
                    dom: '<"flex flex-col md:flex-row md:items-center md:justify-between gap-4 py-4"lf>rt<"flex flex-col md:flex-row md:items-center md:justify-between gap-4 py-4"ip>',
                    initComplete: function() {
                        const wrapperId = `#${this.api().table().node().id}_wrapper`;
                        $(`${wrapperId} .dt-length select, ${wrapperId} .dt-search input`).addClass(
                            '!bg-white !text-gray-700 !border-gray-300');
                        $(`${wrapperId} .dt-length select`).addClass('w-16');
                    },
                    columns: columnsConfig
                });
                initializedTables[tab] = true;
            }
        </script>
    @endpush
</x-app-layout>
