@extends('layouts.admin')

@section('title', 'تعديل المسرح')
@section('page-title', 'تعديل بيانات المسرح')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6 md:p-8 space-y-6">
    <div class="flex items-center justify-between border-b pb-4 border-slate-100">
      <h3 class="text-lg font-black text-slate-900">تعديل المسرح</h3>
      <a href="{{ route('admin.theaters.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-bold flex items-center gap-1">
        → العودة للقائمة
      </a>
    </div>

    <form id="edit-theater-form" onsubmit="submitEditForm(event)" enctype="multipart/form-data" class="space-y-5">
      @csrf
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">اسم المسرح <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="{{ $theater->name ?? '' }}" required class="form-input">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">السعة المقعدية <span class="text-rose-500">*</span></label>
        <input type="number" name="capacity" min="1" value="{{ $theater->capacity ?? '' }}" required class="form-input">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">الوصف</label>
        <textarea name="description" rows="3" class="form-input">{{ $theater->description ?? '' }}</textarea>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">الميزات (افصل بينها بفاصلة)</label>
        <input type="text" id="features-input" value="{{ isset($theater->features) && is_array($theater->features) ? implode(', ', $theater->features) : '' }}" class="form-input">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">تغيير صورة المسرح (اختياري)</label>
        <input type="file" name="image" accept="image/*" class="form-input">
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <a href="{{ route('admin.theaters.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-sm">إلغاء</a>
        <button type="submit" class="btn-forest px-6 py-2.5 rounded-xl text-sm font-bold">تحديث البيانات</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  async function submitEditForm(e) {
    e.preventDefault();
    const form = document.getElementById('edit-theater-form');
    const formData = new FormData(form);
    formData.append('_method', 'PUT');

    const rawFeatures = document.getElementById('features-input').value;
    if (rawFeatures.trim() !== '') {
      rawFeatures.split(',').forEach(feat => formData.append('features[]', feat.trim()));
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    try {
      const response = await fetch("{{ route('admin.theaters.update', $theater->id ?? 0) }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData
      });

      const res = await response.json();
      if (response.ok && res.success) {
        window.location.href = "{{ route('admin.theaters.index') }}";
      } else {
        alert(res.message || 'حدث خطأ أثناء التحديث');
      }
    } catch (err) { console.error(err); }
  }
</script>
@endpush