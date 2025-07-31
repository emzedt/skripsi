<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit People Development') }} {{ $user->nama }}
            </h2>
        </x-slot>
        <form id="developmentForm" action="{{ route('people_development.update', [$user->id, $development->id]) }}"
            method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" value="{{ $user->id }}">

            <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold mb-4">Informasi Periode</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="periode_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="periode_mulai" id="periode_mulai"
                            value="{{ $development->periode_mulai->format('Y-m-d') }}" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="periode_selesai" class="block text-sm font-medium text-gray-700">Tanggal
                            Selesai</label>
                        <input type="date" name="periode_selesai" id="periode_selesai"
                            value="{{ $development->periode_selesai->format('Y-m-d') }}" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <input type="text" name="jabatan" id="jabatan" value="{{ $development->jabatan }}" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div id="objectivesContainer">
                @foreach ($development->objectives as $objectiveIndex => $objective)
                    <div class="bg-white shadow-md rounded-lg p-6 mb-6 objective-group"
                        id="objective_{{ $objectiveIndex }}">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold">Objective</h2>
                            <button type="button" class="text-red-600 hover:text-red-900 remove-objective"
                                onclick="removeObjective('objective_{{ $objectiveIndex }}')">
                                Hapus Objective
                            </button>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi Objective</label>
                            <input type="text" name="objectives[{{ $objectiveIndex }}][name]"
                                value="{{ $objective->objective }}" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div class="kpi-container mb-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            KPI</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Target</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Realisasi</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Bobot (%)</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="kpi-tbody bg-white divide-y divide-gray-200">
                                    @foreach ($objective->kpis as $kpiIndex => $kpi)
                                        <tr class="kpi-row">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="text"
                                                    name="objectives[{{ $objectiveIndex }}][kpis][{{ $kpiIndex }}][name]"
                                                    value="{{ $kpi->kpi }}" required
                                                    class="w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                <input type="hidden" name="objectives[{{ $objectiveIndex }}][id]"
                                                    value="{{ $objective->id }}">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                                <input type="number" step="0.01"
                                                    name="objectives[{{ $objectiveIndex }}][kpis][{{ $kpiIndex }}][target]"
                                                    required value="{{ $kpi->target ?? '' }}"
                                                    class="w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                <input type="hidden"
                                                    name="objectives[{{ $objectiveIndex }}][kpis][{{ $kpiIndex }}][id]"
                                                    value="{{ $kpi->id }}">
                                                <select
                                                    name="objectives[{{ $objectiveIndex }}][kpis][{{ $kpiIndex }}][tipe_kpi]"
                                                    class="py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                    style="width: 110px;">
                                                    <option value="persen"
                                                        {{ $kpi->tipe_kpi == 'persen' ? 'selected' : '' }}>%</option>
                                                    <option value="nominal"
                                                        {{ $kpi->tipe_kpi == 'nominal' ? 'selected' : '' }}>Nominal
                                                    </option>
                                                    <option value="uang"
                                                        {{ $kpi->tipe_kpi == 'uang' ? 'selected' : '' }}>Rp</option>
                                                </select>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" step="0.01"
                                                    name="objectives[{{ $objectiveIndex }}][kpis][{{ $kpiIndex }}][realisasi]"
                                                    value="{{ $kpi->realisasi }}" required
                                                    class="w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" step="0.01"
                                                    name="objectives[{{ $objectiveIndex }}][kpis][{{ $kpiIndex }}][bobot]"
                                                    value="{{ $kpi->bobot }}" required min="0" max="100"
                                                    class="w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm bobot-input focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                    oninput="validateTotalWeight()">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <button type="button"
                                                    class="text-red-600 hover:text-red-900 remove-kpi">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-sm text-red-600 mt-2 total-weight-message" style="display: none;">
                                Total bobot melebihi 100%!
                            </div>
                            <button type="button"
                                class="mt-2 px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 add-kpi"
                                data-objective="{{ $objectiveIndex }}">
                                + Tambah KPI
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <button type="button" id="addObjectiveBtn" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    + Tambah Objective
                </button>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6 mb-8 mt-8">
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <input type="text" name="keterangan" id="keterangan" value="{{ $development->keterangan }}"
                        required
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <a href="{{ route('people_development.show', $user) }}"
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let objectiveCounter = {{ count($development->objectives) }};
            const objectivesContainer = document.getElementById('objectivesContainer');
            const addObjectiveBtn = document.getElementById('addObjectiveBtn');
            const form = document.getElementById('developmentForm');

            // Template untuk Objective baru
            function createObjectiveTemplate(objectiveIndex) {
                const objectiveId = `objective_${objectiveIndex}`;

                return `
        <div class="bg-white shadow-md rounded-lg p-6 mb-6 objective-group" id="${objectiveId}">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Objective</h2>
                <button type="button" class="text-red-600 hover:text-red-900 remove-objective"
                        onclick="removeObjective('${objectiveId}')">
                    Hapus Objective
                </button>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Deskripsi Objective</label>
                <input type="text" name="objectives[${objectiveIndex}][name]" required
                       class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="kpi-container mb-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KPI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Realisasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bobot (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="kpi-tbody bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
                <div class="text-sm text-red-600 mt-2 total-weight-message" style="display: none;">
                    Total bobot melebihi 100%!
                </div>
                <button type="button" class="mt-2 px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 add-kpi"
                        data-objective="${objectiveIndex}">
                    + Tambah KPI
                </button>
            </div>
        </div>`;
            }

            // Template untuk KPI baru
            function createKpiTemplate(objectiveIndex, kpiIndex) {
                return `
        <tr class="kpi-row">
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" name="objectives[${objectiveIndex}][kpis][${kpiIndex}][name]" required
                       class="w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </td>
            <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                <input type="number" step="0.01" name="objectives[${objectiveIndex}][kpis][${kpiIndex}][target]" required
                       class="w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <select name="objectives[${objectiveIndex}][kpis][${kpiIndex}][tipe_kpi]"
                        class="py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        style="width: 110px;">
                    <option value="persen">%</option>
                    <option value="nominal">Nominal</option>
                    <option value="uang">Rp</option>
                </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" step="0.01" name="objectives[${objectiveIndex}][kpis][${kpiIndex}][realisasi]" required
                       class="w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" step="0.01" name="objectives[${objectiveIndex}][kpis][${kpiIndex}][bobot]" required min="0" max="100"
                       class="w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm bobot-input focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       oninput="validateTotalWeight()">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <button type="button" class="text-red-600 hover:text-red-900 remove-kpi">
                    Hapus
                </button>
            </td>
        </tr>`;
            }

            // Fungsi untuk menghitung total bobot semua KPI
            function calculateTotalWeight() {
                let total = 0;
                document.querySelectorAll('.bobot-input').forEach(input => {
                    const value = parseFloat(input.value) || 0;
                    total += value;
                });
                return parseFloat(total.toFixed(2)); // Memastikan total bobot tidak memiliki banyak desimal
            }

            // Fungsi untuk memvalidasi total bobot
            function validateTotalWeight() {
                const total = calculateTotalWeight();
                const messageElement = document.getElementById('global-weight-message');
                const addObjectiveBtn = document.getElementById('addObjectiveBtn');
                const allAddKpiButtons = document.querySelectorAll('.add-kpi');
                const allBobotInputs = document.querySelectorAll('.bobot-input');

                // Reset semua warning
                allBobotInputs.forEach(input => {
                    input.classList.remove('border-red-500', 'bg-red-50');
                });

                if (total > 100) {
                    // Tampilkan pesan global
                    if (!messageElement) {
                        const newMessage = document.createElement('div');
                        newMessage.id = 'global-weight-message';
                        newMessage.className = 'text-sm text-red-600 mt-2 mb-4 p-2 bg-red-50 rounded';
                        newMessage.textContent =
                            'Total bobot melebihi 100%! Harap sesuaikan bobot KPI yang ditandai.';
                        form.insertBefore(newMessage, objectivesContainer.nextSibling);
                    }

                    // Nonaktifkan tombol tambah
                    addObjectiveBtn.disabled = true;
                    allAddKpiButtons.forEach(btn => btn.disabled = true);

                    // Tandai input yang menyebabkan kelebihan
                    let runningTotal = 0;
                    allBobotInputs.forEach(input => {
                        const value = parseFloat(input.value) || 0;
                        runningTotal += value;

                        if (runningTotal > 100) {
                            input.classList.add('border-red-500', 'bg-red-50');
                        }
                    });

                    return false;
                } else if (total === 100) {
                    if (messageElement) messageElement.remove();
                    addObjectiveBtn.disabled = false; // Aktifkan kembali tombol tambah jika total sudah 100
                    allAddKpiButtons.forEach(btn => btn.disabled = false);
                    return true;
                } else {
                    if (messageElement) messageElement.remove();
                    addObjectiveBtn.disabled = false;
                    allAddKpiButtons.forEach(btn => btn.disabled = false);
                    return true;
                }
            }

            // Fungsi untuk menghapus Objective
            window.removeObjective = function(id) {
                document.getElementById(id).remove();
                validateTotalWeight();
            };

            // Add Objective
            addObjectiveBtn.addEventListener('click', function() {
                const objectiveIndex = document.querySelectorAll('.objective-group').length;
                objectivesContainer.insertAdjacentHTML('beforeend', createObjectiveTemplate(
                    objectiveIndex));
            });

            // Event delegation
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-kpi')) {
                    const objectiveDiv = e.target.closest('.objective-group');
                    const objectiveIndex = Array.from(objectivesContainer.children).indexOf(objectiveDiv);
                    const tbody = objectiveDiv.querySelector('.kpi-tbody');
                    const kpiIndex = tbody.querySelectorAll('.kpi-row').length;
                    tbody.insertAdjacentHTML('beforeend', createKpiTemplate(objectiveIndex, kpiIndex));

                    validateTotalWeight();
                }

                if (e.target.classList.contains('remove-kpi')) {
                    e.target.closest('.kpi-row').remove();
                }

                if (e.target.classList.contains('remove-objective')) {
                    e.target.closest('.objective-group').remove();
                }

                validateTotalWeight();
            });

            // Validasi sebelum submit form
            form.addEventListener('submit', function(e) {
                const total = calculateTotalWeight();
                if (total !== 100) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: `Total bobot harus tepat 100%. Saat ini total bobot adalah ${total}%.`,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }

                document.querySelectorAll('input[name*="[target]"], input[name*="[realisasi]"]').forEach(
                    input => {
                        const row = input.closest('.kpi-row');
                        const typeSelect = row.querySelector('select[name*="[tipe_kpi]"]');

                        if (typeSelect && (typeSelect.value === 'nominal' || typeSelect.value ===
                                'uang')) {
                            convertToRawNumber(input);
                        }
                    });
            });

            // Inisialisasi validasi saat pertama kali load
            validateTotalWeight();

            // Format input dengan pemisah ribuan
            function formatNumberInput(inputElement) {
                // Simpan posisi cursor
                const startPos = inputElement.selectionStart;
                const endPos = inputElement.selectionEnd;

                // Ambil nilai dan bersihkan dari semua karakter non-digit
                let value = inputElement.value.replace(/[^0-9]/g, '');

                if (value !== '') {
                    // Format angka dengan pemisah ribuan (tanpa desimal)
                    const formattedValue = parseInt(value).toLocaleString('id-ID');
                    inputElement.value = formattedValue;

                    // Sesuaikan posisi cursor setelah formatting
                    let newPos = startPos + (formattedValue.length - value.length);
                    inputElement.setSelectionRange(newPos, newPos);
                } else {
                    inputElement.value = '';
                }
            }

            // Konversi ke angka biasa sebelum submit
            function convertToRawNumber(inputElement) {
                inputElement.value = inputElement.value.replace(/[^0-9]/g, '');
            }

            // Event listener untuk memformat angka pada input target dan realisasi
            document.addEventListener('input', function(event) {
                const targetInput = event.target;

                if (targetInput.name.includes('[target]') || targetInput.name.includes('[realisasi]')) {
                    const row = targetInput.closest('.kpi-row');
                    const typeSelect = row.querySelector('select[name*="[tipe_kpi]"]');

                    if (typeSelect && (typeSelect.value === 'nominal' || typeSelect.value === 'uang')) {
                        formatNumberInput(targetInput);
                    }
                }
            });
        });
    </script>
</x-app-layout>
