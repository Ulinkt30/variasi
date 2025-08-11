<?php
/**
 * File: views/admin/kelola_pesanan.php
 * Deskripsi: Halaman admin untuk mengelola pesanan (Read & Update Status).
 */

// Sertakan file koneksi database dan controller Pesanan
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/PesananController.php';

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: /variasi-motor/admin/login");
    exit();
}

// Inisialisasi controller
$pesananController = new PesananController($conn);
$all_pesanan = $pesananController->getAllPesanan(); // Mengambil semua pesanan dari database

// Sertakan template header
require_once __DIR__ . '/templates/header.php';
?>

    <div class="flex">
        <!-- Sidebar Admin -->
        <?php require_once __DIR__ . '/templates/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8 bg-slate-50 text-slate-800">
            <header class="flex justify-between items-center pb-4 border-b border-slate-200 mb-6">
                <h1 class="text-3xl font-bold">Kelola Pesanan</h1>
                <p class="text-slate-500">Selamat datang, <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>!</p>
            </header>

            <?php
            // Tampilkan pesan sukses atau error jika ada
            if (isset($_SESSION['success_message'])) {
                echo '<div class="bg-green-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="bg-red-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <!-- Daftar Pesanan -->
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <h2 class="text-2xl font-bold mb-4">Daftar Pesanan</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID Pesanan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Total Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php if (empty($all_pesanan)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-slate-500">Belum ada pesanan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($all_pesanan as $pesanan): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800"><?php echo htmlspecialchars($pesanan['id']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800"><?php echo htmlspecialchars($pesanan['user_id']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800"><?php echo htmlspecialchars($pesanan['tanggal_pesanan']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php 
                                                    if ($pesanan['status_pesanan'] == 'Selesai') echo 'bg-green-500 text-white';
                                                    else if ($pesanan['status_pesanan'] == 'Pending') echo 'bg-yellow-500 text-white';
                                                    else if ($pesanan['status_pesanan'] == 'Dikemas') echo 'bg-purple-500 text-white';
                                                    else if ($pesanan['status_pesanan'] == 'Dikirim') echo 'bg-blue-500 text-white';
                                                    else if ($pesanan['status_pesanan'] == 'Dibatalkan') echo 'bg-red-500 text-white';
                                                ?>">
                                                <?php echo htmlspecialchars($pesanan['status_pesanan']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="/variasi-motor/admin/pesanan_detail/<?php echo htmlspecialchars($pesanan['id']); ?>" class="text-blue-500 hover:text-blue-700">Lihat Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

<?php
// Sertakan template footer
require_once __DIR__ . '/templates/footer.php';
?>
