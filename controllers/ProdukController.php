<?php
/**
 * File: controllers/ProdukController.php
 * Deskripsi: Controller ini menangani semua logika terkait produk,
 * termasuk operasi CRUD untuk admin dan fungsionalitas katalog untuk pengguna.
 */

// Sertakan file koneksi database dan model Produk serta Kategori
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Produk.php';
require_once __DIR__ . '/../models/Kategori.php';

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ProdukController {
    private $db;
    private $produk;
    private $kategori;

    public function __construct($db) {
        $this->db = $db;
        $this->produk = new Produk($this->db);
        $this->kategori = new Kategori($this->db);
    }

    /**
     * Menampilkan semua produk (katalog) atau produk tertentu berdasarkan pencarian.
     * @return array Array produk.
     */
    public function readAll() {
        // Panggil metode readAll dari model Produk
        $stmt = $this->produk->readAll();
        $num = $stmt->rowCount();
        $produks_arr = [];

        if ($num > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $produk_item = [
                    "id" => $id,
                    "nama" => $nama,
                    "deskripsi" => $deskripsi,
                    "harga" => $harga,
                    "stok" => $stok,
                    "gambar" => $gambar,
                    "kategori_id" => $kategori_id,
                    "nama_kategori" => $nama_kategori
                ];
                array_push($produks_arr, $produk_item);
            }
        }
        return $produks_arr;
    }

    /**
     * Menampilkan detail satu produk.
     * @param int $id ID produk.
     * @return array|null Array data produk atau null jika tidak ditemukan.
     */
    public function readOne($id) {
        // Atur properti id model Produk
        $this->produk->id = $id;

        // Panggil metode readOne dari model Produk
        if ($this->produk->readOne()) {
            $produk_item = [
                "id" => $this->produk->id,
                "nama" => $this->produk->nama,
                "deskripsi" => $this->produk->deskripsi,
                "harga" => $this->produk->harga,
                "stok" => $this->produk->stok,
                "gambar" => $this->produk->gambar,
                "kategori_id" => $this->produk->kategori_id,
                "nama_kategori" => $this->produk->nama_kategori
            ];
            return $produk_item;
        }
        return null;
    }

    /**
     * Menangani pembuatan produk baru (khusus admin).
     */
    public function create() {
        // Cek jika request adalah POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ambil data dari form
            $this->produk->nama = $_POST['nama'] ?? '';
            $this->produk->deskripsi = $_POST['deskripsi'] ?? null; // Deskripsi opsional
            // Harga diambil dari hidden input yang sudah diformat
            $this->produk->harga = preg_replace('/[^\d]/', '', ($_POST['harga'] ?? ''));
            $this->produk->stok = $_POST['stok'] ?? '';
            $this->produk->kategori_id = $_POST['kategori_id'] ?? null; // Kategori opsional
            
            // Tangani upload gambar
            $target_dir = __DIR__ . "/../assets/uploads/";
            $this->produk->gambar = '';
            
            if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] == 0) {
                $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $uploadOk = 1;

                // Cek apakah file gambar adalah gambar asli atau file palsu
                $check = getimagesize($_FILES["gambar"]["tmp_name"]);
                if($check === false) {
                    $_SESSION['error_message'] = "File bukan gambar.";
                    $uploadOk = 0;
                }
                
                // Cek ukuran file
                if ($_FILES["gambar"]["size"] > 500000) {
                    $_SESSION['error_message'] = "Maaf, ukuran file terlalu besar.";
                    $uploadOk = 0;
                }
    
                // Izinkan format file tertentu
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" && $imageFileType != "webp") {
                    $_SESSION['error_message'] = "Maaf, hanya file JPG, JPEG, PNG, GIF & WEBP yang diizinkan.";
                    $uploadOk = 0;
                }
    
                if ($uploadOk == 1) {
                    // Hasilkan nama file unik untuk menghindari duplikasi
                    $unique_filename = uniqid() . '.' . $imageFileType;
                    $final_target_file = $target_dir . $unique_filename;

                    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $final_target_file)) {
                        $this->produk->gambar = $unique_filename;
                    } else {
                        $_SESSION['error_message'] = "Maaf, ada error saat mengunggah file Anda.";
                        header("Location: /variasi-motor/admin/kelola_produk");
                        exit();
                    }
                } else {
                    header("Location: /variasi-motor/admin/kelola_produk");
                    exit();
                }
            }


            if ($this->produk->create()) {
                $_SESSION['success_message'] = "Produk berhasil ditambahkan.";
            } else {
                $_SESSION['error_message'] = "Gagal menambahkan produk.";
            }
            
            header("Location: /variasi-motor/admin/kelola_produk?tab=tab2");
            exit();
        }
    }

    /**
     * Menangani pembaruan produk (khusus admin).
     */
    public function update() {
        // Cek jika request adalah POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ambil data dari form
            $this->produk->id = $_POST['id'] ?? '';
            $this->produk->nama = $_POST['nama'] ?? '';
            $this->produk->deskripsi = $_POST['deskripsi'] ?? null; // Deskripsi opsional
            $this->produk->harga = preg_replace('/[^\d]/', '', ($_POST['harga'] ?? ''));
            $this->produk->stok = $_POST['stok'] ?? '';
            $this->produk->kategori_id = $_POST['kategori_id'] ?? null; // Kategori opsional
            
            // Tangani upload gambar (opsional)
            if (isset($_FILES["gambar"]) && !empty($_FILES["gambar"]["name"])) {
                $target_dir = __DIR__ . "/../assets/uploads/";
                
                // Ambil nama file lama dari database untuk dihapus
                $old_produk = new Produk($this->db);
                $old_produk->id = $this->produk->id;
                $old_produk->readOne();
                $old_gambar = $old_produk->gambar;
                
                $imageFileType = strtolower(pathinfo(basename($_FILES["gambar"]["name"]), PATHINFO_EXTENSION));
                $uploadOk = 1;
                
                // Cek apakah file gambar adalah gambar asli atau file palsu
                $check = getimagesize($_FILES["gambar"]["tmp_name"]);
                if($check === false) {
                    $_SESSION['error_message'] = "File baru bukan gambar.";
                    $uploadOk = 0;
                }

                // Cek ukuran file
                if ($_FILES["gambar"]["size"] > 500000) {
                    $_SESSION['error_message'] = "Maaf, ukuran file baru terlalu besar.";
                    $uploadOk = 0;
                }
    
                // Izinkan format file tertentu
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" && $imageFileType != "webp") {
                    $_SESSION['error_message'] = "Maaf, hanya file JPG, JPEG, PNG, GIF & WEBP yang diizinkan.";
                    $uploadOk = 0;
                }

                if ($uploadOk == 1) {
                    // Hapus file lama
                    if (!empty($old_gambar) && file_exists($target_dir . $old_gambar)) {
                        unlink($target_dir . $old_gambar);
                    }
                    
                    // Hasilkan nama file unik untuk menghindari duplikasi
                    $unique_filename = uniqid() . '.' . $imageFileType;
                    $final_target_file = $target_dir . $unique_filename;

                    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $final_target_file)) {
                        $this->produk->gambar = $unique_filename;
                    } else {
                        $_SESSION['error_message'] = "Maaf, ada error saat mengunggah file gambar baru.";
                        header("Location: /variasi-motor/admin/kelola_produk?tab=tab2");
                        exit();
                    }
                } else {
                    header("Location: /variasi-motor/admin/kelola_produk?tab=tab2");
                    exit();
                }
            } else {
                // Jika tidak ada gambar baru, gunakan gambar yang sudah ada
                $old_produk = new Produk($this->db);
                $old_produk->id = $this->produk->id;
                $old_produk->readOne();
                $this->produk->gambar = $old_produk->gambar;
            }

            // Panggil metode update dari model Produk
            if ($this->produk->update()) {
                $_SESSION['success_message'] = "Produk berhasil diperbarui.";
            } else {
                $_SESSION['error_message'] = "Gagal memperbarui produk.";
            }
            header("Location: /variasi-motor/admin/kelola_produk?tab=tab2");
            exit();
        }
    }

    /**
     * Menangani penghapusan produk (khusus admin).
     */
    public function delete() {
        // Cek jika request adalah POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->produk->id = $_POST['id'] ?? '';
            
            // Ambil data produk untuk menghapus file gambar
            $old_produk = new Produk($this->db);
            $old_produk->id = $this->produk->id;
            $old_produk->readOne();
            $gambar_file = $old_produk->gambar;

            if ($this->produk->delete()) {
                // Hapus file gambar dari server
                $target_dir = __DIR__ . "/../assets/uploads/";
                if (!empty($gambar_file) && file_exists($target_dir . $gambar_file)) {
                    unlink($target_dir . $gambar_file);
                }
                $_SESSION['success_message'] = "Produk berhasil dihapus.";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus produk.";
            }
            header("Location: /variasi-motor/admin/kelola_produk?tab=tab2");
            exit();
        }
    }
}
