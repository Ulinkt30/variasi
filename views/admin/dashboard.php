<?php
/**
 * File: views/admin/dashboard.php
 * Deskripsi: Halaman dashboard admin.
 * Memeriksa apakah admin sudah login sebelum menampilkan konten.
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    // Jika belum login, arahkan ke halaman login admin
    header("Location: /variasi-motor/admin/login");
    exit();
}

// Sertakan file koneksi database dan controller
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

// Inisialisasi controller
$adminController = new AdminController($conn);

// Mengambil data dinamis dari controller
$dashboard_data = $adminController->getDashboardData();

$total_produk = $dashboard_data['total_produk'];
$total_pesanan = $dashboard_data['total_pesanan'];
$total_users = $dashboard_data['total_users'];


// Mengambil data untuk widget baru
$pesanan_terbaru = $adminController->getLatestOrders(5); // Ambil 5 pesanan terbaru
$stok_rendah = $adminController->getLowStockProducts(5); // Ambil 5 produk dengan stok rendah


// Sertakan template header
require_once __DIR__ . '/templates/header.php';
?>

    <div class="flex">
        <!-- Sidebar Admin -->
        <?php require_once __DIR__ . '/templates/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8 bg-slate-50 text-slate-800">
            <header class="flex justify-between items-center pb-4 border-b border-slate-200 mb-6">
                <h1 class="text-3xl font-bold">Dashboard</h1>
                <p class="text-slate-500">Selamat datang, <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>!</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card Total Produk (sekarang dinamis) -->
                <div class="bg-white p-6 rounded-lg shadow-xl">
                    <h3 class="text-xl font-semibold mb-2 flex items-center"><i class="fas fa-box-open mr-2 text-blue-500"></i> Total Produk</h3>
                    <p class="text-4xl font-bold text-blue-500"><?php echo htmlspecialchars($total_produk); ?></p>
                </div>
                <!-- Card Total Pesanan (sekarang dinamis) -->
                <div class="bg-white p-6 rounded-lg shadow-xl">
                    <h3 class="text-xl font-semibold mb-2 flex items-center"><i class="fas fa-shopping-basket mr-2 text-green-500"></i> Total Pesanan</h3>
                    <p class="text-4xl font-bold text-green-500"><?php echo htmlspecialchars($total_pesanan); ?></p>
                </div>
                <!-- Card Total Pengguna (sekarang dinamis) -->
                <div class="bg-white p-6 rounded-lg shadow-xl">
                    <h3 class="text-xl font-semibold mb-2 flex items-center"><i class="fas fa-users mr-2 text-yellow-500"></i> Total Pengguna</h3>
                    <p class="text-4xl font-bold text-yellow-500"><?php echo htmlspecialchars($total_users); ?></p>
                </div>
            </div>

            <!-- Bagian Widget Tambahan -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Widget Pesanan Terbaru -->
                <div class="bg-white p-6 rounded-lg shadow-xl">
                    <h2 class="text-2xl font-bold mb-4 flex items-center"><i class="fas fa-clock mr-2 text-blue-500"></i> Pesanan Terbaru</h2>
                    <ul class="divide-y divide-slate-200">
                        <?php if (empty($pesanan_terbaru)): ?>
                            <li class="py-3 text-slate-500">Tidak ada pesanan terbaru.</li>
                        <?php else: ?>
                            <?php foreach ($pesanan_terbaru as $pesanan): ?>
                                <li class="py-3">
                                    <a href="/variasi-motor/admin/pesanan_detail/<?php echo htmlspecialchars($pesanan['id']); ?>" class="flex justify-between items-center hover:bg-slate-50 transition-colors duration-200 p-2 rounded-lg -mx-2">
                                        <div>
                                            <p class="text-sm font-semibold">Pesanan #<?php echo htmlspecialchars($pesanan['id']); ?></p>
                                            <p class="text-xs text-slate-500">Pengguna: <?php echo htmlspecialchars($pesanan['user_id']); ?></p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                <?php 
                                                    if ($pesanan['status_pesanan'] == 'Selesai') echo 'bg-green-100 text-green-800';
                                                    else if ($pesanan['status_pesanan'] == 'Pending') echo 'bg-yellow-100 text-yellow-800';
                                                    else if ($pesanan['status_pesanan'] == 'Dikemas') echo 'bg-purple-100 text-purple-800';
                                                    else if ($pesanan['status_pesanan'] == 'Dikirim') echo 'bg-blue-100 text-blue-800';
                                                    else if ($pesanan['status_pesanan'] == 'Dibatalkan') echo 'bg-red-100 text-red-800';
                                                ?>">
                                                <?php echo htmlspecialchars($pesanan['status_pesanan']); ?>
                                            </span>
                                            <i class="fas fa-eye text-slate-400"></i>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Widget Produk Stok Rendah -->
                <div class="bg-white p-6 rounded-lg shadow-xl">
                    <h2 class="text-2xl font-bold mb-4 flex items-center"><i class="fas fa-exclamation-triangle mr-2 text-red-500"></i> Stok Produk Rendah</h2>
                    <ul class="divide-y divide-slate-200">
                        <?php if (empty($stok_rendah)): ?>
                            <li class="py-3 text-slate-500">Tidak ada produk dengan stok rendah.</li>
                        <?php else: ?>
                            <?php foreach ($stok_rendah as $produk): ?>
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-semibold"><?php echo htmlspecialchars($produk['nama']); ?></p>
                                        <p class="text-xs text-slate-500">Kategori: <?php echo htmlspecialchars($produk['nama_kategori']); ?></p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Stok: <?php echo htmlspecialchars($produk['stok']); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>

<?php
// Sertakan template footer
require_once __DIR__ . '/templates/footer.php';
?>
