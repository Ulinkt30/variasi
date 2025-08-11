<?php
/**
 * File: views/user/riwayat_pesanan.php
 * Deskripsi: Halaman riwayat pesanan pengguna dengan desain seperti Shopee.
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sertakan file koneksi database dan controller Pesanan
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/PesananController.php';

// Sertakan template header (sekarang dengan tema terang)
require_once __DIR__ . '/../templates/header.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo '<div class="container mx-auto p-8 text-center bg-white text-slate-800 rounded-lg shadow-md my-12">';
    echo '<h1 class="text-3xl font-bold mb-4">Riwayat Pesanan Anda</h1>';
    echo '<p class="text-slate-500 mb-6">Anda belum login. Silakan login untuk melihat riwayat pesanan.</p>';
    echo '<a href="/variasi-motor/login" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full inline-block transition duration-300">';
    echo 'Login Sekarang';
    echo '</a>';
    
    require_once __DIR__ . '/../templates/footer.php';
    exit();
}

// Inisialisasi controller
$pesananController = new PesananController($conn);

// Mendapatkan parameter filter status dari URL
$status_filter = $_GET['status'] ?? 'Semua';

// Mengambil riwayat pesanan berdasarkan filter status
$riwayat_pesanan_ids = $pesananController->getRiwayatPesanan($status_filter);

// Sertakan navbar (dengan tema terang)
// Note: Anda mungkin perlu memodifikasi navbar.php untuk mendukung tema terang.
require_once __DIR__ . '/../templates/navbar.php';
?>

<style>
    body {
        background-color: #f1f5f9; /* slate-50 */
        color: #1e293b; /* slate-800 */
    }
    .shopee-orange {
        background-color: #ee4d2d;
    }
    .shopee-orange-text {
        color: #ee4d2d;
    }
    .shopee-status-color {
        background-color: #fffaf0;
        color: #ee4d2d;
        border: 1px solid #ee4d2d;
    }
    .shopee-button {
        border: 1px solid #d0d0d0;
        color: #555;
    }
    /* Menghilangkan scrollbar tapi tetap bisa di-scroll */
    .hide-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    .hide-scrollbar::-webkit-scrollbar {
        display: none; /* Chrome, Safari, and Opera */
    }
</style>

