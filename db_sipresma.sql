-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Des 2025 pada 06.29
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sipresma`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen`
--

CREATE TABLE `dosen` (
  `nidn` varchar(20) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_prodi` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dosen`
--

INSERT INTO `dosen` (`nidn`, `id_user`, `id_prodi`, `nama_lengkap`, `email`) VALUES
('99001', 15, 1, 'Ahmad Santoso, M.Kom', 'ahmad@univ.ac.id'),
('99002', 16, 2, 'Siti Aminah, M.T.', 'siti@univ.ac.id'),
('99003', 19, 1, 'Eka Putri, M.Cs.', 'eka@univ.ac.id'),
('99004', 20, 1, 'Rudi Hermawan, S.Kom., M.T.', 'rudi@univ.ac.id'),
('99005', 21, 2, 'Fahmi Idris, M.Kom.', 'fahmi@univ.ac.id'),
('99006', 22, 2, 'Linda Kusuma, S.Si., M.MSI.', 'linda@univ.ac.id'),
('99007', 23, 4, 'Budi Laksono, S.T., M.Eng.', 'budi@univ.ac.id'),
('99008', 24, 4, 'Ir. Teguh Prakoso, M.T.', 'teguh@univ.ac.id'),
('99009', 26, 4, 'Rina Wijaya, Ph.D.', 'rina@univ.ac.id'),
('99010', 27, 5, 'Dewi Sartika, M.TI.', 'dewi@univ.ac.id'),
('99011', 28, 5, 'Bayu Pradana, S.Kom., M.Kom.', 'bayu@univ.ac.id'),
('99012', 29, 5, 'Sarah Amalia, M.Cs.', 'sarah@univ.ac.id'),
('99013', 30, 6, 'Candra Utama, M.Stat.', 'candra@univ.ac.id'),
('99014', 31, 6, 'Dian Pertiwi, S.Si., M.Si.', 'dian@univ.ac.id'),
('99015', 32, 6, 'Dr. Bambang Sulistyo, M.Sc.', 'bambang@univ.ac.id');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id_kelas` int(11) NOT NULL,
  `kode_mk` varchar(10) NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `nama_kelas` varchar(5) NOT NULL,
  `kuota` int(11) NOT NULL DEFAULT 40,
  `hari` varchar(10) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id_kelas`, `kode_mk`, `nidn`, `nama_kelas`, `kuota`, `hari`, `jam_mulai`, `jam_selesai`) VALUES
