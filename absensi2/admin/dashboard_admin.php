<?php
//  Path: dashboard.php
session_start();

include "../service/koneksi.php"; // Pastikan koneksi sudah dibuat

//filter user
$only_admin = true; // Hanya admin yang boleh akses halaman ini
include '../service/cek_user.php'; // Pastikan file ini benar-benar ada dan berisi cek session

// //FILTER USER
// //jika sudah login, maka akan diarahkan ke dashboard
// if (isset($_SESSION['nip']) == true) {
//   // cek apakah login sebagai admin(1) atau user
//   if ($_SESSION['jenis_user'] == '1') {
//     // jika ya maka lanjutkan program
//   } else {
//     header("Location: user/dashboard.php");
//   }
//   //jika belum login, maka akan diarahkan ke index
// } else {
//   header("Location: ../index.php");
// }

//cek apakah ada data yang dikirim melalui post logout, jika iya jlakukan logout
if (isset($_POST['logout'])) {
  // menghapus semua variabel sesi yang aktif tanpa menghancurkan sesi itu sendiri.
  session_unset();
  //menghapus session
  session_destroy();
  header("Location: ../index.php");
  exit();
}

include "../service/koneksi.php"; // Pastikan koneksi sudah dibuat

// Ambil tanggal hari ini
$todayYear = date("Y");
$todayMonth = date("m");
$todayDate = date("d");

// Query untuk menghitung jumblah karyawan, absen masuk, keluar, dan izin
$query = "
    SELECT 
        (SELECT COUNT(*) FROM user WHERE nip != '123') AS total_karyawan,
        SUM(CASE WHEN jenis_absen = '1' THEN 1 ELSE 0 END) AS total_masuk,
        SUM(CASE WHEN jenis_absen = '2' THEN 1 ELSE 0 END) AS total_keluar,
        SUM(CASE WHEN jenis_absen = '3' THEN 1 ELSE 0 END) AS total_izin
    FROM absen
    WHERE thn = '$todayYear' AND bln = '$todayMonth' AND tgl = '$todayDate'
";

$result = $conn->query($query);
$row = $result->fetch_assoc();

$total_karyawan = $row['total_karyawan'] ?? 0;
$total_absen_masuk = $row['total_masuk'] ?? 0;
$total_absen_keluar = $row['total_keluar'] ?? 0;
$total_absen_izin = $row['total_izin'] ?? 0;



?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap JS
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  -->
</head>

<body class="bg-light d-flex flex-column min-vh-100">
  <!-- Header -->
  <header class="bg-primary text-white text-center py-3">
    <h1>Dashboard Admin</h1>
  </header>

  <!-- Main Content -->
  <main class="container my-2 flex-grow-1">
    <!-- Tanggal dan Jam -->
    <div class="container text-center my-3">
      <h4 id="tanggal" class="fw-bold"></h4>
      <h2 id="jam" class="text-primary fw-bold"></h2>
    </div>

    <div class="row row-cols-2 row-cols-md-2 g-4">
      <!-- Karyawan Masuk -->
      <div class="col">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Absen Masuk</h5>
            <p class="card-text display-4"><?php echo $total_absen_masuk; ?></p> <!-- Absen Masuk -->
          </div>
        </div>
      </div>
      <!-- Karyawan Keluar -->
      <div class="col">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Absen Keluar</h5>
            <p class="card-text display-4"><?php echo $total_absen_keluar; ?></p> <!-- Absen Keluar -->
          </div>
        </div>
      </div>
      <!-- Karyawan Izin -->
      <div class="col">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Absen Izin</h5>
            <p class="card-text display-4"><?php echo $total_absen_izin; ?></p> <!-- Absen Izin -->
          </div>
        </div>
      </div>
      <!-- Total Karyawan -->
      <div class="col">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Total Karyawan</h5>
            <p class="card-text display-4"><?php echo $total_karyawan; ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Buttons at Bottom Inside Main -->
    <div class="row mt-4">
      <div class="col-md-4">
        <a href="../admin/data_karyawan.php" class="btn btn-primary w-100 mb-2">Data Karyawan</a>
      </div>
      <div class="col-md-4">
        <a href="../admin/riwayat_absens.php" class="btn btn-primary w-100 mb-2">Data Absensi</a>
      </div>
      <div class="col-md-4">
        <form action="dashboard_admin.php" method="POST">
          <button type="submit" class="btn btn-danger w-100 mb-2" name="logout">Logout</button>
        </form>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-3 mt-auto">
    <p>&copy; 2025 Universitas Mercu Buana</p>
  </footer>


  <!-- JS untuk menampilkan tanggal dan waktu -->
  <script>
    function updateDateTime() {
      const now = new Date();

      // Format tanggal: Senin, 08 Maret 2025
      const options = {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      };
      const tanggal = now.toLocaleDateString('id-ID', options);

      // Format jam: 14:35:50
      const jam = now.toLocaleTimeString('id-ID');

      // Menampilkan di halaman
      document.getElementById("tanggal").textContent = tanggal;
      document.getElementById("jam").textContent = jam;
    }

    // Jalankan pertama kali
    updateDateTime();

    // Perbarui setiap detik
    setInterval(updateDateTime, 1000);
  </script>

</body>

</html>