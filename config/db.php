<?php
/**
 * File: config/db.php
 * Deskripsi: File ini berisi konfigurasi dan skrip untuk koneksi ke database menggunakan PDO.
 */

// Konfigurasi koneksi database
// Ganti nilai-nilai di bawah ini dengan kredensial database Anda sendiri
$servername = "localhost"; // Nama server database, biasanya "localhost"
$username = "root";        // Nama pengguna database Anda
$password = "";            // Kata sandi database Anda
$dbname = "variasi_motor"; // Nama database yang akan kita gunakan

try {
    // Membuat instance PDO (PHP Data Objects) untuk koneksi ke database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Mengatur mode error PDO ke exception, yang akan menampilkan error secara rinci
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Pesan sukses (opsional, bisa dihapus setelah koneksi berhasil)
    // echo "Koneksi ke database berhasil!";

} catch(PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error
    // Pesan ini hanya untuk developer, jangan tampilkan di lingkungan produksi
    die("Koneksi gagal: " . $e->getMessage());
}
