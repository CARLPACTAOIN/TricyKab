<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
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
                        "on-background": "#1c1b22",
                        "secondary-fixed": "#e4dfff",
                        "surface-bright": "#fcf8ff",
                        "primary-container": "#6258ca",
                        "surface-container-lowest": "#ffffff",
                        "error-container": "#ffdad6",
                        "on-primary-container": "#eae5ff",
                        "outline": "#787584",
                        "outline-variant": "#c8c4d5",
                        "on-secondary-fixed": "#19163f",
                        "on-secondary-container": "#56537f",
                        "tertiary-fixed": "#ffdcbe",
                        "on-tertiary-fixed": "#2c1600",
                        "primary": "#493eb0",
                        "primary-fixed-dim": "#c5c0ff",
                        "surface-container-low": "#f6f2fc",
                        "on-secondary-fixed-variant": "#45426d",
                        "on-tertiary-container": "#ffe3cb",
                        "inverse-primary": "#c5c0ff",
                        "on-error-container": "#93000a",
                        "on-surface": "#1c1b22",
                        "surface-tint": "#584ebf",
                        "inverse-on-surface": "#f3eff9",
                        "on-primary-fixed": "#140067",
                        "tertiary": "#764400",
                        "surface-container-high": "#ebe6f1",
                        "tertiary-container": "#985900",
                        "on-secondary": "#ffffff",
                        "on-surface-variant": "#474553",
                        "surface-variant": "#e5e1eb",
                        "secondary": "#5d5986",
                        "background": "#fcf8ff",
                        "secondary-container": "#cec9fd",
                        "primary-fixed": "#e4dfff",
                        "on-primary": "#ffffff",
                        "surface-container-highest": "#e5e1eb",
                        "on-tertiary-fixed-variant": "#693c00",
                        "inverse-surface": "#312f37",
                        "error": "#ba1a1a",
                        "secondary-fixed-dim": "#c6c1f4",
                        "on-error": "#ffffff",
                        "on-primary-fixed-variant": "#4033a6",
                        "surface": "#fcf8ff",
                        "surface-dim": "#dcd8e2",
                        "surface-container": "#f0ecf6",
                        "tertiary-fixed-dim": "#ffb86f",
                        "on-tertiary": "#ffffff"
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
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glow-primary {
            box-shadow: 0 8px 30px rgba(98, 88, 202, 0.35);
        }
        .map-gradient-overlay {
            background: linear-gradient(to bottom, rgba(252, 248, 255, 0.8) 0%, rgba(252, 248, 255, 0) 20%, rgba(252, 248, 255, 0) 70%, rgba(252, 248, 255, 1) 100%);
        }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-surface selection:bg-primary-fixed selection:text-on-primary-fixed overflow-hidden">
