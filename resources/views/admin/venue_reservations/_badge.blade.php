@php
  $vrStatusMeta = [
    'pending'   => ['label' => 'قيد الانتظار', 'cls' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/80 dark:text-amber-400 dark:border-amber-800/50', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
    'accepted'  => ['label' => 'مقبول',        'cls' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/80 dark:text-sky-400 dark:border-sky-800/50', 'dot' => 'bg-sky-500 dark:bg-sky-400'],
    'approved'  => ['label' => 'معتمد',         'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/80 dark:text-emerald-400 dark:border-emerald-800/50', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
    'rejected'  => ['label' => 'مرفوض',         'cls' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/80 dark:text-rose-400 dark:border-rose-800/50', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
    'cancelled' => ['label' => 'ملغى',          'cls' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800/80 dark:text-slate-300 dark:border-slate-700', 'dot' => 'bg-slate-500 dark:bg-slate-400'],
  ];
  $meta = $vrStatusMeta[$status] ?? ['label' => $status, 'cls' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'];
@endphp
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $meta['cls'] }}">
  <span class="w-1.5 h-1.5 rounded-full {{ $meta['dot'] }}"></span>
  {{ $meta['label'] }}
</span>
