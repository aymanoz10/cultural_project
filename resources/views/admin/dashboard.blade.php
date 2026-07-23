@extends('layouts.admin')

@section('title', 'لوحة التحكم الرئيسية')
@section('page-title', 'لوحة التحكم الرئيسية')

@php
  // Fallback demo values — replace by passing real data from DashboardController@index
  $stats = $stats ?? ['total' => 8, 'finished' => 2, 'live' => 4, 'pendingHalls' => 3];
  $occupancy = $occupancy ?? [
      ['label' => 'أدهم إسماعيل', 'value' => 78],
      ['label' => 'دار الأسد',     'value' => 92],
      ['label' => 'الميدان',       'value' => 54],
      ['label' => 'كفرسوسة',       'value' => 66],
  ];
  $gaugePercent = $gaugePercent ?? 41;
  $scannedCount = $scannedCount ?? 1024;
  $totalTickets = $totalTickets ?? 2498;
  $liveEvent = $liveEvent ?? (object)[
      'title' => 'حفل موسيقي: تراث دمشقي',
      'center' => 'مركز كفرسوسة الثقافي',
      'hall' => 'القاعة الرئيسية',
      'remaining_seconds' => 5048,
  ];
  $recentBookings = $recentBookings ?? collect([
      (object)['ticket_id'=>'TCK-10231','visitor'=>'سارة العلي','event'=>'أمسية شعرية: قصائد من الشام','time'=>'18:42:10','status'=>'صالحة'],
      (object)['ticket_id'=>'TCK-10198','visitor'=>'يوسف حمدان','event'=>'حفل موسيقي: تراث دمشقي','time'=>'18:40:55','status'=>'مستخدمة'],
      (object)['ticket_id'=>'TCK-10305','visitor'=>'لينا شاهين','event'=>'ورشة المسرح التفاعلي للشباب','time'=>'18:39:02','status'=>'صالحة'],
      (object)['ticket_id'=>'TCK-10112','visitor'=>'عمر ديب','event'=>'معرض الفن التشكيلي السوري المعاصر','time'=>'18:37:47','status'=>'غير صالحة'],
      (object)['ticket_id'=>'TCK-10276','visitor'=>'رنا قاسم','event'=>'أمسية شعرية: قصائد من الشام','time'=>'18:35:19','status'=>'صالحة'],
  ]);
  $statusPillClass = [
      'نشطة'=>'status-active','مكتملة'=>'status-slate','ملغاة'=>'status-rejected',
      'صالحة'=>'status-active','مستخدمة'=>'status-slate','غير صالحة'=>'status-rejected',
  ];
@endphp

@section('content')

