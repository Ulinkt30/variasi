<?php
/**
 * File: views/user/keranjang.php
 * Deskripsi: Halaman keranjang belanja dengan desain seperti Shopee.
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sertakan file koneksi database, model, dan controller yang diperlukan
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/KeranjangController.php';
require_once __DIR__ . '/../../controllers/ProdukController.php';

// Inisialisasi controller
$keranjangController = new KeranjangController($conn);
$keranjang_items = $keranjangController->readAll();

// Sertakan template header
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/navbar.php';
?>

<style>
    /* Hide the number input arrows */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
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
        background-color: #FFFFFF;
        padding: 2rem;
        border-radius: 0.5rem;
        text-align: center;
        max-width: 400px;
        width: 90%;
        color: #1E293B;
    }

    /* Responsive table for mobile */
    @media (max-width: 768px) {
        .responsive-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #E2E8F0;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .responsive-table thead {
            display: none;
        }
        .responsive-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: none;
        }
        .responsive-table td:last-child {
            border-bottom: none;
        }
        /* Mengatur label pseudo-element untuk mobile */
        .responsive-table td[data-label="Kuantitas"]::before,
        .responsive-table td[data-label="Aksi"]::before {
            content: attr(data-label);
            font-weight: bold;
            text-align: left;
            width: 40%;
            padding-right: 1rem;
        }
        /* Menyembunyikan Total Harga dan Harga Satuan di mobile */
        .responsive-table td[data-label="Harga Satuan"],
        .responsive-table td[data-label="Total Harga"] {
            display: none;
        }
        /* Style for the combined product cell on mobile */
        .responsive-table td:first-child {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        .responsive-table td:first-child::before {
            content: none; /* Hide the data label for the first cell */
        }
        .responsive-table td:first-child .flex {
            align-items: center;
            flex-grow: 1;
        }
        .responsive-table td:first-child .flex-1 {
            display: flex;
            flex-direction: column;
            margin-left: 1rem; /* Added margin for spacing */
        }
    }
</style>

