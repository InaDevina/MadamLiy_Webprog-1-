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
    const filterRole = document.getElementById('roleFilter');
    const barisEmp = document.querySelectorAll('.emp-row');
    const pesanKosong = document.getElementById('noDataMessage');

    function saringTabel() {
        const kataKunci = inputCari.value.toLowerCase();
        const roleDipilih = filterRole.value;
        let adaDataTerlihat = false;

        barisEmp.forEach(baris => {
            const namaEmp = baris.querySelector('.emp-name').textContent.toLowerCase();
            const roleEmp = baris.querySelector('.emp-role').textContent;

            const cocokNama = namaEmp.includes(kataKunci);
            const cocokRole = (roleDipilih === 'Semua') || (roleEmp === roleDipilih);

            if (cocokNama && cocokRole) {
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
    filterRole.addEventListener('change', saringTabel);
}

function buatInteraksiTombolAction() {
    const btnTambah = document.getElementById('btnTambahEmp');
    if (btnTambah) {
        btnTambah.addEventListener('click', () => {
            alert('Membuka pop-up form penambahan karyawan baru!');
        });
    }

    const actionBtns = document.querySelectorAll('.action-btn');
    actionBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const isDelete = this.classList.contains('text-red-400');
            const row = this.closest('tr');
            const empName = row.querySelector('.emp-name').textContent;

            if (isDelete) {
                const konfirmasi = confirm(`Apakah kamu yakin ingin memberhentikan/menghapus data karyawan bernama "${empName}"?`);
                if (konfirmasi) {
                    row.style.display = 'none'; // Sembunyikan dari tabel jika disetujui
                }
            } else {
                alert(`Masuk ke form Edit Profile Karyawan: ${empName}`);
            }
        });
    });
}