<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Ride Status - Luminous Concierge</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "tertiary-fixed-dim": "#ffb86f",
                    "on-tertiary-fixed": "#2c1600",
                    "tertiary-container": "#985900",
                    "surface-container-low": "#f6f2fc",
                    "secondary-fixed": "#e4dfff",
                    "outline-variant": "#c8c4d5",
                    "on-tertiary": "#ffffff",
                    "on-secondary-fixed": "#19163f",
                    "primary-fixed-dim": "#c5c0ff",
                    "secondary-container": "#cec9fd",
                    "on-primary-container": "#eae5ff",
                    "tertiary": "#764400",
                    "on-secondary": "#ffffff",
                    "error-container": "#ffdad6",
                    "background": "#fcf8ff",
                    "surface-dim": "#dcd8e2",
                    "surface-tint": "#584ebf",
                    "on-secondary-container": "#56537f",
                    "primary-fixed": "#e4dfff",
                    "on-error": "#ffffff",
                    "inverse-on-surface": "#f3eff9",
                    "surface-container": "#f0ecf6",
                    "on-primary": "#ffffff",
                    "surface-container-highest": "#e5e1eb",
                    "secondary": "#5d5986",
                    "surface-bright": "#fcf8ff",
                    "on-primary-fixed": "#140067",
                    "on-tertiary-fixed-variant": "#693c00",
                    "on-secondary-fixed-variant": "#45426d",
                    "secondary-fixed-dim": "#c6c1f4",
                    "primary-container": "#6258ca",
                    "primary": "#493eb0",
                    "on-surface": "#1c1b22",
                    "error": "#ba1a1a",
                    "tertiary-fixed": "#ffdcbe",
                    "surface-variant": "#e5e1eb",
                    "on-primary-fixed-variant": "#4033a6",
                    "on-surface-variant": "#474553",
                    "on-error-container": "#93000a",
                    "inverse-surface": "#312f37",
                    "outline": "#787584",
                    "on-tertiary-container": "#ffe3cb",
                    "surface": "#fcf8ff",
                    "surface-container-high": "#ebe6f1",
                    "surface-container-lowest": "#ffffff",
                    "on-background": "#1c1b22",
                    "inverse-primary": "#c5c0ff"
            },
            "borderRadius": {
                    "DEFAULT": "1rem",
                    "lg": "2rem",
                    "xl": "3rem",
                    "full": "9999px"
            },
            "fontFamily": {
                    "headline": ["Inter"],
                    "body": ["Inter"],
                    "label": ["Inter"]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .pulse-ring {
            box-shadow: 0 0 0 0 rgba(98, 88, 202, 0.4);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(98, 88, 202, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 20px rgba(98, 88, 202, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(98, 88, 202, 0); }
        }
        body { font-family: 'Inter', sans-serif; }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-background text-on-surface overflow-hidden">
<!-- Top Navigation Bar -->
<header class="fixed top-0 w-full z-50 bg-[#fcf8ff]/80 dark:bg-[#1c1b22]/80 backdrop-blur-2xl shadow-[0_4px_30px_rgba(0,0,0,0.03)] flex items-center justify-between px-6 py-4">
<div class="flex items-center gap-4">
<button class="active:scale-95 duration-200 transition-opacity hover:opacity-80">
<span class="material-symbols-outlined text-[#6258ca] dark:text-[#b0a7ff]">arrow_back</span>
</button>
<h1 class="font-semibold tracking-tight text-on-surface text-lg">Book Ride</h1>
</div>
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center">
<span class="material-symbols-outlined text-on-surface-variant">more_vert</span>
</div>
</header>
<!-- Full Screen Map Background -->
<div class="fixed inset-0 z-0">
<img class="w-full h-full object-cover" data-alt="Modern minimalist city map view with soft pastel colors and simplified road networks in light purple and white tones" data-location="Manila" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA4i_Z6RHS73hajqHdFzzABmxD5WmALO8BQdC-WJx60OOBEGzknCN0qzzvnIrraszShu2Skpe-opbseAv8wKFUeNeZGN4Q58guWgy6q3UL2FxBBMnxGITMzmhgCfnkGFWzH1FEKgqq9WjvUvzWPAAFxSdtbSV5IoOybfDNfXXD-6DQPYlri55Q2UB8k13f1ZWv2cuun0z-8dGor5sy_Arwf98CxoBmyojab3YOxBisrhey3VnDIE7tsTL7N1EJK6UST6L1QJPV6qVo"/>
<!-- Driver Marker with Pulse -->
<div class="absolute top-[40%] left-[60%] -translate-x-1/2 -translate-y-1/2 z-10">
<div class="relative">
<div class="pulse-ring absolute inset-0 rounded-full bg-primary/20 w-16 h-16 -m-4"></div>
<div class="bg-primary-container w-8 h-8 rounded-full flex items-center justify-center shadow-lg border-2 border-white">
<span class="material-symbols-outlined text-white text-sm" style="font-variation-settings: 'FILL' 1;">electric_rickshaw</span>
</div>
</div>
</div>
<!-- User Marker -->
<div class="absolute top-[55%] left-[35%] -translate-x-1/2 -translate-y-1/2 z-10">
<div class="bg-on-surface w-4 h-4 rounded-full border-2 border-white shadow-md"></div>
</div>
<!-- Route Pulse Line (Visual Hack) -->
<div class="absolute top-[40%] left-[35%] w-[30%] h-[20%] border-l-4 border-t-4 border-dashed border-primary/40 rounded-tl-3xl z-0"></div>
</div>
<!-- Floating Status Pill -->
<div class="fixed top-24 left-1/2 -translate-x-1/2 z-20">
<div class="bg-white/70 backdrop-blur-md px-6 py-2 rounded-full shadow-[0_8px_32px_rgba(0,0,0,0.08)] border border-white/20 flex items-center gap-3">
<div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
<span class="text-[10px] font-bold tracking-[0.15em] uppercase text-primary">DRIVER ON THE WAY</span>
</div>
</div>
<!-- Bottom Sheet Content -->
<main class="fixed bottom-0 left-0 w-full z-40">
<div class="bg-white rounded-t-[3rem] shadow-[0_-20px_60px_rgba(0,0,0,0.12)] px-8 pt-4 pb-12">
<!-- Drag Handle -->
<div class="w-12 h-1.5 bg-surface-container rounded-full mx-auto mb-8"></div>
<div class="flex flex-col gap-8">
<!-- ETA Section -->
<div class="flex justify-between items-end">
<div>
<p class="text-[11px] font-medium uppercase tracking-widest text-on-surface-variant mb-1">Arriving in</p>
<h2 class="text-6xl font-black tracking-tighter text-on-surface">4 <span class="text-2xl font-bold tracking-normal">min</span></h2>
</div>
<div class="text-right">
<div class="inline-flex items-center gap-1 bg-surface-container-low px-3 py-1 rounded-full mb-2">
<span class="material-symbols-outlined text-sm text-tertiary" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="text-sm font-semibold text-on-surface">4.9</span>
</div>
<p class="text-sm font-medium text-on-surface-variant">Osias TODA</p>
</div>
</div>
<!-- Driver Spotlight Card -->
<div class="bg-surface-container-low rounded-xl p-6 flex items-center gap-5">
<div class="relative">
<img class="w-16 h-16 rounded-2xl object-cover border-2 border-white shadow-sm" data-alt="Professional portrait of a friendly smiling middle-aged Filipino man with a clean haircut against a neutral grey background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuARKYdCl4VtvknzUAtqNA2c5EDyJy0nsrT618ugIQk1hM5zgxQ_tl2dh_pGVEtzsO-heclO4B1fFbnv_niEwPprpt0kGJBl1rVE3RgJZO8d8KeaBMeIZYhkoo_z8b4Q3fz1600lnmdoRnSQkq--3a7S8SiaRwewyRPprDYKek4o0uhVkxOBYg8zkqEFD3_bhhf9b4-ebcGLYEs5X69odxsKdCKbLN6GqkulCexDZ8Kqlm2atPLDM_Sf-m2lZOjZrcJ-A4ACJPDBrck"/>
<div class="absolute -bottom-1 -right-1 bg-primary text-white p-1 rounded-lg">
<span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1;">verified</span>
</div>
</div>
<div class="flex-1">
<h3 class="text-xl font-bold text-on-surface tracking-tight">Juan Dela Cruz</h3>
<p class="text-sm font-medium text-primary tracking-wide">XYZ-5678</p>
<p class="text-xs text-on-surface-variant mt-1">Lumina Premium Rickshaw</p>
</div>
<div class="flex flex-col gap-2">
<button class="w-10 h-10 rounded-full bg-surface-container-lowest shadow-sm flex items-center justify-center text-on-surface active:scale-95 transition-transform">
<span class="material-symbols-outlined">shield</span>
</button>
</div>
</div>
<!-- Action Buttons -->
<div class="flex flex-col gap-4">
<a href="/mockups/passenger/trip-in-progress" class="w-full py-5 bg-primary-container text-on-primary-container rounded-full font-bold text-lg shadow-[0_12px_24px_rgba(98,88,202,0.3)] active:scale-95 transition-all flex items-center justify-center gap-3">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">chat_bubble</span>
                        Contact Juan (Simulate Trip)
                    </a>
<button class="w-full py-4 text-on-surface-variant font-semibold text-sm hover:text-error transition-colors">
                        Cancel Request
                    </button>
</div>
</div>
</div>
</main>
<!-- Bottom Navigation Bar Shell (Hidden on Focus Interaction) -->
<nav class="fixed bottom-0 w-full rounded-t-[3rem] z-50 bg-white/90 backdrop-blur-3xl shadow-[0_-10px_40px_rgba(98,88,202,0.12)] hidden">
<div class="flex justify-around items-center px-8 pb-8 pt-4 w-full">
<div class="flex flex-col items-center justify-center bg-[#6258ca] text-white rounded-full px-5 py-2 shadow-[0_8px_24px_rgba(98,88,202,0.3)]">
<span class="material-symbols-outlined">home</span>
<span class="font-['Inter'] text-[11px] font-medium uppercase tracking-widest mt-1">Home</span>
</div>
<div class="flex flex-col items-center justify-center text-[#474553] px-5 py-2">
<span class="material-symbols-outlined">receipt_long</span>
<span class="font-['Inter'] text-[11px] font-medium uppercase tracking-widest mt-1">Activity</span>
</div>
<div class="flex flex-col items-center justify-center text-[#474553] px-5 py-2">
<span class="material-symbols-outlined">person</span>
<span class="font-['Inter'] text-[11px] font-medium uppercase tracking-widest mt-1">Account</span>
</div>
</div>
</nav>
</body></html>