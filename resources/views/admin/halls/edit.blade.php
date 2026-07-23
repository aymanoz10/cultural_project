@extends('layouts.admin')

@section('title', 'تعديل القاعة')
@section('page-title', 'تعديل بيانات القاعة')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6 md:p-8 space-y-6">
    <div class="flex items-center justify-between border-b pb-4 border-slate-100">
      <h3 class="text-lg font-black text-slate-900">تعديل القاعة</h3>
      <a href="{{ route('admin.halls.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-bold flex items-center gap-1">
        → العودة للقائمة
      </a>
    </div>

    <form id="edit-hall-form" onsubmit="submitEditHall(event)" enctype="multipart/form-data" class="space-y-5">
      @csrf
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">اسم القاعة <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="{{ $hall->name ?? '' }}" required class="form-input">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">السعة الاستيعابية <span class="text-rose-500">*</span></label>
        <input type="number" name="capacity" min="1" value="{{ $hall->capacity ?? '' }}" required class="form-input">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">الميزات (افصل بينها بفواصل)</label>
        <input type="text" id="features-input" value="{{ isset($hall->features) && is_array($hall->features) ? implode(', ', $hall->features) : '' }}" class="form-input">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">تغيير صورة القاعة (اختياري)</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/jpg" class="form-input">
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <a href="{{ route('admin.halls.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-sm">إلغاء</a>
        <button type="submit" class="btn-forest px-6 py-2.5 rounded-xl text-sm font-bold">تحديث البيانات</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  async function submitEditHall(e) {
    e.preventDefault();
    const form = document.getElementById('edit-hall-form');
    const formData = new FormData(form);
    formData.append('_method', 'PUT');

    const rawFeatures = document.getElementById('features-input').value;
    if (rawFeatures.trim() !== '') {
      rawFeatures.split(',').forEach(feat => formData.append('features[]', feat.trim()));
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    try {
      const response = await fetch("{{ route('admin.halls.update', $hall->id ?? 0) }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData
      });

      const res = await response.json();
      if (response.ok && res.success) {
        window.location.href = "{{ route('admin.halls.index') }}";
      } else {
        alert(res.message || 'حدث خطأ أثناء التحديث');
      }
    } catch (err) { console.error(err); }
  }
</script>
@endpush