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

$q_trx_today = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE order_date = CURDATE()");
$res_trx_today = mysqli_fetch_assoc($q_trx_today);
$total_trx_today = $res_trx_today['total'] ?? 0;

$q_income_today = mysqli_query($conn, "SELECT SUM(total) as total FROM orders WHERE order_date = CURDATE()");
$res_income_today = mysqli_fetch_assoc($q_income_today);
$income_today = $res_income_today['total'] ?? 0;

if ($income_today >= 1000000) {
    $display_income = 'Rp ' . number_format($income_today / 1000000, 2, ',', '.') . 'M';
} else {
    $display_income = 'Rp ' . number_format($income_today, 0, ',', '.');
}

$q_trx_week = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE YEARWEEK(order_date, 1) = YEARWEEK(CURDATE(), 1)");
$res_trx_week = mysqli_fetch_assoc($q_trx_week);
$total_trx_week = $res_trx_week['total'] ?? 0;

$q_avg = mysqli_query($conn, "SELECT AVG(total) as rata_rata FROM orders WHERE status = 'Selesai'");
$res_avg = mysqli_fetch_assoc($q_avg);
$avg_transaction = $res_avg['rata_rata'] ?? 0;

$q_top_menu = mysqli_query($conn, "
    SELECT m.name, SUM(do.quantity_ordered) as total_porsi 
    FROM detail_orders do 
    JOIN menu m ON do.menu_id = m.menu_id 
    GROUP BY m.menu_id 
    ORDER BY total_porsi DESC 
    LIMIT 5
");

$colors_bg = ['bg-[#fbbf24]', 'bg-gray-300', 'bg-[#f97316]', 'bg-emerald-500', 'bg-[#3b82f6]'];

$q_latest_orders = mysqli_query($conn, "
    SELECT 
        o.orders_id, 
        o.order_time, 
        u.name AS kasir_name, 
        o.pay_method, 
        o.total, 
        o.status,
        GROUP_CONCAT(CONCAT(do.quantity_ordered, 'x ', m.name) SEPARATOR ', ') AS items_summary
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    LEFT JOIN detail_orders do ON o.orders_id = do.orders_id
    LEFT JOIN menu m ON do.menu_id = m.menu_id
    GROUP BY o.orders_id
    ORDER BY o.order_date DESC, o.order_time DESC
    LIMIT 10
");

$q_chart = mysqli_query($conn, "
    SELECT order_date, SUM(total) as total_sales 
    FROM orders 
    WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
    GROUP BY order_date 
    ORDER BY order_date ASC
");
$chart_labels = [];
$chart_values = [];
while ($row_c = mysqli_fetch_assoc($q_chart)) {
    $chart_labels[] = date('D', strtotime($row_c['order_date'])); 
    $chart_values[] = round($row_c['total_sales'] / 1000000, 2); 
}

if (empty($chart_labels)) {
    $chart_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $chart_values = [0, 0, 0, 0, 0, 0, 0];
}
?>
<!DOCTYPE html>
<html lang="id" class="overscroll-none">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard - Madam Liy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="dashboard.css">
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
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
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
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div>
            <div class="hidden md:flex items-center gap-3 mb-10">
                <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
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
                    class="flex items-center gap-3 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-md shadow-emerald-200 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span class="font-semibold text-sm">Dashboard</span>
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

        <header class="mb-6 md:mb-8 fade-in">
            <h2 class="text-xl md:text-2xl font-bold text-gray-900">Dashboard Overview</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Track your restaurant's performance and transactions</p>
        </header>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8 fade-in">
            <div
                class="interactive-card bg-white p-4 md:p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start mb-2 md:mb-4">
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 bg-emerald-500 text-white flex items-center justify-center rounded-lg md:rounded-xl font-bold">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-[10px] md:text-xs text-gray-500 font-medium">Total Transaksi Hari Ini</p>
                <h3 class="text-lg md:text-2xl font-bold mt-1 text-gray-900">
                    <?= $total_trx_today ?>
                </h3>
            </div>

            <div class="interactive-card bg-white p-4 md:p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start mb-2 md:mb-4">
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 bg-emerald-500 text-white flex items-center justify-center rounded-lg md:rounded-xl font-bold">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <span
                        class="bg-blue-50 text-blue-500 text-[9px] md:text-[11px] font-bold px-2 py-1 rounded-full">Today</span>
                </div>
                <p class="text-[10px] md:text-xs text-gray-500 font-medium">Pendapatan Hari Ini</p>
                <h3 class="text-lg md:text-2xl font-bold mt-1 text-gray-900">
                    <?= $display_income ?>
                </h3>
            </div>

            <div class="interactive-card bg-white p-4 md:p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start mb-2 md:mb-4">
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 bg-emerald-500 text-white flex items-center justify-center rounded-lg md:rounded-xl font-bold">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="bg-purple-50 text-purple-500 text-[9px] md:text-[11px] font-bold px-2 py-1 rounded-full">Week</span>
                </div>
                <p class="text-[10px] md:text-xs text-gray-500 font-medium">Transaksi Minggu Ini</p>
                <h3 class="text-lg md:text-2xl font-bold mt-1 text-gray-900">
                    <?= $total_trx_week ?>
                </h3>
            </div>

            <div class="interactive-card bg-white p-4 md:p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start mb-2 md:mb-4">
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 bg-emerald-500 text-white flex items-center justify-center rounded-lg md:rounded-xl font-bold">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-[10px] md:text-xs text-gray-500 font-medium">Rata-rata per Transaksi</p>
                <h3 class="text-lg md:text-2xl font-bold mt-1 text-gray-900">Rp
                    <?= number_format($avg_transaction, 0, ',', '.') ?>
                </h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 fade-in">
            <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-900 text-sm md:text-base">Penjualan per Minggu</h3>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                        <span class="text-[10px] md:text-xs text-gray-500">Pendapatan (Juta Rp)</span>
                    </div>
                </div>
                <div class="h-48 md:h-64 w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-4 md:mb-6">Menu Terlaris</h3>
                <div class="space-y-4">
                    <?php 
                    $rank = 0;
                    while($menu = mysqli_fetch_assoc($q_top_menu)): 
                        $bg_class = $colors_bg[$rank] ?? 'bg-gray-400';
                    ?>
                    <div class="flex items-center gap-4 hover-menu transition-all p-2 rounded-lg cursor-pointer">
                        <div
                            class="w-10 h-10 min-w-[40px] <?= $bg_class ?> text-white font-bold rounded-xl flex items-center justify-center shadow-sm">
                            <?= ++$rank; ?>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-gray-900">
                                <?= htmlspecialchars($menu['name']) ?>
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <?= $menu['total_porsi'] ?> porsi
                            </p>
                        </div>
                    </div>
                    <?php endwhile; ?>

                    <?php if($rank == 0): ?>
                    <p class="text-xs text-gray-400 text-center py-4">Belum ada data transaksi penjualan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden fade-in">
            <div class="p-4 md:p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 text-sm md:text-base">List Transaksi Terbaru</h3>
                <a href="#" class="text-xs md:text-sm text-emerald-500 font-medium hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr
                            class="bg-gray-50/50 text-[11px] text-gray-500 font-semibold border-b border-gray-100 uppercase tracking-wider">
                            <th class="p-4 pl-6">ID TRANSAKSI</th>
                            <th class="p-4">WAKTU</th>
                            <th class="p-4">ITEMS</th>
                            <th class="p-4">KASIR</th>
                            <th class="p-4">PEMBAYARAN</th>
                            <th class="p-4">TOTAL</th>
                            <th class="p-4 text-center">STATUS</th>
                            <th class="p-4 pr-6 text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-600">
                        <?php 
                        while($trx = mysqli_fetch_assoc($q_latest_orders)): 
                            // Set warna badge dinamis berdasarkan metode pembayaran
                            $pay = strtolower($trx['pay_method']);
                            $pay_badge = "bg-gray-50 text-gray-600";
                            if($pay == 'qris') $pay_badge = "bg-blue-50 text-blue-600";
                            elseif($pay == 'cash') $pay_badge = "bg-green-50 text-green-600";
                            elseif($pay == 'transfer') $pay_badge = "bg-purple-50 text-purple-600";

                            // Set warna badge dinamis berdasarkan status order
                            $status = strtolower($trx['status']);
                            $status_badge = "bg-gray-50 text-gray-600";
                            if($status == 'selesai') $status_badge = "bg-emerald-50 text-emerald-600";
                            elseif($status == 'proses') $status_badge = "bg-orange-50 text-orange-500";
                            elseif($status == 'batal') $status_badge = "bg-red-50 text-red-600";
                        ?>
                        <tr class="border-b border-gray-50 hover:bg-emerald-50/30 transition-colors">
                            <td class="p-4 pl-6 font-bold text-gray-900">#TRX-
                                <?= sprintf("%03d", $trx['orders_id']) ?>
                            </td>
                            <td class="p-4">
                                <?= date('H:i', strtotime($row_trx['order_time'] ?? $trx['order_time'])) ?>
                            </td>
                            <td class="p-4 text-xs text-gray-500">
                                <?= htmlspecialchars($trx['items_summary'] ?? 'Tidak ada item') ?>
                            </td>
                            <td class="p-4">
                                <?= htmlspecialchars($trx['kasir_name'] ?? '-') ?>
                            </td>
                            <td class="p-4"><span
                                    class="<?= $pay_badge ?> px-3 py-1 rounded-full text-[10px] font-bold">
                                    <?= strtoupper($trx['pay_method']) ?>
                                </span></td>
                            <td class="p-4 font-bold text-gray-900">Rp
                                <?= number_format($trx['total'], 0, ',', '.') ?>
                            </td>
                            <td class="p-4 text-center"><span
                                    class="<?= $status_badge ?> px-3 py-1 rounded-full text-[10px] font-bold">
                                    <?= ucfirst($trx['status']) ?>
                                </span></td>
                            <td class="p-4 pr-6 text-center">
                                <button
                                    class="text-red-400 hover:text-red-600 hover:scale-110 transition-transform">🗑️</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="dashboard.js"></script>

    <script>
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chart_labels) ?>,
                    datasets: [{
                        label: 'Pendapatan',
                        data: <?= json_encode($chart_values) ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: function (value) { return 'Rp ' + value + 'M'; } } }
                    }
                }
            });
        }
    </script>
</body>

</html>