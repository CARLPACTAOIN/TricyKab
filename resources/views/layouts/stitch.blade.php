<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="html-root">
<script>
    (function() {
        var root = document.documentElement;
        if (localStorage.getItem('theme') === 'dark') {
            root.classList.add('dark');
            root.classList.remove('light');
        } else {
            root.classList.remove('dark');
            root.classList.add('light');
            if (!localStorage.getItem('theme')) {
                localStorage.setItem('theme', 'light');
            }
        }
    })();
</script>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'TricyKab Admin')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { height: 0px; display: none; }
        .chart-bar { transition: height 0.3s ease; }
        /* Same approach as master layout: content has margin-start so fixed sidebar doesn't overlap */
        .stitch-main { margin-inline-start: 0; }
        @media (min-width: 1024px) {
            .stitch-main { margin-inline-start: 16rem; }
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-800 dark:text-slate-200">
    <div class="flex min-h-screen">
        <!-- Sidebar (fixed; .stitch-main has margin-inline-start to clear it) -->
        @include('layouts.partials.stitch_sidebar')
        
        <!-- Main Content -->
        <main class="stitch-main flex-1 min-w-0 min-h-screen flex flex-col transition-all duration-300">
            <!-- Header -->
            @include('layouts.partials.stitch_header')

            <!-- Content -->
            <div class="p-8">
                 @yield('content')
            </div>

            <!-- Footer -->
            <footer class="mt-auto px-8 py-6 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-sm text-slate-500 bg-white dark:bg-slate-900">
                <p>© {{ date('Y') }} TricyKab Admin Portal. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a class="hover:text-primary transition-colors" href="#">Terms of Service</a>
                    <a class="hover:text-primary transition-colors" href="#">Privacy Policy</a>
                </div>
            </footer>
        </main>
    </div>
    
    <!-- Scripts -->
    @yield('scripts')
</body>
</html>
