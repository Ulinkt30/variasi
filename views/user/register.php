<?php
/**
 * File: views/user/register.php
 * Deskripsi: Halaman tampilan untuk registrasi pengguna baru.
 * Menampilkan form pendaftaran dan pesan error/sukses dari session.
 */

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Variasi Motor</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center">

<div class="w-full max-w-lg mx-auto p-8 rounded-xl shadow-2xl bg-white border border-slate-200">
    <div class="text-center mb-8">
        <h2 class="text-4xl font-bold text-slate-800 mb-2">Daftar Akun</h2>
        <p class="text-slate-500">Buat akun baru untuk mulai berbelanja.</p>
    </div>
    
    <?php
    // Tampilkan pesan error jika ada
    if (isset($_SESSION['error_message'])) {
        echo '<div class="bg-red-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
    // Tampilkan pesan sukses jika ada
    if (isset($_SESSION['success_message'])) {
        echo '<div class="bg-green-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    ?>

    <form id="register-form" action="/variasi-motor/register" method="POST" class="space-y-6">
        <div>
            <label for="username" class="block text-slate-500 font-semibold mb-2">Username</label>
            <input type="text" id="username" name="username" required
                   class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 transition duration-200">
        </div>
        <div>
            <label for="password" class="block text-slate-500 font-semibold mb-2">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 transition duration-200">
        </div>
        <div>
            <label for="confirm_password" class="block text-slate-500 font-semibold mb-2">Konfirmasi Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required
                   class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 transition duration-200">
            <p id="password-error" class="text-red-500 text-sm mt-2 hidden">Password tidak cocok.</p>
        </div>
        <button type="submit" 
                class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105">
            Daftar
        </button>
    </form>
    <div class="mt-6 text-center text-slate-500 text-sm">
        Sudah punya akun? <a href="/variasi-motor/login" class="text-teal-500 hover:underline">Login di sini</a>
    </div>
</div>

<script>
    document.getElementById('register-form').addEventListener('submit', function(event) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const errorElement = document.getElementById('password-error');

        if (password !== confirmPassword) {
            event.preventDefault();
            errorElement.classList.remove('hidden');
        } else {
            errorElement.classList.add('hidden');
        }
    });
</script>

</body>
</html>
