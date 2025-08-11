<?php
/**
 * File: views/admin/login.php
 * Deskripsi: Halaman tampilan untuk login admin.
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
    <title>Login Admin - Variasi Motor</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-gray-800 p-8 rounded-xl shadow-2xl w-full max-w-md mx-auto">
        <h2 class="text-3xl font-bold text-center text-white mb-6">Login Admin</h2>
        
        <?php
        // Tampilkan pesan error jika ada
        if (isset($_SESSION['admin_error_message'])) {
            echo '<div class="bg-red-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['admin_error_message'] . '</div>';
            unset($_SESSION['admin_error_message']);
        }
        ?>

        <form action="/variasi-motor/admin/login" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-gray-300 font-semibold mb-2">Username</label>
                <input type="text" id="username" name="username" required
                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
            </div>
            <div>
                <label for="password" class="block text-gray-300 font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
            </div>
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105">
                Login
            </button>
        </form>
    </div>

</body>
</html>
