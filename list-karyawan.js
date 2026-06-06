// ========== list-karyawan.js ==========

let karyawan = [
  { id:'EMP001', nama:'Andi Susanto',  jabatan:'Cashier', telp:'+62 811-2222-3333', email:'andi.susanto@madamliy.com',  alamat:'Jl. Sudirman No. 123', status:'Aktif' },
  { id:'EMP002', nama:'Budi Wijaya',   jabatan:'Chef',    telp:'+62 812-3333-4444', email:'budi.wijaya@madamliy.com',   alamat:'Jl. Thamrin No. 456',  status:'Aktif' },
  { id:'EMP003', nama:'Citra Putri',   jabatan:'Cashier', telp:'+62 813-4444-5555', email:'citra.putri@madamliy.com',   alamat:'Jl. Gatot Subroto No. 789', status:'Aktif' },
  { id:'EMP004', nama:'Dedi Hartono',  jabatan:'Chef',    telp:'+62 814-5555-6666', email:'dedi.hartono@madamliy.com',  alamat:'Jl. Kuningan No. 321', status:'Aktif' },
  { id:'EMP005', nama:'Eka Prasetyo',  jabatan:'Cashier', telp:'+62 815-6666-7777', email:'eka.prasetyo@madamliy.com',  alamat:'Jl. Rasuna Said No. 654', status:'Aktif' },
  { id:'EMP006', nama:'Fitri Andriani', jabatan:'Chef',   telp:'+62 816-7777-8888', email:'fitri.andriani@madamliy.com', alamat:'Jl. Merdeka No. 987', status:'Aktif' },
];

let editId   = null;
let deleteId = null;

// ---- SIDEBAR ----
function openSidebar()  { document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('sidebar-overlay').classList.remove('hidden'); }
function closeSidebar() { document.getElementById('sidebar').classList.add('-translate-x-full');    document.getElementById('sidebar-overlay').classList.add('hidden'); }

// ---- INITIALS + AVATAR COLOR ----
function initials(name) { return name.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase(); }
function avatarColor(idx) { return `av-${idx % 6}`; }

// ---- RENDER TABLE ----
function renderTable(data = karyawan) {
  const tbody = document.getElementById('emp-table');
  if (data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada karyawan ditemukan.</td></tr>`;
    return;
  }
  tbody.innerHTML = data.map((k, i) => {
    const stCls = k.status === 'Aktif' ? 'badge-aktif' : 'badge-nonaktif';
    const jCls  = k.jabatan === 'Cashier' ? 'badge-cashier' : k.jabatan === 'Chef' ? 'badge-chef' : 'badge-other';
    const globalIdx = karyawan.indexOf(k);
    return `
    <tr>
      <td class="px-5 py-3">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 ${avatarColor(globalIdx)} rounded-xl flex items-center justify-center text-white text-xs font-bold flex-shrink-0">${initials(k.nama)}</div>
          <div>
            <p class="font-semibold text-gray-800 text-sm">${k.nama}</p>
            <p class="text-xs text-gray-400">ID: ${k.id}</p>
          </div>
        </div>
      </td>
      <td class="px-5 py-3">
        <span class="px-2 py-1 rounded-lg text-xs font-semibold ${jCls}">${k.jabatan}</span>
      </td>
      <td class="px-5 py-3 text-gray-500 hidden md:table-cell">${k.telp}</td>
      <td class="px-5 py-3 text-gray-500 hidden lg:table-cell">${k.email}</td>
      <td class="px-5 py-3 text-gray-500 hidden xl:table-cell max-w-xs truncate">${k.alamat}</td>
      <td class="px-5 py-3">
        <span class="px-2 py-1 rounded-lg text-xs font-semibold ${stCls}">${k.status}</span>
      </td>
      <td class="px-5 py-3">
        <div class="flex gap-2">
          <button onclick="openEditModal('${k.id}')" class="text-green-500 hover:text-green-700 text-lg transition" title="Edit">✏️</button>
          <button onclick="openDeleteModal('${k.id}')" class="text-red-400 hover:text-red-600 text-lg transition" title="Hapus">🗑️</button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

// ---- FILTER ----
function filterKaryawan() {
  const q   = document.getElementById('emp-search').value.toLowerCase();
  const job = document.getElementById('emp-filter').value;
  const f   = karyawan.filter(k =>
    (k.nama.toLowerCase().includes(q) || k.email.toLowerCase().includes(q)) &&
    (job === '' || k.jabatan === job)
  );
  renderTable(f);
}

// ---- ADD MODAL ----
function openAddModal() {
  editId = null;
  document.getElementById('modal-title').textContent = 'Tambah Karyawan Baru';
  ['f-nama','f-telp','f-email','f-alamat'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('f-jabatan').value = 'Cashier';
  document.getElementById('f-status').value  = 'Aktif';
  document.getElementById('emp-modal').classList.remove('hidden');
}

// ---- EDIT MODAL ----
function openEditModal(id) {
  editId = id;
  const k = karyawan.find(x => x.id === id);
  document.getElementById('modal-title').textContent = 'Edit Karyawan';
  document.getElementById('f-nama').value    = k.nama;
  document.getElementById('f-telp').value    = k.telp;
  document.getElementById('f-email').value   = k.email;
  document.getElementById('f-alamat').value  = k.alamat;
  document.getElementById('f-jabatan').value = k.jabatan;
  document.getElementById('f-status').value  = k.status;
  document.getElementById('emp-modal').classList.remove('hidden');
}

function closeModal() { document.getElementById('emp-modal').classList.add('hidden'); }

// ---- SAVE ----
function saveKaryawan() {
  const nama = document.getElementById('f-nama').value.trim();
  if (!nama) { alert('Nama wajib diisi!'); return; }
  const data = {
    nama,
    jabatan: document.getElementById('f-jabatan').value,
    telp:    document.getElementById('f-telp').value.trim(),
    email:   document.getElementById('f-email').value.trim(),
    alamat:  document.getElementById('f-alamat').value.trim(),
    status:  document.getElementById('f-status').value,
  };
  if (editId) {
    const idx = karyawan.findIndex(x => x.id === editId);
    karyawan[idx] = { ...karyawan[idx], ...data };
  } else {
    const newId = 'EMP' + String(karyawan.length + 1).padStart(3,'0');
    karyawan.push({ id: newId, ...data });
  }
  closeModal();
  renderTable();
  showToast(editId ? 'Data karyawan diperbarui!' : 'Karyawan berhasil ditambahkan!');
}

// ---- DELETE ----
function openDeleteModal(id) { deleteId = id; document.getElementById('delete-modal').classList.remove('hidden'); }
function closeDeleteModal()  { document.getElementById('delete-modal').classList.add('hidden'); }
function confirmDelete() {
  karyawan = karyawan.filter(k => k.id !== deleteId);
  closeDeleteModal();
  renderTable();
  showToast('Karyawan berhasil dihapus!', 'red');
}

// ---- TOAST ----
function showToast(msg, color = 'green') {
  const t = document.createElement('div');
  t.textContent = msg;
  t.className = `fixed bottom-6 right-6 bg-${color}-500 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-semibold z-50`;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2500);
}

// ---- SCROLL FADE ----
function initFade() {
  const obs = new IntersectionObserver(entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }), { threshold: 0.1 });
  document.querySelectorAll('.fade-in').forEach(el => obs.observe(el));
}

document.addEventListener('DOMContentLoaded', () => { renderTable(); initFade(); });
