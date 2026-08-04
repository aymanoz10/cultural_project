@extends('layouts.admin')

@section('title', 'إدارة القاعات والمرافق')
@section('page-title', 'إدارة القاعات والمرافق')

@section('content')
@php
  $totalCount = isset($venuesCount) 
    ? $venuesCount 
    : ((is_object($venuesList) && method_exists($venuesList, 'total')) 
        ? $venuesList->total() 
        : (is_countable($venuesList) ? count($venuesList) : 0));
@endphp

<div class="space-y-6">
  <!-- الهيدر وأزرار الإجراءات -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900 dark:text-white">قائمة القاعات والمرافق</h2>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">إدارة القاعات المتاحة للأنشطة والفعاليات الثقافية</p>
    </div>
    <div>
      <a href="{{ route('admin.venues.create') }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-4 py-2.5 rounded-xl text-xs transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة قاعة جديدة
      </a>
    </div>
  </div>

  <!-- إحصائيات سريعة -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="card p-5 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm flex items-center justify-between">
      <div>
        <span class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">إجمالي القاعات</span>
        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalCount) }}</span>
      </div>
      <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-500">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
      </div>
    </div>
  </div>

  <!-- جدول القاعات -->
  <div class="card bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-right text-xs">
        <thead class="bg-slate-50 dark:bg-[#111412] text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-white/10">
          <tr>
            <th class="p-4">الصورة / القاعة</th>
            <th class="p-4">المركز الثقافي</th>
            <th class="p-4">نوع المكان</th>
            <th class="p-4">السعة الاستيعابية</th>
            <th class="p-4">التجهيزات والميزات</th>
            <th class="p-4 text-center">الإجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300">
          @forelse($venuesList as $venue)
            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors" id="venue-row-{{ $venue->id }}">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/5 overflow-hidden flex-shrink-0 border border-slate-200 dark:border-white/10">
                    @if(!empty($venue->image_url))
                      <img src="{{ $venue->image_url }}" alt="{{ $venue->name }}" class="w-full h-full object-cover">
                    @elseif(!empty($venue->image))
                      <img src="{{ Storage::url($venue->image) }}" alt="{{ $venue->name }}" class="w-full h-full object-cover">
                    @else
                      <div class="w-full h-full flex items-center justify-center text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                      </div>
                    @endif
                  </div>
                  <div>
                    <span class="font-bold text-slate-900 dark:text-white block text-sm">{{ $venue->name }}</span>
                    <span class="text-[11px] text-slate-400">#{{ $venue->id }}</span>
                  </div>
                </div>
              </td>

              <td class="p-4">
                <span class="font-medium text-slate-800 dark:text-slate-200">
                  {{ $venue->culturalCenter->name ?? 'غير محدد' }}
                </span>
              </td>

              <!-- عرض اسم النوع من العلاقة مباشرة -->
              <td class="p-4">
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 inline-block">
                  {{ $venue->type->name ?? 'غير محدد' }}
                </span>
              </td>

              <td class="p-4">
                <span class="font-bold text-slate-900 dark:text-white">{{ number_format($venue->capacity) }}</span>
                <span class="text-slate-400 text-[11px]"> شخص</span>
              </td>

              <td class="p-4">
                <div class="flex flex-wrap gap-1 max-w-xs">
                  @if(!empty($venue->features) && is_array($venue->features))
                    @foreach($venue->features as $feature)
                      <span class="px-2 py-0.5 rounded-md text-[10px] bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/10">
                        {{ $feature }}
                      </span>
                    @endforeach
                  @else
                    <span class="text-slate-400 text-[11px]">-</span>
                  @endif
                </div>
              </td>

              <td class="p-4">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.venues.edit', $venue->id) }}" class="p-2 text-slate-500 hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-colors" title="تعديل">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </a>
                  <button type="button" id="delete-btn-{{ $venue->id }}" onclick="deleteVenue({{ $venue->id }})" class="p-2 text-slate-500 hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-colors" title="حذف">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-8 text-center text-slate-400">
                لا توجد قاعات مضافة حالياً.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- الترقيم (Pagination) -->
    @if(is_object($venuesList) && method_exists($venuesList, 'hasPages') && $venuesList->hasPages())
      <div class="p-4 border-t border-slate-200 dark:border-white/10">
        {{ $venuesList->links() }}
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
  async function deleteVenue(id) {
    if (!confirm('هل أنت تأكد من رغبتك في حذف هذه القاعة؟')) return;

    const btn = document.getElementById(`delete-btn-${id}`);
    if (btn) btn.style.opacity = '0.5';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    try {
      const response = await fetch(`/admin/venues/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        }
      });

      const res = await response.json();

      if (response.ok && (res.success || res.status === 'success')) {
        const row = document.getElementById(`venue-row-${id}`);
        if (row) {
          row.style.transition = 'opacity 0.3s ease';
          row.style.opacity = '0';
          setTimeout(() => row.remove(), 300);
        } else {
          window.location.reload();
        }
      } else {
        alert(res.message || 'حدث خطأ أثناء الحذف');
        if (btn) btn.style.opacity = '1';
      }
    } catch (err) {
      console.error(err);
      alert('حدث خطأ في الاتصال بالخادم');
      if (btn) btn.style.opacity = '1';
    }
  }
</script>
@endpush