(1, 'IF101', '99001', 'A', 30, 'Senin', '08:00:00', '10:00:00'),
(4, 'IF102', '99003', 'A', 40, 'Selasa', '08:00:00', '11:00:00'),
(5, 'IF103', '99004', 'A', 40, 'Rabu', '09:00:00', '12:00:00'),
(6, 'SI101', '99002', 'A', 35, 'Senin', '07:00:00', '09:00:00'),
(7, 'SI102', '99005', 'A', 25, 'Selasa', '07:30:00', '09:30:00'),
(8, 'SI103', '99006', 'A', 35, 'Rabu', '08:30:00', '10:30:00'),
(9, 'TE101', '99007', 'A', 45, 'Senin', '08:25:00', '10:25:00'),
(10, 'TE102', '99008', 'A', 35, 'Selasa', '08:00:00', '10:00:00'),
(11, 'TE103', '99009', 'A', 25, 'Rabu', '11:25:00', '13:00:00'),
(12, 'TI101', '99010', 'A', 40, 'Senin', '09:25:00', '11:25:00'),
(13, 'TI102', '99011', 'A', 55, 'Selasa', '07:30:00', '09:30:00'),
(14, 'TI103', '99012', 'A', 35, 'Rabu', '10:25:00', '12:25:00'),
(15, 'ST101', '99013', 'A', 30, 'Senin', '07:00:00', '09:00:00'),
(16, 'ST102', '99015', 'A', 45, 'Selasa', '08:30:00', '10:30:00'),
(17, 'ST103', '99014', 'A', 50, 'Rabu', '09:30:00', '11:30:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `krs`
--

CREATE TABLE `krs` (
  `id_krs` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_semester` int(11) NOT NULL,
  `tanggal_krs` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `krs`
--

INSERT INTO `krs` (`id_krs`, `nim`, `id_kelas`, `id_semester`, `tanggal_krs`) VALUES
(9, '21009', 6, 6, '2025-11-30'),
(10, '21009', 7, 6, '2025-11-30'),
(11, '21009', 8, 6, '2025-11-30'),
(16, '21001', 9, 6, '2025-12-01'),
(17, '21001', 11, 6, '2025-12-01'),
(18, '21001', 10, 6, '2025-12-01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nim` varchar(20) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_prodi` int(11) NOT NULL,
  `dosen_wali` varchar(20) DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `angkatan` year(4) NOT NULL,
  `ipk_terakhir` decimal(3,2) DEFAULT 0.00,
  `sks_tempuh` int(11) DEFAULT 0,
  `status_akademik` enum('aktif','cuti','do','lulus') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`nim`, `id_user`, `id_prodi`, `dosen_wali`, `nama_lengkap`, `angkatan`, `ipk_terakhir`, `sks_tempuh`, `status_akademik`) VALUES
('21001', 36, 4, '99009', 'Gilang Pratama', '2021', 3.85, 0, 'aktif'),
('21002', 37, 4, '99008', 'Joko Susilo', '2021', 2.30, 0, 'aktif'),
('21003', 38, 5, '99010', 'Maya Safitri', '2021', 3.90, 0, 'aktif'),
('21004', 39, 5, '99011', 'Haris Firmansyah', '2025', 1.80, 0, 'aktif'),
('21005', 42, 6, '99013', 'Fajar Nugroho', '2021', 3.58, 0, 'aktif'),
('21006', 43, 6, '99014', 'Nadia Putri', '2021', 2.10, 0, 'aktif'),
('21007', 46, 1, '99001', 'Bima Sakti', '2021', 3.90, 0, 'aktif'),
('21008', 47, 1, '99003', 'Ratna Sari', '2021', 1.70, 0, 'aktif'),
('21009', 50, 2, '99002', 'Sinta Bella', '2021', 3.80, 0, 'aktif'),
('21010', 51, 2, '99005', 'Rezki Aditya', '2021', 2.30, 0, 'aktif'),
('23001', 34, 4, '99008', 'Risa Amelia', '2023', 2.40, 0, 'aktif'),
('23002', 33, 4, '99007', 'Dito Wicaksono', '2023', 3.75, 0, 'aktif'),
('23003', 40, 5, '99010', 'Clara Anindita', '2023', 3.87, 0, 'aktif'),
('23004', 41, 5, '99012', 'Doni Kurniawan', '2023', 2.30, 0, 'aktif'),
('23005', 44, 6, '99015', 'Baya Setiawan', '2023', 2.40, 0, 'aktif'),
('23006', 45, 6, '99013', 'Intan Permata', '2023', 4.00, 0, 'aktif'),
('23007', 48, 1, '99001', 'Dimas Anggara', '2023', 3.60, 0, 'aktif'),
('23008', 49, 1, '99004', 'Tegar Iman', '2023', 2.15, 0, 'aktif'),
('23009', 52, 2, '99006', 'Aldo Bagus', '2023', 2.45, 0, 'aktif'),
('23010', 53, 2, '99002', 'Putri Ayu', '2023', 4.00, 0, 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `matakuliah`
--

CREATE TABLE `matakuliah` (
  `kode_mk` varchar(20) NOT NULL,
  `id_prodi` int(11) NOT NULL,
  `nama_mk` varchar(100) NOT NULL,
  `sks` int(11) NOT NULL,
  `semester_paket` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `matakuliah`
--

INSERT INTO `matakuliah` (`kode_mk`, `id_prodi`, `nama_mk`, `sks`, `semester_paket`) VALUES
('IF101', 1, 'Algoritma Pemrograman', 3, 1),
('IF102', 1, 'Matematika Diskrit', 3, 1),
('IF103', 1, 'Pengantar Teknologi Informasi', 3, 1),
('SI101', 2, 'Pengantar Sistem Informasi', 3, 1),
('SI102', 2, 'Konsep Dasar Basis Data', 3, 1),
('SI103', 2, 'Etika Profesi & Komputer', 3, 1),
('ST101', 6, 'Pengantar Statistika', 3, 1),
('ST102', 6, 'Kalkulus I', 3, 1),
('ST103', 6, 'Dasar Pemrograman Statistika', 3, 1),
('TE101', 4, 'Dasar Teknik Elektro', 3, 1),
('TE102', 4, 'Rangkaian Listrik I', 3, 1),
('TE103', 4, 'Fisika Dasar I', 3, 1),
('TI101', 5, 'Jaringan Komputer Dasar', 3, 1),
('TI102', 5, 'Sistem Operasi', 3, 1),
('TI103', 5, 'Manajemen Proyek TI', 3, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai`
--

CREATE TABLE `nilai` (
  `id_nilai` int(11) NOT NULL,
  `id_krs` int(11) NOT NULL,
  `nilai_tugas` decimal(5,2) DEFAULT 0.00,
  `nilai_uts` decimal(5,2) DEFAULT 0.00,
  `nilai_uas` decimal(5,2) DEFAULT 0.00,
  `nilai_akhir` decimal(5,2) DEFAULT 0.00,
  `grade` char(2) DEFAULT 'E'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `nilai`
--

INSERT INTO `nilai` (`id_nilai`, `id_krs`, `nilai_tugas`, `nilai_uts`, `nilai_uas`, `nilai_akhir`, `grade`) VALUES
(4, 9, 80.00, 80.00, 80.00, 80.00, 'AB'),
(5, 11, 90.00, 90.00, 80.00, 86.00, 'A'),
(6, 18, 90.00, 80.00, 85.00, 85.00, 'A'),
(7, 17, 80.00, 80.00, 70.00, 76.00, 'B'),
(8, 16, 85.00, 90.00, 80.00, 84.50, 'AB');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peer_support`
--

CREATE TABLE `peer_support` (
  `id_match` int(11) NOT NULL,
  `mentee_nim` varchar(20) NOT NULL,
  `mentor_nim` varchar(20) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `status` enum('menunggu_dosen','aktif','ditolak') DEFAULT 'menunggu_dosen',
  `catatan_progress` text DEFAULT NULL,
  `acc_doswal_mentee` tinyint(1) DEFAULT 0,
  `acc_doswal_mentor` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peer_support`
--

INSERT INTO `peer_support` (`id_match`, `mentee_nim`, `mentor_nim`, `tanggal_mulai`, `status`, `catatan_progress`, `acc_doswal_mentee`, `acc_doswal_mentor`) VALUES
(1, '21002', '21001', '2025-12-01', 'menunggu_dosen', NULL, 0, 0),
(2, '21004', '23003', '2025-12-01', 'menunggu_dosen', NULL, 0, 0),
(3, '21006', '21005', '2025-12-01', 'menunggu_dosen', NULL, 0, 0),
(4, '21008', '21007', '2025-12-01', 'menunggu_dosen', NULL, 0, 0),
(5, '21010', '21009', '2025-12-01', 'menunggu_dosen', NULL, 0, 0),
(6, '23001', '23002', '2025-12-01', 'menunggu_dosen', NULL, 0, 0),
(7, '23004', '21003', '2025-12-01', 'menunggu_dosen', NULL, 0, 0),
(8, '23005', '21005', '2025-12-01', 'menunggu_dosen', NULL, 0, 0),
(9, '23008', '21007', '2025-12-01', 'menunggu_dosen', NULL, 0, 0),
(10, '23009', '21009', '2025-12-01', 'aktif', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `prestasi`
--

CREATE TABLE `prestasi` (
  `id_prestasi` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `jenis_juara` varchar(50) NOT NULL,
  `tingkat` varchar(50) NOT NULL,
  `tahun` year(4) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `file_bukti` varchar(255) DEFAULT NULL,
  `status_validasi` enum('menunggu','valid','ditolak') DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `prestasi`
--

INSERT INTO `prestasi` (`id_prestasi`, `nim`, `nama_kegiatan`, `jenis_juara`, `tingkat`, `tahun`, `keterangan`, `file_bukti`, `status_validasi`) VALUES
(5, '21001', 'Gemastik 2025', 'Juara 1', 'Nasional', '2025', 'Juara 1 Divisi Penambangan Data Gemastik 2025', '21001_1764560367.pdf', 'valid'),
(7, '21001', 'Gemastik 2024', 'Juara 1', 'Nasional', '2024', 'awdawdawdaw', '21001_1764570610.pdf', 'valid');

-- --------------------------------------------------------

--
-- Struktur dari tabel `prestasi_nonakademik`
--

CREATE TABLE `prestasi_nonakademik` (
  `id_prestasi` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama_kegiatan` varchar(150) NOT NULL,
  `jenis_prestasi` enum('akademik','olahraga','seni','organisasi','lainnya') DEFAULT NULL,
  `tingkat` enum('lokal','regional','nasional','internasional') DEFAULT NULL,
  `tahun` year(4) NOT NULL,
  `poin` int(11) DEFAULT 0,
  `bukti_sertifikat` varchar(255) DEFAULT NULL,
  `validasi_pembina` enum('pending','valid','invalid') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `prodi`
--

CREATE TABLE `prodi` (
  `id_prodi` int(11) NOT NULL,
  `kode_prodi` varchar(10) NOT NULL,
  `nama_prodi` varchar(100) NOT NULL,
  `jenjang` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `prodi`
--

INSERT INTO `prodi` (`id_prodi`, `kode_prodi`, `nama_prodi`, `jenjang`) VALUES
(1, 'IF', 'Informatika', 'S1'),
(2, 'SI', 'Sistem Informasi', 'S1'),
(4, 'TE', 'Teknik Elektro', 'S1'),
(5, 'TI', 'Teknologi Informasi', 'S1'),
(6, 'ST', 'Statistika', 'S1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `semester`
--

CREATE TABLE `semester` (
  `id_semester` int(11) NOT NULL,
  `nama_semester` varchar(20) NOT NULL,
  `status` enum('aktif','non-aktif') DEFAULT 'non-aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `semester`
--

INSERT INTO `semester` (`id_semester`, `nama_semester`, `status`) VALUES
(1, '2023 Ganjil', ''),
(2, '2023 Genap', ''),
(3, '2024 Ganjil', ''),
(4, '2024 Genap', ''),
(5, '2022 Genap', ''),
(6, '2025 Ganjil', 'aktif'),
(8, '2025 Genap', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dosen','mahasiswa') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin123', 'admin', '2025-11-27 09:09:30'),
(8, 'admin2', 'admin123', 'admin', '2025-11-28 00:42:18'),
(15, 'MadSan', 'ahmad123', 'dosen', '2025-11-28 01:13:35'),
(16, 'AmSiti', 'siti123', 'dosen', '2025-11-28 01:13:35'),
(19, 'Ekput', 'ekput123', 'dosen', '2025-11-30 10:12:11'),
(20, 'Rudiher', 'rudi123', 'dosen', '2025-11-30 10:13:01'),
(21, 'Fahmi', 'fahmi123', 'dosen', '2025-11-30 10:13:43'),
(22, 'Linda', 'linda123', 'dosen', '2025-11-30 10:14:14'),
(23, 'Budi', 'budi123', 'dosen', '2025-11-30 10:15:12'),
(24, 'Teguh', 'teguh123', 'dosen', '2025-11-30 10:15:43'),
(26, 'Rina', 'rina123', 'dosen', '2025-11-30 10:17:07'),
(27, 'Dewi', 'dewi123', 'dosen', '2025-11-30 10:17:37'),
(28, 'Bayu', 'bayu123', 'dosen', '2025-11-30 10:18:00'),
(29, 'Sarah', 'sarah123', 'dosen', '2025-11-30 10:18:24'),
(30, 'Candra', 'candra123', 'dosen', '2025-11-30 10:18:58'),
(31, 'Dian', 'dian123', 'dosen', '2025-11-30 10:19:27'),
(32, 'Bambang', 'bambang123', 'dosen', '2025-11-30 10:19:53'),
(33, 'Dito', 'dito123', 'mahasiswa', '2025-11-30 10:50:51'),
(34, 'Risa', 'risa123', 'mahasiswa', '2025-11-30 10:55:36'),
(36, 'Gilang', '123', 'mahasiswa', '2025-11-30 10:58:40'),
(37, 'Joko', 'joko123', 'mahasiswa', '2025-11-30 10:59:54'),
(38, 'Maya', 'maya123', 'mahasiswa', '2025-11-30 11:07:14'),
(39, 'Haris', 'haris123', 'mahasiswa', '2025-11-30 11:08:21'),
(40, 'Clara', 'clara123', 'mahasiswa', '2025-11-30 11:08:59'),
(41, 'Doni', 'doni123', 'mahasiswa', '2025-11-30 11:10:04'),
(42, 'Fajar', 'fajar123', 'mahasiswa', '2025-11-30 11:11:16'),
(43, 'Nadia ', 'nadia123', 'mahasiswa', '2025-11-30 11:12:03'),
(44, 'Baya', 'baya123', 'mahasiswa', '2025-11-30 11:13:20'),
(45, 'Intan', 'intan123', 'mahasiswa', '2025-11-30 11:14:01'),
(46, 'Bima', 'bima123', 'mahasiswa', '2025-11-30 11:15:43'),
(47, 'Ratna', 'ratna123', 'mahasiswa', '2025-11-30 11:16:19'),
(48, 'Dimas', 'dimas123', 'mahasiswa', '2025-11-30 11:17:06'),
(49, 'Tegar', 'tegar123', 'mahasiswa', '2025-11-30 11:17:37'),
(50, 'Sinta', 'sinta123', 'mahasiswa', '2025-11-30 11:18:20'),
(51, 'Rezki', 'rezki123', 'mahasiswa', '2025-11-30 11:19:01'),
(52, 'Aldo', 'aldo123', 'mahasiswa', '2025-11-30 11:19:41'),
(53, 'Putri', 'putri123', 'mahasiswa', '2025-11-30 11:20:17');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`nidn`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_prodi` (`id_prodi`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id_kelas`);

--
-- Indeks untuk tabel `krs`
--
ALTER TABLE `krs`
  ADD PRIMARY KEY (`id_krs`),
  ADD KEY `nim` (`nim`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_semester` (`id_semester`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nim`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_prodi` (`id_prodi`),
  ADD KEY `dosen_wali` (`dosen_wali`);

--
-- Indeks untuk tabel `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD PRIMARY KEY (`kode_mk`),
  ADD KEY `id_prodi` (`id_prodi`);

--
-- Indeks untuk tabel `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id_nilai`),
  ADD KEY `id_krs` (`id_krs`);

--
-- Indeks untuk tabel `peer_support`
--
ALTER TABLE `peer_support`
  ADD PRIMARY KEY (`id_match`),
  ADD KEY `mentee_nim` (`mentee_nim`),
  ADD KEY `mentor_nim` (`mentor_nim`);

--
-- Indeks untuk tabel `prestasi`
--
ALTER TABLE `prestasi`
  ADD PRIMARY KEY (`id_prestasi`),
  ADD KEY `nim` (`nim`);

--
-- Indeks untuk tabel `prestasi_nonakademik`
--
ALTER TABLE `prestasi_nonakademik`
  ADD PRIMARY KEY (`id_prestasi`),
  ADD KEY `nim` (`nim`);

--
-- Indeks untuk tabel `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id_prodi`);

--
-- Indeks untuk tabel `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`id_semester`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `krs`
--
ALTER TABLE `krs`
  MODIFY `id_krs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `peer_support`
--
ALTER TABLE `peer_support`
  MODIFY `id_match` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `prestasi`
--
ALTER TABLE `prestasi`
  MODIFY `id_prestasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `prestasi_nonakademik`
--
ALTER TABLE `prestasi_nonakademik`
  MODIFY `id_prestasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id_prodi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `semester`
--
ALTER TABLE `semester`
  MODIFY `id_semester` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD CONSTRAINT `dosen_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `dosen_ibfk_2` FOREIGN KEY (`id_prodi`) REFERENCES `prodi` (`id_prodi`);

--
-- Ketidakleluasaan untuk tabel `krs`
--
ALTER TABLE `krs`
  ADD CONSTRAINT `krs_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`),
  ADD CONSTRAINT `krs_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `krs_ibfk_3` FOREIGN KEY (`id_semester`) REFERENCES `semester` (`id_semester`);

--
-- Ketidakleluasaan untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `mahasiswa_ibfk_2` FOREIGN KEY (`id_prodi`) REFERENCES `prodi` (`id_prodi`),
  ADD CONSTRAINT `mahasiswa_ibfk_3` FOREIGN KEY (`dosen_wali`) REFERENCES `dosen` (`nidn`);

--
-- Ketidakleluasaan untuk tabel `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD CONSTRAINT `matakuliah_ibfk_1` FOREIGN KEY (`id_prodi`) REFERENCES `prodi` (`id_prodi`);

--
-- Ketidakleluasaan untuk tabel `nilai`
--
ALTER TABLE `nilai`
  ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`id_krs`) REFERENCES `krs` (`id_krs`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `peer_support`
--
ALTER TABLE `peer_support`
  ADD CONSTRAINT `peer_support_ibfk_1` FOREIGN KEY (`mentee_nim`) REFERENCES `mahasiswa` (`nim`),
  ADD CONSTRAINT `peer_support_ibfk_2` FOREIGN KEY (`mentor_nim`) REFERENCES `mahasiswa` (`nim`);

--
-- Ketidakleluasaan untuk tabel `prestasi`
--
ALTER TABLE `prestasi`
  ADD CONSTRAINT `prestasi_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `prestasi_nonakademik`
--
ALTER TABLE `prestasi_nonakademik`
  ADD CONSTRAINT `prestasi_nonakademik_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
