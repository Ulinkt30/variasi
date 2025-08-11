<?php
/**
 * File: views/admin/pesanan_detail.php
 * Deskripsi: Halaman admin untuk melihat detail pesanan.
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

// Mendapatkan ID pesanan dari URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));
$base_dir = 'variasi-motor';

// Temukan indeks ID pesanan
$id_index = array_search($base_dir, $path_parts) + 3;

// Pastikan ID pesanan ada di URL
if (isset($path_parts[$id_index])) {
    $pesanan_id = $path_parts[$id_index];
    $pesanan = $pesananController->getPesananDetail($pesanan_id);
} else {
    $pesanan = null;
}

// Sertakan template header
require_once __DIR__ . '/templates/header.php';
?>

    <div class="flex">
        <!-- Sidebar Admin -->
        <?php require_once __DIR__ . '/templates/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8 bg-slate-50 text-slate-800">
            <header class="flex justify-between items-center pb-4 border-b border-slate-200 mb-6">
                <h1 class="text-3xl font-bold">Detail Pesanan</h1>
                <p class="text-slate-500">Selamat datang, <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>!</p>
            </header>

            <?php if ($pesanan): ?>
                <div class="bg-white p-8 rounded-xl shadow-2xl mb-8">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 pb-4 border-b border-slate-200">
                        <h2 class="text-2xl font-bold mb-4 md:mb-0">Pesanan #<?php echo htmlspecialchars($pesanan['id']); ?></h2>
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                <?php 
                                    if ($pesanan['status_pesanan'] == 'Selesai') echo 'bg-green-500 text-white';
                                    else if ($pesanan['status_pesanan'] == 'Pending') echo 'bg-yellow-500 text-white';
                                    else if ($pesanan['status_pesanan'] == 'Dikemas') echo 'bg-purple-500 text-white';
                                    else if ($pesanan['status_pesanan'] == 'Dikirim') echo 'bg-blue-500 text-white';
                                    else if ($pesanan['status_pesanan'] == 'Dibatalkan') echo 'bg-red-500 text-white';
                                ?>">
                                <?php echo htmlspecialchars($pesanan['status_pesanan']); ?>
                            </span>

                            <form id="update-status-form" action="/variasi-motor/admin/kelola_pesanan/update_status" method="POST" class="inline-flex items-center space-x-2">
                                <input type="hidden" name="pesanan_id" value="<?php echo htmlspecialchars($pesanan['id']); ?>">
                                <select name="status_baru" onchange="confirmUpdate(this)" class="block text-sm rounded-md border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-slate-800">
                                    <option value="" disabled selected>Ubah Status</option>
                                    <option value="Dikemas">Dikemas</option>
                                    <option value="Dikirim">Dikirim</option>
                                    <option value="Selesai">Selesai</option>
                                    <option value="Dibatalkan">Dibatalkan</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                        <div class="bg-slate-50 p-6 rounded-lg shadow-inner">
                            <h2 class="text-xl font-bold mb-4 flex items-center"><i class="fas fa-info-circle mr-2 text-blue-500"></i> Informasi Pesanan</h2>
                            <p class="mb-2"><strong class="text-slate-600">ID Pesanan:</strong> <?php echo htmlspecialchars($pesanan['id']); ?></p>
                            <p class="mb-2"><strong class="text-slate-600">ID Pengguna:</strong> <?php echo htmlspecialchars($pesanan['user_id']); ?></p>
                            <p class="mb-2"><strong class="text-slate-600">Tanggal Pesanan:</strong> <?php echo htmlspecialchars($pesanan['tanggal_pesanan']); ?></p>
                            <p class="mb-2"><strong class="text-slate-600">Total Harga:</strong> <span class="font-bold text-blue-500">Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></span></p>
                        </div>
                        <div class="bg-slate-50 p-6 rounded-lg shadow-inner">
                            <h2 class="text-xl font-bold mb-4 flex items-center"><i class="fas fa-shipping-fast mr-2 text-blue-500"></i> Alamat Pengiriman</h2>
                            <p class="mb-2"><strong class="text-slate-600">Nama Penerima:</strong> <?php echo htmlspecialchars($pesanan['nama_penerima']); ?></p>
                            <p class="mb-2"><strong class="text-slate-600">Alamat:</strong> <?php echo nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])); ?></p>
                            <p class="mb-2"><strong class="text-slate-600">Nomor Telepon:</strong> <?php echo htmlspecialchars($pesanan['telepon_penerima']); ?></p>
                        </div>
                        <?php if ($pesanan['bukti_pembayaran']): ?>
                            <div class="bg-slate-50 p-6 rounded-lg shadow-inner">
                                <h2 class="text-xl font-bold mb-4 flex items-center"><i class="fas fa-credit-card mr-2 text-blue-500"></i> Bukti Pembayaran</h2>
                                <div class="w-full h-48 overflow-hidden rounded-lg">
                                    <a href="/variasi-motor/assets/uploads/<?php echo htmlspecialchars($pesanan['bukti_pembayaran']); ?>" target="_blank" class="block h-full">
                                        <img src="/variasi-motor/assets/uploads/<?php echo htmlspecialchars($pesanan['bukti_pembayaran']); ?>" alt="Bukti Pembayaran" class="w-full h-full object-cover cursor-pointer">
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h2 class="text-xl font-bold mb-4">Produk Dipesan</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 rounded-xl overflow-hidden shadow-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Harga Satuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <?php if (!empty($pesanan['detail'])): ?>
                                    <?php foreach ($pesanan['detail'] as $detail): ?>
                                        <tr class="hover:bg-slate-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 flex items-center space-x-3">
                                                <img src="/variasi-motor/assets/uploads/<?php echo htmlspecialchars($detail['gambar']); ?>" alt="<?php echo htmlspecialchars($detail['nama_produk']); ?>" class="h-10 w-10 rounded-full object-cover">
                                                <span><?php echo htmlspecialchars($detail['nama_produk']); ?></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">Rp <?php echo number_format($detail['harga_satuan'], 0, ',', '.'); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800"><?php echo htmlspecialchars($detail['jumlah']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">Rp <?php echo number_format($detail['harga_satuan'] * $detail['jumlah'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-slate-500">Tidak ada detail produk.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center p-10 bg-white rounded-xl shadow-2xl">
                    <p class="text-xl text-slate-500">Pesanan tidak ditemukan.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

<script>
    function confirmUpdate(selectElement) {
        const status = selectElement.value;
        if (status) {
            if (confirm(`Apakah Anda yakin ingin mengubah status pesanan ini menjadi "${status}"?`)) {
                selectElement.form.submit();
            } else {
                selectElement.value = ""; // Reset pilihan jika dibatalkan
            }
        }
    }
</script>

<?php
// Sertakan template footer
require_once __DIR__ . '/templates/footer.php';
?>
