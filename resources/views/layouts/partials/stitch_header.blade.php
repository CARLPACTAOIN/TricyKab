<header class="h-16 bg-white dark:bg-background-dark border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 sticky top-0 z-10">
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <span>Admin</span>
        <span class="material-icons-outlined text-base">chevron_right</span>
        <span class="text-slate-900 dark:text-white font-medium">@yield('title')</span>
    </div>
    <div class="flex items-center gap-6">
        <form method="GET" action="{{ route('admin.search') }}" class="relative hidden md:block">
            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
            <input name="q" value="{{ request('q') }}" class="pl-10 pr-4 py-1.5 w-64 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20" placeholder="Global search..." type="search" autocomplete="off"/>
        </form>
        <div class="flex items-center gap-4">
            <button class="text-slate-400 hover:text-primary transition-colors">
                <span class="material-icons-outlined">notifications</span>
            </button>
            <button class="text-slate-400 hover:text-primary transition-colors">
                <span class="material-icons-outlined">help_outline</span>
            </button>
            <button id="theme-toggle" type="button" class="text-slate-400 hover:text-primary transition-colors" title="Toggle theme">
                <span class="material-icons-outlined" id="theme-toggle-dark-icon">dark_mode</span>
                <span class="material-icons-outlined hidden" id="theme-toggle-light-icon">light_mode</span>
            </button>
            <script>
                (function() {
                    var root = document.documentElement;
                    var btn = document.getElementById('theme-toggle');
                    var darkIcon = document.getElementById('theme-toggle-dark-icon');
                    var lightIcon = document.getElementById('theme-toggle-light-icon');
                    if (!btn || !darkIcon || !lightIcon) return;

                    function applyTheme(isDark) {
                        if (isDark) {
                            root.classList.add('dark');
                            root.classList.remove('light');
                            darkIcon.classList.add('hidden');
                            lightIcon.classList.remove('hidden');
                        } else {
                            root.classList.remove('dark');
                            root.classList.add('light');
                            darkIcon.classList.remove('hidden');
                            lightIcon.classList.add('hidden');
                        }
                        localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    }

                    if (localStorage.getItem('theme') === 'dark') {
                        applyTheme(true);
                    } else {
                        applyTheme(false);
                    }

                    btn.addEventListener('click', function() {
                        applyTheme(!root.classList.contains('dark'));
                    });
                })();
            </script>
             <!-- Logout Form -->
             <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors" title="Logout">
                    <span class="material-icons-outlined">logout</span>
                </button>
            </form>
        </div>
    </div>
</header>
