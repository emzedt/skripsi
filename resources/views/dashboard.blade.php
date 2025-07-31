<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
            @if (Auth::check())
                <p class="text-sm text-gray-600">Welcome, {{ Auth::user()->nama }}</p>
            @else
                <p class="text-sm text-gray-600">Belum login</p>
            @endif
        </h2>
    </x-slot>

    <div class="py-12 mt-16">
        <div class="max-w-7xl mx-auto my-auto sm:px-6 lg:px-8">
            <div class="md:p-0 p-6">
                <form method="GET" action="{{ route('dashboard') }}" class="max-w-md mb-8">
                    <div class="flex items-center gap-4">
                        <label for="bulan" class="text-sm font-medium text-gray-700">Pilih Bulan:</label>
                        <input type="month" name="bulan" id="bulan"
                            value="{{ request('bulan', now()->format('Y-m')) }}"
                            class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit"
                            class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Tampilkan
                        </button>
                    </div>
                </form>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                    @hasPermission('Lihat Karyawan')
                        <div
                            class="bg-white rounded-lg shadow-md p-6 text-gray-700 flex flex-col items-center justify-center">
                            <div class="bg-gray-100 rounded-md p-3 mb-2">
                                <img src="{{ asset('asset/people-svgrepo-com.svg') }}" alt=""
                                    class="w-8 h-8 svg-colored">
                            </div>
                            <h3 class="text-lg font-semibold mb-1">Total Karyawan</h3>
                            <p class="text-2xl font-bold">{{ $users->count() }}</p>
                        </div>
                    @endhasPermission

                    <div
                        class="bg-white rounded-lg shadow-md p-6 text-gray-700 flex flex-col items-center justify-center">
                        <div class="bg-gray-100 rounded-md p-3 mb-2">
                            <img src="{{ asset('asset/enter-people-svgrepo-com.svg') }}" alt="" class="w-8 h-8">
                        </div>
                        <h3 class="text-lg font-semibold mb-1">Masuk</h3>
                        <p class="text-2xl font-bold">{{ $absenMasuk->count() }}</p>
                    </div>

                    @if (Auth::user()->isAdmin())
                        <div
                            class="bg-white rounded-lg shadow-md p-6 text-gray-700 flex flex-col items-center justify-center">
                            <div class="bg-gray-100 rounded-md p-3 mb-2">
                                <img src="{{ asset('asset/close-lg-svgrepo-com.svg') }}" alt="" class="w-8 h-8">
                            </div>
                            <h3 class="text-lg font-semibold mb-1">Alfa</h3>
                            <p class="text-2xl font-bold">{{ $jumlahAlfaSeluruhUser }}</p>
                        </div>
                    @elseif(Auth::user())
                        <div
                            class="bg-white rounded-lg shadow-md p-6 text-gray-700 flex flex-col items-center justify-center">
                            <div class="bg-gray-100 rounded-md p-3 mb-2">
                                <img src="{{ asset('asset/close-lg-svgrepo-com.svg') }}" alt=""
                                    class="w-8 h-8">
                            </div>
                            <h3 class="text-lg font-semibold mb-1">Alfa</h3>
                            <p class="text-2xl font-bold">{{ $alfa->count() }}</p>
                        </div>
                    @endif

                    <div
                        class="bg-white rounded-lg shadow-md p-6 text-gray-700 flex flex-col items-center justify-center">
                        <div class="bg-gray-100 rounded-md p-3 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-1">Sakit</h3>
                        <p class="text-2xl font-bold">{{ $sakit->count() }}</p>
                    </div>

                    @hasPermission('Lihat Permintaan Lembur')
                        <div
                            class="bg-white rounded-lg shadow-md p-6 text-gray-700 flex flex-col items-center justify-center">
                            <div class="bg-gray-100 rounded-md p-3 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold mb-1">Lembur</h3>
                            <p class="text-2xl font-bold">{{ $lembur->count() }}</p>
                        </div>
                    @endhasPermission

                    <div
                        class="bg-white rounded-lg shadow-md p-6 text-gray-700 flex flex-col items-center justify-center">
                        <div class="bg-gray-100 rounded-md p-3 mb-2">
                            <svg viewBox="0 0 24 24" fill="none" class="w-8 h-8"
                                class="flex-shrink-0 w-6 h-6 text-gray-400 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 6H11V7C11 7.55228 11.4477 8 12 8C12.5523 8 13 7.55228 13 7V6Z"
                                    fill="currentColor" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6 2V4H7V7C7 9.76142 9.23858 12 12 12C9.23858 12 7 14.2386 7 17V20H6V22H18V20H17V17C17 14.2386 14.7614 12 12 12C14.7614 12 17 9.76142 17 7V4H18V2H6ZM9 4H15V7C15 8.65685 13.6569 10 12 10C10.3431 10 9 8.65685 9 7V4ZM9 17V20H15V17C15 15.3431 13.6569 14 12 14C10.3431 14 9 15.3431 9 17Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-1">Cuti</h3>
                        <p class="text-2xl font-bold">{{ $cuti->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        </ </x-app-layout>
