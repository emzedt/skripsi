<aside id="default-sidebar"
    class="fixed top-0 left-0 z-50 w-64 bg-black border-r border-gray-200 dark:bg-black dark:border-gray-700
           transition-transform duration-300 -translate-x-full h-full"
    aria-label="Sidenav">
    <div class="flex flex-col h-full py-5 px-3 bg-black border-r border-gray-200 dark:bg-black dark:border-gray-700">
        <div class="flex-1 overflow-y-auto no-scrollbar px-3">
            <ul class="space-y-2">
                <li>
                    <a href="/dashboard" class="flex items-center justify-center space-x-5 p-4">
                        <img src="/storage/aset/logo_infico.jpg" alt="Logo" class="w-12 h-12">
                        <p class="text-2xl font-normal text-gray-900 dark:text-white">HRIS</p>
                    </a>
                </li>
                <ul class="space-y-2 border-gray-200 dark:border-gray-700">
                    <li>
                        <a href="/dashboard"
                            class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <svg class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 22 21">
                                <path
                                    d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                <path
                                    d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                            </svg>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>
                </ul>
                @hasPermission('Lihat Karyawan')
                    <ul class="border-t space-y-2 border-gray-200 dark:border-gray-700 pt-3">
                    </ul>
                    <li>
                        <button type="button"
                            class="flex items-center justify-between p-2 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            data-collapse-toggle="dropdown-pages">
                            <div class="flex items-center">
                                <svg aria-hidden="true" fill="none"
                                    class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-width="2"
                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />

                                    <path stroke-width="0.5" fill="currentColor"
                                        d="M6 10a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1z" />
                                    <path stroke-width="0.5" fill="currentColor" d="M7 13a1 1 0 100 2h6a1 1 0 100-2H7z" />
                                </svg>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">Data
                                    Master</span>
                            </div>
                            <svg aria-hidden="true" class="w-6 h-6 transition-transform duration-300 rotate-0"
                                data-accordion-icon fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">

                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>

                            </svg>

                        </button>
                        <ul id="dropdown-pages" class="hidden py-2 space-y-2 no-scrollbar">
                            <li>
                                <a href="/karyawan"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Karyawan
                                </a>
                            </li>
                            <li>
                                <a href="/jabatan"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Jabatan
                                </a>
                            </li>
                            <li>
                                <a href="/hak-akses"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Hak Akses
                                </a>
                            </li>
                            <li>
                                <a href="/status-karyawan"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Status Karyawan
                                </a>
                            </li>
                            <li>
                                <a href="/lokasi"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Lokasi
                                </a>
                            </li>
                            <li>
                                <a href="/reset-cuti"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Reset Cuti
                                </a>
                            </li>
                        </ul>
                    </li>
                @endhasPermission
            </ul>
            @hasPermission('Lihat Kalender')
                <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                    <li>
                        <a href="/kalender"
                            class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <svg aria-hidden="true"
                                class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 11h2m2 0h2m2 0h2m-2 4h2M7 15h2m-2 0h2m-2-4h2" />
                            </svg>
                            <span class="ml-3">Kalender</span>
                        </a>
                    </li>
                </ul>
            @endhasPermission
            <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                <li>
                    <button type="button"
                        class="flex items-center justify-between p-2 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                        data-collapse-toggle="dropdown-absensi">
                        <div class="flex items-center">
                            <svg aria-hidden="true"
                                class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="flex-1 ml-3 text-left whitespace-nowrap">Absensi</span>
                        </div>
                        <svg aria-hidden="true" class="w-6 h-6 transition-transform duration-300 rotate-0"
                            data-accordion-icon fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <ul id="dropdown-absensi" class="hidden py-2 space-y-2 no-scrollbar">
                        @hasPermission('Lihat Absensi')
                            <li>
                                <a href="/absensi"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Absensi
                                </a>
                            </li>
                            <li>
                                <a href="/reports/absensi"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Laporan Absensi
                                </a>
                            </li>
                        @endhasPermission
                        @hasPermission('Lihat Absensi Sales')
                            <li>
                                <a href="/absensi-sales"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Absensi Sales
                                </a>
                            </li>
                            <li>
                                <a href="/reports/absensi-sales"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Laporan Absensi Sales
                                </a>
                            </li>
                        @endhasPermission
                    </ul>
                </li>
            </ul>
            @hasPermission('Lihat Pengajuan Cuti')
                <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                    <li>
                        <button type="button"
                            class="flex items-center justify-between p-2 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            data-collapse-toggle="dropdown-cuti">
                            <div class="flex items-center">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 6H11V7C11 7.55228 11.4477 8 12 8C12.5523 8 13 7.55228 13 7V6Z"
                                        fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6 2V4H7V7C7 9.76142 9.23858 12 12 12C9.23858 12 7 14.2386 7 17V20H6V22H18V20H17V17C17 14.2386 14.7614 12 12 12C14.7614 12 17 9.76142 17 7V4H18V2H6ZM9 4H15V7C15 8.65685 13.6569 10 12 10C10.3431 10 9 8.65685 9 7V4ZM9 17V20H15V17C15 15.3431 13.6569 14 12 14C10.3431 14 9 15.3431 9 17Z"
                                        fill="currentColor" />
                                </svg>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">Cuti</span>
                            </div>
                            <svg aria-hidden="true" class="w-6 h-6 transition-transform duration-300 rotate-0"
                                data-accordion-icon fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <ul id="dropdown-cuti" class="hidden py-2 space-y-2 no-scrollbar">
                            <li>
                                <a href="/pengajuan-cuti"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Pengajuan Cuti
                                </a>
                            </li>
                            @hasPermission('Lihat Persetujuan Cuti')
                                <li>
                                    <a href="/persetujuan-cuti"
                                        class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                        Persetujuan Cuti
                                    </a>
                                </li>
                            @endhasPermission
                        </ul>
                    </li>
                </ul>
            @endhasPermission
            <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                <li>
                    <a href="/sakit"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg aria-hidden="true"
                            class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-3">Sakit</span>
                    </a>
                </li>
            </ul>
            <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                <li>
                    <a href="/izin"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg aria-hidden="true"
                            class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-3">Izin</span>
                    </a>
                </li>
            </ul>
            @hasPermission('Lihat Permintaan Lembur')
                <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                    <li>
                        <a href="/permintaan-lembur"
                            class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <svg aria-hidden="true"
                                class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="ml-3">Permintaan Lembur</span>
                        </a>
                    </li>
                </ul>
            @endhasPermission
            @hasPermission('Lihat Gaji')
                <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                    <li>
                        <a href="/gaji"
                            class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <svg aria-hidden="true"
                                class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="ml-3">Data Gaji</span>
                        </a>
                    </li>
                </ul>
            @endhasPermission
            @hasPermission('Lihat Penggajian')
                <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                    <li>
                        <a href="/penggajian"
                            class="flex items-center p-2 text-base font-medium text-white rounded-lg bg-black group hover:bg-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 group-hover:text-white">
                                <path
                                    d="M19 14V6c0-1.1-.9-2-2-2H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zm-9-1c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm13-6v11c0 1.1-.9 2-2 2H4v-2h17V7h2z"
                                    fill="currentColor" />
                            </svg>
                            <span class="ml-3 text-white">Penggajian</span>
                        </a>
                    </li>
                </ul>
            @endhasPermission
            @hasPermission('Lihat People Development')
                <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                    <li>
                        <a href="/people-development"
                            class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <svg aria-hidden="true"
                                class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span class="ml-3">People Development</span>
                        </a>
                    </li>
                </ul>
            @endhasPermission
            @hasPermission('Kelola Sampah')
                <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                    <li>
                        <a href="/trash"
                            class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="ml-3">Sampah</span>
                        </a>
                    </li>
                </ul>
            @endhasPermission
        </div>
    </div>
</aside>
<script defer>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleButton = document.querySelector("[data-drawer-toggle='default-sidebar']");
        const sidebar = document.getElementById("default-sidebar");

        // Fungsi untuk menutup sidebar
        const closeSidebar = () => {
            sidebar.classList.add("-translate-x-full");
            toggleButton.classList.remove("translate-x-[16rem]");
        };

        // Toggle sidebar saat button diklik
        toggleButton.addEventListener("click", function(e) {
            e.stopPropagation(); // Mencegah event bubbling
            if (sidebar.classList.contains("-translate-x-full")) {
                sidebar.classList.remove("-translate-x-full");
                toggleButton.classList.add("translate-x-[16rem]");
            } else {
                closeSidebar();
            }
        });

        // Tutup sidebar saat klik di luar area sidebar
        document.addEventListener("click", function(e) {
            const isClickInsideSidebar = sidebar.contains(e.target);
            const isClickOnToggleButton = toggleButton.contains(e.target);

            if (!isClickInsideSidebar && !isClickOnToggleButton) {
                closeSidebar();
            }
        });
    });
</script>
