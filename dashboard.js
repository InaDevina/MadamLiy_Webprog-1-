// ========== dashboard.js ==========

// ---- DATA ----
const salesData = [
  { day: 'Senin',  val: 2.85 },
  { day: 'Selasa', val: 2.60 },
  { day: 'Rabu',   val: 2.90 },
  { day: 'Kamis',  val: 3.10 },
  { day: 'Jumat',  val: 3.50 },
  { day: 'Sabtu',  val: 3.30 },
  { day: 'Minggu', val: 3.20 },
];

const topMenus = [
  { name: 'Ayam Geprek',  qty: '142 porsi', rank: 1 },
  { name: 'Mie Goreng',   qty: '98 porsi',  rank: 2 },
  { name: 'Bebek Goreng', qty: '76 porsi',  rank: 3 },
  { name: 'Ayam Goreng',  qty: '65 porsi',  rank: 4 },
  { name: 'Es Teh Manis', qty: '203 gelas', rank: 5 },
];

const transactions = [
  { id:'#TRX-001', time:'08:24', items:'2x Ayam Geprek, 1x Mie Goreng, 2x Es Teh', kasir:'Andi Susanto', pay:'QRIS',     total:'Rp 86.000',  status:'Selesai' },
  { id:'#TRX-002', time:'08:31', items:'1x Bebek Goreng, 1x Ayam Goreng',           kasir:'Andi Susanto', pay:'Cash',     total:'Rp 57.000',  status:'Selesai' },
  { id:'#TRX-003', time:'08:35', items:'3x Ayam Geprek, 2x Mie Goreng, 3x Air Mineral', kasir:'Citra Putri', pay:'Transfer', total:'Rp 124.000', status:'Selesai' },
  { id:'#TRX-004', time:'08:38', items:'1x Bebek Goreng, 2x Es Teh Manis',          kasir:'Citra Putri', pay:'QRIS',     total:'Rp 51.000',  status:'Proses' },
  { id:'#TRX-005', time:'08:42', items:'2x Ayam Goreng, 1x Mie Goreng, 1x Teh Botol', kasir:'Andi Susanto', pay:'Cash',  total:'Rp 69.000',  status:'Proses' },
];

// ---- SIDEBAR TOGGLE (mobile) ----
function openSidebar() {
  document.getElementById('sidebar').classList.remove('-translate-x-full');
  document.getElementById('sidebar-overlay').classList.remove('hidden');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.add('-translate-x-full');
  document.getElementById('sidebar-overlay').classList.add('hidden');
}

// ---- RENDER TOP MENU ----
function renderTopMenu() {
  const list = document.getElementById('top-menu-list');
  list.innerHTML = topMenus.map(m => `
    <div class="flex items-center gap-3 group cursor-pointer hover:bg-gray-50 rounded-xl p-2 transition">
      <span class="w-7 h-7 rank-${m.rank} rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0">${m.rank}</span>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-gray-800 truncate">${m.name}</p>
        <p class="text-xs text-gray-400">${m.qty}</p>
      </div>
    </div>
  `).join('');
}

// ---- RENDER TRANSACTIONS ----
function renderTransactions() {
  const tbody = document.getElementById('trx-table');
  tbody.innerHTML = transactions.map(t => {
    const payClass = t.pay === 'QRIS' ? 'badge-qris' : t.pay === 'Cash' ? 'badge-cash' : 'badge-transfer';
    const stClass  = t.status === 'Selesai' ? 'status-selesai' : 'status-proses';
    return `
    <tr>
      <td class="px-5 py-3 font-bold text-gray-700">${t.id}</td>
      <td class="px-5 py-3 text-gray-500">${t.time}</td>
      <td class="px-5 py-3 text-gray-600 hidden md:table-cell max-w-xs truncate">${t.items}</td>
      <td class="px-5 py-3 text-gray-600 hidden lg:table-cell">${t.kasir}</td>
      <td class="px-5 py-3 hidden md:table-cell">
        <span class="px-2 py-1 rounded-lg text-xs font-semibold ${payClass}">${t.pay}</span>
      </td>
      <td class="px-5 py-3 font-semibold text-gray-800">${t.total}</td>
      <td class="px-5 py-3">
        <span class="px-2 py-1 rounded-lg text-xs font-semibold ${stClass}">${t.status}</span>
      </td>
      <td class="px-5 py-3">
        <button onclick="deleteRow(this)" class="text-red-400 hover:text-red-600 text-lg transition" title="Hapus">🗑️</button>
      </td>
    </tr>`;
  }).join('');
}

