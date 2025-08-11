<?php
/**
 * File: models/Pesanan.php
 * Deskripsi: Kelas Model untuk mengelola pesanan pengguna di database.
 */
class Pesanan {
    private $conn;
    private $table_name = "pesanan";
    private $detail_table_name = "detail_pesanan";

    // Properti untuk data pesanan
    public $id;
    public $user_id;
    public $nama_penerima;
    public $alamat_pengiriman;
    public $telepon_penerima;
    public $bukti_pembayaran;
    public $tanggal_pesanan;
    public $total_harga;
    public $status_pesanan;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Membuat pesanan baru.
     * @return int|bool ID pesanan baru jika berhasil, False jika gagal.
     */
    public function create() {
        // Query untuk memasukkan pesanan baru dengan data pengiriman dan pembayaran
        $query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, nama_penerima=:nama_penerima, alamat_pengiriman=:alamat_pengiriman, telepon_penerima=:telepon_penerima, bukti_pembayaran=:bukti_pembayaran, total_harga=:total_harga, status_pesanan=:status_pesanan";
        
        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->nama_penerima = htmlspecialchars(strip_tags($this->nama_penerima));
        $this->alamat_pengiriman = htmlspecialchars(strip_tags($this->alamat_pengiriman));
        $this->telepon_penerima = htmlspecialchars(strip_tags($this->telepon_penerima));
        $this->bukti_pembayaran = htmlspecialchars(strip_tags($this->bukti_pembayaran));
        $this->total_harga = htmlspecialchars(strip_tags($this->total_harga));
        $this->status_pesanan = htmlspecialchars(strip_tags($this->status_pesanan));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":nama_penerima", $this->nama_penerima);
        $stmt->bindParam(":alamat_pengiriman", $this->alamat_pengiriman);
        $stmt->bindParam(":telepon_penerima", $this->telepon_penerima);
        $stmt->bindParam(":bukti_pembayaran", $this->bukti_pembayaran);
        $stmt->bindParam(":total_harga", $this->total_harga);
        $stmt->bindParam(":status_pesanan", $this->status_pesanan);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Menambahkan detail produk ke tabel detail_pesanan.
     * @param int $pesanan_id ID pesanan induk.
     * @param int $produk_id ID produk.
     * @param int $jumlah Jumlah produk.
     * @param float $harga_satuan Harga produk per unit saat dipesan.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function addDetail($pesanan_id, $produk_id, $jumlah, $harga_satuan) {
        $query = "INSERT INTO " . $this->detail_table_name . " SET pesanan_id=:pesanan_id, produk_id=:produk_id, jumlah=:jumlah, harga_satuan=:harga_satuan";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":pesanan_id", $pesanan_id);
        $stmt->bindParam(":produk_id", $produk_id);
        $stmt->bindParam(":jumlah", $jumlah);
        $stmt->bindParam(":harga_satuan", $harga_satuan);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Mengambil riwayat pesanan untuk pengguna tertentu.
     * @param string $status_filter Status pesanan untuk difilter.
     * @return PDOStatement Hasil dari eksekusi query.
     */
    public function readAll($status_filter = 'Semua') {
        $query = "SELECT id, tanggal_pesanan, total_harga, status_pesanan FROM " . $this->table_name . " WHERE user_id = :user_id";
        
        // Tambahkan filter status jika tidak 'Semua'
        if ($status_filter != 'Semua') {
            // Memetakan string dari URL ke status yang ada di database
            $status_map = [
                'Pending' => 'Pending',
                'Menunggu Dikemas' => 'Pending',
                'Dikemas' => 'Dikemas',
                'Dikirim' => 'Dikirim',
                'Selesai' => 'Selesai',
                'Dibatalkan' => 'Dibatalkan'
            ];
            $status_db = $status_map[$status_filter] ?? null;

            if ($status_db) {
                $query .= " AND status_pesanan = :status_pesanan";
            }
        }

        $query .= " ORDER BY tanggal_pesanan DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);

        if (isset($status_db)) {
            $stmt->bindParam(":status_pesanan", $status_db);
        }

        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Mengambil detail satu pesanan.
     * @param int $id ID pesanan.
     * @return array|bool Array data pesanan jika ditemukan, False jika tidak.
     */
    public function readOne($id) {
        $query = "SELECT id, user_id, nama_penerima, alamat_pengiriman, telepon_penerima, bukti_pembayaran, tanggal_pesanan, total_harga, status_pesanan FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil detail produk dari satu pesanan.
     * @param int $pesanan_id ID pesanan.
     * @return array Array detail produk.
     */
    public function getDetailByPesananId($pesanan_id) {
        $query = "SELECT d.produk_id, d.jumlah, d.harga_satuan, p.nama as nama_produk, p.gambar as gambar 
                  FROM " . $this->detail_table_name . " d
                  JOIN produk p ON d.produk_id = p.id
                  WHERE d.pesanan_id = :pesanan_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pesanan_id", $pesanan_id);
        $stmt->execute();
        
        $details = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $details[] = $row;
        }
        return $details;
    }

    /**
     * Memperbarui status pesanan.
     * @param int $id ID pesanan.
     * @param string $status_baru Status baru pesanan.
     * @return bool True jika berhasil, False jika gagal.
     */
    public function updateStatus($id, $status_baru) {
        $query = "UPDATE " . $this->table_name . " SET status_pesanan = :status_baru WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status_baru", $status_baru);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