<div class="container mx-auto p-6">
    
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Riwayat Pembelian</h1>
    </div>

    <!-- Navigasi Tab Status -->
    <div class="bg-white p-2 md:p-4 rounded-lg shadow-md mb-6">
        <div class="flex md:space-x-4 overflow-x-auto text-sm font-medium text-slate-500 border-b border-slate-200 hide-scrollbar">
            <a href="/variasi-motor/riwayat_pesanan" class="flex-shrink-0 px-3 md:px-4 py-2 hover:text-blue-500 transition-colors duration-200 <?php echo ($status_filter == 'Semua') ? 'text-blue-500 border-b-2 border-blue-500' : ''; ?>">Semua</a>
            <a href="/variasi-motor/riwayat_pesanan?status=Pending" class="flex-shrink-0 px-3 md:px-4 py-2 hover:text-blue-500 transition-colors duration-200 <?php echo ($status_filter == 'Pending') ? 'text-blue-500 border-b-2 border-blue-500' : ''; ?>">Pending</a>
            <a href="/variasi-motor/riwayat_pesanan?status=Dikemas" class="flex-shrink-0 px-3 md:px-4 py-2 hover:text-blue-500 transition-colors duration-200 <?php echo ($status_filter == 'Dikemas') ? 'text-blue-500 border-b-2 border-blue-500' : ''; ?>">Sedang Dikemas</a>
            <a href="/variasi-motor/riwayat_pesanan?status=Dikirim" class="flex-shrink-0 px-3 md:px-4 py-2 hover:text-blue-500 transition-colors duration-200 <?php echo ($status_filter == 'Dikirim') ? 'text-blue-500 border-b-2 border-blue-500' : ''; ?>">Dikirim</a>
            <a href="/variasi-motor/riwayat_pesanan?status=Selesai" class="flex-shrink-0 px-3 md:px-4 py-2 hover:text-blue-500 transition-colors duration-200 <?php echo ($status_filter == 'Selesai') ? 'text-blue-500 border-b-2 border-blue-500' : ''; ?>">Selesai</a>
            <a href="/variasi-motor/riwayat_pesanan?status=Dibatalkan" class="flex-shrink-0 px-3 md:px-4 py-2 hover:text-blue-500 transition-colors duration-200 <?php echo ($status_filter == 'Dibatalkan') ? 'text-blue-500 border-b-2 border-blue-500' : ''; ?>">Dibatalkan</a>
        </div>
    </div>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="bg-green-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="bg-red-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>

    <?php if (empty($riwayat_pesanan_ids)): ?>
        <div class="text-center p-10 bg-white rounded-lg shadow-md max-w-lg mx-auto">
            <p class="text-xl text-slate-500">Anda belum memiliki riwayat pesanan.</p>
            <a href="/variasi-motor/katalog" class="bg-blue-600 text-white font-bold py-3 px-6 rounded-full mt-6 inline-block transition duration-300">
                Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($riwayat_pesanan_ids as $pesanan_summary):
                $pesanan = $pesananController->getPesananDetail($pesanan_summary['id']);
                if (!$pesanan || empty($pesanan['detail'])) continue;
            ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center space-x-2 text-sm font-semibold">
                            <i class="fas fa-store text-slate-500"></i>
                            <span class="text-slate-800">Toko Daniel Variasi</span>
                            <span class="bg-slate-100 text-slate-500 text-xs px-2 py-1 rounded-full font-normal">Star+</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded 
                                <?php 
                                    if ($pesanan['status_pesanan'] == 'Selesai') echo 'bg-green-100 text-green-800';
                                    else if ($pesanan['status_pesanan'] == 'Pending') echo 'bg-yellow-100 text-yellow-800';
                                    else if ($pesanan['status_pesanan'] == 'Dikemas') echo 'bg-purple-100 text-purple-800';
                                    else if ($pesanan['status_pesanan'] == 'Dikirim') echo 'bg-blue-100 text-blue-800';
                                    else if ($pesanan['status_pesanan'] == 'Dibatalkan') echo 'bg-red-100 text-red-800';
                                ?>">
                                <?php echo htmlspecialchars(strtoupper($pesanan['status_pesanan'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <?php foreach ($pesanan['detail'] as $detail): ?>
                            <div class="flex space-x-4">
                                <img src="/variasi-motor/assets/uploads/<?php echo htmlspecialchars($detail['gambar']); ?>" alt="<?php echo htmlspecialchars($detail['nama_produk']); ?>" class="w-20 h-20 object-cover rounded-md">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-slate-800"><?php echo htmlspecialchars($detail['nama_produk']); ?></h3>
                                    <p class="text-xs text-slate-500 mt-1">Varian: -</p>
                                    <p class="text-xs text-slate-500">x<?php echo htmlspecialchars($detail['jumlah']); ?></p>
                                </div>
                                <div class="text-right">
                                    <?php 
                                        $harga_satuan = $detail['harga_satuan'];
                                        $diskon = 0.1; // Contoh diskon 10%
                                        $harga_lama = $harga_satuan / (1 - $diskon);
                                    ?>
                                    <p class="text-xs text-slate-400 line-through">Rp <?php echo number_format($harga_lama, 0, ',', '.'); ?></p>
                                    <p class="text-sm text-blue-500 font-bold">Rp <?php echo number_format($harga_satuan * $detail['jumlah'], 0, ',', '.'); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-6 flex justify-end items-center space-x-2 border-t border-slate-200 pt-4">
                        <span class="text-xs text-slate-500">Total Pembelian:</span>
                        <p class="text-xl text-blue-500 font-bold">Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></p>
                    </div>

                    <div class="mt-4 flex justify-end space-x-2">
                        <?php if ($pesanan['status_pesanan'] == 'Selesai'): ?>
                            
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Sertakan footer
require_once __DIR__ . '/../templates/footer.php';
?>
