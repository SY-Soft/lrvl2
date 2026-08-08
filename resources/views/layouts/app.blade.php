<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $title ?? config('app.name', 'SY Soft'))</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body>

<div
    id="livewire-loading"
    class="position-fixed top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center"
    style="z-index: 99999; background: rgba(255,255,255,.65);"
>
    <div class="text-center">
        <div class="spinner-border" role="status"></div>
        <div class="mt-2">Загрузка...</div>
    </div>
</div>


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
<script>
    document.addEventListener('livewire:init', () => {

        const loader = document.getElementById('livewire-loading');
        let requests = 0;

        const show = () => {
            requests++;

            loader.classList.remove('d-none');
            loader.classList.add('d-flex');
        };

        const hide = () => {
            requests--;

            if (requests <= 0) {
                requests = 0;

                loader.classList.remove('d-flex');
                loader.classList.add('d-none');
            }
        };

        Livewire.hook('request', ({ succeed, fail }) => {

            show();

            succeed(() => hide());
            fail(() => hide());

        });

    });
</script>
</body>
</html>
