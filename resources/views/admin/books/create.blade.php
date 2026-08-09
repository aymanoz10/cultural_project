@extends('layouts.admin')

@section('title', 'إضافة كتاب جديد')
@section('page-title', 'إضافة كتاب جديد')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6 md:p-8 space-y-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm">

    <!-- الهيدر -->
    <div class="flex items-center justify-between border-b pb-4 border-slate-100 dark:border-white/10">
      <h3 class="text-lg font-black text-slate-900 dark:text-white">بيانات الكتاب الجديد</h3>
      <a href="{{ route('admin.books.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 text-sm font-bold flex items-center gap-1 transition-colors">
        → العودة للقائمة
      </a>
    </div>

    <!-- حاوية عرض الأخطاء -->
    <div id="error-container" class="hidden p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-xs leading-relaxed space-y-1"></div>

    <form id="create-book-form" onsubmit="submitCreateBook(event)" enctype="multipart/form-data" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- المكتبة -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">المكتبة <span class="text-rose-500">*</span></label>
          <select name="library_id" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            <option value="" disabled selected>اختر المكتبة...</option>
            @foreach($libraries as $library)
              <option value="{{ $library->id }}">{{ $library->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- عنوان الكتاب -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">عنوان الكتاب <span class="text-rose-500">*</span></label>
          <input type="text" name="title" placeholder="مثال: مقدمة ابن خلدون" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- المؤلف -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">المؤلف <span class="text-rose-500">*</span></label>
          <input type="text" name="author" placeholder="مثال: عبد الرحمن بن خلدون" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <!-- التصنيف -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">التصنيف <span class="text-rose-500">*</span></label>
          <input type="text" name="category" placeholder="مثال: تاريخ" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- اللغة -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">اللغة <span class="text-rose-500">*</span></label>
          <input type="text" name="language" value="العربية" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <!-- عدد الصفحات -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">عدد الصفحات</label>
          <input type="number" name="pages_count" min="1" placeholder="مثال: 320" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
      </div>

      <!-- الوصف -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">الوصف</label>
        <textarea name="description" rows="4" placeholder="نبذة مختصرة عن الكتاب..." class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
      </div>

      <!-- صورة الغلاف -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">صورة الغلاف (اختياري)</label>
        <input type="file" name="cover_image" id="image-input" onchange="previewNewImage(event)" accept="image/jpeg,image/png,image/jpg,image/webp" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-2.5 text-xs">

        <!-- معاينة صورة الغلاف -->
        <div id="image-preview-wrapper" class="mt-3 hidden">
          <span class="block text-[11px] text-slate-400 mb-1">معاينة الغلاف المحدد:</span>
          <img id="image-preview" src="#" class="w-20 h-28 object-cover rounded-xl border border-slate-200 dark:border-white/10 shadow-sm">
        </div>
      </div>

      <!-- ملف الكتاب (PDF) -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">ملف الكتاب (PDF)</label>
        <input type="file" name="file" accept="application/pdf" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-2.5 text-xs">
        <p class="text-[11px] text-slate-400 mt-1">صيغة PDF فقط · الحد الأقصى 50 ميجابايت · يُحسب الحجم تلقائياً</p>
      </div>

      <!-- حالة التوفر -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">حالة التوفر</label>
        <label class="inline-flex items-center gap-3 cursor-pointer select-none">
          <span class="relative">
            <input type="checkbox" name="is_available" value="1" checked class="sr-only peer">
            <span class="block w-11 h-6 rounded-full bg-slate-300 dark:bg-white/10 peer-checked:bg-emerald-500 transition-colors"></span>
            <span class="absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:-translate-x-5"></span>
          </span>
          <span class="text-xs font-bold text-slate-600 dark:text-slate-300">الكتاب متاح للاستعارة / التصفح</span>
        </label>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-white/10">
        <a href="{{ route('admin.books.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-bold text-sm transition-colors">إلغاء</a>
        <button type="submit" id="submit-btn" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-6 py-2.5 rounded-xl text-sm transition-all shadow-sm flex items-center gap-2">
          <span>حفظ الكتاب</span>
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function previewNewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
      const output = document.getElementById('image-preview');
      output.src = reader.result;
      document.getElementById('image-preview-wrapper').classList.remove('hidden');
    };
    if (event.target.files[0]) {
      reader.readAsDataURL(event.target.files[0]);
    }
  }

  async function submitCreateBook(e) {
    e.preventDefault();
    const form = document.getElementById('create-book-form');
    const submitBtn = document.getElementById('submit-btn');
    const errorContainer = document.getElementById('error-container');

    errorContainer.classList.add('hidden');
    errorContainer.innerHTML = '';
    submitBtn.disabled = true;
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = `
      <svg class="animate-spin h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span>جاري الحفظ...</span>
    `;

    const formData = new FormData(form);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    try {
      const response = await fetch("{{ route('admin.books.store', [], false) }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: formData
      });

      // رمز 413/419 = تجاوز حجم الرفع المسموح على الخادم أو انتهاء الجلسة (استجابة ليست JSON)
      if (response.status === 413 || response.status === 419) {
        errorContainer.innerHTML = '<div>• تعذّر الرفع: حجم ملف الـPDF أكبر من الحد المسموح على الخادم، أو انتهت صلاحية الجلسة. أعد تحميل الصفحة وجرّب ملفاً أصغر.</div>';
        errorContainer.classList.remove('hidden');
        return;
      }

      let res;
      try {
        res = await response.json();
      } catch (parseErr) {
        errorContainer.innerHTML = `<div>• استجابة غير متوقعة من الخادم (رمز ${response.status}). حدّث الصفحة وحاول مجدداً.</div>`;
        errorContainer.classList.remove('hidden');
        return;
      }

      if (response.ok && (res.success || res.data)) {
        window.location.href = "{{ route('admin.books.index', [], false) }}";
      } else {
        let errorsList = [];
        if (res.errors) {
          errorsList = Object.values(res.errors).flat();
        } else if (res.message) {
          errorsList.push(res.message);
        } else {
          errorsList.push('حدث خطأ غير متوقع أثناء الحفظ');
        }

        errorContainer.innerHTML = errorsList.map(err => `<div>• ${err}</div>`).join('');
        errorContainer.classList.remove('hidden');
      }
    } catch (err) {
      console.error(err);
      errorContainer.innerHTML = '<div>• حدث خطأ في الاتصال بالخادم، يرجى المحاولة لاحقاً</div>';
      errorContainer.classList.remove('hidden');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    }
  }
</script>
@endpush
