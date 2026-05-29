<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SY Soft'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    </main>

    @include('partials.footer')
</body>
</html>
