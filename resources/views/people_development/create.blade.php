<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah People Development') }} {{ $user->nama }}
            </h2>
        </x-slot>

        <form id="developmentForm" action="{{ route('people_development.store') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">

            <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold mb-4">Informasi Periode</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="periode_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="periode_mulai" id="periode_mulai" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="periode_selesai" class="block text-sm font-medium text-gray-700">Tanggal
                            Selesai</label>
                        <input type="date" name="periode_selesai" id="periode_selesai" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <input type="text" name="jabatan" id="jabatan" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div id="objectivesContainer">
                <!-- Objectives akan ditambahkan di sini oleh JavaScript -->
            </div>

            <div class="mt-4">
                <button type="button" id="addObjectiveBtn" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    + Tambah Objective
                </button>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6 mb-8 mt-8">
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" required rows="2"
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script>
        function applyMasking() {
            $('.uang').mask('000.000.000.000.000', {
                reverse: true
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            let objectiveCounter = 0;
            const objectivesContainer = document.getElementById('objectivesContainer');
            const addObjectiveBtn = document.getElementById('addObjectiveBtn');
            const form = document.getElementById('developmentForm');

            applyMasking();

            // Template untuk Objective baru
            function createObjectiveTemplate() {
                // const objectiveId = `objective_${objectiveCounter}`;
                // objectiveCounter++;
                const currentIndex = objectiveCounter;
                const objectiveId = `objective_${currentIndex}`;
                objectiveCounter++;

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
                            <input type="text" name="objectives[${currentIndex}][name]" required
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
                                    <!-- KPI rows will be added here -->
                                </tbody>
                            </table>
                            <div class="text-sm text-red-600 mt-2 total-weight-message" style="display: none;">
                                Total bobot melebihi 100%!
                            </div>
                            <button type="button" class="mt-2 px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 add-kpi"
                                    data-objective="${currentIndex}">
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
                <input type="text" step="0.01" name="objectives[${objectiveIndex}][kpis][${kpiIndex}][target]" required
                    class="uang w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <select name="objectives[${objectiveIndex}][kpis][${kpiIndex}][tipe_kpi]"
                    class="py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    style="width: 110px;">
                    <option value="persen">%</option>
                    <option value="nominal">Nominal</option>
                    <option value="uang">Rp</option>
                </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" step="0.01" name="objectives[${objectiveIndex}][kpis][${kpiIndex}][realisasi]" required
                    class="uang w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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


            // Fungsi untuk menghitung total bobot
            function calculateTotalWeight() {
                let total = 0;
                document.querySelectorAll('.bobot-input').forEach(input => {
                    const value = parseFloat(input.value) || 0;
                    total += value;
                });
                return total;
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
                    addObjectiveBtn.disabled = true;
                    allAddKpiButtons.forEach(btn => btn.disabled = true);
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

            // Tambah Objective
            addObjectiveBtn.addEventListener('click', function() {
                if (calculateTotalWeight() >= 100) return;

                objectivesContainer.insertAdjacentHTML('beforeend', createObjectiveTemplate());
                applyMasking();
                validateTotalWeight();

                // Event listener untuk tombol tambah KPI
                document.querySelectorAll('.add-kpi').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (calculateTotalWeight() >= 100) return;

                        const objectiveIndex = this.getAttribute('data-objective');
                        const tbody = this.closest('.kpi-container').querySelector(
                            '.kpi-tbody');
                        const kpiIndex = tbody.querySelectorAll('.kpi-row').length;

                        tbody.insertAdjacentHTML('beforeend', createKpiTemplate(
                            objectiveIndex, kpiIndex));
                        applyMasking();
                        validateTotalWeight();
                    });
                });
            });

            // Delegasi event untuk menghapus KPI
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-kpi')) {
                    e.target.closest('.kpi-row').remove();
                    validateTotalWeight();
                }
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
            });

            // Tambahkan 1 objective default saat halaman dimuat
            addObjectiveBtn.click();
        });
    </script>

</x-app-layout>
