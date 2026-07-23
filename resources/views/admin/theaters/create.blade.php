@extends('layouts.admin')

@section('title', 'إضافة مسرح جديد')
@section('page-title', 'إضافة مسرح جديد')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6 md:p-8 space-y-6">
    <div class="flex items-center justify-between border-b pb-4 border-slate-100">
      <h3 class="text-lg font-black text-slate-900">بيانات المسرح الجديد</h3>
      <a href="{{ route('admin.theaters.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-bold flex items-center gap-1">
        → العودة للقائمة
      </a>
    </div>

    <form id="create-theater-form" onsubmit="submitTheaterForm(event)" enctype="multipart/form-data" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- اختيار المركز الثقافي -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">المركز الثقافي <span class="text-rose-500">*</span></label>
          <select name="cultural_center_id" required class="form-input bg-white">
            <option value="" disabled selected>اختر المركز الثقافي...</option>
            @foreach($culturalCenters as $center)
              <option value="{{ $center->id }}">{{ $center->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- اسم المسرح -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">اسم المسرح <span class="text-rose-500">*</span></label>
          <input type="text" name="name" placeholder="مثال: مسرح الحمراء" required class="form-input">
        </div>
      </div>

      <!-- السعة الاستيعابية -->
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">السعة الاستيعابية (عدد المقاعد) <span class="text-rose-500">*</span></label>
        <input type="number" name="capacity" min="1" placeholder="مثال: 450" required class="form-input">
      </div>

      <!-- الوصف -->
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">الوصف</label>
        <textarea name="description" rows="3" placeholder="وصف وتجهيزات المسرح..." class="form-input"></textarea>
      </div>

      <!-- الميزات -->
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">الميزات (افصل بين كل ميزة بفاصلة)</label>
        <input type="text" id="features-input" placeholder="نظام صوتي, إضاءة DMX, كواليس" class="form-input">
      </div>

      <!-- صورة المسرح -->
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">صورة المسرح</label>
        <input type="file" name="image" accept="image/*" class="form-input">
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <a href="{{ route('admin.theaters.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-sm">إلغاء</a>
        <button type="submit" class="btn-forest px-6 py-2.5 rounded-xl text-sm font-bold">حفظ المسرح</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  async function submitTheaterForm(e) {
    e.preventDefault();
    const form = document.getElementById('create-theater-form');
    const formData = new FormData(form);

    // معالجة حقل الميزات وتحويله إلى مصفوفة
    const rawFeatures = document.getElementById('features-input').value;
    if (rawFeatures.trim() !== '') {
      rawFeatures.split(',').forEach(feat => {
        if (feat.trim() !== '') {
          formData.append('features[]', feat.trim());
        }
      });
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    try {
      const response = await fetch("{{ route('admin.theaters.store') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData
      });

      const res = await response.json();
      if (response.ok && res.success) {
        window.location.href = "{{ route('admin.theaters.index') }}";
      } else {
        const errorMsg = res.errors ? Object.values(res.errors).flat().join("\n") : (res.message || 'حدث خطأ أثناء الحفظ');
        alert(errorMsg);
      }
    } catch (err) {
      console.error(err);
      alert('حدث خطأ في الاتصال بالخادم');
    }
  }
</script>
@endpush