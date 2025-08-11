<?php
/**
 * File: views/user/checkout.php
 * Deskripsi: Halaman tampilan untuk proses checkout.
 * Menampilkan ringkasan pesanan dari keranjang belanja atau checkout langsung.
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sertakan file koneksi database dan controller yang diperlukan
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/ProdukController.php';
require_once __DIR__ . '/../../controllers/KeranjangController.php';
require_once __DIR__ . '/../../controllers/PesananController.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: /variasi-motor/login");
    exit();
}

// Inisialisasi controller
$produkController = new ProdukController($conn);
$keranjangController = new KeranjangController($conn);
$pesananController = new PesananController($conn);

// Tentukan apakah ini checkout langsung atau dari keranjang
$checkout_items = [];
$total_harga = 0;

// Cek jika ini adalah checkout langsung dari halaman produk_detail (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['produk_id']) && isset($_GET['jumlah'])) {
    $produk_id = htmlspecialchars($_GET['produk_id']);
    $jumlah = htmlspecialchars($_GET['jumlah']);
    $produk = $produkController->readOne($produk_id);
    
    if ($produk) {
        $produk['jumlah'] = $jumlah;
        $checkout_items[] = $produk;
        $total_harga = $produk['harga'] * $produk['jumlah'];
    }
}
// Cek jika ini adalah checkout dari keranjang dengan item terpilih (POST)
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_items'])) {
    $selected_items_json = $_POST['selected_items'];
    $selected_items = json_decode($selected_items_json, true);

    if (is_array($selected_items) && !empty($selected_items)) {
        foreach ($selected_items as $item) {
            $produk = $produkController->readOne($item['id']);
            if ($produk) {
                $produk['jumlah'] = $item['jumlah'];
                $checkout_items[] = $produk;
                $total_harga += $produk['harga'] * $produk['jumlah'];
            }
        }
    }
}
// Fallback: Jika tidak ada parameter spesifik, set item checkout menjadi kosong
else {
    $checkout_items = [];
    $total_harga = 0;
}


// Sertakan template header dan navbar
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/navbar.php';
?>

<style>
    /* Modal styles */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 999;
    }
    .modal-content {
        background-color: #FFFFFF; /* White for modal background */
        padding: 2rem;
        border-radius: 0.5rem;
        text-align: center;
        max-width: 400px;
        width: 90%;
        color: #1E293B; /* Dark text for modal */
    }
</style>

