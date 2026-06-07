document.addEventListener('DOMContentLoaded', () => {
    buatFiturMobileMenu();
    jalankanAnimasiFadeIn();
    buatInteraksiForm();
});

// 1. Fungsi Sidebar Responsif (HP)
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

// 2. Animasi Memunculkan Konten Perlahan
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

// 3. Logika Interaktif Form Profile
function buatInteraksiForm() {
    const btnChangePhoto = document.getElementById('btnChangePhoto');
    const btnSave = document.getElementById('btnSave');
    const btnCancel = document.getElementById('btnCancel');

    const newPass = document.getElementById('newPassword');
    const confirmPass = document.getElementById('confirmPassword');

    if (btnChangePhoto) {
        btnChangePhoto.addEventListener('click', () => {
            alert('Membuka file manager untuk upload foto baru...');
        });
    }

    if (btnSave) {
        btnSave.addEventListener('click', () => {
            if (newPass.value !== confirmPass.value) {
                alert('Gagal menyimpan: Password baru dan konfirmasi password tidak sama!');
                return; 
            }

            alert('Sukses! Profile berhasil diperbarui.');
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', () => {
            const yakin = confirm('Apakah kamu yakin ingin membatalkan perubahan? Data yang baru diketik akan hilang.');
            if (yakin) {
                window.location.reload();
            }
        });
    }
}