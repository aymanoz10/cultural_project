@extends('layouts.admin')

@section('title', 'تفاصيل الفعالية')
@section('page-title', 'تفاصيل الفعالية')

@php
  $activity = $activity ?? $event;
  $startTime = \Carbon\Carbon::parse($activity->start_time);
  $endTime = \Carbon\Carbon::parse($activity->end_time);
  $isFinished = $endTime->isPast();
  $isUpcoming = $startTime->isFuture();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <!-- الهيدر والإجراءات السريعة -->
  <div class="card p-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('admin.events.index') }}" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-colors">
        ← العودة للقائمة
      </a>
      <div>
        <div class="flex items-center gap-2">
          <span class="pill bg-slate-100 text-slate-700 text-xs font-bold">{{ $activity->type ?? 'فعالية' }}</span>
          @if($isFinished)
            <span class="pill status-slate text-xs">مكتملة</span>
          @elseif($isUpcoming)
            <span class="pill status-active text-xs">قادمة</span>
          @else
            <span class="pill bg-emerald-100 text-emerald-800 text-xs font-bold">جارية الآن</span>
          @endif
        </div>
        <h2 class="text-xl font-black text-slate-900 mt-1">{{ $activity->title }}</h2>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.events.edit', $activity->id) }}" class="btn-forest px-4 py-2 rounded-xl text-xs font-bold">
        تعديل البيانات
      </a>
      <form method="POST" action="{{ route('admin.events.destroy', $activity->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفعالية؟');">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 transition-colors">
          حذف الفعالية
        </button>
      </form>
    </div>
  </div>

  <!-- شبكة تفاصيل الفعالية -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    <!-- العمود الأيمن: البطاقات والمعلومات الرئيسية -->
    <div class="md:col-span-2 space-y-6">
      
      <!-- كارت الصورة والوصف -->
      <div class="card p-6 space-y-4">
        @if(!empty($activity->image))
          <div class="rounded-2xl overflow-hidden max-h-72 border border-slate-100">
            <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->title }}" class="w-full h-full object-cover">
          </div>
        @endif

        <div>
          <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">الوصف والتفاصيل</h4>
          <p class="text-sm font-medium text-slate-700 leading-relaxed whitespace-pre-line">
            {{ $activity->description ?? 'لا يوجد وصف مُضاف لهذه الفعالية.' }}
          </p>
        </div>
      </div>

      <!-- كارت التوقيت والمكان -->
      <div class="card p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
          <span class="text-xs font-bold text-slate-400 block mb-1">وقت البداية</span>
          <span class="text-sm font-black text-slate-800 dir-ltr inline-block">{{ $startTime->format('Y-m-d — h:i A') }}</span>
        </div>

        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
          <span class="text-xs font-bold text-slate-400 block mb-1">وقت النهاية</span>
          <span class="text-sm font-black text-slate-800 dir-ltr inline-block">{{ $endTime->format('Y-m-d — h:i A') }}</span>
        </div>
      </div>

    </div>

    <!-- العمود الأيسر: معلومات إضافية -->
    <div class="space-y-6">
      <div class="card p-6 space-y-4">
        <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider border-b pb-3">معلومات الموقع والمنظم</h4>

        <div>
          <span class="text-xs font-bold text-slate-400 block">المركز الثقافي</span>
          <span class="text-sm font-extrabold text-slate-800">{{ $activity->culturalCenter->name ?? $activity->center ?? 'غير محدد' }}</span>
        </div>

        @if(!empty($activity->hall))
          <div>
            <span class="text-xs font-bold text-slate-400 block">القاعة</span>
            <span class="text-sm font-extrabold text-slate-800">{{ $activity->hall->name }}</span>
          </div>
        @endif

        @if(!empty($activity->theater))
          <div>
            <span class="text-xs font-bold text-slate-400 block">المسرح</span>
            <span class="text-sm font-extrabold text-slate-800">{{ $activity->theater->name }}</span>
          </div>
        @endif

        <hr class="border-slate-100">

        <div>
          <span class="text-xs font-bold text-slate-400 block">المحاضر / المنظم</span>
          <span class="text-sm font-extrabold text-slate-800">{{ $activity->host_name ?? 'غير محدد' }}</span>
        </div>

        <div>
          <span class="text-xs font-bold text-slate-400 block">السعة الاستيعابية</span>
          <span class="text-sm font-extrabold text-slate-800">{{ $activity->capacity ?? 'غير محدودة' }} مقعد</span>
        </div>
      </div>
    </div>

  </div>

</div>
@endsection