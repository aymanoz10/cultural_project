@php
  $vrActionMeta = [
    'accepted'  => ['label' => 'قبول',   'cls' => 'text-sky-700 bg-sky-50 hover:bg-sky-100 border-sky-200 dark:text-sky-400 dark:bg-sky-950/50 dark:border-sky-800/50'],
    'approved'  => ['label' => 'اعتماد', 'cls' => 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200 dark:text-emerald-400 dark:bg-emerald-950/50 dark:border-emerald-800/50'],
    'rejected'  => ['label' => 'رفض',    'cls' => 'text-rose-700 bg-rose-50 hover:bg-rose-100 border-rose-200 dark:text-rose-400 dark:bg-rose-950/50 dark:border-rose-800/50'],
    'cancelled' => ['label' => 'إلغاء',  'cls' => 'text-slate-700 bg-slate-100 hover:bg-slate-200 border-slate-200 dark:text-slate-300 dark:bg-slate-800/60 dark:border-slate-700'],
  ];
@endphp
@forelse($reservation->allowedTransitions() as $t)
  <button type="button" onclick="vrAction({{ $reservation->id }}, '{{ $t }}')"
    class="px-3 py-1.5 rounded-lg text-[11px] font-bold border transition-colors {{ $vrActionMeta[$t]['cls'] ?? '' }}">
    {{ $vrActionMeta[$t]['label'] ?? $t }}
  </button>
@empty
  <span class="text-[11px] text-slate-400 font-medium">— حالة نهائية</span>
@endforelse

@once
@push('modals')
  <!-- نافذة تأكيد إجراءات حجز القاعة -->
  <div id="vr-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" data-vr-close></div>
    <div class="relative bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-2xl border border-slate-200 dark:border-white/10 w-full max-w-sm overflow-hidden animate-fade-in">
      <div class="p-5">
        <div class="flex items-start gap-3">
          <div id="vr-modal-icon" class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"></div>
          <div class="flex-1 min-w-0">
            <h3 id="vr-modal-title" class="font-black text-slate-900 dark:text-white text-base"></h3>
            <p id="vr-modal-msg" class="text-xs text-slate-500 dark:text-slate-400 mt-1"></p>
          </div>
        </div>
        <div id="vr-modal-notes-wrap" class="mt-4 hidden">
          <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">سبب / ملاحظة (اختياري)</label>
          <textarea id="vr-modal-notes" rows="2" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-2.5 text-xs focus:ring-2 focus:ring-forest focus:outline-none"></textarea>
        </div>
        <div id="vr-modal-error" class="hidden mt-3 px-3 py-2 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-[11px] font-bold text-rose-600 dark:text-rose-400"></div>
      </div>
      <div class="flex gap-2 px-5 py-4 bg-slate-50 dark:bg-white/5 border-t border-slate-100 dark:border-white/10">
        <button id="vr-modal-confirm" type="button" class="flex-1 py-2.5 rounded-xl text-xs font-black text-white transition-all shadow-sm flex items-center justify-center gap-2">تأكيد</button>
        <button type="button" data-vr-close class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">إلغاء</button>
      </div>
    </div>
  </div>
  <div id="vr-toast" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-[110] px-5 py-3 rounded-xl text-white text-xs font-black shadow-lg transition-all"></div>
@endpush

@push('scripts')
<script>
  (function () {
    const modal = document.getElementById('vr-modal');
    if (!modal) return;
    const iconBox = document.getElementById('vr-modal-icon');
    const titleEl = document.getElementById('vr-modal-title');
    const msgEl = document.getElementById('vr-modal-msg');
    const notesWrap = document.getElementById('vr-modal-notes-wrap');
    const notesEl = document.getElementById('vr-modal-notes');
    const errEl = document.getElementById('vr-modal-error');
    const confirmBtn = document.getElementById('vr-modal-confirm');
    const toast = document.getElementById('vr-toast');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const META = {
      accepted:  { label: 'قبول الحجز',   msg: 'سيتم قبول طلب الحجز مبدئياً.',        tone: 'sky',     notes: false },
      approved:  { label: 'اعتماد الحجز', msg: 'سيتم اعتماد الحجز نهائياً.',           tone: 'emerald', notes: false },
      rejected:  { label: 'رفض الحجز',    msg: 'سيتم رفض طلب الحجز. هذا الإجراء نهائي.', tone: 'rose',    notes: true  },
      cancelled: { label: 'إلغاء الحجز',  msg: 'سيتم إلغاء الحجز. هذا الإجراء نهائي.',   tone: 'slate',   notes: true  },
    };
    const TONE = {
      sky:     { chip: 'bg-sky-100 text-sky-600 dark:bg-sky-950/60 dark:text-sky-400',           btn: 'bg-sky-600 hover:bg-sky-700' },
      emerald: { chip: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400', btn: 'bg-forest hover:bg-forest-600' },
      rose:    { chip: 'bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400',        btn: 'bg-rose-600 hover:bg-rose-700' },
      slate:   { chip: 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300',       btn: 'bg-slate-700 hover:bg-slate-800' },
    };
    const ICON = '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

    let pending = null; // { id, status }

    function open() { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
    function close() { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); errEl.classList.add('hidden'); resetBtn(); }
    function resetBtn() { confirmBtn.disabled = false; confirmBtn.innerHTML = 'تأكيد'; }

    function showToast(text, ok) {
      toast.textContent = text;
      toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-[110] px-5 py-3 rounded-xl text-white text-xs font-black shadow-lg transition-all ' + (ok ? 'bg-forest' : 'bg-rose-600');
      toast.classList.remove('hidden');
      setTimeout(() => toast.classList.add('hidden'), 2600);
    }

    window.vrAction = function (id, status) {
      const m = META[status] || { label: status, msg: '', tone: 'slate', notes: false };
      pending = { id, status };
      titleEl.textContent = m.label;
      msgEl.textContent = m.msg;
      iconBox.className = 'w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0 ' + TONE[m.tone].chip;
      iconBox.innerHTML = ICON;
      confirmBtn.className = 'flex-1 py-2.5 rounded-xl text-xs font-black text-white transition-all shadow-sm flex items-center justify-center gap-2 ' + TONE[m.tone].btn;
      notesEl.value = '';
      notesWrap.classList.toggle('hidden', !m.notes);
      errEl.classList.add('hidden');
      resetBtn();
      open();
    };

    confirmBtn.addEventListener('click', async function () {
      if (!pending) return;
      confirmBtn.disabled = true;
      confirmBtn.innerHTML = '<svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"></path></svg><span>جارٍ التنفيذ...</span>';
      errEl.classList.add('hidden');
      try {
        const response = await fetch(`/admin/venue-reservations/${pending.id}/status`, {
          method: 'PUT',
          headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
          body: JSON.stringify({ status: pending.status, notes: notesEl.value || null })
        });
        const res = await response.json();
        if (response.ok && res.success) {
          close();
          showToast(res.message || 'تم تحديث الحالة بنجاح', true);
          setTimeout(() => window.location.reload(), 700);
        } else {
          const msg = res.message || (res.errors ? Object.values(res.errors).flat().join(' • ') : 'تعذّر تغيير الحالة');
          errEl.textContent = msg; errEl.classList.remove('hidden'); resetBtn();
        }
      } catch (e) {
        errEl.textContent = 'حدث خطأ في الاتصال بالخادم'; errEl.classList.remove('hidden'); resetBtn();
      }
    });

    modal.querySelectorAll('[data-vr-close]').forEach(el => el.addEventListener('click', close));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) close(); });
  })();
</script>
@endpush
@endonce