<div class="container mx-auto p-8 bg-slate-50 min-h-screen">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Keranjang Belanja</h1>

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

    <?php if (empty($keranjang_items)): ?>
        <div class="text-center p-10 bg-white rounded-lg shadow-md max-w-lg mx-auto">
            <p class="text-xl text-slate-500">Keranjang Anda kosong. Yuk, temukan produk menarik!</p>
            <a href="/variasi-motor/katalog" class="bg-blue-500 text-white font-bold py-3 px-6 rounded-full mt-6 inline-block transition duration-300 hover:bg-blue-600">
                Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <form id="checkout-form-keranjang" action="/variasi-motor/checkout" method="POST">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <!-- Header Toko -->
                <div class="flex items-center space-x-2 mb-4">
                    <input type="checkbox" id="check-all-toko" class="form-checkbox text-blue-500">
                    <i class="fas fa-store text-slate-500"></i>
                    <span class="text-slate-800 font-bold">Toko Daniel Variasi</span>
                </div>

                <!-- Tabel Produk -->
                <div class="overflow-x-auto">
                    <table class="responsive-table min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 hidden md:table-header-group">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kuantitas</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Total Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php $total_belanja = 0; ?>
                            <?php foreach ($keranjang_items as $item): ?>
                                <?php $total_belanja += $item['harga'] * $item['jumlah']; ?>
                                <tr>
                                    <!-- Combined product cell for mobile, standard cell for desktop -->
                                    <td class="px-6 py-4 flex items-center" data-label="Produk">
                                        <input type="checkbox" name="produk_pilih[]" value="<?php echo htmlspecialchars($item['keranjang_id']); ?>" class="form-checkbox text-blue-500 mr-4" data-produk-id="<?php echo htmlspecialchars($item['produk_id']); ?>" data-jumlah="<?php echo htmlspecialchars($item['jumlah']); ?>" data-harga="<?php echo htmlspecialchars($item['harga']); ?>">
                                        <img src="/variasi-motor/assets/uploads/<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>" class="w-16 h-16 object-cover rounded-md border border-slate-200">
                                        <div class="flex-1 ml-4">
                                            <p class="text-sm font-semibold text-slate-800 overflow-hidden text-ellipsis whitespace-nowrap" style="max-width: 200px;">
                                                <?php echo htmlspecialchars($item['nama']); ?>
                                            </p>
                                            <!-- Price for mobile view -->
                                            <p class="text-sm font-bold text-slate-800 md:hidden mt-1 mobile-price" data-subtotal="<?php echo htmlspecialchars($item['harga'] * $item['jumlah']); ?>">
                                                Rp <?php echo number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?>
                                            </p>
                                            <p class="text-xs text-slate-500">Stok: <?php echo htmlspecialchars($item['stok']); ?></p>
                                        </div>
                                    </td>
                                    <!-- Price cell for desktop view -->
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell" data-label="Harga Satuan">
                                        <div class="text-sm font-bold text-slate-800" data-harga="<?php echo htmlspecialchars($item['harga']); ?>">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap" data-label="Kuantitas">
                                        <div class="flex items-center border border-slate-200 rounded-md">
                                            <button type="button" class="p-2 text-blue-500 hover:bg-slate-50" onclick="ubahJumlah(this, -1)">-</button>
                                            <input type="number" value="<?php echo htmlspecialchars($item['jumlah']); ?>" min="1" max="<?php echo htmlspecialchars($item['stok']); ?>" class="w-12 text-center border-x border-slate-200 focus:outline-none text-slate-800">
                                            <button type="button" class="p-2 text-blue-500 hover:bg-slate-50" onclick="ubahJumlah(this, 1)">+</button>
                                        </div>
                                    </td>
                                    <!-- Total price cell for desktop view -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-500 hidden md:table-cell" data-label="Total Harga" data-subtotal="<?php echo htmlspecialchars($item['harga'] * $item['jumlah']); ?>">
                                        Rp <?php echo number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap" data-label="Aksi">
                                        <button type="button" class="hapus-satu-btn text-slate-500 hover:text-red-500 transition-colors duration-200" data-keranjang-id="<?php echo htmlspecialchars($item['keranjang_id']); ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Hidden input for selected items -->
            <input type="hidden" name="selected_items" id="selected-items-input">
        </form>

        <!-- Form tersembunyi untuk menghapus banyak item -->
        <form id="hapus-terpilih-form" action="/variasi-motor/keranjang/hapus-terpilih" method="POST" style="display: none;">
            <input type="hidden" name="keranjang_ids" id="keranjang-ids-to-delete">
        </form>

        <!-- Bagian Bawah: Opsi & Checkout -->
        <div class="bg-white p-6 rounded-lg shadow-md mt-6 sticky bottom-0 z-10">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="flex items-center space-x-4">
                    <input type="checkbox" id="check-all-bottom" class="form-checkbox text-blue-500">
                    <label class="text-sm font-bold text-slate-800">Pilih Semua</label>
                    <button type="button" id="hapus-terpilih-btn" class="text-sm text-slate-500 hover:text-red-500 transition-colors duration-200 ml-4">Hapus</button>
                </div>
                
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-slate-500">Total Harga (<span id="jumlah-produk-terpilih">0</span> produk):</span>
                        <p class="text-xl text-blue-500 font-bold" id="total-harga-terpilih">Rp 0</p>
                    </div>
                    <button type="submit" form="checkout-form-keranjang" id="checkout-btn" class="bg-blue-500 text-white font-bold py-3 px-8 rounded-md transition duration-300 hover:bg-blue-600">
                        Checkout
                    </button>
                </div>
            </div>
        </div>
        
    <?php endif; ?>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="konfirmasi-hapus-modal" class="modal-backdrop">
    <div class="modal-content">
        <h2 class="text-lg font-bold mb-4 text-slate-800">Konfirmasi Penghapusan</h2>
        <p class="text-sm mb-6 text-slate-500" id="modal-text-hapus">Apakah Anda yakin ingin menghapus produk ini?</p>
        <div class="flex justify-center space-x-4">
            <button id="batal-hapus-btn" class="bg-white text-slate-800 border border-slate-200 hover:bg-slate-50 font-bold py-2 px-6 rounded-md">Batal</button>
            <button id="lanjutkan-hapus-btn" class="bg-red-500 text-white font-bold py-2 px-6 rounded-md hover:bg-red-600">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- Modal Notifikasi (untuk pesan error) -->
