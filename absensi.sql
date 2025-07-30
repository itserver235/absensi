-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Mar 2025 pada 15.23
-- Versi server: 10.4.22-MariaDB
-- Versi PHP: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absen`
--

CREATE TABLE `absen` (
  `id_absen` int(10) NOT NULL,
  `nip` int(10) NOT NULL,
  `jenis_absen` varchar(20) NOT NULL,
  `thn` int(4) NOT NULL,
  `bln` int(2) NOT NULL,
  `tgl` int(2) NOT NULL,
  `jam` varchar(100) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `ket` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `absen`
--

INSERT INTO `absen` (`id_absen`, `nip`, `jenis_absen`, `thn`, `bln`, `tgl`, `jam`, `lokasi`, `ket`) VALUES
(50, 10001, '1', 2025, 3, 14, '16:42:52', '-7.5318456,112.6176309', ''),
(51, 10001, '2', 2025, 3, 14, '16:43:22', '-7.5318456,112.6176309', ''),
(52, 10002, '1', 2025, 3, 14, '21:19:05', '-7.5318446,112.6176326', ''),
(53, 10002, '3', 2025, 3, 14, '21:19:24', '-7.5318446,112.6176326', 'Saya pulang cepat karena sakit');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(10) NOT NULL,
  `jenis_user` int(2) NOT NULL,
  `nip` int(10) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `jabatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `jenis_user`, `nip`, `password`, `nama`, `created_at`, `jabatan`) VALUES
(1, 1, 123, '123', 'admin', '2025-03-06', 'admin'),
(16, 2, 10001, '10001', 'Budi', '2025-03-14', 'Staff Gudang'),
(17, 2, 10002, '10002', 'Boby', '2025-03-14', 'Staff');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absen`
--
ALTER TABLE `absen`
  ADD PRIMARY KEY (`id_absen`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absen`
--
ALTER TABLE `absen`
  MODIFY `id_absen` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
