@extends('layouts.admin')

@section('title', 'إضافة مركز ثقافي جديد')
@section('page-title', 'إضافة مركز ثقافي جديد')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6 md:p-8 space-y-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm">

    <!-- الهيدر -->
    <div class="flex items-center justify-between border-b pb-4 border-slate-100 dark:border-white/10">
      <h3 class="text-lg font-black text-slate-900 dark:text-white">بيانات المركز الثقافي</h3>
      <a href="{{ route('admin.cultural_centers.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 text-sm font-bold flex items-center gap-1 transition-colors">
        → العودة للقائمة
      </a>
    </div>

    <!-- أخطاء التحقق -->
    @if($errors->any())
      <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-xs leading-relaxed space-y-1">
        @foreach($errors->all() as $error)
          <div>• {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form action="{{ route('admin.cultural_centers.store') }}" method="POST" class="space-y-5">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- اسم المركز -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">اسم المركز <span class="text-rose-500">*</span></label>
          <input type="text" name="name" value="{{ old('name') }}" placeholder="مثال: المركز الثقافي بدمشق" required
                 class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <!-- الموقع -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">الموقع / العنوان <span class="text-rose-500">*</span></label>
          <input type="text" name="location" value="{{ old('location') }}" placeholder="مثال: دمشق - المزة" required
                 class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
      </div>

      <!-- رابط الموقع على الخريطة -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">رابط الموقع على الخريطة (اختياري)</label>
        <input type="text" name="map_location" value="{{ old('map_location') }}" placeholder="https://maps.google.com/?q=..."
               class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
      </div>

      <!-- الوصف -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">الوصف</label>
        <textarea name="description" rows="4" placeholder="تفاصيل المركز والمعلومات العامة..."
                  class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">{{ old('description') }}</textarea>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-white/10">
        <a href="{{ route('admin.cultural_centers.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-bold text-sm transition-colors">إلغاء</a>
        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-6 py-2.5 rounded-xl text-sm transition-all shadow-sm flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <span>حفظ المركز</span>
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