<div id="notifikasi-modal" class="modal-backdrop">
    <div class="modal-content">
        <h2 class="text-lg font-bold mb-4 text-slate-800">Notifikasi</h2>
        <p class="text-sm mb-6 text-slate-500" id="modal-text-notifikasi"></p>
        <button id="tutup-notifikasi-btn" class="bg-blue-500 text-white font-bold py-2 px-6 rounded-md hover:bg-blue-600">Tutup</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('input[name="produk_pilih[]"]');
        const checkAllToko = document.getElementById('check-all-toko');
        const checkAllBottom = document.getElementById('check-all-bottom');
        const selectedItemsInput = document.getElementById('selected-items-input');
        const hapusTerpilihBtn = document.getElementById('hapus-terpilih-btn');
        const hapusSatuBtns = document.querySelectorAll('.hapus-satu-btn');
        const checkoutBtn = document.getElementById('checkout-btn');

        // Modal elements
        const konfirmasiHapusModal = document.getElementById('konfirmasi-hapus-modal');
        const modalTextHapus = document.getElementById('modal-text-hapus');
        const batalHapusBtn = document.getElementById('batal-hapus-btn');
        const lanjutkanHapusBtn = document.getElementById('lanjutkan-hapus-btn');

        const notifikasiModal = document.getElementById('notifikasi-modal');
        const modalTextNotifikasi = document.getElementById('modal-text-notifikasi');
        const tutupNotifikasiBtn = document.getElementById('tutup-notifikasi-btn');

        let isHapusAll = false;

        const updateTotal = () => {
            let total = 0;
            let jumlahProduk = 0;
            let selectedItems = [];
            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const jumlahInput = row.querySelector('input[type="number"]');
                
                if (checkbox.checked) {
                    const hargaSatuan = parseFloat(checkbox.dataset.harga);
                    const jumlah = parseInt(jumlahInput.value);
                    const subtotal = hargaSatuan * jumlah;

                    selectedItems.push({ id: checkbox.dataset.produkId, jumlah: jumlah, harga: hargaSatuan, keranjang_id: checkbox.value });
                    total += subtotal;
                    jumlahProduk++;
                    
                    // Update the subtotal display for the row on desktop
                    const subtotalDesktopElement = row.querySelector('[data-subtotal]');
                    if (subtotalDesktopElement) {
                         subtotalDesktopElement.dataset.subtotal = subtotal;
                         subtotalDesktopElement.innerText = `Rp ${subtotal.toLocaleString('id-ID')}`;
                    }
                    
                }
            });
            document.getElementById('total-harga-terpilih').innerText = `Rp ${total.toLocaleString('id-ID')}`;
            document.getElementById('jumlah-produk-terpilih').innerText = jumlahProduk;
            selectedItemsInput.value = JSON.stringify(selectedItems);
            
            // Toggle visibility of checkout/delete buttons
            if (jumlahProduk > 0) {
                hapusTerpilihBtn.style.display = 'block';
                checkoutBtn.style.opacity = '1';
                checkoutBtn.style.cursor = 'pointer';
            } else {
                hapusTerpilihBtn.style.display = 'none';
                checkoutBtn.style.opacity = '0.5';
                checkoutBtn.style.cursor = 'not-allowed';
            }
        };

        const toggleAll = (isChecked) => {
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateTotal();
        };

        checkAllToko.addEventListener('change', (e) => {
            toggleAll(e.target.checked);
            checkAllBottom.checked = e.target.checked;
        });

        checkAllBottom.addEventListener('change', (e) => {
            toggleAll(e.target.checked);
            checkAllToko.checked = e.target.checked;
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkAllToko.checked = allChecked;
                checkAllBottom.checked = allChecked;
                updateTotal();
            });
        });

        // Event listener untuk tombol checkout
        checkoutBtn.addEventListener('click', (e) => {
            const selectedItems = JSON.parse(selectedItemsInput.value);
            if (selectedItems.length === 0) {
                e.preventDefault();
                // Menggunakan modal notifikasi untuk pesan error
                modalTextNotifikasi.innerText = "Pilih setidaknya satu produk untuk checkout.";
                notifikasiModal.style.display = 'flex';
            }
        });

        // Event listener untuk tombol hapus produk satu per satu
        hapusSatuBtns.forEach(button => {
            button.addEventListener('click', () => {
                const keranjangId = button.dataset.keranjangId;
                isHapusAll = false;
                modalTextHapus.innerText = "Apakah Anda yakin ingin menghapus produk ini dari keranjang?";
                lanjutkanHapusBtn.dataset.keranjangId = keranjangId;
                konfirmasiHapusModal.style.display = 'flex';
            });
        });

        // Event listener untuk tombol hapus semua yang dipilih
        hapusTerpilihBtn.addEventListener('click', () => {
            const selectedItems = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (selectedItems.length > 0) {
                isHapusAll = true;
                modalTextHapus.innerText = `Apakah Anda yakin ingin menghapus ${selectedItems.length} produk yang dipilih dari keranjang?`;
                lanjutkanHapusBtn.dataset.keranjangIds = JSON.stringify(selectedItems);
                konfirmasiHapusModal.style.display = 'flex';
            } else {
                // Tampilkan pesan error atau notifikasi dengan modal
                modalTextNotifikasi.innerText = 'Pilih setidaknya satu produk untuk dihapus.';
                notifikasiModal.style.display = 'flex';
            }
        });

        // Event listener untuk tombol "Batal" di modal konfirmasi hapus
        batalHapusBtn.addEventListener('click', () => {
            konfirmasiHapusModal.style.display = 'none';
        });

        // Event listener untuk tombol "Ya, Hapus" di modal konfirmasi hapus
        lanjutkanHapusBtn.addEventListener('click', () => {
            konfirmasiHapusModal.style.display = 'none';
            if (isHapusAll) {
                const keranjangIds = JSON.parse(lanjutkanHapusBtn.dataset.keranjangIds);
                const form = document.getElementById('hapus-terpilih-form');
                const input = document.getElementById('keranjang-ids-to-delete');
                input.value = JSON.stringify(keranjangIds);
                form.submit();
            } else {
                const keranjangId = lanjutkanHapusBtn.dataset.keranjangId;
                // Buat form dinamis untuk submit penghapusan satu item
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/variasi-motor/keranjang/hapus';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'keranjang_id';
                input.value = keranjangId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Event listener untuk tombol "Tutup" di modal notifikasi
        tutupNotifikasiBtn.addEventListener('click', () => {
            notifikasiModal.style.display = 'none';
        });

        // Initialize total on page load
        updateTotal();
    });

    function ubahJumlah(btn, val) {
        const input = btn.parentNode.querySelector('input[type=number]');
        let jumlah = parseInt(input.value);
        let stok = parseInt(input.max);
        
        jumlah += val;
        if (jumlah < 1) jumlah = 1;
        if (jumlah > stok) jumlah = stok;
        
        input.value = jumlah;
        hitungTotalHarga(input);
    }
    
    function hitungTotalHarga(inputElement) {
        const row = inputElement.closest('tr');
        const hargaElement = row.querySelector('input[type="checkbox"]');
        
        const hargaSatuan = parseFloat(hargaElement.dataset.harga);
        const jumlah = parseInt(inputElement.value);
        const totalHarga = hargaSatuan * jumlah;

        // Mengambil elemen total harga desktop dan mengupdatenya
        const subtotalDesktopElement = row.querySelector('[data-subtotal]');
        if (subtotalDesktopElement) {
             subtotalDesktopElement.dataset.subtotal = totalHarga;
             subtotalDesktopElement.innerText = `Rp ${totalHarga.toLocaleString('id-ID')}`;
        }
        
        // Mengambil elemen harga mobile dan mengupdatenya
        const mobilePriceElement = row.querySelector('.mobile-price');
        if (mobilePriceElement) {
            mobilePriceElement.dataset.subtotal = totalHarga;
            mobilePriceElement.innerText = `Rp ${totalHarga.toLocaleString('id-ID')}`;
        }
        
        // Update the checkbox data attribute so that total price is calculated correctly
        hargaElement.dataset.jumlah = jumlah;
        
        const event = new Event('change');
        hargaElement.dispatchEvent(event);
    }
</script>
<input type="hidden" name="keranjang_id[]" value="<?php echo htmlspecialchars($item['keranjang_id']); ?>">

<?php
// Sertakan footer
require_once __DIR__ . '/../templates/footer.php';
?>
