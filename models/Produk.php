<?php
/**
 * File: models/Produk.php
 * Deskripsi: Kelas Model untuk mengelola data produk di database.
 * Menggunakan PDO untuk interaksi yang aman.
 */
class Produk {
    // Properti untuk koneksi database
    private $conn;
    private $table_name = "produk";
    private $kategori_table_name = "kategori";

    // Properti untuk data produk
    public $id;
    public $nama;
    public $deskripsi;
    public $harga;
    public $stok;
    public $gambar;
    public $kategori_id;
    public $nama_kategori;

    // Konstruktor untuk inisialisasi koneksi database
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Mengambil semua data produk dari database, termasuk nama kategori.
     * @return PDOStatement Hasil dari eksekusi query.
     */
    public function readAll() {
        // Query untuk mengambil semua produk dengan nama kategori, diurutkan berdasarkan id
        $query = "SELECT 
                    p.id, p.nama, p.deskripsi, p.harga, p.stok, p.gambar, p.kategori_id, k.nama_kategori
                  FROM " . $this->table_name . " p
                  LEFT JOIN " . $this->kategori_table_name . " k ON p.kategori_id = k.id
                  ORDER BY p.id DESC";
        
        // Siapkan statement PDO
        $stmt = $this->conn->prepare($query);
        
        // Eksekusi query
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Mengambil satu produk berdasarkan ID.
     * @return bool True jika produk ditemukan, False jika tidak.
     */
    public function readOne() {
        // Query untuk mengambil satu produk berdasarkan ID, termasuk nama kategori
        $query = "SELECT 
                    p.id, p.nama, p.deskripsi, p.harga, p.stok, p.gambar, p.kategori_id, k.nama_kategori 
                  FROM " . $this->table_name . " p
                  LEFT JOIN " . $this->kategori_table_name . " k ON p.kategori_id = k.id
                  WHERE p.id = ? LIMIT 0,1";

        // Siapkan statement PDO
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(1, $this->id);

        // Eksekusi query
        $stmt->execute();

        // Ambil data produk
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika produk ditemukan, isi properti objek ini
        if ($row) {
            $this->id = $row['id'];
            $this->nama = $row['nama'];
            $this->deskripsi = $row['deskripsi'];
            $this->harga = $row['harga'];
            $this->stok = $row['stok'];
            $this->gambar = $row['gambar'];
            $this->kategori_id = $row['kategori_id'];
            $this->nama_kategori = $row['nama_kategori'];
            return true;
        }

        return false;
    }

    /**
     * Membuat produk baru di database.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function create() {
        // Query untuk memasukkan data produk baru
        $query = "INSERT INTO " . $this->table_name . " SET nama=:nama, deskripsi=:deskripsi, harga=:harga, stok=:stok, gambar=:gambar, kategori_id=:kategori_id";
        
        // Siapkan statement PDO
        $stmt = $this->conn->prepare($query);

        // Sanitasi input
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        $this->harga = htmlspecialchars(strip_tags($this->harga));
        $this->stok = htmlspecialchars(strip_tags($this->stok));
        $this->gambar = htmlspecialchars(strip_tags($this->gambar));
        $this->kategori_id = htmlspecialchars(strip_tags($this->kategori_id));

        // Bind parameter
        $stmt->bindParam(":nama", $this->nama);
        // Periksa apakah deskripsi kosong dan bind sebagai NULL jika demikian
        $stmt->bindParam(":deskripsi", $this->deskripsi, (is_null($this->deskripsi) ? PDO::PARAM_NULL : PDO::PARAM_STR));
        $stmt->bindParam(":harga", $this->harga);
        $stmt->bindParam(":stok", $this->stok);
        $stmt->bindParam(":gambar", $this->gambar);
        // Periksa apakah kategori_id kosong dan bind sebagai NULL jika demikian
        $stmt->bindParam(":kategori_id", $this->kategori_id, (is_null($this->kategori_id) || $this->kategori_id === '') ? PDO::PARAM_NULL : PDO::PARAM_INT);

        // Eksekusi query
        if($stmt->execute()){
            return true;
        }
        
        return false;
    }

    /**
     * Memperbarui data produk di database.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function update() {
        // Query untuk memperbarui data produk
        $query = "UPDATE " . $this->table_name . " SET nama=:nama, deskripsi=:deskripsi, harga=:harga, stok=:stok, gambar=:gambar, kategori_id=:kategori_id WHERE id=:id";
        
        // Siapkan statement PDO
        $stmt = $this->conn->prepare($query);

        // Sanitasi input
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        $this->harga = htmlspecialchars(strip_tags($this->harga));
        $this->stok = htmlspecialchars(strip_tags($this->stok));
        $this->gambar = htmlspecialchars(strip_tags($this->gambar));
        $this->kategori_id = htmlspecialchars(strip_tags($this->kategori_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind parameter
        $stmt->bindParam(":nama", $this->nama);
        // Periksa apakah deskripsi kosong dan bind sebagai NULL jika demikian
        $stmt->bindParam(":deskripsi", $this->deskripsi, (is_null($this->deskripsi) ? PDO::PARAM_NULL : PDO::PARAM_STR));
        $stmt->bindParam(":harga", $this->harga);
        $stmt->bindParam(":stok", $this->stok);
        $stmt->bindParam(":gambar", $this->gambar);
        // Periksa apakah kategori_id kosong dan bind sebagai NULL jika demikian
        $stmt->bindParam(":kategori_id", $this->kategori_id, (is_null($this->kategori_id) || $this->kategori_id === '') ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id);

        // Eksekusi query
        if($stmt->execute()){
            return true;
        }
        
        return false;
    }

    /**
     * Menghapus produk dari database.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function delete() {
        // Query untuk menghapus produk
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        // Siapkan statement PDO
        $stmt = $this->conn->prepare($query);

        // Sanitasi dan bind parameter
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        // Eksekusi query
        if($stmt->execute()){
            return true;
        }

        return false;
    }
}
