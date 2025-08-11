<?php
/**
 * File: models/Keranjang.php
 * Deskripsi: Kelas Model untuk mengelola keranjang belanja pengguna di database.
 * Menggunakan PDO untuk interaksi yang aman.
 */
class Keranjang {
    // Properti untuk koneksi database dan nama tabel
    private $conn;
    private $table_name = "keranjang";
    private $produk_table_name = "produk";

    // Properti untuk data keranjang
    public $id;
    public $user_id;
    public $produk_id;
    public $jumlah;

    // Konstruktor untuk inisialisasi koneksi database
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Menambahkan produk ke keranjang atau memperbarui jumlahnya jika sudah ada.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function addToCart() {
        // Cek apakah produk sudah ada di keranjang pengguna
        $query = "SELECT id, jumlah FROM " . $this->table_name . " WHERE user_id = :user_id AND produk_id = :produk_id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":produk_id", $this->produk_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Jika produk sudah ada, perbarui jumlahnya
            $this->id = $row['id'];
            $new_quantity = $row['jumlah'] + $this->jumlah;
            $query = "UPDATE " . $this->table_name . " SET jumlah = :jumlah WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":jumlah", $new_quantity);
            $stmt->bindParam(":id", $this->id);
        } else {
            // Jika produk belum ada, tambahkan item baru
            $query = "INSERT INTO " . $this->table_name . " (user_id, produk_id, jumlah) VALUES (:user_id, :produk_id, :jumlah)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":produk_id", $this->produk_id);
            $stmt->bindParam(":jumlah", $this->jumlah);
        }
        
        // Eksekusi query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Mengambil semua item keranjang untuk pengguna tertentu.
     * @return PDOStatement Hasil dari eksekusi query.
     */
    public function readAll() {
        $query = "SELECT 
                    k.id as keranjang_id, 
                    k.user_id, 
                    k.produk_id, 
                    k.jumlah, 
                    p.nama, 
                    p.harga, 
                    p.gambar,
                    p.stok
                  FROM " . $this->table_name . " k
                  JOIN " . $this->produk_table_name . " p ON k.produk_id = p.id
                  WHERE k.user_id = :user_id
                  ORDER BY k.id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Menghapus item dari keranjang.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
