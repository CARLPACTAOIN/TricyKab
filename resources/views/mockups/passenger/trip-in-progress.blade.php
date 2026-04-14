<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Trip In Progress</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .map-tint {
            background-color: rgba(98, 88, 202, 0.08);
            mix-blend-mode: multiply;
        }
    </style>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "on-tertiary-fixed": "#2c1600",
                    "on-primary": "#ffffff",
                    "tertiary-fixed-dim": "#ffb86f",
                    "surface-tint": "#584ebf",
                    "surface-container-low": "#f6f2fc",
                    "secondary-fixed": "#e4dfff",
                    "on-primary-fixed": "#140067",
                    "surface-container-lowest": "#ffffff",
                    "on-background": "#1c1b22",
                    "on-secondary-fixed-variant": "#45426d",
                    "error": "#ba1a1a",
                    "on-error": "#ffffff",
                    "on-tertiary-fixed-variant": "#693c00",
                    "surface-variant": "#e5e1eb",
                    "on-primary-container": "#eae5ff",
                    "surface-container-high": "#ebe6f1",
                    "primary-fixed": "#e4dfff",
                    "background": "#fcf8ff",
                    "inverse-on-surface": "#f3eff9",
                    "tertiary-container": "#985900",
                    "surface-bright": "#fcf8ff",
                    "outline": "#787584",
                    "on-primary-fixed-variant": "#4033a6",
                    "on-surface": "#1c1b22",
                    "on-tertiary": "#ffffff",
                    "inverse-primary": "#c5c0ff",
                    "inverse-surface": "#312f37",
                    "primary": "#493eb0",
                    "surface": "#fcf8ff",
                    "error-container": "#ffdad6",
                    "on-secondary-container": "#56537f",
                    "on-error-container": "#93000a",
                    "secondary-container": "#cec9fd",
                    "surface-container-highest": "#e5e1eb",
                    "secondary-fixed-dim": "#c6c1f4",
                    "on-secondary-fixed": "#19163f",
                    "surface-dim": "#dcd8e2",
                    "secondary": "#5d5986",
                    "on-surface-variant": "#474553",
                    "primary-container": "#6258ca",
                    "on-tertiary-container": "#ffe3cb",
                    "tertiary": "#764400",
                    "outline-variant": "#c8c4d5",
                    "surface-container": "#f0ecf6",
                    "primary-fixed-dim": "#c5c0ff",
                    "on-secondary": "#ffffff",
                    "tertiary-fixed": "#ffdcbe"
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
        }
      }
    </script>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-background text-on-surface antialiased overflow-hidden">
