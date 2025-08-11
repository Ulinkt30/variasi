<?php
/**
 * File: views/admin/kelola_produk.php
 * Deskripsi: Halaman admin untuk mengelola produk (CRUD)
 * dalam satu tampilan dengan tab untuk menambah, melihat, dan mengelola kategori.
 */

// Sertakan file koneksi database dan controller Produk serta Kategori
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/ProdukController.php';
require_once __DIR__ . '/../../models/Kategori.php';
require_once __DIR__ . '/../../models/Produk.php';

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
$produkController = new ProdukController($conn);

// Inisialisasi model Kategori untuk mengambil daftar kategori
$kategori = new Kategori($conn);
$kategori_stmt = $kategori->readAll();
$kategoris = [];
while ($row = $kategori_stmt->fetch(PDO::FETCH_ASSOC)) {
    $kategoris[] = $row;
}

// Ambil semua produk
$produks = $produkController->readAll();

// Sertakan template header
require_once __DIR__ . '/templates/header.php';
?>

    <div class="flex">
        <!-- Sidebar Admin -->
        <?php require_once __DIR__ . '/templates/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8 bg-slate-50 text-slate-800">
            <header class="flex justify-between items-center pb-4 border-b border-slate-200 mb-6">
                <h1 class="text-3xl font-bold">Kelola Produk</h1>
                <p class="text-slate-500">Selamat datang, <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>!</p>
            </header>

            <?php
            // Tampilkan pesan sukses atau error jika ada
            $show_tab = isset($_GET['tab']) ? htmlspecialchars($_GET['tab']) : 'tab1';
            if (isset($_SESSION['success_message'])) {
                echo '<div class="bg-green-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
                $show_tab = 'tab2';
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="bg-red-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
                $show_tab = 'tab2';
            }
            ?>

            <!-- Tabs Navigation -->
            <div class="bg-white p-4 rounded-t-xl shadow-xl">
                <div class="flex space-x-4">
                    <button id="tab1-button" class="tab-button px-4 py-2 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-200" onclick="showTab('tab1')">
                        Tambah Produk
                    </button>
                    <button id="tab2-button" class="tab-button px-4 py-2 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-200" onclick="showTab('tab2')">
                        Daftar Produk
                    </button>
                    <button id="tab3-button" class="tab-button px-4 py-2 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-200" onclick="showTab('tab3')">
                        Kelola Kategori
                    </button>
                </div>
            </div>

            <!-- Tabs Content -->
            <div id="tab1" class="tab-content bg-white p-6 rounded-b-xl shadow-xl mb-8">
                <!-- Form Tambah Produk -->
                <h2 class="text-2xl font-bold mb-6 text-slate-800">Tambah Produk Baru</h2>
                <form action="/variasi-motor/admin/kelola_produk" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="md:col-span-1">
                        <label for="nama" class="block text-slate-500 font-semibold mb-2 flex items-center">
                            <i class="fas fa-box-open mr-2"></i> Nama Produk
                        </label>
                        <input type="text" id="nama" name="nama" required class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                    </div>
                    <div class="md:col-span-1">
                        <label for="kategori_id" class="block text-slate-500 font-semibold mb-2 flex items-center">
                            <i class="fas fa-tags mr-2"></i> Kategori (Opsional)
                        </label>
                        <select id="kategori_id" name="kategori_id" class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategoris as $kat): ?>
                                <option value="<?php echo htmlspecialchars($kat['id']); ?>"><?php echo htmlspecialchars($kat['nama_kategori']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="deskripsi" class="block text-slate-500 font-semibold mb-2 flex items-center">
                            <i class="fas fa-file-alt mr-2"></i> Deskripsi (Opsional)
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200"></textarea>
                    </div>
                    <div class="md:col-span-1">
                        <label for="harga_display" class="block text-slate-500 font-semibold mb-2 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i> Harga
                        </label>
                        <input type="text" id="harga_display" name="harga_display" required class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200" oninput="formatRupiah(this)">
                        <input type="hidden" id="harga" name="harga">
                    </div>
                    <div class="md:col-span-1">
                        <label for="stok" class="block text-slate-500 font-semibold mb-2 flex items-center">
                            <i class="fas fa-cubes mr-2"></i> Stok
                        </label>
                        <input type="number" id="stok" name="stok" required class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                    </div>
                    <div class="md:col-span-2">
                        <label for="gambar" class="block text-slate-500 font-semibold mb-2 flex items-center">
                            <i class="fas fa-image mr-2"></i> Gambar
                        </label>
                        <input type="file" id="gambar" name="gambar" required class="w-full text-slate-800 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div class="md:col-span-2 mt-4">
                        <button type="submit" name="create" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105">
                            <i class="fas fa-plus mr-2"></i> Tambah Produk
                        </button>
                    </div>
                </form>
            </div>

            <div id="tab2" class="tab-content bg-white p-6 rounded-b-xl shadow-xl mb-8">
                <!-- Daftar Produk -->
                <h2 class="text-2xl font-bold mb-4 text-slate-800">Daftar Produk</h2>
                <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-4 md:space-y-0">
                    <p class="text-sm text-slate-500">Total Produk: <span id="total-produk-count" class="font-bold"><?php echo count($produks); ?></span></p>
                    <div class="relative w-full md:w-auto">
                        <input type="text" id="search-produk" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse border border-slate-200 rounded-xl overflow-hidden" id="produk-table">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(0)">
                                    ID <i class="fas fa-sort ml-1 sort-icon" data-col="0"></i>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Gambar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(2)">
                                    Nama <i class="fas fa-sort ml-1 sort-icon" data-col="2"></i>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(4)">
                                    Harga <i class="fas fa-sort ml-1 sort-icon" data-col="4"></i>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(5)">
                                    Stok <i class="fas fa-sort ml-1 sort-icon" data-col="5"></i>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php if (empty($produks)): ?>
                                <tr id="no-produk-row">
                                    <td colspan="7" class="px-6 py-4 text-center text-slate-500">Belum ada produk.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($produks as $produk): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800"><?php echo htmlspecialchars($produk['id']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <img src="/variasi-motor/assets/uploads/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama']); ?>" class="h-12 w-12 rounded-full object-cover shadow-md">
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-800 overflow-hidden text-ellipsis max-w-[200px]"><?php echo htmlspecialchars($produk['nama']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800"><?php echo htmlspecialchars($produk['nama_kategori']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800"><?php echo htmlspecialchars($produk['stok']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <!-- Tombol Edit dan Hapus -->
                                            <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($produk)); ?>)" class="text-blue-500 hover:text-blue-700 mx-2 transition-colors duration-200">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="openDeleteModal(<?php echo htmlspecialchars($produk['id']); ?>)" class="text-red-500 hover:text-red-700 mx-2 transition-colors duration-200">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab3" class="tab-content bg-white p-6 rounded-b-xl shadow-xl mb-8">
                <!-- Form Tambah Kategori Baru -->
                <h2 class="text-2xl font-bold mb-6">Kelola Kategori</h2>
                <form action="/variasi-motor/admin/kelola_kategori" method="POST" class="space-y-4 mb-8">
                    <div>
                        <label for="nama_kategori" class="block text-slate-500">Nama Kategori</label>
                        <input type="text" id="nama_kategori" name="nama_kategori" required class="w-full bg-slate-100 border border-slate-200 rounded-lg px-4 py-2 mt-1 text-slate-800">
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                        Tambah Kategori
                    </button>
                </form>

                <!-- Daftar Kategori -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php if (empty($kategoris)): ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-slate-500">Belum ada kategori.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($kategoris as $kat): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"><?php echo htmlspecialchars($kat['id']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800"><?php echo htmlspecialchars($kat['nama_kategori']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <!-- Tombol Edit dan Hapus -->
                                            <button onclick="openEditKategoriModal(<?php echo htmlspecialchars(json_encode($kat)); ?>)" class="text-blue-500 hover:text-blue-700">Edit</button>
                                            <form action="/variasi-motor/admin/kelola_kategori/hapus" method="POST" class="inline">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($kat['id']); ?>">
                                                <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                            </form>
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

<!-- Edit Produk Modal -->
<div id="editModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" onclick="closeEditModal()"></div>
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-lg w-full z-50">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Edit Produk</h2>
                <button onclick="closeEditModal()" class="text-slate-500 hover:text-slate-800 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="edit-form" action="/variasi-motor/admin/kelola_produk/update" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                <input type="hidden" name="id" id="edit-id">
                <div class="md:col-span-1">
                    <label for="edit-nama" class="block text-slate-500 font-semibold mb-2 flex items-center">
                        <i class="fas fa-box-open mr-2"></i> Nama Produk
                    </label>
                    <input type="text" id="edit-nama" name="nama" required class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                </div>
                <div class="md:col-span-1">
                    <label for="edit-kategori_id" class="block text-slate-500 font-semibold mb-2 flex items-center">
                        <i class="fas fa-tags mr-2"></i> Kategori
                    </label>
                    <select id="edit-kategori_id" name="kategori_id" required class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoris as $kat): ?>
                            <option value="<?php echo htmlspecialchars($kat['id']); ?>"><?php echo htmlspecialchars($kat['nama_kategori']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="edit-deskripsi" class="block text-slate-500 font-semibold mb-2 flex items-center">
                        <i class="fas fa-file-alt mr-2"></i> Deskripsi
                    </label>
                    <textarea id="edit-deskripsi" name="deskripsi" rows="3" class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200"></textarea>
                </div>
                <div class="md:col-span-1">
                    <label for="edit-harga-display" class="block text-slate-500 font-semibold mb-2 flex items-center">
                        <i class="fas fa-money-bill-wave mr-2"></i> Harga
                    </label>
                    <input type="text" id="edit-harga-display" name="harga-display" required class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200" oninput="formatRupiahEdit(this)">
                    <input type="hidden" id="edit-harga" name="harga">
                </div>
                <div class="md:col-span-1">
                    <label for="edit-stok" class="block text-slate-500 font-semibold mb-2 flex items-center">
                        <i class="fas fa-cubes mr-2"></i> Stok
                    </label>
                    <input type="number" id="edit-stok" name="stok" required class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                </div>
                <div>
                    <label for="edit-gambar" class="block text-slate-500 font-semibold mb-2 flex items-center">
                        <i class="fas fa-image mr-2"></i> Gambar (Kosongkan jika tidak ingin diubah)
                    </label>
                    <input type="file" id="edit-gambar" name="gambar" class="w-full mt-1 text-slate-800">
                </div>
                <div class="md:col-span-2 mt-4">
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Kategori Modal -->
<div id="editKategoriModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" onclick="closeEditKategoriModal()"></div>
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-sm w-full z-50">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Edit Kategori</h2>
                <button onclick="closeEditKategoriModal()" class="text-slate-500 hover:text-slate-800 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="edit-kategori-form" action="/variasi-motor/admin/kelola_kategori/update" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="edit-kategori-id">
                <div>
                    <label for="edit-nama_kategori" class="block text-slate-500">Nama Kategori</label>
                    <input type="text" id="edit-nama_kategori" name="nama_kategori" required class="w-full bg-slate-100 border border-slate-200 rounded-lg px-4 py-2 mt-1 text-slate-800">
                </div>
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-sm w-full z-50">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Konfirmasi Hapus</h2>
                <button onclick="closeDeleteModal()" class="text-slate-500 hover:text-slate-800 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-slate-500 mb-6">Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeDeleteModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold py-2 px-4 rounded-lg transition duration-300">Batal</button>
                <form id="delete-form" action="/variasi-motor/admin/kelola_produk/hapus" method="POST">
                    <input type="hidden" name="id" id="delete-id">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Data produk dari PHP untuk digunakan di JavaScript
    const initialProducts = <?php echo json_encode($produks); ?>;
    let currentProducts = [...initialProducts];
    let sortDirection = {};

    function showTab(tabId) {
        // Sembunyikan semua tab content
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.style.display = 'none';
        });

        // Tampilkan tab content yang dipilih
        document.getElementById(tabId).style.display = 'block';

        // Perbarui gaya tombol tab
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('bg-blue-500', 'text-white');
            button.classList.add('text-slate-500', 'hover:bg-slate-100');
        });
        document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('bg-blue-500', 'text-white');
        document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.remove('text-slate-500', 'hover:bg-slate-100');
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Logika untuk menampilkan tab yang benar saat halaman dimuat
        const showTabOnLoad = '<?php echo isset($_GET["tab"]) ? htmlspecialchars($_GET["tab"]) : "tab1"; ?>';
        showTab(showTabOnLoad);

        // JavaScript untuk fitur pencarian
        const searchInput = document.getElementById('search-produk');
        const totalProdukCount = document.getElementById('total-produk-count');

        searchInput.addEventListener('keyup', () => {
            const searchTerm = searchInput.value.toLowerCase();
            const filteredProducts = initialProducts.filter(produk => {
                return produk.nama.toLowerCase().includes(searchTerm);
            });
            currentProducts = filteredProducts;
            renderTable(currentProducts);
        });
        
        // Event listeners untuk modal edit produk
        window.openEditModal = function(produk) {
            document.getElementById('edit-id').value = produk.id;
            document.getElementById('edit-nama').value = produk.nama;
            document.getElementById('edit-kategori_id').value = produk.kategori_id;
            document.getElementById('edit-deskripsi').value = produk.deskripsi;
            document.getElementById('edit-harga').value = produk.harga;
            document.getElementById('edit-stok').value = produk.stok;
            document.getElementById('edit-harga-display').value = new Intl.NumberFormat('id-ID').format(produk.harga);
            document.getElementById('editModal').classList.remove('hidden');
        };

        window.closeEditModal = function() {
            document.getElementById('editModal').classList.add('hidden');
        };
        
        // Event listeners untuk modal edit kategori
        window.openEditKategoriModal = function(kategori) {
            document.getElementById('edit-kategori-id').value = kategori.id;
            document.getElementById('edit-nama_kategori').value = kategori.nama_kategori;
            document.getElementById('editKategoriModal').classList.remove('hidden');
        };
        
        window.closeEditKategoriModal = function() {
            document.getElementById('editKategoriModal').classList.add('hidden');
        };

        // Event listeners untuk modal hapus produk
        window.openDeleteModal = function(produkId) {
            document.getElementById('delete-id').value = produkId;
            document.getElementById('deleteModal').classList.remove('hidden');
        };
        
        window.closeDeleteModal = function() {
            document.getElementById('deleteModal').classList.add('hidden');
        };

        renderTable(initialProducts);
    });
    
    /**
     * Memformat input angka menjadi format Rupiah.
     * Menggunakan titik sebagai pemisah ribuan.
     */
    function formatRupiah(input) {
        let rawValue = input.value.replace(/\D/g, '');
        document.getElementById('harga').value = rawValue;
        let formattedValue = new Intl.NumberFormat('id-ID').format(rawValue);
        input.value = formattedValue;
    }

    /**
     * Memformat input harga di modal edit menjadi format Rupiah.
     */
    function formatRupiahEdit(input) {
        let rawValue = input.value.replace(/\D/g, '');
        document.getElementById('edit-harga').value = rawValue;
        let formattedValue = new Intl.NumberFormat('id-ID').format(rawValue);
        input.value = formattedValue;
    }
    
    // Fungsi untuk merender tabel produk berdasarkan array data
    function renderTable(products) {
        const tableBody = document.querySelector('#produk-table tbody');
        const totalProdukCount = document.getElementById('total-produk-count');
        tableBody.innerHTML = '';
        totalProdukCount.textContent = products.length;

        if (products.length === 0) {
            const noProductRow = document.createElement('tr');
            noProductRow.id = 'no-produk-row';
            noProductRow.innerHTML = `<td colspan="7" class="px-6 py-4 text-center text-slate-500">Belum ada produk.</td>`;
            tableBody.appendChild(noProductRow);
        } else {
            products.forEach(produk => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50 transition-colors duration-200';
                const kategoriText = produk.nama_kategori ? produk.nama_kategori : 'Tidak ada kategori';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800">${produk.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="/variasi-motor/assets/uploads/${produk.gambar}" alt="${produk.nama}" class="h-12 w-12 rounded-full object-cover shadow-md">
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-800 overflow-hidden text-ellipsis max-w-[200px]">${produk.nama}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">${kategoriText}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">Rp ${new Intl.NumberFormat('id-ID').format(produk.harga)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">${produk.stok}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <button onclick="openEditModal(${htmlspecialchars(JSON.stringify(produk))})" class="text-blue-500 hover:text-blue-700 mx-2 transition-colors duration-200">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="openDeleteModal(${produk.id})" class="text-red-500 hover:text-red-700 mx-2 transition-colors duration-200">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
    }
    
    // Fungsi untuk mengurutkan tabel
    function sortTable(columnIndex) {
        const table = document.getElementById('produk-table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const isNumeric = [true, false, false, false, true, true]; // ID, Harga, Stok adalah numerik
        const sortKey = ['id', null, 'nama', 'nama_kategori', 'harga', 'stok'];
        
        const columnHeader = table.querySelectorAll('th')[columnIndex];
        const currentSortDirection = columnHeader.dataset.sortDirection || 'asc';
        const newSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        columnHeader.dataset.sortDirection = newSortDirection;
        
        // Reset ikon sort pada semua kolom
        table.querySelectorAll('.sort-icon').forEach(icon => {
            icon.classList.remove('fa-sort-up', 'fa-sort-down');
            icon.classList.add('fa-sort');
        });

        // Perbarui ikon sort pada kolom yang diklik
        const currentIcon = columnHeader.querySelector('.sort-icon');
        if (newSortDirection === 'asc') {
            currentIcon.classList.remove('fa-sort');
            currentIcon.classList.add('fa-sort-up');
        } else {
            currentIcon.classList.remove('fa-sort');
            currentIcon.classList.add('fa-sort-down');
        }

        currentProducts.sort((a, b) => {
            let valA = a[sortKey[columnIndex]];
            let valB = b[sortKey[columnIndex]];
            
            // Perlakuan khusus untuk nilai null di kolom kategori
            if (sortKey[columnIndex] === 'nama_kategori') {
                valA = valA ? valA.toLowerCase() : 'zzzzzzzz'; // Letakkan null di akhir saat ascending
                valB = valB ? valB.toLowerCase() : 'zzzzzzzz';
            } else if (isNumeric[columnIndex]) {
                valA = parseFloat(valA);
                valB = parseFloat(valB);
            } else {
                valA = valA.toLowerCase();
                valB = valB.toLowerCase();
            }

            if (valA < valB) {
                return newSortDirection === 'asc' ? -1 : 1;
            }
            if (valA > valB) {
                return newSortDirection === 'asc' ? 1 : -1;
            }
            return 0;
        });

        renderTable(currentProducts);
    }

    // Mengganti fungsi htmlspecialchars karena kita menggunakan JS di sisi klien
    function htmlspecialchars(str) {
        if (typeof str !== 'string') {
            str = String(str);
        }
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
</script>

<?php
// Sertakan template footer
require_once __DIR__ . '/templates/footer.php';
?>
