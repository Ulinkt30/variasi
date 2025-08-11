<?php
/**
 * File: controllers/KategoriController.php
 * Deskripsi: Controller ini menangani semua logika terkait kategori,
 * seperti menambah, mengedit, dan menghapus kategori.
 */

// Sertakan file koneksi database dan model Kategori
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Kategori.php';

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class KategoriController {
    private $db;
    private $kategori;

    public function __construct($db) {
        $this->db = $db;
        $this->kategori = new Kategori($this->db);
    }

    /**
     * Menangani pembuatan kategori baru.
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin_id'])) {
            $this->kategori->nama_kategori = $_POST['nama_kategori'] ?? '';

            if (empty($this->kategori->nama_kategori)) {
                $_SESSION['error_message'] = "Nama kategori tidak boleh kosong.";
                header("Location: /variasi-motor/admin/kelola_produk");
                exit();
            }

            if ($this->kategori->create()) {
                $_SESSION['success_message'] = "Kategori '" . htmlspecialchars($this->kategori->nama_kategori) . "' berhasil ditambahkan.";
            } else {
                $_SESSION['error_message'] = "Gagal menambahkan kategori.";
            }
            header("Location: /variasi-motor/admin/kelola_produk");
            exit();
        } else {
            $_SESSION['error_message'] = "Akses ditolak.";
            header("Location: /variasi-motor/admin/login");
            exit();
        }
    }

    /**
     * Menangani pembaruan kategori.
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin_id'])) {
            $this->kategori->id = $_POST['id'] ?? '';
            $this->kategori->nama_kategori = $_POST['nama_kategori'] ?? '';

            if (empty($this->kategori->id) || empty($this->kategori->nama_kategori)) {
                $_SESSION['error_message'] = "ID atau nama kategori tidak valid.";
                header("Location: /variasi-motor/admin/kelola_produk");
                exit();
            }

            if ($this->kategori->update()) {
                $_SESSION['success_message'] = "Kategori berhasil diperbarui.";
            } else {
                $_SESSION['error_message'] = "Gagal memperbarui kategori.";
            }
            header("Location: /variasi-motor/admin/kelola_produk");
            exit();
        } else {
            $_SESSION['error_message'] = "Akses ditolak.";
            header("Location: /variasi-motor/admin/login");
            exit();
        }
    }

    /**
     * Menangani penghapusan kategori.
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin_id'])) {
            $this->kategori->id = $_POST['id'] ?? '';

            if (empty($this->kategori->id)) {
                $_SESSION['error_message'] = "ID kategori tidak valid.";
                header("Location: /variasi-motor/admin/kelola_produk");
                exit();
            }

            if ($this->kategori->delete()) {
                $_SESSION['success_message'] = "Kategori berhasil dihapus.";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus kategori.";
            }
            header("Location: /variasi-motor/admin/kelola_produk");
            exit();
        } else {
            $_SESSION['error_message'] = "Akses ditolak.";
            header("Location: /variasi-motor/admin/login");
            exit();
        }
    }
}
