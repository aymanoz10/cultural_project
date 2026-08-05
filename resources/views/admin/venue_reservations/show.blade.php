@extends('layouts.admin')

@section('title', 'تفاصيل حجز القاعة')
@section('page-title', 'تفاصيل حجز القاعة')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <!-- الهيدر والإجراءات -->
  <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <a href="{{ route('admin.venue_reservations.index') }}" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-colors">
          ← العودة للقائمة
        </a>
        <div>
          <div class="flex items-center gap-2 mb-1">
            @include('admin.venue_reservations._badge', ['status' => $reservation->status])
            <span class="text-[11px] text-slate-400">#{{ $reservation->id }}</span>
          </div>
          <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ $reservation->applicant_name ?: 'غير محدد' }}</h2>
        </div>
      </div>
    </div>

    <!-- شريط الإجراءات (آلة الحالات) -->
    <div class="mt-5 pt-5 border-t border-slate-100 dark:border-white/10">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-slate-500 dark:text-slate-400">الإجراء المتاح:</span>
          <div class="flex items-center flex-wrap gap-2">
            @include('admin.venue_reservations._actions', ['reservation' => $reservation])
          </div>
        </div>
        @if($reservation->isTerminal())
          <span class="text-[11px] font-bold text-slate-400">لا يمكن تغيير هذه الحالة (نهائية)</span>
        @endif
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- التفاصيل الرئيسية -->
    <div class="md:col-span-2 space-y-6">
      <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl">
        <h4 class="section-accent text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-white/10 pb-3 mb-4">بيانات الطلب</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">مقدّم الطلب</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $reservation->applicant_name ?: 'غير محدد' }}</span>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">الرقم الوطني</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white" style="font-variant-numeric: tabular-nums;">{{ $reservation->national_id_number ?: '—' }}</span>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">الجهة الطالبة</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $reservation->requesting_party ?: '—' }}</span>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">نوع الفعالية</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $reservation->is_public ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-800/50' : 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800/60 dark:text-slate-300 dark:border-slate-700' }}">
              {{ $reservation->is_public ? 'عامة' : 'خاصة' }}
            </span>
          </div>
          <div class="sm:col-span-2">
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">سبب الحجز</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $reservation->reservation_reason ?: '—' }}</span>
          </div>
        </div>
      </div>

      <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl">
        <h4 class="section-accent text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">وصف الفعالية</h4>
        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $reservation->event_description ?: 'لا يوجد وصف مُضاف.' }}</p>
      </div>

      @if($reservation->notes)
        <div class="card p-6 bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-800/40 rounded-3xl">
          <h4 class="text-xs font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2">ملاحظات الإدارة</h4>
          <p class="text-sm font-medium text-amber-800 dark:text-amber-200 leading-relaxed whitespace-pre-line">{{ $reservation->notes }}</p>
        </div>
      @endif
    </div>

    <!-- الموقع والتوقيت -->
    <div class="space-y-6">
      <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl space-y-4">
        <h4 class="section-accent text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-white/10 pb-3">المكان والتوقيت</h4>
        <div>
          <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">القاعة</span>
          <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $reservation->venue->name ?? 'غير محدد' }}</span>
        </div>
        <div>
          <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">المركز الثقافي</span>
          <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $reservation->venue->culturalCenter->name ?? 'غير محدد' }}</span>
        </div>
        <hr class="border-slate-100 dark:border-white/10">
        <div>
          <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">من</span>
          <span class="text-sm font-extrabold text-slate-800 dark:text-white" style="font-variant-numeric: tabular-nums;">{{ optional($reservation->start_time)->format('Y-m-d — h:i A') }}</span>
        </div>
        <div>
          <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">إلى</span>
          <span class="text-sm font-extrabold text-slate-800 dark:text-white" style="font-variant-numeric: tabular-nums;">{{ optional($reservation->end_time)->format('Y-m-d — h:i A') }}</span>
        </div>
        <hr class="border-slate-100 dark:border-white/10">
        <div>
          <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">تاريخ الطلب</span>
          <span class="text-sm font-bold text-slate-600 dark:text-slate-300" style="font-variant-numeric: tabular-nums;">{{ optional($reservation->created_at)->format('Y-m-d H:i') }}</span>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
