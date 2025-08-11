- Membuat tabel `users`
CREATE TABLE `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuat tabel `admins`
CREATE TABLE `admins` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuat tabel `kategori`
CREATE TABLE `kategori` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(255) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuat tabel `produk`
CREATE TABLE `produk` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT,
  `harga` DECIMAL(10, 2) NOT NULL,
  `stok` INT(11) NOT NULL DEFAULT 0,
  `gambar` VARCHAR(255) NOT NULL,
  `kategori_id` INT(11) UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`kategori_id`) REFERENCES `kategori`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuat tabel `pesanan`
CREATE TABLE `pesanan` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `nama_penerima` VARCHAR(255) NOT NULL,
  `alamat_pengiriman` TEXT NOT NULL,
  `telepon_penerima` VARCHAR(20) NOT NULL,
  `bukti_pembayaran` VARCHAR(255) DEFAULT NULL,
  `tanggal_pesanan` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `total_harga` DECIMAL(10, 2) NOT NULL,
  `status_pesanan` VARCHAR(50) NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuat tabel `detail_pesanan`
CREATE TABLE `detail_pesanan` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pesanan_id` INT(11) UNSIGNED NOT NULL,
  `produk_id` INT(11) UNSIGNED NOT NULL,
  `jumlah` INT(11) NOT NULL,
  `harga_satuan` DECIMAL(10, 2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`produk_id`) REFERENCES `produk`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Memasukkan data admin default
-- Password 'admin' telah di-hash
INSERT INTO `admins` (`username`, `password`) VALUES ('admin', '$2y$10$Ew.qA91YqWlU6B1xV.xJ..YkYlZq1.R4qT.p0.p.4.P.L.E.a.Q.');
