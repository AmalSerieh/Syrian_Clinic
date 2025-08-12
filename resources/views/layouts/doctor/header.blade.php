<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


</head>
<body class="bg-gray-900 text-white">
    <div class="flex">
        @include('layouts.doctor.sidebar')
        <div class="flex-1 flex flex-col">
            @include('layouts.doctor.navbar')
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
