@extends('layouts.admin')

@section('title', 'لوحة التحكم الرئيسية')
@section('page-title', 'لوحة التحكم الرئيسية')

@php
  // قيم احتياطية للعرض التجريبي فقط — البيانات الحقيقية تأتي من DashboardController@web
  $stats = $stats ?? ['total' => 0, 'finished' => 0, 'live' => 0, 'upcoming' => 0, 'pendingHalls' => 0, 'total_books' => 0];
  $completion_rate = $completion_rate ?? 0;
  $activities_by_center = $activities_by_center ?? collect();
  $live_event = $live_event ?? null;
  $recent_reservations = $recent_reservations ?? collect();
  $recent_books = $recent_books ?? collect();
  $books_by_category = $books_by_category ?? collect();

  $catMax = max((int) (collect($books_by_category)->map(fn($v) => (int) $v)->max() ?? 1), 1);
  $centerMax = max((int) (collect($activities_by_center)->max('value') ?? 1), 1);
@endphp

@push('styles')
<style>
  /* احترام تفضيل تقليل الحركة (prefers-reduced-motion) */
  @media (prefers-reduced-motion: reduce) {
    .dash-animate, .occupancy-bar, #gauge-circle { transition: none !important; animation: none !important; }
  }
  .tnum { font-variant-numeric: tabular-nums; }
</style>
@endpush

@section('content')

