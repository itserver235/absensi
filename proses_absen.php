<?php
session_start();
include "../service/koneksi.php"; // Pastikan file ini benar-benar ada dan berisi koneksi ke database
//filter user
$only_user = true; // Hanya user biasa yang boleh masuk
include '../service/cek_user.php'; // Pastikan file ini benar-benar ada dan berisi cek session

// Cek apakah user sudah login
if (!isset($_SESSION['nip'])) {
  echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href = '../index.php';</script>";
  exit();
}

// Ambil data dari session
$nip = $_SESSION['nip'];
$jenis_absen = $_POST['jenis_absen']; // 1 = Masuk, 2 = Keluar, 3 = Izin
$lokasi = $_POST['lokasi']; // Lokasi dari JavaScript
$ket = isset($_POST['ket']) ? $_POST['ket'] : ''; // Ambil keterangan jika izin

// Ambil waktu sekarang
date_default_timezone_set('Asia/Jakarta'); // Sesuaikan dengan zona waktu
$thn = date("Y");
$bln = date("m");
$tgl = date("d");
$jam = date("H:i:s");

// Query insert ke tabel absen
$sql = "INSERT INTO absen (nip, jenis_absen, thn, bln, tgl, jam, lokasi, ket)
        VALUES ('$nip', '$jenis_absen', '$thn', '$bln', '$tgl', '$jam', '$lokasi', '$ket')";

// Tentukan pesan alert berdasarkan jenis absen
if ($jenis_absen == 1) {
  $pesan_alert = "Absen Masuk Berhasil!";
} elseif ($jenis_absen == 2) {
  $pesan_alert = "Absen Keluar Berhasil!";
} elseif ($jenis_absen == 3) {
  $pesan_alert = "Izin Berhasil Dikirim!";
} else {
  $pesan_alert = "Proses Absen Gagal!";
}

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('$pesan_alert'); window.location.href = '../user/dashboard.php';</script>";
} else {
  echo "<script>alert('Gagal absen: " . $conn->error . "'); window.location.href = '../user/dashboard.php';</script>";
}

$conn->close();
