<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Lokasi') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-md overflow-hidden">
            <div class="mt-4 mb-4 p-6">
                <form method="POST" action="{{ route('lokasi.update', $lokasi->id) }}">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="nama" :value="__('Nama Lokasi')" />
                        <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama"
                            :value="$lokasi->nama" />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="lokasi" :value="__('Pilih Lokasi')" />
                        <div id="map-container" class="relative w-full h-64 border rounded">
                            <div id="map" class="w-full h-full relative z-10"></div>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="longitude" :value="__('Longitude')" />
                        <x-text-input id="longitude" class="block mt-1 w-full" type="text" name="longitude"
                            :value="$lokasi->longitude" />
                        <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="latitude" :value="__('Latitude')" />
                        <x-text-input id="latitude" class="block mt-1 w-full" type="text" name="latitude"
                            :value="$lokasi->latitude" />
                        <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="radius" :value="__('Radius (meter)')" />
                        <x-text-input id="radius" class="block mt-1 w-full" type="number" name="radius"
                            :value="$lokasi->radius" />
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
            // Default location from database or Jakarta
            const defaultLat = {{ $lokasi->latitude ?? -6.2 }};
            const defaultLng = {{ $lokasi->longitude ?? 106.816666 }};
            const defaultRadius = {{ $lokasi->radius ?? 100 }};
            const defaultLocation = [defaultLat, defaultLng];

            // Initialize map
            const map = L.map("map", {
                center: defaultLocation,
                zoom: 17,
                fullscreenControl: true,
            });

            // Add tile layer
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: "© OpenStreetMap contributors"
            }).addTo(map);

            // Initialize marker
            let marker = L.marker(defaultLocation, {
                draggable: true
            }).addTo(map);

            // Initialize circle
            let circle;
            if (defaultRadius > 0) {
                circle = L.circle(defaultLocation, {
                    radius: defaultRadius,
                    color: '#3b82f6',
                    fillOpacity: 0.2
                }).addTo(map);
            }

            // Update form inputs when marker moves
            function updateFormInputs(lat, lng) {
                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;
            }

            // Update marker position from form inputs
            function updateMarkerFromForm() {
                const lat = parseFloat(document.getElementById("latitude").value);
                const lng = parseFloat(document.getElementById("longitude").value);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const newPos = [lat, lng];
                    marker.setLatLng(newPos);
                    map.panTo(newPos);
                    updateCirclePosition();
                }
            }

            // Update circle radius
            function updateCircleRadius() {
                const radius = parseInt(document.getElementById("radius").value);

                if (!isNaN(radius) && radius > 0) {
                    if (!circle) {
                        circle = L.circle(marker.getLatLng(), {
                            radius: radius,
                            color: '#3b82f6',
                            fillOpacity: 0.2
                        }).addTo(map);
                    } else {
                        circle.setRadius(radius);
                    }
                } else if (circle) {
                    map.removeLayer(circle);
                    circle = null;
                }
            }

            // Update circle position
            function updateCirclePosition() {
                if (circle) {
                    circle.setLatLng(marker.getLatLng());
                }
            }

            // Event when clicking on map
            map.on("click", function(e) {
                marker.setLatLng(e.latlng);
                updateFormInputs(e.latlng.lat, e.latlng.lng);
                updateCirclePosition();
            });

            // Event when dragging marker
            marker.on("dragend", function(e) {
                const pos = marker.getLatLng();
                updateFormInputs(pos.lat, pos.lng);
                updateCirclePosition();
            });

            // Event when form inputs change
            document.getElementById("latitude").addEventListener("change", updateMarkerFromForm);
            document.getElementById("longitude").addEventListener("change", updateMarkerFromForm);
            document.getElementById("radius").addEventListener("input", updateCircleRadius);

            // Add current location button
            const locateButton = L.control({
                position: 'topright'
            });
            locateButton.onAdd = function() {
                const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                div.innerHTML =
                    `<a href="#" title="Gunakan Lokasi Saat Ini" class="bg-white rounded shadow">📍</a>`;
                div.onclick = getCurrentLocation;
                return div;
            };
            locateButton.addTo(map);

            // --- TAMBAHAN: Tambahkan pencarian lokasi ---
            const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false,
                placeholder: 'Cari lokasi...'
            }).on("markgeocode", function(e) {
                const latlng = e.geocode.center;
                map.setView(latlng, 17);
                marker.setLatLng(latlng);
                updateFormInputs(latlng.lat, latlng.lng);
                updateCirclePosition(); // Pastikan lingkaran juga pindah
            }).addTo(map);
            // --- Akhir Bagian Tambahan ---

            // Get current location function
            function getCurrentLocation(e) {
                e.preventDefault();
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            const pos = [position.coords.latitude, position.coords.longitude];
                            marker.setLatLng(pos);
                            map.setView(pos, 17);
                            updateFormInputs(pos[0], pos[1]);
                            updateCirclePosition();
                        },
                        error => {
                            alert("Gagal mendapatkan lokasi: " + error.message);
                        }
                    );
                } else {
                    alert("Browser tidak mendukung geolokasi");
                }
            }
        });
    </script>
</x-app-layout>