<div class="space-y-6 text-slate-800 dark:text-slate-100 transition-colors duration-300">

  {{-- 1. بطاقات الإحصائيات (بيانات حقيقية) --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">

    {{-- إجمالي الفعاليات --}}
    <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500/40" role="group" aria-label="إجمالي الفعاليات {{ $stats['total'] }}">
      <div class="flex items-center justify-between mb-4">
        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">إجمالي الفعاليات</span>
        <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-[#252A27] text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-white/5 flex items-center justify-center" aria-hidden="true">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
      </div>
      <div>
        <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2 tnum">{{ number_format($stats['total']) }}</div>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-semibold flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-amber-400"></span>
          عبر جميع المراكز الثقافية
        </p>
      </div>
    </div>

    {{-- الفعاليات المكتملة --}}
    <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500/40" role="group" aria-label="الفعاليات المكتملة {{ $stats['finished'] }}">
      <div class="flex items-center justify-between mb-4">
        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">الفعاليات المكتملة</span>
        <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40 flex items-center justify-center" aria-hidden="true">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
      <div>
        <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2 tnum">{{ number_format($stats['finished']) }}</div>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-semibold">فعاليات منتهية</p>
      </div>
    </div>

    {{-- الفعاليات الجارية --}}
    <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500/40" role="group" aria-label="الفعاليات الجارية الآن {{ $stats['live'] }}">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          @if($stats['live'] > 0)
            <span class="relative flex h-2.5 w-2.5" aria-hidden="true">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
            </span>
          @endif
          <span class="text-xs font-bold text-slate-600 dark:text-slate-300">الفعاليات الجارية</span>
        </div>
        <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/40 flex items-center justify-center" aria-hidden="true">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        </div>
      </div>
      <div>
        <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2 tnum">{{ number_format($stats['live']) }}</div>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-semibold tnum">{{ number_format($stats['upcoming'] ?? 0) }} فعالية قادمة</p>
      </div>
    </div>

    {{-- طلبات حجز القاعات --}}
    <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500/40" role="group" aria-label="طلبات حجز القاعات المعلقة {{ $stats['pendingHalls'] }}">
      <div class="flex items-center justify-between mb-4">
        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">طلبات حجز القاعات</span>
        <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40 flex items-center justify-center" aria-hidden="true">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
      <div>
        <div class="text-4xl font-black text-amber-600 dark:text-amber-400 tracking-tight mb-2 tnum">{{ number_format($stats['pendingHalls']) }}</div>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-semibold">بانتظار الموافقة والاعتماد</p>
      </div>
    </div>

    {{-- إجمالي الكتب --}}
    <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500/40" role="group" aria-label="إجمالي الكتب {{ $stats['total_books'] }}">
      <div class="flex items-center justify-between mb-4">
        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">إجمالي الكتب</span>
        <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-[#252A27] text-forest dark:text-emerald-400 border border-emerald-200 dark:border-white/5 flex items-center justify-center" aria-hidden="true">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5v-15A2.5 2.5 0 016.5 2H20v20H6.5a2.5 2.5 0 010-5H20"/></svg>
        </div>
      </div>
      <div>
        <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2 tnum">{{ number_format($stats['total_books']) }}</div>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-semibold flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
          عبر جميع مكتبات المراكز
        </p>
      </div>
    </div>

  </div>

  {{-- 2. قسم المخططات: الفعاليات حسب المركز + نسبة الإنجاز --}}
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- الفعاليات حسب المركز الثقافي (عمودان) --}}
    <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 md:p-7 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md xl:col-span-2 flex flex-col">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="section-accent font-extrabold text-slate-900 dark:text-white text-lg">الفعاليات حسب المركز الثقافي</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">أكثر المراكز نشاطاً حسب عدد الفعاليات المسجّلة</p>
        </div>
        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1.5 rounded-full border border-emerald-200 dark:border-emerald-800/40">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          الإجمالي: <span class="tnum">{{ number_format($stats['total']) }}</span>
        </span>
      </div>

      @if($activities_by_center->count() > 0)
        <div class="flex items-end gap-4 md:gap-8 h-60 px-2 pt-6" role="img" aria-label="مخطط أعمدة: عدد الفعاليات لكل مركز ثقافي">
          @foreach ($activities_by_center as $item)
            <div class="flex-1 flex flex-col items-center justify-end h-full">
              <span class="text-xs font-extrabold text-emerald-700 dark:text-emerald-400 mb-2 tnum bg-emerald-100 dark:bg-emerald-950 px-2 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800/60">
                {{ number_format($item['value']) }}
              </span>
              <div class="w-full max-w-[64px] bg-slate-100 dark:bg-[#111412] rounded-2xl overflow-hidden flex items-end p-1 relative border border-slate-200 dark:border-white/5" style="height: 100%;">
                <div class="occupancy-bar dash-animate w-full rounded-xl transition-all duration-700 ease-out relative shadow-inner"
                     style="height:0%; background: linear-gradient(180deg, #34d399 0%, #059669 50%, #064e3b 100%);"
                     data-h="{{ $centerMax > 0 ? round(($item['value'] / $centerMax) * 100) : 0 }}">
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="flex justify-between mt-4 px-2 border-t border-slate-100 dark:border-white/5 pt-3">
          @foreach ($activities_by_center as $item)
            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex-1 text-center truncate px-1" title="{{ $item['label'] }}">{{ $item['label'] }}</span>
          @endforeach
        </div>
      @else
        <div class="flex-1 min-h-[220px] flex flex-col items-center justify-center text-slate-400 gap-2">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          <span class="text-xs font-bold">لا توجد فعاليات مسجّلة بعد</span>
        </div>
      @endif
    </div>

    {{-- نسبة الفعاليات المكتملة (عمود واحد) --}}
    <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 md:p-7 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md flex flex-col items-center justify-between relative overflow-hidden">
      <div class="w-full text-right mb-4">
        <h2 class="section-accent font-extrabold text-slate-900 dark:text-white text-lg">نسبة الفعاليات المكتملة</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">الفعاليات المنتهية من إجمالي المسجّلة</p>
      </div>

      <div class="relative flex items-center justify-center my-2" role="img" aria-label="نسبة الفعاليات المكتملة {{ $completion_rate }} بالمئة">
        <svg width="190" height="190" viewBox="0 0 190 190" class="drop-shadow-lg">
          <defs>
            <linearGradient id="modernGaugeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" style="stop-color:#34d399;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
            </linearGradient>
          </defs>
          <circle cx="95" cy="95" r="76" fill="none" class="stroke-slate-100 dark:stroke-[#232925]" stroke-width="14"/>
          <circle id="gauge-circle" cx="95" cy="95" r="76" fill="none" stroke="url(#modernGaugeGradient)" stroke-width="14" stroke-linecap="round"
            stroke-dasharray="477" stroke-dashoffset="477" transform="rotate(-90 95 95)" data-percent="{{ $completion_rate }}"/>
          <text x="95" y="92" text-anchor="middle" font-size="34" font-weight="900" class="fill-slate-900 dark:fill-white" id="gauge-text" font-family="system-ui" style="font-variant-numeric: tabular-nums;">{{ $completion_rate }}%</text>
          <text x="95" y="115" text-anchor="middle" font-size="11" font-weight="700" class="fill-slate-400 dark:fill-slate-500">نسبة الإنجاز</text>
        </svg>
      </div>

      <div class="w-full bg-slate-50 dark:bg-[#111412] rounded-2xl p-3 text-center border border-slate-200 dark:border-white/5 mt-2">
        <p class="text-sm font-extrabold text-slate-900 dark:text-white tnum">{{ number_format($stats['finished']) }} <span class="text-slate-400 dark:text-slate-500 font-normal">/</span> {{ number_format($stats['total']) }}</p>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">فعالية مكتملة من الإجمالي</p>
      </div>
    </div>

  </div>

  {{-- 3. الفعالية الجارية/القادمة + أحدث طلبات الحجز --}}
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- بطاقة الفعالية الجارية أو القادمة (عمود واحد) --}}
    <div class="relative overflow-hidden rounded-3xl p-6 md:p-7 text-white bg-gradient-to-br from-emerald-900 via-emerald-950 to-slate-950 dark:from-[#0B3A2C] dark:via-[#082B20] dark:to-[#041712] border border-emerald-500/30 shadow-xl dark:shadow-2xl flex flex-col justify-between">
      <div class="absolute top-0 right-0 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

      @if($live_event)
        <div>
          <div class="flex items-center justify-between mb-4">
            @if($live_event['is_live'])
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[11px] font-black">
                <span class="relative flex h-2 w-2" aria-hidden="true">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                </span>
                جارية الآن
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/20 text-amber-200 border border-amber-400/30 text-[11px] font-black">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                الفعالية القادمة
              </span>
            @endif
            <span class="text-xs text-emerald-200/90 font-bold bg-white/10 px-3 py-1 rounded-full backdrop-blur-sm border border-white/5 max-w-[55%] truncate">{{ $live_event['center'] }}</span>
          </div>

          <h3 class="text-xl font-black text-white leading-snug mb-1">{{ $live_event['title'] }}</h3>
          <p class="text-xs text-emerald-200/70 font-semibold flex items-center gap-1.5">
            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            {{ $live_event['venue'] }}
          </p>
        </div>

        <div class="my-6 bg-black/40 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center">
          <p id="countdown-timer" class="text-emerald-400 text-3xl font-black tracking-widest tnum drop-shadow-sm" data-seconds="{{ $live_event['seconds'] }}">--:--:--</p>
          <p class="text-[11px] text-slate-300 dark:text-slate-400 font-medium mt-1">
            {{ $live_event['is_live'] ? 'الوقت المتبقي لانتهاء الفعالية' : 'الوقت المتبقي لبدء الفعالية' }}
          </p>
        </div>

        <div class="flex gap-2.5">
          <a href="{{ Route::has('admin.events.show') ? route('admin.events.show', $live_event['id']) : (Route::has('admin.events.index') ? route('admin.events.index') : '#') }}"
             class="flex-1 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-black py-3 rounded-xl text-center transition-all duration-200 shadow-lg">
            تفاصيل الفعالية
          </a>
        </div>
      @else
        <div class="flex flex-col items-center justify-center text-center py-12 gap-3 min-h-[240px]">
          <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center border border-white/10" aria-hidden="true">
            <svg class="w-7 h-7 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <p class="text-sm font-black text-white">لا توجد فعاليات جارية أو قادمة</p>
          <p class="text-[11px] text-emerald-200/70 font-medium">ستظهر هنا الفعالية الجارية أو أقرب فعالية قادمة</p>
          <a href="{{ Route::has('admin.events.create') ? route('admin.events.create') : '#' }}" class="mt-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-black px-5 py-2.5 rounded-xl transition-all">إضافة فعالية</a>
        </div>
      @endif
    </div>

    {{-- أحدث طلبات حجز القاعات (عمودان) --}}
    <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 md:p-7 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md xl:col-span-2 flex flex-col">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="section-accent font-extrabold text-slate-900 dark:text-white text-lg">أحدث طلبات حجز القاعات</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">آخر 5 طلبات حجز مسجّلة في النظام</p>
        </div>
        <a href="{{ Route::has('admin.reservations.index') ? route('admin.reservations.index') : '#' }}"
           class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#232925] px-3.5 py-2 rounded-full border border-slate-200 dark:border-white/10 hover:bg-slate-200 dark:hover:bg-[#2b332e] hover:text-slate-900 dark:hover:text-white transition-all">
          عرض الكل
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
      </div>

      @if($recent_reservations->count() > 0)
        <div class="overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <th class="pb-3 px-3">مقدّم الطلب</th>
                <th class="pb-3 px-3">القاعة</th>
                <th class="pb-3 px-3">التاريخ</th>
                <th class="pb-3 px-3">الحالة</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-xs font-medium text-slate-700 dark:text-slate-300">
              @foreach ($recent_reservations as $r)
                @php
                  $applicant = $r->applicant_name ?: ($r->requesting_party ?: 'غير محدد');
                  $statusMap = [
                    'pending'   => ['label' => 'قيد الانتظار', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/80 dark:text-amber-400 dark:border-amber-800/50', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
                    'accepted'  => ['label' => 'مقبول',        'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/80 dark:text-emerald-400 dark:border-emerald-800/50', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
                    'approved'  => ['label' => 'معتمد',         'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/80 dark:text-emerald-400 dark:border-emerald-800/50', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
                    'rejected'  => ['label' => 'مرفوض',         'badge' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/80 dark:text-rose-400 dark:border-rose-800/50', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
                    'cancelled' => ['label' => 'ملغى',          'badge' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800/80 dark:text-slate-300 dark:border-slate-700', 'dot' => 'bg-slate-500 dark:bg-slate-400'],
                  ];
                  $st = $statusMap[$r->status] ?? ['label' => $r->status, 'badge' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800/80 dark:text-slate-300 dark:border-slate-700', 'dot' => 'bg-slate-500'];
                @endphp
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors duration-150">
                  <td class="py-3.5 px-3">
                    <div class="flex items-center gap-2">
                      <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-center font-bold text-[11px]" aria-hidden="true">
                        {{ mb_substr($applicant, 0, 1) }}
                      </div>
                      <span class="font-extrabold text-slate-900 dark:text-white max-w-[140px] truncate">{{ $applicant }}</span>
                    </div>
                  </td>
                  <td class="py-3.5 px-3 text-slate-600 dark:text-slate-300 font-semibold max-w-[160px] truncate">{{ $r->venue->name ?? 'غير محدد' }}</td>
                  <td class="py-3.5 px-3 text-slate-500 dark:text-slate-400 tnum">{{ optional($r->created_at)->format('Y-m-d') ?? '—' }}</td>
                  <td class="py-3.5 px-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $st['badge'] }}">
                      <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                      {{ $st['label'] }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="flex-1 min-h-[180px] flex flex-col items-center justify-center text-slate-400 gap-2">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          <span class="text-xs font-bold">لا توجد طلبات حجز حتى الآن</span>
        </div>
      @endif
    </div>

  </div>

  {{-- 4. ودجات الكتب: أحدث الكتب + توزيع التصنيفات --}}
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- أحدث الكتب (عمودان) --}}
    <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 md:p-7 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md xl:col-span-2 flex flex-col">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="section-accent font-extrabold text-slate-900 dark:text-white text-lg">أحدث 5 كتب مضافة</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">آخر الكتب التي تمت إضافتها إلى المكتبات</p>
        </div>
        <a href="{{ Route::has('admin.books.index') ? route('admin.books.index') : '#' }}"
           class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#232925] px-3.5 py-2 rounded-full border border-slate-200 dark:border-white/10 hover:bg-slate-200 dark:hover:bg-[#2b332e] hover:text-slate-900 dark:hover:text-white transition-all">
          عرض الكل
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
          <thead>
            <tr class="border-b border-slate-200 dark:border-white/10 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
              <th class="pb-3 px-3">الكتاب</th>
              <th class="pb-3 px-3">المؤلف</th>
              <th class="pb-3 px-3">المكتبة</th>
              <th class="pb-3 px-3 text-center">إجراءات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-xs font-medium text-slate-700 dark:text-slate-300">
            @forelse ($recent_books as $book)
              <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors duration-150">
                <td class="py-3 px-3">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-12 rounded-lg bg-slate-100 dark:bg-white/5 overflow-hidden flex-shrink-0 border border-slate-200 dark:border-white/10">
                      @if(!empty($book->cover_image) && \Illuminate\Support\Facades\Storage::disk('public')->exists($book->cover_image))
                        <img src="{{ Storage::url($book->cover_image) }}" alt="غلاف {{ $book->title }}" class="w-full h-full object-cover">
                      @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400" aria-hidden="true">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 19.5v-15A2.5 2.5 0 016.5 2H20v20H6.5a2.5 2.5 0 010-5H20"/></svg>
                        </div>
                      @endif
                    </div>
                    <span class="font-extrabold text-slate-900 dark:text-white max-w-[180px] truncate">{{ $book->title }}</span>
                  </div>
                </td>
                <td class="py-3 px-3 text-slate-600 dark:text-slate-300 font-semibold">{{ $book->author }}</td>
                <td class="py-3 px-3 text-slate-500 dark:text-slate-400">{{ $book->library->name ?? 'غير محدد' }}</td>
                <td class="py-3 px-3">
                  <div class="flex items-center justify-center gap-1.5">
                    <a href="{{ Route::has('admin.books.show') ? route('admin.books.show', $book->id) : '#' }}" class="p-1.5 text-slate-500 hover:text-forest dark:hover:text-emerald-400 hover:bg-emerald-500/10 rounded-lg transition-colors" title="عرض" aria-label="عرض {{ $book->title }}">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="{{ Route::has('admin.books.edit') ? route('admin.books.edit', $book->id) : '#' }}" class="p-1.5 text-slate-500 hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-colors" title="تعديل" aria-label="تعديل {{ $book->title }}">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="py-8 text-center text-slate-400">لا توجد كتب مضافة بعد.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- توزيع الكتب حسب التصنيف (عمود واحد) --}}
    <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 md:p-7 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md flex flex-col">
      <div class="mb-5">
        <h2 class="section-accent font-extrabold text-slate-900 dark:text-white text-lg">توزيع الكتب حسب التصنيف</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">عدد الكتب في كل تصنيف</p>
      </div>

      <div class="space-y-4 flex-1">
        @forelse ($books_by_category as $category => $count)
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <span class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate max-w-[70%]">{{ $category }}</span>
              <span class="text-xs font-black text-forest dark:text-emerald-400 tnum bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md border border-emerald-100 dark:border-emerald-800/40">{{ (int) $count }}</span>
            </div>
            <div class="w-full h-2.5 bg-slate-100 dark:bg-[#111412] rounded-full overflow-hidden border border-slate-200 dark:border-white/5" role="img" aria-label="{{ $category }}: {{ (int) $count }} كتاب">
              <div class="dash-animate h-full rounded-full transition-all duration-700 ease-out" style="width: {{ round(((int) $count / $catMax) * 100) }}%; background: linear-gradient(90deg, #34d399 0%, #059669 60%, #0F4C3A 100%);"></div>
            </div>
          </div>
        @empty
          <div class="h-full flex items-center justify-center text-slate-400 text-xs">لا توجد بيانات تصنيفات بعد.</div>
        @endforelse
      </div>
    </div>

  </div>

</div>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // أعمدة الفعاليات حسب المركز
    const bars = document.querySelectorAll('.occupancy-bar');
    if (reduceMotion) {
      bars.forEach(el => { el.style.height = (el.dataset.h || 0) + '%'; });
    } else {
      requestAnimationFrame(() => {
        bars.forEach((el, index) => {
          setTimeout(() => { el.style.height = (el.dataset.h || 0) + '%'; }, index * 120);
        });
      });
    }

    // مؤشر نسبة الإنجاز (حلقة)
    const gaugeCircle = document.getElementById('gauge-circle');
    const gaugeText = document.getElementById('gauge-text');
    if (gaugeCircle) {
      const pct = parseInt(gaugeCircle.dataset.percent, 10) || 0;
      const circumference = 477;
      const finalOffset = circumference - (circumference * pct / 100);
      if (reduceMotion) {
        gaugeCircle.setAttribute('stroke-dashoffset', finalOffset);
        if (gaugeText) gaugeText.textContent = pct + '%';
      } else {
        setTimeout(() => {
          gaugeCircle.style.transition = 'stroke-dashoffset 1.2s cubic-bezier(0.34, 1.56, 0.64, 1)';
          gaugeCircle.setAttribute('stroke-dashoffset', finalOffset);
        }, 250);
        let cur = 0;
        const step = Math.max(1, Math.round(pct / 40)) || 1;
        const gaugeInterval = setInterval(() => {
          cur = Math.min(cur + step, pct);
          if (gaugeText) gaugeText.textContent = cur + '%';
          if (cur >= pct) clearInterval(gaugeInterval);
        }, 16);
      }
    }

    // العد التنازلي للفعالية الجارية/القادمة
    const timerEl = document.getElementById('countdown-timer');
    if (timerEl) {
      let seconds = parseInt(timerEl.dataset.seconds, 10) || 0;
      const render = () => {
        if (seconds <= 0) { timerEl.textContent = '00:00:00'; return; }
        const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
        const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        timerEl.textContent = `${h}:${m}:${s}`;
      };
      render();
      if (seconds > 0) {
        setInterval(() => { if (seconds > 0) seconds--; render(); }, 1000);
      }
    }
  });
</script>
@endpush
