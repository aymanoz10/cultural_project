@extends('layouts.admin')

@section('title', 'إدارة المسارح')
@section('page-title', 'قائمة المسارح الثقافية')

@section('page-actions')
  <a href="{{ route('admin.theaters.create') }}" 
     class="inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 dark:bg-gradient-to-r dark:from-amber-500 dark:to-amber-600 dark:hover:from-amber-400 dark:hover:to-amber-500 text-slate-950 font-black text-xs px-5 py-3 rounded-2xl shadow-md dark:shadow-lg transition-all duration-200 hover:scale-[1.02]">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
    إضافة مسرح جديد
  </a>
@endsection

@section('content')
<div class="space-y-6 text-slate-800 dark:text-slate-100 transition-colors duration-300">

  <!-- 1. كروت الإحصائيات (متوافقة بالكامل مع الثيمين) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    
    <!-- Card 1: Total Theaters -->
    <div class="bg-white dark:bg-[#181C1A]/90 border border-slate-200 dark:border-white/10 rounded-3xl p-6 shadow-sm dark:shadow-xl backdrop-blur-md flex items-center gap-4 transition-colors duration-300">
      <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h18M3 16h18"/></svg>
      </div>
      <div>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400">إجمالي المسارح</p>
        <h3 id="stat-total-theaters" class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-0.5">0</h3>
      </div>
    </div>

    <!-- Card 2: Total Seats -->
    <div class="bg-white dark:bg-[#181C1A]/90 border border-slate-200 dark:border-white/10 rounded-3xl p-6 shadow-sm dark:shadow-xl backdrop-blur-md flex items-center gap-4 transition-colors duration-300">
      <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <div>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400">إجمالي المقاعد</p>
        <h3 id="stat-total-seats" class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight mt-0.5">0 مقعد</h3>
      </div>
    </div>

  </div>

  <!-- 2. جدول عرض المسارح -->
  <div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl p-6 border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl backdrop-blur-md overflow-hidden transition-colors duration-300">
    <div class="overflow-x-auto">
      <table class="w-full text-right border-collapse">
        <thead>
          <tr class="border-b border-slate-200 dark:border-white/10 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
            <th class="pb-4 px-4">الصورة</th>
            <th class="pb-4 px-4">اسم المسرح</th>
            <th class="pb-4 px-4">معرف المركز</th>
            <th class="pb-4 px-4">السعة المقعدية</th>
            <th class="pb-4 px-4">الميزات</th>
            <th class="pb-4 px-4 text-center">الإجراءات</th>
          </tr>
        </thead>
        <tbody id="theaters-table-body" class="divide-y divide-slate-100 dark:divide-white/5 text-xs font-medium text-slate-700 dark:text-slate-200">
          <tr>
            <td colspan="6" class="text-center py-8 text-slate-400 dark:text-slate-500 font-bold">جاري تحميل البيانات...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', fetchTheaters);

  async function fetchTheaters() {
    try {
      const response = await fetch("{{ route('admin.theaters.index') }}", { headers: { 'Accept': 'application/json' } });
      const result = await response.json();
      const theaters = result.data || result;
      renderTheaters(theaters);
    } catch (err) { console.error(err); }
  }

  function renderTheaters(theaters) {
    const tbody = document.getElementById('theaters-table-body');
    tbody.innerHTML = '';

    if (!theaters || theaters.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-slate-400 dark:text-slate-500 font-bold">لا توجد مسارح حالياً.</td></tr>`;
      return;
    }

    let totalSeats = 0;
    theaters.forEach(t => {
      totalSeats += parseInt(t.capacity || 0);
      const img = t.image ? `/storage/${t.image}` : 'https://placehold.co/80x80?text=لا+صورة';
      
      let features = (Array.isArray(t.features) && t.features.length) 
        ? t.features.map(f => `<span class="bg-slate-100 dark:bg-[#111412] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10 px-2.5 py-1 rounded-md text-[11px]">${f}</span>`).join(' ') 
        : '<span class="text-slate-400 dark:text-slate-600">—</span>';

      tbody.innerHTML += `
        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors duration-150">
          <td class="py-4 px-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-[#111412] border border-slate-200 dark:border-white/10 overflow-hidden flex items-center justify-center">
              <img src="${img}" class="w-full h-full object-cover">
            </div>
          </td>
          <td class="py-4 px-4 font-extrabold text-slate-900 dark:text-white text-sm">${t.name}</td>
          <td class="py-4 px-4">
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
              #${t.cultural_center_id || '-'}
            </span>
          </td>
          <td class="py-4 px-4">
            <span class="font-extrabold text-emerald-600 dark:text-emerald-400 font-mono text-sm">${t.capacity}</span> 
            <span class="text-xs text-slate-500 dark:text-slate-400 font-sans font-normal">مقعد</span>
          </td>
          <td class="py-4 px-4 text-slate-600 dark:text-slate-300">
            <div class="flex gap-1.5 flex-wrap">${features}</div>
          </td>
          <td class="py-4 px-4 text-center">
            <div class="flex items-center justify-center gap-2">
              <a href="/admin/theaters/${t.id}/edit" class="p-2 rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all" title="تعديل">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              </a>
              <button onclick="deleteTheater(${t.id})" class="p-2 rounded-xl bg-rose-500/10 text-rose-700 dark:text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 transition-all" title="حذف">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
          </td>
        </tr>
      `;
    });

    document.getElementById('stat-total-theaters').innerText = theaters.length;
    document.getElementById('stat-total-seats').innerText = totalSeats.toLocaleString() + ' مقعد';
  }

  async function deleteTheater(id) {
    if (!confirm('هل أنت تأكد من رغبتك في حذف هذا المسرح؟')) return;
    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const res = await fetch(`/admin/theaters/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (res.ok && data.success) fetchTheaters();
    } catch (err) { console.error(err); }
  }
</script>
@endpush