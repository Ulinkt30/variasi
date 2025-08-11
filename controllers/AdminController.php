<?php
/**
 * File: controllers/AdminController.php
 * Deskripsi: Controller ini menangani semua logika terkait admin,
 * seperti login admin, operasi CRUD produk, dan pengelolaan akun admin.
 */

// Sertakan file koneksi database dan model Admin
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Admin.php';

// Inisialisasi session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AdminController {
    private $db;
    private $admin;

    public function __construct($db) {
        $this->db = $db;
        $this->admin = new Admin($this->db);
    }

    /**
     * Menangani proses login admin.
     */
    public function login() {
        // Cek jika request adalah POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ambil data dari form
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Pastikan username dan password tidak kosong
            if (empty($username) || empty($password)) {
                // Pesan error ini sekarang mencerminkan nama input yang benar
                $_SESSION['admin_error_message'] = "Username dan password tidak boleh kosong.";
                header("Location: /variasi-motor/admin/login");
                exit();
            }

            // Atur properti model Admin
            $this->admin->username = $username;
            $this->admin->password = $password;

            // Panggil metode login dari model Admin
            if ($this->admin->login()) {
                // Login berhasil, simpan data admin ke session
                $_SESSION['admin_id'] = $this->admin->id;
                $_SESSION['admin_username'] = $this->admin->username;
                $_SESSION['admin_success_message'] = "Selamat datang, Admin " . $this->admin->username . "!";
                // Arahkan ke halaman dashboard admin
                header("Location: /variasi-motor/admin/dashboard");
                exit();
            } else {
                // Login gagal
                $_SESSION['admin_error_message'] = "Username atau password salah.";
                header("Location: /variasi-motor/admin/login");
                exit();
            }
        }
    }

    /**
     * Menangani proses logout admin.
     */
    public function logout() {
        // Hapus semua data session admin
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        // Arahkan kembali ke halaman login admin
        header("Location: /variasi-motor/admin/login");
        exit();
    }
    
    /**
     * Mengambil data ringkasan untuk dashboard admin.
     * @return array Array asosiatif berisi total produk, pesanan, dan pengguna.
     */
    public function getDashboardData() {
        try {
            // Query untuk menghitung total produk
            $query_produk = "SELECT COUNT(*) as total_produk FROM produk";
            $stmt_produk = $this->db->prepare($query_produk);
            $stmt_produk->execute();
            $total_produk = $stmt_produk->fetch(PDO::FETCH_ASSOC)['total_produk'];

            // Query untuk menghitung total pesanan
            $query_pesanan = "SELECT COUNT(*) as total_pesanan FROM pesanan";
            $stmt_pesanan = $this->db->prepare($query_pesanan);
            $stmt_pesanan->execute();
            $total_pesanan = $stmt_pesanan->fetch(PDO::FETCH_ASSOC)['total_pesanan'];
            
            // Query untuk menghitung total pengguna
            $query_users = "SELECT COUNT(*) as total_users FROM users";
            $stmt_users = $this->db->prepare($query_users);
            $stmt_users->execute();
            $total_users = $stmt_users->fetch(PDO::FETCH_ASSOC)['total_users'];

            return [
                'total_produk' => $total_produk,
                'total_pesanan' => $total_pesanan,
                'total_users' => $total_users
            ];
            
        } catch(PDOException $e) {
            // Tangani error jika terjadi masalah pada query database
            error_log("Database Error in AdminController::getDashboardData: " . $e->getMessage());
            return [
                'total_produk' => 'Error',
                'total_pesanan' => 'Error',
                'total_users' => 'Error'
            ];
        }
    }
    
    /**
     * Mengambil semua data admin dari database.
     * @return array Array berisi semua data admin.
     */
    public function getAllAdmins() {
        $admins_arr = [];
        $query = "SELECT id, username, created_at FROM admins ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $admins_arr[] = $row;
        }
        return $admins_arr;
    }

    /**
     * Menangani pembuatan admin baru (khusus admin).
     */
    public function create() {
        // Cek jika request adalah POST dan admin sudah login
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin_id'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['error_message'] = "Username dan password tidak boleh kosong.";
                header("Location: /variasi-motor/admin/kelola_admin");
                exit();
            }

            // Atur properti model Admin
            $this->admin->username = $username;
            $this->admin->password = $password;

            // Panggil metode create dari model Admin
            if ($this->admin->create()) {
                $_SESSION['success_message'] = "Admin baru berhasil ditambahkan.";
            } else {
                $_SESSION['error_message'] = "Gagal menambahkan admin baru. Username mungkin sudah digunakan.";
            }

            header("Location: /variasi-motor/admin/kelola_admin");
            exit();
        } else {
            $_SESSION['error_message'] = "Akses ditolak.";
            header("Location: /variasi-motor/admin/login");
            exit();
        }
    }
    
    /**
     * Menghapus admin dari database (khusus admin).
     */
    public function delete() {
        // Cek jika request adalah POST dan admin sudah login
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin_id'])) {
            $admin_id = $_POST['id'] ?? '';

            if (empty($admin_id)) {
                $_SESSION['error_message'] = "ID admin tidak valid.";
                header("Location: /variasi-motor/admin/kelola_admin");
                exit();
            }

            // Pastikan admin tidak menghapus dirinya sendiri
            if ($admin_id == $_SESSION['admin_id']) {
                $_SESSION['error_message'] = "Anda tidak bisa menghapus akun Anda sendiri.";
                header("Location: /variasi-motor/admin/kelola_admin");
                exit();
            }

            // Panggil metode delete dari model Admin
            if ($this->admin->delete($admin_id)) {
                $_SESSION['success_message'] = "Admin berhasil dihapus.";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus admin.";
            }

            header("Location: /variasi-motor/admin/kelola_admin");
            exit();
        } else {
            $_SESSION['error_message'] = "Akses ditolak.";
            header("Location: /variasi-motor/admin/login");
            exit();
        }
    }

    /**
     * Mengambil pesanan terbaru dari database.
     * @param int $limit Batas jumlah pesanan yang diambil.
     * @return array Array berisi data pesanan terbaru.
     */
    public function getLatestOrders($limit = 5) {
        $orders = [];
        $query = "SELECT p.id, p.status_pesanan, u.id as user_id 
                  FROM pesanan p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.status_pesanan = 'Pending'
                  ORDER BY p.tanggal_pesanan DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $orders[] = $row;
        }
        return $orders;
    }
    
    /**
     * Mengambil produk dengan stok rendah dari database.
     * @param int $limit Batas jumlah produk yang diambil.
     * @return array Array berisi data produk dengan stok rendah.
     */
    public function getLowStockProducts($limit = 5) {
        $products = [];
        $query = "SELECT p.nama, p.stok, k.nama_kategori
                  FROM produk p
                  JOIN kategori k ON p.kategori_id = k.id
                  WHERE p.stok < 10
                  ORDER BY p.stok ASC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = $row;
        }
        return $products;
    }
}
