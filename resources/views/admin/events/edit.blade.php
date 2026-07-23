@extends('layouts.admin')

@section('title', 'تعديل الفعالية')
@section('page-title', 'تعديل الفعالية')

@php
  $types = \App\Models\Activity::TYPES ?? ['محاضرة', 'ورشة عمل', 'حفل موسيقي', 'معرض', 'عرض مسرحي'];
  $centers = $centers ?? collect([
      (object)['id' => 1, 'name' => 'مركز أدهم إسماعيل للفنون'],
      (object)['id' => 2, 'name' => 'دار الأسد للثقافة والفنون'],
      (object)['id' => 3, 'name' => 'مركز الميدان الثقافي'],
      (object)['id' => 4, 'name' => 'مركز كفرسوسة الثقافي'],
  ]);
  $halls = $halls ?? collect([]);
  $theaters = $theaters ?? collect([]);
@endphp

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6 md:p-8 space-y-6">
    <div class="flex items-center justify-between border-b pb-4 border-slate-100">
      <h3 class="text-lg font-black text-slate-800">تعديل بيانات الفعالية: {{ $activity->title ?? $event->title }}</h3>
      <a href="{{ route('admin.events.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">
        ← العودة لقائمة الفعاليات
      </a>
    </div>

    <form method="POST" action="{{ route('admin.events.update', $activity->id ?? $event->id) }}" enctype="multipart/form-data" class="space-y-5">
      @csrf
      @method('PUT')

      <!-- عنوان الفعالية -->
      <div>
        <label class="text-xs font-bold text-slate-700 mb-1 block">عنوان الفعالية <span class="text-rose-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $activity->title ?? $event->title) }}" placeholder="مثال: أمسية موسيقية" required class="form-input">
        @error('title') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- المركز الثقافي -->
        <div>
          <label class="text-xs font-bold text-slate-700 mb-1 block">المركز الثقافي <span class="text-rose-500">*</span></label>
          <select name="cultural_center_id" required class="form-input bg-white">
            <option value="" disabled>اختر المركز الثقافي</option>
            @foreach ($centers as $center)
              <option value="{{ $center->id }}" @selected(old('cultural_center_id', $activity->cultural_center_id ?? $event->cultural_center_id) == $center->id)>
                {{ $center->name }}
              </option>
            @endforeach
          </select>
          @error('cultural_center_id') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
        </div>

        <!-- نوع الفعالية -->
        <div>
          <label class="text-xs font-bold text-slate-700 mb-1 block">نوع الفعالية <span class="text-rose-500">*</span></label>
          <select name="type" required class="form-input bg-white">
            <option value="" disabled>اختر نوع الفعالية</option>
            @foreach ($types as $type)
              <option value="{{ $type }}" @selected(old('type', $activity->type ?? $event->type) == $type)>
                {{ $type }}
              </option>
            @endforeach
          </select>
          @error('type') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
        </div>
      </div>

      <!-- اسم المحاضر / المنظم -->
      <div>
        <label class="text-xs font-bold text-slate-700 mb-1 block">اسم المحاضر / المنظم</label>
        <input type="text" name="host_name" value="{{ old('host_name', $activity->host_name ?? $event->host_name) }}" placeholder="مثال: د. أحمد خالد" class="form-input">
        @error('host_name') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>

      <!-- توقيت البداية والنهاية -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-xs font-bold text-slate-700 mb-1 block">وقت وتاريخ البداية <span class="text-rose-500">*</span></label>
          <input type="datetime-local" name="start_time" value="{{ old('start_time', isset($activity->start_time) ? \Carbon\Carbon::parse($activity->start_time)->format('Y-m-d\TH:i') : '') }}" required class="form-input">
          @error('start_time') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="text-xs font-bold text-slate-700 mb-1 block">وقت وتاريخ النهاية <span class="text-rose-500">*</span></label>
          <input type="datetime-local" name="end_time" value="{{ old('end_time', isset($activity->end_time) ? \Carbon\Carbon::parse($activity->end_time)->format('Y-m-d\TH:i') : '') }}" required class="form-input">
          @error('end_time') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
        </div>
      </div>

      <!-- القاعة والمسرح والسعة -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="text-xs font-bold text-slate-700 mb-1 block">القاعة (اختياري)</label>
          <select name="hall_id" class="form-input bg-white">
            <option value="">بدون قاعة</option>
            @foreach ($halls as $hall)
              <option value="{{ $hall->id }}" @selected(old('hall_id', $activity->hall_id ?? null) == $hall->id)>{{ $hall->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-xs font-bold text-slate-700 mb-1 block">المسرح (اختياري)</label>
          <select name="theater_id" class="form-input bg-white">
            <option value="">بدون مسرح</option>
            @foreach ($theaters as $theater)
              <option value="{{ $theater->id }}" @selected(old('theater_id', $activity->theater_id ?? null) == $theater->id)>{{ $theater->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-xs font-bold text-slate-700 mb-1 block">السعة الاستيعابية</label>
          <input type="number" name="capacity" value="{{ old('capacity', $activity->capacity ?? $event->capacity) }}" min="1" placeholder="مثال: 150" class="form-input">
          @error('capacity') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
        </div>
      </div>

      <!-- الوصف -->
      <div>
        <label class="text-xs font-bold text-slate-700 mb-1 block">تفاصيل / وصف الفعالية</label>
        <textarea name="description" rows="4" placeholder="اكتب وصفاً شاملاً للفعالية..." class="form-input">{{ old('description', $activity->description ?? $event->description) }}</textarea>
        @error('description') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>

      <!-- صورة الفعالية -->
      <div>
        <label class="text-xs font-bold text-slate-700 mb-1 block">صورة الفعالية</label>
        @if (isset($activity->image) && $activity->image)
          <div class="mb-3 flex items-center gap-3 p-2 bg-slate-50 border rounded-xl">
            <img src="{{ asset('storage/' . $activity->image) }}" alt="الصورة الحالية" class="w-14 h-14 object-cover rounded-lg">
            <span class="text-xs font-semibold text-slate-600">الصورة الحالية مرفوعة</span>
          </div>
        @endif
        <input type="file" name="image" accept="image/*" class="form-input">
        @error('image') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>

      <!-- الأزرار -->
      <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
        <button type="submit" class="btn-forest rounded-xl px-6 py-2.5 font-bold text-sm">حفظ التعديلات</button>
        <a href="{{ route('admin.events.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl px-6 py-2.5 font-bold text-sm transition-colors">إلغاء</a>
      </div>
    </form>
  </div>
</div>
@endsection