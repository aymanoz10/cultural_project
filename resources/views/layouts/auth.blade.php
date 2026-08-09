<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'تسجيل الدخول') | منظومة الثقافة</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: { forest: { DEFAULT: '#0F4C3A', light: '#115E3B' } },
        fontFamily: { cairo: ['Cairo','sans-serif'], tajawal: ['Tajawal','sans-serif'] },
        borderRadius: { '3xl':'1.75rem' }
      }
    }
  }
</script>

<style>
  html,body{font-family:'Cairo','Tajawal',sans-serif;}
  .card{ background:#fff; border-radius:1.75rem; border:1px solid rgba(203,213,225,.6); box-shadow: 0 10px 30px -18px rgba(15,76,58,.15); }
  .pill{ font-size:.72rem; font-weight:700; padding:.28rem .75rem; border-radius:999px; display:inline-flex; align-items:center; gap:.3rem; }
  .status-slate{ background:#F1F5F9; color:#475569; }
  .btn-forest{ background:#0F4C3A; color:#fff; }
  .btn-forest:hover{ background:#0c3d2f; }
  input[type=text],input[type=password]{
    background:#F8F9FA; border:1px solid #E5E8EA; border-radius:.9rem; padding:.6rem .9rem; font-size:.85rem; outline:none; width:100%;
  }
  input:focus{ border-color:#0F4C3A; box-shadow:0 0 0 3px rgba(15,76,58,.12); }
</style>
@stack('styles')
</head>
<body class="min-h-screen w-full flex items-center justify-center relative overflow-hidden"
      style="background:radial-gradient(1200px 600px at 20% -10%, #12513f 0%, #0b3226 45%, #08251c 100%);">

  <div class="absolute inset-0 opacity-20"
       style="background-image:radial-gradient(circle at 80% 20%, #ffffff33 0, transparent 40%), radial-gradient(circle at 10% 90%, #ffffff22 0, transparent 40%);"></div>

  <div class="relative z-10 w-full max-w-md mx-4">
    @yield('content')
  </div>

</body>
</html>