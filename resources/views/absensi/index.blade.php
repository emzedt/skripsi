<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Absensi Karyawan') }}
        </h2>
    </x-slot>

    <div class="flex items-center justify-center py-5 gap-8 mt-8">
        <a href="/absensi-masuk">
            <button type="button"
                class="flex items-center px-4 py-2 text-white bg-gray-800 rounded-md hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900">
                Absensi Masuk
            </button>
        </a>
        <a href="/absensi-keluar">
            <button type="button"
                class="flex items-center px-4 py-2 text-white bg-gray-800 rounded-md hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900">
                Absensi Keluar
            </button>
        </a>
    </div>

    <div class="px-10 flex justify-center">
        <div class="border border-gray-300 rounded-xl overflow-hidden shadow-sm max-w-7xl w-full">
            <button onclick="toggleMap()" type="button"
                class="w-full flex justify-between items-center px-5 py-4 bg-gray-100 hover:bg-gray-200 text-left transition-all">
                <span class="text-gray-800 font-semibold text-base">
                    Lokasi Kantor & Posisi Anda
                </span>
                <span id="toggleIcon" class="text-xl font-bold text-gray-600 transition-all transform">
                    +
                </span>
            </button>

            <div id="mapContainer" class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out z-40">
                <div id="map" class="w-full h-[400px] relative z-10"></div>
            </div>
        </div>
    </div>

    <div class="py-6 px-10 flex justify-center">
        <div class="max-w-7xl w-full">
            <div class="overflow-hidden bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="p-6 bg-white">
                    <table id="absensi-table" class="min-w-full dt-tailwindcss divide-y divide-gray-200">
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
                                    Jam Masuk</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Jam Keluar</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status</th>
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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.tailwindcss.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // --- Inisialisasi Saat Dokumen Siap ---
        $(document).ready(function() {

            // 1. Inisialisasi DataTables
            window.absensiTable = $('#absensi-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: "{{ route('absensi.index') }}", // Route untuk mengambil data JSON
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
                dom: '<"flex flex-row items-center justify-between gap-3 py-4"lf>t<"flex flex-row items-center justify-between gap-3 py-4"ip>',
                drawCallback: function(settings) {
                    // Memaksa penyesuaian ulang lebar kolom setiap kali tabel digambar ulang (misal: setelah search)
                    this.api().columns.adjust();
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        className: 'text-center'
                    },
                    {
                        data: 'user.nama',
                        name: 'user.nama',
                        className: 'text-center'
                    }, // Data dari relasi
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jam_masuk',
                        name: 'jam_masuk',
                        className: 'text-center'
                    },
                    {
                        data: 'jam_keluar',
                        name: 'jam_keluar',
                        className: 'text-center'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        render: function(data, type, row) {
                            let badgeClass = '';

                            switch (data) {
                                case 'Hadir':
                                    badgeClass = 'text-green-800 bg-green-100';
                                    break;
                                case 'Sakit':
                                    badgeClass = 'text-yellow-800 bg-yellow-100';
                                    break;
                                case 'Cuti':
                                    badgeClass = 'text-indigo-800 bg-indigo-100';
                                    break;
                                case 'Izin':
                                    badgeClass = 'text-blue-800 bg-blue-100';
                                    break;
                                default:
                                    // Ini akan menangani status 'Alpa' atau status lain yang tidak terdaftar
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
                        render: function(data, type, row) {
                            let showUrl = `{{ route('absensi.show', ':id') }}`.replace(':id', data);
                            let editUrl = `{{ route('absensi.edit', ':id') }}`.replace(':id', data);

                            let tanggalFormatted = row.tanggal_formatted || row.tanggal;

                            let namaUser = row.user ? row.user.nama : 'Pengguna';

                            return `
                                <div class="px-6 py-4 flex space-x-2">
                                    {{-- Tombol Lihat --}}
                                    <a href="${showUrl}" class="text-black hover:text-gray-700" title="Lihat Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>

                                    {{-- Tombol Edit --}}
                                    <a href="${editUrl}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <button type="button" class="text-red-600 hover:text-red-900" title="Hapus"
                                        onclick="confirmDelete(${data}, '${namaUser}', '${tanggalFormatted}')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });
        });

        // Simpan data lokasi kantor dari backend ke variabel JavaScript
        const officeLocations = @json($lokasis);

        let mapInitialized = false;
        let isMapOpen = false;
        let map; // Definisikan map di scope global

        function toggleMap() {
            const container = document.getElementById("mapContainer");
            const icon = document.getElementById("toggleIcon");

            if (isMapOpen) {
                container.style.maxHeight = "0";
                icon.textContent = "+";
            } else {
                container.style.maxHeight = "500px"; // cukup besar agar peta tampil penuh
                icon.textContent = "−";

                if (!mapInitialized) {
                    initializeMap();
                    mapInitialized = true;
                }
            }

            isMapOpen = !isMapOpen;
        }

        async function initializeMap() {
            map = L.map('map');

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Tambahkan kontrol fullscreen
            map.addControl(new L.Control.Fullscreen());

            // Cek GPS user
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;

                        // Set view ke posisi user saat ini
                        map.setView([userLat, userLng], 17);

                        // Tandai posisi user
                        const userMarker = L.marker([userLat, userLng], {
                                icon: L.icon({
                                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/64/64113.png',
                                    iconSize: [25, 35],
                                    iconAnchor: [12, 41],
                                })
                            }).addTo(map)
                            .bindPopup(
                                `<b>Posisi Kamu Sekarang</b><br>Lat: ${userLat.toFixed(6)}, Lng: ${userLng.toFixed(6)}`
                            )
                            .openPopup();

                        // Tambahkan semua lokasi kantor ke map
                        addOfficeLocations(userLat, userLng);

                        // Reverse geocoding untuk dapat alamat (opsional)
                        fetch(
                                `https://nominatim.openstreetmap.org/reverse?lat=${userLat}&lon=${userLng}&format=json`
                            )
                            .then(response => response.json())
                            .then(data => {
                                const address = data.display_name || "Alamat tidak ditemukan";
                                console.log("Alamat kamu:", address);
                            });
                    },
                    error => {
                        console.error("Error GPS:", error);
                        // Fallback ke lokasi default atau lokasi kantor pertama jika ada
                        const defaultLocation = officeLocations.length > 0 ? [officeLocations[0].latitude,
                            officeLocations[0].longitude
                        ] : [-6.2653, 106.7864]; // Default koordinat jika tidak ada lokasi kantor

                        map.setView(defaultLocation, 15);

                        // Tambahkan lokasi kantor ke map
                        addOfficeLocations();

                        Swal.fire("GPS Error", "Izinkan akses lokasi atau pastikan GPS aktif!", "warning");
                    }
                );
            } else {
                // Browser tidak support GPS
                const defaultLocation = officeLocations.length > 0 ? [officeLocations[0].latitude, officeLocations[0]
                    .longitude
                ] : [-6.2653, 106.7864];

                map.setView(defaultLocation, 15);

                // Tambahkan lokasi kantor ke map
                addOfficeLocations();

                Swal.fire("Browser Tidak Support", "GPS tidak tersedia di browser ini.", "error");
            }
        }

        function addOfficeLocations(userLat = null, userLng = null) {
            if (officeLocations && officeLocations.length > 0) {
                // Buat layer group untuk kantor
                const officeGroup = L.layerGroup().addTo(map);

                officeLocations.forEach(office => {
                    const officeLat = parseFloat(office.latitude);
                    const officeLng = parseFloat(office.longitude);
                    const radius = parseFloat(office.radius);

                    // Tambahkan marker untuk lokasi kantor
                    const officeMarker = L.marker([officeLat, officeLng], {
                            icon: L.icon({
                                iconUrl: 'https://cdn-icons-png.flaticon.com/512/1180/1180058.png', // Ganti dengan ikon kantor
                                iconSize: [32, 32],
                                iconAnchor: [16, 32],
                            })
                        }).addTo(officeGroup)
                        .bindPopup(`<b>${office.nama || 'Kantor'}</b><br>
                              Radius: ${radius} meter<br>
                              Lat: ${officeLat.toFixed(6)}, Lng: ${officeLng.toFixed(6)}`);

                    // Tambahkan circle untuk menunjukkan radius absensi
                    const circle = L.circle([officeLat, officeLng], {
                        radius: radius, // dalam meter
                        color: '#3388ff',
                        fillColor: '#3388ff',
                        fillOpacity: 0.2,
                        weight: 2
                    }).addTo(officeGroup);

                    // Jika user position tersedia, hitung jarak dan tentukan apakah dalam radius
                    if (userLat && userLng) {
                        const distance = getDistance(userLat, userLng, officeLat, officeLng);
                        const isWithinRadius = distance <= radius;

                        console.log(`Jarak ke kantor ${office.nama || 'Kantor'}: ${distance.toFixed(2)} meter`);
                        console.log(`Dalam radius absensi: ${isWithinRadius ? 'Ya' : 'Tidak'}`);

                        // Update popup dengan informasi jarak
                        officeMarker.bindPopup(
                            `<b>${office.nama || 'Kantor'}</b><br>
                                            Radius: ${radius} meter<br>
                                            Jarak Anda: ${distance.toFixed(2)} meter<br>
                                            Status: ${isWithinRadius ? '<span style="color:green">Dalam radius</span>' : '<span style="color:red">Di luar radius</span>'}`
                        );
                    }
                });

                // Jika tidak ada posisi user, atur tampilan ke semua lokasi kantor
                if (!userLat && officeLocations.length > 0) {
                    // Buat bounds untuk semua lokasi kantor
                    const bounds = L.latLngBounds(officeLocations.map(office => [
                        parseFloat(office.latitude),
                        parseFloat(office.longitude)
                    ]));

                    // Perluas bounds untuk tampilan yang lebih baik
                    map.fitBounds(bounds.pad(0.2));
                }
            } else {
                console.log("Tidak ada data lokasi kantor yang tersedia");
            }
        }

        // Fungsi untuk menghitung jarak antara dua koordinat dalam meter (formula Haversine)
        function getDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // radius bumi dalam meter
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180;
            const Δλ = (lon2 - lon1) * Math.PI / 180;

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c; // dalam meter
        }

        function confirmDelete(id, name, tanggal) {
            Swal.fire({
                title: 'Yakin ingin menghapus absensi?',
                text: `Absensi ${name} pada tanggal ${tanggal} akan dihapus!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mengirimkan request DELETE menggunakan Fetch API
                    fetch(`/absensi/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Deleted!', data.message, 'success')
                                    .then(() => location.reload()); // Reload halaman setelah sukses
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                        });
                }
            });
        }


        document.addEventListener('DOMContentLoaded', function() {
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}',
                });
            @endif
        });
    </script>
</x-app-layout>
