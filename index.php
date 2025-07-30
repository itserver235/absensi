<?php
// Menyambungkan koneksi ke database
include 'service/koneksi.php';
session_start();

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['nip'])) {
  if ($_SESSION['jenis_user'] == 1) {
    header("Location: admin/dashboard_admin.php");
  } else {
    header("Location: user/dashboard.php");
  }
  exit();
}

// Jika form login disubmit
if (isset($_POST['login'])) {
  $nip = $_POST['nip'];
  $password = $_POST['password'];

  // Gunakan prepared statement untuk keamanan
  $stmt = $conn->prepare("SELECT * FROM user WHERE nip = ?"); // Query untuk mencari user berdasarkan NIP memakai prepared statement
  $stmt->bind_param("i", $nip); // i untuk tipe data integer, kalau string pakai s
  $stmt->execute(); // Jalankan query
  $result = $stmt->get_result(); // Ambil hasil query

  // Jika NIP ditemukan
  if ($result->num_rows > 0) { // Jika NIP ditemukan
    $row = $result->fetch_assoc(); // Ambil data dari hasil query

    // Periksa password
    if ($password === $row['password']) { // Ubah jika pakai password_hash() // === itu cek nilai dan type data yang sama
      $_SESSION['nip'] = $row['nip'];
      $_SESSION['nama'] = $row['nama'];
      $_SESSION['jenis_user'] = $row['jenis_user'];

      // Arahkan sesuai jenis_user
      if ($row['jenis_user'] == 1) {
        header("Location: admin/dashboard_admin.php");
      } else {
        header("Location: user/dashboard.php");
      }
      exit();
    } else {
      echo "<script>alert('Password salah!');</script>";
    }
  } else {
    echo "<script>alert('NIP tidak ditemukan!');</script>";
  }

  $stmt->close();
  $conn->close();
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">

  <!-- Header -->
  <header class="bg-primary text-white text-center py-3">
    <h1>Aplikasi Absensi Karyawan Universitas Mercu Buana</h1>
  </header>

  <!-- Main Content -->
  <main class="flex-grow-1 d-flex justify-content-center align-items-center">
    <div class="card p-4 shadow-lg" style="width: 350px;">
      <h3 class="text-center">Login</h3>
      <form action="index.php" method="POST">
        <div class="mb-3">
          <label for="nip" class="form-label">NIP</label>
          <input type="text" id="nip" name="nip" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-3 mt-4">
    &copy; 2025 Universitas Mercu Buana
  </footer>

</body>

</html>