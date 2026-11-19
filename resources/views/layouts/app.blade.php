<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Чудо Обувь')
    </title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}">
    
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <script defer src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('catalog') }}">
                <img class="brand-logo" src="{{ asset('assets/images/logo.png') }}" alt="Чудо Обувь">
                <span class="brand-name">Чудо Обувь</span>
            </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

<div class="collapse navbar-collapse" id="mainNavigation">

    @auth

        <ul class="navbar-nav me-auto">

            <li class="nav-item">
                <a class="nav-link" href="{{ route('catalog') }}">
                    Каталог
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('cart.index') }}">
                    Корзина

                    @if (array_sum(session('cart', [])) > 0)
                        <span class="badge discount-badge">
                            {{ array_sum(session('cart', [])) }}
                        </span>
                    @endif

                </a>
            </li>

        </ul>

        <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">

            <a class="nav-link" href="{{ route('home') }}">
                <span class="navbar-text fw-semibold">
                    {{ auth()->user()->full_name }}
                </span>
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button
                    type="submit"
                    class="btn btn-outline-secondary btn-sm"
                >
                    Выйти
                </button>
            </form>

        </div>

    @else

        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">
                    Войти
                </a>
            </li>
        </ul>

    @endauth

</div>
            </div>
        </nav>

<main class="py-4">

    <div class="container d-flex justify-content-center">

        @php
            $icons = [
                'success' => 'fa-check-circle',
                'danger' => 'fa-exclamation-triangle',
                'info' => 'fa-info-circle',
            ];
        @endphp

        @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $type)

            @if (session($key))

                <div
                    class="col-10 alert alert-{{ $type }} alert-dismissible d-flex align-items-center"
                    role="alert"
                >

                    <i class="fas {{ $icons[$type] }} me-2 fs-5"></i>

                    <div class="flex-grow-1">
                        {{ session($key) }}
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Закрыть"
                    ></button>

                </div>

            @endif

        @endforeach

    </div>

    @yield('content')

</main>
    </div>
    @stack('scripts')
</body>
</html>
