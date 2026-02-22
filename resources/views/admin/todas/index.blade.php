@extends('layouts.stitch')

@section('title', 'TODA Management')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">TODA Management</h1>
    <p class="text-slate-500 mt-1">Manage and monitor Tricycle Operators and Drivers Associations (TODA).</p>
</div>

<!-- Action Bar -->
<div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 mb-6 shadow-sm">
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative">
            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input type="text" placeholder="Search TODA..." class="pl-10 pr-4 py-2 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary">
        </div>
        <div class="relative">
            <select class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary appearance-none min-w-[140px]">
                <option>All Status</option>
                <option>Active</option>
                <option>Inactive</option>
            </select>
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
        </div>
    </div>
    <button onclick="document.getElementById('addTodaModal').classList.remove('hidden')" class="bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition-all shadow-md active:scale-[0.98]">
        <span class="material-icons-outlined">add</span>
        Add New TODA
    </button>
</div>

<!-- Main Table -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">TODA Name</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Area Coverage</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Operating Hours</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($todas as $toda)
                <tr class="table-row-hover transition-colors group">
                    <td class="px-6 py-4 text-sm font-medium text-slate-500">#{{ $toda->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">{{ substr($toda->name, 0, 1) }}</div>
                            <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $toda->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                        <div class="flex items-center gap-1.5">
                            <span class="material-icons-outlined text-sm text-primary">place</span>
                            {{ $toda->area_coverage ?? '—' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $toda->operating_hours ?? '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($toda->status == 'active')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Active</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-500">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('todas.edit', $toda->id) }}" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-amber-600 transition-colors" title="Edit">
                                <span class="material-icons-outlined text-xl">edit</span>
                            </a>
                            <form action="{{ route('todas.destroy', $toda->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                                    <span class="material-icons-outlined text-xl">delete_outline</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <span class="material-icons-outlined text-4xl text-slate-300 mb-2">search_off</span>
                            <p>No TODAs found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
        {{ $todas->links() }}
    </div>
</div>

<!-- Add TODA Modal -->
<div id="addTodaModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-slate-900/75 transition-opacity" aria-hidden="true" onclick="document.getElementById('addTodaModal').classList.add('hidden')"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-200 dark:border-slate-700">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                        <span class="material-icons-outlined text-primary">add_business</span>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white" id="modal-title">Add New TODA</h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500">Fill in the details to register a new transport association.</p>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ route('todas.store') }}" method="POST">
                @csrf
                <div class="px-4 py-5 sm:p-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">TODA Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="e.g. Poblacion TODA">
                    </div>
                    <div>
                        <label for="area_coverage" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Area Coverage</label>
                        <input type="text" name="area_coverage" id="area_coverage" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="e.g. Brgy. Poblacion">
                    </div>
                    <div>
                        <label for="operating_hours" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Operating Hours</label>
                        <input type="text" name="operating_hours" id="operating_hours" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="e.g. 5:00 AM - 10:00 PM">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Save TODA
                    </button>
                    <button type="button" onclick="document.getElementById('addTodaModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
