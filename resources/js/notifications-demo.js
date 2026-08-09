const app = document.getElementById('demo-app');

if (app) {
    const role = app.dataset.role;
    const apiPrefix = app.dataset.apiPrefix;
    const storageKey = `demo_token_${role}`;

    const tokenInput = document.getElementById('token-input');
    const statusMsg = document.getElementById('status-msg');
    const unreadBadge = document.getElementById('unread-badge');
    const dropdown = document.getElementById('dropdown');
    const notificationsList = document.getElementById('notifications-list');
    const fullList = document.getElementById('full-list');
    const emptyState = document.getElementById('empty-state');

    const saved = localStorage.getItem(storageKey);
    if (saved) tokenInput.value = saved;

    function token() {
        return localStorage.getItem(storageKey) || tokenInput.value.trim();
    }

    function setStatus(msg, ok = true) {
        if (!statusMsg) return;
        statusMsg.textContent = msg;
        statusMsg.className = ok ? 'text-sm text-emerald-400 min-h-5' : 'text-sm text-red-400 min-h-5';
    }

    async function api(path, options = {}) {
        const headers = {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            ...(token() ? { Authorization: `Bearer ${token()}` } : {}),
            ...options.headers,
        };

        const res = await fetch(`/api${path.startsWith('/admin') ? path.replace('/api', '') : path.replace(/^\/api/, '')}`.replace('//', '/'), {
            ...options,
            headers,
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            throw new Error(data.message || JSON.stringify(data) || res.statusText);
        }

        return data;
    }

    function apiPath(suffix) {
        if (role === 'admin') {
            return `/admin${suffix}`;
        }
        return suffix;
    }

    async function fetchApi(suffix, options = {}) {
        const t = token();
        if (!t) throw new Error('أدخل Bearer Token أولاً');

        const apiBase = (app.dataset.apiBase || '/api').replace(/\/$/, '');
        const url = role === 'admin' ? `${apiBase}/admin${suffix}` : `${apiBase}${suffix}`;

        const res = await fetch(url, {
            ...options,
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                Authorization: `Bearer ${t}`,
                ...(options.headers || {}),
            },
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || JSON.stringify(data.errors || data) || res.statusText);
        return data;
    }

    function renderItem(n, compact = false) {
        const read = n.read_at ? 'opacity-60' : 'border-r-2 border-emerald-500';
        const d = n.data || {};
        return `
            <article class="p-3 hover:bg-slate-800/50 ${read} ${compact ? '' : 'rounded-xl border border-slate-800 bg-slate-950'}" data-id="${n.id}">
                <div class="flex justify-between gap-2">
                    <h3 class="font-medium text-sm text-white">${d.title || n.type}</h3>
                    ${!n.read_at ? '<span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0 mt-1"></span>' : ''}
                </div>
                <p class="text-xs text-slate-400 mt-1">${d.body || ''}</p>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-[10px] text-slate-600">${new Date(n.created_at).toLocaleString('ar-SY')}</span>
                    ${!n.read_at ? `<button type="button" data-read="${n.id}" class="text-[10px] text-emerald-400 hover:underline">تعليم كمقروء</button>` : ''}
                </div>
            </article>`;
    }

    async function loadUnreadCount() {
        try {
            const { count } = await fetchApi('/notifications/unread-count');
            if (count > 0) {
                unreadBadge.textContent = count > 99 ? '99+' : count;
                unreadBadge.classList.remove('hidden');
            } else {
                unreadBadge.classList.add('hidden');
            }
        } catch {
            unreadBadge.classList.add('hidden');
        }
    }

    async function loadNotifications() {
        try {
            const json = await fetchApi('/notifications');
            const items = json.data || [];

            notificationsList.innerHTML = items.slice(0, 5).map(n => renderItem(n, true)).join('') || '';
            fullList.innerHTML = items.map(n => renderItem(n)).join('') || '<p class="text-slate-500 text-sm">لا توجد إشعارات</p>';

            emptyState.classList.toggle('hidden', items.length > 0);

            document.querySelectorAll('[data-read]').forEach(btn => {
                btn.addEventListener('click', async () => {
                    await fetchApi(`/notifications/${btn.dataset.read}/read`, { method: 'PATCH' });
                    await refresh();
                });
            });

            await loadUnreadCount();
            setStatus('تم التحديث');
        } catch (e) {
            setStatus(e.message, false);
        }
    }

    async function refresh() {
        await loadNotifications();
    }

    document.getElementById('save-token-btn')?.addEventListener('click', () => {
        localStorage.setItem(storageKey, tokenInput.value.trim());
        setStatus('تم حفظ Token');
        refresh();
    });

    document.getElementById('refresh-btn')?.addEventListener('click', refresh);

    document.getElementById('bell-btn')?.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
        refresh();
    });

    document.getElementById('read-all-btn')?.addEventListener('click', async () => {
        try {
            await fetchApi('/notifications/read-all', { method: 'POST' });
            await refresh();
        } catch (e) {
            setStatus(e.message, false);
        }
    });

    document.getElementById('test-notification-btn')?.addEventListener('click', async () => {
        try {
            await fetchApi('/notifications/test', { method: 'POST' });
            setStatus('تم إرسال إشعار تجريبي');
            await refresh();
        } catch (e) {
            setStatus(e.message, false);
        }
    });

    document.getElementById('register-fcm-btn')?.addEventListener('click', async () => {
        try {
            const demoToken = `demo-fcm-${role}-${Date.now()}`;
            await fetchApi('/device-tokens', {
                method: 'POST',
                body: JSON.stringify({ token: demoToken, platform: 'web' }),
            });
            setStatus(`تم تسجيل FCM: ${demoToken}`);
        } catch (e) {
            setStatus(e.message, false);
        }
    });

    document.addEventListener('click', e => {
        if (!dropdown.contains(e.target) && !document.getElementById('bell-btn')?.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    if (token()) refresh();
}
