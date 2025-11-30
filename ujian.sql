-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table mku_app.jawaban_mahasiswa
CREATE TABLE IF NOT EXISTS `jawaban_mahasiswa` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_sesi` bigint unsigned NOT NULL,
  `id_soal` bigint unsigned NOT NULL,
  `id_pilihan_dipilih` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jawaban_mahasiswa_id_sesi_id_soal_unique` (`id_sesi`,`id_soal`),
  KEY `jawaban_mahasiswa_id_soal_foreign` (`id_soal`),
  KEY `jawaban_mahasiswa_id_pilihan_dipilih_foreign` (`id_pilihan_dipilih`),
  CONSTRAINT `jawaban_mahasiswa_id_pilihan_dipilih_foreign` FOREIGN KEY (`id_pilihan_dipilih`) REFERENCES `pilihan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jawaban_mahasiswa_id_sesi_foreign` FOREIGN KEY (`id_sesi`) REFERENCES `sesi_ujian` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jawaban_mahasiswa_id_soal_foreign` FOREIGN KEY (`id_soal`) REFERENCES `soal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mku_app.jawaban_mahasiswa: ~0 rows (approximately)

-- Dumping structure for table mku_app.pilihan
CREATE TABLE IF NOT EXISTS `pilihan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_soal` bigint unsigned NOT NULL,
  `teks_pilihan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `huruf_pilihan` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_benar` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pilihan_id_soal_foreign` (`id_soal`),
  CONSTRAINT `pilihan_id_soal_foreign` FOREIGN KEY (`id_soal`) REFERENCES `soal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mku_app.pilihan: ~0 rows (approximately)
