<?php
/**
 * File: models/User.php
 * Deskripsi: Kelas Model untuk mengelola data pengguna di database.
 * Menggunakan PDO untuk interaksi yang aman.
 */

class User {
    // Properti untuk menyimpan koneksi database
    private $conn;
    private $table_name = "users";

    // Properti untuk menyimpan data user
    public $id;
    public $username;
    public $password;

    // Konstruktor untuk inisialisasi koneksi database
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Mendaftarkan pengguna baru ke database.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function register() {
        // Cek apakah username sudah ada
        if ($this->findUserByUsername()) {
            return false;
        }

        // Query untuk memasukkan data pengguna baru
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
     * Melakukan login pengguna.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function login() {
        // Query untuk mencari pengguna berdasarkan username
        $query = "SELECT id, username, password FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";

        // Siapkan statement PDO
        $stmt = $this->conn->prepare($query);

        // Sanitasi dan bind parameter
        $this->username = htmlspecialchars(strip_tags($this->username));
        $stmt->bindParam(':username', $this->username);

        // Eksekusi query
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika pengguna ditemukan
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
     * Metode pembantu untuk mencari pengguna berdasarkan username.
     * @return bool True jika pengguna ditemukan, False jika tidak.
     */
    public function findUserByUsername() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
