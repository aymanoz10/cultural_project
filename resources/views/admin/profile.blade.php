@extends('layouts.admin')

@section('page-title', 'الملف الشخصي')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 text-slate-800 dark:text-slate-100 transition-colors duration-300">
    
    <!-- تنبيهات النجاح -->
    @if(session('success'))
        <div class="px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-700 dark:text-emerald-400 text-xs font-bold flex items-center gap-2 animate-pulse">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- تنبيهات الخطأ -->
    @if(session('error'))
        <div class="px-4 py-3 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-700 dark:text-rose-400 text-xs font-bold flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- أخطاء التحقق -->
    @if($errors->any())
        <div class="px-4 py-3 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-700 dark:text-rose-400 text-xs font-bold">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- البطاقة الرئيسية -->
    <div class="bg-white dark:bg-[#161f17]/90 rounded-3xl border border-slate-200/80 dark:border-[#d4af37]/30 shadow-soft-lg dark:shadow-[0_10px_30px_rgba(0,0,0,0.5)] backdrop-blur-md overflow-hidden transition-all duration-300">
        
        <!-- غلاف الهيدر العلوي (عرض كامل وارتفاع تلقائي متناسق 100% بدون قص أو فراغات) -->
        <div class="relative w-full overflow-hidden bg-white dark:bg-[#161f17]">
            <!-- صورة الوضع الفاتح (Light Theme) -->
            <img src="{{ asset('images/pretty-light.png') }}" 
                 alt="مديرية الثقافة بدمشق - وضع فاتح" 
                 class="w-full h-auto block dark:hidden">

            <!-- صورة الوضع الداكن (Dark Theme) -->
            <img src="{{ asset('images/pretty.png') }}" 
                 alt="مديرية الثقافة بدمشق - وضع داكن" 
                 class="w-full h-auto block hidden dark:block">
        </div>

        <!-- محتوى البطاقة ومعلومات المستخدم -->
        <div class="px-6 sm:px-8 pb-8 pt-4">
            
            <!-- قسم صورة البروفايل والاسم في الأعلى بشكل منفصل (تمت إضافة الهامش السالب والطبقة هنا حصراً) -->
            <div class="relative z-10 -mt-12 sm:-mt-16 flex flex-col sm:flex-row items-center sm:items-end gap-5 pb-6 mb-8 border-b border-slate-100 dark:border-white/10 text-center sm:text-right">
                
                <!-- صورة البروفايل المنفصلة -->
                <div class="relative flex-shrink-0">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-white dark:bg-[#0d1410] p-1 shadow-xl border-2 border-slate-200 dark:border-[#d4af37]">
                        <img id="avatar-preview" 
                             src="{{ $admin->avatar ? asset('storage/' . $admin->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($admin->name) . '&background=0F4C3A&color=d4af37' }}" 
                             class="w-full h-full rounded-full object-cover" 
                             alt="Avatar">
                    </div>
                    <!-- زر تغيير الصورة -->
                    <label for="avatar-input" 
                           class="absolute bottom-0 right-0 w-8 h-8 bg-forest-500 hover:bg-forest-600 dark:bg-[#d4af37] dark:hover:bg-[#b59228] text-white dark:text-slate-950 font-bold rounded-full flex items-center justify-center cursor-pointer shadow-lg transition-all hover:scale-110 border-2 border-white dark:border-[#111a14]"
                           title="تغيير الصورة">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </label>
                </div>

                <!-- اسم المستخدم ودوره -->
                <div class="flex-grow">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-[#f3f4f6] tracking-wide">{{ $admin->name }}</h2>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-2">
                        <span class="px-3 py-0.5 rounded-full text-[11px] font-extrabold bg-forest-50 dark:bg-[#d4af37]/10 text-forest-600 dark:text-[#d4af37] border border-forest-200 dark:border-[#d4af37]/30">
                            {{ $admin->role_name ?? 'Admin' }}
                        </span>
                        <span class="text-xs text-slate-400 dark:text-slate-400 font-mono">{{ $admin->email }}</span>
                    </div>
                </div>
            </div>

            <!-- النموذج -->
            <form action="{{ route('test.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-right">
                @csrf
                
                <input type="file" id="avatar-input" name="avatar" class="hidden" accept="image/jpeg,image/png,image/jpg,image/webp">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- الاسم الكامل -->
                    <div class="space-y-2">
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300">الاسم الكامل <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" 
                               value="{{ old('name', $admin->name) }}"
                               required
                               class="w-full bg-slate-50 dark:bg-[#0d1410] text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-xs rounded-xl px-4 py-3 border border-slate-200 dark:border-[#d4af37]/30 focus:border-forest-500 dark:focus:border-[#d4af37] focus:outline-none focus:ring-1 focus:ring-forest-500 dark:focus:ring-[#d4af37] transition-all font-medium"
                               placeholder="أدخل اسمك الكامل">
                    </div>

                    <!-- البريد الإلكتروني -->
                    <div class="space-y-2">
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300">البريد الإلكتروني</label>
                        <input type="email" 
                               value="{{ $admin->email }}"
                               disabled
                               class="w-full bg-slate-100 dark:bg-black/20 text-slate-400 dark:text-slate-500 text-xs rounded-xl px-4 py-3 border border-slate-200 dark:border-white/5 font-medium font-mono cursor-not-allowed">
                    </div>

                    <!-- رقم الهاتف -->
                    <div class="space-y-2">
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300">رقم الهاتف <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" 
                               value="{{ old('phone', $admin->phone) }}"
                               required
                               class="w-full bg-slate-50 dark:bg-[#0d1410] text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-xs rounded-xl px-4 py-3 border border-slate-200 dark:border-[#d4af37]/30 focus:border-forest-500 dark:focus:border-[#d4af37] focus:outline-none focus:ring-1 focus:ring-forest-500 dark:focus:ring-[#d4af37] transition-all font-medium font-mono text-right"
                               placeholder="09xxxxxxxx">
                    </div>

                    <!-- كلمة المرور الجديدة -->
                    <div class="space-y-2">
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300">كلمة المرور الجديدة</label>
                        <input type="password" name="password" 
                               class="w-full bg-slate-50 dark:bg-[#0d1410] text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-xs rounded-xl px-4 py-3 border border-slate-200 dark:border-[#d4af37]/30 focus:border-forest-500 dark:focus:border-[#d4af37] focus:outline-none focus:ring-1 focus:ring-forest-500 dark:focus:ring-[#d4af37] transition-all font-medium"
                               placeholder="اتركها فارغة إذا لم ترد التغيير">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">اترك الحقل فارغاً إذا لم ترد تغيير كلمة المرور</p>
                    </div>
                </div>

                <!-- الأزرار -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-white/10">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 transition-all">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl text-xs font-extrabold text-white dark:text-slate-950 bg-forest-500 hover:bg-forest-600 dark:bg-[#d4af37] dark:hover:bg-[#b59228] transition-all active:scale-95 shadow-lg shadow-forest-500/20 dark:shadow-[#d4af37]/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// معاينة الصورة فوراً عند اختيار ملف جديد
document.getElementById('avatar-input').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endsection