@extends('layouts.stitch')

@section('title', 'Fare Configuration')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Fare Configuration</h1>
    <p class="text-slate-500 mt-1">Manage standardized fares, discounts, and special trip rates.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center gap-2">
        <span class="material-icons-outlined">check_circle</span>
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-xl border border-red-200 dark:border-red-800">
        <div class="flex items-center gap-2 mb-2">
            <span class="material-icons-outlined">error</span>
            <span class="font-bold">Please check the following errors:</span>
        </div>
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Main Configuration Form -->
    <div class="xl:col-span-2 space-y-6">
        <!-- Regular Fare Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 dark:text-white">Regular / Shared Trip Settings</h3>
                <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Standard</span>
            </div>
            <div class="p-6">
                <form action="{{ route('fares.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ride_type" value="shared">
                    <input type="hidden" name="effective_date" value="{{ date('Y-m-d') }}">
                    
                    <div class="space-y-6">
                        <!-- Base, Min Dist, Per KM -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Base Fare</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="base_fare" value="{{ $sharedMatrix->base_fare ?? 10 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Min. Distance (km)</label>
                                <div class="relative">
                                    <input type="number" step="0.1" name="minimum_distance" value="{{ $sharedMatrix->minimum_distance ?? 2 }}" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white pr-8" required>
                                    <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none text-slate-500 font-bold">km</div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Succeeding Km Rate</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="per_km_rate" value="{{ $sharedMatrix->per_km_rate ?? 2 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-slate-800">

                        <!-- Surcharges -->
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="material-icons-outlined text-slate-400 text-lg">payments</span>
                                Surcharges & Add-ons
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Per Passenger Add-on</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                        <input type="number" step="0.25" name="per_passenger_addon" value="{{ $sharedMatrix->per_passenger_addon ?? 0 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Rush Hour Surcharge</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                        <input type="number" step="0.25" name="rush_hour_surcharge" value="{{ $sharedMatrix->rush_hour_surcharge ?? 0 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-slate-800">

                        <!-- Discounts -->
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="material-icons-outlined text-slate-400 text-lg">loyalty</span>
                                Discount Settings (Student/Senior/PWD)
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Discount Percentage</label>
                                    <div class="relative">
                                        <input type="number" name="discount_percentage" value="{{ $sharedMatrix->discount_percentage ?? 20 }}" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white pr-8" required>
                                        <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none text-slate-500 font-bold">%</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-slate-800">

                        <!-- Night Diff -->
                         <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="material-icons-outlined text-slate-400 text-lg">nightlight</span>
                                    Night Differential
                                </h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Percentage Increase</label>
                                    <div class="relative">
                                        <input type="number" name="night_diff_percentage" value="{{ $sharedMatrix->night_diff_percentage ?? 0 }}" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white pr-8">
                                        <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none text-slate-500 font-bold">%</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg font-medium shadow-md transition-all active:scale-[0.98]">
                                Save Regular Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Special Trip Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
             <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 dark:text-white">Special / Pakyaw Trip Settings</h3>
                 <span class="px-2 py-1 rounded text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Special</span>
            </div>
            <div class="p-6">
                 <form action="{{ route('fares.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ride_type" value="special">
                    <input type="hidden" name="effective_date" value="{{ date('Y-m-d') }}">
                     
                    <!-- Hidden required fields for validation with defaults/zeros -->
                    <input type="hidden" name="minimum_distance" value="0">
                    <input type="hidden" name="discount_percentage" value="0">
                    <input type="hidden" name="night_diff_percentage" value="0">
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Base Agreed Rate</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="1" name="base_fare" value="{{ $specialMatrix->base_fare ?? 50 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Per Km Rate (Excess)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="1" name="per_km_rate" value="{{ $specialMatrix->per_km_rate ?? 5 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Rush Hour Surcharge</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="rush_hour_surcharge" value="{{ $sharedMatrix->rush_hour_surcharge ?? 0 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg font-medium shadow-md transition-all active:scale-[0.98]">
                                Save Special Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Sidebar -->
    <div class="xl:col-span-1">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm sticky top-24">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white">Fare Preview</h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- Regular Preview -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-dashed border-slate-300 dark:border-slate-700">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium text-slate-500">Regular Trip (5km)</span>
                        <span class="material-icons-outlined text-slate-400 text-lg">directions_bike</span>
                    </div>
                    @php
                        $base = $sharedMatrix->base_fare ?? 10;
                        $perKm = $sharedMatrix->per_km_rate ?? 2;
                        $minDist = $sharedMatrix->minimum_distance ?? 2;
                        
                        $dist = 5;
                        $excess = max(0, $dist - $minDist);
                        $total = $base + ($excess * $perKm);
                    @endphp
                    <div class="flex items-baseline gap-1">
                         <span class="text-2xl font-bold text-slate-900 dark:text-white">
                            ₱ {{ number_format($total, 2) }}
                        </span>
                    </div>
                    <div class="text-xs text-slate-400 mt-1">
                        Base: ₱{{ $base }} ({{ $minDist }}km) + ₱{{ $perKm }}/km
                    </div>
                </div>

                <!-- Discount Preview -->
                 <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 rounded-lg border border-dashed border-emerald-200 dark:border-emerald-800">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Discounted (20% Off)</span>
                        <span class="material-icons-outlined text-emerald-500 text-lg">verified</span>
                    </div>
                    @php
                        $disc = $total * (1 - (($sharedMatrix->discount_percentage ?? 20) / 100));
                    @endphp
                    <div class="flex items-baseline gap-1">
                         <span class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">
                            ₱ {{ number_format($disc, 2) }}
                        </span>
                        <span class="text-sm text-emerald-600/60 line-through">₱ {{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg flex items-start gap-3">
                    <span class="material-icons-outlined text-blue-600 dark:text-blue-400 mt-0.5">info</span>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Changes to basic fare rates will automatically apply to all new dispatch requests.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