function deleteRow(btn) {
  const row = btn.closest('tr');
  row.style.transition = 'opacity 0.3s, transform 0.3s';
  row.style.opacity = '0';
  row.style.transform = 'translateX(20px)';
  setTimeout(() => row.remove(), 300);
}

// ---- DRAW SVG CHART ----
function drawChart() {
  const svg = document.getElementById('sales-chart');
  const tooltip  = document.getElementById('chart-tooltip');
  const tipDay   = document.getElementById('tooltip-day');
  const tipVal   = document.getElementById('tooltip-val');
  const W = 600, H = 200;
  const PAD = { top: 20, right: 20, bottom: 10, left: 40 };

  const vals = salesData.map(d => d.val);
  const minV = Math.min(...vals) - 0.3;
  const maxV = Math.max(...vals) + 0.3;
  const n    = salesData.length;

  // helpers
  const xPos = i => PAD.left + (i / (n - 1)) * (W - PAD.left - PAD.right);
  const yPos = v => PAD.top + ((maxV - v) / (maxV - minV)) * (H - PAD.top - PAD.bottom);

  // Y grid lines
  let gridHTML = '';
  [3.5, 3.0, 2.5, 2.0].forEach(g => {
    const y = yPos(g);
    gridHTML += `<line x1="${PAD.left}" y1="${y}" x2="${W - PAD.right}" y2="${y}" stroke="#f3f4f6" stroke-width="1"/>`;
    gridHTML += `<text x="${PAD.left - 6}" y="${y + 4}" font-size="9" fill="#9ca3af" text-anchor="end">${g}M</text>`;
  });

  // Area fill path
  let linePath = '';
  let areaPath = `M ${xPos(0)} ${H - PAD.bottom} `;
  salesData.forEach((d, i) => {
    const x = xPos(i), y = yPos(d.val);
    if (i === 0) { linePath += `M ${x} ${y}`; areaPath += `L ${x} ${y} `; }
    else         { linePath += ` L ${x} ${y}`; areaPath += `L ${x} ${y} `; }
  });
  areaPath += `L ${xPos(n - 1)} ${H - PAD.bottom} Z`;

  // Dots
  let dotsHTML = '';
  salesData.forEach((d, i) => {
    const x = xPos(i), y = yPos(d.val);
    dotsHTML += `<circle class="chart-dot" cx="${x}" cy="${y}" r="5" fill="#22c55e" stroke="white" stroke-width="2"
      data-day="${d.day}" data-val="${d.val}" />`;
  });

  svg.innerHTML = `
    <defs>
      <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%"   stop-color="#22c55e" stop-opacity="0.25"/>
        <stop offset="100%" stop-color="#22c55e" stop-opacity="0.02"/>
      </linearGradient>
    </defs>
    ${gridHTML}
    <path d="${areaPath}" fill="url(#areaGrad)"/>
    <path d="${linePath}" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    ${dotsHTML}
  `;

  // Tooltip on dot hover
  svg.querySelectorAll('.chart-dot').forEach(dot => {
    dot.addEventListener('mouseenter', e => {
      const rect = svg.closest('.relative').getBoundingClientRect();
      const svgRect = svg.getBoundingClientRect();
      // calc position relative to parent
      const cx = parseFloat(dot.getAttribute('cx'));
      const cy = parseFloat(dot.getAttribute('cy'));
      const scaleX = svgRect.width  / W;
      const scaleY = svgRect.height / H;
      const tipX = cx * scaleX;
      const tipY = cy * scaleY;

      tipDay.textContent = dot.dataset.day;
      tipVal.textContent = `Rp ${dot.dataset.val}M`;
      tooltip.style.left = `${tipX + 10}px`;
      tooltip.style.top  = `${tipY - 36}px`;
      tooltip.classList.remove('hidden');

      dot.setAttribute('r', '8');
    });

    dot.addEventListener('mouseleave', () => {
      tooltip.classList.add('hidden');
      dot.setAttribute('r', '5');
    });
  });
}

// ---- SCROLL FADE-IN OBSERVER ----
function initScrollObserver() {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        // stagger children stat-cards
        e.target.querySelectorAll('.stat-card').forEach((c, i) => {
          c.style.transitionDelay = `${i * 80}ms`;
        });
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
}

// ---- INIT ----
document.addEventListener('DOMContentLoaded', () => {
  renderTopMenu();
  renderTransactions();
  drawChart();
  initScrollObserver();
});
