@extends('layouts.stitch')

@section('title', 'Create Standby Point')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Add Standby Point</h1>
            <p class="text-slate-500 mt-1">Create a new waiting area and geofence for drivers.</p>
        </div>
        <a href="{{ route('admin.standby-points') }}" class="px-4 py-2 rounded-lg text-sm font-medium border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            Back
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.standby-points.store') }}">
            @csrf
            @include('admin.standby-points._form', ['standbyPoint' => null])
        </form>
    </div>
</div>
@endsection