<div class="space-y-6">

  {{-- 1. Stats Cards Grid --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
    
    {{-- Card 1: Total Events (Hero Dark Emerald Card) --}}
    <div class="relative overflow-hidden rounded-3xl p-6 text-white bg-gradient-to-br from-[#0F4C3A] via-[#0B3A2C] to-[#06221A] shadow-xl shadow-emerald-950/20 ring-1 ring-white/10 group transition-all duration-300 hover:-translate-y-1">
      <div class="absolute -end-8 -bottom-8 w-36 h-36 bg-emerald-400/10 rounded-full blur-2xl group-hover:bg-emerald-400/20 transition-all duration-500"></div>
      
      <div class="relative z-10 flex flex-col justify-between h-full">
        <div class="flex items-center justify-between mb-4">
          <span class="text-xs font-bold text-emerald-200/90 bg-white/10 backdrop-blur-md px-3 py-1 rounded-full border border-white/10">إجمالي الفعاليات</span>
          <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10 shadow-inner">
            <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
          </div>
        </div>
        <div>
          <h3 class="text-4xl font-black tracking-tight text-white mb-2">{{ $stats['total'] }}</h3>
          <p class="text-[11px] text-emerald-200/70 font-semibold flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
            عبر جميع المراكز الثقافية
          </p>
        </div>
      </div>
    </div>

    {{-- Card 2: Completed Events --}}
    <div class="bg-white rounded-3xl p-6 ring-1 ring-slate-900/5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md flex flex-col justify-between">
      <div class="flex items-center justify-between mb-4">
        <span class="text-xs font-bold text-slate-500">الفعاليات المكتملة</span>
        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
      <div>
        <h3 class="text-4xl font-black text-slate-900 tracking-tight mb-2">{{ $stats['finished'] }}</h3>
        <p class="text-[11px] text-slate-400 font-semibold">خلال الشهر الحالي</p>
      </div>
    </div>

    {{-- Card 3: Live Events --}}
    <div class="bg-white rounded-3xl p-6 ring-1 ring-slate-900/5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md flex flex-col justify-between">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
          </span>
          <span class="text-xs font-bold text-slate-500">الفعاليات الجارية</span>
        </div>
        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        </div>
      </div>
      <div>
        <h3 class="text-4xl font-black text-slate-900 tracking-tight mb-2">{{ $stats['live'] }}</h3>
        <p class="text-[11px] text-slate-400 font-semibold">بث مباشر من نقاط الحضور</p>
      </div>
    </div>

    {{-- Card 4: Pending Requests --}}
    <div class="bg-white rounded-3xl p-6 ring-1 ring-slate-900/5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md flex flex-col justify-between">
      <div class="flex items-center justify-between mb-4">
        <span class="text-xs font-bold text-slate-500">طلبات حجز القاعات</span>
        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
      <div>
        <h3 class="text-4xl font-black text-amber-600 tracking-tight mb-2">{{ $stats['pendingHalls'] }}</h3>
        <p class="text-[11px] text-slate-400 font-semibold">بانتظار الموافقة والاعتماد</p>
      </div>
    </div>

  </div>

  {{-- 2. Charts Section --}}
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    
    {{-- Occupancy Chart (2 Columns) --}}
    <div class="bg-white rounded-3xl p-6 md:p-7 ring-1 ring-slate-900/5 shadow-sm xl:col-span-2 flex flex-col justify-between">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="font-extrabold text-slate-900 text-lg">نسبة إشغال القاعات والمراكز</h3>
          <p class="text-xs text-slate-400 font-medium mt-0.5">مقارنة أسبوعية دقيقة بين المراكز الأربعة</p>
        </div>
        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-100">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          هذا الأسبوع
        </span>
      </div>

      <div class="flex items-end gap-4 md:gap-8 h-60 px-2 pt-6">
        @foreach ($occupancy as $index => $item)
          <div class="flex-1 flex flex-col items-center justify-end h-full group/bar">
            <span class="text-xs font-extrabold text-emerald-800 mb-2 opacity-0 translate-y-2 transition-all duration-300 group-hover/bar:opacity-100 group-hover/bar:translate-y-0 font-mono bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
              {{ $item['value'] }}%
            </span>
            <div class="w-full max-w-[64px] bg-slate-100 rounded-2xl overflow-hidden flex items-end p-1 relative" style="height: 100%;">
              <div class="occupancy-bar w-full rounded-xl transition-all duration-700 ease-out relative group-hover/bar:brightness-110 shadow-sm" 
                   style="height:0%; background: linear-gradient(180deg, {{ ['#0F4C3A','#1B6B4F','#2A8A65','#3AA980'][$index] }} 0%, {{ ['#06221A','#0F4C3A','#1B6B4F','#2A8A65'][$index] }} 100%);" 
                   data-h="{{ $item['value'] }}">
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="flex justify-between mt-4 px-2 border-t border-slate-100 pt-3">
        @foreach ($occupancy as $item)
          <span class="text-xs font-bold text-slate-600 w-1/4 text-center truncate">{{ $item['label'] }}</span>
        @endforeach
      </div>
    </div>

    {{-- Gauge Chart (1 Column) --}}
    <div class="bg-white rounded-3xl p-6 md:p-7 ring-1 ring-slate-900/5 shadow-sm flex flex-col items-center justify-between relative overflow-hidden">
      <div class="w-full text-right mb-4">
        <h3 class="font-extrabold text-slate-900 text-lg">نسبة مسح التذاكر اليومية</h3>
        <p class="text-xs text-slate-400 font-medium mt-0.5">معدل الدخول والتحقق الفعلي حتى اللحظة</p>
      </div>

      <div class="relative flex items-center justify-center my-2">
        <svg width="190" height="190" viewBox="0 0 190 190" class="drop-shadow-md">
          <defs>
            <linearGradient id="modernGaugeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" style="stop-color:#0F4C3A;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#3AA980;stop-opacity:1" />
            </linearGradient>
          </defs>
          <!-- Background track -->
          <circle cx="95" cy="95" r="76" fill="none" stroke="#F1F5F9" stroke-width="14"/>
          <!-- Progress ring -->
          <circle id="gauge-circle" cx="95" cy="95" r="76" fill="none" stroke="url(#modernGaugeGradient)" stroke-width="14" stroke-linecap="round"
            stroke-dasharray="477" stroke-dashoffset="477" transform="rotate(-90 95 95)" data-percent="{{ $gaugePercent }}"/>
          
          <text x="95" y="92" text-anchor="middle" font-size="34" font-weight="900" fill="#0F4C3A" id="gauge-text" font-family="system-ui">0%</text>
          <text x="95" y="115" text-anchor="middle" font-size="11" font-weight="700" fill="#94A3B8">نسبة الإنجاز</text>
        </svg>
      </div>

      <div class="w-full bg-slate-50 rounded-2xl p-3 text-center border border-slate-100 mt-2">
        <p class="text-sm font-extrabold text-slate-800 font-mono">{{ number_format($scannedCount) }} <span class="text-slate-400 font-normal">/</span> {{ number_format($totalTickets) }}</p>
        <p class="text-[11px] text-slate-400 font-medium mt-0.5">تذكرة تم تحقق مسحها بنجاح</p>
      </div>
    </div>

  </div>

  {{-- 3. Live Event & Recent Bookings Row --}}
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    
    {{-- Live Event Card (1 Column) --}}
    <div class="relative overflow-hidden rounded-3xl p-6 md:p-7 text-white bg-gradient-to-br from-[#0F4C3A] via-[#0B3A2C] to-[#041712] shadow-xl shadow-emerald-950/20 ring-1 ring-white/10 flex flex-col justify-between">
      <div class="absolute top-0 right-0 w-48 h-48 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

      <div>
        <div class="flex items-center justify-between mb-4">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[11px] font-black">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
            </span>
            بث مباشر الآن
          </span>
          <span class="text-xs text-emerald-200/80 font-bold bg-white/10 px-3 py-1 rounded-full backdrop-blur-sm">{{ $liveEvent->center }}</span>
        </div>

        <h3 class="text-xl font-black text-white leading-snug mb-1">{{ $liveEvent->title }}</h3>
        <p class="text-xs text-emerald-200/70 font-semibold flex items-center gap-1.5">
          <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          {{ $liveEvent->hall }}
        </p>
      </div>

      <div class="my-6 bg-black/20 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center">
        <p id="countdown-timer" class="text-white text-3xl font-black tracking-widest font-mono drop-shadow-sm" data-seconds="{{ $liveEvent->remaining_seconds }}">--:--:--</p>
        <p class="text-[11px] text-emerald-200/60 font-medium mt-1">الوقت المتبقي لانتهاء الفعالية</p>
      </div>

      <div class="flex gap-2.5">
        <a href="{{ Route::has('admin.events.index') ? route('admin.events.index') : '#' }}" 
           class="flex-1 bg-white hover:bg-emerald-50 text-[#0F4C3A] text-xs font-black py-3 rounded-xl text-center transition-all duration-200 shadow-md">
          تفاصيل الفعالية
        </a>
        <button type="button" 
                class="bg-white/10 hover:bg-white/20 text-white text-xs font-bold px-4 py-3 rounded-xl border border-white/10 transition-all duration-200 backdrop-blur-sm">
          إنهاء البث
        </button>
      </div>
    </div>

    {{-- Recent Bookings Table (2 Columns) --}}
    <div class="bg-white rounded-3xl p-6 md:p-7 ring-1 ring-slate-900/5 shadow-sm xl:col-span-2 flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-5">
          <div>
            <h3 class="font-extrabold text-slate-900 text-lg">آخر الحجوزات وتسجيلات الحضور</h3>
            <p class="text-xs text-slate-400 font-medium mt-0.5">استعراض لآخر 5 عمليات مسح وتسجيل محلي</p>
          </div>
          <a href="{{ Route::has('admin.bookings.index') ? route('admin.bookings.index') : '#' }}" 
             class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 bg-emerald-50 px-3.5 py-2 rounded-full border border-emerald-100 hover:bg-emerald-100 transition-all">
            عرض الكل
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          </a>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-100 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                <th class="pb-3 px-3">رمز التذكرة</th>
                <th class="pb-3 px-3">الزائر</th>
                <th class="pb-3 px-3">الفعالية</th>
                <th class="pb-3 px-3">الوقت</th>
                <th class="pb-3 px-3">الحالة</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-xs font-medium text-slate-700">
              @foreach ($recentBookings as $b)
                <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                  <td class="py-3.5 px-3">
                    <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-1 rounded-md text-[11px] border border-slate-200/60">
                      {{ $b->ticket_id }}
                    </span>
                  </td>
                  <td class="py-3.5 px-3">
                    <div class="flex items-center gap-2">
                      <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 text-emerald-800 flex items-center justify-center font-bold text-[11px]">
                        {{ mb_substr($b->visitor, 0, 1) }}
                      </div>
                      <span class="font-extrabold text-slate-800">{{ $b->visitor }}</span>
                    </div>
                  </td>
                  <td class="py-3.5 px-3 text-slate-600 font-semibold max-w-[200px] truncate">{{ $b->event }}</td>
                  <td class="py-3.5 px-3 text-slate-400 font-mono text-[11px]">{{ $b->time }}</td>
                  <td class="py-3.5 px-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold
                      {{ $b->status === 'صالحة' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                      {{ $b->status === 'مستخدمة' ? 'bg-slate-100 text-slate-600 border border-slate-200' : '' }}
                      {{ $b->status === 'غير صالحة' ? 'bg-rose-50 text-rose-600 border border-rose-200' : '' }}
                    ">
                      <span class="w-1.5 h-1.5 rounded-full 
                        {{ $b->status === 'صالحة' ? 'bg-emerald-500' : '' }}
                        {{ $b->status === 'مستخدمة' ? 'bg-slate-400' : '' }}
                        {{ $b->status === 'غير صالحة' ? 'bg-rose-500' : '' }}
                      "></span>
                      {{ $b->status }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

</div>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    // Animate occupancy bars with staggered delay
    requestAnimationFrame(()=>{
      document.querySelectorAll('.occupancy-bar').forEach((el, index) => {
        setTimeout(() => {
          el.style.height = el.dataset.h + '%';
        }, index * 120);
      });
    });

    // Animate the QR scan gauge
    const gaugeCircle = document.getElementById('gauge-circle');
    const gaugeText = document.getElementById('gauge-text');
    if (gaugeCircle) {
      const pct = parseInt(gaugeCircle.dataset.percent, 10) || 0;
      const circumference = 477;
      setTimeout(()=>{
        gaugeCircle.style.transition = 'stroke-dashoffset 1.2s cubic-bezier(0.34, 1.56, 0.64, 1)';
        gaugeCircle.setAttribute('stroke-dashoffset', circumference - (circumference * pct / 100));
      }, 250);
      
      let cur = 0;
      const gaugeInterval = setInterval(()=>{
        cur++;
        if (gaugeText) gaugeText.textContent = cur + '%';
        if (cur >= pct) clearInterval(gaugeInterval);
      }, 16);
    }

    // Live countdown timer
    const timerEl = document.getElementById('countdown-timer');
    if (timerEl) {
      let seconds = parseInt(timerEl.dataset.seconds, 10) || 0;
      const render = () => {
        const h = String(Math.floor(seconds/3600)).padStart(2,'0');
        const m = String(Math.floor((seconds%3600)/60)).padStart(2,'0');
        const s = String(seconds%60).padStart(2,'0');
        timerEl.textContent = `${h}:${m}:${s}`;
      };
      render();
      setInterval(()=>{ if (seconds > 0) seconds--; render(); }, 1000);
    }
  });
</script>
@endpush