<!-- TopAppBar -->
<nav class="fixed top-0 w-full z-50 bg-[#fcf8ff]/80 dark:bg-[#1c1b22]/80 backdrop-blur-2xl shadow-[0_4px_30px_rgba(0,0,0,0.03)] flex items-center justify-between px-6 py-4 w-full">
<div class="flex items-center gap-4">
<button class="text-[#6258ca] dark:text-[#b0a7ff] hover:opacity-80 transition-opacity active:scale-95 duration-200">
<span class="material-symbols-outlined text-2xl" data-icon="arrow_back">arrow_back</span>
</button>
<h1 class="font-['Inter'] font-semibold tracking-tight text-on-surface text-lg">Book Ride</h1>
</div>
<div class="w-10 h-10 rounded-full overflow-hidden bg-surface-container-high ring-2 ring-white/50">
<img class="w-full h-full object-cover" data-alt="Close-up portrait of a professional driver with a friendly expression, soft natural lighting, high-end photography style" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDWVz0SQtLZ63GAojpIWQFtpihUL8dU5dNWLB6SmoeJkFLqzb-0Q6Uos3bnM5ZVszq0ZFWy9dcOvgrXNpTnCISdepIWkCtSoR1QS1ZgHOBwN9nj7SckRujCBCrogEGd2gX-HUMenWcYtCEAYaT8iuoDntsVz74fpRb9jqARNpqODxA3mCE58NAtA-t1LFSbDh9efnEUIw9z4f6fc4QifWZh9J_iO54Meo7svOeVWrajV5jeB2yebtIdaH8xnyf3ZSWzLwi7vrI1rng"/>
</div>
</nav>
<!-- Map Content (Full-bleed) -->
<main class="relative h-screen w-full pt-0 overflow-hidden">
<div class="absolute inset-0 bg-surface-container-low">
<!-- Simulated Map Visualization -->
<img class="w-full h-full object-cover opacity-60 grayscale-[20%]" data-alt="Minimalist top-down map illustration showing city streets with a highlighted violet route pulse connecting two points in a clean aesthetic" data-location="Poblacion Municipal Hall" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDhmtvgsq5Rtd-VLBO6qGSR-zXdEoLaq3zbug1ojR3wdL48oXRb1Lojk9M_POacxpzD0LfsgfadObxeGloNK572mzGqPr_qGT74bJJ-F75OExlzhWQjigHSt6KBID5BsFuk7SIX_9c7a67ms8QPAxNl58uNTwLhsM4I8nO9ExNizVfC-Hg_s1SKSI_jojcot4DvpoKTXw71QoU7-XytvurK9AAnBHJuWdHV3GK3QRt-MyET6qGW2AbFnF6pJzSAhZse1y_fuamNzUc"/>
<div class="absolute inset-0 map-gradient-overlay pointer-events-none"></div>
<!-- Route Elements -->
<div class="absolute top-1/3 left-1/4 transform -translate-x-1/2 -translate-y-1/2">
<div class="bg-primary text-white p-2 rounded-full shadow-lg flex items-center justify-center">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">location_on</span>
</div>
<div class="mt-2 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full shadow-sm">
<p class="text-[10px] font-bold text-on-surface whitespace-nowrap">Poblacion Municipal Hall</p>
</div>
</div>
<div class="absolute bottom-1/2 right-1/4 transform translate-x-1/2 translate-y-1/2">
<div class="bg-tertiary text-white p-2 rounded-full shadow-lg flex items-center justify-center">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">school</span>
</div>
<div class="mt-2 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full shadow-sm">
<p class="text-[10px] font-bold text-on-surface whitespace-nowrap">Osias National High School</p>
</div>
</div>
<!-- The Route Pulse -->
<svg class="absolute inset-0 w-full h-full pointer-events-none" preserveaspectratio="none">
<path class="opacity-40" d="M 120 280 Q 200 350 400 450" fill="none" stroke="#6258ca" stroke-dasharray="1 15" stroke-linecap="round" stroke-width="6"></path>
<circle class="animate-pulse" cx="260" cy="370" fill="#6258ca" r="8">
<title>Vehicle</title>
</circle>
</svg>
</div>
<!-- Premium Slide-up Panel -->
<section class="absolute bottom-0 w-full bg-surface-container-lowest rounded-t-xl shadow-[0_-20px_50px_rgba(0,0,0,0.08)] px-8 pt-4 pb-10 z-40 transform translate-y-0">
<!-- Drag Handle -->
<div class="flex justify-center mb-8">
<div class="w-12 h-1.5 bg-outline-variant/30 rounded-full"></div>
</div>
<!-- Location Inputs -->
<div class="relative space-y-4 mb-8">
<div class="absolute left-[13px] top-[24px] bottom-[24px] w-[2px] bg-surface-container-highest flex flex-col items-center justify-between">
<div class="w-2 h-2 rounded-full bg-primary -mt-1"></div>
<div class="w-2 h-2 rounded-full bg-tertiary -mb-1"></div>
</div>
<!-- Pickup -->
<div class="flex items-center gap-4 pl-8 pr-4 py-4 bg-surface-container-low rounded-lg">
<div class="flex-1">
<p class="text-[10px] font-bold tracking-widest text-on-surface-variant uppercase mb-1">Pickup Location</p>
<p class="font-semibold text-on-surface text-base">Poblacion Municipal Hall</p>
</div>
<span class="material-symbols-outlined text-outline-variant" data-icon="my_location">my_location</span>
</div>
<!-- Destination -->
<div class="flex items-center gap-4 pl-8 pr-4 py-4 bg-surface-container-low rounded-lg border-2 border-primary/10">
<div class="flex-1">
<p class="text-[10px] font-bold tracking-widest text-primary uppercase mb-1">Destination</p>
<p class="font-semibold text-on-surface text-base">Osias National High School</p>
</div>
<span class="material-symbols-outlined text-primary" data-icon="edit">edit</span>
</div>
</div>
<!-- Ride Type Selector -->
<div class="grid grid-cols-2 gap-4 mb-8">
<button class="flex flex-col items-center p-4 rounded-lg bg-primary-container text-on-primary-container ring-2 ring-primary glow-primary transition-all duration-300">
<span class="material-symbols-outlined text-2xl mb-2" data-icon="group" style="font-variation-settings: 'FILL' 1;">group</span>
<p class="text-xs font-black tracking-widest uppercase">SHARED</p>
</button>
<button class="flex flex-col items-center p-4 rounded-lg bg-surface-container-high text-on-surface-variant hover:bg-surface-variant transition-all">
<span class="material-symbols-outlined text-2xl mb-2" data-icon="person">person</span>
<p class="text-xs font-bold tracking-widest uppercase">SPECIAL</p>
</button>
</div>
<!-- Fare and Payment info -->
<div class="flex items-center justify-between mb-8 px-2">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-tertiary-fixed flex items-center justify-center">
<span class="material-symbols-outlined text-on-tertiary-fixed" data-icon="payments">payments</span>
</div>
<div>
<p class="text-[11px] font-medium text-on-surface-variant">Shared Fare</p>
<p class="text-xl font-extrabold text-on-surface">₱35.00</p>
</div>
</div>
<div class="flex flex-col items-end">
<p class="text-[11px] font-bold text-tertiary uppercase tracking-tighter">Est. 8 mins</p>
<p class="text-[10px] text-on-surface-variant">Arrival Time 4:25 PM</p>
</div>
</div>
<!-- CTA Button -->
<a href="/mockups/passenger/assigned-driver" class="w-full bg-primary-container text-on-primary-container font-headline font-bold py-5 rounded-full glow-primary active:scale-[0.98] transition-all flex items-center justify-center gap-3 text-lg">
                Book Ride
                <span class="material-symbols-outlined" data-icon="chevron_right">chevron_right</span>
