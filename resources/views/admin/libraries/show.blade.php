@extends('layouts.admin')

@section('title', 'تفاصيل المكتبة')
@section('page-title', 'تفاصيل المكتبة')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

  <!-- الهيدر والإجراءات -->
  <div class="card p-6 flex flex-wrap items-center justify-between gap-4 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm">
    <div class="flex items-center gap-3">
      <a href="{{ route('admin.libraries.index') }}" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-colors">
        ← العودة للقائمة
      </a>
      <div>
        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block">{{ $library->culturalCenter->name ?? 'غير محدد' }}</span>
        <h2 class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ $library->name }}</h2>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.books.create') }}" class="bg-forest hover:bg-forest-600 text-white px-4 py-2 rounded-xl text-xs font-black transition-all shadow-sm">
        إضافة كتاب
      </a>
      <a href="{{ route('admin.libraries.edit', $library->id) }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-4 py-2 rounded-xl text-xs font-black transition-all shadow-sm">
        تعديل المكتبة
      </a>
    </div>
  </div>

  <!-- شبكة التفاصيل -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- الصورة والمعلومات -->
    <div class="space-y-6">
      <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm flex flex-col items-center">
        <div class="w-40 h-40 rounded-2xl overflow-hidden border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-white/5 shadow-sm">
          @if(!empty($library->image))
            <img src="{{ Storage::url($library->image) }}" alt="{{ $library->name }}" class="w-full h-full object-cover">
          @else
            <div class="w-full h-full flex items-center justify-center text-slate-400">
              <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
          @endif
        </div>
        <span class="text-[11px] text-slate-400 mt-3">#{{ $library->id }}</span>
      </div>

      <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm space-y-4">
        <div>
          <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">المركز الثقافي</span>
          <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $library->culturalCenter->name ?? 'غير محدد' }}</span>
        </div>
        <div>
          <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">عدد الكتب</span>
          <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ number_format($library->books->count()) }} كتاب</span>
        </div>
      </div>
    </div>

    <!-- قائمة كتب المكتبة -->
    <div class="md:col-span-2">
      <div class="card bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-white/10">
          <h4 class="text-sm font-black text-slate-900 dark:text-white">كتب هذه المكتبة</h4>
          <a href="{{ route('admin.books.index', ['library_id' => $library->id]) }}" class="text-[11px] font-bold text-forest dark:text-emerald-400 hover:underline">عرض في وحدة الكتب</a>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-right text-xs">
            <thead class="bg-slate-50 dark:bg-[#111412] text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-white/10">
              <tr>
                <th class="p-4">العنوان</th>
                <th class="p-4">المؤلف</th>
                <th class="p-4">التصنيف</th>
                <th class="p-4">الحالة</th>
                <th class="p-4 text-center">عرض</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300">
              @forelse($library->books as $book)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                  <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $book->title }}</td>
                  <td class="p-4">{{ $book->author }}</td>
                  <td class="p-4">
                    <span class="px-2 py-0.5 rounded-md text-[10px] bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">{{ $book->category }}</span>
                  </td>
                  <td class="p-4">
                    @if($book->is_available)
                      <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">متاحة</span>
                    @else
                      <span class="text-[11px] font-bold text-slate-400">غير متاحة</span>
                    @endif
                  </td>
                  <td class="p-4 text-center">
                    <a href="{{ route('admin.books.show', $book->id) }}" class="p-2 inline-flex text-slate-500 hover:text-forest dark:hover:text-emerald-400 hover:bg-emerald-500/10 rounded-lg transition-colors" title="عرض">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="p-8 text-center text-slate-400">لا توجد كتب في هذه المكتبة بعد.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

</div>
@endsection
