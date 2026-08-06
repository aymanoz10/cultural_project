@extends('layouts.admin')

@section('title', 'إضافة فعالية جديدة')
@section('page-title', 'إضافة فعالية جديدة')

@php
  $types = $activityTypes ?? collect();
  $centers = $centers ?? collect([
      (object)['id'=>1,'name'=>'مركز أدهم إسماعيل للفنون'],
      (object)['id'=>2,'name'=>'دار الأسد للثقافة والفنون'],
      (object)['id'=>3,'name'=>'مركز الميدان الثقافي'],
      (object)['id'=>4,'name'=>'مركز كفرسوسة الثقافي'],
  ]);

  $halls = $halls ?? collect([]);
  $theaters = $theaters ?? collect([]);
@endphp

@section('content')

<div class="card p-6 max-w-2xl">
  <div class="flex items-center justify-between mb-5">
    <h3 class="font-extrabold text-lg text-slate-800">بيانات الفعالية الجديدة</h3>
    <a href="{{ route('admin.events.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">← العودة لقائمة الفعاليات</a>
  </div>

  <!-- تم إضافة enctype="multipart/form-data" لتمكين رفع الصور -->
  <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <!-- عنوان الفعالية -->
    <div>
      <label class="text-xs font-bold text-slate-500 mb-1 block">عنوان الفعالية <span class="text-rose-500">*</span></label>
      <input type="text" name="title" value="{{ old('title') }}" placeholder="مثال: أمسية موسيقية" required>
      @error('title') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
      <!-- المركز الثقافي (تعديل الاسم إلى cultural_center_id) -->
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">المركز الثقافي <span class="text-rose-500">*</span></label>
        <select name="cultural_center_id" required>
          <option value="" disabled selected>اختر المركز الثقافي</option>
          @foreach ($centers as $center)
            <option value="{{ $center->id }}" @selected(old('cultural_center_id') == $center->id)>{{ $center->name }}</option>
          @endforeach
        </select>
        @error('cultural_center_id') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>

      <!-- نوع الفعالية (مطلوب في الكنترولر) -->
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">نوع الفعالية <span class="text-rose-500">*</span></label>
        <select name="activity_type_id" required>
          <option value="" disabled selected>اختر النوع</option>
          @foreach ($activityTypes as $type)
            <option value="{{ $type->id }}" @selected(old('activity_type_id') == $type->id)>{{ $type->title }}</option>
          @endforeach
        </select>
        @error('activity_type_id') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>
    </div>

    <!-- اسم المحاضر / المنظم -->
    <div>
      <label class="text-xs font-bold text-slate-500 mb-1 block">اسم المحاضر / المنظم</label>
      <input type="text" name="presenter_name" value="{{ old('presenter_name') }}" placeholder="مثال: د. أحمد خالد">
      @error('presenter_name') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
    </div>

    <!-- التوقيت والتاريخ (تنسيق start_time و end_time المطابق للـ Validation) -->
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">وقت وتاريخ البداية <span class="text-rose-500">*</span></label>
        <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" required>
        @error('start_time') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">وقت وتاريخ النهاية <span class="text-rose-500">*</span></label>
        <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" required>
        @error('end_time') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>
    </div>

    <!-- القاعة، المسرح، والسعة الكلية -->
    <div class="grid grid-cols-3 gap-3">
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">القاعة (اختياري)</label>
        <select name="hall_id">
          <option value="">بدون قاعة</option>
          @foreach ($halls as $hall)
            <option value="{{ $hall->id }}" @selected(old('hall_id') == $hall->id)>{{ $hall->name }}</option>
          @endforeach
        </select>
        @error('hall_id') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">المسرح (اختياري)</label>
        <select name="theater_id">
          <option value="">بدون مسرح</option>
          @foreach ($theaters as $theater)
            <option value="{{ $theater->id }}" @selected(old('theater_id') == $theater->id)>{{ $theater->name }}</option>
          @endforeach
        </select>
        @error('theater_id') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">السعة الكلية</label>
        <input type="number" name="capacity" value="{{ old('capacity') }}" min="1" placeholder="100">
        @error('capacity') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>
    </div>

    <!-- الوصف -->
    <div>
      <label class="text-xs font-bold text-slate-500 mb-1 block">وصف الفعالية</label>
      <textarea name="description" rows="3" placeholder="أدخل تفاصيل ووصف الفعالية...">{{ old('description') }}</textarea>
      @error('description') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
    </div>

    <!-- صورة الفعالية -->
    <div>
      <label class="text-xs font-bold text-slate-500 mb-1 block">صورة الفعالية</label>
      <input type="file" name="image" accept="image/*">
      @error('image') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
    </div>

    <!-- أزرار الإجراءات -->
    <div class="flex gap-3 pt-2">
      <button type="submit" class="btn-forest rounded-xl px-6 py-2.5 font-bold text-sm">حفظ الفعالية</button>
      <a href="{{ route('admin.events.index') }}" class="bg-slate-100 text-slate-600 rounded-xl px-6 py-2.5 font-bold text-sm hover:bg-slate-200 transition-colors">إلغاء</a>
    </div>
  </form>
</div>

@endsection