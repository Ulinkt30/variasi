-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Agu 2025 pada 16.13
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `variasi_motor`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$z1hzn45yCXumKxzP0q96i.6vITY8WG5QrzCOptL0quFmtHrsp7NsK', '2025-08-09 03:36:59', '2025-08-09 03:36:59'),
(2, 'ulin30', '$2y$10$GgrtJVAiqL1hjWq0DoveG.1DNaTBZYZ763xd70yy4KX69MCWO7TJW', '2025-08-10 18:55:27', '2025-08-10 18:55:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id` int(11) UNSIGNED NOT NULL,
  `pesanan_id` int(11) UNSIGNED NOT NULL,
  `produk_id` int(11) UNSIGNED NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id`, `pesanan_id`, `produk_id`, `jumlah`, `harga_satuan`) VALUES
(93, 75, 35, 1, 14500.00),
(94, 75, 53, 1, 1233421.00),
(95, 75, 52, 1, 1695000.00),
(96, 76, 52, 1, 1695000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `created_at`) VALUES
(14, 'Lampu &amp; Pencahayaan', '2025-08-10 19:30:00'),
(15, 'Baut &amp; Fastener', '2025-08-10 19:34:22'),
(16, 'asd', '2025-08-10 19:39:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `produk_id` int(11) UNSIGNED NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `keranjang`
--

INSERT INTO `keranjang` (`id`, `user_id`, `produk_id`, `jumlah`, `created_at`, `updated_at`) VALUES
(45, 2, 35, 1, '2025-08-10 18:46:29', '2025-08-10 18:46:29'),
(46, 2, 33, 5, '2025-08-10 19:03:52', '2025-08-10 19:03:52'),
(58, 6, 52, 1, '2025-08-10 20:42:32', '2025-08-10 20:42:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `nama_penerima` varchar(255) NOT NULL,
  `alamat_pengiriman` text NOT NULL,
  `telepon_penerima` varchar(20) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_pesanan` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_harga` decimal(10,2) NOT NULL,
  `status_pesanan` varchar(50) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id`, `user_id`, `nama_penerima`, `alamat_pengiriman`, `telepon_penerima`, `bukti_pembayaran`, `tanggal_pesanan`, `total_harga`, `status_pesanan`) VALUES
