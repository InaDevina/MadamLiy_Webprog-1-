// ========== edit-profile.js ==========

// ---- SIDEBAR ----
function openSidebar() { document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('sidebar-overlay').classList.remove('hidden'); }
function closeSidebar() { document.getElementById('sidebar').classList.add('-translate-x-full'); document.getElementById('sidebar-overlay').classList.add('hidden'); }

// ---- PHOTO UPLOAD ----
function triggerPhotoUpload() { document.getElementById('photo-input').click(); }

function handlePhotoUpload(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => {
    const img = document.getElementById('avatar-img');
    const initEl = document.getElementById('avatar-initials');
    img.src = ev.target.result;
    img.classList.remove('hidden');
    initEl.classList.add('hidden');
  };
  reader.readAsDataURL(file);
}

// ---- TOGGLE PASSWORD SECTION ----
function togglePasswordSection() {
  const sec = document.getElementById('password-section');
  const btn = document.getElementById('toggle-pw-btn');
  if (sec.classList.contains('hidden')) {
    sec.classList.remove('hidden');
    btn.textContent = 'Sembunyikan';
  } else {
    sec.classList.add('hidden');
    btn.textContent = 'Tampilkan';
  }
}

// ---- TOGGLE SHOW/HIDE PASSWORD ----
function togglePw(inputId, btn) {
  const input = document.getElementById(inputId);
  if (input.type === 'password') {
    input.type = 'text';
    btn.textContent = '🙈';
  } else {
    input.type = 'password';
    btn.textContent = '👁️';
  }
}

// ---- PASSWORD STRENGTH ----
document.addEventListener('DOMContentLoaded', () => {
  const pwBaru = document.getElementById('p-pw-baru');
  if (pwBaru) {
    pwBaru.addEventListener('input', () => {
      const val = pwBaru.value;
      let score = 0;
      if (val.length >= 8) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[0-9]/.test(val)) score++;
      if (/[^A-Za-z0-9]/.test(val)) score++;

      const labels = ['', 'Lemah', 'Cukup', 'Bagus', 'Kuat'];
      const colors = ['', 'sb-weak', 'sb-fair', 'sb-good', 'sb-strong'];
      const allCls = ['sb-weak', 'sb-fair', 'sb-good', 'sb-strong'];

      for (let i = 1; i <= 4; i++) {
        const bar = document.getElementById(`sb${i}`);
        bar.className = `h-1 flex-1 rounded-full ${i <= score ? colors[score] : 'bg-gray-200'}`;
      }
      document.getElementById('strength-label').textContent = val.length ? labels[score] : '';
    });
  }

  // Sync avatar initials with name input
  const namaInput = document.getElementById('p-nama');
  if (namaInput) {
    namaInput.addEventListener('input', () => {
      const words = namaInput.value.trim().split(' ').filter(Boolean);
      const inits = words.slice(0, 2).map(w => w[0].toUpperCase()).join('');
      document.getElementById('avatar-initials').textContent = inits || 'AU';
      document.getElementById('profile-name').textContent = namaInput.value || 'Admin User';
    });
  }

  initFade();
});

// ---- SAVE PROFILE ----
function saveProfile(e) {
  e.preventDefault();
  const nama = document.getElementById('p-nama').value.trim();
  const email = document.getElementById('p-email').value.trim();
  if (!nama || !email) { showToast('Nama dan email wajib diisi!', 'red'); return; }

  // Password check if section visible
  const sec = document.getElementById('password-section');
  if (!sec.classList.contains('hidden')) {
    const baru = document.getElementById('p-pw-baru').value;
    const konfirm = document.getElementById('p-pw-konfirm').value;
    if (baru && baru !== konfirm) { showToast('Password baru tidak cocok!', 'red'); return; }
  }

  showToast('Profil berhasil disimpan! ✅');
}

// ---- RESET FORM ----
function resetForm() {
  document.getElementById('p-nama').value = 'Admin User';
  document.getElementById('p-email').value = 'admin@madamliy.com';
  document.getElementById('p-telp').value = '+62 812-3456-7890';
  document.getElementById('p-jabatan').value = '';
  document.getElementById('p-alamat').value = '';
  document.getElementById('profile-name').textContent = 'Admin User';
  document.getElementById('avatar-initials').textContent = 'AU';
  showToast('Form direset.', 'gray');
}

// ---- TOAST ----
function showToast(msg, color = 'green') {
  const existing = document.querySelector('.toast-msg');
  if (existing) existing.remove();
  const t = document.createElement('div');
  t.className = `toast-msg fixed bottom-6 right-6 bg-${color}-500 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-semibold z-50`;
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2500);
}

// ---- SCROLL FADE ----
function initFade() {
  const obs = new IntersectionObserver(entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }), { threshold: 0.1 });
  document.querySelectorAll('.fade-in').forEach(el => obs.observe(el));
}
