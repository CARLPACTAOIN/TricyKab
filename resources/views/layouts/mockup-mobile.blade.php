<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TricyKab') }} Mockup</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Inter', sans-serif; }
            .bg-map-pattern {
                background-color: #e5e5e5;
                background-image: repeating-linear-gradient(45deg, #d4d4d4 25%, transparent 25%, transparent 75%, #d4d4d4 75%, #d4d4d4), repeating-linear-gradient(45deg, #d4d4d4 25%, #e5e5e5 25%, #e5e5e5 75%, #d4d4d4 75%, #d4d4d4);
                background-position: 0 0, 10px 10px;
                background-size: 20px 20px;
            }
        </style>
    </head>
    <body class="bg-[#15141e] min-h-screen flex items-center justify-center py-10 antialiased selection:bg-[#6258ca] selection:text-white">
        
        <!-- Mobile Device Simulator -->
        <div class="relative w-full max-w-[400px] h-[850px] bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden border-[6px] border-gray-900 flex flex-col">
            
            <!-- Mobile status bar (dummy) -->
            <div class="h-7 w-full bg-transparent absolute top-0 z-50 flex justify-between items-center px-6 pt-2 pointer-events-none">
                <span class="text-xs font-semibold text-gray-800">9:41</span>
                <div class="flex space-x-1 items-center">
                    <svg class="w-4 h-4 text-gray-800" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm4.707-10.707l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 111.414-1.414L11 12.586l4.293-4.293a1 1 0 011.414 1.414z"></path></svg>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-grow relative h-full w-full flex flex-col">
                @yield('content')
            </main>
        </div>

    </body>
</html>
