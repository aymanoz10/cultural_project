@extends('layouts.admin')

@section('title', 'إدارة الفعاليات')
@section('page-title', 'إدارة الفعاليات')

@php
  // جلب المراكز تلقائياً كخيار احتياطي في حال عدم إرسالها من الكنترولر
  $centers = $centers ?? \App\Models\CulturalCenter::all();
@endphp

@section('content')

<div class="space-y-6 text-slate-800 dark:text-slate-100 transition-colors duration-300">

  <!-- أدوات التحكم والبحث -->
  <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
    
    <!-- زر إضافة فعالية جديدة -->
    <a href="{{ route('admin.events.create') }}" 
       class="inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 dark:bg-gradient-to-r dark:from-amber-500 dark:to-amber-600 dark:hover:from-amber-400 dark:hover:to-amber-500 text-slate-950 font-black text-xs px-5 py-3 rounded-2xl shadow-md dark:shadow-lg transition-all duration-200 hover:scale-[1.02]">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
      إضافة فعالية جديدة
    </a>

    <!-- نموذج الفلترة والبحث -->
    <form method="GET" action="{{ route('admin.events.index') }}" class="flex flex-wrap sm:flex-nowrap items-center gap-3 flex-1 max-w-xl">
      <!-- اختيارات المراكز -->
      <select name="center_id" onchange="this.form.submit()" 
              class="w-full sm:w-auto bg-white dark:bg-[#111412] text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-white/10 rounded-xl px-3.5 py-2.5 text-xs font-semibold focus:border-emerald-500 focus:outline-none transition-colors">
        <option value="">كل المراكز الثقافية</option>
        @foreach ($centers as $center)
          <option value="{{ $center->id }}" @selected(request('center_id') == $center->id)>
            {{ $center->name }}
          </option>
        @endforeach
      </select>

      <!-- حقل البحث -->
      <div class="relative flex-1 min-w-[200px] w-full">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الفعالية..." 
               class="w-full bg-white dark:bg-[#111412] text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 pl-16 text-xs font-medium focus:border-emerald-500 focus:outline-none transition-colors">
        
        <button type="submit" 
                class="absolute left-2 top-1/2 -translate-y-1/2 text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/20 transition-all">
          بحث
        </button>
      </div>
    </form>
  </div>

  <!-- جدول الفعاليات -->
  <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md overflow-hidden transition-colors duration-300">
    <div class="overflow-x-auto">
      <table class="w-full text-right border-collapse">
        <thead>
          <tr class="border-b border-slate-200 dark:border-white/10 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
            <th class="pb-4 px-4">عنوان الفعالية</th>
            <th class="pb-4 px-4">المركز الثقافي</th>
            <th class="pb-4 px-4">تاريخ ووقت البداية</th>
            <th class="pb-4 px-4">النوع</th>
            <th class="pb-4 px-4">السعة</th>
            <th class="pb-4 px-4 text-center">الإجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-xs font-medium text-slate-700 dark:text-slate-200">
          @forelse ($events as $event)
            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors duration-150">
              
              <!-- عنوان الفعالية -->
              <td class="py-4 px-4 font-extrabold text-slate-900 dark:text-white text-sm">
                {{ $event->title }}
              </td>

              <!-- المركز الثقافي -->
              <td class="py-4 px-4 text-slate-600 dark:text-slate-300 font-medium">
                {{ $event->culturalCenter->name ?? 'غير محدد' }}
              </td>

              <!-- تاريخ ووقت البداية -->
              <td class="py-4 px-4 text-slate-500 dark:text-slate-400 font-mono text-xs dir-ltr text-right">
                {{ \Carbon\Carbon::parse($event->start_time)->format('Y-m-d — h:i A') }}
              </td>

              <!-- نوع الفعالية -->
              <td class="py-4 px-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-[#111412] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10">
                  {{ $event->type }}
                </span>
              </td>

              <!-- السعة -->
              <td class="py-4 px-4 font-extrabold text-emerald-600 dark:text-emerald-400 font-mono text-sm">
                {{ $event->capacity ?? '—' }}
              </td>

              <!-- الإجراءات -->
              <td class="py-4 px-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <!-- عرض -->
                  <a href="{{ route('admin.events.show', $event->id) }}" 
                     class="p-2 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 hover:bg-sky-500/20 border border-sky-500/20 transition-all" 
                     title="عرض الفعالية">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </a>
                  
                  <!-- تعديل -->
                  <a href="{{ route('admin.events.edit', $event->id) }}" 
                     class="p-2 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all" 
                     title="تعديل">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </a>
                  
                  <!-- حذف -->
                  <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفعالية؟');" class="inline-block m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="p-2 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 transition-all" 
                            title="حذف">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-slate-400 dark:text-slate-500 py-10 font-bold">
                لا توجد فعاليات مطابقة لمُدخلات البحث.
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

</div>

@endsection