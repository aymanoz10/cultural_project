  @extends('layouts.admin')

  @section('title', 'إضافة مستخدم جديد')
  @section('page-title', 'إضافة مستخدم جديد')

  @php
    $roles = [
        'super'        => 'سوبر أدمن (Super Admin)',
        'admin'        => 'مدير النظام (Admin)',
        'ticketsAdmin' => 'مسؤول التذاكر (Tickets Admin)',
    ];
  @endphp

  @section('content')

  <div class="max-w-3xl mx-auto">
    <div class="card p-6 md:p-8 transition-colors duration-300">
      
      {{-- رأس النموذج --}}
      <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-100 dark:border-white/10">
        <div>
          <h3 class="font-black text-xl text-slate-900 dark:text-white tracking-tight">بيانات المستخدم الجديد</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">أدخل معلومات الحساب وصلاحيات الإدارة بدقة</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-white/5 px-4 py-2 rounded-xl transition-all border border-slate-200 dark:border-white/10">
          ← العودة لقائمة المستخدمين
        </a>
      </div>

      {{-- تنبيه الأخطاء --}}
      @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50 text-rose-700 dark:text-rose-400 text-xs font-bold space-y-1 animate-fade-in">
          <div class="flex items-center gap-2 mb-1.5 text-sm font-black">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>يرجى تصحيح الأخطاء التالية قبل الحفظ:</span>
          </div>
          <ul class="list-disc list-inside pr-2 space-y-0.5 font-semibold">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- النموذج مُضاف له ID صريح --}}
      <form id="createUserForm" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- الاسم الكامل ورقم الهاتف --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">الاسم الكامل <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="مثال: محمد الرفاعي" required
                  class="w-full bg-slate-50 dark:bg-[#141414] border @error('name') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:border-forest-500 dark:focus:border-[#d4af37] transition-colors">
            @error('name') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1.5 font-bold">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">رقم الهاتف (اسم المستخدم للدخول) <span class="text-rose-500">*</span></label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="09xxxxxxxx" dir="ltr" required
                  class="w-full bg-slate-50 dark:bg-[#141414] border @error('phone') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:border-forest-500 dark:focus:border-[#d4af37] transition-colors text-right">
            @error('phone') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1.5 font-bold">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- كلمة المرور وتأكيدها --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">كلمة المرور <span class="text-rose-500">*</span></label>
            <input type="password" name="password" placeholder="••••••••" required
                  class="w-full bg-slate-50 dark:bg-[#141414] border @error('password') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:border-forest-500 dark:focus:border-[#d4af37] transition-colors">
            @error('password') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1.5 font-bold">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">تأكيد كلمة المرور <span class="text-rose-500">*</span></label>
            <input type="password" name="password_confirmation" placeholder="••••••••" required
                  class="w-full bg-slate-50 dark:bg-[#141414] border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:border-forest-500 dark:focus:border-[#d4af37] transition-colors">
          </div>
        </div>

        {{-- الدور / الصلاحية والمركز التابع له --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">الدور / الصلاحية <span class="text-rose-500">*</span></label>
            <select name="role" required
                    class="w-full bg-slate-50 dark:bg-[#141414] border @error('role') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:border-forest-500 dark:focus:border-[#d4af37] transition-colors">
              <option value="" disabled selected>اختر الصلاحية</option>
              @foreach ($roles as $key => $label)
                <option value="{{ $key }}" @selected(old('role') == $key)>{{ $label }}</option>
              @endforeach
            </select>
            @error('role') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1.5 font-bold">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">المركز الثقافي التابع له (إن وجد)</label>
            <select name="center_id"
                    class="w-full bg-slate-50 dark:bg-[#141414] border @error('center_id') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:border-forest-500 dark:focus:border-[#d4af37] transition-colors">
              <option value="" selected>لا يوجد (عام / إشراف عام)</option>
              @if(isset($centers) && count($centers) > 0)
                @foreach ($centers as $center)
                  <option value="{{ $center->id }}" @selected(old('center_id') == $center->id)>{{ $center->name }}</option>
                @endforeach
              @endif
            </select>
            @error('center_id') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1.5 font-bold">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- حالة الحساب والصورة الشخصية --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">حالة الحساب</label>
            <select name="status"
                    class="w-full bg-slate-50 dark:bg-[#141414] border @error('status') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:border-forest-500 dark:focus:border-[#d4af37] transition-colors">
              <option value="active" @selected(old('status', 'active') == 'active')>نشط</option>
              <option value="pending" @selected(old('status') == 'pending')>بانتظار التفعيل</option>
              <option value="banned" @selected(old('status') == 'banned')>محظور</option>
            </select>
            @error('status') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1.5 font-bold">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">الصورة الشخصية (Avatar)</label>
            <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                  class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-forest-50 file:text-forest-700 dark:file:bg-[#d4af37]/10 dark:file:text-[#d4af37] hover:file:bg-forest-100 transition-all cursor-pointer border @error('avatar') border-rose-500 dark:border-rose-500 @else border-slate-200 dark:border-white/10 @enderror rounded-2xl bg-slate-50 dark:bg-[#141414] p-1.5">
            @error('avatar') <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1.5 font-bold">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- أزرار الحفظ والإلغاء --}}
        <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-white/10">
          <button type="submit" id="submitUserBtn" class="inline-flex items-center justify-center bg-[#0F4C3A] hover:bg-[#0C4132] text-white dark:bg-[#d4af37] dark:hover:bg-[#b8952b] dark:text-slate-900 rounded-2xl px-6 py-3 font-bold text-sm shadow-lg shadow-[#0F4C3A]/20 transition-all hover:scale-[1.01] active:scale-95 cursor-pointer">
            حفظ المستخدم
          </button>
          <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 text-slate-600 dark:text-slate-300 rounded-2xl px-6 py-3 font-bold text-sm transition-all border border-slate-200 dark:border-white/10">
            إلغاء
          </a>
        </div>

      </form>
    </div>
  </div>

  {{-- كود جافاسكريبت مخصص لإجبار وتتبع عملية الإرسال --}}
  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('createUserForm');
      const btn = document.getElementById('submitUserBtn');

      if (form) {
          form.addEventListener('submit', function(e) {
              console.log('🚀 تم استقبال حدث الإرسال (Submit Event)...');
              if (btn) {
                  btn.innerHTML = 'جاري الحفظ...';
                  btn.style.opacity = '0.7';
              }
          });
      }
  });
  </script>

  @endsection