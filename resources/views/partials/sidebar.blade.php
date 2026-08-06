@php
  $currentRole = Auth::guard('admin')->user()?->role;
  
  $menuItems = [
    [
      'route' => 'admin.dashboard',
      'label' => 'لوحة التحكم',
      'roles' => ['super', 'admin'],
      'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    ],
    [
      'route' => 'admin.cultural_centers.index',
      'label' => 'المراكز الثقافية',
      'roles' => ['super', 'admin'],
      'icon'  => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z'
    ],
    [
      'route' => 'admin.events.index',
      'label' => 'الفعاليات',
      'roles' => ['super', 'admin'],
      'icon'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
    ],
    [
      'route' => 'admin.venues.index',
      'label' => 'القاعات والمرافق',
      'roles' => ['super', 'admin'],
      'icon'  => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
    ],
    [
      'route' => 'admin.libraries.index',
      'label' => 'المكتبات',
      'roles' => ['super', 'admin'],
      'icon'  => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'
    ],
    [
      'route' => 'admin.books.index',
      'label' => 'الكتب',
      'roles' => ['super', 'admin'],
      'icon'  => 'M4 19.5v-15A2.5 2.5 0 016.5 2H20v20H6.5a2.5 2.5 0 010-5H20'
    ],
    [
      'route' => 'admin.venue_reservations.index',
      'label' => 'حجوزات القاعات',
      'roles' => ['super', 'admin'],
      'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'
    ],
    [
      'route' => 'admin.reservations.index', // تم التعديل لربط إدارة الحجوزات
      'label' => 'الحجوزات ومتابعة الحضور',
      'roles' => ['super', 'admin'],
      'icon'  => 'M12 4v1m0 14v1m8-8h1M3 12h1m14.36-6.36l-.7.7M6.34 17.66l-.7.7m12.02 0l-.7-.7M6.34 6.34l-.7-.7M9 9h1v1H9V9zm5 0h1v1h-1V9zm-5 5h1v1H9v-1zm5 0h1v1h-1v-1z'
    ],
    [
      'route' => 'admin.barcode.scan',
      'label' => 'مسح الباركود',
      'roles' => ['ticketsAdmin', 'super', 'admin'],
      'icon'  => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M4 8h4m4-4h4m-4 16h4'
    ],
    [
      'route' => 'admin.users.index',
      'label' => 'المستخدمين والحظر',
      'roles' => ['super'],
      'icon'  => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-3.13a4 4 0 100-8 4 4 0 000 8zm6 3a4 4 0 010 7.75M7 12a4 4 0 010-7.75'
    ],
    [
      'route' => 'admin.memberships.index',
      'label' => 'العضويات الثقافية',
      'roles' => ['super'],
      'icon'  => 'M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7zm0 4h18M7 15h4'
    ],
    [
      'route' => 'admin.reports.index',
      'label' => 'التقارير وتصدير البيانات',
      'roles' => ['super', 'admin'],
      'icon'  => 'M9 17v-6h2v6H9zm4 0v-9h2v9h-2zm-8 0v-3h2v3H5zm14 3H3a1 1 0 01-1-1V4'
    ],
  ];
@endphp

{{-- القائمة الجانبية للشاشات المكتبية --}}
<aside id="main-sidebar" class="hidden lg:flex flex-col w-72 shrink-0 h-screen sticky top-0 bg-white dark:bg-[#1a1a1a] border-l border-slate-200/80 dark:border-[#d4af37]/20 z-30 transition-colors duration-300">
  <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-100 dark:border-[#d4af37]/10">
    <div class="w-11 h-11 rounded-2xl bg-forest dark:bg-[#d4af37] flex items-center justify-center text-white dark:text-[#1a1a1a] font-black text-lg shadow-lg">
      ث
    </div>
    <div class="flex flex-col">
      <h1 class="font-extrabold text-slate-800 dark:text-[#d4af37] text-[15px] leading-tight">منظومة الثقافة</h1>
      <p class="text-[11px] text-slate-400 dark:text-gray-400 font-medium mt-0.5">إدارة المراكز الثقافية</p>
    </div>
  </div>

  <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-0.5 custom-scrollbar">
    <p class="text-[10px] font-bold text-slate-400 dark:text-gray-400 uppercase tracking-wider px-3 mb-2 mt-1">القائمة الرئيسية</p>
    
    @foreach ($menuItems as $item)
      @if (in_array($currentRole, $item['roles']))
        @php
          $hasRoute = $item['route'] !== '#' && Route::has($item['route']);
          $routeUrl = $hasRoute ? route($item['route']) : '#';
          $isActive = $hasRoute && request()->routeIs($item['route'].'*');
        @endphp
        <a href="{{ $routeUrl }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 relative
           {{ $isActive 
              ? 'bg-forest dark:bg-[#d4af37] text-white dark:text-[#1a1a1a] shadow-md' 
              : 'text-slate-500 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-[#d4af37]/10 hover:text-slate-800 dark:hover:text-[#d4af37]' }}">
          
          <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ $isActive ? 'bg-white/15 dark:bg-[#1a1a1a]/20' : 'bg-slate-100 dark:bg-[#262626]' }}">
            <svg class="w-[18px] h-[18px] {{ $isActive ? 'text-white dark:text-[#1a1a1a]' : 'text-slate-400 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
            </svg>
          </span>
          
          <span class="flex-1">{{ $item['label'] }}</span>
        </a>
      @endif
    @endforeach

    <div class="mx-3 my-3 border-t border-slate-100 dark:border-[#d4af37]/10"></div>

    @if($currentRole !== 'ticketsAdmin' && Route::has('admin.profile'))
      @php $isProfileActive = request()->routeIs('admin.profile*'); @endphp
      <a href="{{ route('admin.profile') }}"
         class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200
         {{ $isProfileActive ? 'bg-forest dark:bg-[#d4af37] text-white dark:text-[#1a1a1a] shadow-md' : 'text-slate-500 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-[#d4af37]/10' }}">
        <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ $isProfileActive ? 'bg-white/15 dark:bg-[#1a1a1a]/20' : 'bg-slate-100 dark:bg-[#262626]' }}">
          <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
        </span>
        <span class="flex-1">الملف الشخصي</span>
      </a>
    @endif
  </nav>
