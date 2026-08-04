@extends('layouts.admin')

@section('title', 'إدارة القاعات')
@section('page-title', 'قائمة القاعات والمرافق')

@section('content')

<div class="space-y-6 text-slate-800 dark:text-slate-100 transition-colors duration-300">

  {{-- 1. Top Header & Action Button --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">قائمة القاعات والمرافق</h2>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">إدارة قاعات المراكز الثقافية وسعاتها ومميزاتها</p>
    </div>
    <a href="{{ route('admin.halls.create') }}" 
       class="inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 dark:bg-gradient-to-r dark:from-amber-500 dark:to-amber-600 dark:hover:from-amber-400 dark:hover:to-amber-500 text-slate-950 font-black text-xs px-5 py-3 rounded-2xl shadow-md dark:shadow-lg transition-all duration-200 hover:scale-[1.02]">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
      إضافة قاعة جديدة
    </a>
  </div>

  {{-- 2. Stats Cards --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    
    {{-- Card 1: Total Halls --}}
    <div class="bg-white dark:bg-[#181C1A]/90 border border-slate-200 dark:border-white/10 rounded-3xl p-6 shadow-sm dark:shadow-xl backdrop-blur-md flex items-center justify-between transition-colors duration-300">
      <div>
        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">إجمالي القاعات</span>
        <h3 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight mt-1">{{ $hallsCount ?? 1 }}</h3>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7"/></svg>
      </div>
    </div>

    {{-- Card 2: Total Capacity --}}
    <div class="bg-white dark:bg-[#181C1A]/90 border border-slate-200 dark:border-white/10 rounded-3xl p-6 shadow-sm dark:shadow-xl backdrop-blur-md flex items-center justify-between transition-colors duration-300">
      <div>
        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">السعة الكلية</span>
        <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight mt-1">
          {{ number_format($totalCapacity ?? 100) }} <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">شخص</span>
        </h3>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
      </div>
    </div>

  </div>

  {{-- 3. Search Bar --}}
  <div class="bg-white dark:bg-[#181C1A]/90 rounded-2xl p-4 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-lg backdrop-blur-md transition-colors duration-300">
    <div class="relative max-w-md">
      <input type="text" 
             placeholder="بحث باسم القاعة..." 
             class="w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-xs rounded-xl pr-10 pl-4 py-3 border border-slate-200 dark:border-white/10 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-all">
      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </div>
    </div>
  </div>

  {{-- 4. Halls Table --}}
  <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md overflow-hidden transition-colors duration-300">
    <div class="overflow-x-auto">
      <table class="w-full text-right border-collapse">
        <thead>
          <tr class="border-b border-slate-200 dark:border-white/10 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
            <th class="pb-4 px-4">الصورة</th>
            <th class="pb-4 px-4">اسم القاعة</th>
            <th class="pb-4 px-4">معرف المركز</th>
            <th class="pb-4 px-4">السعة</th>
            <th class="pb-4 px-4">الميزات</th>
            <th class="pb-4 px-4 text-center">الإجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-xs font-medium text-slate-700 dark:text-slate-200">
          @forelse($halls ?? [] as $hall)
            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors duration-150">
              <td class="py-4 px-4">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-[#111412] border border-slate-200 dark:border-white/10 overflow-hidden flex items-center justify-center text-slate-400">
                  @if(isset($hall->image_url))
                    <img src="{{ asset($hall->image_url) }}" class="w-full h-full object-cover">
                  @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  @endif
                </div>
              </td>
              <td class="py-4 px-4 font-extrabold text-slate-900 dark:text-white text-sm">
                {{ $hall->name ?? 'الجمال' }}
              </td>
              <td class="py-4 px-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                  #{{ $hall->center_id ?? 1 }}
                </span>
              </td>
              <td class="py-4 px-4">
                <span class="font-extrabold text-emerald-600 dark:text-emerald-400 font-mono text-sm">
                  {{ $hall->capacity ?? 100 }} <span class="text-xs text-slate-500 dark:text-slate-400 font-sans font-normal">شخص</span>
                </span>
              </td>
              <td class="py-4 px-4 text-slate-600 dark:text-slate-300">
                <div class="flex flex-wrap gap-1.5">
                  @php
                    $featuresList = is_array($hall->features) 
                        ? $hall->features 
                        : (is_string($hall->features) ? explode(' ', $hall->features) : ['بروجكتور', 'برادة مياه', 'مكيف']);
                  @endphp

                  @foreach($featuresList as $feature)
                    <span class="bg-slate-100 dark:bg-[#111412] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10 px-2.5 py-1 rounded-md text-[11px]">
                      {{ $feature }}
                    </span>
                  @endforeach
                </div>
              </td>
              <td class="py-4 px-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="#" class="p-2 rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all" title="تعديل">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </a>
                  <button type="button" class="p-2 rounded-xl bg-rose-500/10 text-rose-700 dark:text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 transition-all" title="حذف">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors duration-150">
              <td class="py-4 px-4">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-[#111412] border border-slate-200 dark:border-white/10 overflow-hidden flex items-center justify-center text-slate-400">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
              </td>
              <td class="py-4 px-4 font-extrabold text-slate-900 dark:text-white text-sm">الجمال</td>
              <td class="py-4 px-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                  #1
                </span>
              </td>
              <td class="py-4 px-4">
                <span class="font-extrabold text-emerald-600 dark:text-emerald-400 font-mono text-sm">
                  100 <span class="text-xs text-slate-500 dark:text-slate-400 font-sans font-normal">شخص</span>
                </span>
              </td>
              <td class="py-4 px-4 text-slate-600 dark:text-slate-300">
                <div class="flex flex-wrap gap-1.5">
                  <span class="bg-slate-100 dark:bg-[#111412] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10 px-2 py-0.5 rounded-md text-[11px]">بروجكتور</span>
                  <span class="bg-slate-100 dark:bg-[#111412] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10 px-2 py-0.5 rounded-md text-[11px]">برادة مياه</span>
                  <span class="bg-slate-100 dark:bg-[#111412] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10 px-2 py-0.5 rounded-md text-[11px]">مكيف</span>
                </div>
              </td>
              <td class="py-4 px-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="#" class="p-2 rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </a>
                  <button type="button" class="p-2 rounded-xl bg-rose-500/10 text-rose-700 dark:text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

@endsection