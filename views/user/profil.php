<?php
/**
 * File: views/user/profil.php
 * Deskripsi: Halaman tampilan untuk profil pengguna.
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sertakan template header dan navbar
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/navbar.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, tampilkan pesan dan link ke halaman login
    echo '<div class="container mx-auto p-8 text-center">';
    echo '<h1 class="text-4xl font-bold mb-4">Profil Pengguna</h1>';
    echo '<p class="text-xl text-gray-400">Anda belum login. Silakan login untuk melihat profil.</p>';
    echo '<a href="/variasi-motor/login" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full mt-6 inline-block transition duration-300">';
    echo 'Login Sekarang';
    echo '</a>';
    echo '</div>';
    
    // Sertakan footer dan keluar
    require_once __DIR__ . '/../templates/footer.php';
    exit();
}

// TODO: Tampilkan data profil pengguna di sini
?>

<div class="container mx-auto p-8">
    <h1 class="text-4xl font-bold mb-4">Profil Pengguna</h1>
    <div class="bg-gray-800 p-6 rounded-lg shadow-md">
        <p class="text-xl">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <p class="text-gray-400 mt-2">Ini adalah halaman profil Anda. Di sini Anda bisa mengelola informasi pribadi dan melihat riwayat pesanan.</p>
    </div>
</div>

<?php
// Sertakan template footer
require_once __DIR__ . '/../templates/footer.php';
?>
