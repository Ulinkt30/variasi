<?php
/**
 * File: controllers/AuthController.php
 * Deskripsi: Controller ini menangani semua logika terkait autentikasi pengguna
 * seperti registrasi, login, dan logout.
 */

// Sertakan file koneksi database dan model User
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

// Inisialisasi session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($this->db);
    }

    /**
     * Menangani proses registrasi pengguna baru.
     */
    public function register() {
        // Cek jika request adalah POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ambil data dari form
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Pastikan username dan password tidak kosong
            if (empty($username) || empty($password)) {
                $_SESSION['error_message'] = "Username dan password tidak boleh kosong.";
                header("Location: /variasi-motor/register");
                exit();
            }

            // Atur properti model User
            $this->user->username = $username;
            $this->user->password = $password;

            // Panggil metode register dari model User
            try {
                if ($this->user->register()) {
                    $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";
                    header("Location: /variasi-motor/login");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Username sudah digunakan atau terjadi kesalahan.";
                    header("Location: /variasi-motor/register");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error_message'] = "Terjadi kesalahan database: " . $e->getMessage();
                header("Location: /variasi-motor/register");
                exit();
            }
        }
    }

    /**
     * Menangani proses login pengguna.
     */
    public function login() {
        // Cek jika request adalah POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ambil data dari form
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Pastikan username dan password tidak kosong
            if (empty($username) || empty($password)) {
                $_SESSION['error_message'] = "Username dan password tidak boleh kosong.";
                header("Location: /variasi-motor/login");
                exit();
            }

            // Atur properti model User
            $this->user->username = $username;
            $this->user->password = $password;

            // Panggil metode login dari model User
            if ($this->user->login()) {
                // Login berhasil, simpan data user ke session
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['username'] = $this->user->username;
                $_SESSION['success_message'] = "Selamat datang, " . $this->user->username . "!";
                // Arahkan ke halaman utama setelah login
                header("Location: /variasi-motor");
                exit();
            } else {
                // Login gagal
                $_SESSION['error_message'] = "Username atau password salah.";
                header("Location: /variasi-motor/login");
                exit();
            }
        }
    }

    /**
     * Menangani proses logout pengguna.
     */
    public function logout() {
        // Hapus semua data session
        $_SESSION = array();
        // Hancurkan session
        session_destroy();
        // Arahkan kembali ke halaman login
        header("Location: /variasi-motor/login");
        exit();
    }
}
