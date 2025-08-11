<?php
/**
 * File: controllers/UserController.php
 * Deskripsi: Controller ini menangani semua logika terkait pengelolaan pengguna,
 * khususnya untuk keperluan admin.
 */

// Sertakan file koneksi database dan model User
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class UserController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($this->db);
    }

    /**
     * Mengambil semua data pengguna dari database.
     * @return array Array berisi semua data pengguna.
     */
    public function getAllUsers() {
        $users_arr = [];
        $query = "SELECT id, username, created_at FROM users ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users_arr[] = $row;
        }
        return $users_arr;
    }

    /**
     * Menghapus pengguna dari database (khusus admin).
     */
    public function delete() {
        // Cek jika request adalah POST dan admin sudah login
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin_id'])) {
            $user_id = $_POST['id'] ?? '';

            if (empty($user_id)) {
                $_SESSION['error_message'] = "ID pengguna tidak valid.";
                header("Location: /variasi-motor/admin/kelola_pengguna");
                exit();
            }

            // Query untuk menghapus pengguna
            $query = "DELETE FROM users WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $user_id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Pengguna berhasil dihapus.";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus pengguna.";
            }

            header("Location: /variasi-motor/admin/kelola_pengguna");
            exit();
        } else {
            $_SESSION['error_message'] = "Akses ditolak.";
            header("Location: /variasi-motor/admin/login");
            exit();
        }
    }
}