</aside>

{{-- القائمة الجانبية لهواتف الجوال --}}
<div id="mobile-sidebar-overlay" onclick="closeMobileSidebar()" class="fixed inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity opacity-0"></div>

<aside id="mobile-sidebar" class="fixed inset-y-0 right-0 w-72 bg-white dark:bg-[#1a1a1a] border-l border-slate-200/80 dark:border-[#d4af37]/20 z-50 transform translate-x-full lg:hidden transition-transform duration-300 ease-out shadow-2xl">
  <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-[#d4af37]/10">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-forest dark:bg-[#d4af37] flex items-center justify-center text-white dark:text-[#1a1a1a] font-black">ث</div>
      <h1 class="font-extrabold text-slate-800 dark:text-[#d4af37]">منظومة الثقافة</h1>
    </div>
    <button onclick="closeMobileSidebar()" aria-label="إغلاق القائمة" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
      <svg class="w-5 h-5" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>
  
  <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-0.5">
    @foreach ($menuItems as $item)
      @if (in_array($currentRole, $item['roles']))
        @php 
          $hasRoute = $item['route'] !== '#' && Route::has($item['route']);
          $routeUrl = $hasRoute ? route($item['route']) : '#';
          $isActive = $hasRoute && request()->routeIs($item['route'].'*'); 
        @endphp
        <a href="{{ $routeUrl }}" onclick="closeMobileSidebar()"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 {{ $isActive ? 'bg-forest dark:bg-[#d4af37] text-white dark:text-[#1a1a1a] shadow-md' : 'text-slate-500 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-[#d4af37]/10' }}">
          <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ $isActive ? 'bg-white/15 dark:bg-[#1a1a1a]/20' : 'bg-slate-100 dark:bg-[#262626]' }}">
            <svg class="w-[18px] h-[18px] {{ $isActive ? 'text-white dark:text-[#1a1a1a]' : 'text-slate-400 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
          </span>
          <span>{{ $item['label'] }}</span>
        </a>
      @endif
    @endforeach
  </nav>
</aside>

<script>
  function toggleMobileSidebar() {
    const sidebar = document.getElementById('mobile-sidebar');
    const overlay = document.getElementById('mobile-sidebar-overlay');
    if (!sidebar || !overlay) return;
    sidebar.classList.remove('translate-x-full');
    overlay.classList.remove('hidden');
    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
    document.body.style.overflow = 'hidden';
  }
  function closeMobileSidebar() {
    const sidebar = document.getElementById('mobile-sidebar');
    const overlay = document.getElementById('mobile-sidebar-overlay');
    if (!sidebar || !overlay) return;
    sidebar.classList.add('translate-x-full');
    overlay.classList.add('opacity-0');
    setTimeout(() => overlay.classList.add('hidden'), 300);
    document.body.style.overflow = '';
  }
</script>