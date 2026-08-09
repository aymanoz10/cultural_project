@extends('layouts.admin')

@section('title', 'إدارة المراكز الثقافية')
@section('page-title', 'قائمة المراكز الثقافية')

@section('page-actions')
  <a href="{{ route('admin.cultural_centers.create') }}" class="btn-forest px-4 py-2.5 rounded-xl text-sm flex items-center gap-2 font-bold shadow-md">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    إضافة مركز جديد
  </a>
@endsection

@section('content')
<!-- جدول المراكز الثقافية -->
<div class="card overflow-hidden">
  <div class="table-wrap overflow-x-auto">
    <table>
      <thead>
        <tr>
          <th>اسم المركز</th>
          <th>الموقع</th>
          <th>الوصف</th>
          <th>عدد الصور</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody id="centers-table-body">
        <tr>
          <td colspan="5" class="text-center py-8 text-slate-400 font-bold">جاري تحميل البيانات...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', fetchCenters);

  async function fetchCenters() {
    try {
      const response = await fetch("{{ route('admin.cultural_centers.index') }}", { headers: { 'Accept': 'application/json' } });
      const result = await response.json();
      const centers = result.data || result;
      renderCenters(centers);
    } catch (err) { console.error(err); }
  }

  function renderCenters(centers) {
    const tbody = document.getElementById('centers-table-body');
    tbody.innerHTML = '';

    if (!centers || centers.length === 0) {
      tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-slate-400 font-bold">لا توجد مراكز ثقافية حالياً.</td></tr>`;
      return;
    }

    centers.forEach(c => {
      const photoCount = c.photos ? c.photos.length : 0;
      tbody.innerHTML += `
        <tr>
          <td class="font-extrabold text-slate-900">${c.name}</td>
          <td class="font-bold text-slate-600">${c.location}</td>
          <td class="text-xs text-slate-500 max-w-xs truncate">${c.description || '—'}</td>
          <td><span class="pill status-slate">${photoCount} صور</span></td>
          <td>
            <div class="flex items-center gap-2">
              <a href="/admin/cultural-centers/${c.id}/edit" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-600 transition" title="تعديل">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              </a>
              <button onclick="deleteCenter(${c.id})" class="p-1.5 hover:bg-rose-50 rounded-lg text-rose-600 transition" title="حذف">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
          </td>
        </tr>
      `;
    });
  }

  async function deleteCenter(id) {
    if (!confirm('هل أنت تأكد من رغبتك في حذف هذا المركز الثقافي؟')) return;
    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const res = await fetch(`/admin/cultural-centers/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (res.ok && data.success) fetchCenters();
    } catch (err) { console.error(err); }
  }
</script>
@endpush