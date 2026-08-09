@extends('layouts.admin')

@section('title', 'مسح الباركود والحضور')
@section('page-title', 'مسح الباركود والحضور')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
  <div>
    <h2 class="section-accent text-xl font-black text-slate-900 dark:text-white">مسح الباركود وتسجيل الحضور</h2>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">وجّه الكاميرا نحو باركود التذكرة، أو ألصق الكود يدوياً — يُفكّ التشفير وتُسجّل الحالة من «فعّالة» إلى «مكتملة».</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- الماسح الضوئي --}}
    <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl">
      <h3 class="text-sm font-black text-slate-800 dark:text-white mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-forest dark:text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        مسح بالكاميرا المباشرة
      </h3>

      {{-- زر تشغيل الكاميرا --}}
      <button type="button" id="start-cam-btn" class="w-full mb-4 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        تشغيل / سماح الكاميرا
      </button>

      {{-- مكان عرض حالة الكاميرا --}}
      <div id="cam-status" class="text-[11px] text-slate-500 text-center mb-2 hidden"></div>

      <div id="reader" class="overflow-hidden rounded-xl border border-slate-200 dark:border-white/10 min-h-[250px] bg-slate-100 dark:bg-black/20 flex items-center justify-center">
        <span class="text-xs text-slate-400">اضغط زر تشغيل الكاميرا أعلاه</span>
      </div>
    </div>

    {{-- إدخال يدوي --}}
    <div class="card p-6 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl flex flex-col justify-center">
      <div class="w-14 h-14 bg-forest/10 dark:bg-[#d4af37]/10 text-forest dark:text-[#d4af37] rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M4 8h4m4-4h4m-4 16h4"/></svg>
      </div>
      <form id="barcodeForm" class="space-y-4">
        <div>
          <label for="barcode" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">كود الباركود / رقم التذكرة</label>
          <input type="text" id="barcode" name="barcode" autofocus autocomplete="off" placeholder="امسح الباركود أو ألصق الكود هنا..."
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#111412] text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-forest dark:focus:ring-[#d4af37]">
        </div>
        <button type="submit" id="verify-btn" class="w-full py-3 px-4 bg-forest hover:bg-forest-600 text-white font-black rounded-xl shadow-sm transition-all">
          التحقّق وتسجيل الحضور
        </button>
      </form>
    </div>
  </div>

  {{-- بطاقة النتيجة --}}
  <div id="result-card" class="hidden card rounded-2xl overflow-hidden border">
    <div id="result-banner" class="flex items-center gap-3 px-6 py-4">
      <svg id="result-icon" class="w-7 h-7 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"></svg>
      <span id="result-message" class="font-black text-base"></span>
    </div>
    <div id="result-details" class="bg-white dark:bg-[#181C1A] px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 dark:border-white/10"></div>
  </div>
</div>
@endsection

