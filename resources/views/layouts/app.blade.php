<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <link rel="icon" href="{{ asset('logo-infico.ico') }}" type="image/x-icon">

    <title>HRIS Infico</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.tailwindcss.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /*
              Versi ini memperbaiki warna garis pemisah dan memastikan SEMUA teks
              di dalam link (termasuk sub-menu) berwarna putih.
            */

        /* PERBAIKAN 1: Mengatur warna garis pemisah (border)
            */
        #default-sidebar .border-t {
            border-top-color: #4b5563 !important;
            /* Tailwind's gray-600, lebih terlihat */
        }

        /* PERBAIKAN 2: Mengatur warna SEMUA teks di dalam link dan tombol.
              Selector ini lebih umum dan mencakup teks di sub-menu.
            */
        #default-sidebar a,
        #default-sidebar button {
            color: white !important;
        }

        /* Mengatur warna ikon default menjadi abu-abu */
        #default-sidebar a svg,
        #default-sidebar button svg {
            color: #9ca3af;
            /* Tailwind's gray-400 */
        }

        /* Mengatur warna latar, teks, dan ikon saat di-hover */
        #default-sidebar a:hover,
        #default-sidebar button:hover {
            background-color: #374151;
            /* Tailwind's gray-700 */
        }

        #default-sidebar a:hover,
        #default-sidebar button:hover,
        #default-sidebar a:hover svg,
        #default-sidebar button:hover svg {
            color: white !important;
        }

        /* Aturan khusus untuk teks 'HRIS' di logo agar tetap putih */
        #default-sidebar a p {
            color: white;
        }
    </style>
</head>

<body class="font-sans antialiased h-screen overflow-x-hidden">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')
        @include('components.sidebar')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow pt-16 fixed w-full top-0 z-40">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="pt-32">
            {{ $slot }}
        </main>

    </div>

    @stack('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.tailwindcss.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: '{{ session('success') }}',
                    showConfirmButton: true,
                    timer: 3000
                });
            @endif
        });
    </script>
</body>

</html>
