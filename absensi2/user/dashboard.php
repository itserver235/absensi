<?php
session_start();

//cara cek yang ada di session
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

include "../service/koneksi.php"; // Pastikan file ini benar-benar ada dan berisi koneksi ke database

//filter user
$only_user = true; // Hanya user biasa yang boleh masuk
include '../service/cek_user.php'; // Pastikan file ini benar-benar ada dan berisi cek session

// //FILTER USER
// //jika sudah login, maka akan diarahkan ke dashboard
// if (isset($_SESSION['nip']) == true) {
//   // cek apakah login sebagai admin(1) atau user
//   if ($_SESSION['jenis_user'] == '1') {
//     header("Location: admin/dashboard_admin.php");
//   } else {
//     //lanjut program
//   }
//   //jika belum login, maka akan diarahkan ke index
// } else {
//   header("Location: ../index.php");
// }

//jika belum login, maka akan diarahkan ke login
if (isset($_POST['logout'])) {
  // menghapus semua variabel sesi yang aktif tanpa menghancurkan sesi itu sendiri.
  session_unset();
  //menghapus session
  session_destroy();
  header("Location: ../index.php");
  exit();
}

$nip = $_SESSION['nip'];
$thn = date('Y');
$bln = date('m');
$tgl = date('d');

// Cek apakah user sudah absen masuk hari ini
$query = "SELECT * FROM absen WHERE nip = '$nip' AND thn = '$thn' AND bln = '$bln' AND tgl = '$tgl' ORDER BY id_absen DESC LIMIT 1";
$result = $conn->query($query);
$jenis_absen = ($result->num_rows > 0) ? $result->fetch_assoc()['jenis_absen'] : null;

// Logika perubahan tombol
if ($jenis_absen == '1') {
  $jenis_absen_next = '2'; // Jika sudah absen masuk, tombol menjadi Keluar
  $tombol_text = "Keluar";
  $tombol_class = "btn-danger";
} else {
  $jenis_absen_next = '1'; // Jika belum absen atau izin, tombol kembali jadi Masuk
  $tombol_text = "Masuk";
  $tombol_class = "btn-success";
}

?>


<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Karyawan</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <!-- Load Bootstrap JavaScript (jQuery, Popper.js, Bootstrap JS) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js"></script>
  <style>
    .btn-circle {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      font-size: 18px;
    }
  </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

  <!-- Header -->
  <header class="bg-primary text-white text-center py-3">
    <h1>Dashboard Karyawan</h1>
  </header>

  <!-- Main Content -->
  <main class="container text-center my-4 flex-grow-1">
    <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?></h2>

    <!-- Tanggal dan Jam -->
    <div class="container text-center mt-4">
      <h4 id="tanggal" class="fw-bold"></h4>
      <h2 id="jam" class="text-primary fw-bold"></h2>
    </div>

    <!-- Tombol Absen -->
    <form id="absenForm" action="../service/proses_absen.php" method="POST">
      <input type="hidden" name="jenis_absen" id="jenis_absen" value="<?php echo $jenis_absen_next; ?>">
      <input type="hidden" name="lokasi" id="lokasi">
      <button type="button" id="btnAbsen" class="btn btn-circle mt-4 <?php echo $tombol_class; ?>">
        <?php echo $tombol_text; ?>
      </button>
    </form>

    <!-- Container untuk tiga tombol -->
    <div class="container mt-4">
      <div class="row justify-content-center">
        <div class="col-12 col-md-4 mb-2">
          <button class="btn btn-warning btn-block" data-toggle="modal" data-target="#izinModal">Izin</button>
        </div>
        <div class="col-12 col-md-4 mb-2">
          <a href="../user/absenku.php" class="btn btn-info btn-block">Absenku</a>
        </div>
        <div class="col-12 col-md-4">
          <form action="dashboard.php" method="POST">
            <button type="submit" name="logout" class="btn btn-danger btn-block">Logout</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Izin -->
    <div class="modal fade" id="izinModal" tabindex="-1" aria-labelledby="izinModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="izinModalLabel">Form Izin</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="izinForm" action="../service/proses_absen.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="jenis_absen" value="3"> <!-- 3 = Izin -->
              <input type="hidden" name="lokasi" id="lokasiIzin"> <!-- Ubah ID menjadi lokasiIzin -->
              <div class="form-group">
                <label for="ket">Keterangan Izin:</label>
                <textarea name="ket" class="form-control" required placeholder="Masukkan alasan izin..."></textarea>
				<div class="form-group mt-2">
  <label for="izinFoto" class="mb-1">Bukti Foto (opsional):</label>
  <input type="file"
         class="form-control-file"
         id="izinFoto"
         name="foto"
         accept="image/*"
         capture="environment">
  <small class="form-text text-muted">Format JPG/PNG, maksimal 2 MB.</small>
</div>
              </div>
              <button type="button" class="btn btn-primary" onclick="getLocationAndSubmit('izinForm', 'lokasiIzin')">Kirim</button>
            </form>
          </div>
        </div>
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

    // Fungsi untuk mendapatkan lokasi berdasarkan form yang dikirimkan
    function getLocationAndSubmit(formId, lokasiId) {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(position) {
            document.getElementById(lokasiId).value = position.coords.latitude + "," + position.coords.longitude;
            document.getElementById(formId).submit();
          },
          function(error) {
            alert("Gagal mendapatkan lokasi. Silakan coba lagi.");
          }
        );
      } else {
        alert("Geolocation tidak didukung di browser ini.");
      }
    }

    // Event listener tombol absen (Masuk/Keluar)
    document.getElementById("btnAbsen").addEventListener("click", function() {
      getLocationAndSubmit("absenForm", "lokasi");
    });
  </script>

</body>

</html>