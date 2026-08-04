@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')
@section('page-title', 'قائمة المستخدمين والصلاحيات')

{{-- ❌ تم حذف مصفوفة البيانات الوهمية لأن البيانات تأتي الآن بشكل حقيقي من الكنترولر ❌ --}}

@section('content')

<div class="bg-white dark:bg-[#181C1A]/90 rounded-3xl border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl p-6 md:p-8 transition-colors duration-300">
  
  {{-- رأس الصفحة والزر --}}
  <div class="flex flex-wrap items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-100 dark:border-white/10">
    <div>
      <h3 class="font-black text-xl text-slate-900 dark:text-white tracking-tight">حسابات المستخدمين والمشرفين</h3>
      <p class="text-xs text-slate-400 dark:text-slate-400 mt-1">إدارة الأدوار، الصلاحيات، وتفعيل أو حظر الحسابات بكل سهولة</p>
    </div>
    
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl px-5 py-3 font-bold text-sm shadow-lg shadow-indigo-600/20 transition-all hover:scale-[1.02] active:scale-95">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
      </svg>
      إضافة مستخدم جديد
    </a>
  </div>

  {{-- جدول المستخدمين --}}
  <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-white/10">
    <table class="w-full text-right border-collapse">
      <thead>
        <tr class="bg-slate-50 dark:bg-white/[0.03] border-b border-slate-200 dark:border-white/10 text-xs font-extrabold text-slate-500 dark:text-slate-400">
          <th class="py-4 px-5">الاسم الكامل</th>
          <th class="py-4 px-5">رقم الهاتف</th> {{-- ✅ تعديل من إيميل إلى هاتف --}}
          <th class="py-4 px-5">الدور / الصلاحية</th>
          <th class="py-4 px-5">المركز التابع له</th>
          <th class="py-4 px-5">الحالة</th>
          <th class="py-4 px-5 text-center">الإجراءات</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-semibold text-slate-700 dark:text-slate-300">
        @forelse($users as $user)
          <tr class="hover:bg-slate-50/80 dark:hover:bg-white/[0.02] transition-colors">
            
            {{-- الاسم --}}
            <td class="py-4 px-5">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-black flex items-center justify-center text-xs border border-indigo-100 dark:border-indigo-900/50">
                  {{ mb_substr($user->name, 0, 1) }}
                </div>
                <span class="font-extrabold text-slate-900 dark:text-white">{{ $user->name }}</span>
              </div>
            </td>

            {{-- رقم الهاتف ✅ --}}
            <td class="py-4 px-5 text-slate-500 dark:text-slate-400 dir-ltr text-right text-xs font-mono">{{ $user->phone }}</td>

            {{-- الدور (مع ترجمة القيم الإنجليزية من الداتا بيز إلى العربية) ✅ --}}
            <td class="py-4 px-5">
              <span class="inline-flex items-center px-3 py-1 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-slate-300 text-xs font-bold border border-slate-200 dark:border-white/10">
                @if($user->role == 'super')
                  مدير النظام (Super)
                @elseif($user->role == 'admin')
                  مشرف مركز (Admin)
                @elseif($user->role == 'ticketsAdmin')
                  مسؤول تذاكر
                @else
                  {{ $user->role }}
                @endif
              </span>
            </td>

            {{-- المركز (إظهار ID المركز أو كلمة عام إذا كان null) ✅ --}}
            <td class="py-4 px-5 text-slate-500 dark:text-slate-400 text-xs">
              {{ $user->center_id ? 'مركز رقم (' . $user->center_id . ')' : 'نظام عام' }}
            </td>

            {{-- الحالة --}}
            <td class="py-4 px-5">
              @if($user->status == 'active')
                <span class="inline-flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 text-xs px-3 py-1 rounded-full border border-emerald-200 dark:border-emerald-800/40 font-bold">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> نشط
                </span>
              @elseif($user->status == 'pending')
                <span class="inline-flex items-center gap-1.5 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 text-xs px-3 py-1 rounded-full border border-amber-200 dark:border-amber-800/40 font-bold">
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> معلق
                </span>
              @else
                <span class="inline-flex items-center gap-1.5 bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 text-xs px-3 py-1 rounded-full border border-rose-200 dark:border-rose-800/40 font-bold">
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> محظور
                </span>
              @endif
            </td>

            {{-- الإجراءات --}}
            <td class="py-4 px-5 text-center">
              <div class="flex items-center justify-center gap-2">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center gap-1 text-xs bg-slate-100 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 text-slate-700 dark:text-slate-300 px-3 py-2 rounded-xl font-bold transition-all border border-slate-200 dark:border-white/10" title="تعديل">
                  تعديل
                </a>
                
                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('هل تريد حذف الحساب نهائياً؟')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="inline-flex items-center gap-1 text-xs bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-950/80 text-rose-600 dark:text-rose-400 px-3 py-2 rounded-xl font-bold transition-all border border-rose-200 dark:border-rose-900/40" title="حذف">
                    حذف
                  </button>
                </form>
              </div>
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-12 text-slate-400">
              <div class="flex flex-col items-center justify-center gap-2">
                <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="font-bold text-slate-500 dark:text-slate-400">لا يوجد مستخدمون حالياً.</span>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>

@endsection