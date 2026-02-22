<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>TricyKab Admin Registration</title>
    <meta name="description" content="Create your TricyKab Admin account">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

    <!-- Material Icons Outlined -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <!-- Tailwind Config -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#6258ca",
                        "background-light": "#f6f6f8",
                        "background-dark": "#15141e",
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

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .auth-card-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col items-center justify-center p-6 font-display">

<!-- Branding Logo Section -->
<div class="mb-8 flex flex-col items-center">
    <a href="{{ url('/') }}" class="flex flex-col items-center">
        <div class="w-16 h-16 bg-primary rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-primary/20">
            <span class="material-icons-outlined text-white text-4xl">electric_rickshaw</span>
        </div>
        <h1 class="text-3xl font-bold text-primary tracking-tight">TricyKab</h1>
    </a>
</div>

<!-- Registration Card -->
<div class="w-full max-w-[450px] bg-white dark:bg-slate-900 rounded-lg auth-card-shadow p-8 md:p-10 border border-slate-100 dark:border-slate-800">
    <!-- Header -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-semibold text-slate-800 dark:text-white mb-2">Sign Up</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Create your TricyKab Admin account</p>
    </div>

    <!-- Form -->
    <form class="space-y-5" method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Full Name Field -->
        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider" for="name">
                Full Name
            </label>
            <div class="relative">
                <input class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm" id="name" name="name" placeholder="Enter your full name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"/>
            </div>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Field -->
        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider" for="email">
                Email Address
            </label>
            <div class="relative">
                <input class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm" id="email" name="email" placeholder="Enter your email" type="email" value="{{ old('email') }}" required autocomplete="username"/>
            </div>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Field -->
        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider" for="password">
                Password
            </label>
            <div class="relative">
                <input class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm pr-12" id="password" name="password" placeholder="Create a password" type="password" required autocomplete="new-password"/>
                <button class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors" type="button" onclick="togglePassword('password', this)">
                    <span class="material-icons-outlined text-xl">visibility</span>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password Field -->
        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider" for="password_confirmation">
                Confirm Password
            </label>
            <div class="relative">
                <input class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm pr-12" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" type="password" required autocomplete="new-password"/>
                <button class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors" type="button" onclick="togglePassword('password_confirmation', this)">
                    <span class="material-icons-outlined text-xl">visibility</span>
                </button>
            </div>
            @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Terms -->
        <div class="flex items-center pt-2">
            <input class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary" id="terms" type="checkbox"/>
            <label class="ml-2 text-xs text-slate-500 dark:text-slate-400 leading-tight" for="terms">
                I agree to the <a class="text-primary hover:underline" href="#">Terms of Service</a> and <a class="text-primary hover:underline" href="#">Privacy Policy</a>.
            </label>
        </div>

        <!-- Action Button -->
        <div class="pt-4">
            <button class="w-full bg-primary hover:bg-primary/90 text-white font-semibold py-3.5 rounded-lg transition-all transform active:scale-[0.98] shadow-lg shadow-primary/20 flex items-center justify-center gap-2" type="submit">
                Create Account
                <span class="material-icons-outlined text-sm">arrow_forward</span>
            </button>
        </div>
    </form>

    <!-- Social Divider -->
    <div class="relative my-8">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-100 dark:border-slate-800"></div>
        </div>
        <div class="relative flex justify-center text-xs uppercase">
            <span class="bg-white dark:bg-slate-900 px-2 text-slate-400">Or sign up with</span>
        </div>
    </div>

    <!-- Social Sign Up -->
    <div class="grid grid-cols-2 gap-4 mb-8">
        <button class="flex items-center justify-center gap-2 py-2.5 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-medium text-slate-600 dark:text-slate-300">
            <svg class="w-4 h-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Google
        </button>
        <button class="flex items-center justify-center gap-2 py-2.5 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-medium text-slate-600 dark:text-slate-300">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#1877F2" xmlns="http://www.w3.org/2000/svg">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Facebook
        </button>
    </div>

    <!-- Footer Links -->
    <div class="text-center pt-2">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Already have an account?
            <a class="text-primary font-semibold hover:underline decoration-2 underline-offset-4" href="{{ route('login') }}">Sign In</a>
        </p>
    </div>
</div>

<!-- Decorative Elements -->
<div class="fixed top-0 right-0 p-10 -mr-20 -mt-20 pointer-events-none">
    <div class="w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
</div>
<div class="fixed bottom-0 left-0 p-10 -ml-20 -mb-20 pointer-events-none">
    <div class="w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
</div>

<!-- Background Pattern -->
<div class="fixed inset-0 z-[-1] opacity-30 pointer-events-none" style="background-image: radial-gradient(#6258ca 0.5px, transparent 0.5px); background-size: 24px 24px;"></div>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('.material-icons-outlined');
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
