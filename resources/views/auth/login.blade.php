<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>TricyKab Admin Login</title>
    <meta name="description" content="Sign in to TricyKab Admin Panel">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <!-- Tailwind Config -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#6258ca",
                        "primary-dark": "#4e46a3",
                        "accent-red": "#f03d25",
                        "background-light": "#f6f6f8",
                        "background-dark": "#15141e",
                        "neutral-muted": "#6b7280",
                        "neutral-border": "#e5e7eb",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1.5rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <!-- Logo Branding -->
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="flex items-center justify-center space-x-2">
            <div class="bg-primary p-2 rounded-lg shadow-md">
                <span class="material-icons text-white text-3xl">electric_rickshaw</span>
            </div>
            <h1 class="text-3xl font-bold text-primary tracking-tight">TricyKab</h1>
        </a>
    </div>

    <!-- Login Card -->
    <div class="bg-white dark:bg-background-dark border border-neutral-border/50 dark:border-white/10 rounded-lg shadow-xl overflow-hidden">
        <div class="p-8 md:p-10">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Sign In</h2>
                <p class="text-neutral-muted dark:text-gray-400 text-sm">Admin access only. Passenger and driver sign-in uses OTP in the mobile apps.</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('login') }}" class="space-y-6" method="POST">
                @csrf

                <!-- Email Field -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block" for="email">Email</label>
                    <div class="relative">
                        <input class="w-full px-4 py-3 rounded-lg border-neutral-border dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-gray-400" id="email" name="email" placeholder="Enter your email" required type="email" value="{{ old('email') }}" autofocus/>
                        <span class="material-icons absolute right-3 top-3 text-gray-400 text-xl">alternate_email</span>
                    </div>
                    @error('email')
                        <p class="text-accent-red text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block" for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-semibold text-accent-red hover:underline transition-colors" href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input class="w-full px-4 py-3 rounded-lg border-neutral-border dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-gray-400" id="password" name="password" placeholder="Enter your password" required type="password" autocomplete="current-password"/>
                        <button class="absolute right-3 top-3 text-gray-400 hover:text-primary transition-colors" type="button" onclick="togglePassword('password', this)">
                            <span class="material-icons text-xl">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-accent-red text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input class="h-4 w-4 text-primary focus:ring-primary border-neutral-border rounded transition-all" id="remember-me" name="remember" type="checkbox"/>
                    <label class="ml-2 block text-sm text-gray-600 dark:text-gray-400" for="remember-me">
                        Remember password?
                    </label>
                </div>

                <!-- Sign In Button -->
                <div>
                    <button class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-4 rounded-lg shadow-lg shadow-primary/30 transition-all transform active:scale-[0.98]" type="submit">
                        Sign In
                    </button>
                </div>
            </form>

            <!-- Admin access note -->
            <div class="text-center">
                <p class="text-xs text-neutral-muted dark:text-gray-500">
                    Need access? Contact the LGU/TMU system administrator.
                </p>
            </div>
        </div>
    </div>

    <!-- Page Footer Info -->
    <div class="mt-8 text-center">
        <p class="text-xs text-neutral-muted dark:text-gray-500 uppercase tracking-widest">
            &copy; {{ date('Y') }} TricyKab Dispatch System. All Rights Reserved.
        </p>
    </div>
</div>

<!-- Decorative Elements -->
<div class="fixed top-0 left-0 -z-10 w-full h-full overflow-hidden pointer-events-none opacity-50">
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 right-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
</div>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('.material-icons');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>

</body>
</html>
