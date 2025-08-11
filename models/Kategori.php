<?php
/**
 * File: models/Kategori.php
 * Deskripsi: Kelas Model untuk mengelola data kategori di database.
 */
class Kategori {
    private $conn;
    private $table_name = "kategori";

    // Properti untuk data kategori
    public $id;
    public $nama_kategori;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Mengambil semua kategori dari database.
     * @return PDOStatement Hasil dari eksekusi query.
     */
    public function readAll() {
        $query = "SELECT id, nama_kategori FROM " . $this->table_name . " ORDER BY nama_kategori";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Membuat kategori baru di database.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nama_kategori) VALUES (:nama_kategori)";
        
        $stmt = $this->conn->prepare($query);
        $this->nama_kategori = htmlspecialchars(strip_tags($this->nama_kategori));
        $stmt->bindParam(":nama_kategori", $this->nama_kategori);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Memperbarui kategori di database.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nama_kategori = :nama_kategori WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $this->nama_kategori = htmlspecialchars(strip_tags($this->nama_kategori));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":nama_kategori", $this->nama_kategori);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Menghapus kategori dari database.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
