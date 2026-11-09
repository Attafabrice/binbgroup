<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', "BinB' group | Immobilier, BTP et services en Côte d'Ivoire")</title>
    <meta name="description" content="@yield('description', "BinB' group, entreprise ivoirienne spécialisée dans le foncier, la construction de bâtiments et de routes, la maintenance informatique, la fourniture de bureaux et l'événementiel.")">
    <meta name="theme-color" content="#0b5a9e">

    <meta property="og:title" content="@yield('og_title', "BinB' group | Nous transformons vos rêves en réalité")">
    <meta property="og:description" content="@yield('og_description', "Foncier, bâtiment, route, maintenance et événementiel en Côte d'Ivoire.")">
    <meta property="og:image" content="{{ asset('assets/images/hero.webp') }}">
    <meta property="og:type" content="website">

    <link rel="icon" type="image/webp" sizes="32x32" href="{{ asset('assets/icons/favicon-32.webp') }}">
    <link rel="icon" type="image/webp" sizes="192x192" href="{{ asset('assets/icons/favicon-192.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icons/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @stack('preload')

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Permet au CSS de savoir que le JS est actif (animations, onglets) -->
    <script>document.documentElement.classList.add('js');</script>
    <script src="{{ asset('js/script.js') }}" defer></script>
    @stack('head')
</head>
<body>
    @include('partials.icons')

    <a class="skip-link" href="#main">Aller au contenu</a>

    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.lightbox')

    @stack('scripts')
</body>
</html>
