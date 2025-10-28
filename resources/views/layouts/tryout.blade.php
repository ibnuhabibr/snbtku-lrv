<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Tryout' }} - {{ config('app.name', 'SNBTKU') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Livewire Styles -->
        @livewireStyles

        <!-- Prevent zoom on mobile -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        
        <!-- Prevent caching for tryout pages -->
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <!-- Full screen layout without navbar -->
        <div class="min-h-screen">
            <!-- Page Content -->
            <main class="h-screen">
                @yield('content')
            </main>
        </div>

        <!-- Livewire Scripts -->
        @livewireScripts

        <!-- Additional Scripts for Tryout -->
        <script>
            // Disable right-click context menu
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });

            // Disable F12, Ctrl+Shift+I, Ctrl+U, etc.
            document.addEventListener('keydown', function(e) {
                // F12
                if (e.keyCode === 123) {
                    e.preventDefault();
                    return false;
                }
                // Ctrl+Shift+I
                if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
                    e.preventDefault();
                    return false;
                }
                // Ctrl+U
                if (e.ctrlKey && e.keyCode === 85) {
                    e.preventDefault();
                    return false;
                }
                // Ctrl+S
                if (e.ctrlKey && e.keyCode === 83) {
                    e.preventDefault();
                    return false;
                }
            });

            // Disable text selection
            document.addEventListener('selectstart', function(e) {
                e.preventDefault();
                return false;
            });

            // Disable drag
            document.addEventListener('dragstart', function(e) {
                e.preventDefault();
                return false;
            });

            // Focus management - keep focus on page
            window.addEventListener('blur', function() {
                setTimeout(function() {
                    window.focus();
                }, 100);
            });

            // Disable print
            window.addEventListener('beforeprint', function(e) {
                e.preventDefault();
                return false;
            });
        </script>
    </body>
</html>