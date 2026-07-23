@extends('layouts.admin')

@section('title', 'إدارة المسارح')
@section('page-title', 'قائمة المسارح الثقافية')

@section('page-actions')
  <a href="{{ route('admin.theaters.create') }}" class="btn-forest px-4 py-2.5 rounded-xl text-sm flex items-center gap-2 font-bold shadow-md">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    إضافة مسرح جديد
  </a>
@endsection

@section('content')
<!-- كروت الإحصائيات -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="card p-5 flex items-center gap-4">
    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h18M3 16h18"/></svg>
    </div>
    <div>
      <p class="text-xs font-bold text-slate-400">إجمالي المسارح</p>
      <h3 id="stat-total-theaters" class="text-xl font-black text-slate-800">0</h3>
    </div>
  </div>

  <div class="card p-5 flex items-center gap-4">
    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <div>
      <p class="text-xs font-bold text-slate-400">إجمالي المقاعد</p>
      <h3 id="stat-total-seats" class="text-xl font-black text-slate-800">0 مقعد</h3>
    </div>
  </div>
</div>

<!-- جدول عرض المسارح -->
<div class="card overflow-hidden">
  <div class="table-wrap overflow-x-auto">
    <table>
      <thead>
        <tr>
          <th>الصورة</th>
          <th>اسم المسرح</th>
          <th>معرف المركز</th>
          <th>السعة المقعدية</th>
          <th>الميزات</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody id="theaters-table-body">
        <tr>
          <td colspan="6" class="text-center py-8 text-slate-400 font-bold">جاري تحميل البيانات...</td>
        </tr>
      </tbody>
    </table>
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
      tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-slate-400 font-bold">لا توجد مسارح حالياً.</td></tr>`;
      return;
    }

    let totalSeats = 0;
    theaters.forEach(t => {
      totalSeats += parseInt(t.capacity || 0);
      const img = t.image ? `/storage/${t.image}` : 'https://placehold.co/80x80?text=لا+صورة';
      let features = (Array.isArray(t.features) && t.features.length) 
        ? t.features.map(f => `<span class="pill status-slate">${f}</span>`).join(' ') 
        : '—';

      tbody.innerHTML += `
        <tr>
          <td><img src="${img}" class="w-12 h-12 object-cover rounded-xl border border-slate-100"></td>
          <td class="font-extrabold text-slate-900">${t.name}</td>
          <td><span class="font-bold text-forest">#${t.cultural_center_id || '-'}</span></td>
          <td><span class="font-bold text-slate-800">${t.capacity}</span> مقعد</td>
          <td><div class="flex gap-1 flex-wrap">${features}</div></td>
          <td>
            <div class="flex items-center gap-2">
              <a href="/admin/theaters/${t.id}/edit" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-600 transition" title="تعديل">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              </a>
              <button onclick="deleteTheater(${t.id})" class="p-1.5 hover:bg-rose-50 rounded-lg text-rose-600 transition" title="حذف">
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