<!-- Top Navigation Shell -->
<header class="fixed top-0 w-full z-50 bg-white/80 dark:bg-slate-950/80 backdrop-blur-xl flex justify-between items-center px-6 py-4 w-full shadow-[0px_40px_60px_-5px_rgba(0,0,0,0.06)]">
<button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100/50 transition-colors active:scale-95 duration-200">
<span class="material-symbols-outlined text-indigo-600">arrow_back</span>
</button>
<div class="flex flex-col items-center">
<span class="uppercase tracking-widest text-[10px] font-extrabold text-teal-600 mb-0.5">STATUS</span>
<h1 class="text-lg font-bold text-indigo-600 tracking-tight">TRIP IN PROGRESS</h1>
</div>
<button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100/50 transition-colors active:scale-95 duration-200">
<span class="material-symbols-outlined text-indigo-600">more_vert</span>
</button>
</header>
<!-- Main Content Canvas: Map View -->
<main class="relative h-screen w-full bg-surface-container">
<!-- Map Background -->
<div class="absolute inset-0 z-0">
<img class="w-full h-full object-cover grayscale opacity-50 contrast-125" data-alt="top-down aerial view of urban city streets with stylized purple and indigo color grading and minimal building detail" data-location="Manila" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDUZsMA144BtjxGsKuUvM39K0rUAKERu13lBRalod5V4CBc-4LMyjcLryZFKlXShOdyE5diIFQAc_6xs318Qeg6_f8i5hiiDp-zZYrCkLtQcur_YkXj009y2dBnbZ5y68b9M7R4chqC1ACfg0eCLZpiiSciX6c3QIXnTqfLliHRiMD3EeIcfh4b3NOQwVfDk8_won3prvuNVM_9Uvyy1ydy0w_YUHLCblerxbkY25e1WdaoE6xgPG5Lxs1M5ilycYJJ07HYl-mDSOo"/>
<div class="absolute inset-0 map-tint"></div>
</div>
<!-- Vehicle & Route Visualization -->
<div class="absolute inset-0 z-10 flex items-center justify-center pointer-events-none">
<!-- Route Pulse Trail -->
<div class="relative">
<div class="absolute w-[300px] h-2 bg-primary-container/20 -translate-x-1/2 -translate-y-1/2 rounded-full blur-sm"></div>
<div class="absolute w-[200px] h-1.5 bg-primary/40 -translate-x-1/2 -translate-y-1/2 rounded-full shadow-[0_0_20px_rgba(73,62,176,0.5)]"></div>
<!-- Vehicle Icon (Tricycle Representation) -->
<div class="relative -translate-x-1/2 -translate-y-1/2">
<div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center shadow-[0_0_40px_rgba(73,62,176,0.4)] border-4 border-white animate-pulse">
<span class="material-symbols-outlined text-white text-3xl" style="font-variation-settings: 'FILL' 1;">directions_car</span>
</div>
</div>
</div>
</div>
<!-- Floating Badge -->
<div class="absolute top-24 left-1/2 -translate-x-1/2 z-20">
<div class="bg-surface-container-lowest/90 backdrop-blur-md px-6 py-2.5 rounded-full shadow-[0_20px_40px_rgba(0,0,0,0.08)] flex items-center gap-3">
<div class="w-2.5 h-2.5 bg-teal-500 rounded-full animate-ping"></div>
<span class="text-xs font-bold tracking-widest text-teal-600 uppercase">ON TRACK</span>
</div>
</div>
<!-- Bottom Sheet Panel -->
<section class="fixed bottom-0 left-0 w-full z-40 px-4 pb-4">
<div class="bg-surface-container-lowest rounded-xl shadow-[0_-24px_60px_rgba(0,0,0,0.08)] max-w-2xl mx-auto overflow-hidden">
<!-- Handlebar -->
<div class="w-full flex justify-center py-3">
<div class="w-12 h-1.5 bg-surface-container-high rounded-full"></div>
</div>
<div class="px-8 pb-10">
<!-- ETA Section -->
<div class="flex items-end justify-between mb-8">
<div>
<p class="text-on-surface-variant text-sm font-medium mb-1">Arriving in</p>
<h2 class="text-6xl font-extrabold text-on-surface tracking-tighter">6 min</h2>
</div>
<div class="text-right">
<p class="text-primary font-bold text-lg">1.2 km away</p>
<p class="text-on-surface-variant text-xs">Target: 4:30 PM</p>
</div>
</div>
<!-- Tonal Shift Section for Driver Details -->
<div class="bg-surface-container-low rounded-lg p-5 flex items-center justify-between mb-8">
<div class="flex items-center gap-4">
<div class="relative">
<img class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-sm" data-alt="portrait of a friendly middle-aged Filipino man with a warm smile wearing a casual collared shirt" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCTEYGI87M9oElHVcgtWAv5mEWZ9L3GQ7Dymr6Edh6ZQ2DSQ7VDCUKCkDlSmZAIEhM0yJFeAGB4P8RpKl_Wi9e1zdmtbjqC5YvKwavdnEoaRLTXKqgoPWHTsW9M5gvzihYxqVIVppP36n5BqyU_TnOMyVDdVC-zxcmFTWs_59_9ouVBaUpFiZzbA3EOcFrtdcUKSRfkMOmHxjeofVnwfsajzFN36gpgSLo0JDADuWpvw7M9NFqxiPup3UnR4xaF5mHrBw9pz7kDOlE"/>
<div class="absolute -bottom-1 -right-1 bg-primary w-6 h-6 rounded-full flex items-center justify-center border-2 border-surface-container-low">
<span class="material-symbols-outlined text-[14px] text-white" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
</div>
<div>
<h3 class="text-lg font-bold text-on-surface leading-tight">Juan Dela Cruz</h3>
<p class="text-on-surface-variant text-sm font-medium tracking-wide uppercase">XYZ-5678 • Tricycle</p>
</div>
</div>
<div class="flex gap-2">
<button class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-primary shadow-sm hover:bg-primary/5 transition-colors">
<span class="material-symbols-outlined">call</span>
</button>
<button class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-primary shadow-sm hover:bg-primary/5 transition-colors">
<span class="material-symbols-outlined">chat_bubble</span>
</button>
</div>
</div>
<!-- Action Cluster -->
<div class="grid grid-cols-5 gap-3">
<a href="/mockups/passenger/book-ride" class="col-span-1 border-2 border-error/20 rounded-full flex flex-col items-center justify-center py-4 text-error hover:bg-error/5 transition-all active:scale-95">
<span class="material-symbols-outlined mb-1" style="font-variation-settings: 'FILL' 1;">emergency_home</span>
<span class="text-[10px] font-bold uppercase tracking-tighter">SOS</span>
</a>
<button class="col-span-4 bg-primary-container text-on-primary rounded-full py-4 px-6 flex items-center justify-center gap-3 shadow-[0_16px_32px_rgba(98,88,202,0.25)] hover:brightness-110 active:scale-95 transition-all">
<span class="material-symbols-outlined">ios_share</span>
<span class="font-bold tracking-tight text-lg">Share Trip Details</span>
</button>
</div>
</div>
</div>
</section>
</main>
<!-- Bottom Navigation Shell (Suppressed contextually but included as per Blueprint for hierarchy check) -->
<!-- Prohibiting Navbar here as per "Task-Focused" rule: sub-page with back action implies temporary departure from global nav -->
<!-- Footer spacer for bottom sheet overlap protection -->
<div class="h-40"></div>
</body></html>