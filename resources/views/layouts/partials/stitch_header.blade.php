<header class="h-16 bg-white dark:bg-background-dark border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 sticky top-0 z-10">
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <span>Admin</span>
        <span class="material-icons-outlined text-base">chevron_right</span>
        <span class="text-slate-900 dark:text-white font-medium">@yield('title')</span>
    </div>
    <div class="flex items-center gap-6">
        <form method="GET" action="{{ route('admin.search') }}" id="global-search-form" class="relative hidden md:block" autocomplete="off">
            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl pointer-events-none">search</span>
            <input
                id="global-search-input"
                name="q"
                value="{{ request('q') }}"
                class="pl-10 pr-4 py-1.5 w-64 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20"
                placeholder="Global search..."
                type="search"
                autocomplete="off"
            />
            <div
                id="global-search-suggest"
                class="hidden absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden z-50 max-h-80 overflow-y-auto"
            ></div>
        </form>
        <div class="flex items-center gap-4">
            <div class="relative" id="admin-notifications-root">
                <button
                    type="button"
                    id="admin-notifications-toggle"
                    class="relative text-slate-400 hover:text-primary transition-colors"
                    aria-label="Notifications"
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <span class="material-icons-outlined">notifications</span>
                    <span
                        id="admin-notifications-badge"
                        class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white text-[10px] font-bold flex items-center justify-center"
                    >0</span>
                </button>
                <div
                    id="admin-notifications-panel"
                    class="hidden absolute right-0 top-full mt-2 w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl z-50 overflow-hidden"
                >
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Notifications</p>
                        <a href="{{ auth()->user()->isLguAdmin() ? route('admin.sos') : route('admin.bookings') }}" class="text-xs text-primary hover:underline">View all</a>
                    </div>
                    <div id="admin-notifications-list" class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                        <p class="px-4 py-6 text-sm text-slate-500 text-center">Loading…</p>
                    </div>
                </div>
            </div>
            <button type="button" class="text-slate-400 hover:text-primary transition-colors" title="Help (coming soon)">
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
             <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="button" id="logout-open-btn" class="text-slate-400 hover:text-red-500 transition-colors" title="Logout" aria-haspopup="dialog">
                    <span class="material-icons-outlined">logout</span>
                </button>
            </form>
        </div>
    </div>
</header>

<script>
(function () {
    const csrf = @json(csrf_token());
    const notificationsUrl = @json(route('admin.notifications'));
    const dismissUrl = @json(route('admin.notifications.dismiss'));
    const suggestUrl = @json(route('admin.search.suggest'));

    // --- Global search suggestions ---
    const searchInput = document.getElementById('global-search-input');
    const suggestBox = document.getElementById('global-search-suggest');
    let suggestTimer = null;

    function hideSuggest() {
        if (suggestBox) suggestBox.classList.add('hidden');
    }

    function renderSuggest(groups) {
        if (!suggestBox) return;
        if (!groups.length) {
            suggestBox.innerHTML = '<p class="px-4 py-3 text-sm text-slate-500">No matches found.</p>';
            suggestBox.classList.remove('hidden');
            return;
        }
        suggestBox.innerHTML = groups.map(function (group) {
            const items = group.items.map(function (item) {
                return '<a href="' + item.url + '" class="block px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">' +
                    '<p class="text-sm font-medium text-slate-900 dark:text-white">' + escapeHtml(item.label) + '</p>' +
                    (item.meta ? '<p class="text-xs text-slate-500">' + escapeHtml(item.meta) + '</p>' : '') +
                '</a>';
            }).join('');
            return '<div class="py-2">' +
                '<p class="px-4 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">' + escapeHtml(group.label) + '</p>' +
                items +
            '</div>';
        }).join('');
        suggestBox.classList.remove('hidden');
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    if (searchInput && suggestBox) {
        searchInput.addEventListener('input', function () {
            clearTimeout(suggestTimer);
            const q = searchInput.value.trim();
            if (q.length < 2) {
                hideSuggest();
                return;
            }
            suggestTimer = setTimeout(function () {
                fetch(suggestUrl + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) { renderSuggest(data.groups || []); })
                    .catch(function () { hideSuggest(); });
            }, 250);
        });

        searchInput.addEventListener('focus', function () {
            if (searchInput.value.trim().length >= 2 && suggestBox.innerHTML) {
                suggestBox.classList.remove('hidden');
            }
        });

        document.addEventListener('click', function (e) {
            if (!document.getElementById('global-search-form')?.contains(e.target)) {
                hideSuggest();
            }
        });
    }

    // --- Admin notifications ---
    const toggle = document.getElementById('admin-notifications-toggle');
    const panel = document.getElementById('admin-notifications-panel');
    const list = document.getElementById('admin-notifications-list');
    const badge = document.getElementById('admin-notifications-badge');

    function severityClasses(severity) {
        if (severity === 'critical') return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300';
        if (severity === 'warning') return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300';
        return 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300';
    }

    function renderNotifications(items) {
        if (!list || !badge) return;
        const count = items.length;
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : String(count);
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }

        if (!count) {
            list.innerHTML = '<p class="px-4 py-8 text-sm text-slate-500 text-center">No new notifications.</p>';
            return;
        }

        list.innerHTML = items.map(function (item) {
            return '<div class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50">' +
                '<div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 ' + severityClasses(item.severity) + '">' +
                    '<span class="material-icons-outlined text-lg">' + escapeHtml(item.icon || 'notifications') + '</span>' +
                '</div>' +
                '<div class="flex-1 min-w-0">' +
                    '<a href="' + item.url + '" class="block">' +
                        '<p class="text-sm font-semibold text-slate-900 dark:text-white truncate">' + escapeHtml(item.title) + '</p>' +
                        '<p class="text-xs text-slate-500 truncate">' + escapeHtml(item.body) + '</p>' +
                        '<p class="text-[10px] text-slate-400 mt-1">' + escapeHtml(item.created_at_human || '') + '</p>' +
                    '</a>' +
                '</div>' +
                '<button type="button" data-dismiss-key="' + escapeHtml(item.key) + '" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 shrink-0" title="Dismiss">' +
                    '<span class="material-icons-outlined text-base">close</span>' +
                '</button>' +
            '</div>';
        }).join('');

        list.querySelectorAll('[data-dismiss-key]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const key = btn.getAttribute('data-dismiss-key');
                fetch(dismissUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ key: key }),
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) { renderNotifications(data.items || []); })
                    .catch(function () {});
            });
        });
    }

    function fetchNotifications() {
        fetch(notificationsUrl, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (r) { return r.json(); })
            .then(function (data) { renderNotifications(data.items || []); })
            .catch(function () {
                if (list) list.innerHTML = '<p class="px-4 py-6 text-sm text-rose-500 text-center">Could not load notifications.</p>';
            });
    }

    if (toggle && panel) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            const open = !panel.classList.contains('hidden');
            panel.classList.toggle('hidden', open);
            toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
            if (!open) fetchNotifications();
        });

        document.addEventListener('click', function (e) {
            if (!document.getElementById('admin-notifications-root')?.contains(e.target)) {
                panel.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    fetchNotifications();
    setInterval(fetchNotifications, 30000);
})();
</script>
