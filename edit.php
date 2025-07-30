<?php
session_start();
include '../service/koneksi.php';
//filter user
$only_admin = true; // Hanya admin yang boleh akses halaman ini
include '../service/cek_user.php'; // Pastikan file ini benar-benar ada dan berisi cek session


$id = "";
$nip = "";
$password = "";
$nama = "";
$jabatan = "";
$jenis_user = "";
$created_at = "";

// Cek apakah ada request method GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {

  // Periksa apakah id yang dikirim ada di URL
  if (!isset($_GET['id'])) {
    header("Location: ../admin/data_karyawan.php");
    exit;
  }

  $id = $_GET['id'];

  // Ambil data dari database berdasarkan ID
  $query = "SELECT * FROM user WHERE id=$id";
  $result = $conn->query($query);
  $row = $result->fetch_assoc();

  // Periksa apakah data ditemukan
  if (!$row) {
    header("Location: ../admin/data_karyawan.php");
    exit;
  }

  $nip = $row['nip'];
  $password = $row['password'];
  $nama = $row['nama'];
  $jabatan = $row['jabatan'];
  $jenis_user = $row['jenis_user'];
  $created_at = $row['created_at'];
}
// Jika request adalah POST (proses update data)
else {

  $id = $_POST['id'];
  $nip = $_POST['nip'];
  $password = $_POST['password'];
  $nama = $_POST['nama'];
  $jabatan = $_POST['jabatan'];
  $jenis_user = $_POST['jenis_user'];
  $created_at = date("Y-m-d");

  // Validasi form tidak boleh kosong
  do {
    if (empty($nip) || empty($password) || empty($nama) || empty($jabatan) || empty($jenis_user)) {
      echo "<script>alert('Lengkapi Data!');</script>";
      break;
    }

    // Perintah update ke database
    $sql = "UPDATE user SET nip='$nip', password='$password', nama='$nama', jabatan='$jabatan', jenis_user='$jenis_user', created_at='$created_at' WHERE id=$id";
    $result = $conn->query($sql);

    if (!$result) {
      echo "<script>alert('Query Salah: " . $conn->error . "');</script>";
      break;
    }

    echo "<script>
        alert('Data berhasil diupdate');
        window.location.href = '../admin/data_karyawan.php';
      </script>";
    exit;
  } while (false);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <title>Edit Data Karyawan</title>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
  <header class="bg-primary text-white text-center py-3">
    <h1>Edit Data Karyawan</h1>
  </header>

  <div class="container my-5">
    <h2>Form Edit</h2>
    <form method="POST">
      <input type="hidden" class="form-control" name="id" value="<?= $id ?>">

      <div class="row mb-3">
        <label class="col-sm-3 col-form-label">NIP</label>
        <div class="col-sm-6">
          <input type="text" class="form-control" name="nip" value="<?= $nip ?>" required>
        </div>
      </div>

      <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Password</label>
        <div class="col-sm-6">
          <input type="text" class="form-control" name="password" value="<?= $password ?>" required>
        </div>
      </div>

      <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Nama</label>
        <div class="col-sm-6">
          <input type="text" class="form-control" name="nama" value="<?= $nama ?>" required>
        </div>
      </div>

      <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Jabatan</label>
        <div class="col-sm-6">
          <input type="text" class="form-control" name="jabatan" value="<?= $jabatan ?>" required>
        </div>
      </div>

      <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Jenis User</label>
        <div class="col-sm-6">
          <select class="form-control" name="jenis_user" required>
            <option value="">Pilih Jenis User</option>
            <option value="1" <?= ($jenis_user == 1) ? "selected" : "" ?>>Admin</option>
            <option value="2" <?= ($jenis_user == 2) ? "selected" : "" ?>>Karyawan</option>
			<option value="3" <?= ($jenis_user == 3) ? "selected" : "" ?>>Staff</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="offset-sm-3 col-sm-3 d-grid">
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
        <div class="col-sm-3 d-grid">
          <a class="btn btn-outline-primary" href="../admin/data_karyawan.php" role="button">Cancel</a>
        </div>
      </div>

    </form>
  </div>
</body>

</html>