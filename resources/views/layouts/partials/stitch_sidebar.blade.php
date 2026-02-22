<aside class="w-64 bg-white dark:bg-background-dark border-r border-slate-200 dark:border-slate-800 flex flex-col fixed h-full z-20 transition-transform duration-300 lg:translate-x-0 transform -translate-x-full lg:fixed lg:left-0 lg:top-0 lg:bottom-0">
    <div class="p-6 flex items-center gap-3">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
            <span class="material-icons-outlined text-white">electric_rickshaw</span>
        </div>
        <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">TricyKab</span>
    </div>
    <nav class="flex-1 px-3 py-4 space-y-1">
        <a class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary' }}" href="{{ route('admin.dashboard') }}">
            <span class="material-icons-outlined mr-3 {{ request()->routeIs('admin.dashboard') ? '' : 'text-slate-400 group-hover:text-primary' }}">dashboard</span>
            <span class="font-medium">Dashboard</span>
        </a>
        <a class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('todas.*') ? 'sidebar-item-active' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary' }}" href="{{ route('todas.index') }}">
            <span class="material-icons-outlined mr-3 {{ request()->routeIs('todas.*') ? '' : 'text-slate-400 group-hover:text-primary' }}">groups</span>
            <span class="font-medium">TODA Management</span>
        </a>
        <a class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('drivers.*') ? 'sidebar-item-active' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary' }}" href="{{ route('drivers.index') }}">
            <span class="material-icons-outlined mr-3 {{ request()->routeIs('drivers.*') ? '' : 'text-slate-400 group-hover:text-primary' }}">person_outline</span>
            <span class="font-medium">Drivers</span>
        </a>
        <a class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('tricycles.*') ? 'sidebar-item-active' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary' }}" href="{{ route('tricycles.index') }}">
            <span class="material-icons-outlined mr-3 {{ request()->routeIs('tricycles.*') ? '' : 'text-slate-400 group-hover:text-primary' }}">electric_rickshaw</span>
            <span class="font-medium">Tricycle Fleet</span>
        </a>
        <a class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('fares.*') ? 'sidebar-item-active' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary' }}" href="{{ route('fares.index') }}">
            <span class="material-icons-outlined mr-3 {{ request()->routeIs('fares.*') ? '' : 'text-slate-400 group-hover:text-primary' }}">payments</span>
            <span class="font-medium">Fare Rates</span>
        </a>
    </nav>
    <div class="p-4 border-t border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3 px-2 py-2">
            <img class="w-10 h-10 rounded-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBbIk3iuRxp4r-wgqZbyWYOMM9sOSkKl2jt-Fq9wGM352O2H_e_F1z5EhUZerxZLjTcG8HC2jbxNPZlAAkEOrTVMUn9jc_g1y183yDb-cLSjaSLkD9fBHPMvh9L1rny2AqouohpPml_Ah_ya4q8F9CVvfQo6GATF2Z6kuHUuVYw8BF9BsfKc7FjnXKzkYv149ya67FQDvNo8p-edFyyNUP4UaqMmtr84a6WHp1GT43jGz_sEM7qB74RUuuHH05MR8N-LXfHcV9jzDk" alt="Admin Profile">
            <div class="overflow-hidden">
                <p class="text-sm font-semibold truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-slate-500 truncate">Super Admin</p>
            </div>
        </div>
    </div>
</aside>
