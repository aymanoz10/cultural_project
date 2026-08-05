@extends('layouts.admin')

@section('title', 'التقارير وتصدير البيانات')
@section('page-title', 'التقارير وتصدير البيانات')

@php
  $vrLabels = ['pending' => 'قيد الانتظار', 'accepted' => 'مقبول', 'approved' => 'معتمد', 'rejected' => 'مرفوض', 'cancelled' => 'ملغى'];
  $trLabels = ['CONFIRMED' => 'مؤكّد', 'WAIT_LIST' => 'قائمة انتظار', 'CANCELLED' => 'ملغى'];
  $exportParams = ['from_date' => $from->toDateString(), 'to_date' => $to->toDateString()];
  $s = $report['summary'];
@endphp

@section('content')
<div class="space-y-6">

  <!-- الهيدر والنطاق -->
  <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div>
      <h2 class="section-accent text-xl font-black text-slate-900 dark:text-white">التقارير وتصدير البيانات</h2>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
        نطاق التقرير:
        @if($is_super)
          <span class="font-bold text-forest dark:text-emerald-400">كل المراكز الثقافية</span>
        @elseif($center)
          <span class="font-bold text-forest dark:text-emerald-400">{{ $center->name }}</span>
        @else
          <span class="font-bold text-rose-600 dark:text-rose-400">حسابك غير مرتبط بمركز</span>
        @endif
        — من <span class="font-bold tabular-nums">{{ $from->format('Y-m-d') }}</span>
        إلى <span class="font-bold tabular-nums">{{ $to->format('Y-m-d') }}</span>
      </p>
    </div>

    <!-- أزرار التصدير -->
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.reports.export', array_merge($exportParams, ['format' => 'pdf'])) }}" target="_blank" rel="noopener"
         class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-black px-4 py-2.5 rounded-xl text-xs transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        تصدير PDF
      </a>
      <a href="{{ route('admin.reports.export', array_merge($exportParams, ['format' => 'word'])) }}"
         class="inline-flex items-center gap-2 bg-sky-700 hover:bg-sky-800 text-white font-black px-4 py-2.5 rounded-xl text-xs transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        تصدير Word
      </a>
    </div>
  </div>

  @if(! $is_super && ! $center)
    <div class="card p-5 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50 rounded-2xl text-rose-700 dark:text-rose-300 text-sm font-bold">
      لم يتم ربط حسابك بأي مركز ثقافي، لذا لا توجد بيانات لعرضها في التقرير. يرجى التواصل مع المشرف العام.
    </div>
  @endif

  <!-- فلترة المدة -->
  <div class="card p-4 md:p-5 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
      <div>
        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">من تاريخ</label>
        <input type="date" name="from_date" value="{{ $from->toDateString() }}" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs">
      </div>
      <div>
        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">إلى تاريخ</label>
        <input type="date" name="to_date" value="{{ $to->toDateString() }}" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs">
      </div>
      <div>
        <button type="submit" class="inline-flex items-center justify-center gap-2 w-full bg-forest hover:bg-forest-600 text-white font-black px-5 py-3 rounded-xl text-xs transition-all shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          توليد التقرير
        </button>
      </div>
    </form>
  </div>

  <!-- بطاقات الملخّص -->
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
    @php
      $cards = [
        ['label' => 'الفعاليات', 'val' => $s['activities'], 'color' => 'text-slate-900 dark:text-white', 'ring' => 'bg-amber-500'],
        ['label' => 'حجوزات القاعات', 'val' => $s['venue_reservations'], 'color' => 'text-slate-900 dark:text-white', 'ring' => 'bg-sky-500'],
        ['label' => 'حجوزات التذاكر', 'val' => $s['ticket_reservations'], 'color' => 'text-slate-900 dark:text-white', 'ring' => 'bg-emerald-500'],
        ['label' => 'المقاعد المحجوزة', 'val' => $s['ticket_seats'], 'color' => 'text-slate-900 dark:text-white', 'ring' => 'bg-emerald-400'],
        ['label' => 'كتب مضافة', 'val' => $s['books_added'], 'color' => 'text-slate-900 dark:text-white', 'ring' => 'bg-forest'],
        ['label' => $is_super ? 'المراكز' : 'المركز', 'val' => $s['centers'], 'color' => 'text-slate-900 dark:text-white', 'ring' => 'bg-slate-400'],
      ];
    @endphp
    @foreach($cards as $c)
      <div class="card bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl p-4">
        <div class="flex items-center gap-2 mb-1.5">
          <span class="w-2 h-2 rounded-full {{ $c['ring'] }}"></span>
          <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ $c['label'] }}</span>
        </div>
        <span class="text-2xl font-black tracking-tight tabular-nums {{ $c['color'] }}">{{ number_format($c['val']) }}</span>
      </div>
    @endforeach
  </div>

  <!-- التوزيعات -->
  <div class="grid grid-cols-1 {{ $is_super ? 'lg:grid-cols-3' : 'lg:grid-cols-2' }} gap-6">

    <!-- الفعاليات حسب النوع -->
    <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl">
      <h3 class="section-accent font-extrabold text-slate-900 dark:text-white text-base mb-4">الفعاليات حسب النوع</h3>
      @forelse($report['activities_by_type'] as $type => $count)
        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-white/5 last:border-0">
          <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $type }}</span>
          <span class="text-xs font-black text-forest dark:text-emerald-400 tabular-nums bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md">{{ $count }}</span>
        </div>
      @empty
        <p class="text-xs text-slate-400 py-4 text-center">لا توجد فعاليات ضمن المدة.</p>
      @endforelse
    </div>

    @if($is_super)
    <!-- الفعاليات حسب المركز -->
    <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl">
      <h3 class="section-accent font-extrabold text-slate-900 dark:text-white text-base mb-4">الفعاليات حسب المركز</h3>
      @forelse($report['activities_by_center'] as $c => $count)
        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-white/5 last:border-0">
          <span class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate max-w-[70%]">{{ $c }}</span>
          <span class="text-xs font-black text-forest dark:text-emerald-400 tabular-nums bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md">{{ $count }}</span>
        </div>
      @empty
        <p class="text-xs text-slate-400 py-4 text-center">لا توجد بيانات.</p>
      @endforelse
    </div>
    @endif

    <!-- الحجوزات حسب الحالة -->
    <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl">
      <h3 class="section-accent font-extrabold text-slate-900 dark:text-white text-base mb-4">الحجوزات حسب الحالة</h3>
      <p class="text-[11px] font-bold text-slate-400 mb-1">حجوزات القاعات</p>
      @forelse($report['venue_res_by_status'] as $status => $count)
        <div class="flex items-center justify-between py-1.5">
          <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $vrLabels[$status] ?? $status }}</span>
          <span class="text-xs font-black text-sky-700 dark:text-sky-400 tabular-nums">{{ $count }}</span>
        </div>
      @empty
        <p class="text-[11px] text-slate-400 py-1">لا توجد.</p>
      @endforelse
      <p class="text-[11px] font-bold text-slate-400 mt-3 mb-1 border-t border-slate-100 dark:border-white/5 pt-2">حجوزات التذاكر</p>
      @forelse($report['ticket_res_by_status'] as $status => $count)
        <div class="flex items-center justify-between py-1.5">
          <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $trLabels[$status] ?? $status }}</span>
          <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 tabular-nums">{{ $count }}</span>
        </div>
      @empty
        <p class="text-[11px] text-slate-400 py-1">لا توجد.</p>
      @endforelse
    </div>
  </div>

  <!-- جدول الفعاليات ضمن المدة -->
  <div class="card table-wrap bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="p-5 border-b border-slate-100 dark:border-white/10">
      <h3 class="section-accent font-extrabold text-slate-900 dark:text-white text-base">الفعاليات ضمن المدة ({{ number_format($report['activities']->count()) }})</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-right text-xs">
        <thead class="bg-slate-50 dark:bg-[#111412] text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-white/10">
          <tr>
            <th class="p-4">الفعالية</th>
            <th class="p-4">النوع</th>
            @if($is_super)<th class="p-4">المركز</th>@endif
            <th class="p-4">القاعة</th>
            <th class="p-4">التاريخ</th>
            <th class="p-4">الحالة</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300">
          @forelse($report['activities'] as $a)
            @php
              $isFinished = $a->end_time && $a->end_time->isPast();
              $isLive = $a->start_time && $a->end_time && $a->start_time->isPast() && $a->end_time->isFuture();
            @endphp
            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
              <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $a->title }}</td>
              <td class="p-4">{{ $a->activityType->title ?? '—' }}</td>
              @if($is_super)<td class="p-4">{{ $a->culturalCenter->name ?? '—' }}</td>@endif
              <td class="p-4">{{ $a->venue->name ?? '—' }}</td>
              <td class="p-4 tabular-nums text-slate-500 dark:text-slate-400">{{ optional($a->start_time)->format('Y-m-d H:i') }}</td>
              <td class="p-4">
                @if($isLive)
                  <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">جارية</span>
                @elseif($isFinished)
                  <span class="text-[11px] font-bold text-slate-500">منتهية</span>
                @else
                  <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">قادمة</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="{{ $is_super ? 6 : 5 }}" class="p-8 text-center text-slate-400">لا توجد فعاليات ضمن المدة المحددة.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- تفاصيل حجوزات القاعات --}}
  <div class="card table-wrap bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="p-5 border-b border-slate-100 dark:border-white/10">
      <h3 class="section-accent font-extrabold text-slate-900 dark:text-white text-base">تفاصيل حجوزات القاعات ({{ number_format($report['venue_reservations']->count()) }})</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-right text-xs">
        <thead class="bg-slate-50 dark:bg-[#111412] text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-white/10">
          <tr>
            <th class="p-4">مقدّم الطلب</th>
            <th class="p-4">الرقم الوطني</th>
            <th class="p-4">القاعة</th>
            @if($is_super)<th class="p-4">المركز</th>@endif
            <th class="p-4">من</th>
            <th class="p-4">إلى</th>
            <th class="p-4">الحالة</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300">
          @forelse($report['venue_reservations'] as $vr)
            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
              <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $vr->applicant_name ?: '—' }}</td>
              <td class="p-4 tabular-nums text-slate-500 dark:text-slate-400">{{ $vr->national_id_number }}</td>
              <td class="p-4">{{ $vr->venue->name ?? '—' }}</td>
              @if($is_super)<td class="p-4">{{ $vr->venue->culturalCenter->name ?? '—' }}</td>@endif
              <td class="p-4 tabular-nums text-slate-500 dark:text-slate-400">{{ optional($vr->start_time)->format('Y-m-d H:i') }}</td>
              <td class="p-4 tabular-nums text-slate-500 dark:text-slate-400">{{ optional($vr->end_time)->format('Y-m-d H:i') }}</td>
              <td class="p-4">@include('admin.venue_reservations._badge', ['status' => $vr->status])</td>
            </tr>
          @empty
            <tr><td colspan="{{ $is_super ? 7 : 6 }}" class="p-8 text-center text-slate-400">لا توجد حجوزات قاعات ضمن المدة.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- تفاصيل حجوزات التذاكر --}}
  <div class="card table-wrap bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="p-5 border-b border-slate-100 dark:border-white/10">
      <h3 class="section-accent font-extrabold text-slate-900 dark:text-white text-base">تفاصيل حجوزات التذاكر ({{ number_format($report['ticket_reservations']->count()) }})</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-right text-xs">
        <thead class="bg-slate-50 dark:bg-[#111412] text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-white/10">
          <tr>
            <th class="p-4">رقم التذكرة</th>
            <th class="p-4">المستخدم</th>
            <th class="p-4">الفعالية</th>
            <th class="p-4">المقاعد</th>
            <th class="p-4">التاريخ</th>
            <th class="p-4">الحالة</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300">
          @forelse($report['ticket_reservations'] as $tr)
            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
              <td class="p-4 font-mono text-[11px] text-slate-800 dark:text-slate-200">{{ $tr->ticket_id }}</td>
              <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $tr->user->name ?? '—' }}</td>
              <td class="p-4">{{ $tr->activity->title ?? '—' }}</td>
              <td class="p-4 tabular-nums text-center">{{ $tr->seats_count }}</td>
              <td class="p-4 tabular-nums text-slate-500 dark:text-slate-400">{{ $tr->reservation_date ?? optional($tr->created_at)->format('Y-m-d') }}</td>
              <td class="p-4 font-bold {{ $tr->status === 'CONFIRMED' ? 'text-emerald-600 dark:text-emerald-400' : ($tr->status === 'CANCELLED' ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">{{ $trLabels[$tr->status] ?? $tr->status }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="p-8 text-center text-slate-400">لا توجد حجوزات تذاكر ضمن المدة.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- تفاصيل الكتب المضافة --}}
  <div class="card table-wrap bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="p-5 border-b border-slate-100 dark:border-white/10">
      <h3 class="section-accent font-extrabold text-slate-900 dark:text-white text-base">تفاصيل الكتب المضافة ({{ number_format($report['books']->count()) }})</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-right text-xs">
        <thead class="bg-slate-50 dark:bg-[#111412] text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-white/10">
          <tr>
            <th class="p-4">العنوان</th>
            <th class="p-4">المؤلف</th>
            <th class="p-4">التصنيف</th>
            <th class="p-4">المكتبة</th>
            <th class="p-4">الحالة</th>
            <th class="p-4">أُضيف في</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300">
          @forelse($report['books'] as $bk)
            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
              <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $bk->title }}</td>
              <td class="p-4">{{ $bk->author }}</td>
              <td class="p-4">{{ $bk->category }}</td>
              <td class="p-4">{{ $bk->library->name ?? '—' }}</td>
              <td class="p-4 font-bold {{ $bk->is_available ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">{{ $bk->is_available ? 'متاحة' : 'غير متاحة' }}</td>
              <td class="p-4 tabular-nums text-slate-500 dark:text-slate-400">{{ optional($bk->created_at)->format('Y-m-d') }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="p-8 text-center text-slate-400">لا توجد كتب مضافة ضمن المدة.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- المكتبات + المراكز --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card table-wrap bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden">
      <div class="p-5 border-b border-slate-100 dark:border-white/10">
        <h3 class="section-accent font-extrabold text-slate-900 dark:text-white text-base">المكتبات ({{ number_format($report['libraries']->count()) }})</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-right text-xs">
          <thead class="bg-slate-50 dark:bg-[#111412] text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-white/10">
            <tr><th class="p-4">المكتبة</th><th class="p-4">المركز</th><th class="p-4">عدد الكتب</th></tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300">
            @forelse($report['libraries'] as $lib)
              <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $lib->name }}</td>
                <td class="p-4">{{ $lib->culturalCenter->name ?? '—' }}</td>
                <td class="p-4 tabular-nums text-center font-black text-forest dark:text-emerald-400">{{ $lib->books_count }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="p-8 text-center text-slate-400">لا توجد مكتبات ضمن النطاق.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card table-wrap bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden">
      <div class="p-5 border-b border-slate-100 dark:border-white/10">
        <h3 class="section-accent font-extrabold text-slate-900 dark:text-white text-base">المراكز الثقافية ({{ number_format($report['centers_detail']->count()) }})</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-right text-xs">
          <thead class="bg-slate-50 dark:bg-[#111412] text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-white/10">
            <tr><th class="p-4">المركز</th><th class="p-4">الموقع</th><th class="p-4">القاعات</th><th class="p-4">الفعاليات</th></tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300">
            @forelse($report['centers_detail'] as $ctr)
              <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $ctr->name }}</td>
                <td class="p-4 text-slate-500 dark:text-slate-400">{{ $ctr->location ?? '—' }}</td>
                <td class="p-4 tabular-nums text-center">{{ $ctr->venues_count }}</td>
                <td class="p-4 tabular-nums text-center">{{ $ctr->activities_count }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="p-8 text-center text-slate-400">لا توجد بيانات.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection
