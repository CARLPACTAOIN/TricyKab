@extends('layouts.stitch')

@section('title', 'Driver Management')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Driver Management</h1>
    <p class="text-slate-500 mt-1">Manage fleet drivers and their assignments.</p>
</div>

<!-- Action Bar -->
<div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('drivers.index') }}" class="flex flex-wrap items-center gap-3">
        <div class="relative">
            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Driver..." class="pl-10 pr-4 py-2 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary">
        </div>
        <div class="relative">
            <select name="status" class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary appearance-none min-w-[140px]">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
        </div>
        <button type="submit" class="bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Search</button>
    </form>
    <button onclick="document.getElementById('addDriverModal').classList.remove('hidden')" class="bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition-all shadow-md active:scale-[0.98]">
        <span class="material-icons-outlined">person_add</span>
        Register New Driver
    </button>
</div>

<!-- Main Table -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Driver Name</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">License #</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Contact Info</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Assigned Tricycle</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">TODA</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($drivers as $driver)
                <tr class="table-row-hover transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                             <div class="w-8 h-8 rounded-full bg-slate-100 object-cover flex items-center justify-center text-slate-500 font-bold text-xs border border-slate-200">
                                {{ substr($driver->first_name, 0, 1) }}{{ substr($driver->last_name, 0, 1) }}
                            </div>
                            <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $driver->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-mono">{{ $driver->license_number }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                        <div>{{ $driver->contact_number ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-400">{{ \Illuminate\Support\Str::limit($driver->address, 24) }}</div>
                    </td>
                     <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                        @if($driver->tricycle)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                <span class="material-icons-outlined text-[10px]">local_taxi</span>
                                {{ $driver->tricycle->plate_number }}
                            </span>
                        @else
                            <span class="text-slate-400 italic">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $driver->toda->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($driver->status == 'active')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Active</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-500">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('drivers.show', $driver->id) }}" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-primary transition-colors" title="View">
                                <span class="material-icons-outlined text-xl">visibility</span>
                            </a>
                            <button type="button" onclick="document.getElementById('editDriverModal-{{ $driver->id }}').classList.remove('hidden')" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-amber-600 transition-colors" title="Edit">
                                <span class="material-icons-outlined text-xl">edit</span>
                            </button>
                            <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                                    <span class="material-icons-outlined text-xl">delete_outline</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Edit Driver Modal -->
                <div id="editDriverModal-{{ $driver->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 bg-slate-900/75 transition-opacity" aria-hidden="true" onclick="document.getElementById('editDriverModal-{{ $driver->id }}').classList.add('hidden')"></div>

                        <!-- Modal panel -->
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-xl text-left overflow-hidden shadow-2xl shadow-black/30 transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border-0">
                            <!-- Modal Header -->
                            <div class="px-6 py-4 border-b border-primary/10 flex items-center justify-between bg-primary/[0.02]">
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white" id="modal-title">Edit Driver</h3>
                                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-400" onclick="document.getElementById('editDriverModal-{{ $driver->id }}').classList.add('hidden')">
                                    <span class="sr-only">Close</span>
                                    <span class="material-icons-outlined text-lg">close</span>
                                </button>
                            </div>
                            <!-- Modal Form Body -->
                            <form action="{{ route('drivers.update', $driver->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="p-6 space-y-5 text-start whitespace-normal">
                                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <label for="first_name_{{ $driver->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">First Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="first_name" id="first_name_{{ $driver->id }}" value="{{ $driver->first_name }}" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="First Name">
                                        </div>
                                        <div class="space-y-1.5">
                                            <label for="last_name_{{ $driver->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Last Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="last_name" id="last_name_{{ $driver->id }}" value="{{ $driver->last_name }}" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="Last Name">
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="license_number_{{ $driver->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">License Number <span class="text-red-500">*</span></label>
                                        <input type="text" name="license_number" id="license_number_{{ $driver->id }}" value="{{ $driver->license_number }}" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="License Number">
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <label for="contact_number_{{ $driver->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Contact Number</label>
                                            <input type="text" name="contact_number" id="contact_number_{{ $driver->id }}" value="{{ $driver->contact_number }}" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="Contact Number">
                                        </div>
                                        <div class="space-y-1.5">
                                            <label for="toda_id_{{ $driver->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">TODA Assignment</label>
                                            <select name="toda_id" id="toda_id_{{ $driver->id }}" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none appearance-none text-slate-700 dark:text-slate-300">
                                                <option value="">— Select TODA —</option>
                                                @foreach($todas as $toda)
                                                    <option value="{{ $toda->id }}" {{ $driver->toda_id == $toda->id ? 'selected' : '' }}>{{ $toda->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                     <div class="space-y-1.5">
                                        <label for="tricycle_id_{{ $driver->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Assigned Tricycle</label>
                                        <select name="tricycle_id" id="tricycle_id_{{ $driver->id }}" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none appearance-none text-slate-700 dark:text-slate-300">
                                            <option value="">— Select Tricycle —</option>
                                            @foreach($tricycles as $tricycle)
                                                <option value="{{ $tricycle->id }}" {{ $driver->tricycle_id == $tricycle->id ? 'selected' : '' }}>{{ $tricycle->body_number }} ({{ $tricycle->plate_number }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <label for="status_{{ $driver->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Status</label>
                                            <select name="status" id="status_{{ $driver->id }}" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none appearance-none text-slate-700 dark:text-slate-300">
                                                <option value="active" {{ $driver->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $driver->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="address_{{ $driver->id }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Address</label>
                                        <textarea name="address" id="address_{{ $driver->id }}" rows="2" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="Driver Address">{{ $driver->address }}</textarea>
                                    </div>
                                </div>
                                <!-- Modal Footer -->
                                <div class="px-6 py-4 bg-primary/[0.02] border-t border-primary/10 flex items-center gap-3 justify-end">
                                    <button type="button" class="px-6 py-2.5 rounded-lg border border-primary/20 text-slate-600 dark:text-slate-400 font-semibold text-sm hover:bg-primary/5 transition-colors" onclick="document.getElementById('editDriverModal-{{ $driver->id }}').classList.add('hidden')">
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
                    <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                         <div class="flex flex-col items-center justify-center">
                            <span class="material-icons-outlined text-4xl text-slate-300 mb-2">person_off</span>
                            <p>No Drivers found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
        {{ $drivers->links() }}
    </div>
</div>

<!-- Add Driver Modal -->
<div id="addDriverModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-slate-900/75 transition-opacity" aria-hidden="true" onclick="document.getElementById('addDriverModal').classList.add('hidden')"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-xl text-left overflow-hidden shadow-2xl shadow-black/30 transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border-0">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-primary/10 flex items-center justify-between bg-primary/[0.02]">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white" id="modal-title">Register New Driver</h3>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-400" onclick="document.getElementById('addDriverModal').classList.add('hidden')">
                    <span class="sr-only">Close</span>
                    <span class="material-icons-outlined text-lg">close</span>
                </button>
            </div>
            <!-- Modal Form Body -->
            <form action="{{ route('drivers.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5 text-start whitespace-normal">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="first_name" class="text-sm font-semibold text-slate-600 dark:text-slate-300">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" id="first_name" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="First Name">
                        </div>
                        <div class="space-y-1.5">
                            <label for="last_name" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" id="last_name" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="Last Name">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label for="license_number" class="text-sm font-semibold text-slate-600 dark:text-slate-300">License Number <span class="text-red-500">*</span></label>
                        <input type="text" name="license_number" id="license_number" required class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="License Number">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="contact_number" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Contact Number</label>
                            <input type="text" name="contact_number" id="contact_number" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="Contact Number">
                        </div>
                        <div class="space-y-1.5">
                            <label for="toda_id" class="text-sm font-semibold text-slate-600 dark:text-slate-300">TODA Assignment</label>
                            <select name="toda_id" id="toda_id" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none appearance-none text-slate-700 dark:text-slate-300">
                                <option value="">— Select TODA —</option>
                                @foreach($todas as $toda)
                                    <option value="{{ $toda->id }}">{{ $toda->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                     <div class="space-y-1.5">
                        <label for="tricycle_id" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Assigned Tricycle</label>
                        <select name="tricycle_id" id="tricycle_id" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none appearance-none text-slate-700 dark:text-slate-300">
                            <option value="">— Select Tricycle —</option>
                            @foreach($tricycles as $tricycle)
                                <option value="{{ $tricycle->id }}">{{ $tricycle->body_number }} ({{ $tricycle->plate_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label for="address" class="text-sm font-semibold text-slate-600 dark:text-slate-300">Address</label>
                        <textarea name="address" id="address" rows="2" class="w-full px-4 py-2.5 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg transition-all text-sm outline-none text-slate-700 dark:text-slate-300" placeholder="Driver Address"></textarea>
                    </div>
                    <input type="hidden" name="status" value="active">
                </div>
                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-primary/[0.02] border-t border-primary/10 flex items-center gap-3 justify-end">
                    <button type="button" class="px-6 py-2.5 rounded-lg border border-primary/20 text-slate-600 dark:text-slate-400 font-semibold text-sm hover:bg-primary/5 transition-colors" onclick="document.getElementById('addDriverModal').classList.add('hidden')">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-lg shadow-primary/20 transition-all">
                        Register Driver
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
