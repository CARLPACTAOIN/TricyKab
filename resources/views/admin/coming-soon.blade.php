@extends('layouts.stitch')

@section('title', $title)

@section('content')
<div class="max-w-3xl">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/30">
                <span class="material-icons-outlined text-blue-600">build_circle</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $title }}</h1>
                <p class="mt-2 text-slate-600 dark:text-slate-400">
                    {{ $description ?? 'This module is queued for PRD-aligned implementation.' }}
                </p>
                <div class="mt-6 text-sm text-slate-500 dark:text-slate-400">
                    Focus for this phase: align dashboard and authentication flows while keeping future modules scoped.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
