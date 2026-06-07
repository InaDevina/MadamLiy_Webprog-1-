document.addEventListener('DOMContentLoaded', () => {
    buatFiturMobileMenu();
    jalankanAnimasiFadeIn();
    buatFiturPencarianDanFilter();
    buatInteraksiTombolAction();
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
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }

    if (btnOpen) btnOpen.addEventListener('click', toggleMenu);
    if (btnClose) btnClose.addEventListener('click', toggleMenu);
    if (overlay) overlay.addEventListener('click', toggleMenu);
}

function jalankanAnimasiFadeIn() {
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

function buatFiturPencarianDanFilter() {
    const inputCari = document.getElementById('searchInput');
    const filterKategori = document.getElementById('categoryFilter');
    const barisMenu = document.querySelectorAll('.menu-row');
    const pesanKosong = document.getElementById('noDataMessage');

    function saringTabel() {
        const kataKunci = inputCari.value.toLowerCase();
        const kategoriDipilih = filterKategori.value;
        let adaDataTerlihat = false;

        barisMenu.forEach(baris => {
            const namaMenu = baris.querySelector('.menu-name').textContent.toLowerCase();
            const kategoriMenu = baris.querySelector('.menu-category').textContent;

            const cocokNama = namaMenu.includes(kataKunci);
            const cocokKategori = (kategoriDipilih === 'Semua') || (kategoriMenu === kategoriDipilih);

            if (cocokNama && cocokKategori) {
                baris.style.display = '';
                adaDataTerlihat = true;
            } else {
                baris.style.display = 'none';
            }
        });

        if (!adaDataTerlihat) {
            pesanKosong.classList.remove('hidden');
        } else {
            pesanKosong.classList.add('hidden');
        }
    }

    inputCari.addEventListener('input', saringTabel);
    filterKategori.addEventListener('change', saringTabel);
}

function buatInteraksiTombolAction() {
    const btnTambah = document.getElementById('btnTambahMenu');
    if (btnTambah) {
        btnTambah.addEventListener('click', () => {
            alert('Akan memunculkan form tambah menu baru.');
        });
    }

    const actionBtns = document.querySelectorAll('.action-btn');
    actionBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const isDelete = this.classList.contains('text-red-400');
            const row = this.closest('tr');
            const menuName = row.querySelector('.menu-name').textContent;

            if (isDelete) {
                const konfirmasi = confirm(`Apakah kamu yakin ingin menghapus "${menuName}" dari daftar?`);
                if (konfirmasi) {
                    row.style.display = 'none';
                }
            } else {
                alert(`Masuk ke mode edit untuk: ${menuName}`);
            }
        });
    });
}