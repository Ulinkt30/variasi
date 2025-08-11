<?php
/**
 * File: controllers/PesananController.php
 * Deskripsi: Controller ini menangani semua logika terkait pesanan,
 * seperti proses checkout dan riwayat pesanan.
 */

// Sertakan file koneksi database dan model yang diperlukan
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Pesanan.php';
require_once __DIR__ . '/../models/Keranjang.php';
require_once __DIR__ . '/../models/Produk.php';

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class PesananController {
    private $db;
    private $pesanan;
    private $keranjang;
    private $produk;

    public function __construct($db) {
        $this->db = $db;
        $this->pesanan = new Pesanan($this->db);
        $this->keranjang = new Keranjang($this->db);
        $this->produk = new Produk($this->db);
    }

    /**
     * Memproses checkout pesanan.
     */
    public function checkout() {
        // Cek jika pengguna sudah login dan request adalah POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            
            // Ambil data dari form checkout
            $nama_penerima = $_POST['nama_penerima'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $telepon = $_POST['telepon'] ?? '';
            $produk_ids = $_POST['produk_id'] ?? [];
            $jumlahs = $_POST['jumlah'] ?? [];
            $harga_satuans = $_POST['harga_satuan'] ?? [];
            $keranjang_ids = $_POST['keranjang_id'] ?? []; // Mengambil keranjang_id dari form checkout

            // Pastikan data produk yang akan dipesan tidak kosong
            if (empty($produk_ids)) {
                $_SESSION['error_message'] = "Tidak ada produk yang dipesan.";
                header("Location: /variasi-motor/checkout");
                exit();
            }

            // Tangani upload bukti pembayaran
            $bukti_pembayaran = '';
            if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
                $target_dir = __DIR__ . "/../assets/uploads/";
                $target_file = $target_dir . basename($_FILES["bukti_pembayaran"]["name"]);
                
                // Pastikan folder uploads ada dan dapat ditulis
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
                    $bukti_pembayaran = basename($_FILES["bukti_pembayaran"]["name"]);
                }
            }

            $total_harga = 0;
            // Hitung total harga dari item yang dikirimkan
            for ($i = 0; $i < count($produk_ids); $i++) {
                $total_harga += $harga_satuans[$i] * $jumlahs[$i];
            }
            
            try {
                // Buat pesanan baru
                $this->pesanan->user_id = $_SESSION['user_id'];
                $this->pesanan->total_harga = $total_harga;
                $this->pesanan->status_pesanan = 'Pending'; // Status awal pesanan
                $this->pesanan->nama_penerima = $nama_penerima;
                $this->pesanan->alamat_pengiriman = $alamat;
                $this->pesanan->telepon_penerima = $telepon;
                $this->pesanan->bukti_pembayaran = $bukti_pembayaran;

                $pesanan_id = $this->pesanan->create();

                // Cek apakah ID pesanan valid sebelum melanjutkan
                if ($pesanan_id && is_numeric($pesanan_id) && $pesanan_id > 0) {
                    // Simpan detail pesanan dan hapus item dari keranjang
                    for ($i = 0; $i < count($produk_ids); $i++) {
                        $produk_id = $produk_ids[$i];
                        $jumlah = $jumlahs[$i];
                        $harga_satuan = $harga_satuans[$i];
                        $keranjang_id = $keranjang_ids[$i]; // Mengambil keranjang_id

                        $this->pesanan->addDetail($pesanan_id, $produk_id, $jumlah, $harga_satuan);
                        
                        // Hapus item dari keranjang setelah berhasil di-checkout
                        $this->keranjang->id = $keranjang_id;
                        $this->keranjang->delete();
                    }
                    
                    $_SESSION['success_message'] = "Checkout berhasil! Pesanan Anda sedang diproses.";
                    header("Location: /variasi-motor/riwayat_pesanan");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Gagal memproses pesanan. Pesanan tidak dapat dibuat.";
                    header("Location: /variasi-motor/checkout");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error_message'] = "Gagal memproses pesanan: " . $e->getMessage();
                header("Location: /variasi-motor/checkout");
                exit();
            }

        } else {
            $_SESSION['error_message'] = "Anda harus login untuk memproses checkout.";
            header("Location: /variasi-motor/login");
            exit();
        }
    }
    
    /**
     * Memproses checkout langsung dari halaman detail produk.
     */
    public function directCheckout() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['user_id'])) {
            $produk_id = $_GET['produk_id'] ?? '';
            $jumlah = $_GET['jumlah'] ?? 1;

            if (empty($produk_id) || empty($jumlah)) {
                $_SESSION['error_message'] = "Data produk tidak valid.";
                header("Location: /variasi-motor/katalog");
                exit();
            }

            // Ambil detail produk
            $this->produk->id = $produk_id;
            $produk_data = $this->produk->readOne();

            if (!$produk_data) {
                $_SESSION['error_message'] = "Produk tidak ditemukan.";
                header("Location: /variasi-motor/katalog");
                exit();
            }

            // Simpan data produk di sesi untuk halaman checkout
            $_SESSION['direct_checkout_item'] = [
                'produk_id' => $produk_id,
                'nama' => $produk_data['nama'],
                'harga' => $produk_data['harga'],
                'gambar' => $produk_data['gambar'],
                'jumlah' => $jumlah
            ];

            // Alihkan ke halaman checkout
            header("Location: /variasi-motor/checkout");
            exit();

        } else {
            $_SESSION['error_message'] = "Anda harus login untuk memproses checkout.";
            header("Location: /variasi-motor/login");
            exit();
        }
    }

    /**
     * Memproses checkout dari item yang dipilih di keranjang.
     */
    public function selectedCheckout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $selected_items_json = $_POST['selected_items'] ?? '[]';
            $selected_items = json_decode($selected_items_json, true);

            if (empty($selected_items)) {
                $_SESSION['error_message'] = "Tidak ada produk yang dipilih untuk checkout.";
                header("Location: /variasi-motor/keranjang");
                exit();
            }

            $checkout_data = [];
            $total_harga = 0;
            $keranjang_ids_to_delete = [];

            foreach ($selected_items as $item) {
                // Ambil data produk lengkap dari database untuk validasi dan harga terbaru
                $this->produk->id = $item['id'];
                $produk_data = $this->produk->readOne();

                if ($produk_data) {
                    $checkout_data[] = [
                        'produk_id' => $item['id'],
                        'nama' => $produk_data['nama'],
                        'harga' => $produk_data['harga'],
                        'gambar' => $produk_data['gambar'],
                        'jumlah' => $item['jumlah']
                    ];
                    $total_harga += $produk_data['harga'] * $item['jumlah'];
                    
                }
            }

            if (empty($checkout_data)) {
                $_SESSION['error_message'] = "Produk yang dipilih tidak valid atau tidak ditemukan.";
                header("Location: /variasi-motor/keranjang");
                exit();
            }
            
            // Simpan data checkout ke sesi
            $_SESSION['selected_checkout_items'] = $checkout_data;
            $_SESSION['selected_checkout_total'] = $total_harga;

            header("Location: /variasi-motor/checkout");
            exit();
        }
        
        $_SESSION['error_message'] = "Akses ditolak.";
        header("Location: /variasi-motor/keranjang");
        exit();
    }


    /**
     * Mendapatkan riwayat pesanan pengguna.
     * @param string $status_filter Status pesanan untuk difilter.
     * @return array Array riwayat pesanan.
     */
    public function getRiwayatPesanan($status_filter = 'Semua') {
        if (isset($_SESSION['user_id'])) {
            $this->pesanan->user_id = $_SESSION['user_id'];
            $stmt = $this->pesanan->readAll($status_filter);
            $riwayat_pesanan = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $riwayat_pesanan[] = $row;
            }
            return $riwayat_pesanan;
        }
        return [];
    }

    /**
     * Mendapatkan semua pesanan untuk halaman admin.
     * @return array Array semua pesanan.
     */
    public function getAllPesanan() {
        $query = "SELECT id, user_id, tanggal_pesanan, total_harga, status_pesanan FROM pesanan ORDER BY tanggal_pesanan DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $all_pesanan = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $all_pesanan[] = $row;
        }
        return $all_pesanan;
    }

    /**
     * Mendapatkan detail satu pesanan, termasuk produk-produknya.
     * @param int $pesanan_id ID pesanan.
     * @return array|null Array data pesanan dan detail produk, atau null jika tidak ditemukan.
     */
    public function getPesananDetail($pesanan_id) {
        $pesanan_data = $this->pesanan->readOne($pesanan_id);
        
        if ($pesanan_data) {
            $detail_data = $this->pesanan->getDetailByPesananId($pesanan_id);
            $pesanan_data['detail'] = $detail_data;
            return $pesanan_data;
        }
        
        return null;
    }

    /**
     * Memperbarui status pesanan.
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin_id'])) {
            $pesanan_id = $_POST['pesanan_id'] ?? '';
            $status_baru = $_POST['status_baru'] ?? '';

            if (empty($pesanan_id) || empty($status_baru)) {
                $_SESSION['error_message'] = "ID pesanan atau status baru tidak valid.";
                header("Location: /variasi-motor/admin/pesanan_detail/{$pesanan_id}");
                exit();
            }

            if ($this->pesanan->updateStatus($pesanan_id, $status_baru)) {
                $_SESSION['success_message'] = "Status pesanan berhasil diperbarui.";
            } else {
                $_SESSION['error_message'] = "Gagal memperbarui status pesanan.";
            }

            header("Location: /variasi-motor/admin/pesanan_detail/{$pesanan_id}");
            exit();
        } else {
            $_SESSION['error_message'] = "Akses ditolak.";
            header("Location: /variasi-motor/admin/login");
            exit();
        }
    }
}
