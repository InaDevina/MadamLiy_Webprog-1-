document.addEventListener('DOMContentLoaded', () => {
    buatFiturMobileMenu();
    jalankanAnimasiScroll();
    buatGrafikPenjualan();
    buatPopUpInteraktif();
});

function buatFiturMobileMenu() {
    const btnOpen = document.getElementById('mobileMenuBtn');
    const btnClose = document.getElementById('closeSidebarBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleMenu() {
        sidebar.classList.toggle('-translate-x-full'); 

        if (overlay.classList.contains('hidden')) {
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.remove('opacity-0'), 10);
        } else {
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300); // Tunggu animasi selesai
        }
    }

    if (btnOpen) btnOpen.addEventListener('click', toggleMenu);
    if (btnClose) btnClose.addEventListener('click', toggleMenu);
    if (overlay) overlay.addEventListener('click', toggleMenu);
}

function jalankanAnimasiScroll() {
    const elemenMungul = document.querySelectorAll('.fade-in');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, { threshold: 0.1 });

    elemenMungul.forEach(elemen => observer.observe(elemen));
}

function buatPopUpInteraktif() {
    const kartu = document.querySelectorAll('.interactive-card');
    kartu.forEach(k => {
        k.addEventListener('click', () => {
            alert('Fitur ini akan terhubung dengan database pada pengembangan selanjutnya!');
        });
    });
}

function buatGrafikPenjualan() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const dataPenjualan = [3.1, 2.7, 3.3, 2.9, 3.8, 3.7];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            datasets: [{
                label: 'Pendapatan (Juta Rp)',
                data: dataPenjualan,
                borderColor: '#10b981',
                borderWidth: 2.5,
                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#ffffff',
                pointHoverBorderColor: '#10b981',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            animations: {
                x: {
                    type: 'number',
                    easing: 'linear',
                    duration: 400,
                    from: NaN,
                    delay(ctx) {
                        if (ctx.type !== 'data' || ctx.xStarted) { return 0; }
                        ctx.xStarted = true;
                        return ctx.index * 300;
                    }
                },
                y: {
                    type: 'number',
                    easing: 'linear',
                    duration: 400,
                    from: (ctx) => {
                        return ctx.index === 0 ? ctx.chart.scales.y.getPixelForValue(0) : ctx.chart.getDatasetMeta(ctx.datasetIndex).data[ctx.index - 1].getProps(['y'], true).y;
                    },
                    delay(ctx) {
                        if (ctx.type !== 'data' || ctx.yStarted) { return 0; }
                        ctx.yStarted = true;
                        return ctx.index * 300;
                    }
                }
            },

            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#374151',
                    bodyColor: '#10b981',
                    bodyFont: { weight: 'bold', size: 13 },
                    borderColor: '#f3f4f6',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            return 'Pendapatan: Rp ' + context.parsed.y + ' Juta';
                        }
                    }
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 4.0,
                    ticks: {
                        stepSize: 0.95,
                        color: '#9ca3af',
                        font: { size: 10 },
                        callback: function (value) {
                            if (value === 0) return '0M';
                            return value.toFixed(2) + 'M';
                        }
                    },
                    grid: {
                        color: '#f3f4f6',
                        drawBorder: false,
                        borderDash: [5, 5]
                    }
                },
                x: {
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });
}