@extends('layouts.admin')

@section('title', 'إضافة قاعة جديدة')
@section('page-title', 'إضافة قاعة جديدة')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6 md:p-8 space-y-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm">
    
    <!-- الهيدر -->
    <div class="flex items-center justify-between border-b pb-4 border-slate-100 dark:border-white/10">
      <h3 class="text-lg font-black text-slate-900 dark:text-white">بيانات القاعة الجديدة</h3>
      <a href="{{ route('admin.venues.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 text-sm font-bold flex items-center gap-1 transition-colors">
        → العودة للقائمة
      </a>
    </div>

    <!-- حاوية عرض الأخطاء -->
    <div id="error-container" class="hidden p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-xs leading-relaxed space-y-1"></div>

    <form id="create-venue-form" onsubmit="submitCreateVenue(event)" enctype="multipart/form-data" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- اختيار المركز الثقافي -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">المركز الثقافي <span class="text-rose-500">*</span></label>
          <select name="cultural_center_id" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            <option value="" disabled selected>اختر المركز الثقافي...</option>
            @foreach($culturalCenters as $center)
              <option value="{{ $center->id }}">{{ $center->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- اسم القاعة -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">اسم القاعة <span class="text-rose-500">*</span></label>
          <input type="text" name="name" placeholder="مثال: قاعة المحاضرات الرئيسية" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- نوع المكان الديناميكي -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">نوع المكان <span class="text-rose-500">*</span></label>
          <select name="venue_type_id" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            <option value="" disabled selected>اختر النوع...</option>
            @foreach($venueTypes as $venueType)
              <option value="{{ $venueType->id }}">{{ $venueType->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- السعة الاستيعابية -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">السعة الاستيعابية (عدد الأشخاص) <span class="text-rose-500">*</span></label>
          <input type="number" name="capacity" min="1" placeholder="مثال: 100" required class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
      </div>

      <!-- الميزات والتجهيزات -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">الميزات والتجهيزات (افصل بينها بفواصل)</label>
        <input type="text" id="features-input" placeholder="شاشة عرض، بروجكتور، مكيف هوائي" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
      </div>

      <!-- صورة القاعة -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">صورة القاعة (اختياري)</label>
        <input type="file" name="image" id="image-input" onchange="previewNewImage(event)" accept="image/jpeg,image/png,image/jpg,image/webp" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-2.5 text-xs">
        
        <!-- معاينة الصورة الجديدة -->
        <div id="image-preview-wrapper" class="mt-3 hidden">
          <span class="block text-[11px] text-slate-400 mb-1">معاينة الصورة المحددة:</span>
          <img id="image-preview" src="#" class="w-20 h-20 object-cover rounded-xl border border-slate-200 dark:border-white/10 shadow-sm">
        </div>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-white/10">
        <a href="{{ route('admin.venues.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-bold text-sm transition-colors">إلغاء</a>
        <button type="submit" id="submit-btn" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-6 py-2.5 rounded-xl text-sm transition-all shadow-sm flex items-center gap-2">
          <span>حفظ القاعة</span>
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

  async function submitCreateVenue(e) {
    e.preventDefault();
    const form = document.getElementById('create-venue-form');
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

    const rawFeatures = document.getElementById('features-input').value;
    if (rawFeatures.trim() !== '') {
      rawFeatures.split(/[,،]/).forEach(feat => {
        if (feat.trim() !== '') {
          formData.append('features[]', feat.trim());
        }
      });
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    try {
      const response = await fetch("{{ route('admin.venues.store') }}", {
        method: 'POST',
        headers: { 
          'X-CSRF-TOKEN': csrfToken, 
          'Accept': 'application/json' 
        },
        body: formData
      });

      const res = await response.json();
      if (response.ok && (res.success || res.data)) {
        window.location.href = "{{ route('admin.venues.index') }}";
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