// ========== kitchen-queue.js ==========

// ---- DATA ----
let orders = [
  { id:'001', table:'Table 5',   time:'08:24', items:[ {qty:2,name:'Ayam Geprek',note:'Extra sambal'}, {qty:1,name:'Mie Goreng',note:'No msg'}, {qty:2,name:'Es Teh Manis',note:''} ], ready:false },
  { id:'002', table:'Table 3',   time:'08:31', items:[ {qty:1,name:'Bebek Goreng',note:'With lalapan'}, {qty:1,name:'Ayam Goreng',note:''}, {qty:1,name:'Teh Botol',note:''} ], ready:false },
  { id:'003', table:'Table 7',   time:'08:35', items:[ {qty:3,name:'Ayam Geprek',note:'Level 3 spicy'}, {qty:2,name:'Mie Goreng',note:''}, {qty:3,name:'Air Mineral',note:''} ], ready:false },
  { id:'004', table:'Table 2',   time:'08:38', items:[ {qty:1,name:'Bebek Goreng',note:''}, {qty:2,name:'Es Teh Manis',note:''} ], ready:false },
  { id:'005', table:'Table 8',   time:'08:42', items:[ {qty:2,name:'Ayam Goreng',note:''}, {qty:1,name:'Mie Goreng',note:'Extra pedas'}, {qty:1,name:'Teh Botol',note:''} ], ready:false },
  { id:'006', table:'Takeaway',  time:'08:45', items:[ {qty:4,name:'Ayam Geprek',note:''}, {qty:4,name:'Es Teh Manis',note:''} ], ready:false },
];

// Seconds elapsed per order (for live timer simulation)
const orderElapsed = {};
orders.forEach((o, i) => { orderElapsed[o.id] = i * 180 + 504; }); // staggered start

// ---- SIDEBAR ----
function openSidebar()  { document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('sidebar-overlay').classList.remove('hidden'); }
function closeSidebar() { document.getElementById('sidebar').classList.add('-translate-x-full');    document.getElementById('sidebar-overlay').classList.add('hidden'); }

// ---- FORMAT TIME ----
function fmtElapsed(sec) {
  const m = Math.floor(sec / 60);
  const s = sec % 60;
  return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
}

// ---- RENDER CARDS ----
function renderOrders() {
  const grid = document.getElementById('orders-grid');
  const active = orders.filter(o => !o.ready);

  if (orders.every(o => o.ready)) {
    grid.innerHTML = '';
    document.getElementById('empty-state').classList.remove('hidden');
  } else {
    document.getElementById('empty-state').classList.add('hidden');
  }

  grid.innerHTML = active.map((o, i) => {
    const elapsed = orderElapsed[o.id] || 0;
    const urgent  = elapsed > 900;
    const hdrClass = `card-header-${(i % 6) + 1}`;
    const itemsHTML = o.items.map(it => `
      <div class="flex items-start gap-2 py-1.5">
        <span class="qty-badge">${it.qty}x</span>
        <div>
          <p class="text-sm font-semibold text-gray-800">${it.name}</p>
          ${it.note ? `<p class="text-xs text-gray-400">${it.note}</p>` : ''}
        </div>
      </div>
    `).join('');
    return `
    <div class="order-card bg-white" id="card-${o.id}">
      <!-- Card Header -->
      <div class="${hdrClass} px-4 py-3 flex items-center justify-between">
        <div>
          <p class="font-bold text-white text-sm">Order #${o.id}</p>
          <p class="text-white/80 text-xs">${o.table}</p>
        </div>
        <span class="timer-chip ${urgent ? 'urgent' : ''}" id="timer-${o.id}">${fmtElapsed(elapsed)}</span>
      </div>
      <!-- Items -->
      <div class="p-4 space-y-0 divide-y divide-gray-50">
        ${itemsHTML}
      </div>
      <!-- Ready button -->
      <div class="px-4 pb-4">
        <button class="btn-ready" onclick="markReady('${o.id}')">
          ✅ Mark as Ready
        </button>
      </div>
    </div>`;
  }).join('');

  updateCounts();
}

// ---- MARK READY ----
function markReady(id) {
  const card = document.getElementById(`card-${id}`);
  card.classList.add('card-removing');
  setTimeout(() => {
    const o = orders.find(x => x.id === id);
    if (o) o.ready = true;
    renderOrders();
    showToast(`Order #${id} sudah siap! 🎉`);
  }, 400);
}

// ---- COUNT BADGE ----
function updateCounts() {
  const waiting = orders.filter(o => !o.ready).length;
  const ready   = orders.filter(o => o.ready).length;
  document.getElementById('count-waiting').textContent = `${waiting} Menunggu`;
  document.getElementById('count-ready').textContent   = `${ready} Siap`;
}

// ---- LIVE TIMER TICK ----
function tickTimers() {
  orders.forEach(o => {
    if (!o.ready) {
      orderElapsed[o.id] = (orderElapsed[o.id] || 0) + 1;
      const el = document.getElementById(`timer-${o.id}`);
      if (el) {
        el.textContent = fmtElapsed(orderElapsed[o.id]);
        el.classList.toggle('urgent', orderElapsed[o.id] > 900);
      }
    }
  });
}

// ---- TOAST ----
function showToast(msg) {
  const t = document.createElement('div');
  t.textContent = msg;
  t.className = 'fixed bottom-6 right-6 bg-green-500 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-semibold z-50';
  t.style.animation = 'cardIn .3s ease';
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2500);
}

// ---- SCROLL FADE ----
function initFade() {
  const obs = new IntersectionObserver(entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }), { threshold: 0.1 });
  document.querySelectorAll('.fade-in').forEach(el => obs.observe(el));
}

// ---- INIT ----
document.addEventListener('DOMContentLoaded', () => {
  renderOrders();
  initFade();
  setInterval(tickTimers, 1000);
});
