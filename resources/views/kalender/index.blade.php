<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kalender Hari Libur') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-7">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Larger calendar container with day view option -->
            <div id="calendar" class="p-3" style="min-height: 800px; width: 85%; margin: 0 auto;"></div>
        </div>
    </div>

    <!-- Modal with higher z-index -->
    <div id="liburModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden"
        style="z-index: 9999;">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-800">Tambah Hari Libur</h3>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formLibur" class="p-6">
                <input type="hidden" id="selectedDate">
                <input type="hidden" id="eventId">
                <div class="mb-4">
                    <label for="jenis_libur" class="block text-sm font-medium text-gray-700 mb-1">Jenis Libur</label>
                    <select id="jenis_libur" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="Libur">Libur</option>
                        <option value="Cuti Bersama">Cuti Bersama</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <input type="text" id="keterangan" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex justify-end">
                    <button type="button" id="deleteBtn"
                        class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Hapus
                    </button>
                    <div class="flex space-x-2">
                        <button type="button" id="cancelBtn"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Batal
                        </button>

                        <button type="submit" id="saveBtn"
                            class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Simpan
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const modal = document.getElementById('liburModal');
            const modalTitle = document.getElementById('modalTitle');
            const closeModal = document.getElementById('closeModal');
            const cancelBtn = document.getElementById('cancelBtn');
            const deleteBtn = document.getElementById('deleteBtn');
            const saveBtn = document.getElementById('saveBtn');
            const formLibur = document.getElementById('formLibur');
            const selectedDateInput = document.getElementById('selectedDate');
            const eventIdInput = document.getElementById('eventId');
            const keteranganInput = document.getElementById('keterangan');
            const jenisLiburInput = document.getElementById('jenis_libur');

            // Cek permission
            const canManageHolidays = @json(auth()->user()?->hasPermission('Kelola Hari Libur') ?? false);

            let currentEvent = null;
            let isEditMode = false;

            // Fungsi untuk menampilkan SweetAlert info
            function showInfoAlert(title, message) {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'info',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3b82f6',
                });
            }

            // Fungsi untuk menampilkan konfirmasi delete dengan SweetAlert
            function showDeleteConfirmation() {
                return Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan menghapus hari libur ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                });
            }

            // Format tanggal untuk display
            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            // Inisialisasi kalender
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'timeGridDay,dayGridMonth,dayGridWeek'
                },
                height: 'auto',
                contentHeight: 700,
                aspectRatio: 1.5,
                dateClick: function(info) {
                    if (canManageHolidays) {
                        // Mode tambah baru (untuk yang punya permission)
                        resetModal();
                        selectedDateInput.value = info.dateStr;
                        modalTitle.textContent =
                            `Tambah Hari Libur - ${formatDate(selectedDateInput.value)}`;
                        modal.classList.remove('hidden');
                    } else {
                        // Hanya tampilkan info tanggal (untuk yang tidak punya permission)
                        showInfoAlert(
                            'Info Tanggal',
                            `Tanggal: ${formatDate(info.dateStr)}\nAnda tidak memiliki izin untuk mengelola hari libur`
                        );
                    }
                },
                events: {
                    url: '/hari-libur',
                    extraParams: {
                        withTrashed: true
                    }
                },
                eventDisplay: 'block',
                eventColor: '#ef4444',
                eventTextColor: '#ffffff',
                eventClick: function(info) {
                    info.jsEvent.preventDefault();

                    if (canManageHolidays) {
                        // Mode edit (untuk yang punya permission)
                        currentEvent = info.event;
                        isEditMode = true;
                        selectedDateInput.value = info.event.startStr;
                        keteranganInput.value = info.event.title;
                        jenisLiburInput.value = info.event.extendedProps.jenis_libur || 'Libur';
                        eventIdInput.value = info.event.id;
                        deleteBtn.classList.remove('hidden');
                        deleteBtn.classList.add('mr-40')
                        modalTitle.textContent =
                            `Edit Hari Libur - ${formatDate(selectedDateInput.value)}`;
                        modal.classList.remove('hidden');
                    } else {
                        // Hanya tampilkan info event (untuk yang tidak punya permission)
                        showInfoAlert(
                            'Detail Hari Libur',
                            `Tanggal: ${formatDate(info.event.startStr)}\nKeterangan: ${info.event.title}`
                        );
                    }
                }
            });

            calendar.render();

            // Reset modal ke state awal
            function resetModal() {
                formLibur.reset();
                deleteBtn.classList.add('hidden');
                currentEvent = null;
                isEditMode = false;
            }

            // Tutup modal
            [closeModal, cancelBtn].forEach(btn => {
                btn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                    resetModal();
                });
            });

            // Tutup modal ketika klik di luar modal
            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                    resetModal();
                }
            });

            // Handle delete
            deleteBtn.addEventListener('click', function() {
                // Immediately show the delete confirmation
                modal.classList.add('hidden');
                showDeleteConfirmation().then((result) => {
                    if (result.isConfirmed) {
                        const eventId = eventIdInput.value;

                        axios.delete(`/hari-libur/${eventId}`, {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                }
                            })
                            .then(() => {
                                // Close both modals on success
                                modal.classList.add('hidden');
                                resetModal();
                                calendar.refetchEvents();

                                // Show success message
                                Swal.fire(
                                    'Terhapus!',
                                    'Hari libur berhasil dihapus.',
                                    'success'
                                );
                            })
                            .catch(error => {
                                console.error(error);
                                Swal.fire(
                                    'Gagal!',
                                    error.response?.data?.message ||
                                    'Gagal menghapus hari libur',
                                    'error'
                                );
                            });
                    }
                });
            });

            // Handle submit form
            formLibur.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!canManageHolidays) {
                    showToast('Anda tidak memiliki izin untuk mengelola hari libur', 'error');
                    return;
                }

                const data = {
                    tanggal: selectedDateInput.value,
                    keterangan: keteranganInput.value,
                    jenis_libur: jenisLiburInput.value
                };

                // Jika edit mode, gunakan PUT, jika tidak POST
                const method = isEditMode ? 'put' : 'post';
                const url = isEditMode ? `/hari-libur/${eventIdInput.value}` : '/hari-libur';

                axios[method](url, data)
                    .then(response => {
                        modal.classList.add('hidden');
                        resetModal();
                        calendar.refetchEvents();
                        showToast(`Hari libur berhasil ${isEditMode ? 'diupdate' : 'ditambahkan'}`,
                            'success');
                    })
                    .catch(error => {
                        console.error(error);
                        const message = error.response?.data?.message ||
                            `Gagal ${isEditMode ? 'mengupdate' : 'menambahkan'} hari libur`;
                        showToast(message, 'error');
                    });
            });

            // Fungsi untuk menampilkan toast notification
            function showToast(message, type) {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-6 py-3 rounded-md text-white font-medium shadow-lg z-[10000] ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        });
    </script>
</x-app-layout>
