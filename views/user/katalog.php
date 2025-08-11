<?php
/**
 * File: views/user/katalog.php
 * Deskripsi: Halaman tampilan untuk katalog produk pengguna.
 * Menampilkan daftar semua produk yang tersedia menggunakan Tailwind CSS.
 */

// Sertakan file koneksi database dan controller Produk
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/ProdukController.php';
require_once __DIR__ . '/../../models/Kategori.php';

// Inisialisasi controller
$produkController = new ProdukController($conn);
$produks = $produkController->readAll();

// Inisialisasi model Kategori untuk mengambil daftar kategori
$kategori = new Kategori($conn);
$kategori_stmt = $kategori->readAll();
$kategoris = [];
while ($row = $kategori_stmt->fetch(PDO::FETCH_ASSOC)) {
    $kategoris[] = $row;
}

// Mengambil parameter filter dan sorting dari URL
$kategori_filter = $_GET['kategori'] ?? 'Semua';
$sort = $_GET['sort'] ?? 'terbaru';

// Filter produk berdasarkan kategori
$produks_filtered = [];
if ($kategori_filter === 'Semua') {
    $produks_filtered = $produks;
} else {
    // Decode URL untuk mencocokkan nama kategori yang benar
    $decoded_kategori_filter = urldecode($kategori_filter);
    foreach ($produks as $produk) {
        if ($produk['nama_kategori'] === $decoded_kategori_filter) {
            $produks_filtered[] = $produk;
        }
    }
}

// Sorting produk
if ($sort === 'harga_asc') {
    usort($produks_filtered, function($a, $b) {
        return $a['harga'] <=> $b['harga'];
    });
} elseif ($sort === 'harga_desc') {
    usort($produks_filtered, function($a, $b) {
        return $b['harga'] <=> $a['harga'];
    });
}

// --- Logika Pagination ---
$items_per_page = 15;
$total_items = count($produks_filtered);
$total_pages = ceil($total_items / $items_per_page);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // Pastikan halaman berada dalam jangkauan yang valid
$start_index = ($current_page - 1) * $items_per_page;
$paginated_products = array_slice($produks_filtered, $start_index, $items_per_page);

// Sertakan header, navbar, dan template umum lainnya
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/navbar.php';
?>

