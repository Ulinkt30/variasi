<?php
/**
 * File: models/Admin.php
 * Deskripsi: Kelas Model untuk mengelola data admin di database.
 * Menggunakan PDO untuk interaksi yang aman.
 */
class Admin {
    // Properti untuk koneksi database
    private $conn;
    private $table_name = "admins";

    // Properti untuk data admin
    public $id;
    public $username;
    public $password;

    // Konstruktor untuk inisialisasi koneksi database
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Melakukan login admin.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function login() {
        // Query untuk mencari admin berdasarkan username
        $query = "SELECT id, username, password FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";

        // Siapkan statement PDO
        $stmt = $this->conn->prepare($query);

        // Sanitasi dan bind parameter
        $this->username = htmlspecialchars(strip_tags($this->username));
        $stmt->bindParam(':username', $this->username);

        // Eksekusi query
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika admin ditemukan
        if ($row) {
            // Verifikasi password yang diinput dengan password hash di database
            if (password_verify($this->password, $row['password'])) {
                // Set properti id
                $this->id = $row['id'];
                return true;
            }
        }
        
        return false;
    }

    /**
     * Menambahkan admin baru ke database.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function create() {
        // Cek apakah username sudah ada
        if ($this->findAdminByUsername()) {
            return false;
        }

        // Query untuk memasukkan data admin baru
        $query = "INSERT INTO " . $this->table_name . " (username, password) VALUES (:username, :password)";
        
        // Siapkan statement PDO
        $stmt = $this->conn->prepare($query);

        // Sanitasi input
        $this->username = htmlspecialchars(strip_tags($this->username));
        // Hash password untuk keamanan
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind parameter
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);

        // Eksekusi query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    /**
     * Menghapus admin dari database.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if($stmt->execute()){
            return true;
        }

        return false;
    }

    /**
     * Metode pembantu untuk mencari admin berdasarkan username.
     * @return bool True jika admin ditemukan, False jika tidak.
     */
    public function findAdminByUsername() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
