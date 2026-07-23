@extends('layouts.admin')

@section('title', 'إضافة مستخدم جديد')
@section('page-title', 'إضافة مستخدم جديد')

@php
  // بيانات افتراضية للأدوار والمراكز للتطوير والتجربة قبل الربط الكامل
  $roles = $roles ?? [
      'admin'          => 'مدير النظام (Admin)',
      'center_manager' => 'مدير مركز ثقافي',
      'event_organizer' => 'مشرف فعاليات',
      'user'           => 'مستفيد / مواطن',
  ];

  $centers = $centers ?? collect([
      (object)['id'=>1, 'name'=>'مركز أدهم إسماعيل للفنون'],
      (object)['id'=>2, 'name'=>'دار الأسد للثقافة والفنون'],
      (object)['id'=>3, 'name'=>'مركز الميدان الثقافي'],
      (object)['id'=>4, 'name'=>'مركز كفرسوسة الثقافي'],
  ]);
@endphp

@section('content')

<div class="card p-6 max-w-2xl">
  <div class="flex items-center justify-between mb-5">
    <h3 class="font-extrabold text-lg text-slate-800">بيانات المستخدم الجديد</h3>
    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-400">← العودة لقائمة المستخدمين</a>
  </div>

  <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
    @csrf

    <!-- الاسم الكامل -->
    <div>
      <label class="text-xs font-bold text-slate-500 mb-1 block">الاسم الكامل</label>
      <input type="text" name="name" value="{{ old('name') }}" placeholder="مثال: أحمد المحمود" required>
      @error('name') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
    </div>

    <!-- البريد الإلكتروني ورقم الهاتف -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">البريد الإلكتروني</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="user@example.com" required>
        @error('email') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">رقم الهاتف</label>
        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="09xxxxxxxx">
        @error('phone') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>
    </div>

    <!-- كلمة المرور وتأكيدها -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">كلمة المرور</label>
        <input type="password" name="password" placeholder="••••••••" required>
        @error('password') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" placeholder="••••••••" required>
      </div>
    </div>

    <!-- الدور والمركز الثقافي التابع له -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">الدور / الصلاحية</label>
        <select name="role" required>
          <option value="" disabled selected>اختر الصلاحية</option>
          @foreach ($roles as $key => $label)
            <option value="{{ $key }}" @selected(old('role') == $key)>{{ $label }}</option>
          @endforeach
        </select>
        @error('role') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-xs font-bold text-slate-500 mb-1 block">المركز الثقافي التابع له (إن وجد)</label>
        <select name="center_id">
          <option value="" selected>لا يوجد (عام / مدير نظام)</option>
          @foreach ($centers as $center)
            <option value="{{ $center->id }}" @selected(old('center_id') == $center->id)>{{ $center->name }}</option>
          @endforeach
        </select>
        @error('center_id') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
      </div>
    </div>

    <!-- حالة الحساب -->
    <div>
      <label class="text-xs font-bold text-slate-500 mb-1 block">حالة الحساب</label>
      <select name="status">
        <option value="active" @selected(old('status', 'active') == 'active')>نشط</option>
        <option value="pending" @selected(old('status') == 'pending')>بانتظار التفعيل</option>
        <option value="banned" @selected(old('status') == 'banned')>محظور</option>
      </select>
      @error('status') <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p> @enderror
    </div>

    <!-- أزرار الحفظ والإلغاء -->
    <div class="flex gap-3 pt-2">
      <button type="submit" class="btn-forest rounded-xl px-6 py-2.5 font-bold text-sm">حفظ المستخدم</button>
      <a href="{{ route('admin.users.index') }}" class="bg-slate-100 text-slate-600 rounded-xl px-6 py-2.5 font-bold text-sm">إلغاء</a>
    </div>
  </form>
</div>

@endsection