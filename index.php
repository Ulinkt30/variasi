<?php
/**
 * File: index.php
 * Deskripsi: File ini adalah titik masuk utama (front controller)
 * yang menangani semua permintaan dan mengarahkan ke controller yang tepat.
 */

// Aktifkan laporan error PHP untuk debugging.
// Hapus baris ini setelah debugging selesai.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sertakan file koneksi database
require_once __DIR__ . '/config/db.php';
// Sertakan file controller
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ProdukController.php';
require_once __DIR__ . '/controllers/KeranjangController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/PesananController.php';
require_once __DIR__ . '/controllers/KategoriController.php'; // Sertakan KategoriController
require_once __DIR__ . '/controllers/UserController.php'; // Tambahkan UserController

// Mendapatkan URI dari permintaan
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Pisahkan URI untuk mendapatkan path
$path = parse_url($request_uri, PHP_URL_PATH);

// Tentukan base directory. Sesuaikan jika nama folder proyek Anda berbeda.
$base_dir = '/variasi-motor';

// Hapus base directory dari path untuk routing yang benar
if (substr($path, 0, strlen($base_dir)) === $base_dir) {
    $path = substr($path, strlen($base_dir));
}

// Jika path menjadi kosong setelah penghapusan, atur ke '/'
if ($path === '') {
    $path = '/';
}

// Inisialisasi controller
$authController = new AuthController($conn);
$produkController = new ProdukController($conn);
$keranjangController = new KeranjangController($conn);
$adminController = new AdminController($conn);
$pesananController = new PesananController($conn);
$kategoriController = new KategoriController($conn); // Inisialisasi KategoriController
$userController = new UserController($conn); // Inisialisasi UserController

// Routing sederhana
switch (true) {
    // --- USER ROUTES ---
    case $path === '/' || $path === '/beranda':
        require_once __DIR__ . '/views/user/beranda.php';
        break;
    case $path === '/login':
        if ($request_method === 'POST') {
            $authController->login();
        } else {
            require_once __DIR__ . '/views/user/login.php';
        }
        break;
    case $path === '/register':
        if ($request_method === 'POST') {
            $authController->register();
        } else {
            require_once __DIR__ . '/views/user/register.php';
        }
        break;
    case $path === '/logout':
        $authController->logout();
        break;
    case $path === '/katalog':
        require_once __DIR__ . '/views/user/katalog.php';
        break;
    case preg_match('/^\/produk\/(\d+)$/', $path, $matches):
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/views/user/produk_detail.php';
        break;
    case $path === '/keranjang':
        if ($request_method === 'POST') {
            $keranjangController->addToCart();
        } else {
            require_once __DIR__ . '/views/user/keranjang.php';
        }
        break;
    case $path === '/keranjang/hapus':
        if ($request_method === 'POST') {
            $keranjangController->delete();
        }
        break;
    // ROUTING CHECKOUT BARU
    // Rute ini sekarang hanya untuk MENAMPILKAN halaman checkout
    case $path === '/checkout':
        require_once __DIR__ . '/views/user/checkout.php';
        break;
    // Rute BARU untuk memproses data checkout
    case $path === '/proses-checkout':
        if ($request_method === 'POST') {
            $pesananController->checkout();
        }
        break;
    case $path === '/checkout/selected': // Rute ini tidak lagi diperlukan, tapi kita biarkan dulu
        if ($request_method === 'POST') {
            $pesananController->selectedCheckout();
        }
        break;
    case $path === '/profil':
        require_once __DIR__ . '/views/user/profil.php';
        break;
    case $path === '/riwayat_pesanan':
        require_once __DIR__ . '/views/user/riwayat_pesanan.php';
        break;
    case $path === '/checkout/direct': // Rute ini juga akan diganti dengan yang baru
        $pesananController->directCheckout();
        break;

    // --- ADMIN ROUTES ---
    case $path === '/admin/login':
        if ($request_method === 'POST') {
            $adminController->login();
        } else {
            require_once __DIR__ . '/views/admin/login.php';
        }
        break;
    case $path === '/admin/logout':
        $adminController->logout();
        break;
    case $path === '/admin/dashboard':
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;
    case $path === '/admin/kelola_produk':
        if ($request_method === 'POST') {
            $produkController->create(); // Menangani proses tambah produk
        } else {
            require_once __DIR__ . '/views/admin/kelola_produk.php';
        }
        break;
    case preg_match('/^\/admin\/kelola_produk\?id=(\d+)$/', $path, $matches):
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/views/admin/kelola_produk.php';
        break;
    case $path === '/admin/kelola_produk/update':
        if ($request_method === 'POST') {
            $produkController->update(); // Menangani proses edit produk
        }
        break;
    case $path === '/admin/kelola_produk/hapus':
        if ($request_method === 'POST') {
            $produkController->delete(); // Menangani proses hapus produk
        }
        break;
    case $path === '/admin/kelola_pesanan':
        require_once __DIR__ . '/views/admin/kelola_pesanan.php';
        break;
    case preg_match('/^\/admin\/pesanan_detail\/(\d+)$/', $path, $matches):
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/views/admin/pesanan_detail.php';
        break;
    case $path === '/admin/kelola_pesanan/update_status':
        if ($request_method === 'POST') {
            $pesananController->updateStatus(); // Menangani update status pesanan
        }
        break;
    case $path === '/admin/kelola_kategori':
        if ($request_method === 'POST') {
            $kategoriController->create(); // Menangani proses tambah kategori
        }
        break;
    case $path === '/admin/kelola_kategori/update':
        if ($request_method === 'POST') {
            $kategoriController->update(); // Menangani proses edit kategori
        }
        break;
    case $path === '/admin/kelola_kategori/hapus':
        if ($request_method === 'POST') {
            $kategoriController->delete(); // Menangani proses hapus kategori
        }
        break;
    case $path === '/admin/kelola_pengguna':
        require_once __DIR__ . '/views/admin/kelola_pengguna.php';
        break;
    case $path === '/admin/kelola_pengguna/hapus':
        if ($request_method === 'POST') {
            $userController->delete();
        }
        break;
    case $path === '/admin/kelola_admin':
        require_once __DIR__ . '/views/admin/kelola_pengguna.php'; // Menggunakan file yang sama untuk mengelola admin
        break;
    case $path === '/admin/kelola_admin/tambah':
        if ($request_method === 'POST') {
            $adminController->create(); // Menangani proses tambah admin
        }
        break;
    case $path === '/admin/kelola_admin/hapus':
        if ($request_method === 'POST') {
            $adminController->delete(); // Tambahkan metode delete di AdminController
        }
        break;
    default:
        // Halaman 404 Not Found
        http_response_code(404);
        echo "404 Not Found";
        break;
}
