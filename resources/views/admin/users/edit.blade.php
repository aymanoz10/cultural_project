@extends('layouts.admin')

@section('title', 'تعديل بيانات مستخدم')
@section('page-title', 'تعديل المستخدم')

@php
  $roles = $roles ?? [
      'admin'          => 'مدير النظام (Admin)',
      'center_manager' => 'مدير مركز ثقافي',
      'user'           => 'مستفيد / مواطن',
  ];

  $user = $user ?? (object)[
      'id' => 1,
      'name' => 'محمد الرفاعي',
      'email' => 'm.rifai@example.com',
      'phone' => '0912345678',
      'role' => 'admin',
      'center_id' => null,
      'status' => 'active'
  ];
@endphp

@section('content')

<div class="card p-6 max-w-2xl">
  <div class="flex items-center justify-between mb-5">
    <h3 class="font-extrabold text-lg text-slate-800">تعديل حساب: {{ $user->name }}</h3>
    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-400">← العودة للقائمة</a>
  </div>

  <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
      <label class="text-xs font-bold text-slate-500 mb-1 block">الاسم الكامل</label>
      <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">البريد الإلكتروني</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
      </div>
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">رقم الهاتف</label>
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
      </div>
    </div>

    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
      <label class="text-xs font-bold text-slate-500 mb-1 block">تغيير كلمة المرور (تُترك فارغة إذا لم ترد التغيير)</label>
      <input type="password" name="password" placeholder="كلمة مرور جديدة...">
    </div>

    <div>
      <label class="text-xs font-bold text-slate-500 mb-1 block">الدور / الصلاحية</label>
      <select name="role" required>
        @foreach ($roles as $key => $label)
          <option value="{{ $key }}" @selected(old('role', $user->role) == $key)>{{ $label }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-xs font-bold text-slate-500 mb-1 block">حالة الحساب</label>
      <select name="status">
        <option value="active" @selected(old('status', $user->status) == 'active')>نشط</option>
        <option value="pending" @selected(old('status', $user->status) == 'pending')>معلق</option>
        <option value="banned" @selected(old('status', $user->status) == 'banned')>محظور</option>
      </select>
    </div>

    <div class="flex gap-3 pt-2">
      <button type="submit" class="btn-forest rounded-xl px-6 py-2.5 font-bold text-sm">تحديث البيانات</button>
      <a href="{{ route('admin.users.index') }}" class="bg-slate-100 text-slate-600 rounded-xl px-6 py-2.5 font-bold text-sm">إلغاء</a>
    </div>
  </form>
</div>

@endsection