@php
  $headerAdmin  = auth('admin')->user();
  $headerAvatar = $headerAdmin && $headerAdmin->avatar
      ? asset('storage/' . $headerAdmin->avatar)
      : 'https://ui-avatars.com/api/?name=' . urlencode($headerAdmin->name ?? 'A') . '&background=0F4C3A&color=fff&bold=true';
  $roleLabels = ['super' => 'مشرف عام', 'admin' => 'مشرف مركز', 'ticketsAdmin' => 'مسؤول تذاكر'];
  $roleLabel  = $roleLabels[$headerAdmin->role ?? ''] ?? ($headerAdmin->role_name ?? 'مشرف');
@endphp

<header class="sticky top-0 z-30 h-16 flex items-center bg-white/80 dark:bg-[#1a1a1a]/85 backdrop-blur-xl border-b border-slate-200/70 dark:border-[#d4af37]/15 px-4 md:px-6 lg:px-8 transition-colors duration-300">
  <div class="flex items-center justify-between gap-4 w-full max-w-[1600px] mx-auto">

    {{-- ابدأ (يمين RTL): زر الجوال + عنوان الصفحة --}}
    <div class="flex items-center gap-3 min-w-0">
      <button id="mobile-sidebar-toggle" onclick="toggleMobileSidebar()" aria-label="القائمة"
              class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-[#d4af37] hover:bg-slate-100 dark:hover:bg-[#d4af37]/10 transition-all active:scale-95">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>

      <div class="min-w-0">
        <h1 class="text-base md:text-lg font-black text-slate-800 dark:text-white tracking-tight leading-tight truncate">@yield('page-title', 'لوحة التحكم')</h1>
        <p class="hidden md:block text-[11px] text-slate-400 dark:text-slate-500 font-medium leading-tight">{{ now()->locale('ar')->translatedFormat('l، j F Y') }}</p>
      </div>
    </div>

    {{-- انتهِ (يسار RTL): صفّ الإجراءات --}}
    <div class="flex items-center gap-1 sm:gap-1.5">

      {{-- تبديل الوضع --}}
      <button onclick="toggleTheme()" type="button" aria-label="تبديل الوضع الليلي/النهاري"
              class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-[#d4af37] hover:bg-slate-100 dark:hover:bg-[#d4af37]/10 transition-all active:scale-95">
        {{-- شمس (الوضع النهاري) --}}
        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        {{-- قمر (الوضع الليلي) --}}
        <svg class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
      </button>

      {{-- الإشعارات --}}
      <div class="relative" id="notif-bell">
        <button id="notif-toggle" type="button" aria-label="الإشعارات"
                class="relative w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-[#d4af37] hover:bg-slate-100 dark:hover:bg-[#d4af37]/10 transition-all active:scale-95">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          <span id="notif-count" class="hidden absolute top-1 left-1 min-w-[17px] h-[17px] px-1 flex items-center justify-center text-[10px] font-black text-white bg-rose-500 rounded-full ring-2 ring-white dark:ring-[#1a1a1a]" style="font-variant-numeric: tabular-nums;">0</span>
        </button>

        <div id="notif-panel" class="hidden fixed top-16 inset-x-3 sm:absolute sm:top-full sm:inset-x-auto sm:left-0 sm:mt-2 sm:w-[22rem] bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-2xl shadow-slate-400/20 dark:shadow-black/60 border border-slate-200 dark:border-[#d4af37]/20 z-50 overflow-hidden animate-fade-in">
          <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-[#d4af37]/10">
            <span class="font-black text-slate-800 dark:text-[#d4af37] text-sm">الإشعارات</span>
            <button id="notif-mark-all" type="button" class="text-[11px] font-bold text-forest dark:text-[#d4af37] hover:underline">تعليم الكل كمقروء</button>
          </div>
          <div id="notif-list" class="max-h-[26rem] overflow-y-auto divide-y divide-slate-100 dark:divide-white/5">
            <div class="p-6 text-center text-xs text-slate-400">جارٍ التحميل...</div>
          </div>
          <a href="{{ route('admin.notifications.index') }}" class="block text-center px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 border-t border-slate-100 dark:border-[#d4af37]/10">عرض كل الإشعارات</a>
        </div>
      </div>

      {{-- فاصل --}}
      <div class="h-7 w-px bg-slate-200 dark:bg-white/10 mx-1 hidden sm:block"></div>

      {{-- قائمة المستخدم (نقرة) --}}
      <div class="relative" id="user-menu">
        <button id="user-menu-toggle" type="button" aria-label="حساب المستخدم"
                class="flex items-center gap-2.5 p-1 sm:pl-2.5 rounded-full hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
          <span class="relative flex-shrink-0">
            <img src="{{ $headerAvatar }}" alt="{{ $headerAdmin->name ?? 'مشرف' }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-forest/15 dark:ring-[#d4af37]/25">
            <span class="absolute -bottom-0.5 -left-0.5 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-[#1a1a1a] rounded-full"></span>
          </span>
          <span class="hidden sm:flex flex-col text-right leading-tight">
            <span class="text-xs font-extrabold text-slate-800 dark:text-white truncate max-w-[130px]">{{ $headerAdmin->name ?? 'مشرف النظام' }}</span>
            <span class="text-[10px] font-bold text-forest dark:text-[#d4af37]">{{ $roleLabel }}</span>
          </span>
          <svg id="user-menu-chevron" class="hidden sm:block w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div id="user-menu-panel" class="hidden absolute left-0 mt-2 w-60 bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-2xl shadow-slate-400/20 dark:shadow-black/60 border border-slate-200 dark:border-[#d4af37]/20 z-50 overflow-hidden animate-fade-in">
          <div class="flex items-center gap-3 p-4 border-b border-slate-100 dark:border-[#d4af37]/10">
            <img src="{{ $headerAvatar }}" alt="" class="w-10 h-10 rounded-full object-cover ring-2 ring-forest/15 dark:ring-[#d4af37]/25">
            <div class="min-w-0">
              <p class="font-extrabold text-slate-800 dark:text-white text-sm truncate">{{ $headerAdmin->name ?? 'مشرف النظام' }}</p>
              <p class="text-[11px] text-slate-400 truncate">{{ $headerAdmin->email ?? $headerAdmin->phone ?? '' }}</p>
            </div>
          </div>
          <div class="p-1.5">
            <a href="{{ Route::has('admin.profile') ? route('admin.profile') : '#' }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-[#d4af37]/10 hover:text-slate-900 dark:hover:text-[#d4af37] transition">
              <svg class="w-[18px] h-[18px] text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              الملف الشخصي
            </a>
            <a href="{{ Route::has('admin.profile') ? route('admin.profile') : '#' }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-[#d4af37]/10 hover:text-slate-900 dark:hover:text-[#d4af37] transition">
              <svg class="w-[18px] h-[18px] text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              الإعدادات
            </a>
          </div>
          @if(Route::has('logout'))
            <div class="p-1.5 border-t border-slate-100 dark:border-[#d4af37]/10">
              <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition">
                  <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25"/></svg>
                  تسجيل الخروج
                </button>
              </form>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</header>

<script>
  {{-- منطق الإشعارات --}}
  (function () {
    const toggle = document.getElementById('notif-toggle');
    const panel = document.getElementById('notif-panel');
    const list = document.getElementById('notif-list');
    const countEl = document.getElementById('notif-count');
    const markAll = document.getElementById('notif-mark-all');
    const wrap = document.getElementById('notif-bell');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!toggle) return;

    const icons = {
      calendar: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
      chat: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l1.3-3.9A7.9 7.9 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
      ticket: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 012 2v3a2 2 0 000 4v3a2 2 0 01-2 2H5a2 2 0 01-2-2v-3a2 2 0 000-4V7a2 2 0 012-2z"/>',
      bell: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
    };
    const esc = (s) => (s || '').replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));

    function render(data) {
      const c = data.unread_count || 0;
      if (c > 0) { countEl.textContent = c > 99 ? '99+' : c; countEl.classList.remove('hidden'); }
      else { countEl.classList.add('hidden'); }
      if (!data.notifications || !data.notifications.length) {
        list.innerHTML = '<div class="p-8 text-center text-xs text-slate-400">لا توجد إشعارات</div>';
        return;
      }
      list.innerHTML = data.notifications.map(n => `
        <a href="${n.action_url || '#'}" data-id="${n.id}" class="notif-item flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors ${n.read ? '' : 'bg-emerald-50/40 dark:bg-emerald-950/10'}">
          <span class="flex-shrink-0 w-9 h-9 rounded-xl bg-forest/10 dark:bg-[#d4af37]/10 text-forest dark:text-[#d4af37] flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[n.icon] || icons.bell}</svg>
          </span>
          <span class="flex-1 min-w-0">
            <span class="block text-xs font-black text-slate-800 dark:text-white truncate">${esc(n.title)}</span>
            <span class="block text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2">${esc(n.body)}</span>
            <span class="block text-[10px] text-slate-400 mt-0.5">${esc(n.time)}</span>
          </span>
          ${n.read ? '' : '<span class="flex-shrink-0 w-2 h-2 rounded-full bg-rose-500 mt-1"></span>'}
        </a>`).join('');
      list.querySelectorAll('.notif-item').forEach(el => {
        el.addEventListener('click', () => {
          const id = el.getAttribute('data-id');
          fetch(`/admin/notifications/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } }).catch(() => {});
        });
      });
    }

    async function load() {
      try {
        const r = await fetch('/admin/notifications/feed', { headers: { 'Accept': 'application/json' } });
        if (r.ok) render(await r.json());
      } catch (e) {}
    }

    toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      document.getElementById('user-menu-panel')?.classList.add('hidden');
      panel.classList.toggle('hidden');
      if (!panel.classList.contains('hidden')) load();
    });
    document.addEventListener('click', (e) => { if (wrap && !wrap.contains(e.target)) panel.classList.add('hidden'); });
    markAll?.addEventListener('click', async (e) => {
      e.stopPropagation();
      await fetch('/admin/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } }).catch(() => {});
      load();
    });

    load();
    setInterval(load, 45000);
  })();

  {{-- منطق قائمة المستخدم --}}
  (function () {
    const wrap = document.getElementById('user-menu');
    const btn = document.getElementById('user-menu-toggle');
    const menu = document.getElementById('user-menu-panel');
    const chevron = document.getElementById('user-menu-chevron');
    if (!btn) return;
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      document.getElementById('notif-panel')?.classList.add('hidden');
      const open = menu.classList.toggle('hidden');
      chevron?.classList.toggle('rotate-180', !open);
    });
    document.addEventListener('click', (e) => {
      if (wrap && !wrap.contains(e.target)) { menu.classList.add('hidden'); chevron?.classList.remove('rotate-180'); }
    });
  })();
</script>
