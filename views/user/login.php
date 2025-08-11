<?php
/**
 * File: views/user/login.php
 * Deskripsi: Halaman tampilan untuk login pengguna.
 * Menampilkan form login dan pesan error/sukses dari session dengan tampilan baru.
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
    <title>Login - Variasi Motor</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center">

<div class="w-full max-w-lg mx-auto p-8 rounded-xl shadow-2xl bg-white border border-slate-200">
    <div class="text-center mb-8">
        <h2 class="text-4xl font-bold text-slate-800 mb-2">Login</h2>
        <p class="text-slate-500">Masuk ke akun Anda untuk mulai berbelanja.</p>
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

    <form action="/variasi-motor/login" method="POST" class="space-y-6">
        <div>
            <label for="username" class="block text-slate-500 font-semibold mb-2">Username</label>
            <input type="text" id="username" name="username" required
                   class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
        </div>
        <div>
            <label for="password" class="block text-slate-500 font-semibold mb-2">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
        </div>
        <button type="submit" 
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105">
            Login
        </button>
    </form>
    <div class="mt-6 text-center text-slate-500 text-sm">
        Belum punya akun? <a href="/variasi-motor/register" class="text-blue-500 hover:underline">Daftar di sini</a>
    </div>
</div>

</body>
</html>