@push('scripts')
{{-- تغيير السيرفر لضمان تحميل المكتبة بدون مشاكل شبكة --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('barcode');
    const form = document.getElementById('barcodeForm');
    const card = document.getElementById('result-card');
    const banner = document.getElementById('result-banner');
    const icon = document.getElementById('result-icon');
    const msg = document.getElementById('result-message');
    const details = document.getElementById('result-details');
    const camBtn = document.getElementById('start-cam-btn');
    const camStatus = document.getElementById('cam-status');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    
    let locked = false;
    let html5QrCode = null;

    const ICONS = {
      ok: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
      warn: 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
      err: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    };
    const STYLES = {
      checked_in:  { tone: 'emerald', icon: 'ok'  },
      already_used:{ tone: 'amber',   icon: 'warn'},
      wait_list:   { tone: 'amber',   icon: 'warn'},
      cancelled:   { tone: 'rose',    icon: 'err' },
      not_found:   { tone: 'rose',    icon: 'err' },
      invalid:     { tone: 'rose',    icon: 'err' },
      error:       { tone: 'rose',    icon: 'err' },
    };
    const TONE = {
      emerald: 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/50',
      amber:   'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800/50',
      rose:    'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800/50',
    };
    const esc = (s) => (s ?? '—').toString().replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));

    function row(label, value) {
      return `<div><span class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 mb-0.5">${label}</span><span class="block text-sm font-extrabold text-slate-800 dark:text-white">${esc(value)}</span></div>`;
    }

    function renderResult(data) {
      const st = STYLES[data.result] || STYLES.error;
      card.classList.remove('hidden');
      banner.className = 'flex items-center gap-3 px-6 py-4 border-b ' + TONE[st.tone];
      icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="${ICONS[st.icon]}"/>`;
      msg.textContent = data.message || '';
      const i = data.info || {};
      if (data.info) {
        details.classList.remove('hidden');
        details.innerHTML =
          row('رقم التذكرة', i.ticket_id) +
          row('الحالة', i.status_label) +
          row('الحاجز', i.holder_name) +
          row('الفعالية', i.activity_title) +
          row('توقيت الفعالية', i.activity_time) +
          row('عدد المقاعد', i.seats_count) +
          (i.used_at ? row('وقت التسجيل السابق', i.used_at) : '');
      } else {
        details.classList.add('hidden');
      }
      card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    async function verifyCode(code) {
      code = (code || '').trim();
      if (!code || locked) return;
      
      locked = true;

      if (html5QrCode && html5QrCode.isScanning) {
        try { await html5QrCode.pause(true); } catch(e) {}
      }

      try {
        const res = await fetch("{{ route('admin.barcode.verify', [], false) }}", {
          method: 'POST',
          headers: { 
            'X-CSRF-TOKEN': csrf, 
            'Accept': 'application/json', 
            'Content-Type': 'application/json' 
          },
          body: JSON.stringify({ code })
        });

        const data = await res.json();
        renderResult(data);
      } catch (e) {
        renderResult({ success: false, result: 'error', message: 'حدث خطأ في الاتصال بالخادم' });
      } finally {
        setTimeout(async () => { 
          input.value = '';
          input.focus();
          locked = false; 
          if (html5QrCode && html5QrCode.isScanning) {
            try { html5QrCode.resume(); } catch(e) {}
          }
        }, 3000);
      }
    }

    form.addEventListener('submit', (e) => { 
      e.preventDefault(); 
      verifyCode(input.value); 
    });

    // تشغيل الكاميرا عند الضغط على الزر
    camBtn.addEventListener('click', async function () {
      if (typeof Html5Qrcode === 'undefined') {
        alert("فشل تحميل مكتبة الكاميرا. تحقّق من اتصال الإنترنت.");
        return;
      }

      camStatus.classList.remove('hidden');
      camStatus.textContent = "جاري طلب إذن الكاميرا...";

      if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("reader");
      }

      const config = { fps: 10, qrbox: { width: 220, height: 220 } };

      try {
        // تجربة الكاميرا الخلفية أولاً
        await html5QrCode.start(
          { facingMode: "environment" },
          config,
          (decodedText) => {
            input.value = decodedText;
            verifyCode(decodedText);
          }
        );
        camStatus.textContent = "الكاميرا تعمل بنجاح ✓";
        camBtn.classList.add('hidden'); // إخفاء الزر بعد التشغيل
      } catch (err) {
        console.warn("فشل الكاميرا الخلفية، جاري التجربة بالكاميرا الافتراضية...", err);
        try {
          // تجربة أي كاميرا متوفرة (مثل كاميرا اللابتوب)
          await html5QrCode.start(
            { facingMode: "user" },
            config,
            (decodedText) => {
              input.value = decodedText;
              verifyCode(decodedText);
            }
          );
          camStatus.textContent = "الكاميرا تعمل بنجاح ✓";
          camBtn.classList.add('hidden');
        } catch (err2) {
          camStatus.textContent = "تعذر تشغيل الكاميرا: " + err2;
          alert("خطأ الكاميرا: " + err2 + "\nتأكد من إعطاء الصلاحية في المتصفح ومن استخدام رابط HTTPS مشفّر.");
        }
      }
    });
  });
</script>
@endpush  