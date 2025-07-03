@extends('layouts.error')

@section('content')
    <div class="min-h-screen bg-white flex flex-col items-center justify-center p-6 overflow-hidden relative">
        <!-- Giant 403 Text -->
        <div class="flex items-center justify-center">
            <div style="font-size: 10rem;" class="font-bold text-black opacity-70 leading-none">
                403
            </div>
        </div>

        <!-- Content Container -->
        <div class="max-w-md w-full text-center space-y-6 relative z-10 bg-white bg-opacity-90 p-8 rounded-lg">
            <!-- Main Message -->
            <h1 class="text-4xl font-light text-gray-800">Akses Ditolak</h1>

            <!-- Description -->
            <div class="text-gray-600 space-y-3 text-lg">
                <p>Anda tidak memiliki izin untuk mengakses sumber ini.</p>
                <p>Silahkan periksa hak akses Anda atau hubungi dukungan.</p>
            </div>


            <!-- Action Buttons -->
            <div class="pt-8 flex justify-center gap-4">
                <a href="{{ url()->previous() }}"
                    class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
                <a href="{{ url('/') }}"
                    class="px-6 py-3 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection
