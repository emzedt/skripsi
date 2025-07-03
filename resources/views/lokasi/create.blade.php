<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Lokasi') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-md overflow-hidden">
            <div class="mt-4 mb-4 p-6">
                <form method="POST" action="{{ route('lokasi.store') }}">
                    @csrf
                    <div>
                        <x-input-label for="nama" :value="__('Nama Lokasi')" />
                        <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama"
                            :value="old('nama')" />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="lokasi" :value="__('Pilih Lokasi')" />
                        <div id="map-container" class="relative w-full h-64 border rounded">
                            <div id="map" class="w-full h-full relative z-10">
                            </div>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="longitude" :value="__('Longitude')" />
                        <x-text-input id="longitude" class="block mt-1 w-full" type="text" name="longitude"
                            :value="old('longitude')" readonly />
                        <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="latitude" :value="__('Latitude')" />
                        <x-text-input id="latitude" class="block mt-1 w-full" type="text" name="latitude"
                            :value="old('latitude')" readonly />
                        <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="radius" :value="__('Radius (meter)')" />
                        <x-text-input id="radius" class="block mt-1 w-full" type="number" name="radius"
                            :value="old('radius')" />
                        <x-input-error :messages="$errors->get('radius')" class="mt-2" />
                    </div>
                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('lokasi.index') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const defaultLocation = [-6.200000, 106.816666]; // Jakarta

            // Inisialisasi Leaflet Map
            const map = L.map("map", {
                center: defaultLocation,
                zoom: 13,
                fullscreenControl: true, // Tambahkan tombol fullscreen
                fullscreenControlOptions: {
                    position: "topright",
                },
            });

            // Tambahkan Layer Peta
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: "&copy; OpenStreetMap contributors",
            }).addTo(map);

            let marker = L.marker(defaultLocation, {
                draggable: true
            }).addTo(map);

            // Tombol "Gunakan Lokasi Saat Ini"
            const locateButton = L.control({
                position: 'topright'
            });
            locateButton.onAdd = function(map) {
                const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                div.innerHTML = `
                    <a title="Gunakan Lokasi Saat Ini"
                       class="bg-white rounded-full shadow-md hover:bg-gray-100 cursor-pointer">
                        📍
                    </a>
                `;
                div.onclick = () => getCurrentLocation();
                return div;
            };
            locateButton.addTo(map);

            // Fungsi deteksi lokasi saat ini
            function getCurrentLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            const userLat = position.coords.latitude;
                            const userLng = position.coords.longitude;

                            // Update marker dan view
                            marker.setLatLng([userLat, userLng]);
                            map.setView([userLat, userLng], 17);
                            updateInputs(userLat, userLng);

                        },
                        error => {
                            Swal.fire("Error!", "Izinkan akses GPS atau pastikan GPS aktif", "error");
                        }
                    );
                } else {
                    Swal.fire("Browser Tidak Support", "GPS tidak tersedia di browser ini", "warning");
                }
            }

            function updateInputs(lat, lng) {
                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;
            }

            marker.on("dragend", function(e) {
                const position = marker.getLatLng();
                updateInputs(position.lat, position.lng);
            });

            map.on("click", function(e) {
                marker.setLatLng(e.latlng);
                updateInputs(e.latlng.lat, e.latlng.lng);
            });

            // Tambahkan pencarian lokasi
            const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false
            }).on("markgeocode", function(e) {
                const latlng = e.geocode.center;
                map.setView(latlng, 13);
                marker.setLatLng(latlng);
                updateInputs(latlng.lat, latlng.lng);
            }).addTo(map);

            // Fungsi untuk toggle ukuran peta
            const toggleButton = document.getElementById("toggle-map");
            let isExpanded = false;

            toggleButton.addEventListener("click", function(e) {
                e.preventDefault();
                const mapContainer = document.getElementById("map-container");

                if (isExpanded) {
                    mapContainer.style.height = "256px";
                    toggleButton.textContent = "🔽";
                } else {
                    mapContainer.style.height = "80vh";
                    toggleButton.textContent = "🔼";
                }

                setTimeout(() => {
                    map.invalidateSize(); // Perbarui ukuran peta setelah animasi
                }, 300);

                isExpanded = !isExpanded;
            });

            // Event untuk deteksi fullscreen
            map.on("enterFullscreen", function() {
                console.log("Map masuk fullscreen");
            });

            map.on("exitFullscreen", function() {
                console.log("Map keluar fullscreen");
            });
        });
    </script>
</x-app-layout>
