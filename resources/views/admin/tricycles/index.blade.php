@extends('layouts.stitch')

@section('title', 'Tricycle Fleet Management')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tricycle Fleet</h1>
    <p class="text-slate-500 mt-1">Manage registered tricycles and fleet assignments.</p>
</div>

<!-- Action Bar -->
<div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('tricycles.index') }}" class="flex flex-wrap items-center gap-3">
        <div class="relative">
            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Tricycle..." class="pl-10 pr-4 py-2 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary">
        </div>
        <div class="relative">
            <select name="status" class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary appearance-none min-w-[140px]">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
        </div>
        <button type="submit" class="bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Search</button>
    </form>
    <button onclick="document.getElementById('addTricycleModal').classList.remove('hidden')" class="bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition-all shadow-md active:scale-[0.98]">
        <span class="material-icons-outlined">electric_rickshaw</span>
        Register New Tricycle
    </button>
</div>

<!-- Main Table -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Body #</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Plate #</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">TODA</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Driver</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($tricycles as $tricycle)
                <tr class="table-row-hover transition-colors group">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary">
                            #{{ $tricycle->body_number }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white font-mono">{{ $tricycle->plate_number }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $tricycle->toda->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                         @if($tricycle->driver)
                            <div class="flex items-center gap-2">
                                <span class="material-icons-outlined text-slate-400 text-sm">person</span>
                                {{ $tricycle->driver->full_name }}
                            </div>
                        @else
                            <span class="text-slate-400 italic">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($tricycle->status == 'active')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Serviceable</span>
                        @elseif($tricycle->status == 'maintenance')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Maintenance</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-500">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" onclick="document.getElementById('editTricycleModal-{{ $tricycle->id }}').classList.remove('hidden')" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-amber-600 transition-colors" title="Edit">
                                <span class="material-icons-outlined text-xl">edit</span>
                            </button>
                            <form action="{{ route('tricycles.destroy', $tricycle->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                                    <span class="material-icons-outlined text-xl">delete_outline</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Edit Tricycle Modal -->
                <div id="editTricycleModal-{{ $tricycle->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 bg-slate-900/75 transition-opacity" aria-hidden="true" onclick="document.getElementById('editTricycleModal-{{ $tricycle->id }}').classList.add('hidden')"></div>

                        <!-- Modal panel -->
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-xl text-left overflow-hidden shadow-2xl shadow-black/30 transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border-0">
                            <!-- Modal Header -->
                            <div class="px-6 py-4 border-b border-primary/10 flex items-center justify-between bg-primary/[0.02]">
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white" id="modal-title">Edit Tricycle</h3>
                                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-400" onclick="document.getElementById('editTricycleModal-{{ $tricycle->id }}').classList.add('hidden')">
                                    <span class="sr-only">Close</span>
                                    <span class="material-icons-outlined text-lg">close</span>
                                </button>
                            </div>
                            <!-- Modal Form Body -->
                            <form action="{{ route('tricycles.update', $tricycle->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="p-6 space-y-5 text-start whitespace-normal">
                                    <div class="space-y-1.5">
                                        <label for="body_number_{{ $tricycle->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Body Number <span class="text-red-500">*</span></label>
                                        <input type="text" name="body_number" id="body_number_{{ $tricycle->id }}" value="{{ $tricycle->body_number }}" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="e.g. 101">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="plate_number_{{ $tricycle->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Plate Number <span class="text-red-500">*</span></label>
                                        <input type="text" name="plate_number" id="plate_number_{{ $tricycle->id }}" value="{{ $tricycle->plate_number }}" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="e.g. ABC 123">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="toda_id_{{ $tricycle->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">TODA Association <span class="text-red-500">*</span></label>
                                        <select name="toda_id" id="toda_id_{{ $tricycle->id }}" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none appearance-none text-slate-700 dark:text-slate-300">
                                            <option value="">— Select TODA —</option>
                                            @foreach($todas as $toda)
                                                <option value="{{ $toda->id }}" {{ $tricycle->toda_id == $toda->id ? 'selected' : '' }}>{{ $toda->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="make_model_{{ $tricycle->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Make & Model</label>
                                        <input type="text" name="make_model" id="make_model_{{ $tricycle->id }}" value="{{ $tricycle->make_model }}" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="e.g. Honda TMX 155">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="status_{{ $tricycle->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Status</label>
                                        <select id="status_{{ $tricycle->id }}" name="status" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none appearance-none text-slate-700 dark:text-slate-300">
                                            <option value="active" {{ $tricycle->status == 'active' ? 'selected' : '' }}>Active (Serviceable)</option>
                                            <option value="maintenance" {{ $tricycle->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                            <option value="inactive" {{ $tricycle->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Modal Footer -->
                                <div class="px-6 py-4 bg-primary/[0.02] border-t border-primary/10 flex items-center gap-3 justify-end">
                                    <button type="button" class="px-6 py-2.5 rounded-lg border border-primary/20 text-slate-600 dark:text-slate-400 font-semibold text-sm hover:bg-primary/5 transition-colors" onclick="document.getElementById('editTricycleModal-{{ $tricycle->id }}').classList.add('hidden')">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-lg shadow-primary/20 transition-all">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                         <div class="flex flex-col items-center justify-center">
                            <span class="material-icons-outlined text-4xl text-slate-300 mb-2">no_crash</span>
                            <p>No Tricycles found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
        {{ $tricycles->links() }}
    </div>
</div>

<!-- Add Tricycle Modal -->
<div id="addTricycleModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-slate-900/75 transition-opacity" aria-hidden="true" onclick="document.getElementById('addTricycleModal').classList.add('hidden')"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-xl text-left overflow-hidden shadow-2xl shadow-black/30 transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border-0">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-primary/10 flex items-center justify-between bg-primary/[0.02]">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white" id="modal-title">Register New Tricycle</h3>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-400" onclick="document.getElementById('addTricycleModal').classList.add('hidden')">
                    <span class="sr-only">Close</span>
                    <span class="material-icons-outlined text-lg">close</span>
                </button>
            </div>
            <!-- Modal Form Body -->
            <form action="{{ route('tricycles.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5 text-start whitespace-normal">
                    <div class="space-y-1.5">
                        <label for="body_number" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Body Number <span class="text-red-500">*</span></label>
                        <input type="text" name="body_number" id="body_number" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="e.g. 101">
                    </div>
                    <div class="space-y-1.5">
                        <label for="plate_number" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Plate Number <span class="text-red-500">*</span></label>
                        <input type="text" name="plate_number" id="plate_number" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="e.g. ABC 123">
                    </div>
                    <div class="space-y-1.5">
                        <label for="toda_id" class="text-sm font-semibold text-slate-600 dark:text-slate-300">TODA Association <span class="text-red-500">*</span></label>
                        <select name="toda_id" id="toda_id" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none appearance-none text-slate-700 dark:text-slate-300">
                            <option value="">— Select TODA —</option>
                            @foreach($todas as $toda)
                                <option value="{{ $toda->id }}">{{ $toda->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label for="make_model" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Make & Model</label>
                        <input type="text" name="make_model" id="make_model" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="e.g. Honda TMX 155">
                    </div>
                    <div class="space-y-1.5">
                        <label for="status" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Status</label>
                        <select id="status" name="status" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none appearance-none text-slate-700 dark:text-slate-300">
                            <option value="active">Active (Serviceable)</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-primary/[0.02] border-t border-primary/10 flex items-center gap-3 justify-end">
                    <button type="button" class="px-6 py-2.5 rounded-lg border border-primary/20 text-slate-600 dark:text-slate-400 font-semibold text-sm hover:bg-primary/5 transition-colors" onclick="document.getElementById('addTricycleModal').classList.add('hidden')">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-lg shadow-primary/20 transition-all">
                        Add Tricycle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
