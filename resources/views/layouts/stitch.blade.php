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

    <!-- Global delete confirmation modal (Stitch admin) -->
    <div id="confirm-delete-modal" class="hidden fixed inset-0 z-[100]">
        <div id="confirm-delete-backdrop" class="absolute inset-0 bg-slate-900/75"></div>
        <div class="relative min-h-full flex items-end sm:items-center justify-center p-4">
            <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-xl shadow-2xl shadow-black/30 border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center">
                            <span class="material-icons-outlined text-rose-600 dark:text-rose-300">delete_forever</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Confirm delete</h3>
                            <p id="confirm-delete-subtitle" class="text-xs text-slate-500">This action cannot be undone.</p>
                        </div>
                    </div>
                    <button type="button" id="confirm-delete-close" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-400">
                        <span class="sr-only">Close</span>
                        <span class="material-icons-outlined text-lg">close</span>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <p id="confirm-delete-message" class="text-sm text-slate-700 dark:text-slate-200">
                        Are you sure you want to delete this item?
                    </p>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" id="confirm-delete-cancel" class="px-4 py-2 rounded-lg text-sm font-medium border border-slate-200 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 transition-colors">
                        Cancel
                    </button>
                    <button type="button" id="confirm-delete-confirm" class="px-4 py-2 rounded-lg text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Scripts -->
    @yield('scripts')
</body>
</html>