INSERT INTO `pilihan` (`id`, `id_soal`, `teks_pilihan`, `huruf_pilihan`, `is_benar`, `created_at`, `updated_at`) VALUES
	(3, 1, '1', 'A', 1, '2025-11-22 08:19:56', '2025-11-22 11:06:48'),
	(4, 1, '2', 'B', 0, '2025-11-22 08:19:56', '2025-11-22 11:06:48'),
	(5, 1, '3', 'C', 0, '2025-11-22 08:19:56', '2025-11-22 08:19:56'),
	(6, 1, '4', 'D', 0, '2025-11-22 08:19:56', '2025-11-22 08:19:56'),
	(7, 1, '5', 'E', 0, '2025-11-22 11:07:29', '2025-11-22 11:07:29'),
	(8, 2, 'a', 'A', 0, '2025-11-22 11:42:52', '2025-11-22 11:42:52'),
	(9, 2, 'b', 'B', 0, '2025-11-22 11:42:52', '2025-11-22 11:42:52'),
	(10, 2, 'c', 'C', 0, '2025-11-22 11:42:52', '2025-11-22 11:42:52'),
	(11, 2, 'd', 'D', 1, '2025-11-22 11:42:52', '2025-11-22 11:42:52'),
	(12, 3, 'z', 'A', 0, '2025-11-22 11:43:07', '2025-11-22 11:43:07'),
	(13, 3, 'x', 'B', 1, '2025-11-22 11:43:07', '2025-11-22 11:43:07'),
	(14, 3, 'c', 'C', 0, '2025-11-22 11:43:07', '2025-11-22 11:43:07'),
	(15, 3, 'v', 'D', 0, '2025-11-22 11:43:07', '2025-11-22 11:43:07'),
	(18, 4, '11', 'A', 0, '2025-11-22 15:02:25', '2025-11-22 15:02:25'),
	(19, 4, '22', 'B', 1, '2025-11-22 15:02:25', '2025-11-22 15:02:25'),
	(54, 20, 'Manajer', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(55, 20, 'Wirausaha', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(56, 20, 'Karyawan', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(57, 20, 'Investor', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(58, 21, 'Mengoperasikan semua jenis mesin', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(59, 21, 'Menyusun rencana usaha (business plan)', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(60, 21, 'Memiliki gelar sarjana teknik', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(61, 21, 'Bekerja di perusahaan multinasional', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(62, 22, 'Mencari modal besar', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(63, 22, 'Merekrut banyak karyawan', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(64, 22, 'Melakukan identifikasi peluang usaha', 'C', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(65, 22, 'Membeli aset dan perlengkapan', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(66, 23, 'Analisis 5W+1H', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(67, 23, 'Analisis Finansial', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(68, 23, 'Analisis SWOT', 'C', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(69, 23, 'Analisis PESTLE', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(70, 24, 'Rencana Produksi', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(71, 24, 'Visi dan Misi', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(72, 24, 'Bauran Pemasaran (Marketing Mix)', 'C', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(73, 24, 'Analisis Pesaing', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(74, 25, 'Perseroan Terbatas (PT)', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(75, 25, 'Persekutuan Komanditer (CV)', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(76, 25, 'Koperasi', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(77, 25, 'Perusahaan Perseorangan', 'D', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(78, 26, 'Untuk memenuhi syarat pinjaman bank', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(79, 26, 'Untuk menilai apakah ide bisnis layak secara teknis, pasar, dan finansial', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(80, 26, 'Untuk merekrut karyawan pertama', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(81, 26, 'Untuk menentukan nama dan logo perusahaan', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(82, 27, 'Biaya Variabel (Variable Cost)', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(83, 27, 'Biaya Tetap (Fixed Cost)', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(84, 27, 'Biaya Total (Total Cost)', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(85, 27, 'Biaya Peluang (Opportunity Cost)', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(86, 28, 'Hak Cipta', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(87, 28, 'Merek Dagang', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(88, 28, 'Paten', 'C', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(89, 28, 'Rahasia Dagang', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(90, 29, 'Laba Maksimum', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(91, 29, 'Titik Impas (Break-Even Point)', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(92, 29, 'Arus Kas Positif', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(93, 29, 'Tingkat Pengembalian Investasi (ROI)', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(94, 30, 'Selalu menunggu instruksi dari atasan', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(95, 30, 'Mampu melihat masalah sebagai peluang untuk menciptakan solusi baru', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(96, 30, 'Menghindari risiko sekecil apapun', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(97, 30, 'Hanya fokus pada keuntungan jangka pendek', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(98, 31, 'Mendapatkan gelar profesi baru', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(99, 31, 'Memastikan bahwa wirausahawan memiliki standar kemampuan yang diakui secara nasional', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(100, 31, 'Menjadi syarat untuk mendapatkan KTP', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(101, 31, 'Membebaskan usaha dari pajak', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(102, 32, 'Distribusi', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(103, 32, 'Pemasaran', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(104, 32, 'Produksi', 'C', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(105, 32, 'Administrasi', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(106, 33, 'Penetapan Harga Berbasis Pesaing (Competitor-Based Pricing)', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(107, 33, 'Penetapan Harga Berbasis Nilai (Value-Based Pricing)', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(108, 33, 'Penetapan Harga Biaya-Plus (Cost-Plus Pricing)', 'C', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(109, 33, 'Penetapan Harga Psikologis (Psychological Pricing)', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(110, 34, 'NPWP (Nomor Pokok Wajib Pajak)', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(111, 34, 'IMB (Izin Mendirikan Bangunan)', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(112, 34, 'NIB (Nomor Induk Berusaha)', 'C', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(113, 34, 'Akta Notaris', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(114, 35, 'Business Model Canvas (BMC)', 'A', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(115, 35, 'Diagram Alir (Flowchart)', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(116, 35, 'Struktur Organisasi', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(117, 35, 'Rencana Anggaran Biaya (RAB)', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(118, 36, 'Membutuhkan biaya yang sangat besar', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(119, 36, 'Berasal dari sumber yang dianggap terpercaya (pelanggan lain)', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(120, 36, 'Mudah dikontrol oleh perusahaan', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(121, 36, 'Hanya bisa dilakukan oleh artis terkenal', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(122, 37, 'Harga jual produk yang paling murah di pasar', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(123, 37, 'Faktor atau keunggulan unik yang membedakan produk Anda dari pesaing', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(124, 37, 'Lokasi penjualan yang strategis', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(125, 37, 'Jumlah karyawan yang dimiliki perusahaan', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(126, 38, 'Laporan Laba Rugi', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(127, 38, 'Laporan Arus Kas', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(128, 38, 'Neraca', 'C', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(129, 38, 'Catatan Atas Laporan Keuangan', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(130, 39, 'Berinteraksi dengan pemasok (supplier) dan pelanggan', 'A', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(131, 39, 'Membuat laporan keuangan', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(132, 39, 'Mengoperasikan mesin produksi', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(133, 39, 'Melakukan pembukuan harian', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(134, 40, 'Mengelola media sosial perusahaan', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(135, 40, 'Meningkatkan visibilitas dan peringkat website di mesin pencari seperti Google', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(136, 40, 'Mendesain kemasan produk', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(137, 40, 'Mengurus perizinan usaha secara online', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(138, 41, 'Pemasaran', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(139, 41, 'Produksi', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(140, 41, 'Manajerial', 'C', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(141, 41, 'Teknis', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(142, 42, 'Kejujuran terhadap pelanggan', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(143, 42, 'Tanggung jawab terhadap kualitas produk', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(144, 42, 'Memberikan informasi yang menyesatkan demi keuntungan', 'C', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(145, 42, 'Membayar upah karyawan sesuai aturan', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(146, 43, 'Modal Investasi', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(147, 43, 'Modal Kerja', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(148, 43, 'Modal Sendiri', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(149, 43, 'Modal Pinjaman', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(150, 44, 'Menjual produk ke semua orang tanpa terkecuali', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(151, 44, 'Membagi pasar yang heterogen menjadi kelompok-kelompok yang lebih kecil dan homogen', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(152, 44, 'Menentukan harga jual tertinggi', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(153, 44, 'Mengalahkan semua pesaing di pasar', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(154, 45, 'Untuk langsung dijual kepada pelanggan pertama', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(155, 45, 'Untuk menguji desain, fungsi, dan mendapatkan umpan balik dari calon pengguna', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(156, 45, 'Untuk mendaftarkan hak paten', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(157, 45, 'Untuk diiklankan di media massa', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(158, 46, 'Konflik internal antar karyawan', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(159, 46, 'Perubahan peraturan pemerintah terkait industri', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(160, 46, 'Kerusakan mesin produksi', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(161, 46, 'Manajemen arus kas yang buruk', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(162, 47, 'Mengukur kinerja dan kemajuan pencapaian tujuan bisnis', 'A', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(163, 47, 'Menghitung total utang perusahaan', 'B', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(164, 47, 'Menentukan gaji direksi', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(165, 47, 'Merekrut karyawan baru', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(166, 48, 'Most Valuable Player', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(167, 48, 'Minimum Viable Product', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(168, 48, 'Maximum Viable Profit', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(169, 48, 'Main Valuable Proposition', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(170, 49, 'Perguruan tinggi luar negeri', 'A', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(171, 49, 'Asosiasi industri dan pakar di bidangnya', 'B', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(172, 49, 'Konsultan politik', 'C', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55'),
	(173, 49, 'Bank Dunia', 'D', 0, '2025-11-22 16:52:55', '2025-11-22 16:52:55');

-- Dumping structure for table mku_app.sesi_ujian
CREATE TABLE IF NOT EXISTS `sesi_ujian` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_ujian` bigint unsigned NOT NULL,
  `nim` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_mulai` timestamp NOT NULL,
  `waktu_selesai` timestamp NULL DEFAULT NULL,
  `skor_akhir` int DEFAULT NULL,
  `status` enum('berlangsung','selesai','timeout') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'berlangsung',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sesi_ujian_id_ujian_foreign` (`id_ujian`),
  KEY `sesi_ujian_nim_foreign` (`nim`),
  CONSTRAINT `sesi_ujian_id_ujian_foreign` FOREIGN KEY (`id_ujian`) REFERENCES `ujian` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sesi_ujian_nim_foreign` FOREIGN KEY (`nim`) REFERENCES `mahasiswas` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mku_app.sesi_ujian: ~0 rows (approximately)

-- Dumping structure for table mku_app.soal
CREATE TABLE IF NOT EXISTS `soal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_ujian` bigint unsigned NOT NULL,
  `teks_soal` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_soal` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipe` enum('pilihan_ganda','essay') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pilihan_ganda',
  `kunci_jawaban` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `soal_id_ujian_foreign` (`id_ujian`),
  CONSTRAINT `soal_id_ujian_foreign` FOREIGN KEY (`id_ujian`) REFERENCES `ujian` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mku_app.soal: ~34 rows (approximately)
INSERT INTO `soal` (`id`, `id_ujian`, `teks_soal`, `nomor_soal`, `created_at`, `updated_at`, `tipe`, `kunci_jawaban`) VALUES
	(1, 1, 'aaaSilakan coba tambah soal baru lagi - sekarang data akan tersimpan ke database dengan benar dan redirect ke halaman index dengan success message', 1, '2025-11-22 08:19:56', '2025-11-22 11:06:48', 'pilihan_ganda', NULL),
	(2, 1, 'bbbb', 2, '2025-11-22 11:42:52', '2025-11-22 11:42:52', 'pilihan_ganda', NULL),
	(3, 1, 'loasfasjhfba', 3, '2025-11-22 11:43:07', '2025-11-22 11:43:07', 'pilihan_ganda', NULL),
	(4, 1, 'asasfas', 4, '2025-11-22 15:02:25', '2025-11-22 15:02:25', 'pilihan_ganda', NULL),
	(20, 2, 'Sikap mental dan jiwa yang selalu aktif, kreatif, berdaya, bercipta, dan bersahaja dalam berusaha adalah definisi dari...', 1, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(21, 2, 'Menurut SKKNI, salah satu unit kompetensi yang harus dimiliki oleh seorang wirausahawan industri adalah kemampuan untuk...', 2, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(22, 2, 'Langkah pertama yang paling krusial dalam memulai sebuah usaha baru adalah...', 3, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(23, 2, 'Analisis yang digunakan untuk mengukur kekuatan (Strengths), kelemahan (Weaknesses), peluang (Opportunities), dan ancaman (Threats) dalam sebuah usaha dikenal dengan istilah...', 4, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(24, 2, 'Dalam menyusun rencana pemasaran, strategi yang mencakup Produk (Product), Harga (Price), Tempat (Place), dan Promosi (Promotion) disebut...', 5, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(25, 2, 'Badan usaha yang modalnya dimiliki oleh satu orang dan segala risiko serta tanggung jawab ditanggung sepenuhnya oleh pemilik tersebut adalah...', 6, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(26, 2, 'Apa tujuan utama dari sebuah studi kelayakan bisnis (feasibility study)?', 7, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(27, 2, 'Biaya yang jumlahnya tetap dan tidak berubah meskipun volume produksi meningkat atau menurun disebut...', 8, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(28, 2, 'Hak eksklusif yang diberikan negara kepada inventor atas hasil invensinya di bidang teknologi disebut...', 9, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(29, 2, 'Titik di mana total pendapatan sama dengan total biaya, sehingga perusahaan tidak mengalami laba maupun rugi, disebut...', 10, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(30, 2, 'Salah satu ciri seorang wirausahawan yang inovatif adalah...', 11, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(31, 2, 'Dalam konteks BNSP, sertifikasi kompetensi bagi wirausaha bertujuan untuk...', 12, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(32, 2, 'Proses mengubah bahan baku menjadi barang jadi yang memiliki nilai tambah disebut...', 13, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(33, 2, 'Strategi menentukan harga jual produk dengan menghitung semua biaya produksi ditambah dengan margin keuntungan yang diinginkan adalah metode...', 14, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(34, 2, 'Izin yang wajib dimiliki oleh setiap usaha yang bergerak di bidang perdagangan di Indonesia adalah...', 15, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(35, 2, 'Model bisnis yang menggambarkan bagaimana sebuah organisasi menciptakan, memberikan, dan menangkap nilai, yang dituangkan dalam 9 blok bangunan disebut...', 16, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(36, 2, 'Kegiatan promosi yang dilakukan dari mulut ke mulut (word of mouth) merupakan salah satu bentuk promosi yang paling efektif karena...', 17, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(37, 2, 'Apa yang dimaksud dengan \'Unique Selling Proposition\' (USP)?', 18, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(38, 2, 'Laporan keuangan yang menunjukkan posisi aset, kewajiban, dan modal perusahaan pada suatu waktu tertentu adalah...', 19, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(39, 2, 'Seorang wirausahawan harus memiliki kemampuan negosiasi yang baik, terutama saat...', 20, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(40, 2, 'Dalam kewirausahaan digital, SEO (Search Engine Optimization) penting untuk...', 21, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(41, 2, 'Kemampuan untuk mengelola sumber daya (manusia, uang, material) secara efektif dan efisien merupakan bagian dari kompetensi...', 22, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(42, 2, 'Etika bisnis yang baik mencakup, KECUALI...', 23, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(43, 2, 'Modal yang digunakan untuk membiayai operasional sehari-hari perusahaan, seperti membeli bahan baku dan membayar gaji, disebut...', 24, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(44, 2, 'Segmentasi pasar adalah proses...', 25, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(45, 2, 'Apa manfaat utama dari membuat prototipe produk sebelum diproduksi massal?', 26, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(46, 2, 'Salah satu risiko eksternal yang mungkin dihadapi oleh seorang wirausahawan adalah...', 27, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(47, 2, 'Key Performance Indicator (KPI) dalam bisnis digunakan untuk...', 28, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(48, 2, 'Dalam konsep \'Lean Startup\', MVP adalah singkatan dari...', 29, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL),
	(49, 2, 'Standar Kompetensi Kerja Nasional Indonesia (SKKNI) bidang kewirausahaan industri dikembangkan oleh pemerintah bersama dengan...', 30, '2025-11-22 16:52:55', '2025-11-22 16:52:55', 'pilihan_ganda', NULL);

-- Dumping structure for table mku_app.ujian
CREATE TABLE IF NOT EXISTS `ujian` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_ujian` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `durasi_menit` int NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mku_app.ujian: ~2 rows (approximately)
INSERT INTO `ujian` (`id`, `nama_ujian`, `durasi_menit`, `deskripsi`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'UTS Tengah semester', 5, NULL, 1, '2025-11-22 07:34:26', '2025-11-22 14:23:00'),
	(2, 'Pretest Kewirausahaan Industri', 60, NULL, 1, '2025-11-22 16:23:35', '2025-11-22 16:23:35');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
