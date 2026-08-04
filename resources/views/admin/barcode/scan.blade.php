@extends('layouts.admin')

@section('content')
<div class="p-6">
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h2 class="text-2xl font-black text-slate-800 dark:text-[#d4af37]">مسح الباركود والحضور</h2>
      <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">قم بتوجيه كاميرا الهاتف/الجهاز نحو الباركود أو ادخله يدوياً لتسجيل الحضور.</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-5xl mx-auto">
    
    {{-- قسم الماسح الضوئي عبر الكاميرا --}}
    <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-6 border border-slate-200/80 dark:border-[#d4af37]/20 shadow-sm">
      <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-forest dark:text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        مسح بالكاميرا المباشرة
      </h3>
      
      {{-- حاوية عرض كاميرا الماسح الضوئي --}}
      <div id="reader" class="overflow-hidden rounded-xl border border-slate-200 dark:border-[#d4af37]/30"></div>
    </div>

    {{-- قسم الإدخال اليدوي --}}
    <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-6 border border-slate-200/80 dark:border-[#d4af37]/20 shadow-sm flex flex-col justify-center">
      <div class="w-16 h-16 bg-forest/10 dark:bg-[#d4af37]/10 text-forest dark:text-[#d4af37] rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M4 8h4m4-4h4m-4 16h4"/>
        </svg>
      </div>
      
      <form id="barcodeForm" action="#" method="POST" class="space-y-4">
        @csrf
        <div>
          <label for="barcode" class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">كود الباركود / رقم التذكرة</label>
          <input type="text" id="barcode" name="barcode" autofocus placeholder="امسح الباركود أو اكتب الكود هنا..." 
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-[#d4af37]/30 bg-slate-50 dark:bg-[#262626] text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-forest dark:focus:ring-[#d4af37]">
        </div>
        <button type="submit" class="w-full py-3 px-4 bg-forest dark:bg-[#d4af37] text-white dark:text-[#1a1a1a] font-bold rounded-xl shadow-md transition-all">
          التحقق وتسجيل الحضور
        </button>
      </form>
    </div>

  </div>
</div>
@endsection

@push('scripts')
{{-- استدعاء مكتبة Html5Qrcode لتشغيل الكاميرا وقراءة الباركود/QR --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const barcodeInput = document.getElementById('barcode');
    const barcodeForm = document.getElementById('barcodeForm');

    function onScanSuccess(decodedText, decodedResult) {
      // وضع القراءة داخل الحقل تلقائياً عند اكتشاف الباركود عبر الكاميرا
      barcodeInput.value = decodedText;
      
      // إرسال الفورم مباشرة بعد المسح الناجح (اختياري، يمكنك إلغاؤها إذا أردت المراجعة يدوياً)
      // barcodeForm.submit();
    }

    function onScanFailure(error) {
      // يمكن تجاهل الأخطاء البسيطة المتكررة أثناء البحث عن باركود في الإطار
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
      "reader",
      { fps: 10, qrbox: { width: 250, height: 250 } },
      false
    );
    
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
  });
</script>
@endpush