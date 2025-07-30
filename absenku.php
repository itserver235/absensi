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


if (isset($_POST['logout'])) {
  // menghapus semua variabel sesi yang aktif tanpa menghancurkan sesi itu sendiri.
  session_unset();
  //menghapus session
  session_destroy();
  header("Location: ../index.php");
  exit();
}


// Ambil data dari session
$nip = $_SESSION['nip'];
$nama = $_SESSION['nama'];

// Ambil bulan & tahun saat ini sebagai default
date_default_timezone_set('Asia/Jakarta');
$tahunSekarang = date("Y");
$bulanSekarang = date("m");

// Jika pengguna memilih filter tahun & bulan
$selectedYear = isset($_POST['tahun']) ? $_POST['tahun'] : $tahunSekarang;
$selectedMonth = isset($_POST['bulan']) ? $_POST['bulan'] : $bulanSekarang;

// Query untuk mengambil data absensi
$query = "SELECT absen.tgl, absen.jenis_absen, absen.jam, absen.lokasi, absen.ket, user.jabatan 
          FROM absen 
          JOIN user ON absen.nip = user.nip 
          WHERE absen.nip = '$nip' 
          AND absen.thn = '$selectedYear' 
          AND absen.bln = '$selectedMonth' 
          ORDER BY absen.tgl ASC";
$result = $conn->query($query);

// Mapping jenis absen
$jenis_absen_map = [
  "1" => "Masuk",
  "2" => "Keluar",
  "3" => "Izin"
];

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Absen</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js"></script>
  <!-- Tambahkan FontAwesome untuk ikon -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
  <!-- Load Google Maps API -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAOVYRIgupAurZup5y1PRh8Ismb1A3lLao&libraries=places&callback=initMap"></script>

</head>

