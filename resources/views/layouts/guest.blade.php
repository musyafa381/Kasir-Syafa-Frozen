<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - Syafa Frozen Food</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-badge.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body, html, *, *::before, *::after,
        input, select, textarea, button, optgroup,
        h1, h2, h3, h4, h5, h6, p, span, a, label, th, td, li,
        .font-sans, .font-mono, .font-serif,
        [class*="font-"] {
            font-family: 'Urbanist', system-ui, -apple-system, BlinkMacSystemFont, sans-serif !important;
            font-feature-settings: 'liga' 1, 'calt' 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased selection:bg-[#a0bddb]/40 selection:text-[#163144] min-h-screen">
    {{ $slot }}
    @livewireScripts
</body>
</html>
