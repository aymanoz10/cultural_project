<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'لوحة التحكم') | منظومة الثقافة</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- الخطوط الرسمية -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

  <!-- إعدادات Tailwind المتطورة -->
  <script>
    tailwind.config = {
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

  <!-- أنماط CSS المخصصة والدقيقة -->
  <style>
    root { color-scheme: light; }
   body {
      font-family: 'Cairo', 'Tajawal', sans-serif;
      background-color: #F8FAFC;
      color: #0F172A;
      -webkit-tap-highlight-color: transparent;

      /* 🎨 إضافة خلفية الزخرفة */
      background-image: url("{{ asset('images/WhiteBackground.jpeg') }}");
      background-repeat: repeat;         /* لتكرار النمط المزخرف بسلاسة */
      background-size: 900px;             /* تحكّم بحجم الزخرفة حسب الرغبة */
      background-position: center top;
      background-attachment: fixed;       /* تثبيت الخلفية أثناء التمرير */
    }

    /* شريط التمرير الاحترافي */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
    ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

    /* الكروت والأسطح */
    .card {
      background: #FFFFFF;
      border-radius: 1.5rem;
      border: 1px solid rgba(226, 232, 240, 0.8);
      box-shadow: 0 10px 30px -12px rgba(15, 76, 58, 0.06);
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-hover:hover {
      transform: translateY(-2px);
      box-shadow: 0 16px 36px -12px rgba(15, 76, 58, 0.12);
    }

    /* القائمة الجانبية */
    .sidebar-item {
      transition: all 0.2s font-medium;
      border-radius: 0.875rem;
    }
    .sidebar-item.active {
      background: linear-gradient(135deg, #0F4C3A 0%, #0C4132 100%);
      color: #FFFFFF;
      box-shadow: 0 8px 20px -6px rgba(15, 76, 58, 0.5);
    }
    .sidebar-item.active .side-ic { color: #FFFFFF !important; }

    /* النوافذ المنبثقة (Modals) */
    .modal-overlay {
      display: none;
      opacity: 0;
      transition: opacity 0.3s ease;
      background: rgba(15, 23, 42, 0.45);
      backdrop-filter: blur(8px);
    }
    .modal-overlay.active {
      display: flex;
      opacity: 1;
    }
    .modal-content {
      transform: scale(0.95);
      transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .modal-overlay.active .modal-content {
      transform: scale(1);
    }

    /* شارات الحالة (Pills & Badges) */
    .pill {
      font-size: 0.72rem;
      font-weight: 700;
      padding: 0.3rem 0.8rem;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
    }
    .status-active { background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; }
    .status-pending { background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; }
    .status-rejected { background: #FFE4E6; color: #BE123C; border: 1px solid #FECDD3; }
    .status-indigo { background: #E0E7FF; color: #4338CA; border: 1px solid #C7D2FE; }
    .status-slate { background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; }

    /* الجداول العصرية */
    .table-wrap table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-wrap thead th {
      font-size: 0.75rem;
      color: #64748B;
      font-weight: 800;
      padding: 1rem 1.25rem;
      text-align: right;
      background: #F8FAFC;
      border-bottom: 1px solid #E2E8F0;
      white-space: nowrap;
    }
    .table-wrap tbody td {
      padding: 1rem 1.25rem;
      font-size: 0.85rem;
      color: #334155;
      border-bottom: 1px solid #F1F5F9;
      white-space: nowrap;
      transition: background 0.15s ease;
    }
    .table-wrap tbody tr:last-child td { border-bottom: none; }
    .table-wrap tbody tr:hover td { background: #F8FAFC; }

    /* مدخلات النصوص النموذجية */
    .form-input, input[type=text], input[type=password], input[type=date], input[type=number], select, textarea {
      background: #FFFFFF;
      border: 1px solid #CBD5E1;
      border-radius: 0.875rem;
      padding: 0.65rem 1rem;
      font-size: 0.875rem;
      outline: none;
      width: 100%;
      color: #0F172A;
      transition: all 0.2s ease;
    }
    .form-input:focus, input:focus, select:focus, textarea:focus {
      border-color: #0F4C3A;
      box-shadow: 0 0 0 4px rgba(15, 76, 58, 0.1);
      background: #FFFFFF;
    }

    /* الأزرار المصممة */
    .btn-forest {
      background: linear-gradient(135deg, #0F4C3A 0%, #0C4132 100%);
      color: #FFFFFF;
      font-weight: 700;
      transition: all 0.2s ease;
      box-shadow: 0 4px 12px rgba(15, 76, 58, 0.25);
    }
    .btn-forest:hover {
      background: linear-gradient(135deg, #0C4132 0%, #093428 100%);
      box-shadow: 0 6px 16px rgba(15, 76, 58, 0.35);
      transform: translateY(-1px);
    }
    .btn-forest:active { transform: translateY(0); }

    /* حركات وتأثيرات الظهور */
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(6px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>

  @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

<div class="flex min-h-screen relative overflow-x-hidden">

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
      
      <!-- هيدر الصفحة والتصنيفات -->
      <div class="flex items-center justify-between gap-4">
        <div>
        </div>
        @yield('page-actions')
      </div>

      <!-- تنبيهات النظام الناجحة -->
      @if (session('success'))
        <div class="card p-4 bg-emerald-50/90 border-emerald-200/80 text-emerald-800 text-sm font-extrabold flex items-center justify-between gap-3 animate-fade-in shadow-soft-xs">
          <div class="flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>{{ session('success') }}</span>
          </div>
          <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold p-1">✕</button>
        </div>
      @endif

      @yield('content')
    </div>

    {{-- FOOTER --}}
    @include('partials.footer')
  </main>
</div>

{{-- Shared modal container for page-specific modals --}}
@stack('modals')

<!-- السكربتات الأساسية المحدثة -->
<script>
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

  // إدارة النوافذ المنبثقة بحركات ناعمة
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

  // إغلاق المودال عند النقر على الخلفية
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
      e.target.classList.remove('active');
      document.body.classList.remove('overflow-hidden');
    }
  });

  // التحكم بالشريط الجانبي على أجهزة الموبايل بشكل سلس
  const mobileBtn = document.getElementById('mobile-menu-btn');
  const backdrop = document.getElementById('mobile-sidebar-backdrop');

  if (mobileBtn) {
    mobileBtn.addEventListener('click', () => {
      const aside = document.querySelector('aside');
      if (!aside) return;

      const isOpen = aside.classList.contains('translate-x-0');

      if (!isOpen) {
        aside.classList.remove('hidden', '-translate-x-full');
        aside.classList.add('fixed', 'inset-y-0', 'right-0', 'z-40', 'translate-x-0', 'shadow-2xl');
        backdrop.classList.remove('hidden');
      } else {
        aside.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
      }
    });
  }

  backdrop?.addEventListener('click', () => {
    const aside = document.querySelector('aside');
    aside?.classList.add('-translate-x-full');
    backdrop.classList.add('hidden');
  });
</script>

@stack('scripts')
</body>
</html>