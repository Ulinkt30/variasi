<?php
/**
 * File: views/admin/templates/sidebar.php
 * Deskripsi: Template sidebar untuk panel admin.
 * Berisi navigasi ke halaman-halaman utama admin.
 */
?>
<aside class="w-64 h-screen bg-white text-slate-800 shadow-xl fixed">
    <div class="p-6 text-center border-b border-slate-200">
        <h2 class="text-2xl font-bold text-blue-500">Admin Panel</h2>
    </div>
    <nav class="mt-8">
        <a href="/variasi-motor/admin/dashboard" class="flex items-center p-4 text-slate-500 hover:bg-slate-100 hover:text-blue-500 transition-colors duration-200">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
        </a>
        <a href="/variasi-motor/admin/kelola_produk" class="flex items-center p-4 text-slate-500 hover:bg-slate-100 hover:text-blue-500 transition-colors duration-200">
            <i class="fas fa-box-open mr-3"></i> Kelola Produk
        </a>
        <a href="/variasi-motor/admin/kelola_pesanan" class="flex items-center p-4 text-slate-500 hover:bg-slate-100 hover:text-blue-500 transition-colors duration-200">
            <i class="fas fa-shopping-basket mr-3"></i> Kelola Pesanan
        </a>
        <a href="/variasi-motor/admin/kelola_pengguna" class="flex items-center p-4 text-slate-500 hover:bg-slate-100 hover:text-blue-500 transition-colors duration-200">
            <i class="fas fa-users mr-3"></i> Kelola Pengguna
        </a>
    </nav>
    <div class="absolute bottom-0 w-full p-4 border-t border-slate-200">
        <a href="/variasi-motor/admin/logout" class="flex items-center justify-center bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-300">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
    </div>
</aside>
