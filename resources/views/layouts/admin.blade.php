<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'لوحة التحكم') | منظومة الثقافة</title>

  <script>
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  </script>

  <script src="https://cdn.tailwindcss.com"></script>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            forest: {
              DEFAULT: '#0F4C3A',
              50: '#F2F8F5',
              100: '#E1EFEA',
              200: '#C3DFD5',
              300: '#94C7B7',
              400: '#52A38D',
              500: '#0F4C3A',
              600: '#0C4132',
              700: '#093428',
              800: '#07271E',
              900: '#041712',
            },
          },
          fontFamily: {
            cairo: ['Cairo', 'sans-serif'],
            tajawal: ['Tajawal', 'sans-serif'],
          },
          boxShadow: {
            'soft-xs': '0 1px 3px 0 rgba(15, 23, 42, 0.03), 0 1px 2px -1px rgba(15, 23, 42, 0.02)',
            'soft-md': '0 10px 30px -10px rgba(15, 76, 58, 0.08), 0 4px 12px -4px rgba(15, 23, 42, 0.03)',
            'soft-lg': '0 20px 40px -15px rgba(15, 76, 58, 0.12)',
            'glass': '0 8px 32px 0 rgba(15, 76, 58, 0.05)',
          },
          borderRadius: {
            '2xl': '1.25rem',
            '3xl': '1.75rem',
          }
        }
      }
    }
  </script>

  <style>
    html { scroll-behavior: smooth; }

    body {
      font-family: 'Cairo', 'Tajawal', sans-serif;
      background-color: #F5F8F6;
      color: #0F172A;
      -webkit-tap-highlight-color: transparent;
      background-image:
        radial-gradient(1100px 560px at 88% -12%, rgba(15, 76, 58, 0.06), transparent 60%),
        radial-gradient(900px 520px at 6% 112%, rgba(212, 175, 55, 0.05), transparent 55%),
        url("{{ asset('images/WhiteBackground.webp') }}");
      background-repeat: no-repeat, no-repeat, repeat;
      background-size: auto, auto, 900px;
      background-position: center top, center top, center top;
      background-attachment: fixed, fixed, fixed;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    html.dark body {
      background-color: #141414;
      color: #f3f4f6;
      background-image:
        radial-gradient(1100px 560px at 88% -12%, rgba(15, 76, 58, 0.22), transparent 60%),
        radial-gradient(900px 520px at 6% 112%, rgba(212, 175, 55, 0.07), transparent 55%),
        url("{{ asset('images/DarkBackground.webp') }}");
    }

    ::selection { background: rgba(15, 76, 58, 0.16); }
    html.dark ::selection { background: rgba(212, 175, 55, 0.30); color: #fff; }

    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #A7CDBF, #52A38D); border-radius: 99px; border: 2px solid transparent; background-clip: padding-box; }
    ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #52A38D, #0F4C3A); background-clip: padding-box; }
    html.dark ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #d4af37, #b8912e); background-clip: padding-box; }

    /* حلقة تركيز واضحة للتنقّل بلوحة المفاتيح (وصولية) */
    :focus-visible { outline: 2px solid rgba(15, 76, 58, 0.55); outline-offset: 2px; border-radius: 6px; }
    html.dark :focus-visible { outline-color: rgba(212, 175, 55, 0.75); }

    .card {
      background: #FFFFFF;
      border-radius: 1.5rem;
      border: 1px solid rgba(226, 232, 240, 0.9);
      box-shadow:
        inset 0 1px 0 0 rgba(255, 255, 255, 0.9),
        0 1px 2px -1px rgba(15, 23, 42, 0.05),
        0 14px 30px -16px rgba(15, 76, 58, 0.16);
      transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease, border-color 0.25s ease;
    }
    html.dark .card {
      background: rgba(24, 24, 24, 0.92);
      border-color: rgba(212, 175, 55, 0.28);
      box-shadow:
        inset 0 1px 0 0 rgba(255, 255, 255, 0.04),
        0 12px 34px -16px rgba(0, 0, 0, 0.65);
    }

    /* ظل مميّز يتفوّق على أدوات الظل المضمّنة (shadow-sm) للحفاظ على عمق موحّد */
    div.card {
      box-shadow:
        inset 0 1px 0 0 rgba(255, 255, 255, 0.9),
        0 1px 2px -1px rgba(15, 23, 42, 0.05),
        0 14px 30px -16px rgba(15, 76, 58, 0.16);
    }
    html.dark div.card {
      box-shadow:
        inset 0 1px 0 0 rgba(255, 255, 255, 0.04),
        0 12px 34px -16px rgba(0, 0, 0, 0.65);
    }

    /* رفع لطيف عند المرور — للبطاقات القابلة للنقر (اختياري) */
    .hover-lift { transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease, border-color 0.25s ease; }
    .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 20px 42px -18px rgba(15, 76, 58, 0.30); border-color: rgba(15, 76, 58, 0.22); }
    html.dark .hover-lift:hover { box-shadow: 0 22px 46px -18px rgba(0, 0, 0, 0.7); border-color: rgba(212, 175, 55, 0.45); }

    /* شريط لوني صغير قبل عناوين الأقسام */
    .section-accent { position: relative; padding-right: 0.85rem; }
    .section-accent::before {
      content: ""; position: absolute; right: 0; top: 50%; transform: translateY(-50%);
      width: 4px; height: 1.05em; border-radius: 99px;
      background: linear-gradient(180deg, #0F4C3A, #52A38D);
    }
    html.dark .section-accent::before { background: linear-gradient(180deg, #d4af37, #b8912e); }

    .sidebar-item { transition: all 0.2s ease; border-radius: 0.875rem; }
    .sidebar-item.active {
      background: linear-gradient(135deg, #0F4C3A 0%, #0C4132 100%);
      color: #FFFFFF;
      box-shadow: 0 8px 20px -6px rgba(15, 76, 58, 0.5);
    }

    .table-wrap table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-wrap thead th {
      font-size: 0.72rem;
      color: #64748B;
      font-weight: 800;
      letter-spacing: 0.03em;
      padding: 0.95rem 1.25rem;
      text-align: right;
      background: linear-gradient(180deg, #F8FAFC, #EEF3F1);
      border-bottom: 1px solid #E2E8F0;
      white-space: nowrap;
    }
    html.dark .table-wrap thead th {
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(0, 0, 0, 0.28));
      color: #d4af37;
      border-bottom-color: rgba(212, 175, 55, 0.2);
    }
    .table-wrap tbody tr { transition: background-color 0.15s ease; }
    .table-wrap tbody td {
      padding: 1rem 1.25rem;
      font-size: 0.85rem;
      color: #334155;
      border-bottom: 1px solid #F1F5F9;
      white-space: nowrap;
    }
    html.dark .table-wrap tbody td {
      color: #9ca3af;
      border-bottom-color: rgba(255, 255, 255, 0.05);
    }

    .form-input, input[type=text], input[type=password], input[type=date], input[type=number], input[type=email], input[type=tel], select, textarea {
      background: #FFFFFF;
      border: 1px solid #CBD5E1;
      border-radius: 0.875rem;
      padding: 0.65rem 1rem;
      font-size: 0.875rem;
      outline: none;
      width: 100%;
      color: #0F172A;
      transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    html.dark .form-input, html.dark input, html.dark select, html.dark textarea {
      background: #141414;
      border-color: rgba(212, 175, 55, 0.3);
      color: #f3f4f6;
    }
    .form-input:focus, input:focus, select:focus, textarea:focus {
      border-color: #0F4C3A;
      box-shadow: 0 0 0 3px rgba(15, 76, 58, 0.12);
    }
    html.dark .form-input:focus, html.dark input:focus, html.dark select:focus, html.dark textarea:focus {
      border-color: #d4af37;
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.16);
    }

    .animate-fade-in { animation: fadeIn 0.35s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (prefers-reduced-motion: reduce) {
      html { scroll-behavior: auto; }
      .animate-fade-in { animation: none; }
    }
  </style>

  @stack('styles')
</head>
<body class="bg-slate-50 dark:bg-[#1a1a1a] text-slate-800 dark:text-gray-100 antialiased min-h-screen flex flex-col transition-colors duration-300">

<div class="flex min-h-screen relative overflow-x-clip">

  {{-- SIDEBAR --}}
  @include('partials.sidebar')

  {{-- MOBILE BACKDROP OVERLAY --}}
  <div id="mobile-sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-30 hidden transition-opacity duration-300"></div>

  {{-- MAIN CONTAINER --}}
  <main class="flex-1 flex flex-col min-w-0 transition-all duration-300">

    {{-- NAVBAR --}}
    @include('partials.navbar')

    {{-- PAGE CONTENT --}}
    <div class="flex-1 p-4 md:p-6 lg:p-8 space-y-6 max-w-[1600px] w-full mx-auto animate-fade-in">
      
      <div class="flex items-center justify-between gap-4">
        <div></div>
        @yield('page-actions')
      </div>

      @if (session('success'))
        <div class="card p-4 bg-emerald-50/90 dark:bg-[#1f1f1f] border-emerald-200/80 dark:border-[#d4af37]/20 text-emerald-800 dark:text-[#d4af37] text-sm font-extrabold flex items-center justify-between gap-3 animate-fade-in shadow-soft-xs">
          <div class="flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>{{ session('success') }}</span>
          </div>
          <button onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-[#d4af37] hover:text-emerald-900 font-bold p-1">✕</button>
        </div>
      @endif

      @yield('content')
    </div>

    {{-- FOOTER --}}
    @include('partials.footer')
  </main>
</div>

@stack('modals')

<script>
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

  function toggleTheme() {
    const htmlEl = document.documentElement;
    if (htmlEl.classList.contains('dark')) {
      htmlEl.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    } else {
      htmlEl.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    }
  }

  function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('active');
    document.body.classList.add('overflow-hidden');
  }

  function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('active');
    document.body.classList.remove('overflow-hidden');
  }

  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
      e.target.classList.remove('active');
      document.body.classList.remove('overflow-hidden');
    }
  });

  // القائمة الجانبية للجوال تُدار بالكامل عبر toggleMobileSidebar()/closeMobileSidebar()
  // في partials/sidebar.blade.php (تعمل على #mobile-sidebar).
  // أُزيل المعالج القديم الذي كان يتلاعب بالقائمة المكتبية (#main-sidebar) عبر
  // document.querySelector('aside') فيزيل صنف hidden ويجعلها تظهر فوق المحتوى على الجوال.
</script>

@stack('scripts')
</body>
</html>