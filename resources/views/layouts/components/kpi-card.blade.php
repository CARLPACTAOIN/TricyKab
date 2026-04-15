<div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex flex-col justify-between h-full">
    <div class="flex justify-between items-start">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ $title }}</h3>
        @if(isset($icon))
            <div class="p-2 bg-gray-50 rounded-lg text-gray-400">
                {!! $icon !!}
            </div>
        @endif
    </div>

    <div class="mt-4 flex items-baseline gap-2">
        <div class="text-3xl font-bold text-gray-900">{{ $value }}</div>
        @if(isset($trend))
            <div class="text-sm font-medium {{ $trendDirection === 'up' ? 'text-green-600' : 'text-red-600' }}">
                {{ $trendDirection === 'up' ? '↑' : '↓' }} {{ $trend }}
            </div>
        @endif
    </div>

    @if(isset($subtitle))
        <div class="mt-2 text-sm text-gray-500">{{ $subtitle }}</div>
    @endif
</div>

