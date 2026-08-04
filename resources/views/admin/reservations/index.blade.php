@extends('layouts.admin')
@section('title', 'الحجوزات ومتابعة الحضور')

@section('content')
<div class="p-6 space-y-6 dir-rtl text-right" dir="rtl">

    {{-- العنون الرئيسي --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-[#d4af37]">إدارة الحجوزات ومتابعة الحضور</h1>
            <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">متابعة كافة حجوزات الفعاليات وتأكيد الحضور وإدارة قائمة الانتظار</p>
        </div>
    </div>

    {{-- بطاقات الإحصائيات --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#1a1a1a] border border-slate-200/80 dark:border-[#d4af37]/20 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-forest/10 dark:bg-[#d4af37]/10 text-forest dark:text-[#d4af37] flex items-center justify-center font-bold text-xl">
                📊
            </div>
            <div>
                <p class="text-xs text-slate-400 dark:text-gray-400 font-medium">إجمالي الحجوزات</p>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ $stats['total'] }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1a1a1a] border border-slate-200/80 dark:border-[#d4af37]/20 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xl">
                ✅
            </div>
            <div>
                <p class="text-xs text-slate-400 dark:text-gray-400 font-medium">المؤكدة</p>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ $stats['confirmed'] }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1a1a1a] border border-slate-200/80 dark:border-[#d4af37]/20 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xl">
                ⏳
            </div>
            <div>
                <p class="text-xs text-slate-400 dark:text-gray-400 font-medium">قائمة الانتظار</p>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ $stats['wait_list'] }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1a1a1a] border border-slate-200/80 dark:border-[#d4af37]/20 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-xl">
                ❌
            </div>
            <div>
                <p class="text-xs text-slate-400 dark:text-gray-400 font-medium">الملغاة</p>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ $stats['cancelled'] }}</h3>
            </div>
        </div>
    </div>

    {{-- رسائل النجاح --}}
    @if(session('success'))
        <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 p-4 rounded-xl text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    {{-- شريط البحث والفلترة --}}
    <div class="bg-white dark:bg-[#1a1a1a] border border-slate-200/80 dark:border-[#d4af37]/20 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.reservations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم، البريد أو رقم التذكرة..." 
                       class="w-full bg-slate-50 dark:bg-[#262626] border border-slate-200 dark:border-[#d4af37]/20 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-forest dark:focus:ring-[#d4af37]">
            </div>

            <div>
                <select name="activity_id" class="w-full bg-slate-50 dark:bg-[#262626] border border-slate-200 dark:border-[#d4af37]/20 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-forest dark:focus:ring-[#d4af37]">
                    <option value="">جميع الفعاليات</option>
                    @foreach($activities as $act)
                        <option value="{{ $act->id }}" {{ request('activity_id') == $act->id ? 'selected' : '' }}>{{ $act->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="w-full bg-slate-50 dark:bg-[#262626] border border-slate-200 dark:border-[#d4af37]/20 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-forest dark:focus:ring-[#d4af37]">
                    <option value="">جميع الحالات</option>
                    <option value="CONFIRMED" {{ request('status') === 'CONFIRMED' ? 'selected' : '' }}>مؤكد</option>
                    <option value="WAIT_LIST" {{ request('status') === 'WAIT_LIST' ? 'selected' : '' }}>قائمة انتظار</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>ملغى</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-forest dark:bg-[#d4af37] text-white dark:text-[#1a1a1a] font-bold py-2 px-4 rounded-xl text-sm shadow transition hover:opacity-90">
                    تطبيق الفلترة
                </button>
                <a href="{{ route('admin.reservations.index') }}" class="bg-slate-100 dark:bg-[#262626] text-slate-600 dark:text-gray-300 font-bold py-2 px-3 rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-[#333] flex items-center justify-center">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    {{-- جدول عرض الحجوزات --}}
    <div class="bg-white dark:bg-[#1a1a1a] border border-slate-200/80 dark:border-[#d4af37]/20 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right">
                <thead class="bg-slate-50 dark:bg-[#262626] text-slate-500 dark:text-gray-400 font-bold border-b border-slate-100 dark:border-[#d4af37]/10">
                    <tr>
                        <th class="p-4">رقم التذكرة</th>
                        <th class="p-4">المستفيد</th>
                        <th class="p-4">الفعالية</th>
                        <th class="p-4">المقاعد</th>
                        <th class="p-4">التاريخ</th>
                        <th class="p-4">الحالة</th>
                        <th class="p-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#d4af37]/10 text-slate-700 dark:text-gray-200">
                    @forelse($reservations as $res)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-[#d4af37]/5 transition-colors">
                            <td class="p-4 font-mono font-bold text-forest dark:text-[#d4af37] dir-ltr inline-block">
                                {{ $res->ticket_id }}
                            </td>
                            <td class="p-4 font-semibold">
                                <div>{{ $res->user->name ?? 'مستخدم غير معروف' }}</div>
                                <div class="text-xs text-slate-400 dark:text-gray-400 font-normal">{{ $res->user->email ?? '' }}</div>
                            </td>
                            <td class="p-4 font-bold">
                                {{ $res->activity->title ?? '-' }}
                            </td>
                            <td class="p-4 font-bold">
                                <span class="bg-slate-100 dark:bg-[#262626] px-2.5 py-1 rounded-lg text-xs">{{ $res->seats_count }} مقاعد</span>
                            </td>
                            <td class="p-4 text-xs font-medium text-slate-500 dark:text-gray-400">
                                {{ $res->reservation_date }}
                            </td>
                            <td class="p-4">
                                @if(strtoupper($res->status) === 'CONFIRMED')
                                    <span class="bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs px-3 py-1 rounded-full font-bold">مؤكد</span>
                                @elseif(strtoupper($res->status) === 'WAIT_LIST')
                                    <span class="bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-xs px-3 py-1 rounded-full font-bold">انتظار</span>
                                @else
                                    <span class="bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400 text-xs px-3 py-1 rounded-full font-bold">ملغى</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if(strtoupper($res->status) !== 'CANCELLED')
                                    <form method="POST" action="{{ route('admin.reservations.cancel', $res->id) }}" class="inline-block" onsubmit="return confirm('هل تريد إلغاء هذا الحجز؟');">
                                        @csrf
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            إلغاء الحجز
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 dark:text-gray-500 font-medium">
                                لا توجد حجوزات مطابقة لشروط البحث.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- الترقيم اللاحق (Pagination) --}}
        @if($reservations->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-[#d4af37]/10">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection