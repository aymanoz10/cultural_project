<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - مديرية الثقافة بدمشق</title>

    <!-- خط تجوال Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Tajawal', system-ui, -apple-system, sans-serif;
            background-color: #06221a;
            background-image: 
                radial-gradient(circle at 50% 50%, rgba(13, 75, 59, 0.6) 0%, rgba(4, 22, 17, 0.95) 100%),
                url("{{ asset('images/background.jpeg') }}");
            background-repeat: repeat;
            background-size: 1160px;
            background-position: center;
        }

        .bokeh-top-right {
            background: radial-gradient(circle, rgba(235, 195, 115, 0.4) 0%, rgba(235, 195, 115, 0) 65%);
        }
        .bokeh-mid-right {
            background: radial-gradient(circle, rgba(220, 175, 90, 0.3) 0%, rgba(220, 175, 90, 0) 70%);
        }
        .bokeh-bottom-left {
            background: radial-gradient(circle, rgba(220, 175, 90, 0.25) 0%, rgba(220, 175, 90, 0) 70%);
        }
    </style>
</head>
<body class="min-h-[100dvh] flex items-center justify-center p-4 relative overflow-x-hidden antialiased select-none">

    <div class="absolute -top-10 -right-10 w-96 h-96 bokeh-top-right blur-2xl pointer-events-none z-0"></div>
    <div class="absolute top-1/3 -right-16 w-80 h-80 bokeh-mid-right blur-3xl pointer-events-none z-0"></div>
    <div class="absolute bottom-5 left-5 w-80 h-80 bokeh-bottom-left blur-3xl pointer-events-none z-0"></div>

    <div class="w-full max-w-[400px] rounded-[28px] sm:rounded-[32px] shadow-[0_25px_60px_-15px_rgba(0,0,0,0.7)] p-6 sm:p-9 pt-20 sm:pt-24 relative z-10 text-center border border-white/40 backdrop-blur-sm my-auto" style="background-color: #ffffff !important;">

      <div class="absolute -top-14 sm:-top-20 left-1/2 -translate-x-1/2 flex flex-col items-center justify-center z-20 w-full px-4">
          <img src="{{ asset('images/logo.png') }}" 
               alt="مديرية الثقافة بدمشق" 
               class="w-28 h-28 sm:w-40 sm:h-40 object-contain filter drop-shadow-[0_12px_20px_rgba(0,0,0,0.35)]"
               onerror="this.onerror=null; this.src='https://via.placeholder.com/200x180/ffffff/06221a?text=ضع+اللوغو+هنا';">
      </div>

        <h2 class="text-xl sm:text-2xl font-black text-slate-900 mb-1 mt-2 sm:mt-0">تسجيل دخول المشرفين</h2>
        <p class="text-[11px] sm:text-[12px] text-slate-500 font-semibold mb-5 sm:mb-6">أدخل رقم الهاتف وكلمة المرور للوصول إلى لوحة التحكم</p>

        <div id="alert-box" class="hidden mb-4 p-3 rounded-2xl text-xs font-bold text-center transition-all duration-300"></div>

        <form id="login-form" class="space-y-3.5">
            
            <div class="relative flex items-center">
                <input type="text" id="phone" name="phone" placeholder="09xxxxxxxx" 
                    style="background-color: #f8fafc !important;"
                    class="w-full border border-slate-300 rounded-2xl pr-12 pl-4 py-3 text-sm font-bold text-slate-800 placeholder-slate-400 outline-none focus:border-[#0d4b3b] focus:ring-1 focus:ring-[#0d4b3b] transition" 
                    required autofocus dir="ltr">
                
                <div class="absolute right-3.5 flex items-center gap-1.5 text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span class="text-slate-200 text-xs font-light">|</span>
                </div>
            </div>

            <div class="relative flex items-center">
                <input type="password" id="password" name="password" placeholder="••••••••" 
                    style="background-color: #f8fafc !important;"
                    class="w-full border border-slate-300 rounded-2xl pr-10 pl-10 py-3 text-sm font-bold text-slate-800 placeholder-slate-400 outline-none focus:border-[#0d4b3b] focus:ring-1 focus:ring-[#0d4b3b] transition" 
                    required>
                
                <div class="absolute right-3.5 text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                <button type="button" id="toggle-password" class="absolute left-3.5 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>

            <button type="submit" id="submit-btn" 
                    class="w-full bg-gradient-to-r from-[#062920] via-[#0d4b3b] to-[#062920] hover:brightness-110 active:scale-[0.99] text-white font-extrabold py-3.5 rounded-2xl text-sm transition-all duration-200 shadow-lg shadow-[#06221a]/40 flex items-center justify-center gap-2 mt-2">
                <svg id="btn-spinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="btn-text" class="tracking-wide">تسجيل الدخول</span>
            </button>

        </form>

        <div class="mt-6 sm:mt-8 text-[11px] font-bold text-slate-400">
            نظام إدارة لوحة التحكم المحمية &copy; {{ date('Y') }}
        </div>

    </div>

    <script>
        const togglePasswordBtn = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');

        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('text-[#0d4b3b]');
        });

        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;
            const alertBox = document.getElementById('alert-box');
            const submitBtn = document.getElementById('submit-btn');
            const btnSpinner = document.getElementById('btn-spinner');
            const btnText = document.getElementById('btn-text');

            alertBox.classList.add('hidden');
            alertBox.className = 'hidden mb-4 p-3 rounded-2xl text-xs font-bold text-center transition-all duration-300';

            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-90', 'cursor-not-allowed');
            btnSpinner.classList.remove('hidden');
            btnText.innerText = 'جاري التحقق...';

            try {
                // 👈 تم تغيير المسار إلى /login ليعمل مع Web Session
                const response = await fetch('/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin', // 👈 أضف هذا السطر لضمان حفظ جلسة الكوكيز
        body: JSON.stringify({ phone, password })
    });

                const data = await response.json();

                if (response.ok) {
                    if (data.token) localStorage.setItem('admin_token', data.token);
                    if (data.admin) localStorage.setItem('admin_user', JSON.stringify(data.admin));

                    alertBox.classList.remove('hidden');
                    alertBox.classList.add('bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200');
                    alertBox.innerText = data.message || 'تم تسجيل الدخول بنجاح! جاري التوجيه...';

                    setTimeout(() => {
                        window.location.href = data.redirect || '/admin/dashboard';
                    }, 800);

                } else {
                    alertBox.classList.remove('hidden');
                    alertBox.classList.add('bg-rose-50', 'text-rose-700', 'border', 'border-rose-200');
                    alertBox.innerText = data.message || 'بيانات الدخول غير صحيحة، يرجى المحاولة مرة أخرى';
                }
            } catch (error) {
                alertBox.classList.remove('hidden');
                alertBox.classList.add('bg-rose-50', 'text-rose-700', 'border', 'border-rose-200');
                alertBox.innerText = 'حدث خطأ أثناء الاتصال بالخادم، يرجى التأكد من الاتصال بالشبكة';
            } finally {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-90', 'cursor-not-allowed');
                btnSpinner.classList.add('hidden');
                btnText.innerText = 'تسجيل الدخول';
            }
        });
    </script>

</body>
</html>