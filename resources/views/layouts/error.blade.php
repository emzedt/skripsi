<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRIS Infico</title>
    <link rel="icon" href="{{ asset('logo-infico.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased h-screen overflow-x-hidden">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')
        @include('components.sidebar')

        <!-- Page Content -->
        <main class="pt-0">
            @yield('content')
        </main>

    </div>

</body>

</html>