<body class="d-flex flex-column min-vh-100 bg-light">

  <!-- Header -->
  <header class="bg-primary text-white text-center py-3">
    <h1>Riwayat Absensi</h1>
  </header>

  <!-- Main Content -->
  <main class="container my-4 flex-grow-1">
    <h2 class="text-center">Riwayat Absensi - <?php echo htmlspecialchars($nama); ?></h2>

    <!-- Form Filter -->
    <form method="POST" class="text-center mt-3">
      <div class="row justify-content-center align-items-center">
        <!-- Dropdown Tahun -->
        <div class="col-6 col-md-3 mb-2">
          <select name="tahun" class="form-control">
            <?php
            for ($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--) {
              $selected = ($i == $selectedYear) ? "selected" : "";
              echo "<option value='$i' $selected>$i</option>";
            }
            ?>
          </select>
        </div>

        <!-- Dropdown Bulan -->
        <div class="col-6 col-md-3 mb-2">
          <select name="bulan" class="form-control">
            <?php
            $bulan_arr = [
              "01" => "Januari",
              "02" => "Februari",
              "03" => "Maret",
              "04" => "April",
              "05" => "Mei",
              "06" => "Juni",
              "07" => "Juli",
              "08" => "Agustus",
              "09" => "September",
              "10" => "Oktober",
              "11" => "November",
              "12" => "Desember"
            ];
            foreach ($bulan_arr as $key => $value) {
              $selected = ($key == $selectedMonth) ? "selected" : "";
              echo "<option value='$key' $selected>$value</option>";
            }
            ?>
          </select>
        </div>

        <!-- Tombol Tampilkan -->
        <div class="col-6 col-md-2 mb-2">
          <button type="submit" class="btn btn-primary btn-block">Tampilkan</button>
        </div>

        <!-- Tombol kembali -->
        <div class="col-6 col-md-2 mb-2">
          <a href="../user/dashboard.php" class="btn btn-secondary btn-block">Kembali</a>
        </div>

      </div>
    </form>

    <!-- Tabel Riwayat Absen -->
    <div class="table-responsive mt-3">
      <table class="table table-bordered table-striped">
        <thead class="thead-dark">
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jenis Absen</th>
            <th>Jam</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
            $no = 1;
            while ($row = $result->fetch_assoc()) {
              echo "<tr>
                  <td>{$no}</td>
                  <td>{$row['tgl']}</td>
                  <td>{$jenis_absen_map[$row['jenis_absen']]}</td>
                  <td>{$row['jam']}</td>
                  <td>
                    <button class='btn btn-info btn-sm' data-toggle='modal' 
                      data-target='#modalDetail'
                      data-nama='{$nama}'
                      data-nip='{$nip}'
                      data-jabatan='{$row['jabatan']}'
                      data-jenis='{$jenis_absen_map[$row['jenis_absen']]}'
                      data-tahun='{$selectedYear}'
                      data-bulan='{$bulan_arr[$selectedMonth]}'
                      data-tanggal='{$row['tgl']}'
                      data-jam='{$row['jam']}'
                      data-lokasi='{$row['lokasi']}'
                      data-keterangan='{$row['ket']}'
                      ><i class='fas fa-eye'></i></button>
                  </td>
                </tr>";
              $no++;
            }
          } else {
            echo "<tr><td colspan='5' class='text-center'>Tidak ada data absensi</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>


    <!-- Modal Pop-up untuk Detail Absensi -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Detail Absensi</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <tr>
                <th>Nama</th>
                <td id="modalNama"></td>
              </tr>
              <tr>
                <th>NIP</th>
                <td id="modalNip"></td>
              </tr>
              <tr>
                <th>Jabatan</th>
                <td id="modalJabatan"></td>
              </tr>
              <tr>
                <th>Jenis Absen</th>
                <td id="modalJenis"></td>
              </tr>
              <tr>
                <th>Tahun</th>
                <td id="modalTahun"></td>
              </tr>
              <tr>
                <th>Bulan</th>
                <td id="modalBulan"></td>
              </tr>
              <tr>
                <th>Tanggal</th>
                <td id="modalTanggal"></td>
              </tr>
              <tr>
                <th>Jam</th>
                <td id="modalJam"></td>
              </tr>
              <tr>
                <th>Lokasi</th>
                <td id="modalLokasi"></td>
              </tr>
              <tr>
                <th>peta</th>
                <td>
                  <div id="map" style="height: 200px; width: 100%;"></div>
                </td>
              </tr>
              <tr>
                <th>Keterangan</th>
                <td id="modalKeterangan" class="text-wrap"></td>
              </tr>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>



    <!-- Tambahkan CSS untuk wrap agar tulisan keterangan tidak melebihi pembatas -->
    <style>
      #modalKeterangan {
        word-wrap: break-word;
        white-space: normal;
        max-width: 200px;
        /* Sesuaikan ukuran maksimal */
      }
    </style>


    <script>
      function initMap(lat, lng) {
        var lokasi = {
          lat: parseFloat(lat),
          lng: parseFloat(lng)
        };
        var map = new google.maps.Map(document.getElementById("map"), {
          zoom: 15,
          center: lokasi,
        });
        new google.maps.Marker({
          position: lokasi,
          map: map,
        });
      }

      $(document).ready(function() {
        $("#modalDetail").on("show.bs.modal", function(event) {
          var button = $(event.relatedTarget);

          $("#modalNama").text(button.data("nama"));
          $("#modalNip").text(button.data("nip"));
          $("#modalJabatan").text(button.data("jabatan"));
          $("#modalJenis").text(button.data("jenis"));
          $("#modalTahun").text(button.data("tahun"));
          $("#modalBulan").text(button.data("bulan"));
          $("#modalTanggal").text(button.data("tanggal"));
          $("#modalJam").text(button.data("jam"));
          $("#modalLokasi").text(button.data("lokasi"));
          $("#modalKeterangan").text(button.data("keterangan"));

          // Ambil koordinat lokasi
          var lokasi = button.data("lokasi").split(",");
          var lat = lokasi[0].trim();
          var lng = lokasi[1].trim();

          // Tampilkan Google Maps
          initMap(lat, lng);
        });
      });
    </script>

  </main>

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-3 mt-auto">
    <p>&copy; 2025 Universitas Mercu Buana</p>
  </footer>

</body>

</html>

<?php
$conn->close();
?>