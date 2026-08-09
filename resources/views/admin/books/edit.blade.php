@extends('layouts.admin')

@section('title', 'تعديل الكتاب')
@section('page-title', 'تعديل بيانات الكتاب')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6 md:p-8 space-y-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm">

    <!-- الهيدر -->
    <div class="flex items-center justify-between border-b pb-4 border-slate-100 dark:border-white/10">
      <h3 class="text-lg font-black text-slate-900 dark:text-white">تعديل الكتاب: {{ $book->title }}</h3>
      <a href="{{ route('admin.books.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 text-sm font-bold flex items-center gap-1 transition-colors">
        → العودة للقائمة
      </a>
    </div>

    <!-- حاوية عرض الأخطاء -->
    <div id="error-container" class="hidden p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-xs leading-relaxed space-y-1"></div>

    <form id="edit-book-form" onsubmit="submitEditBook(event)" enctype="multipart/form-data" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- المكتبة -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">المكتبة <span class="text-rose-500">*</span></label>
          <select name="library_id" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            @foreach($libraries as $library)
              <option value="{{ $library->id }}" {{ $book->library_id == $library->id ? 'selected' : '' }}>{{ $library->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- عنوان الكتاب -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">عنوان الكتاب <span class="text-rose-500">*</span></label>
          <input type="text" name="title" value="{{ $book->title }}" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- المؤلف -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">المؤلف <span class="text-rose-500">*</span></label>
          <input type="text" name="author" value="{{ $book->author }}" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <!-- التصنيف -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">التصنيف <span class="text-rose-500">*</span></label>
          <input type="text" name="category" value="{{ $book->category }}" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- اللغة -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">اللغة <span class="text-rose-500">*</span></label>
          <input type="text" name="language" value="{{ $book->language }}" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <!-- عدد الصفحات -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">عدد الصفحات</label>
          <input type="number" name="pages_count" min="1" value="{{ $book->pages_count }}" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
      </div>

      <!-- الوصف -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">الوصف</label>
        <textarea name="description" rows="4" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">{{ $book->description }}</textarea>
      </div>

      <!-- صورة الغلاف الحالية وتعديلها -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">تغيير صورة الغلاف (اختياري)</label>

        <div class="mb-3 flex items-center gap-3">
          @if($book->cover_image)
            <div>
              <span class="block text-[11px] text-slate-400 mb-1">الغلاف الحالي:</span>
              <img src="{{ Storage::url($book->cover_image) }}" class="w-16 h-24 object-cover rounded-xl border border-slate-200 dark:border-white/10 shadow-sm">
            </div>
          @endif

          <div id="image-preview-wrapper" class="hidden">
            <span class="block text-[11px] text-slate-400 mb-1">الغلاف الجديد:</span>
            <img id="image-preview" src="#" class="w-16 h-24 object-cover rounded-xl border border-amber-500 shadow-sm">
          </div>
        </div>

        <input type="file" name="cover_image" id="image-input" onchange="previewNewImage(event)" accept="image/jpeg,image/png,image/jpg,image/webp" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-2.5 text-xs">
      </div>

      <!-- ملف الكتاب (PDF) -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">ملف الكتاب (PDF)</label>

        @if($book->hasFile())
          <div class="mb-2 flex flex-wrap items-center gap-2 text-[11px]">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-800/50 font-bold">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
              ملف مرفوع{{ $book->file_size ? ' · '.$book->file_size : '' }}
            </span>
            <a href="{{ route('admin.books.read', $book->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/50 dark:text-sky-400 dark:border-sky-800/50 font-bold hover:opacity-80 transition-opacity">قراءة</a>
            <a href="{{ route('admin.books.download', $book->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 dark:bg-white/5 dark:text-slate-300 dark:border-white/10 font-bold hover:opacity-80 transition-opacity">تحميل</a>
          </div>
        @else
          <p class="mb-2 text-[11px] text-slate-400">لا يوجد ملف مرفوع لهذا الكتاب بعد.</p>
        @endif

        <input type="file" name="file" accept="application/pdf" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-2.5 text-xs">
        <p class="text-[11px] text-slate-400 mt-1">ارفع PDF جديداً لاستبدال الحالي · اتركه فارغاً للإبقاء عليه · الحد الأقصى 50 ميجابايت</p>
      </div>

      <!-- حالة التوفر -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">حالة التوفر</label>
        <label class="inline-flex items-center gap-3 cursor-pointer select-none">
          <span class="relative">
            <input type="checkbox" name="is_available" value="1" {{ $book->is_available ? 'checked' : '' }} class="sr-only peer">
            <span class="block w-11 h-6 rounded-full bg-slate-300 dark:bg-white/10 peer-checked:bg-emerald-500 transition-colors"></span>
            <span class="absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:-translate-x-5"></span>
          </span>
          <span class="text-xs font-bold text-slate-600 dark:text-slate-300">الكتاب متاح للاستعارة / التصفح</span>
        </label>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-white/10">
        <a href="{{ route('admin.books.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-bold text-sm transition-colors">إلغاء</a>
        <button type="submit" id="submit-btn" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-6 py-2.5 rounded-xl text-sm transition-all shadow-sm flex items-center gap-2">
          <span>تحديث البيانات</span>
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

  async function submitEditBook(e) {
    e.preventDefault();
    const form = document.getElementById('edit-book-form');
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
      <span>جاري التحديث...</span>
    `;

    const formData = new FormData(form);
    formData.append('_method', 'PUT');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    try {
      const response = await fetch("{{ route('admin.books.update', $book->id, false) }}", {
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
          errorsList.push('حدث خطأ غير متوقع أثناء التحديث');
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
