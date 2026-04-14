@php
$colorClasses = [
    'CREATED' => 'bg-[#23b7e5] bg-opacity-20 text-[#23b7e5]',
    'SEARCHING_DRIVER' => 'bg-[#23b7e5] bg-opacity-20 text-[#23b7e5] animate-pulse',
    'DRIVER_ASSIGNED' => 'bg-[#6258ca] bg-opacity-20 text-[#6258ca]',
    'DRIVER_ON_THE_WAY' => 'bg-[#6258ca] bg-opacity-20 text-[#6258ca]',
    'DRIVER_ARRIVED' => 'bg-[#09ad95] bg-opacity-20 text-[#09ad95]',
    'TRIP_IN_PROGRESS' => 'bg-[#6258ca] bg-opacity-20 text-[#6258ca]',
    'COMPLETED' => 'bg-[#09ad95] bg-opacity-20 text-[#09ad95]',
    'CANCELLED_BY_PASSENGER' => 'bg-red-100 text-red-800',
    'CANCELLED_BY_DRIVER' => 'bg-red-100 text-red-800',
    'NO_SHOW_PASSENGER' => 'bg-red-100 text-red-800',
    'NO_SHOW_DRIVER' => 'bg-red-100 text-red-800',
    'CANCELLED_NO_DRIVER' => 'bg-gray-200 text-gray-700',
];
$baseClass = $colorClasses[$status] ?? 'bg-gray-100 text-gray-700';
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $baseClass }} shadow-sm border border-black/5">
    {{ str_replace('_', ' ', $status) }}
</span>

