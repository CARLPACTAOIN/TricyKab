<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>TricyKab | Smart Tricycle Dispatch System</title>
    <meta name="description" content="Experience fast, fair, and secure tricycle commuting in the heart of Kabacan.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>

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
                        "secondary": "#23b7e5",
                        "success": "#09ad95",
                        "background-light": "#f6f6f8",
                        "background-dark": "#15141e",
                        "footer-dark": "#283250",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-16px); }
        }
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200">

<!-- Header / Navbar -->
<header class="sticky top-0 z-50 w-full bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-primary/10">
    <nav class="container mx-auto px-6 h-20 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="bg-primary p-1.5 rounded-lg">
                <span class="material-icons text-white text-2xl">electric_rickshaw</span>
            </div>
            <span class="text-2xl font-800 tracking-tight text-primary font-bold">TricyKab</span>
        </div>
        <div class="hidden md:flex items-center gap-8">
            <a class="font-medium hover:text-primary transition-colors" href="#home">Home</a>
            <a class="font-medium hover:text-primary transition-colors" href="#features">Features</a>
            <a class="font-medium hover:text-primary transition-colors" href="#how-it-works">How It Works</a>
            <a class="font-medium hover:text-primary transition-colors" href="#contact">Contact</a>
        </div>
        <div class="flex items-center gap-4">
            @auth
                <a class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg font-semibold shadow-lg shadow-primary/25 transition-all active:scale-95" href="{{ url('/admin/dashboard') }}">
                    Dashboard
                </a>
            @else
                <a class="font-semibold text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="{{ route('login') }}">Admin Login</a>
                <a class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg font-semibold shadow-lg shadow-primary/25 transition-all active:scale-95" href="#contact">
                    Request Access
                </a>
            @endauth
        </div>
    </nav>
</header>

