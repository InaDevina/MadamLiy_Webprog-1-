// ========== kelola-menu.js ==========

let menus = [
  { id:1, nama:'Ayam Geprek',  kategori:'Makanan', harga:25000, desc:'Ayam goreng crispy dengan sambal', status:'Tersedia' },
  { id:2, nama:'Ayam Goreng',  kategori:'Makanan', harga:22000, desc:'Ayam goreng kremes',               status:'Tersedia' },
  { id:3, nama:'Bebek Goreng', kategori:'Makanan', harga:35000, desc:'Bebek goreng dengan lalapan',       status:'Tersedia' },
  { id:4, nama:'Mie Goreng',   kategori:'Makanan', harga:20000, desc:'Mie goreng dengan sayuran',         status:'Tersedia' },
  { id:5, nama:'Teh Botol',    kategori:'Minuman', harga:5000,  desc:'Teh botol dingin',                  status:'Tersedia' },
  { id:6, nama:'Es Teh Manis', kategori:'Minuman', harga:8000,  desc:'Es teh manis segar',                status:'Tersedia' },
];

let editId   = null;
let deleteId = null;

// ---- SIDEBAR ----
function openSidebar()  { document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('sidebar-overlay').classList.remove('hidden'); }
function closeSidebar() { document.getElementById('sidebar').classList.add('-translate-x-full');    document.getElementById('sidebar-overlay').classList.add('hidden'); }

// ---- RENDER TABLE ----
function renderTable(data = menus) {
  const tbody = document.getElementById('menu-table');
  if (data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada menu ditemukan.</td></tr>`;
    return;
  }
  tbody.innerHTML = data.map(m => {
    const rupiah = 'Rp ' + m.harga.toLocaleString('id-ID');
    const sCls   = m.status === 'Tersedia' ? 'badge-tersedia' : 'badge-habis';
    const kCls   = m.kategori === 'Makanan' ? 'badge-makanan' : 'badge-minuman';
    return `
    <tr>
      <td class="px-5 py-3 font-semibold text-gray-800">${m.nama}</td>
      <td class="px-5 py-3">
        <span class="px-2 py-1 rounded-lg text-xs font-semibold ${kCls}">${m.kategori}</span>
      </td>
      <td class="px-5 py-3 font-bold text-gray-700">${rupiah}</td>
      <td class="px-5 py-3 text-gray-500 hidden md:table-cell">${m.desc}</td>
      <td class="px-5 py-3">
        <span class="px-2 py-1 rounded-lg text-xs font-semibold ${sCls}">${m.status}</span>
      </td>
      <td class="px-5 py-3">
        <div class="flex gap-2">
          <button onclick="openEditModal(${m.id})" class="text-green-500 hover:text-green-700 text-lg transition" title="Edit">✏️</button>
          <button onclick="openDeleteModal(${m.id})" class="text-red-400 hover:text-red-600 text-lg transition" title="Hapus">🗑️</button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

// ---- FILTER / SEARCH ----
function filterMenu() {
  const q   = document.getElementById('menu-search').value.toLowerCase();
  const cat = document.getElementById('menu-filter').value;
  const filtered = menus.filter(m =>
    (m.nama.toLowerCase().includes(q) || m.desc.toLowerCase().includes(q)) &&
    (cat === '' || m.kategori === cat)
  );
  renderTable(filtered);
}

// ---- ADD MODAL ----
function openAddModal() {
  editId = null;
  document.getElementById('modal-title').textContent = 'Tambah Menu Baru';
  document.getElementById('f-nama').value     = '';
  document.getElementById('f-harga').value    = '';
  document.getElementById('f-desc').value     = '';
  document.getElementById('f-kategori').value = 'Makanan';
  document.getElementById('f-status').value   = 'Tersedia';
  document.getElementById('menu-modal').classList.remove('hidden');
}

// ---- EDIT MODAL ----
function openEditModal(id) {
  editId = id;
  const m = menus.find(x => x.id === id);
  document.getElementById('modal-title').textContent = 'Edit Menu';
  document.getElementById('f-nama').value     = m.nama;
  document.getElementById('f-harga').value    = m.harga;
  document.getElementById('f-desc').value     = m.desc;
  document.getElementById('f-kategori').value = m.kategori;
  document.getElementById('f-status').value   = m.status;
  document.getElementById('menu-modal').classList.remove('hidden');
}

function closeModal() { document.getElementById('menu-modal').classList.add('hidden'); }

// ---- SAVE (add or edit) ----
function saveMenu() {
  const nama  = document.getElementById('f-nama').value.trim();
  const harga = parseInt(document.getElementById('f-harga').value);
  if (!nama || isNaN(harga)) { alert('Nama dan harga wajib diisi!'); return; }

  const data = {
    nama,
    harga,
    kategori: document.getElementById('f-kategori').value,
    desc:     document.getElementById('f-desc').value.trim(),
    status:   document.getElementById('f-status').value,
  };

  if (editId) {
    const idx = menus.findIndex(x => x.id === editId);
    menus[idx] = { ...menus[idx], ...data };
  } else {
    menus.push({ id: Date.now(), ...data });
  }

  closeModal();
  renderTable();
  showToast(editId ? 'Menu berhasil diperbarui!' : 'Menu berhasil ditambahkan!');
}

// ---- DELETE ----
function openDeleteModal(id) { deleteId = id; document.getElementById('delete-modal').classList.remove('hidden'); }
function closeDeleteModal()  { document.getElementById('delete-modal').classList.add('hidden'); }

function confirmDelete() {
  menus = menus.filter(m => m.id !== deleteId);
  closeDeleteModal();
  renderTable();
  showToast('Menu berhasil dihapus!', 'red');
}

// ---- TOAST ----
function showToast(msg, color = 'green') {
  const t = document.createElement('div');
  t.textContent = msg;
  t.className = `fixed bottom-6 right-6 bg-${color}-500 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-semibold z-50`;
  t.style.animation = 'slideUp .3s ease';
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2500);
}

// ---- SCROLL FADE ----
function initFade() {
  const obs = new IntersectionObserver(entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }), { threshold: 0.1 });
  document.querySelectorAll('.fade-in').forEach(el => obs.observe(el));
}

// ---- INIT ----
document.addEventListener('DOMContentLoaded', () => { renderTable(); initFade(); });