(75, 6, 'Quibusdam et ullamco', 'Voluptatem est eum e', '015283445579', 'IMG_20221111_144918.jpg', '2025-08-10 20:42:56', 2942921.00, 'Dikirim'),
(76, 6, 'Quibusdam et ullamco', 'Voluptatem est eum e', '015283445579', 'IMG_20221111_144918.jpg', '2025-08-10 20:44:23', 1695000.00, 'Pending');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `kategori_id` int(11) UNSIGNED DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `nama`, `deskripsi`, `harga`, `stok`, `kategori_id`, `gambar`, `created_at`, `updated_at`) VALUES
(15, 'HANDGRIP GRIP RCB ORIGINAL RACING BOY', 'PROMO &amp; GRATIS ONGKIR\r\n\r\n\r\n\r\nHANDGRIP MEREK AKAI ORIGINAL 100% \r\n\r\n\r\n\r\nUNIVERSAL MOTOR / BISA UNTUK SEMUA JENIS MOTOR\r\n\r\n\r\n\r\nPRODUK ASLI / ORIGINAL 100%\r\n\r\n\r\n\r\nPRODUK 100 % SESUAI DENGAN YANG ADA DIFOTO / REAL PICT\r\n\r\n\r\n\r\nBARANG READY STOCK !!!\r\n\r\n\r\n\r\nSELAMA IKLAN MASIH TAYANG YA BOSKU\r\n\r\n\r\n\r\nTERIMAKASIH !!!', 30000.00, 50, NULL, 'Screenshot_14.jpg', '2025-08-10 05:59:21', '2025-08-10 05:59:21'),
(16, 'Lampu Depan Led Biled CRF KLX KTM WR155 DTRACKER Supermoto Xcase', 'Harga satuan\r\n\r\nBarang dijamin sesuai foto dan video\r\n\r\n\r\n\r\nMerk Xcase\r\n\r\nBahan kuat dan kokoh\r\n\r\nTersedia senja : Putih Biru, Putih Merah, Ice Blue Merah\r\n\r\nCahaya dekat PUTIH, cahaya jauh KUNING\r\n\r\nUntuk CRF 150L, KLX BIGFOOT DTRACKER, WR 155 (PNP BATOK ORI)\r\n\r\nKabel model kaki 3 H4, tinggal colok\r\n\r\nMohon chat admin jika ada pertanyaan lebih lanjut\r\n\r\n\r\n\r\nUkuran :\r\n\r\nPanjang = 12.2 cm\r\n\r\nTinggi = 9.2 cm\r\n\r\nTebal = 5.1 cm\r\n\r\n\r\n\r\n- Pesanan sebelum jam 3 siang akan dikirim di hari yang sama\r\n\r\n- Wajib video unboxing sampai tes untuk klaim, tanpa video unb', 100000.00, 100, NULL, 'Screenshot_15.jpg', '2025-08-10 06:01:24', '2025-08-10 06:01:24'),
(18, 'Lampu Led Alis Slim 60 Cm Remot RGB Sein Running Fleksibel Motor Mobil', 'Lampu LED Alis Slim 60CM Remote RGB Sein Running Flexible Motor Mobil\r\n\r\n\r\n\r\nHarga Sepasang (2 PCS)\r\n\r\n\r\n\r\nLengkap dengan Box LAMPU LED ALIS SLIM 60CM REMOTE RGB SEIN RUNNING FLEXIBLE\r\n\r\n\r\n\r\nDengan menggunakan remote anda bisa mengatur warna led sesuai selera anda.\r\n\r\nModel terbaru dengan bahan yang lebih flexible.\r\n\r\n\r\n\r\nUkuran :\r\n\r\n\r\n\r\nPanjang 60 cm \r\n\r\nLebar 1.8 cm\r\n\r\nTebal 0.5 cm \r\n\r\n\r\n\r\nPackage Included: \r\n\r\n2 x LED DRL RGB 60 cm\r\n\r\n1 x Remote', 90000.00, 100, NULL, 'Screenshot_17.jpg', '2025-08-10 06:10:56', '2025-08-10 06:10:56'),
(19, 'Lampu Rem Led Running 8 &amp; 10 Mode Beat Karbu Bonus Jelly Flash Stoplamp Custom Running Auto Manual Lampu Belakang Motor Beat', 'Lampu Rem Led Running 8 &amp; 10 Mode Beat Karbu Bonus Jelly Flash Stoplamp Custom Running Auto Manual Lampu Belakang Motor Beat\r\nMOHON DI BACA TERLEBIH DAHULU DESKRIPSI PRODUK INI DENGAN TELITI YA.\r\n\r\n \r\n\r\nSpesifikasi lampu Rem/Stoplamp LED Running 8 &amp;10 Mode BEAT KARBU:\r\n\r\n \r\n\r\nTambahkan keselamatan dan gaya ekstra pada Beat kesayangan Anda dengan Lampu Rem/Stoplamp LED Running 8 &amp; 10 mode.\r\n\r\nLampu rem inovatif ini menawarkan fitur menarik yang akan membuat Anda terlihat lebih Stylish di jalan.\r\n\r\n \r\n\r\nBERIKUT ADALAH KEUNGGULAN DARI PRODUK INI:\r\n\r\n- Teknologi LED Terbaru: Lampu rem ini menggunakan teknologi LED terbaru untuk memberikan pencahayaan yang terang, tajam, dan tahan lama. Anda dapat berkendara dengan percaya diri bahkan dalam kondisi cahaya yang rendah.\r\n\r\n- Desain Stylish: Didesain dengan desain yang menarik, lampu rem ini tidak hanya berfungsi dengan baik, tetapi juga menambahkan sentuhan gaya pada motor Beat Anda. Tampil beda di jalanan dengan lampu rem yang futuristik ini\r\n\r\n \r\n\r\nTERSEDIA 2 VARIAN YAITU:\r\n\r\n\r\n\r\n1. Varian 8 mode = Menyala Running automatis dengan 8 nyala yang berbeda, bisa di stel kecepatannya. Pada saat di rem Stay / diam.\r\n\r\n\r\n\r\n2. Varian 10 mode = Menyala Running Manual &amp; Atomatis, ada tombol untuk memilih mode running dan ada stelan kecepatannya. Pada saat direm berkedip.\r\n\r\n \r\n\r\nBELI 1 PAKET SUDAH DAPAT:\r\n\r\n- Lampu Rem / Stoplamp LED Running Beat Karbu\r\n\r\n- Lem untuk menempelkan lampunya\r\n\r\n- BONUS stiker One Custom Modify\r\n\r\n- - Packingziplock / dus &amp; bubble wrap (packing super aman).\r\n\r\n\r\n\r\n\r\n\r\nGRATIS 1 PASANG (2PCS) LED CAMBUS UNTUK PEMBELIAN 2 PCS PRODUK\r\n\r\nGRATIS LAMPU TEMBAK LASER UNTUK PEMBELIAN 5 PCS PRODUK\r\n\r\n\r\n\r\nPERINGATAN:\r\n\r\n*TIDAK DI JUAL BESERTA REFLEKTOR\r\n\r\n*MIKA SMOKE DI JUAL TERPISAH\r\n\r\n \r\n\r\nSELAMAT BERBELANJA DI TOKO KAMI (ONE CUSTOM MODIFY) Mohon tanyakan stok terlebih dahulu\r\n\r\n\r\n\r\nOperasional Toko \r\n\r\nSenin-Sabtu : 08.00-17.00 WIB', 250000.00, 30, NULL, 'Screenshot_18.jpg', '2025-08-10 06:13:38', '2025-08-10 06:13:38'),
(20, 'Luffy Anime Character Keychain Premium Quality Thick Material for Children\'s Bag Accessories', ' Please make a video when opening the package, this video will serve as evidence if the item received is damaged/defective.\r\n\r\n- Without a video, we cannot carry out the replacement process.\r\n\r\n- If an error occurs on the part of the buyer, returns are not permitted. Examples of errors on the part of the buyer include selecting the wrong product variant, throwing the product causing damage, and opening the packaging incorrectly, which results in damage to the product.\r\n\r\n- All product-related questions can be asked directly to us via the chat feature.\r\n\r\n- Goods are guaranteed to be 100% new (brand new). Stock of goods is ready, if empty you will be informed.', 30000.00, 200, NULL, 'SDFSD.jpg', '2025-08-10 16:29:58', '2025-08-10 16:29:58'),
(21, 'T10 W5W LED Lights Silica Gel 4 Colors 8 Eyes RGB Flashing Running Flash Blinking DC 12V Dusk Brake Turn Signal SEN Jelly', 'T10 W5W LED Lights Silica Gel 4 Colors 8 Eyes RGB Flashing Running Flash Blinking DC 12V Dusk Brake Turn Signal SEN Jelly', 15000.00, 150, NULL, 'Screenshot_22.jpg', '2025-08-10 16:31:18', '2025-08-10 16:31:18'),
(22, 'Motorcycle Sticker &amp; Aquarium Background Size 45x50cm', 'Beli 1 pcs = 45 cm x 50 cm\r\n\r\nBuy 2 pcs = 45 cm x 100 cm without cutting\r\n\r\nBuy 3 pcs = 45 cm x 150 cm without cutting\r\n\r\nBuy 4 pcs = 45 cm x 200 cm without cutting\r\n\r\nBuy 5 pcs = 45 cm x 250 cm without cutting', 9900.00, 250, NULL, 'Screenshot_24.jpg', '2025-08-10 18:09:26', '2025-08-10 18:09:26'),
(23, 'STICKER PACK RACING - BUNNY DECAL STICKER PACK RACING', '- Print Cut (already cut according to the image pattern)\r\n\r\n- Weather resistant\r\n\r\n- Sharp colors\r\n\r\n- Sharp design, image does not break\r\n\r\n- Can be used indoors or outdoors\r\n\r\n- Glossy coated/laminated (shiny) makes the sticker look more elegant and more durable             ', 5000.00, 500, NULL, 'Screenshot_25.jpg', '2025-08-10 18:11:59', '2025-08-10 18:11:59'),
(24, 'ANTI-HASSLE MOTORCYCLE', '1. PCX 150-160\r\n\r\n2. NMAX OLD-NEW\r\n\r\n3. VARIOUS 110-125-150-160\r\n\r\n4. SCOOPY OLD-NEW\r\n\r\n5. BEAT OLD-NEW\r\n\r\n6. AEROX OLD-NEW\r\n\r\n7. SUPRA X 125\r\n\r\n8. MIO GT 115/125/MIO M3/J/SMILE/SPORTY\r\n\r\n9. XEON\r\n\r\n10. FINO\r\n\r\n11. FAZZIO\r\n\r\n12. XRIDE OLD\r\n\r\n13. GENIUS\r\n\r\n14. LEXI\r\n\r\n15. ADV 150/160', 89000.00, 50, NULL, 'Screenshot_26.jpg', '2025-08-10 18:14:03', '2025-08-10 18:14:03'),
(25, 'Titanium Disc Bolts for Honda Beat Vario Scoopy PCX Spacy Grand Knok Thread 12 Length 2 cm Grade 5', '- If the motorbike area has been replaced with accessories / is different from the product photo, please ask the Admin first, because if the product cannot be installed, it is not our responsibility.\r\n\r\n\r\n\r\n- We have tried to make the color of the titanium bolts as close to the original color as possible in the photos. The color of titanium bolts is greatly affected by lighting, so they may vary slightly when viewed in low light.\r\n\r\n\r\n\r\n- We do not accept complaints or returns because we have sent the product according to the order.\r\n\r\n\r\n\r\n- Ordering a product means agreeing to the above terms from us.\r\n\r\n\r\n\r\nHappy Shopping…!\r\n\r\n', 25000.00, 100, NULL, 'Screenshot_27.jpg', '2025-08-10 18:16:01', '2025-08-10 18:16:01'),
(26, 'Paket 1 bks isi 25 pcs Baut hexagon baja 8.8 M6 full drat', '- Price shown is for 1 box of 25 pcs.\r\n\r\n- Price includes nuts\r\n\r\n- Size M6-1.0pitch\r\n\r\n- Full thread model\r\n\r\n- Grade 8.8 steel material', 13000.00, 100, NULL, 'Screenshot_28.jpg', '2025-08-10 18:17:21', '2025-08-10 18:17:21'),
(27, 'Custom BAUT TANAM PLAT Motor 2PCS', 'PLAT MOTOR BAUT TANAM\r\n\r\nHARGA SEPASANG DEPAN BELAKANG\r\n\r\nBAHAN ALMUNIUM TEBAL\r\n\r\nBAHAN PELAPIS GALVANIS TAHAN KARAT\r\n\r\nCAT DASAR EPOXY FILLER ', 150000.00, 100, NULL, 'Screenshot_29.jpg', '2025-08-10 18:18:29', '2025-08-10 18:18:29'),
(28, 'Cat PU JetBlack 250ml', 'Cat PU JetBlack 250ml', 50.00, 50, NULL, 'Screenshot_30.jpg', '2025-08-10 18:19:12', '2025-08-10 18:19:12'),
(29, 'paket Lampu Kolong + Modul Kedip 6 Mode 12 Volt Semua Motor', 'Deskripsi Produk\r\n\r\nGaransi 100% From seller!\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nRecomended Original Led Premium\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nPaket lengkap lampu kolong + Modul Relay 12 Volt', 2500000.00, 100, 14, 'Screenshot_31.jpg', '2025-08-10 18:19:55', '2025-08-10 19:30:32'),
(30, 'striping custom mio sporty motif api', 'striping custom mio sporty motif api', 250000.00, 25, NULL, 'Screenshot_32.jpg', '2025-08-10 18:20:34', '2025-08-10 18:20:34'),
(31, 'Velg Pelek Racing RCB CT600 CT-600 PNP Honda Vario 125 Vario 150 Vario 160 CBS Uk185/215-14', 'Deskripsi Produk : \r\n\r\nMerk : 100% original RCB\r\n\r\nModel : CT 600\r\n\r\nBahan CNC Aluminium\r\n\r\nUkuran 185- 215 ring 14\r\n\r\nHarga 1 set depan belakang \r\n\r\nPemakaian untuk motor : Vario 125 Vario 150 Vario 160 cbs\r\n\r\nWarna :  Black, Blue, Merah dan Orange \r\n\r\n\r\n\r\n***Untuk Vario 125/150 lubang cakram depan 5 perlu ganti piringan ke lubang cakram 4\r\n\r\n***Khusus Motor Vario 160 CBS harus tambah boshing', 2380960.00, 2, NULL, 'Screenshot_33.jpg', '2025-08-10 18:30:32', '2025-08-10 18:30:32'),
(32, 'Master Rem RCB S1 &amp; Handle Kiri S1 Racing Boy 14mm CNC Forged', 'Master Rem RCB S1 &amp; Handle Kiri S1 Racing Boy 14mm CNC Forged\r\n\r\nHandle rem set\r\n\r\nOriginal RCB \r\n\r\nWarna silver, merah, hitam Dan gold.\r\n\r\nMohon cantumkan warna yg dipilih pada saat pembelian.\r\n\r\nSudah termasuk swit rem', 1695000.00, 194, NULL, 'Screenshot_34.jpg', '2025-08-10 18:32:29', '2025-08-10 18:32:29'),
(33, 'BRAKET MESIN / PANGKON MESIN TIGER CNC MERK MOS ANODIZE WARNA LENGKAP', 'RAKET MESIN / PANGKON MESIN TIGER CNC MERK MOS ANODIZE WARNA LENGKAP\r\n\r\nSILVER\r\n\r\nHITAM\r\n\r\nHIJAU\r\n\r\nBIRU\r\n\r\nBIRU POSH\r\n\r\nUNGU\r\n\r\nGOLD\r\n\r\nORANYE\r\n\r\nMERAH\r\n\r\nPINK\r\n\r\nMILANTA\r\n\r\n\r\n\r\nWARNA ASLI ANODIZE\r\n\r\nREADY STOCK\r\n\r\nLANGSUNG DI CHECKOUT\r\n\r\nTERIMA KASIH', 205000.00, 244, NULL, 'Screenshot_35.jpg', '2025-08-10 18:33:45', '2025-08-10 18:33:45'),
(34, 'CDI BRT TIGER DC HYPERBAND HIJAU SPECIAL EDITION ORIGINAL BRT', 'Keunggulan Produk :\r\n\r\n- Limitter sudah di setting 11.0000 RPM\r\n\r\n- Memiliki 32 Bit Procesor\r\n\r\n- Power lebih besar\r\n\r\n- Generasi Terbaru\r\n\r\n- BERGARANSI 1 TAHUN', 666000.00, 41, NULL, 'Screenshot_36.jpg', '2025-08-10 18:35:07', '2025-08-10 18:35:07'),
(35, 'Baut L M8 Stainless 304 THE A2-70', 'Baut L M8 stainless 304 THE A2-70\r\n\r\n- Diemeter ulir = 8mm (baut 12an)\r\n\r\n- Panjang Baut 10 - 150mm (pilihan di variasi)\r\n\r\n- Full drat / tidak tergantung stock yang ada (tidak mengikat)\r\n\r\n- Pitch = 1.25\r\n\r\n- Kunci = L6\r\n\r\n- Harga perbiji', 14500.00, 12434, NULL, 'Screenshot_37.jpg', '2025-08-10 18:36:50', '2025-08-10 18:36:50'),
(41, 'TUTUP GEAR CNC ANODIZE PNP CB GL MP TIGER', '', 175000.00, 30, NULL, '6898f01bc5376.jpg', '2025-08-10 19:16:43', '2025-08-10 19:16:43'),
(52, 'Baut L M8 Stainless 304 The A2-70', 'sdad', 1695000.00, 23, 15, '6898f453eed3e.jpg', '2025-08-10 19:34:43', '2025-08-10 19:34:43'),
(53, 'LAMPU DEPAN NINJA SS SET BILED AES TURBO EXPERIENCE v2 60WATT MINILASER LUMINOS', 'as', 1233421.00, 123, 15, '6898f592b173c.jpg', '2025-08-10 19:40:02', '2025-08-10 19:40:02'),
(54, 'Gas Spontan ACERBIS V2 FULL CNC 1 KABEL Jonngkok', '', 250000.00, 12, 14, '6898fa463274c.jpg', '2025-08-10 20:00:06', '2025-08-10 20:00:06'),
(55, 'Oli Motor Matic Honda 0.8L SAE 10W-30', 'oli motor segala matic.0,8L\r\n\r\n disarankan untuk menambahkan bubble warp tambahan.\r\n\r\njenis Honda matic \r\n\r\nSAE 10W-30 \r\n\r\nayo harga paling termurah...', 39000.00, 199, 15, '689904841ddd3.jpg', '2025-08-10 20:43:48', '2025-08-10 20:43:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(2, 'ulin1', '$2y$10$W0eZvCzTniwSPuCGnG4hSe4zqbpslw0XgsD3bo5zOOVAfr.bUxa6y', '2025-08-10 18:46:19', '2025-08-10 18:46:19'),
(3, 'ulin2', '$2y$10$s6OmLC2PUVDDIrj6P1EAn.4eqIOWxIyRCbGDCUdhUyt.DxIDOc49O', '2025-08-10 18:46:43', '2025-08-10 18:46:43'),
(4, 'ulin4', '$2y$10$UowgNFTQB8KGVr0qBN/PN.UQVgYN.eBULzejFt2ZYLsO74.l4zhF.', '2025-08-10 19:06:33', '2025-08-10 19:06:33'),
(5, 'ulin', '$2y$10$LzMNSli9cpSDwUW6U6C5feKANfgJ6MG3NZ0GSd1aVSKriqf//YYe6', '2025-08-10 20:39:05', '2025-08-10 20:39:05'),
(6, 'angga', '$2y$10$detj5Z/Vfjc8D8VMmowRyeyfdCULfnVNdq0tSuSR6T9I5KuAfA5lK', '2025-08-10 20:42:08', '2025-08-10 20:42:08');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_id` (`pesanan_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indeks untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produk_kategori` (`kategori_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