<main>
    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-20 pb-24 md:pt-32 md:pb-40" id="home">
        <div class="container mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
            <div class="z-10">
                <span class="inline-block py-1.5 px-4 rounded-full bg-primary/10 text-primary font-bold text-sm tracking-wide mb-6">
                    SMART DISPATCH FOR KABACAN
                </span>
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-slate-900 dark:text-white leading-[1.1] mb-6">
                    Ride Smart with <span class="text-primary">TricyKab</span>
                </h1>
                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 mb-10 max-w-lg leading-relaxed">
                    Experience fast, fair, and secure tricycle commuting in the heart of Kabacan. No more long waits or unfair fares.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#contact" class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-xl shadow-primary/30 transition-all flex items-center gap-2">
                        Request Access <span class="material-icons">arrow_forward</span>
                    </a>
                    <a href="#features" class="bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-8 py-4 rounded-xl font-bold text-lg transition-all">
                        Learn More
                    </a>
                </div>
                <p class="mt-6 text-sm text-slate-600 dark:text-slate-400">
                    Try the web apps:
                    <a href="{{ route('passenger.app') }}" class="font-semibold text-primary hover:underline">Passenger</a>
                    <span class="mx-2 text-slate-300 dark:text-slate-600">·</span>
                    <a href="{{ route('driver.app') }}" class="font-semibold text-primary hover:underline">Driver</a>
                </p>
                <div class="mt-12 flex items-center gap-4 text-sm text-slate-500">
                    <div class="flex -space-x-3">
                        <img alt="User" class="w-10 h-10 rounded-full border-2 border-white" src="{{ asset('assets/images/stitch/avatar-1.jpg') }}"/>
                        <img alt="User" class="w-10 h-10 rounded-full border-2 border-white" src="{{ asset('assets/images/stitch/avatar-2.jpg') }}"/>
                        <img alt="User" class="w-10 h-10 rounded-full border-2 border-white" src="{{ asset('assets/images/stitch/avatar-3.jpg') }}"/>
                    </div>
                    <p><span class="font-bold text-slate-900 dark:text-white">500+</span> daily riders in Kabacan</p>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -top-20 -right-20 w-96 h-96 bg-primary/20 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-secondary/20 rounded-full blur-3xl opacity-50"></div>
                <div class="animate-float relative z-10 rounded-3xl overflow-hidden shadow-2xl bg-white dark:bg-slate-900 p-4 border border-primary/5">
                    <div class="bg-slate-100 dark:bg-slate-800 rounded-2xl w-full h-[400px] flex items-center justify-center relative">
                        <img alt="Map View" class="absolute inset-0 w-full h-full object-cover opacity-80" src="{{ asset('assets/images/stitch/hero-map.jpg') }}"/>
                        <div class="relative bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-2xl max-w-xs border border-primary/10">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-success/20 text-success flex items-center justify-center">
                                    <span class="material-icons">check_circle</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm">Driver Found!</h4>
                                    <p class="text-xs text-slate-500">Juan Dela Cruz is 2 mins away</p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded"></div>
                                <div class="h-2 w-2/3 bg-slate-100 dark:bg-slate-800 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-slate-100 dark:bg-slate-900/50" id="features">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-primary font-bold tracking-widest text-sm uppercase">Why Choose Us</span>
                <h2 class="text-4xl font-extrabold mt-4 mb-6">Revolutionizing local transport with modern technology</h2>
                <p class="text-slate-600 dark:text-slate-400 text-lg">We provide a seamless experience for both commuters and tricycle drivers, ensuring safety and reliability in every trip.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white dark:bg-slate-800 p-8 rounded-lg shadow-xl shadow-slate-200/50 dark:shadow-none border border-primary/5 hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons text-3xl">location_on</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Real-time Tracking</h3>
                    <p class="text-slate-600 dark:text-slate-400">See exactly where your driver is and get notified the moment they arrive at your location.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white dark:bg-slate-800 p-8 rounded-lg shadow-xl shadow-slate-200/50 dark:shadow-none border border-primary/5 hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-14 h-14 bg-secondary/10 text-secondary rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons text-3xl">payments</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Fair Tariffs</h3>
                    <p class="text-slate-600 dark:text-slate-400">No more haggling. Our system calculates standardized fares based on official local guidelines.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white dark:bg-slate-800 p-8 rounded-lg shadow-xl shadow-slate-200/50 dark:shadow-none border border-primary/5 hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-14 h-14 bg-success/10 text-success rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons text-3xl">verified_user</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Secure Rides</h3>
                    <p class="text-slate-600 dark:text-slate-400">Every driver is verified and registered with the local TODA, ensuring your peace of mind.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-24" id="how-it-works">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold mb-4">Simple 3-Step Process</h2>
                <p class="text-slate-600 dark:text-slate-400">Getting around Kabacan has never been this easy.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-12">
                <!-- Step 01 -->
                <div class="relative group">
                    <span class="absolute -top-10 left-0 text-9xl font-black text-primary/5 dark:text-primary/10 select-none group-hover:text-primary/10 transition-colors">01</span>
                    <div class="relative pt-8">
                        <h3 class="text-2xl font-bold mb-4">Book a Ride</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Enter your destination in the TricyKab app and our smart system will match you with the nearest available driver.</p>
                    </div>
                </div>
                <!-- Step 02 -->
                <div class="relative group">
                    <span class="absolute -top-10 left-0 text-9xl font-black text-primary/5 dark:text-primary/10 select-none group-hover:text-primary/10 transition-colors">02</span>
                    <div class="relative pt-8">
                        <h3 class="text-2xl font-bold mb-4">Meet Driver</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Follow your driver's progress on the map. They'll pick you up exactly where you are in minutes.</p>
                    </div>
                </div>
                <!-- Step 03 -->
                <div class="relative group">
                    <span class="absolute -top-10 left-0 text-9xl font-black text-primary/5 dark:text-primary/10 select-none group-hover:text-primary/10 transition-colors">03</span>
                    <div class="relative pt-8">
                        <h3 class="text-2xl font-bold mb-4">Ride &amp; Pay</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Enjoy a comfortable ride. Pay the standardized fare via cash or digital wallet through our secure system.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-24 bg-primary/5" id="contact">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-4xl font-extrabold mb-8">Get in touch with us</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-12">Have questions about our service or want to partner with us as a driver? We're here to help you move forward.</p>
                    <div class="space-y-6">
                        <div class="flex items-center gap-6 p-4 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-primary/5">
                            <div class="w-12 h-12 bg-primary text-white rounded-lg flex items-center justify-center">
                                <span class="material-icons">place</span>
                            </div>
                            <div>
                                <h4 class="font-bold">Address</h4>
                                <p class="text-slate-600 dark:text-slate-400">Poblacion, Kabacan, Cotabato, Philippines</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 p-4 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-primary/5">
                            <div class="w-12 h-12 bg-secondary text-white rounded-lg flex items-center justify-center">
                                <span class="material-icons">email</span>
                            </div>
                            <div>
                                <h4 class="font-bold">Email</h4>
                                <p class="text-slate-600 dark:text-slate-400">support@tricykab.com</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 p-4 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-primary/5">
                            <div class="w-12 h-12 bg-success text-white rounded-lg flex items-center justify-center">
                                <span class="material-icons">call</span>
                            </div>
                            <div>
                                <h4 class="font-bold">Phone</h4>
                                <p class="text-slate-600 dark:text-slate-400">+63 912 345 6789</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-2xl shadow-primary/10 border border-primary/10">
                    <form class="space-y-6">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold mb-2">First Name</label>
                                <input class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg p-3 focus:ring-primary focus:border-primary" type="text"/>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Last Name</label>
                                <input class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg p-3 focus:ring-primary focus:border-primary" type="text"/>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Email Address</label>
                            <input class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg p-3 focus:ring-primary focus:border-primary" type="email"/>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Message</label>
                            <textarea class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg p-3 focus:ring-primary focus:border-primary" rows="4"></textarea>
                        </div>
                        <button class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/20" type="submit">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="bg-footer-dark text-white pt-20 pb-10">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <!-- Column 1: Brand -->
            <div>
                <div class="flex items-center gap-2 mb-6">
                    <div class="bg-primary p-1 rounded-lg">
                        <span class="material-icons text-white">electric_rickshaw</span>
                    </div>
                    <span class="text-2xl font-bold tracking-tight">TricyKab</span>
                </div>
                <p class="text-slate-400 mb-6 leading-relaxed">
                    The smart choice for tricycle dispatching in Kabacan. We aim to modernize local transport with safety and efficiency.
                </p>
                <div class="flex gap-4">
                    <a class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center hover:bg-primary transition-colors" href="#">
                        <i class="material-icons text-xl">facebook</i>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center hover:bg-primary transition-colors" href="#">
                        <i class="material-icons text-xl">camera</i>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center hover:bg-primary transition-colors" href="#">
                        <i class="material-icons text-xl">share</i>
                    </a>
                </div>
            </div>
            <!-- Column 2: Pages -->
            <div>
                <h4 class="text-lg font-bold mb-6">Explore</h4>
                <ul class="space-y-4 text-slate-400">
                    <li><a class="hover:text-white transition-colors" href="#home">Home</a></li>
                    <li><a class="hover:text-white transition-colors" href="#features">Features</a></li>
                    <li><a class="hover:text-white transition-colors" href="#how-it-works">How It Works</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Driver Portal</a></li>
                </ul>
            </div>
            <!-- Column 3: Account -->
            <div>
                <h4 class="text-lg font-bold mb-6">Account</h4>
                <ul class="space-y-4 text-slate-400">
                    <li><a class="hover:text-white transition-colors" href="#">My Profile</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Ride History</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Privacy Policy</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Terms of Service</a></li>
                </ul>
            </div>
            <!-- Column 4: Contact Info -->
            <div>
                <h4 class="text-lg font-bold mb-6">Contact Info</h4>
                <ul class="space-y-4 text-slate-400">
                    <li class="flex items-start gap-3">
                        <span class="material-icons text-primary text-sm mt-1">place</span>
                        <span>Kabacan, North Cotabato, Philippines</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-icons text-primary text-sm">email</span>
                        <span>info@tricykab.com</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-icons text-primary text-sm">phone</span>
                        <span>+63 912 345 6789</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="pt-8 border-t border-slate-700 text-center text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} TricyKab Dispatch System. All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>
