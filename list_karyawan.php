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
    SELECT u.name, r.role_name 
    FROM users u 
    LEFT JOIN role r ON u.role_id = r.role_id 
    WHERE u.user_id = $session_user_id
");
$user_login = mysqli_fetch_assoc($query_user);
$nama_user  = $user_login['name'] ?? 'Admin User';
$role_user  = $user_login['role_name'] ?? 'Admin';

$query_positions = mysqli_query($conn, "SELECT DISTINCT position FROM employee WHERE position IS NOT NULL AND position != '' ORDER BY position ASC");

$query_employees = mysqli_query($conn, "SELECT * FROM employee ORDER BY name ASC");

?>
<!DOCTYPE html>
<html lang="id" class="overscroll-none">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>List Karyawan - Madam Liy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="list_karyawan.css">
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
                    class="flex items-center gap-3 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-md shadow-emerald-200 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span class="font-semibold text-sm">List Karyawan</span>
                </a>

                <a href="edit_profile.php"
                    class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl transition-all group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="font-medium text-sm">Edit Profile</span>
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

        <header class="mb-6 md:mb-8 fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">List Karyawan</h2>
                <p class="text-gray-500 text-sm mt-1">Manage employee data and information</p>
            </div>
            <a href="tambah_karyawan.php" id="btnTambahEmp"
                class="bg-emerald-500 hover:bg-emerald-600 text-white font-medium py-2.5 px-5 rounded-xl transition-all shadow-md shadow-emerald-200 flex items-center justify-center gap-2 w-full md:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Karyawan Baru
            </a>
        </header>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden fade-in">

            <div
                class="p-4 md:p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h3 class="font-bold text-lg text-gray-900">Daftar Karyawan</h3>

                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <input type="text" id="searchInput" placeholder="Cari karyawan..."
                        class="w-full sm:w-64 px-4 py-2.5 md:py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-gray-600">

                    <div class="relative w-full sm:w-48">
                        <select id="roleFilter"
                            class="w-full appearance-none px-4 py-2.5 md:py-2 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-gray-600 cursor-pointer">
                            <option value="Semua">Semua Jabatan</option>
                            <?php while($pos = mysqli_fetch_assoc($query_positions)): ?>
                            <option value="<?= htmlspecialchars($pos['position']) ?>">
                                <?= htmlspecialchars($pos['position']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr
                            class="bg-white text-[11px] text-gray-500 font-semibold border-b border-gray-100 uppercase tracking-wider">
                            <th class="p-4 pl-6">NAMA</th>
                            <th class="p-4">JABATAN</th>
                            <th class="p-4">NO. TELEPON</th>
                            <th class="p-4">EMAIL</th>
                            <th class="p-4">ALAMAT</th>
                            <th class="p-4 text-center">STATUS</th>
                            <th class="p-4 pr-6 text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-600" id="empTableBody">

                        <?php 
                        if(mysqli_num_rows($query_employees) > 0):
                            while($emp = mysqli_fetch_assoc($query_employees)): 
                                
                                $nama_lengkap = trim($emp['name']);
                                $words = explode(" ", $nama_lengkap);
                                $inisial = "";
                                if(count($words) >= 2) {
                                    $inisial = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                } else {
                                    $inisial = strtoupper(substr($words[0], 0, 2));
                                }

                                $jabatan = strtolower($emp['position']);
                                $role_badge = "bg-gray-50 text-gray-500";
                                if(strpos($jabatan, 'cashier') !== false || strpos($jabatan, 'kasir') !== false) {
                                    $role_badge = "bg-blue-50 text-blue-500";
                                } elseif(strpos($jabatan, 'chef') !== false || strpos($jabatan, 'koki') !== false) {
                                    $role_badge = "bg-orange-50 text-orange-500";
                                } elseif(strpos($jabatan, 'admin') !== false || strpos($jabatan, 'owner') !== false) {
                                    $role_badge = "bg-purple-50 text-purple-500";
                                }

                                $status_karyawan = ucfirst(strtolower($emp['status']));
                                $status_color = ($status_karyawan == 'Aktif' || $status_karyawan == 'Active') ? 'text-emerald-500' : 'text-red-500';

                                $emp_formatted_id = "EMP" . str_pad($emp['employee_id'], 3, "0", STR_PAD_LEFT);
                        ?>

                        <tr class="border-b border-gray-50 emp-row transition-all duration-200 hover:bg-emerald-50/40">
                            <td class="p-4 pl-6 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                                    <?= $inisial ?>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 emp-name">
                                        <?= htmlspecialchars($nama_lengkap) ?>
                                    </p>
                                    <p class="text-[11px] text-gray-400">ID:
                                        <?= $emp_formatted_id ?>
                                    </p>
                                </div>
                            </td>
                            <td class="p-4">
                                <span
                                    class="<?= $role_badge ?> px-3 py-1 rounded-full text-[11px] font-bold tracking-wide emp-role">
                                    <?= htmlspecialchars($emp['position'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="p-4 text-gray-600">
                                <?= htmlspecialchars($emp['phone_number'] ?? '-') ?>
                            </td>
                            <td class="p-4 text-gray-600">
                                <?= htmlspecialchars($emp['email'] ?? '-') ?>
                            </td>
                            <td class="p-4 text-gray-600 text-xs">
                                <?= htmlspecialchars($emp['address'] ?? '-') ?>
                            </td>
                            <td class="p-4 text-center font-bold <?= $status_color ?>">
                                <?= $status_karyawan ?>
                            </td>
                            <td class="p-4 pr-6 text-center">
                                <div class="flex items-center justify-center gap-3">
                                 
                                    <a href="edit_karyawan.php?id=<?= $emp['employee_id'] ?>"
                                        class="action-btn text-emerald-500 hover:text-emerald-700 hover:scale-110 transition-transform"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                              
                                    <button
                                        onclick="if(confirm('Yakin ingin menghapus data karyawan <?= htmlspecialchars($nama_lengkap) ?>?')) window.location.href='hapus_karyawan.php?id=<?= $emp['employee_id'] ?>';"
                                        class="action-btn text-red-400 hover:text-red-600 hover:scale-110 transition-transform"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">Belum ada data karyawan di database.
                            </td>
                        </tr>
                        <?php endif; ?>

                    </tbody>
                </table>

                <div id="noDataMessage" class="hidden text-center p-8 text-gray-500">
                    Karyawan tidak ditemukan. Silakan coba pencarian lain.
                </div>
            </div>
        </div>

    </main>

    <script src="list_karyawan.js"></script>
</body>
</html>