<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SY Soft'))</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body>
    @include('partials.header')

    <main>
        @if (session('status'))
            <div class="container pt-4">
                <div class="alert alert-success d-flex align-items-center gap-2 mb-0" role="alert">
                    <i class="bi bi-check-circle"></i>
                    <div>{{ session('status') }}</div>
                </div>
            </div>
        @endif

            @yield('content')
            {{ $slot ?? '' }}
    </main>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js'])
    @livewireScripts
</body>
</html>
