<?php
/**
 * File: views/user/produk_detail.php
 * Deskripsi: Halaman tampilan untuk detail satu produk.
 * Mengambil ID produk dari URL, menampilkan informasi lengkap,
 * dan menyediakan opsi untuk menambahkan ke keranjang dengan jumlah yang dapat diubah.
 */

// Sertakan file koneksi database dan controller Produk
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/ProdukController.php';

// Inisialisasi controller
$produkController = new ProdukController($conn);

// Mendapatkan ID produk dari URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));
$base_dir = 'variasi-motor';

// Temukan indeks ID produk
$id_index = array_search($base_dir, $path_parts) + 2;

// Pastikan ID produk ada di URL
if (isset($path_parts[$id_index])) {
    $produk_id = $path_parts[$id_index];
    $produk = $produkController->readOne($produk_id);
} else {
    $produk = null;
}

// Sertakan header, navbar, dan template umum lainnya
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/navbar.php';
?>

<div class="container mx-auto bg-slate-50 text-slate-800">
    <?php if ($produk): ?>
        <div class="lg:flex lg:space-x-8">
            <!-- Kolom Kiri: Gambar -->
            <div class="lg:w-1/3 flex flex-col items-center mb-8 lg:mb-0">
                <!-- Kontainer gambar, memiliki gaya desktop dan tidak ada gaya pada mobile -->
                <div class="w-full lg:p-6 lg:bg-white lg:rounded-lg lg:shadow-md sticky lg:top-24">
                    <img src="/variasi-motor/assets/uploads/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                         alt="<?php echo htmlspecialchars($produk['nama']); ?>" 
                         class="w-full h-auto object-cover lg:rounded-lg">
                </div>
            </div>

            <!-- Kolom Tengah: Judul dan Deskripsi Produk -->
            <div class="lg:w-2/5 flex flex-col mb-8 lg:mb-0">
                <div class="p-6 w-full h-full bg-white rounded-lg shadow-md">
                    <!-- Judul produk dengan ellipsis 3 baris di mobile, normal di desktop -->
                    <h1 class="text-2xl font-bold mb-4 text-slate-800 line-clamp-3 lg:line-clamp-none"><?php echo htmlspecialchars($produk['nama']); ?></h1>
                    <p class="text-xl font-bold mb-4 text-blue-500">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
                    <h2 class="text-xl font-bold mb-4 text-slate-800">Deskripsi Produk</h2>
                    <div id="deskripsi-container">
                        <!-- Deskripsi pendek dengan ellipsis 3 baris di mobile, normal di desktop -->
                        <p id="deskripsi-pendek" class="text-slate-500 overflow-hidden line-clamp-3 lg:line-clamp-none" style="-webkit-box-orient: vertical; display: -webkit-box;">
                            <?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?>
                        </p>
                        <p id="deskripsi-lengkap" class="text-slate-500 hidden">
                            <?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?>
                        </p>
                        <?php if (strlen($produk['deskripsi']) > 150): ?>
                            <button id="lihat-selengkapnya-btn" onclick="toggleDeskripsi()" class="text-blue-500 hover:underline mt-2 focus:outline-none">
                                Lihat selengkapnya
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Detail Produk, Opsi Pembelian -->
            <div class="lg:w-1/5">
                <!-- Kontainer ini terlihat di semua ukuran layar -->
                <div class="p-6 sticky lg:top-24 max-w-sm mx-auto bg-white rounded-lg shadow-md">
                    <div class="mb-4">
                        <p class="text-lg text-slate-500">Total Harga:</p>
                        <!-- Perbaikan: Menghapus "Rp " statis dari sini -->
                        <p class="text-xl font-bold text-blue-500"><span id="total-harga"><?php echo number_format($produk['harga'], 0, ',', '.'); ?></span></p>
                        <p class="text-sm font-semibold text-green-500">Stok: <?php echo htmlspecialchars($produk['stok']); ?></p>
                    </div>

                    <!-- Form untuk menambahkan ke keranjang dengan input jumlah -->
                    <form action="/variasi-motor/keranjang" method="POST" class="mb-4">
                        <input type="hidden" name="produk_id" value="<?php echo htmlspecialchars($produk['id']); ?>">
                        
                        <div class="flex items-center space-x-4 mb-6">
                            <label for="jumlah" class="text-lg font-semibold text-slate-800">Jumlah:</label>
                            <div class="flex items-center border border-slate-200 rounded-md overflow-hidden">
                                <button type="button" onclick="ubahJumlah(-1)" class="bg-slate-100 text-blue-500 p-2 hover:bg-slate-200 transition-colors duration-200">-</button>
                                <input type="number" id="jumlah" name="jumlah" value="1" min="1" max="<?php echo htmlspecialchars($produk['stok']); ?>" required class="w-16 text-center bg-white text-slate-800 focus:outline-none border-x border-slate-200">
                                <button type="button" onclick="ubahJumlah(1)" class="bg-slate-100 text-blue-500 p-2 hover:bg-slate-200 transition-colors duration-200">+</button>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-md transition duration-300 transform hover:scale-105">
                            <i class="fas fa-shopping-cart mr-2"></i> + Keranjang
                        </button>
                    </form>

                    <!-- Tombol Langsung Checkout -->
                    <form action="/variasi-motor/checkout" method="GET">
                        <input type="hidden" name="produk_id" value="<?php echo htmlspecialchars($produk['id']); ?>">
                        <input type="hidden" name="jumlah" id="jumlah-checkout" value="1">
                        <button type="submit" 
                                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-md transition duration-300 transform hover:scale-105 mt-4">
                            Checkout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center p-10 bg-white rounded-lg shadow-md">
            <h1 class="text-4xl font-bold text-slate-800 mb-4">Produk Tidak Ditemukan</h1>
            <p class="text-xl text-slate-500">Maaf, produk yang Anda cari tidak tersedia.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    function ubahJumlah(val) {
        const input = document.getElementById('jumlah');
        const inputCheckout = document.getElementById('jumlah-checkout');
        let jumlah = parseInt(input.value);
        let stok = parseInt(input.max);
        
        jumlah += val;

        if (jumlah < 1) {
            jumlah = 1;
        }
        if (jumlah > stok) {
            jumlah = stok;
        }
        
        input.value = jumlah;
        inputCheckout.value = jumlah;
        hitungTotalHarga();
    }
    
    function hitungTotalHarga() {
        const hargaSatuan = <?php echo htmlspecialchars($produk['harga']); ?>;
        const jumlah = parseInt(document.getElementById('jumlah').value);
        const totalHarga = hargaSatuan * jumlah;
        
        // Memformat ulang harga ke format Rupiah
        const formattedTotalHarga = totalHarga.toLocaleString('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).replace('IDR', '').trim();

        document.getElementById('total-harga').innerText = formattedTotalHarga;
    }

    function toggleDeskripsi() {
        const shortDesc = document.getElementById('deskripsi-pendek');
        const longDesc = document.getElementById('deskripsi-lengkap');
        const button = document.getElementById('lihat-selengkapnya-btn');

        if (shortDesc.classList.contains('hidden')) {
            shortDesc.classList.remove('hidden');
            longDesc.classList.add('hidden');
            button.innerText = 'Lihat selengkapnya';
        } else {
            shortDesc.classList.add('hidden');
            longDesc.classList.remove('hidden');
            button.innerText = 'Tutup';
        }
    }
    
    // Panggil fungsi ini saat halaman dimuat untuk memastikan harga awal benar
    window.onload = () => {
        hitungTotalHarga();
        document.getElementById('jumlah').addEventListener('change', hitungTotalHarga);
        // Sinkronisasi nilai input jumlah dengan input checkout
        document.getElementById('jumlah').addEventListener('change', (e) => {
            document.getElementById('jumlah-checkout').value = e.target.value;
        });
    };
</script>

<?php
// Sertakan footer
require_once __DIR__ . '/../templates/footer.php';
?>