<div class="container mx-auto p-8 md:pt-0 bg-slate-50">
    <!-- Filter & Sorting Section -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 border-b border-slate-200 pb-4 space-y-4 md:space-y-0">
        <h2 class="text-3xl font-bold text-slate-800">Katalog Produk</h2>
        <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">
            <!-- Dropdown Filter Kategori -->
            <div class="flex items-center space-x-2 w-full md:w-auto">
                <span class="text-slate-800">Filter:</span>
                <div class="relative w-full">
                    <select id="kategori-filter-select" 
                            class="block w-full bg-white text-slate-800 border-2 border-slate-200 rounded-lg py-2 px-4 pr-8 transition-colors duration-300 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Semua" <?php echo ($kategori_filter === 'Semua') ? 'selected' : ''; ?>>Semua Kategori</option>
                        <?php foreach ($kategoris as $kat): ?>
                            <option value="<?php echo urlencode($kat['nama_kategori']); ?>" <?php echo ($kategori_filter === urlencode($kat['nama_kategori'])) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-800">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>
            </div>
            <!-- Dropdown Sortir -->
            <div class="flex items-center space-x-2 w-full md:w-auto">
                <span class="text-slate-800">Sortir:</span>
                <div class="relative w-full">
                    <select id="sort-select" 
                            class="block w-full bg-white text-slate-800 border-2 border-slate-200 rounded-lg py-2 px-4 pr-8 transition-colors duration-300 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="terbaru" <?php echo ($sort === 'terbaru') ? 'selected' : ''; ?>>Terbaru</option>
                        <option value="harga_asc" <?php echo ($sort === 'harga_asc') ? 'selected' : ''; ?>>Harga Terendah</option>
                        <option value="harga_desc" <?php echo ($sort === 'harga_desc') ? 'selected' : ''; ?>>Harga Tertinggi</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-800">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex justify-between items-center mb-6">
        <div class="relative w-full">
            <input type="text" id="search-input" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
        </div>
    </div>

    <?php if (empty($paginated_products)): ?>
        <p id="no-products-message" class="text-center text-xl text-slate-500" style="display: block;">Belum ada produk yang tersedia saat ini.</p>
    <?php else: ?>
        <p id="no-products-message" class="text-center text-xl text-slate-500" style="display: none;">Belum ada produk yang tersedia saat ini.</p>
        <div id="product-list" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            <?php foreach ($paginated_products as $produk): ?>
                <div class="product-card bg-white rounded-lg overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 relative" data-product-name="<?php echo htmlspecialchars(strtolower($produk['nama'])); ?>" data-product-category="<?php echo htmlspecialchars($produk['nama_kategori']); ?>" data-product-price="<?php echo htmlspecialchars($produk['harga']); ?>">
                    <a href="/variasi-motor/produk/<?php echo htmlspecialchars($produk['id']); ?>" class="block">
                        <img src="/variasi-motor/assets/uploads/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                             alt="<?php echo htmlspecialchars($produk['nama']); ?>" 
                             class="w-full h-32 md:h-48 object-cover">
                    </a>
                    <div class="p-4 flex flex-col">
                        <h3 class="text-sm font-semibold text-slate-800 mb-1 overflow-hidden whitespace-nowrap text-ellipsis w-full"><?php echo htmlspecialchars($produk['nama']); ?></h3>
                        <p class="text-sm font-bold text-teal-500">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
                        <form class="mt-2 add-to-cart-form">
                            <input type="hidden" name="produk_id" value="<?php echo htmlspecialchars($produk['id']); ?>">
                            <input type="hidden" name="jumlah" value="1">
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-3 rounded-lg text-xs transition duration-300 flex items-center justify-center">
                                <i class="fas fa-cart-plus mr-1"></i> Beli
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Pagination Controls -->
    <?php if ($total_pages > 1): ?>
    <div class="flex justify-center mt-8 space-x-2">
        <?php
            $query_params = array_merge($_GET, ['page' => $current_page - 1]);
            $prev_url = '?' . http_build_query($query_params);
        ?>
        <a href="<?php echo $prev_url; ?>" class="px-4 py-2 border rounded-lg hover:bg-slate-200 transition-colors duration-200 <?php echo $current_page <= 1 ? 'pointer-events-none bg-slate-100 text-slate-400' : 'bg-white text-slate-800'; ?>">
            &laquo; Sebelumnya
        </a>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php
                $query_params = array_merge($_GET, ['page' => $i]);
                $page_url = '?' . http_build_query($query_params);
            ?>
            <a href="<?php echo $page_url; ?>" class="px-4 py-2 border rounded-lg transition-colors duration-200 <?php echo $i === $current_page ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-slate-800 hover:bg-slate-200'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
        
        <?php
            $query_params = array_merge($_GET, ['page' => $current_page + 1]);
            $next_url = '?' . http_build_query($query_params);
        ?>
        <a href="<?php echo $next_url; ?>" class="px-4 py-2 border rounded-lg hover:bg-slate-200 transition-colors duration-200 <?php echo $current_page >= $total_pages ? 'pointer-events-none bg-slate-100 text-slate-400' : 'bg-white text-slate-800'; ?>">
            Selanjutnya &raquo;
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Notifikasi -->
<div id="notifikasi-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity"></div>
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-sm w-full z-50 text-center">
            <h2 id="modal-notif-title" class="text-2xl font-bold mb-4 text-slate-800">Notifikasi</h2>
            <p id="modal-notif-message" class="text-slate-500 mb-6"></p>
            <button id="close-notif-modal-btn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Tutup</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const initialProducts = <?php echo json_encode($produks); ?>;
        const kategoriSelect = document.getElementById('kategori-filter-select');
        const sortSelect = document.getElementById('sort-select');
        const searchInput = document.getElementById('search-input');
        const productList = document.getElementById('product-list');
        const noProductsMessage = document.getElementById('no-products-message');
        
        function renderProducts(products) {
            productList.innerHTML = '';
            if (products.length === 0) {
                noProductsMessage.style.display = 'block';
            } else {
                noProductsMessage.style.display = 'none';
                products.forEach(produk => {
                    const productCard = document.createElement('div');
                    productCard.className = 'bg-white rounded-lg overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 relative product-card';
                    productCard.setAttribute('data-product-name', produk.nama.toLowerCase());
                    productCard.setAttribute('data-product-category', produk.nama_kategori);
                    productCard.setAttribute('data-product-price', produk.harga);

                    productCard.innerHTML = `
                        <a href="/variasi-motor/produk/${produk.id}" class="block">
                            <img src="/variasi-motor/assets/uploads/${produk.gambar}" 
                                 alt="${produk.nama}" 
                                 class="w-full h-32 md:h-48 object-cover">
                        </a>
                        <div class="p-4 flex flex-col">
                            <h3 class="text-sm font-semibold text-slate-800 mb-1 overflow-hidden whitespace-nowrap text-ellipsis w-full">${produk.nama}</h3>
                            <p class="text-sm font-bold text-teal-500">Rp ${new Intl.NumberFormat('id-ID').format(produk.harga)}</p>
                            <form class="mt-2 add-to-cart-form">
                                <input type="hidden" name="produk_id" value="${produk.id}">
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-3 rounded-lg text-xs transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-cart-plus mr-1"></i> Beli
                                </button>
                            </form>
                        </div>
                    `;
                    productList.appendChild(productCard);
                });
            }
        }
        
        // Fungsi untuk memfilter dan menyortir produk secara real-time
        function filterAndSortProducts() {
            const currentSearch = searchInput.value.toLowerCase();
            const currentKategori = kategoriSelect.value === 'Semua' ? 'Semua' : decodeURIComponent(kategoriSelect.value);
            const currentSort = sortSelect.value;
            
            let filteredProducts = initialProducts.filter(produk => {
                const matchKategori = currentKategori === 'Semua' || produk.nama_kategori === currentKategori;
                const matchSearch = produk.nama.toLowerCase().includes(currentSearch);
                return matchKategori && matchSearch;
            });

            if (currentSort === 'harga_asc') {
                filteredProducts.sort((a, b) => a.harga - b.harga);
            } else if (currentSort === 'harga_desc') {
                filteredProducts.sort((a, b) => b.harga - a.harga);
            } else {
                // Default sorting: terbaru (descending ID)
                filteredProducts.sort((a, b) => b.id - a.id);
            }
            
            // Render produk yang sudah di-filter dan di-sortir
            renderProducts(filteredProducts);
        }

        // Tambahkan event listener ke setiap dropdown
        kategoriSelect.addEventListener('change', filterAndSortProducts);
        sortSelect.addEventListener('change', filterAndSortProducts);
        searchInput.addEventListener('keyup', filterAndSortProducts);

        // Logika untuk form tambah ke keranjang
        const forms = document.querySelectorAll('.add-to-cart-form');
        const notifikasiModal = document.getElementById('notifikasi-modal');
        const modalNotifMessage = document.getElementById('modal-notif-message');
        const modalNotifTitle = document.getElementById('modal-notif-title');
        const closeNotifBtn = document.getElementById('close-notif-modal-btn');
        
        closeNotifBtn.addEventListener('click', () => {
            notifikasiModal.classList.add('hidden');
        });

        // Delegate event listener to a parent element to handle dynamically added forms
        document.getElementById('product-list').addEventListener('submit', async (e) => {
            if (e.target.classList.contains('add-to-cart-form')) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                
                try {
                    const response = await fetch('/variasi-motor/keranjang', {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (response.ok) {
                        modalNotifTitle.textContent = 'Sukses!';
                        modalNotifMessage.textContent = 'Produk berhasil ditambahkan ke keranjang.';
                    } else {
                        const errorText = await response.text();
                        modalNotifTitle.textContent = 'Gagal!';
                        modalNotifMessage.textContent = `Gagal menambahkan produk: ${errorText}`;
                    }
                    
                } catch (error) {
                    modalNotifTitle.textContent = 'Error!';
                    modalNotifMessage.textContent = 'Terjadi kesalahan jaringan.';
                } finally {
                    notifikasiModal.classList.remove('hidden');
                }
            }
        });
        
        // Panggil fungsi filter dan sortir saat halaman pertama kali dimuat
        // Note: Filter dan sortir yang ada di URL akan diproses oleh PHP,
        // jadi kita tidak perlu memanggil filterAndSortProducts() di sini saat DOMContentLoaded
    });
</script>

<?php
// Sertakan footer
require_once __DIR__ . '/../templates/footer.php';
?>
