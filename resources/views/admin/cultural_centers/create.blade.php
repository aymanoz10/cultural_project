@extends('layouts.admin')

@section('title', 'إضافة مركز ثقافي جديد')
@section('page-title', 'إضافة مركز ثقافي جديد')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6 md:p-8 space-y-6">
    
    <!-- رأس النموذج -->
    <div class="flex items-center justify-between border-b pb-4 border-slate-700/50">
      <h3 class="text-lg font-black text-white">بيانات المركز الثقافي</h3>
      <a href="{{ route('admin.cultural_centers.index') }}" class="text-slate-400 hover:text-slate-200 text-sm font-bold flex items-center gap-1 transition-colors">
        ← العودة للقائمة
      </a>
    </div>

    <!-- نموذج الإضافة -->
    <form action="{{ route('admin.cultural_centers.store') }}" method="POST" class="space-y-5">
      @csrf
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- اسم المركز -->
        <div>
          <label class="block text-xs font-bold text-slate-300 mb-1">اسم المركز <span class="text-rose-500">*</span></label>
          <input type="text" name="name" value="{{ old('name') }}" placeholder="مثال: المركز الثقافي بدمشق" required 
                 class="form-input @error('name') border-rose-500 @enderror">
          @error('name')
            <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span>
          @enderror
        </div>

        <!-- الموقع / العنوان -->
        <div>
          <label class="block text-xs font-bold text-slate-300 mb-1">الموقع / العنوان <span class="text-rose-500">*</span></label>
          <input type="text" name="location" value="{{ old('location') }}" placeholder="مثال: دمشق - المزة" required 
                 class="form-input @error('location') border-rose-500 @enderror">
          @error('location')
            <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <!-- الوصف -->
      <div>
        <label class="block text-xs font-bold text-slate-300 mb-1">الوصف</label>
        <textarea name="description" rows="4" placeholder="تفاصيل المركز والمعلومات العامة..." 
                  class="form-input @error('description') border-rose-500 @enderror">{{ old('description') }}</textarea>
        @error('description')
          <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span>
        @enderror
      </div>

      <!-- أزرار الحفظ والإلغاء -->
      <div class="flex justify-end gap-3 pt-4 border-t border-slate-700/50">
        <a href="{{ route('admin.cultural_centers.index') }}" class="px-5 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800 font-bold text-sm transition-colors">
          إلغاء
        </a>
        <button type="submit" class="btn-forest px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-colors">
          حفظ المركز
        </button>
      </div>

    </form>
  </div>
</div>
@endsection