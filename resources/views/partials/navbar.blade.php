<header class="sticky top-0 z-20 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-4 md:px-8 py-3 transition-all duration-300">
  <div class="flex items-center justify-between gap-4 max-w-[1600px] mx-auto">
    
    <!-- الجانب الأيمن -->
    <div class="flex items-center gap-3.5">
      <!-- زر القائمة الجانبية للموبايل -->
      <button id="mobile-sidebar-toggle" onclick="toggleMobileSidebar()" 
              class="lg:hidden p-2.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all active:scale-95">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      <!-- Breadcrumb + Title -->
      <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center justify-center w-9 h-9 rounded-xl bg-forest/10 text-forest">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
        </div>
        <div>
          <h1 class="text-base md:text-lg font-black text-slate-800 tracking-tight leading-tight">
            @yield('page-title', 'لوحة التحكم')
          </h1>
          <p class="text-[11px] text-slate-400 font-medium hidden md:block">{{ now()->format('l، d M Y') }}</p>
        </div>
      </div>
    </div>

    <!-- الجانب الأيسر -->
    <div class="flex items-center gap-2 md:gap-4">
      
      <!-- زر الإشعارات -->
      <button class="relative p-2.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all active:scale-95">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
      </button>

      <!-- فاصل -->
      <div class="h-8 w-px bg-slate-200 hidden md:block"></div>

      <!-- بطاقة المستخدم -->
      <div class="flex items-center gap-3 pl-1">
        <div class="relative group cursor-pointer">
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 border-2 border-emerald-500/20 text-emerald-700 font-black flex items-center justify-center text-sm shadow-sm transition-all group-hover:shadow-md group-hover:border-emerald-500/40">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
          </div>
          <span class="absolute bottom-0 left-0 w-3 h-3 bg-emerald-500 border-[2.5px] border-white rounded-full"></span>
          
          <!-- Dropdown -->
          <div class="absolute left-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-left">
            <div class="p-4 border-b border-slate-50">
              <p class="font-extrabold text-slate-800 text-sm">{{ auth()->user()->name ?? 'مشرف النظام' }}</p>
              <p class="text-xs text-slate-400 mt-0.5">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
            </div>
            <div class="p-2">
              <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                الملف الشخصي
              </a>
              <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                الإعدادات
              </a>
            </div>
          </div>
        </div>

        <div class="hidden sm:flex flex-col text-right">
          <span class="text-xs font-extrabold text-slate-800 leading-tight">
            {{ auth()->user()->name ?? 'مشرف النظام' }}
          </span>
          <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60 w-max mt-1">
            {{ auth()->user()->role_name ?? 'Admin' }}
          </span>
        </div>
      </div>

      <!-- زر تسجيل الخروج -->
      <form action="{{ route('logout') }}" method="POST" class="m-0">
        @csrf
        <button type="submit" title="تسجيل الخروج" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition-all active:scale-95 shadow-sm">
          <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25"/>
          </svg>
          <span class="hidden md:inline">تسجيل الخروج</span>
        </button>
      </form>

    </div>
  </div>
</header>