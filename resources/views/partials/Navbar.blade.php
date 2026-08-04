<header class="sticky top-0 z-20 bg-white/80 dark:bg-[#1a1a1a]/90 backdrop-blur-xl border-b border-slate-200/60 dark:border-[#d4af37]/20 px-4 md:px-8 py-3 transition-all duration-300">
  <div class="flex items-center justify-between gap-4 max-w-[1600px] mx-auto">
    
    <div class="flex items-center gap-3.5">
      <button id="mobile-sidebar-toggle" onclick="toggleMobileSidebar()" 
              class="lg:hidden p-2.5 text-slate-500 dark:text-gray-300 hover:text-slate-900 hover:bg-slate-100 dark:hover:bg-[#d4af37]/10 rounded-xl transition-all active:scale-95">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center justify-center w-9 h-9 rounded-xl bg-forest/10 dark:bg-[#d4af37]/10 text-forest dark:text-[#d4af37] border dark:border-[#d4af37]/20">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
        </div>
        <div>
          <h1 class="text-base md:text-lg font-black text-slate-800 dark:text-[#d4af37] tracking-tight leading-tight">
            @yield('page-title', 'لوحة التحكم')
          </h1>
          <p class="text-[11px] text-slate-400 dark:text-gray-400 font-medium hidden md:block">{{ now()->format('l، d M Y') }}</p>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-2 md:gap-4">
      
      <button id="theme-toggle-btn" onclick="toggleTheme()" type="button" title="تبديل الوضع"
              class="relative inline-flex items-center justify-between w-14 h-7 p-1 rounded-full bg-slate-200 dark:bg-[#141414] border border-slate-300 dark:border-[#d4af37]/40 transition-colors duration-300 shadow-inner group cursor-pointer">
        <svg class="w-4 h-4 text-amber-400 z-10 transition-transform duration-300 dark:scale-100 scale-75 opacity-60 dark:opacity-100" fill="currentColor" viewBox="0 0 20 20">
          <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
        </svg>
        <svg class="w-4 h-4 text-amber-500 z-10 transition-transform duration-300 dark:scale-75 scale-100 opacity-100 dark:opacity-60" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" clip-rule="evenodd"/>
        </svg>
        <span class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full bg-white dark:bg-gradient-to-r dark:from-[#d4af37] dark:to-[#c5a028] border border-slate-200 dark:border-[#d4af37] shadow-md transform transition-transform duration-300 dark:translate-x-7 translate-x-0"></span>
      </button>

      <button class="relative p-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-gray-200 hover:bg-slate-100 dark:hover:bg-[#d4af37]/10 rounded-xl transition-all active:scale-95">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white dark:ring-[#1a1a1a]"></span>
      </button>

      <div class="h-8 w-px bg-slate-200 dark:bg-[#d4af37]/20 hidden md:block"></div>

      <div class="flex items-center gap-3 pl-1">
        <div class="relative group cursor-pointer">
          @php
            $headerAdmin = auth('admin')->user();
            $headerAvatar = $headerAdmin && $headerAdmin->avatar ? asset('storage/' . $headerAdmin->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($headerAdmin->name ?? 'A') . '&background=0F4C3A&color=fff';
          @endphp
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 border-2 border-emerald-500/20 overflow-hidden shadow-sm transition-all group-hover:shadow-md group-hover:border-emerald-500/40">
            <img src="{{ $headerAvatar }}" class="w-full h-full object-cover" alt="Avatar">
          </div>
          <span class="absolute bottom-0 left-0 w-3 h-3 bg-emerald-500 border-[2.5px] border-white dark:border-[#1a1a1a] rounded-full"></span>
          
          <div class="absolute left-0 top-full mt-2 w-56 bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-[#d4af37]/20 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-left z-30">
            <div class="p-4 border-b border-slate-50 dark:border-[#d4af37]/10">
              <p class="font-extrabold text-slate-800 dark:text-[#d4af37] text-sm">{{ $headerAdmin->name ?? 'مشرف النظام' }}</p>
              <p class="text-xs text-slate-400 mt-0.5">{{ $headerAdmin->email ?? 'admin@example.com' }}</p>
            </div>
            <div class="p-2">
              <a href="{{ Route::has('admin.profile') ? route('admin.profile') : '#' }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-[#d4af37]/10 hover:text-slate-900 dark:hover:text-[#d4af37] transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                الملف الشخصي
              </a>
              <a href="{{ Route::has('admin.profile') ? route('admin.profile') : '#' }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-[#d4af37]/10 hover:text-slate-900 dark:hover:text-[#d4af37] transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                الإعدادات
              </a>
            </div>
          </div>
        </div>

        <div class="hidden sm:flex flex-col text-right">
          <span class="text-xs font-extrabold text-slate-800 dark:text-gray-100 leading-tight">
            {{ $headerAdmin->name ?? 'مشرف النظام' }}
          </span>
          <span class="text-[10px] font-bold text-emerald-700 dark:text-[#d4af37] bg-emerald-50 dark:bg-[#d4af37]/10 px-2 py-0.5 rounded-md border border-emerald-200/60 dark:border-[#d4af37]/30 w-max mt-1">
            {{ $headerAdmin->role_name ?? 'Admin' }}
          </span>
        </div>
      </div>

      @if(Route::has('logout'))
      <form action="{{ route('logout') }}" method="POST" class="m-0">
        @csrf
        <button type="submit" title="تسجيل الخروج" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200/60 dark:border-rose-900/50 transition-all active:scale-95 shadow-sm">
          <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25"/>
          </svg>
          <span class="hidden md:inline">تسجيل الخروج</span>
        </button>
      </form>
      @endif

    </div>
  </div>
</header>