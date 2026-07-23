@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')
@section('page-title', 'قائمة المستخدمين والصلاحيات')

@php
  $users = $users ?? collect([
      (object)['id' => 1, 'name' => 'محمد الرفاعي', 'email' => 'm.rifai@example.com', 'role' => 'مدير النظام', 'center' => 'عام', 'status' => 'active'],
      (object)['id' => 2, 'name' => 'سامر الخالد', 'email' => 'samer@example.com', 'role' => 'مدير مركز', 'center' => 'مركز أدهم إسماعيل', 'status' => 'active'],
      (object)['id' => 3, 'name' => 'خالد العلي', 'email' => 'khaled@example.com', 'role' => 'مواطن', 'center' => '-', 'status' => 'banned'],
  ]);
@endphp

@section('content')

<div class="card p-6">
  <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
      <h3 class="font-extrabold text-lg text-slate-800">حسابات المستخدمين والمشرفين</h3>
      <p class="text-xs text-slate-400 mt-1">إدارة الأدوار، الصلاحيات، وتفعيل أو حظر الحسابات</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-forest rounded-xl px-5 py-2.5 font-bold text-sm">+ إضافة مستخدم جديد</a>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-right border-collapse">
      <thead>
        <tr class="border-b border-slate-100 text-xs font-extrabold text-slate-400 pb-3">
          <th class="py-3 px-4">الاسم الكامل</th>
          <th class="py-3 px-4">البريد الإلكتروني</th>
          <th class="py-3 px-4">الدور / الصلاحية</th>
          <th class="py-3 px-4">المركز التابع له</th>
          <th class="py-3 px-4">الحالة</th>
          <th class="py-3 px-4 text-center">الإجراءات</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-sm font-bold text-slate-700">
        @forelse($users as $user)
          <tr class="hover:bg-slate-50/50 transition">
            <td class="py-3.5 px-4 font-extrabold text-slate-800">{{ $user->name }}</td>
            <td class="py-3.5 px-4 text-slate-500 dir-ltr text-right">{{ $user->email }}</td>
            <td class="py-3.5 px-4"><span class="bg-slate-100 text-slate-600 text-xs px-2.5 py-1 rounded-lg">{{ $user->role }}</span></td>
            <td class="py-3.5 px-4 text-slate-500">{{ $user->center }}</td>
            <td class="py-3.5 px-4">
              @if($user->status == 'active')
                <span class="bg-emerald-50 text-emerald-600 text-xs px-2.5 py-1 rounded-full border border-emerald-200">نشط</span>
              @elseif($user->status == 'pending')
                <span class="bg-amber-50 text-amber-600 text-xs px-2.5 py-1 rounded-full border border-amber-200">معلق</span>
              @else
                <span class="bg-rose-50 text-rose-600 text-xs px-2.5 py-1 rounded-full border border-rose-200">محظور</span>
              @endif
            </td>
            <td class="py-3.5 px-4 text-center">
              <div class="flex items-center justify-center gap-2">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg font-bold">تعديل</a>
                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('هل تريد حذف الحساب؟')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 px-3 py-1.5 rounded-lg font-bold">حذف</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-8 text-slate-400">لا يوجد مستخدمون حالياً.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection