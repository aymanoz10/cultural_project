@extends('layouts.admin')

@section('title', 'تفاصيل الكتاب')
@section('page-title', 'تفاصيل الكتاب')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <!-- الهيدر والإجراءات -->
  <div class="card p-6 flex flex-wrap items-center justify-between gap-4 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm">
    <div class="flex items-center gap-3">
      <a href="{{ route('admin.books.index') }}" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-colors">
        ← العودة للقائمة
      </a>
      <div>
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">{{ $book->category }}</span>
          @if($book->is_available)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/80 dark:text-emerald-400 dark:border-emerald-800/50">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>متاحة
            </span>
          @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800/80 dark:text-slate-300 dark:border-slate-700">
              <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>غير متاحة
            </span>
          @endif
        </div>
        <h2 class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ $book->title }}</h2>
      </div>
    </div>

    <div class="flex items-center gap-3">
      @if($book->hasFile())
        <a href="{{ route('admin.books.read', $book->id) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-xl text-xs font-black transition-all shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.247m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.247"/></svg>
          قراءة
        </a>
        <a href="{{ route('admin.books.download', $book->id) }}" class="inline-flex items-center gap-1.5 bg-forest hover:bg-forest-600 text-white px-4 py-2 rounded-xl text-xs font-black transition-all shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
          تحميل
        </a>
      @endif
      <a href="{{ route('admin.books.edit', $book->id) }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-4 py-2 rounded-xl text-xs font-black transition-all shadow-sm">
        تعديل البيانات
      </a>
    </div>
  </div>

  <!-- شبكة التفاصيل -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- الغلاف -->
    <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm flex flex-col items-center">
      <div class="w-40 h-56 rounded-2xl overflow-hidden border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-white/5 shadow-sm">
        @if(!empty($book->cover_image))
          <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
        @else
          <div class="w-full h-full flex items-center justify-center text-slate-400">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 19.5v-15A2.5 2.5 0 016.5 2H20v20H6.5a2.5 2.5 0 010-5H20"/></svg>
          </div>
        @endif
      </div>
      <span class="text-[11px] text-slate-400 mt-3">#{{ $book->id }}</span>
    </div>

    <!-- المعلومات -->
    <div class="md:col-span-2 space-y-6">
      <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm space-y-4">
        <h4 class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-white/10 pb-3">البيانات الأساسية</h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">المؤلف</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $book->author }}</span>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">المكتبة</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $book->library->name ?? 'غير محدد' }}</span>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">التصنيف</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $book->category }}</span>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">اللغة</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $book->language }}</span>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">عدد الصفحات</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $book->pages_count ? number_format($book->pages_count) . ' صفحة' : 'غير محدد' }}</span>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">حجم الملف</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $book->file_size ?: 'غير محدد' }}</span>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">ملف الكتاب</span>
            <span class="text-sm font-extrabold {{ $book->hasFile() ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-white' }}">{{ $book->hasFile() ? 'مرفوع (PDF)' : 'غير مرفوع' }}</span>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-0.5">عدد التحميلات</span>
            <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ number_format($book->download_count) }}</span>
          </div>
        </div>
      </div>

      <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm">
        <h4 class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">الوصف والتفاصيل</h4>
        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
          {{ $book->description ?: 'لا يوجد وصف مُضاف لهذا الكتاب.' }}
        </p>
      </div>
    </div>

  </div>

</div>
@endsection