</a>
</section>
</main>
<!-- BottomNavBar (Optional Suppression applied as this is a focused task page, but included as per shell logic for Top-Level consistency check) -->
<nav class="fixed bottom-0 w-full rounded-t-xl z-50 bg-white/90 dark:bg-[#1c1b22]/90 backdrop-blur-3xl shadow-[0_-10px_40px_rgba(98,88,202,0.12)] hidden md:hidden">
<div class="flex justify-around items-center px-8 pb-8 pt-4 w-full">
<div class="flex flex-col items-center justify-center bg-[#6258ca] text-white rounded-full px-5 py-2 shadow-[0_8px_24px_rgba(98,88,202,0.3)]">
<span class="material-symbols-outlined" data-icon="home" style="font-variation-settings: 'FILL' 1;">home</span>
<span class="font-['Inter'] text-[11px] font-medium uppercase tracking-widest mt-1">Home</span>
</div>
<div class="flex flex-col items-center justify-center text-[#474553] dark:text-[#a6a4b1] px-5 py-2 hover:bg-[#fcf8ff] dark:hover:bg-[#2b2933] rounded-full transition-all">
<span class="material-symbols-outlined" data-icon="receipt_long">receipt_long</span>
<span class="font-['Inter'] text-[11px] font-medium uppercase tracking-widest mt-1">Activity</span>
</div>
<div class="flex flex-col items-center justify-center text-[#474553] dark:text-[#a6a4b1] px-5 py-2 hover:bg-[#fcf8ff] dark:hover:bg-[#2b2933] rounded-full transition-all">
<span class="material-symbols-outlined" data-icon="person">person</span>
<span class="font-['Inter'] text-[11px] font-medium uppercase tracking-widest mt-1">Account</span>
</div>
</div>
</nav>
</body></html>