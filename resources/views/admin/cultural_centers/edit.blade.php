@extends('layouts.admin')

@section('title', 'تعديل المركز الثقافي')
@section('page-title', 'تعديل بيانات المركز الثقافي')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
  <div class="card p-6 md:p-8 space-y-6">
    <div class="flex items-center justify-between border-b pb-4 border-slate-100">
      <h3 class="text-lg font-black text-slate-900">تعديل المركز</h3>
      <a href="{{ route('admin.cultural_centers.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-bold flex items-center gap-1">
        → العودة للقائمة
      </a>
    </div>

    <form id="edit-center-form" onsubmit="submitEditForm(event)" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">اسم المركز <span class="text-rose-500">*</span></label>
          <input type="text" name="name" value="{{ $center->name }}" required class="form-input">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">الموقع / العنوان <span class="text-rose-500">*</span></label>
          <input type="text" name="location" value="{{ $center->location }}" required class="form-input">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">الوصف</label>
        <textarea name="description" rows="3" class="form-input">{{ $center->description }}</textarea>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <a href="{{ route('admin.cultural_centers.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-sm">إلغاء</a>
        <button type="submit" class="btn-forest px-6 py-2.5 rounded-xl text-sm font-bold">تحديث البيانات</button>
      </div>
    </form>
  </div>

  <!-- قسم إدارات الصور الخاصة بالمركز -->
  <div class="card p-6 space-y-4">
    <h4 class="text-md font-bold text-slate-800">معرض الصور</h4>
    <form id="upload-photos-form" onsubmit="uploadPhotos(event)" class="flex gap-3">
      <input type="file" name="photos[]" multiple accept="image/*" required class="form-input flex-1">
      <button type="submit" class="btn-forest px-4 py-2 rounded-xl text-xs font-bold">رفع الصور</button>
    </form>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4" id="photos-grid">
      @foreach($center->photos as $photo)
        <div class="relative group rounded-xl overflow-hidden border border-slate-100">
          <img src="/storage/{{ $photo->photo }}" class="w-full h-24 object-cover">
          <button onclick="removePhoto({{ $photo->id }}, this)" class="absolute top-1 right-1 bg-rose-600 text-white p-1 rounded-lg text-xs opacity-80 hover:opacity-100">حذف</button>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

  async function submitEditForm(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('edit-center-form'));
    formData.append('_method', 'PUT');

    try {
      const response = await fetch("{{ route('admin.cultural_centers.update', $center->id) }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData
      });

      const res = await response.json();
      if (response.ok && res.success) {
        window.location.href = "{{ route('admin.cultural_centers.index') }}";
      } else { alert('حدث خطأ أثناء التحديث'); }
    } catch (err) { console.error(err); }
  }

  async function uploadPhotos(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('upload-photos-form'));
    try {
      const res = await fetch("{{ route('admin.cultural_centers.photos.store', $center->id) }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData
      });
      if (res.ok) window.location.reload();
    } catch (err) { console.error(err); }
  }

  async function removePhoto(photoId, btnElement) {
    if(!confirm('هل أنت تأكد من حذف الصورة؟')) return;
    try {
      const res = await fetch(`/admin/cultural-centers/photos/${photoId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
      });
      if(res.ok) btnElement.closest('div').remove();
    } catch (err) { console.error(err); }
  }
</script>
@endpush