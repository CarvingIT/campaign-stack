<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Campaign Stack') }}</title>

        <link rel="icon" type="image/x-icon" href="/i/campaign-stack-100.png">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('js')
    </head>
    <body class="font-sans antialiased text-zinc-900 dark:text-zinc-100 bg-slate-50 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(254,243,199,0.35),rgba(255,255,255,0))] dark:bg-[#09090B]">
        <div class="min-h-screen bg-slate-50 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(254,243,199,0.35),rgba(255,255,255,0))] dark:bg-[#09090B]">
            @include('layouts.navigation')

            <!-- Page Heading (Fluid Integrated Header) -->
            @isset($header)
                <div class="max-w-7xl mx-auto pt-6 pb-2 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
