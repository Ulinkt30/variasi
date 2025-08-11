<?php
/**
 * File: views/user/beranda.php
 * Deskripsi: Halaman beranda utama untuk pengguna.
 * Menampilkan banner, produk terbaru, dan opsi sortir.
 */

// Sertakan file koneksi database dan controller Produk
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/ProdukController.php';

// Inisialisasi controller Produk dengan koneksi database
$produkController = new ProdukController($conn);

// Mendapatkan semua produk dari database
$produk_terbaru = $produkController->readAll();

// Mengambil parameter sortir dari URL. Defaultnya 'terbaru'.
$sort = $_GET['sort'] ?? 'terbaru';
$produks_sorted = $produk_terbaru;

// Logika untuk sorting produk
if ($sort === 'harga_asc') {
    // Mengurutkan produk berdasarkan harga terendah ke tertinggi
    usort($produks_sorted, function($a, $b) {
        return $a['harga'] <=> $b['harga'];
    });
} elseif ($sort === 'harga_desc') {
    // Mengurutkan produk berdasarkan harga tertinggi ke terendah
    usort($produks_sorted, function($a, $b) {
        return $b['harga'] <=> $a['harga'];
    });
}
// Untuk 'terbaru', tidak perlu di-sort karena sudah di-query dari database dalam urutan DESC

// Sertakan template header dan navbar
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/navbar.php';
?>

<div class="container mx-auto p-8 md:pt-0 bg-slate-50">
    <!-- Banner Section -->
    <div class="bg-blue-500 rounded-xl shadow-2xl overflow-hidden mb-12">
        <div class="flex flex-col md:flex-row items-center justify-between p-8 md:p-12">
            <div class="text-center md:text-left mb-6 md:mb-0">
                <!-- Judul untuk desktop -->
                <h1 class="hidden md:block text-4xl font-extrabold text-white">Toko Daniel Variasi Bangko</h1>
                <!-- Judul untuk mobile -->
                <h1 class="block md:hidden text-3xl font-extrabold text-white">Daniel Variasi</h1>
                <p class="text-base md:text-lg text-white mt-2">Dapatkan diskon hingga 50% untuk variasi pilihan.</p>
            </div>
            <a href="/variasi-motor/katalog" class="bg-teal-500 text-white hover:bg-teal-600 font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                Belanja Sekarang
            </a>
        </div>
    </div>
    
    <!-- Bagian Filter dan Sortir -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 border-b border-slate-200 pb-4 space-y-4 md:space-y-0">
        <h2 class="text-3xl font-bold text-slate-800">Produk Terbaru</h2>
        <div class="flex items-center space-x-4">
            <span class="text-slate-800">Sortir:</span>
            <!-- Dropdown untuk opsi sortir dengan tampilan modern -->
            <div class="relative">
                <select onchange="window.location.href = this.value" class="block w-full bg-white text-slate-800 border-2 border-slate-200 rounded-lg py-2 px-4 pr-8 transition-colors duration-300 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="/variasi-motor/" <?php if ($sort === 'terbaru') echo 'selected'; ?>>Terbaru</option>
                    <option value="/variasi-motor/?sort=harga_asc" <?php if ($sort === 'harga_asc') echo 'selected'; ?>>Harga Terendah</option>
                    <option value="/variasi-motor/?sort=harga_desc" <?php if ($sort === 'harga_desc') echo 'selected'; ?>>Harga Tertinggi</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-800">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
            </div>
        </div>
    </div>

    
    <?php if (empty($produks_sorted)): ?>
        <!-- Pesan jika tidak ada produk yang ditemukan -->
        <p class="text-center text-xl text-slate-500">Belum ada produk yang tersedia saat ini.</p>
    <?php else: ?>
        <!-- Menampilkan daftar produk dalam grid -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            <?php foreach ($produks_sorted as $produk): ?>
                <!-- Link ke halaman detail produk -->
                <a href="/variasi-motor/produk/<?php echo htmlspecialchars($produk['id']); ?>" class="block">
                    <!-- Kartu produk -->
                    <div class="bg-white rounded-lg overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <!-- Gambar produk -->
                        <img src="/variasi-motor/assets/uploads/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                             alt="<?php echo htmlspecialchars($produk['nama']); ?>" 
                             class="w-full h-32 md:h-48 object-cover">
                        <!-- Detail produk -->
                        <div class="p-6">
                            <h3 class="text-sm font-semibold text-slate-800 mb-1 overflow-hidden whitespace-nowrap text-ellipsis" style="height: 1.5em; line-height: 1.5em;"><?php echo htmlspecialchars($produk['nama']); ?></h3>
                            <p class="text-sm font-bold text-teal-500">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Sertakan template footer
require_once __DIR__ . '/../templates/footer.php';
?>
