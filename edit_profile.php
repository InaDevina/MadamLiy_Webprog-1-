<?php

$db_host = "localhost";
$db_user = "root";
$db_pass = "3307";
$db_name = "resto_madamliy"; 

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$session_user_id = 1; 

$query_user = mysqli_query($conn, "
    SELECT u.*, r.role_name 
    FROM users u 
    LEFT JOIN role r ON u.role_id = r.role_id 
    WHERE u.user_id = $session_user_id
");
$user_data = mysqli_fetch_assoc($query_user);

$nama_user  = $user_data['name'] ?? 'Unknown';
$username   = $user_data['username'] ?? 'unknown';
$role_user  = $user_data['role_name'] ?? 'User';

$words = explode(" ", trim($nama_user));
$inisial = "";
if(count($words) >= 2) {
    $inisial = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
} else {
    $inisial = strtoupper(substr($nama_user, 0, 2));
}

$pesan_sukses = "success";
$pesan_error = "try again";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $post_nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $post_user = mysqli_real_escape_string($conn, $_POST['username']);
    
    $post_new_pass = $_POST['new_password'];
    $post_conf_pass = $_POST['confirm_password'];

    if (!empty($post_new_pass)) {
        if ($post_new_pass === $post_conf_pass) {
            $q_update = "UPDATE users SET name='$post_nama', username='$post_user', password='$post_new_pass' WHERE user_id=$session_user_id";
            if(mysqli_query($conn, $q_update)) {
                $pesan_sukses = "Profil dan Password berhasil diperbarui!";
                $nama_user = $post_nama; 
                $username = $post_user;
            } else {
                $pesan_error = "Gagal memperbarui data: " . mysqli_error($conn);
            }
        } else {
            $pesan_error = "Konfirmasi password baru tidak cocok!";
        }
    } else {
        $q_update = "UPDATE users SET name='$post_nama', username='$post_user' WHERE user_id=$session_user_id";
        if(mysqli_query($conn, $q_update)) {
            $pesan_sukses = "Profil berhasil diperbarui!";
            $nama_user = $post_nama; 
            $username = $post_user;
        } else {
            $pesan_error = "Gagal memperbarui data: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="overscroll-none">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Profile - Madam Liy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="edit_profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body
    class="bg-custom-gradient h-screen text-gray-800 flex flex-col md:flex-row overflow-hidden overscroll-none p-0 md:p-4 gap-0 md:gap-4 relative">

    <div
        class="md:hidden flex items-center justify-between bg-white p-4 shadow-sm border-b border-gray-100 z-20 w-full sticky top-0">
        <div class="flex items-center gap-3">
            <button id="mobileMenuBtn" class="text-gray-600 hover:text-emerald-500 focus:outline-none p-1">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 15a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h14a2 2 0 012 2v6z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 7V3m0 0a2 2 0 00-2 2v2m2-4a2 2 0 012 2v2"></path>
                    </svg>
                </div>
                <h1 class="font-bold text-base text-gray-900">Madam Liy</h1>
            </div>
        </div>
    </div>

    <div id="sidebarOverlay"
        class="fixed inset-0 bg-gray-900/50 z-30 hidden md:hidden transition-opacity opacity-0 duration-300"></div>

    <aside id="sidebar"
        class="fixed md:relative top-0 left-0 w-64 h-full bg-white md:rounded-2xl border-r md:border border-gray-100 p-6 flex flex-col justify-between shadow-2xl md:shadow-sm z-40 md:z-10 transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:mt-4 md:ml-2">
        <button id="closeSidebarBtn" class="md:hidden absolute top-4 right-4 text-gray-400 hover:text-red-500 p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div>
            <div class="hidden md:flex items-center gap-3 mb-10">
                <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 15a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h14a2 2 0 012 2v6z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 7V3m0 0a2 2 0 00-2 2v2m2-4a2 2 0 012 2v2"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-tight text-gray-900">Madam Liy</h1>
                    <p class="text-xs text-gray-400">UC Walk</p>
                </div>
            </div>

            <nav class="space-y-2 mt-8 md:mt-0">
                <a href="dashboard.php"
                    class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl transition-all group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>

                <a href="kelola_menu.php"
                    class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl transition-all group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="font-medium text-sm">Kelola Menu</span>
                </a>

                <a href="list_karyawan.php"
                    class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl transition-all group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span class="font-medium text-sm">List Karyawan</span>
                </a>

                <a href="edit_profile.php"
                    class="flex items-center gap-3 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-md shadow-emerald-200 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="font-semibold text-sm">Edit Profile</span>
                </a>
            </nav>
        </div>

        <div class="mt-10">
            <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 mb-3">
                <p class="text-[11px] text-gray-400 mb-1">Logged in as</p>
                <p class="font-bold text-sm text-gray-900">
                    <?= htmlspecialchars($nama_user) ?>
                </p>
                <p class="text-xs text-emerald-500 font-medium mt-1">Role:
                    <?= htmlspecialchars($role_user) ?>
                </p>
            </div>
            <button
                class="w-full bg-[#ff0f0f] hover:bg-red-700 text-white font-medium py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-md shadow-red-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                Logout
            </button>
        </div>
    </aside>

    <main class="flex-1 p-4 md:p-6 md:mt-4 h-auto md:h-[calc(100vh-2rem)] overflow-y-auto">

        <header class="mb-6 md:mb-8 fade-in">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Edit Profile</h2>
            <p class="text-gray-500 text-sm mt-1">Update your personal account information</p>
        </header>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 fade-in max-w-4xl">

            <?php if(!empty($pesan_sukses)): ?>
            <div
                class="mb-6 p-4 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-sm font-medium">
                ✅
                <?= $pesan_sukses ?>
            </div>
            <?php endif; ?>
            <?php if(!empty($pesan_error)): ?>
            <div class="mb-6 p-4 bg-red-50 text-red-700 border border-red-200 rounded-xl text-sm font-medium">
                ❌
                <?= $pesan_error ?>
            </div>
            <?php endif; ?>

            <form action="edit_profile.php" method="POST">

                <div class="flex items-center gap-5 mb-8">
                    <div
                        class="w-20 h-20 bg-emerald-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-md shrink-0">
                        <?= $inisial ?>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">
                            <?= htmlspecialchars($nama_user) ?>
                        </h3>
                        <p class="text-sm text-gray-500 font-medium">Username: @
                            <?= htmlspecialchars($username) ?>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($nama_user) ?>" required
                            class="w-full px-4 py-3 text-sm text-gray-800 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Username</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required
                            class="w-full px-4 py-3 text-sm text-gray-800 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Role Akses Sistem</label>
                        <input type="text" value="<?= htmlspecialchars($role_user) ?>" readonly disabled
                            class="w-full px-4 py-3 text-sm text-gray-500 border border-gray-200 rounded-xl bg-gray-50 cursor-not-allowed">
                        <p class="text-[10px] text-gray-400 mt-1">*Role akses hanya bisa diubah oleh Super Admin.</p>
                    </div>
                </div>

                <hr class="my-8 border-gray-100">

                <h3 class="font-bold text-gray-900 mb-6">Change Password</h3>
                <p class="text-xs text-gray-500 mb-4">Biarkan kosong jika tidak ingin mengganti password.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Password Baru</label>
                        <input type="password" name="new_password" placeholder="Masukkan password baru"
                            class="w-full px-4 py-3 text-sm text-gray-800 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Konfirmasi Password</label>
                        <input type="password" name="confirm_password" placeholder="Ketik ulang password"
                            class="w-full px-4 py-3 text-sm text-gray-800 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all bg-white">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 mt-10">
                    <button type="submit" name="update_profile" id="btnSave"
                        class="flex-1 bg-[#22c55e] hover:bg-green-600 text-white font-medium py-3 px-6 rounded-xl transition-all shadow-md shadow-green-200 text-sm">
                        Save Changes
                    </button>
                    <a href="dashboard.php" id="btnCancel"
                        class="px-8 py-3 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium rounded-xl transition-all text-sm text-center">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </main>

    <script src="edit_profile.js"></script>
</body>
</html>