<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Absensi Keluar') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto p-4 mt-8">
        <!-- Camera Container -->
        <div class="relative w-full aspect-[4/3] rounded-lg border-2 border-gray-300 overflow-hidden">
            <video id="video" autoplay playsinline class="w-full h-full object-cover"></video>
            <canvas id="canvas" class="absolute inset-0 w-full h-full hidden"></canvas>
            <canvas id="face-canvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>
        </div>

        <!-- Button Container -->
        <div class="mt-5 flex justify-center space-x-3">
            <button id="capture-btn" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                Ambil Foto
            </button>
            <button id="submit-btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 hidden">
                Submit Absensi
            </button>
            <button id="retry-btn" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 hidden">
                Ambil Ulang
            </button>
        </div>

        <!-- Hidden Inputs -->
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="foto_keluar" id="photo-data">
    </div>

    {{-- <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="{{ url('/face/dist/face-api.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // DOM Elements
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const faceCanvas = document.getElementById('face-canvas');
            const captureBtn = document.getElementById('capture-btn');
            const submitBtn = document.getElementById('submit-btn');
            const retryBtn = document.getElementById('retry-btn');
            const photoData = document.getElementById('photo-data');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');

            // Global variables
            let stream = null;
            let faceMatcher = null;
            let capturedImage = null;
            let detectedUser = null;
            let isProcessing = false;
            let detectionInterval = null;
            let faceCanvasCtx = faceCanvas.getContext('2d');
            let canvasCtx = canvas.getContext('2d');

            // Initialize the app
            await initApp();

            async function initApp() {
                await initCamera();
                await loadModels();
                await startFaceDetection();
                setupEventListeners();
                getLocation();
            }

            // 1. Initialize Camera
            async function initCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            width: {
                                ideal: 640
                            },
                            height: {
                                ideal: 480
                            },
                            facingMode: 'user'
                        },
                        audio: false
                    });
                    video.srcObject = stream;

                    // Wait for video to be ready
                    await new Promise((resolve) => {
                        video.onloadedmetadata = () => {
                            // Set canvas dimensions to match video
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            faceCanvas.width = video.videoWidth;
                            faceCanvas.height = video.videoHeight;
                            resolve();
                        };
                    });
                } catch (err) {
                    showError('Gagal mengakses kamera: ' + err.message);
                    console.error('Camera error:', err);
                }
            }

            // 2. Load FaceAPI Models
            async function loadModels() {
                try {
                    showLoading('Memuat model pengenalan wajah...');

                    // First load the models
                    await faceapi.nets.tinyFaceDetector.loadFromUri("{{ asset('face/weights') }}");
                    await faceapi.nets.faceLandmark68Net.loadFromUri("{{ asset('face/weights') }}");
                    await faceapi.nets.faceRecognitionNet.loadFromUri("{{ asset('face/weights') }}");

                    // Then load the face data
                    await loadFaceData();

                    Swal.close();
                } catch (err) {
                    showError('Gagal memuat model pengenalan wajah: ' + err.message);
                    console.error('Model loading error:', err);
                }
            }

            // Load face recognition data
            async function startFaceDetection() {
                showLoading('Memuat data wajah, harap tunggu...');

                try {
                    const response = await fetch("{{ url('/ajaxGetNeural') }}", {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();

                    if (data && data.length > 0) {
                        faceMatcher = await createFaceMatcher(data);
                        startDetectionLoop();
                        Swal.close();
                    } else {
                        showError('Tidak ada data wajah yang tersedia');
                    }
                } catch (err) {
                    showError('Gagal memuat data wajah: ' + err.message);
                    console.error('Face data loading error:', err);
                }
            }

            async function createFaceMatcher(data) {
                const labeledFaceDescriptors = await Promise.all(
                    data.map(item => {
                        const descriptors = item.descriptors.map(d => new Float32Array(d));
                        return new faceapi.LabeledFaceDescriptors(item.label, descriptors);
                    })
                );
                return new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);
            }

            function startDetectionLoop() {
                // Clear any existing interval
                if (detectionInterval) {
                    clearInterval(detectionInterval);
                }

                detectionInterval = setInterval(async () => {
                    if (isProcessing || !faceMatcher) return;

                    try {
                        // Detect all faces in the video stream
                        const detections = await faceapi.detectAllFaces(
                                video,
                                new faceapi.TinyFaceDetectorOptions()
                            )
                            .withFaceLandmarks()
                            .withFaceDescriptors();

                        // Clear previous drawings
                        faceCanvasCtx.clearRect(0, 0, faceCanvas.width, faceCanvas.height);
                    } catch (err) {
                        console.error('Detection error:', err);
                    }
                }, 1000); // Run detection every second
            }

            // 4. Setup Event Listeners
            function setupEventListeners() {
                captureBtn.addEventListener('click', capturePhoto);
                submitBtn.addEventListener('click', submitAttendance);
                retryBtn.addEventListener('click', retryCapture);
            }

            async function capturePhoto() {
                if (!stream) {
                    showError('Kamera belum siap');
                    return;
                }

                try {
                    isProcessing = true;
                    clearInterval(detectionInterval);

                    // Capture current frame
                    canvasCtx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    capturedImage = canvas.toDataURL('image/jpeg', 0.8);
                    photoData.value = capturedImage;

                    // Detect faces in captured image
                    const detections = await faceapi.detectAllFaces(
                            canvas,
                            new faceapi.TinyFaceDetectorOptions()
                        )
                        .withFaceLandmarks()
                        .withFaceDescriptors();

                    if (detections.length === 0) {
                        showError('Tidak terdeteksi wajah pada foto');
                        retryBtn.classList.remove('hidden');
                        return;
                    }

                    // If we have face matcher, try to recognize faces
                    if (faceMatcher) {
                        const results = detections.map(d =>
                            faceMatcher.findBestMatch(d.descriptor)
                        );

                        // Find the best match (lowest distance)
                        const bestMatch = results.reduce((prev, current) =>
                            (prev.distance < current.distance) ? prev : current
                        );

                        // Check if recognition is confident enough
                        if (bestMatch.label === "unknown" || bestMatch.distance > 0.5) {
                            showError('Wajah tidak dikenali atau kemiripan rendah');
                            retryBtn.classList.remove('hidden');
                            return;
                        }

                        // Show captured image with detection
                        faceCanvasCtx.clearRect(0, 0, faceCanvas.width, faceCanvas.height);
                        faceCanvasCtx.drawImage(canvas, 0, 0, faceCanvas.width, faceCanvas.height);

                        // Draw the recognition box on the captured image
                        const resizedDetections = faceapi.resizeResults(detections, {
                            width: canvas.width,
                            height: canvas.height
                        });

                        const bestMatchIndex = results.findIndex(r =>
                            r.label === bestMatch.label && r.distance === bestMatch.distance
                        );

                        // Update UI
                        captureBtn.classList.add('hidden');
                        submitBtn.classList.remove('hidden');
                        retryBtn.classList.remove('hidden');

                        // Store the recognized user
                        detectedUser = bestMatch.label;
                    }
                } catch (err) {
                    showError('Gagal mengambil foto: ' + err.message);
                    console.error('Capture error:', err);
                } finally {
                    isProcessing = false;
                }
            }

            async function validateLocation(latitude, longitude, lokasiId = null) {
                try {
                    const formData = new FormData();
                    formData.append('latitude', latitude);
                    formData.append('longitude', longitude);
                    if (lokasiId) formData.append('lokasi_id', lokasiId);

                    const response = await fetch("{{ route('validate-location') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json'
                        }
                    });

                    return await response.json();
                } catch (err) {
                    console.error('Location validation error:', err);
                    return {
                        valid: false,
                        message: 'Gagal memvalidasi lokasi'
                    };
                }
            }

            async function submitAttendance() {
                if (!capturedImage || !detectedUser) {
                    showError('Data tidak lengkap');
                    return;
                }

                try {
                    showLoading('Mengirim data absensi pulang...');

                    const formData = new FormData();
                    formData.append('username', detectedUser);
                    formData.append('foto_keluar', capturedImage); // Changed from foto_masuk to foto_keluar
                    formData.append('latitude', latitudeInput.value);
                    formData.append('longitude', longitudeInput.value);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                    const response = await fetch("{{ route('absensi-keluar.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                        }
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(errorText || 'Network response was not ok');
                    }

                    const result = await response.json();

                    Swal.fire({
                        icon: result.status === 'error' ? 'error' : 'success',
                        title: result.message || (result.status === 'error' ? 'Gagal' : 'Berhasil'),
                        timer: 3000,
                        showConfirmButton: false
                    });

                    if (result.status !== 'error') {
                        setTimeout(() => {
                            window.location.href = "{{ route('absensi.index') }}";
                        }, 1500);
                    } else {
                        retryBtn.classList.remove('hidden');
                    }
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: err.message.includes('radius lokasi terdekat') ?
                            'Anda berada di luar radius lokasi terdekat' : err.message,
                        showConfirmButton: true
                    });
                    retryBtn.classList.remove('hidden');
                }
            }

            function retryCapture() {
                resetCaptureUI();
                startDetectionLoop();
            }

            function resetCaptureUI() {
                // Clear captured image
                capturedImage = null;
                detectedUser = null;
                photoData.value = '';

                // Reset buttons
                captureBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
                retryBtn.classList.add('hidden');

                // Clear canvas
                faceCanvasCtx.clearRect(0, 0, faceCanvas.width, faceCanvas.height);
            }

            // 5. Location Services
            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            latitudeInput.value = position.coords.latitude;
                            longitudeInput.value = position.coords.longitude;
                        },
                        error => {
                            console.error('Geolocation error:', error);
                            showError('Gagal mendapatkan lokasi: ' + error.message);
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000
                        }
                    );
                } else {
                    showError('Browser tidak mendukung geolocation');
                }
            }

            // Helper functions
            function showLoading(message) {
                Swal.fire({
                    title: 'Loading...',
                    text: message,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            }

            function showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
                isProcessing = false;
            }
        });
    </script>
</x-app-layout>
