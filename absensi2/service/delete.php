<?php
session_start();
include '../service/koneksi.php';
//filter user
$only_admin = true; // Hanya admin yang boleh akses halaman ini
include '../service/cek_user.php'; // Pastikan file ini benar-benar ada dan berisi cek session


// adalah perintah untuk menghapus data
// jika ada data yang dikirim dari form hapus
// maka akan dijalankan perintah berikut  
if (isset($_GET["id"])) {
  $id = intval($_GET["id"]); // Pastikan ID berupa angka untuk keamanan

  // Ambil NIP berdasarkan ID
  $result = $conn->query("SELECT nip FROM user WHERE id = $id");
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nip = $row["nip"];

    // Hapus data di tabel absen
    $conn->query("DELETE FROM absen WHERE nip = '$nip'");

    // Hapus data di tabel user
    $conn->query("DELETE FROM user WHERE id = $id");
  }
}

header("location: ../admin/data_karyawan.php");
exit;
