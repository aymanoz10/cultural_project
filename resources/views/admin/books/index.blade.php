@extends('layouts.admin')

@section('title', 'إدارة الكتب')
@section('page-title', 'إدارة الكتب')

@section('page-actions')
  <a href="{{ route('admin.books.create') }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-4 py-2.5 rounded-xl text-xs transition-all shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    إضافة كتاب
  </a>
@endsection

@section('content')
<div class="space-y-6">
  <!-- الهيدر -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h2 class="section-accent text-xl font-black text-slate-900 dark:text-white">قائمة الكتب</h2>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">إدارة الكتب المتاحة في مكتبات المراكز الثقافية</p>
    </div>
    <span class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#232925] px-3.5 py-2 rounded-full border border-slate-200 dark:border-white/10 self-start">
      إجمالي: {{ number_format($books->total()) }} كتاب
    </span>
  </div>

  <!-- شريط البحث والفلترة -->
  <div class="card p-4 md:p-5 bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm">
    <form method="GET" action="{{ route('admin.books.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <div class="md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">بحث</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالعنوان أو المؤلف أو التصنيف..." class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">التصنيف</label>
        <select name="category" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
          <option value="">كل التصنيفات</option>
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">المكتبة</label>
        <select name="library_id" class="form-input w-full bg-slate-50 dark:bg-[#111412] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
          <option value="">كل المكتبات</option>
          @foreach($libraries as $library)
            <option value="{{ $library->id }}" {{ (string) request('library_id') === (string) $library->id ? 'selected' : '' }}>{{ $library->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-4 flex items-center gap-2 pt-1">
        <button type="submit" class="inline-flex items-center gap-2 bg-forest hover:bg-forest-600 text-white font-black px-5 py-2.5 rounded-xl text-xs transition-all shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          تطبيق الفلترة
        </button>
        @if(request()->hasAny(['search', 'category', 'library_id']))
          <a href="{{ route('admin.books.index') }}" class="px-4 py-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-bold text-xs transition-colors">إعادة تعيين</a>
        @endif
      </div>
    </form>
  </div>

  <!-- جدول الكتب -->
  <div class="card table-wrap bg-white dark:bg-[#181C1A] border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-right text-xs">
        <thead class="bg-slate-50 dark:bg-[#111412] text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-white/10">
          <tr>
            <th class="p-4">الكتاب</th>
            <th class="p-4">المؤلف</th>
            <th class="p-4">التصنيف</th>
            <th class="p-4">المكتبة</th>
            <th class="p-4">الحالة</th>
            <th class="p-4 text-center">الإجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300">
          @forelse($books as $book)
            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors" id="book-row-{{ $book->id }}">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-11 h-14 rounded-lg bg-slate-100 dark:bg-white/5 overflow-hidden flex-shrink-0 border border-slate-200 dark:border-white/10">
                    @if(!empty($book->cover_image))
                      <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                      <div class="w-full h-full flex items-center justify-center text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 19.5v-15A2.5 2.5 0 016.5 2H20v20H6.5a2.5 2.5 0 010-5H20"/></svg>
                      </div>
                    @endif
                  </div>
                  <div>
                    <span class="font-bold text-slate-900 dark:text-white block text-sm">{{ $book->title }}</span>
                    <span class="text-[11px] text-slate-400">#{{ $book->id }}</span>
                  </div>
                </div>
              </td>

              <td class="p-4">
                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $book->author }}</span>
              </td>

              <td class="p-4">
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 inline-block">
                  {{ $book->category }}
                </span>
              </td>

              <td class="p-4">
                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $book->library->name ?? 'غير محدد' }}</span>
              </td>

              <td class="p-4">
                @if($book->is_available)
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/80 dark:text-emerald-400 dark:border-emerald-800/50">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                    متاحة
                  </span>
                @else
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800/80 dark:text-slate-300 dark:border-slate-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    غير متاحة
                  </span>
                @endif
              </td>

              <td class="p-4">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.books.show', $book->id) }}" class="p-2 text-slate-500 hover:text-forest dark:hover:text-emerald-400 hover:bg-emerald-500/10 rounded-lg transition-colors" title="عرض">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </a>
                  <a href="{{ route('admin.books.edit', $book->id) }}" class="p-2 text-slate-500 hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-colors" title="تعديل">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </a>
                  <button type="button" id="delete-btn-{{ $book->id }}" onclick="deleteBook({{ $book->id }})" class="p-2 text-slate-500 hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-colors" title="حذف">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-8 text-center text-slate-400">
                لا توجد كتب مطابقة حالياً.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- الترقيم -->
    @if($books->hasPages())
      <div class="p-4 border-t border-slate-200 dark:border-white/10">
        {{ $books->links() }}
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
  async function deleteBook(id) {
    if (!confirm('هل أنت متأكد من رغبتك في حذف هذا الكتاب؟')) return;

    const btn = document.getElementById(`delete-btn-${id}`);
    if (btn) btn.style.opacity = '0.5';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    try {
      const response = await fetch(`/admin/books/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        }
      });

      const res = await response.json();

      if (response.ok && (res.success || res.status === 'success')) {
        const row = document.getElementById(`book-row-${id}`);
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
