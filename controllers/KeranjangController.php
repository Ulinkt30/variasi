<?php
/**
 * File: controllers/KeranjangController.php
 * Deskripsi: Controller ini menangani semua logika terkait keranjang belanja pengguna,
 * seperti menambah, menghapus, dan menampilkan isi keranjang.
 */

// Sertakan file koneksi database, model Keranjang, dan model Produk
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Keranjang.php';
require_once __DIR__ . '/../models/Produk.php';

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class KeranjangController {
    private $db;
    private $keranjang;
    private $produk;

    public function __construct($db) {
        $this->db = $db;
        $this->keranjang = new Keranjang($this->db);
        $this->produk = new Produk($this->db);
    }

    /**
     * Menambahkan produk ke keranjang.
     */
    public function addToCart() {
        // Cek jika request adalah POST dan pengguna sudah login
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $produk_id = $_POST['produk_id'] ?? '';
            $jumlah = $_POST['jumlah'] ?? 1; // Default jumlah 1

            // Pastikan produk_id tidak kosong
            if (empty($produk_id)) {
                $_SESSION['error_message'] = "ID produk tidak valid.";
                header("Location: /variasi-motor/katalog");
                exit();
            }

            // Atur properti model Keranjang
            $this->keranjang->user_id = $_SESSION['user_id'];
            $this->keranjang->produk_id = $produk_id;
            $this->keranjang->jumlah = $jumlah;

            // Panggil metode addToCart dari model Keranjang
            if ($this->keranjang->addToCart()) {
                $_SESSION['success_message'] = "Produk berhasil ditambahkan ke keranjang.";
            } else {
                $_SESSION['error_message'] = "Gagal menambahkan produk ke keranjang.";
            }
            
            header("Location: /variasi-motor/keranjang");
            exit();
        } else {
            // Jika belum login atau request bukan POST, arahkan ke halaman login
            $_SESSION['error_message'] = "Anda harus login untuk menambahkan produk ke keranjang.";
            header("Location: /variasi-motor/login");
            exit();
        }
    }

    /**
     * Menampilkan semua item di keranjang pengguna.
     * @return array Array item keranjang.
     */
    public function readAll() {
        if (isset($_SESSION['user_id'])) {
            $this->keranjang->user_id = $_SESSION['user_id'];
            $stmt = $this->keranjang->readAll();
            $num = $stmt->rowCount();
            $keranjang_items = [];

            if ($num > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    array_push($keranjang_items, $row);
                }
            }
            return $keranjang_items;
        }
        return [];
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function delete() {
        // Cek jika request adalah POST dan pengguna sudah login
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $keranjang_id = $_POST['keranjang_id'] ?? '';

            if (empty($keranjang_id)) {
                $_SESSION['error_message'] = "ID item keranjang tidak valid.";
                header("Location: /variasi-motor/keranjang");
                exit();
            }

            // Atur properti model Keranjang
            $this->keranjang->id = $keranjang_id;
            $this->keranjang->user_id = $_SESSION['user_id'];

            // Panggil metode delete dari model Keranjang
            if ($this->keranjang->delete()) {
                $_SESSION['success_message'] = "Item berhasil dihapus dari keranjang.";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus item dari keranjang.";
            }

            header("Location: /variasi-motor/keranjang");
            exit();
        }
    }
}
