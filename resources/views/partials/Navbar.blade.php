<header class="bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between">
  <div>
    <h2 class="text-base font-extrabold text-slate-800">@yield('page-title', 'لوحة التحكم')</h2>
  </div>
  
  <div class="flex items-center gap-3" id="admin-profile-section">
    <img id="admin-avatar" src="" class="w-9 h-9 rounded-full object-cover hidden border border-slate-200" alt="Admin Avatar">
    <div id="admin-avatar-placeholder" class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
      A
    </div>

    <div class="text-right">
      <span id="admin-name" class="text-xs font-extrabold text-slate-800 block">مشرف النظام</span>
      <span id="admin-role" class="text-[10px] text-slate-400 block font-bold">Admin</span>
    </div>

    <button onclick="logoutAdmin()" class="mr-2 text-xs text-rose-600 hover:bg-rose-50 p-2 rounded-lg font-bold transition flex items-center gap-1" title="تسجيل الخروج">
      <span>🚪</span>
      <span>خروج</span>
    </button>
  </div>
</header>

<script>
  // تحميل بيانات الأدمن من LocalStorage عند فتح اللوحة
  document.addEventListener('DOMContentLoaded', () => {
    const adminData = localStorage.getItem('admin_user');
    if (adminData) {
      try {
        const admin = JSON.parse(adminData);
        if (admin.name) document.getElementById('admin-name').innerText = admin.name;
        if (admin.role) document.getElementById('admin-role').innerText = admin.role;

        if (admin.avatar) {
          const avatarImg = document.getElementById('admin-avatar');
          avatarImg.src = admin.avatar;
          avatarImg.classList.remove('hidden');
          document.getElementById('admin-avatar-placeholder').classList.add('hidden');
        }
      } catch (e) {
        console.error('Error parsing admin user data', e);
      }
    }
  });

  // دالة تسجيل الخروج واستدعاء Endpoint الـ logout
  async function logoutAdmin() {
    const token = localStorage.getItem('admin_token');
    
    if (token) {
      try {
        await fetch('/api/admin/logout', {
          method: 'POST',
          headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        });
      } catch (e) {
        console.error('Logout error:', e);
      }
    }

    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_user');
    window.location.href = '/login';
  }
</script>