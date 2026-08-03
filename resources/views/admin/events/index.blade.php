@extends('layouts.admin')

@section('title', 'إدارة الفعاليات')
@section('page-title', 'إدارة الفعاليات')

@php
  // جلب المراكز تلقائياً كخيار احتياطي في حال عدم إرسالها من الكنترولر
  $centers = $centers ?? \App\Models\CulturalCenter::all();
@endphp

@section('content')

<!-- أدوات التحكم والبحث -->
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
  <a href="{{ route('admin.events.create') }}" class="btn-forest rounded-xl px-5 py-2.5 text-sm font-bold shadow-xs">
    + إضافة فعالية جديدة
  </a>

  <!-- نموذج الفلترة والبحث -->
  <form method="GET" action="{{ route('admin.events.index') }}" class="flex flex-wrap items-center gap-3 flex-1 max-w-xl">
    <select name="center_id" onchange="this.form.submit()" class="form-input !w-auto bg-white text-sm border-slate-200">
      <option value="">كل المراكز الثقافية</option>
      @foreach ($centers as $center)
        <option value="{{ $center->id }}" @selected(request('center_id') == $center->id)>
          {{ $center->name }}
        </option>
      @endforeach
    </select>

    <div class="relative flex-1 min-w-[200px]">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الفعالية..." class="form-input bg-white w-full pr-9 border-slate-200">
      <button type="submit" class="absolute left-2 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 hover:text-slate-600 px-2 py-1">
        بحث
      </button>
    </div>
  </form>
</div>

<!-- جدول الفعاليات -->
<div class="card overflow-hidden">
  <div class="table-wrap overflow-x-auto">
    <table class="w-full text-right border-collapse">
      <thead>
        <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
          <th class="py-3.5 px-4">عنوان الفعالية</th>
          <th class="py-3.5 px-4">المركز الثقافي</th>
          <th class="py-3.5 px-4">تاريخ ووقت البداية</th>
          <th class="py-3.5 px-4">النوع</th>
          <th class="py-3.5 px-4">السعة</th>
          <th class="py-3.5 px-4 text-center">الإجراءات</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-sm">
        @forelse ($events as $event)
          <tr class="hover:bg-slate-50/50 transition-colors">
            <td class="py-3.5 px-4 font-black text-slate-800">
              {{ $event->title }}
            </td>
            <td class="py-3.5 px-4 text-slate-600 font-medium">
              {{ $event->culturalCenter->name ?? 'غير محدد' }}
            </td>
            <td class="py-3.5 px-4 text-slate-500 font-semibold dir-ltr text-right">
              {{ \Carbon\Carbon::parse($event->start_time)->format('Y-m-d — h:i A') }}
            </td>
            <td class="py-3.5 px-4">
              <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700">
                {{ $event->activityType?->title ?? 'فعالية' }}
              </span>
            </td>
            <td class="py-3.5 px-4 text-slate-600 font-bold">
              {{ $event->capacity ?? '—' }}
            </td>
            <td class="py-3.5 px-4">
              <div class="flex items-center justify-center gap-3">
                <!-- عرض -->
                <a href="{{ route('admin.events.show', $event->id) }}" class="text-sky-600 hover:text-sky-800 text-xs font-black">
                  عرض
                </a>
                
                <!-- تعديل -->
                <a href="{{ route('admin.events.edit', $event->id) }}" class="text-forest hover:underline text-xs font-black">
                  تعديل
                </a>
                
                <!-- حذف -->
                <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفعالية؟');" class="inline m-0">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-black">
                    حذف
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-slate-400 py-10 font-bold">
              لا توجد فعاليات مطابقة لمُدخلات البحث
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- الترقيم (Pagination) -->
@if (method_exists($events, 'links'))
  <div class="mt-4 px-2">
    {{ $events->withQueryString()->links() }}
  </div>
@endif

@endsection