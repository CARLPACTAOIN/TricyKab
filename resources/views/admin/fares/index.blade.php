@extends('layouts.stitch')

@section('title', 'Fare Rules')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Fare Rules</h1>
    <p class="text-slate-500 mt-1">Manage shared and special fare rules aligned with LGU policy.</p>
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
    <div class="xl:col-span-2 space-y-6">
        <!-- Shared Fare Rules -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 dark:text-white">Shared Ride Rules</h3>
                <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Shared</span>
            </div>
            <div class="p-6">
                <form action="{{ route('fares.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ride_type" value="shared">
                    <input type="hidden" name="effective_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="multiplier" value="1">

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Base Fare</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="base_fare" value="{{ $sharedMatrix->base_fare ?? 15 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Per Km Rate</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="per_km_rate" value="{{ $sharedMatrix->per_km_rate ?? 2.5 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Min Fare</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="min_fare" value="{{ $sharedMatrix->min_fare ?? 0 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Max Fare</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="max_fare" value="{{ $sharedMatrix->max_fare ?? 999 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg font-medium shadow-md transition-all active:scale-[0.98]">
                                Save Shared Rule
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Special Fare Rules -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 dark:text-white">Special Ride Rules</h3>
                <span class="px-2 py-1 rounded text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Special</span>
            </div>
            <div class="p-6">
                <form action="{{ route('fares.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ride_type" value="special">
                    <input type="hidden" name="effective_date" value="{{ date('Y-m-d') }}">

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Base Fare</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="base_fare" value="{{ $specialMatrix->base_fare ?? 50 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Per Km Rate</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="per_km_rate" value="{{ $specialMatrix->per_km_rate ?? 5 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Multiplier</label>
                                <input type="number" step="0.1" name="multiplier" value="{{ $specialMatrix->multiplier ?? 1.5 }}" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Min Fare</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="min_fare" value="{{ $specialMatrix->min_fare ?? 0 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Max Fare</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-500 font-semibold">₱</div>
                                    <input type="number" step="0.25" name="max_fare" value="{{ $specialMatrix->max_fare ?? 999 }}" class="pl-8 block w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg font-medium shadow-md transition-all active:scale-[0.98]">
                                Save Special Rule
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
                @php
                    $sharedBase = $sharedMatrix->base_fare ?? 15;
                    $sharedRate = $sharedMatrix->per_km_rate ?? 2.5;
                    $sharedMin = $sharedMatrix->min_fare ?? 0;
                    $sharedMax = $sharedMatrix->max_fare ?? 999;

                    $specialBase = $specialMatrix->base_fare ?? 50;
                    $specialRate = $specialMatrix->per_km_rate ?? 5;
                    $specialMultiplier = $specialMatrix->multiplier ?? 1.5;
                    $specialMin = $specialMatrix->min_fare ?? 0;
                    $specialMax = $specialMatrix->max_fare ?? 999;

                    $distanceKm = 5;
                    $sharedRaw = $sharedBase + ($distanceKm * $sharedRate);
                    $sharedTotal = min(max($sharedRaw, $sharedMin), $sharedMax);

                    $specialRaw = $specialBase + ($distanceKm * $specialRate * $specialMultiplier);
                    $specialSuggested = min(max($specialRaw, $specialMin), $specialMax);
                @endphp

                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-dashed border-slate-300 dark:border-slate-700">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium text-slate-500">Shared Ride (5km)</span>
                        <span class="material-icons-outlined text-slate-400 text-lg">directions_bike</span>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-slate-900 dark:text-white">₱ {{ number_format($sharedTotal, 2) }}</span>
                    </div>
                    <div class="text-xs text-slate-400 mt-1">
                        Base: ₱{{ $sharedBase }} + ₱{{ $sharedRate }}/km
                    </div>
                </div>

                <div class="p-4 bg-amber-50 dark:bg-amber-900/10 rounded-lg border border-dashed border-amber-200 dark:border-amber-800">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium text-amber-700 dark:text-amber-400">Special Suggested (5km)</span>
                        <span class="material-icons-outlined text-amber-500 text-lg">stars</span>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-amber-700 dark:text-amber-400">₱ {{ number_format($specialSuggested, 2) }}</span>
                    </div>
                    <div class="text-xs text-amber-600/70 mt-1">
                        Uses multiplier {{ number_format($specialMultiplier, 1) }}x, within min/max bounds
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg flex items-start gap-3">
                    <span class="material-icons-outlined text-blue-600 dark:text-blue-400 mt-0.5">info</span>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Special rides require a passenger proposal within min/max bounds and must be locked before trip start.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
