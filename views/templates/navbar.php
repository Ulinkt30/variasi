<?php
/**
 * File: views/templates/navbar.php
 * Deskripsi: Template navbar untuk semua halaman pengguna.
 * Menampilkan menu navigasi, status login, dan ikon keranjang.
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sertakan file koneksi database dan controller Keranjang
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/KeranjangController.php';

$cart_item_count = 0;
if (isset($_SESSION['user_id'])) {
    $keranjangController = new KeranjangController($conn);
    $cart_items = $keranjangController->readAll();
    $cart_item_count = count($cart_items);
}
?>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.2/dist/cdn.min.js" defer></script>

<nav x-data="{ open: false }" class="bg-slate-50 shadow-lg fixed top-0 w-full z-50">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <!-- Logo dan tombol hamburger menu -->
        <a href="/variasi-motor/" class="text-2xl font-bold text-slate-800 hover:text-blue-500 transition-colors duration-300">
            Daniel Variasi
        </a>
        
        <!-- Hamburger Menu Button dan Ikon Keranjang (hanya terlihat di mobile) -->
        <div class="md:hidden flex items-center space-x-4">
            <!-- Ikon Keranjang untuk Mobile -->
            <a href="/variasi-motor/keranjang" class="relative text-slate-800 hover:text-blue-500 transition-colors duration-300">
                <i class="fas fa-shopping-cart text-xl"></i>
                <?php if ($cart_item_count > 0): ?>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center"><?php echo $cart_item_count; ?></span>
                <?php endif; ?>
            </a>
            <button @click="open = !open" class="text-slate-800 focus:outline-none">
                <i class="fas" :class="open ? 'fa-times' : 'fa-bars'"></i>
            </button>
        </div>

        <!-- Menu Desktop -->
        <div class="hidden md:flex items-center space-x-6">
            
            <!-- Ikon Keranjang untuk Desktop -->
            <a href="/variasi-motor/keranjang" class="relative text-slate-800 hover:text-blue-500 transition-colors duration-300">
                <i class="fas fa-shopping-cart text-xl"></i>
                <?php if ($cart_item_count > 0): ?>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center"><?php echo $cart_item_count; ?></span>
                <?php endif; ?>
            </a>

            <a href="/variasi-motor/katalog" class="text-slate-800 hover:text-blue-500 transition-colors duration-300 font-semibold">
                Katalog
            </a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <!-- Tombol dengan ikon profil dan username -->
                    <button @click="open = !open" class="text-slate-800 hover:text-blue-500 transition-colors duration-300 font-semibold flex items-center space-x-2 focus:outline-none">
                        <i class="fas fa-user-circle text-2xl"></i>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-3 w-48 bg-white rounded-lg shadow-xl overflow-hidden"
                         style="display: none;">
                        <a href="/variasi-motor/profil" class="block px-4 py-3 text-sm text-slate-800 hover:bg-blue-500 hover:text-white">
                            Profil
                        </a>
                        <a href="/variasi-motor/riwayat_pesanan" class="block px-4 py-3 text-sm text-slate-800 hover:bg-blue-500 hover:text-white">
                            Pesanan Saya
                        </a>
                        <a href="/variasi-motor/logout" class="block px-4 py-3 text-sm text-red-500 hover:bg-blue-500 hover:text-white">
                            Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/variasi-motor/login" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full transition-colors duration-300">
                    Login
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Overlay semi-transparan untuk menu mobile -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden"
         style="top: 64px; display: none;"
         @click="open = false">
    </div>

    <!-- Menu Mobile dengan efek geser dari kanan -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300 transform" 
         x-transition:enter-start="translate-x-full" 
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed right-0 z-50 w-64 bg-slate-50 shadow-lg md:hidden"
         style="top: 64px; height: calc(100vh - 64px); display: none;">
        <div class="flex flex-col h-full p-4 space-y-2">
            <!-- Isi menu mobile -->
            <a href="/variasi-motor/katalog" class="block text-slate-800 hover:text-blue-500 transition-colors duration-300 font-semibold">
                Katalog
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/variasi-motor/profil" class="block text-slate-800 hover:text-blue-500 transition-colors duration-300 font-semibold">
                    Profil
                </a>
                <a href="/variasi-motor/riwayat_pesanan" class="block text-slate-800 hover:text-blue-500 transition-colors duration-300 font-semibold">
                    Pesanan Saya
                </a>
                <a href="/variasi-motor/logout" class="block text-red-500 hover:text-red-600 transition-colors duration-300 font-semibold">
                    Logout
                </a>
            <?php else: ?>
                <a href="/variasi-motor/login" class="block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full text-center transition-colors duration-300">
                    Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Div untuk memberi jarak agar konten tidak tertutup navbar fixed -->
<div class="pt-20"></div>