<div class="container mx-auto bg-slate-50 text-slate-800 min-h-screen">
    

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="bg-green-500 text-white p-3 rounded-lg text-center mb-6">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="bg-red-500 text-white p-3 rounded-lg text-center mb-6">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>

    <?php if (empty($checkout_items)): ?>
        <div class="text-center p-10 bg-white rounded-lg shadow-2xl">
            <p class="text-xl text-slate-500">Keranjang Anda kosong. Tidak ada produk untuk checkout.</p>
            <a href="/variasi-motor/katalog" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-full mt-6 inline-block transition duration-300">
                Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <!-- Form ini sekarang mengarah ke endpoint khusus untuk memproses pesanan -->
        <form id="checkout-form" action="/variasi-motor/proses-checkout" method="POST" enctype="multipart/form-data">
            <div class="lg:flex lg:space-x-8">
                <!-- Ringkasan Pesanan & Alamat Pengiriman -->
                <div class="lg:w-2/3 bg-white p-8 rounded-xl shadow-2xl mb-8 lg:mb-0">
                    <h2 class="text-3xl font-bold mb-6 text-slate-800">Checkout</h2>
                    
                    <div class="space-y-4 mb-8">
                        <div>
                            <label for="nama_penerima" class="block text-slate-500 font-semibold mb-2">Nama Penerima</label>
                            <input type="text" id="nama_penerima" name="nama_penerima" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="alamat" class="block text-slate-500 font-semibold mb-2">Alamat Lengkap</label>
                            <textarea id="alamat" name="alamat" rows="4" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label for="telepon" class="block text-slate-500 font-semibold mb-2">Nomor Telepon</label>
                            <input type="tel" id="telepon" name="telepon" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <h2 class="text-3xl font-bold mb-6 text-slate-800">Produk Dipesan</h2>
                    <div class="overflow-x-auto mb-8">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <?php foreach ($checkout_items as $item): ?>
                                    <input type="hidden" name="produk_id[]" value="<?php echo htmlspecialchars($item['id']); ?>">
                                    <input type="hidden" name="jumlah[]" value="<?php echo htmlspecialchars($item['jumlah']); ?>">
                                    <input type="hidden" name="harga_satuan[]" value="<?php echo htmlspecialchars($item['harga']); ?>">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover" src="/variasi-motor/assets/uploads/<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-slate-800 overflow-hidden whitespace-nowrap text-ellipsis" style="max-width: 200px;"><?php echo htmlspecialchars($item['nama']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                            Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                            <?php echo htmlspecialchars($item['jumlah']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                            Rp <?php echo number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ringkasan Total & Pembayaran -->
                <div class="lg:w-1/3 bg-white p-8 rounded-xl shadow-2xl">
                    <h2 class="text-3xl font-bold mb-6 text-slate-800">Ringkasan</h2>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-xl font-medium text-slate-500">Total Harga:</span>
                        <span class="text-2xl font-bold text-blue-500">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></span>
                    </div>
                    
                    <h3 class="text-xl font-bold mt-8 mb-4 text-slate-800">Metode Pembayaran</h3>
                    <div class="bg-slate-50 p-4 rounded-lg">
                        <p class="text-slate-500 mb-2">Transfer Bank BCA</p>
                        <p class="text-lg font-semibold text-slate-800 flex items-center">
                            <span id="nomor-rekening">92183130484</span>
                            <button type="button" onclick="copyToClipboard('92183130484')" class="ml-2 text-blue-500 hover:text-blue-600 transition-colors duration-200">
                                <i class="fas fa-copy"></i>
                            </button>
                        </p>
                    </div>

                    <!-- Formulir Unggah Bukti Pembayaran -->
                    <h3 class="text-xl font-bold mt-8 mb-4 text-slate-800">Unggah Bukti Pembayaran</h3>
                    <div>
                        <label for="bukti_pembayaran" class="block text-slate-500 font-semibold mb-2">Pilih File Gambar</label>
                        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" required class="w-full mt-1 text-slate-800">
                    </div>

                    <!-- Tombol Buat Pesanan -->
                    <button type="button" id="submit-checkout-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-8 rounded-full transition duration-300 transform hover:scale-105 mt-8">
                        <i class="fas fa-money-check-alt mr-2"></i> Buat Pesanan
                    </button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<!-- Modal Konfirmasi -->
<div id="konfirmasi-modal" class="modal-backdrop">
    <div class="modal-content">
        <h2 class="text-lg font-bold mb-4 text-slate-800">Konfirmasi Pesanan</h2>
        <p class="text-sm text-slate-500 mb-6">Apakah Anda yakin ingin membuat pesanan? Pastikan data sudah benar dan bukti pembayaran sudah diunggah.</p>
        <div class="flex justify-center space-x-4">
            <button id="batal-btn" class="bg-white text-slate-800 border border-slate-200 hover:bg-slate-50 font-bold py-2 px-6 rounded-md">Batal</button>
            <button id="lanjutkan-btn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md">Ya, Buat Pesanan</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const submitBtn = document.getElementById('submit-checkout-btn');
        const checkoutForm = document.getElementById('checkout-form');
        const modalBackdrop = document.getElementById('konfirmasi-modal');
        const batalBtn = document.getElementById('batal-btn');
        const lanjutkanBtn = document.getElementById('lanjutkan-btn');

        submitBtn.addEventListener('click', (e) => {
            // Cek validitas formulir sebelum menampilkan modal
            if (checkoutForm.checkValidity()) {
                e.preventDefault(); // Mencegah submit form bawaan
                modalBackdrop.style.display = 'flex';
            } else {
                // Biarkan form menampilkan pesan error HTML5
                checkoutForm.reportValidity();
            }
        });

        batalBtn.addEventListener('click', () => {
            modalBackdrop.style.display = 'none';
        });

        lanjutkanBtn.addEventListener('click', () => {
            modalBackdrop.style.display = 'none';
            checkoutForm.submit();
        });
    });

    function copyToClipboard(text) {
        const el = document.createElement('textarea');
        el.value = text;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        console.log('Nomor rekening berhasil disalin!');
    }
</script>

<?php
// Sertakan footer
require_once __DIR__ . '/../templates/footer.php';
?>
