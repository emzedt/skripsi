<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrasi Wajah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">Anda perlu mendaftarkan wajah Anda terlebih dahulu sebelum dapat mengakses fitur
                        absensi.</p>

                    <div class="flex flex-col items-center">
                        <div id="video-container" class="mb-4 w-full max-w-md relative">
                            <video id="video" width="100%" height="auto" autoplay muted playsinline
                                class="block"></video>
                            <canvas id="capture-canvas" class="hidden"></canvas>
                            <img id="captured-image-preview" class="hidden w-full max-w-md" alt="Hasil Capture">
                        </div>

                        <div id="face-features" class="hidden mb-4 text-center">
                            <p class="text-green-600 font-bold">Wajah terdeteksi!</p>
                        </div>

                        <div id="capture-controls" class="flex flex-col items-center space-y-2 mb-4">
                            <button id="capture-btn"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Ambil Gambar
                            </button>
                            <button id="retake-btn"
                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded hidden">
                                Ambil Ulang
                            </button>
                        </div>

                        <button id="register-btn"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded hidden">
                            Daftarkan Wajah Saya
                        </button>

                        <div id="result-message" class="mt-4 hidden text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="{{ url('/face/dist/face-api.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // Elements
            const video = document.getElementById('video');
            const captureCanvas = document.getElementById('capture-canvas');
            const capturedImagePreview = document.getElementById('captured-image-preview');
            const captureBtn = document.getElementById('capture-btn');
            const retakeBtn = document.getElementById('retake-btn');
            const registerBtn = document.getElementById('register-btn');
            const faceFeatures = document.getElementById('face-features');
            const resultMessage = document.getElementById('result-message');

            // Variables
            let stream = null;
            let capturedImage = null;
            let faceDescriptor = null;
            const label = "{{ $user->id }}";

            // Start video stream
            async function startVideoStream() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: "user",
                            width: 640,
                            height: 480
                        },
                        audio: false
                    });
                    video.srcObject = stream;
                } catch (err) {
                    console.error("Error accessing camera:", err);
                    resultMessage.classList.remove('hidden');
                    resultMessage.innerHTML =
                        '<p class="text-red-600">Error: Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.</p>';
                }
            }

            // Load face-api models
            async function loadModels() {
                try {
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri("{{ url('/face/weights') }}"),
                        faceapi.nets.faceLandmark68Net.loadFromUri("{{ url('/face/weights') }}"),
                        faceapi.nets.faceRecognitionNet.loadFromUri("{{ url('/face/weights') }}")
                    ]);
                    console.log("Models loaded successfully");
                } catch (err) {
                    console.error("Error loading models:", err);
                    resultMessage.classList.remove('hidden');
                    resultMessage.innerHTML =
                        '<p class="text-red-600">Error: Gagal memuat model deteksi wajah.</p>';
                }
            }

            // Capture image
            captureBtn.addEventListener('click', async function() {
                try {
                    // Set canvas dimensions
                    captureCanvas.width = video.videoWidth;
                    captureCanvas.height = video.videoHeight;

                    // Draw video frame to canvas
                    const context = captureCanvas.getContext('2d');
                    context.drawImage(video, 0, 0, captureCanvas.width, captureCanvas.height);

                    // Get image data
                    capturedImage = captureCanvas.toDataURL('image/png');
                    capturedImagePreview.src = capturedImage;

                    // Show captured image and hide video
                    video.classList.add('hidden');
                    capturedImagePreview.classList.remove('hidden');

                    // Change buttons visibility
                    captureBtn.classList.add('hidden');
                    retakeBtn.classList.remove('hidden');
                    registerBtn.classList.remove('hidden');

                    // Detect face in captured image
                    const img = await faceapi.fetchImage(capturedImage);
                    const detections = await faceapi.detectSingleFace(img,
                        new faceapi.TinyFaceDetectorOptions()
                    ).withFaceLandmarks().withFaceDescriptor();

                    if (detections) {
                        faceDescriptor = detections.descriptor;
                        faceFeatures.classList.remove('hidden');
                    } else {
                        faceFeatures.classList.add('hidden');
                        resultMessage.classList.remove('hidden');
                        resultMessage.innerHTML =
                            '<p class="text-red-600">Wajah tidak terdeteksi. Silakan coba lagi.</p>';
                        registerBtn.disabled = true;
                    }
                } catch (err) {
                    console.error("Error capturing image:", err);
                    resultMessage.classList.remove('hidden');
                    resultMessage.innerHTML =
                        '<p class="text-red-600">Error: Gagal mengambil gambar.</p>';
                }
            });

            // Retake image
            retakeBtn.addEventListener('click', function() {
                // Show video and hide captured image
                video.classList.remove('hidden');
                capturedImagePreview.classList.add('hidden');

                // Change buttons visibility
                captureBtn.classList.remove('hidden');
                retakeBtn.classList.add('hidden');
                registerBtn.classList.add('hidden');
                faceFeatures.classList.add('hidden');
                resultMessage.classList.add('hidden');

                // Reset variables
                capturedImage = null;
                faceDescriptor = null;
            });

            // Register face
            registerBtn.addEventListener('click', async function() {
                if (!capturedImage || !faceDescriptor) {
                    resultMessage.classList.remove('hidden');
                    resultMessage.innerHTML =
                        '<p class="text-red-600">Error: Data wajah tidak valid.</p>';
                    return;
                }

                try {
                    registerBtn.disabled = true;
                    resultMessage.classList.remove('hidden');
                    resultMessage.innerHTML =
                        '<p class="text-blue-600">Memproses pendaftaran wajah...</p>';

                    // Prepare data for sending
                    const labeledDescriptors = new faceapi.LabeledFaceDescriptors(label, [
                        faceDescriptor
                    ]);

                    // Kirim gambar dulu
                    const saveImageResponse = await fetch("{{ route('face.save') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            face_image: capturedImage
                        })
                    });

                    const imageResult = await saveImageResponse.json();
                    if (!imageResult.success) {
                        throw new Error(imageResult.message || 'Gagal menyimpan gambar wajah');
                    }

                    // Kemudian simpan descriptor
                    const saveDescripResponse = await fetch(
                        "{{ route('face.ajaxDescrip') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                myData: labeledDescriptors,
                                user_id: {{ $user->id }},
                                image: capturedImage,
                                path: "{{ $user->id }}"
                            })
                        });

                    const descripResult = await saveDescripResponse.json();
                    if (!descripResult.success) {
                        throw new Error(descripResult.message ||
                            'Gagal menyimpan descriptor wajah');
                    }

                    resultMessage.innerHTML =
                        '<p class="text-green-600">Pendaftaran wajah berhasil!</p>';
                    setTimeout(() => {
                        window.location.href = "{{ url('/absensi') }}";
                    }, 2000);
                } catch (err) {
                    console.error("Error registering face:", err);
                    resultMessage.innerHTML = `<p class="text-red-600">Error: ${err.message}</p>`;
                    registerBtn.disabled = false;
                }
            });

            // Initialize
            await loadModels();
            await startVideoStream();
        });
    </script>
</x-app-layout>
