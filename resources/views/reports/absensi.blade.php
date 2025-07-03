<x-app-layout>
    <div class="max-w-2xl mx-auto p-4">
        <div class="p-4 border-2 border-gray-200 border-solid rounded-lg dark:border-gray-700 mt-14">
            <x-slot name="header">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Laporan Absensi') }}
                </h2>
            </x-slot>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <form action="{{ route('reports.absensi.excel') }}" method="GET" class="mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="start_date"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-black">Dari
                                Tanggal</label>
                            <input type="date" name="start_date" id="start_date"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="end_date"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-black">Sampai
                                Tanggal</label>
                            <input type="date" name="end_date" id="end_date"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit"
                            class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                            Export Excel
                        </button>

                        <a href="{{ route('reports.absensi.pdf') }}"
                            onclick="event.preventDefault(); document.getElementById('pdf-form').submit();"
                            class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800">
                            Export PDF
                        </a>
                    </div>
                </form>

                <form id="pdf-form" action="{{ route('reports.absensi.pdf') }}" method="GET" class="hidden">
                    <input type="hidden" name="start_date" id="pdf_start_date">
                    <input type="hidden" name="end_date" id="pdf_end_date">
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            const pdfStartDate = document.getElementById('pdf_start_date');
            const pdfEndDate = document.getElementById('pdf_end_date');

            startDate.addEventListener('change', function() {
                pdfStartDate.value = this.value;
            });

            endDate.addEventListener('change', function() {
                pdfEndDate.value = this.value;
            });
        });
    </script>
</x-app-layout>
