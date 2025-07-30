<?php
session_start();
include '../service/koneksi.php';
//filter user
$only_admin = true; // Hanya admin yang boleh akses halaman ini
include '../service/cek_user.php'; // Pastikan file ini benar-benar ada dan berisi cek session


$nip = "";
$nama = "";
$password = "";
$jabatan = "";
$jenis_user = "";
$created_at = "";

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nip = $_POST['nip'];
  $nama = $_POST['nama'];
  $password = $_POST['password'];
  $jabatan = $_POST['jabatan'];
  $jenis_user = $_POST['jenis_user'];
  $created_at = date("Y-m-d");

  // var_dump($_POST);
  // exit;

  do {
    // Validasi data tidak boleh kosong
    if (empty($nip) || empty($nama) || empty($password) || empty($jabatan) || empty($jenis_user)) {
      echo "<script>alert('Lengkapi Data!');</script>";
      break;
    }

    // Cek apakah NIP sudah ada
    $stmt = $conn->prepare("SELECT * FROM user WHERE nip = ?");
    $stmt->bind_param("i", $nip);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      echo "<script>alert('NIP sudah digunakan!');</script>";
      break;
    }

    // Simpan data ke database tanpa hashing password
    $stmt = $conn->prepare("INSERT INTO user (nip, password, nama, jabatan, jenis_user, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nip,  $nama, $password, $jabatan, $jenis_user, $created_at);
    $result = $stmt->execute();

    if (!$result) {
      echo "<script>alert('Gagal menyimpan data: " . $conn->error . "');</script>";
      break;
    }

    // Reset nilai setelah berhasil
    $nip = "";
    $nama = "";
    $password = "";
	$jabatan = "";
    $jenis_user = "";
    $created_at = "";

    echo "<script>
            alert('Data berhasil dibuat');
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
  <title>Buat Data</title>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
  <header class="bg-primary text-white text-center py-3">
    <h1>Data Karyawan</h1>
  </header>

  <div class="container my-5">
    <h2>Data Baru</h2>
    <form method="POST">
      <div class="row mb-3">
        <label class="col-sm-3 col-form-label">NIP</label>
        <div class="col-sm-6">
          <input type="text" class="form-control" name="nip" value="<?= $nip ?>" required>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Nama</label>
        <div class="col-sm-6">
          <input type="text" class="form-control" name="password" required>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Password</label>
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
            <option value="1">Admin</option>
            <option value="2">Karyawan</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="offset-sm-3 col-sm-3 d-grid">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <div class="col-sm-3 d-grid">
          <a class="btn btn-outline-primary" href="../admin/data_karyawan.php" role="button">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</body>

</html>