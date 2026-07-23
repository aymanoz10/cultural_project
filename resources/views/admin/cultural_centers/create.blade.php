@extends('layouts.admin')

@section('title', 'إضافة مركز ثقافي جديد')
@section('page-title', 'إضافة مركز ثقافي جديد')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6 md:p-8 space-y-6">
    <div class="flex items-center justify-between border-b pb-4 border-slate-100">
      <h3 class="text-lg font-black text-slate-900">بيانات المركز الثقافي</h3>
      <a href="{{ route('admin.cultural_centers.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-bold flex items-center gap-1">
        → العودة للقائمة
      </a>
    </div>

    <form id="create-center-form" onsubmit="submitForm(event)" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">اسم المركز <span class="text-rose-500">*</span></label>
          <input type="text" name="name" placeholder="مثال: المركز الثقافي بدمشق" required class="form-input">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">الموقع / العنوان <span class="text-rose-500">*</span></label>
          <input type="text" name="location" placeholder="مثال: دمشق - المزة" required class="form-input">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">الوصف</label>
        <textarea name="description" rows="3" placeholder="تفاصيل المركز والمعلومات العامة..." class="form-input"></textarea>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <a href="{{ route('admin.cultural_centers.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-sm">إلغاء</a>
        <button type="submit" class="btn-forest px-6 py-2.5 rounded-xl text-sm font-bold">حفظ المركز</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  async function submitForm(e) {
    e.preventDefault();
    const form = document.getElementById('create-center-form');
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    try {
      const response = await fetch("{{ route('admin.cultural_centers.store') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData
      });

      const res = await response.json();
      if (response.ok && res.success) {
        window.location.href = "{{ route('admin.cultural_centers.index') }}";
      } else {
        alert(res.message || 'حدث خطأ في البيانات المدخلة');
      }
    } catch (err) { console.error(err); }
  }
</script>
@endpush