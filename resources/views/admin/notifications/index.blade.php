@extends('layouts.admin')

@section('title', 'الإشعارات')
@section('page-title', 'الإشعارات')

@php
  $iconPaths = [
    'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    'chat'     => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l1.3-3.9A7.9 7.9 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    'ticket'   => 'M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 012 2v3a2 2 0 000 4v3a2 2 0 01-2 2H5a2 2 0 01-2-2v-3a2 2 0 000-4V7a2 2 0 012-2z',
    'bell'     => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
  ];
@endphp

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
  <div class="flex items-center justify-between gap-4">
    <div>
      <h2 class="section-accent text-xl font-black text-slate-900 dark:text-white">كل الإشعارات</h2>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">آخر الإشعارات الواردة إلى حسابك</p>
    </div>
    <button type="button" id="page-mark-all" class="inline-flex items-center gap-2 bg-forest hover:bg-forest-600 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition-all shadow-sm">
      تعليم الكل كمقروء
    </button>
  </div>

  <div class="card bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden divide-y divide-slate-100 dark:divide-white/5">
    @forelse($notifications as $n)
      @php
        $d = is_array($n->data) ? $n->data : (json_decode($n->data, true) ?: []);
        $icon = $iconPaths[$d['icon'] ?? 'bell'] ?? $iconPaths['bell'];
        $url = $d['action_url'] ?? '#';
        $unread = $n->read_at === null;
      @endphp
      <a href="{{ $url }}" class="flex items-start gap-3 px-5 py-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors {{ $unread ? 'bg-emerald-50/40 dark:bg-emerald-950/10' : '' }}">
        <span class="flex-shrink-0 w-10 h-10 rounded-xl bg-forest/10 dark:bg-[#d4af37]/10 text-forest dark:text-[#d4af37] flex items-center justify-center">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
        </span>
        <span class="flex-1 min-w-0">
          <span class="block text-sm font-black text-slate-800 dark:text-white">{{ $d['title'] ?? 'إشعار' }}</span>
          <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $d['body'] ?? '' }}</span>
          <span class="block text-[11px] text-slate-400 mt-1">{{ $n->created_at?->locale('ar')->diffForHumans() }}</span>
        </span>
        @if($unread)
          <span class="flex-shrink-0 mt-1.5 w-2.5 h-2.5 rounded-full bg-rose-500"></span>
        @endif
      </a>
    @empty
      <div class="p-12 text-center text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <p class="text-sm font-bold">لا توجد إشعارات بعد</p>
      </div>
    @endforelse
  </div>

  @if($notifications->hasPages())
    <div>{{ $notifications->links() }}</div>
  @endif
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('page-mark-all')?.addEventListener('click', async () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    await fetch('/admin/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } }).catch(() => {});
    window.location.reload();
  });
</script>
@endpush
