@extends('layouts.admin')

@section('title', 'حجوزات القاعات')
@section('page-title', 'إدارة حجوزات القاعات')

@section('content')
@php
  $statusOptions = [
    'pending'   => 'قيد الانتظار',
    'accepted'  => 'مقبول',
    'approved'  => 'معتمد',
    'rejected'  => 'مرفوض',
    'cancelled' => 'ملغى',
  ];
  $statCards = [
    ['key' => 'total',     'label' => 'الإجمالي',      'val' => $stats['total'],     'accent' => 'text-slate-900 dark:text-white',      'ring' => 'bg-slate-400'],
    ['key' => 'pending',   'label' => 'قيد الانتظار',  'val' => $stats['pending'],   'accent' => 'text-amber-600 dark:text-amber-400',  'ring' => 'bg-amber-500'],
    ['key' => 'accepted',  'label' => 'مقبول',         'val' => $stats['accepted'],  'accent' => 'text-sky-600 dark:text-sky-400',      'ring' => 'bg-sky-500'],
    ['key' => 'approved',  'label' => 'معتمد',          'val' => $stats['approved'],  'accent' => 'text-emerald-600 dark:text-emerald-400', 'ring' => 'bg-emerald-500'],
    ['key' => 'rejected',  'label' => 'مرفوض',          'val' => $stats['rejected'],  'accent' => 'text-rose-600 dark:text-rose-400',    'ring' => 'bg-rose-500'],
    ['key' => 'cancelled', 'label' => 'ملغى',           'val' => $stats['cancelled'], 'accent' => 'text-slate-500 dark:text-slate-400',  'ring' => 'bg-slate-400'],
  ];
@endphp

<div class="space-y-6">
  <!-- الهيدر -->
  <div>
    <h2 class="section-accent text-xl font-black text-slate-900 dark:text-white">قائمة حجوزات القاعات</h2>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">مراجعة طلبات حجز القاعات وإدارة حالاتها (قبول / اعتماد / رفض / إلغاء)</p>
  </div>

  <!-- إحصائيات الحالات -->
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
    @foreach($statCards as $c)
      <a href="{{ $c['key'] === 'total' ? route('admin.venue_reservations.index') : route('admin.venue_reservations.index', ['status' => $c['key']]) }}"
         class="card bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl p-4 hover-lift {{ request('status') === $c['key'] || ($c['key']==='total' && !request('status')) ? 'ring-2 ring-forest/40 dark:ring-[#d4af37]/40' : '' }}">
        <div class="flex items-center gap-2 mb-1.5">
          <span class="w-2 h-2 rounded-full {{ $c['ring'] }}"></span>
          <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ $c['label'] }}</span>
        </div>
        <span class="text-2xl font-black tracking-tight {{ $c['accent'] }}" style="font-variant-numeric: tabular-nums;">{{ number_format($c['val']) }}</span>
      </a>
    @endforeach
  </div>

  <!-- شريط الفلترة -->
  <div class="card p-4 md:p-5 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl">
    <form method="GET" action="{{ route('admin.venue_reservations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <div class="md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">بحث</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم مقدّم الطلب أو الجهة أو الرقم الوطني..." class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs">
      </div>
      <div>
        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">الحالة</label>
        <select name="status" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs">
          <option value="">كل الحالات</option>
          @foreach($statusOptions as $val => $label)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">القاعة</label>
        <select name="venue_id" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs">
          <option value="">كل القاعات</option>
          @foreach($venues as $venue)
            <option value="{{ $venue->id }}" {{ (string) request('venue_id') === (string) $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="md:col-span-4 flex items-center gap-2 pt-1">
        <button type="submit" class="inline-flex items-center gap-2 bg-forest hover:bg-forest-600 text-white font-black px-5 py-2.5 rounded-xl text-xs transition-all shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          تطبيق الفلترة
        </button>
        @if(request()->hasAny(['search', 'status', 'venue_id']))
          <a href="{{ route('admin.venue_reservations.index') }}" class="px-4 py-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-bold text-xs transition-colors">إعادة تعيين</a>
        @endif
      </div>
    </form>
  </div>

  <!-- الجدول -->
  <div class="card table-wrap bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-right text-xs">
        <thead class="bg-slate-50 dark:bg-[#111412] text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-white/10">
          <tr>
            <th class="p-4">مقدّم الطلب</th>
            <th class="p-4">الجهة</th>
            <th class="p-4">القاعة</th>
            <th class="p-4">الفترة</th>
            <th class="p-4">الحالة</th>
            <th class="p-4 text-center">الإجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300">
          @forelse($reservations as $r)
            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-forest/10 dark:bg-[#d4af37]/10 text-forest dark:text-[#d4af37] flex items-center justify-center font-black text-sm flex-shrink-0">
                    {{ mb_substr($r->applicant_name ?: 'ـ', 0, 1) }}
                  </div>
                  <div>
                    <span class="font-bold text-slate-900 dark:text-white block">{{ $r->applicant_name ?: 'غير محدد' }}</span>
                    <span class="text-[11px] text-slate-400" style="font-variant-numeric: tabular-nums;">{{ $r->national_id_number }}</span>
                  </div>
                </div>
              </td>
              <td class="p-4 text-slate-800 dark:text-slate-200 max-w-[150px] truncate" title="{{ $r->requesting_party }}">{{ $r->requesting_party ?: '—' }}</td>
              <td class="p-4">
                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $r->venue->name ?? 'غير محدد' }}</span>
              </td>
              <td class="p-4 text-slate-500 dark:text-slate-400" style="font-variant-numeric: tabular-nums;">
                <span class="block">{{ optional($r->start_time)->format('Y-m-d H:i') }}</span>
                <span class="block text-[11px] text-slate-400">حتى {{ optional($r->end_time)->format('Y-m-d H:i') }}</span>
              </td>
              <td class="p-4">
                @include('admin.venue_reservations._badge', ['status' => $r->status])
              </td>
              <td class="p-4">
                <div class="flex items-center justify-center flex-wrap gap-1.5">
                  <a href="{{ route('admin.venue_reservations.show', $r->id) }}" class="p-2 text-slate-500 hover:text-forest dark:hover:text-emerald-400 hover:bg-emerald-500/10 rounded-lg transition-colors" title="عرض التفاصيل">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </a>
                  @include('admin.venue_reservations._actions', ['reservation' => $r])
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-8 text-center text-slate-400">لا توجد حجوزات مطابقة حالياً.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($reservations->hasPages())
      <div class="p-4 border-t border-slate-200 dark:border-white/10">
        {{ $reservations->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
