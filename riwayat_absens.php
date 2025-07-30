<?php
//  Path: dashboard.php
session_start();

//cara cek yang ada di session
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

include "../service/koneksi.php";

//filter user
$only_admin = true; // Hanya admin yang boleh akses halaman ini
include '../service/cek_user.php'; // Pastikan file ini benar-benar ada dan berisi cek session



// Ambil bulan & tahun saat ini sebagai default
date_default_timezone_set('Asia/Jakarta');
$tahunSekarang = date("Y");
$bulanSekarang = date("m");
$tanggalSekarang = date("d");

// Ambil data untuk dropdown filter
$query_users = "SELECT nip, nama FROM user ORDER BY nama ASC";
$result_users = $conn->query($query_users);

// Ambil filter jika dipilih
$selected_nip = isset($_POST['nip']) ? $_POST['nip'] : '';
$selected_jenis = isset($_POST['jenis_absen']) ? $_POST['jenis_absen'] : '';
$selected_tahun = isset($_POST['tahun']) ? $_POST['tahun'] : $tahunSekarang;
$selected_bulan = isset($_POST['bulan']) ? $_POST['bulan'] : $bulanSekarang;
$selected_tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : $tanggalSekarang;

// Query ambil data absensi dengan join ke tabel user
$query = "SELECT absen.nip, user.nama, user.jabatan, absen.jenis_absen, absen.thn, absen.bln, absen.tgl, absen.jam, absen.lokasi, absen.ket 
          FROM absen 
          JOIN user ON absen.nip = user.nip";


// Tambah filter jika dipilih
$conditions = [];
if (!empty($selected_nip)) {
  $conditions[] = "absen.nip = '$selected_nip'";
}
if (!empty($selected_jenis)) {
  $conditions[] = "absen.jenis_absen = '$selected_jenis'";
}
if (!empty($selected_tahun)) {
  $conditions[] = "absen.thn = '$selected_tahun'";
}
if (!empty($selected_bulan)) {
  $conditions[] = "absen.bln = '$selected_bulan'";
}
if (!empty($selected_tanggal)) {
  $conditions[] = "absen.tgl = '$selected_tanggal'";
}
if (!empty($conditions)) {
  $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY absen.tgl ASC";
$result = $conn->query($query);

// Mapping jenis absen
$jenis_absen_map = [
  "1" => "Masuk",
  "2" => "Keluar",
  "3" => "Izin"
];

// Ambil data tahun unik dari database
$query_tahun = "SELECT DISTINCT thn FROM absen ORDER BY thn DESC";
$result_tahun = $conn->query($query_tahun);

// Bulan dalam format angka & nama
$bulan_map = [
  "1" => "Januari",
  "2" => "Februari",
  "3" => "Maret",
  "4" => "April",
  "5" => "Mei",
  "6" => "Juni",
  "7" => "Juli",
  "8" => "Agustus",
  "9" => "September",
  "10" => "Oktober",
  "11" => "November",
  "12" => "Desember"
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Absensi Karyawan</title>
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
    <h1>Data Absensi Karyawan</h1>
  </header>

  <!-- Main Content -->
  <main class="container my-4 flex-grow-1">
    <h2 class="text-center">Riwayat Absensi</h2>

    <!-- Form Filter -->
    <form method="POST" class="text-center mt-3">
      <div class="row justify-content-center">
        <!-- Dropdown NIP -->
        <div class="col-md-2 mb-2">
          <select name="nip" class="form-control">
            <option value="">-- Semua Karyawan --</option>
            <?php while ($row = $result_users->fetch_assoc()) {
              $selected = ($row['nip'] == $selected_nip) ? "selected" : "";
              echo "<option value='{$row['nip']}' $selected>{$row['nama']} ({$row['nip']})</option>";
            } ?>
          </select>
        </div>

        <!-- Dropdown Jenis Absen -->
        <div class="col-md-2 mb-2">
          <select name="jenis_absen" class="form-control">
            <option value="">-- Semua Jenis Absen --</option>
            <?php foreach ($jenis_absen_map as $key => $value) {
              $selected = ($key == $selected_jenis) ? "selected" : "";
              echo "<option value='$key' $selected>$value</option>";
            } ?>
          </select>
        </div>

        <!-- Dropdown Tahun -->
        <div class="col-md-2 mb-2">
          <select name="tahun" class="form-control">
            <option value="">-- Semua Tahun --</option>
            <?php while ($row = $result_tahun->fetch_assoc()) {
              $selected = ($row['thn'] == $selected_tahun) ? "selected" : "";
              echo "<option value='{$row['thn']}' $selected>{$row['thn']}</option>";
            } ?>
          </select>
        </div>

        <!-- Dropdown Bulan -->
        <div class="col-md-1 mb-2">
          <select name="bulan" class="form-control">
            <option value="">-- Semua Bulan --</option>
            <?php foreach ($bulan_map as $key => $value) {
              $selected = ($key == $selected_bulan) ? "selected" : "";
              echo "<option value='$key' $selected>$value</option>";
            } ?>
          </select>
        </div>

        <!-- Dropdown Tanggal -->
        <div class="col-md-1 mb-2">
          <select name="tanggal" class="form-control">
            <option value="">-- Semua Tanggal --</option>
            <?php for ($i = 1; $i <= 31; $i++) {
              $selected = ($i == $selected_tanggal) ? "selected" : "";
              echo "<option value='$i' $selected>$i</option>";
            } ?>
          </select>
        </div>

        <!-- Tombol Filter -->
        <div class="col-6 col-md-2 mb-2">
          <button type="submit" class="btn btn-primary btn-block">Tampilkan</button>
        </div>
        <!-- Tombol Kembali -->
        <div class="col-6 col-md-2 mb-2">
          <a href="../admin/dashboard_admin.php" class="btn btn-secondary btn-block">Kembali</a>
        </div>
      </div>
    </form>

    <!-- Tabel Riwayat Absen -->
    <div class="table-responsive mt-3">
      <table class="table table-bordered table-striped">
        <thead class="thead-dark">
          <tr>
            <th>No</th>
            <th>NIP</th>
            <th>Nama</th>
            <th>Jenis Absen</th>
            <th>Jam</th>
            <th>Aksi</th> <!-- Kolom baru -->
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
            $no = 1;
            while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$no}</td>
                      <td>{$row['nip']}</td>
                      <td>{$row['nama']}</td>
                      <td>{$jenis_absen_map[$row['jenis_absen']]}</td>
                      <td>{$row['jam']}</td>
                      <td>
                        <button class='btn btn-info btn-sm' data-toggle='modal' 
                          data-target='#modalDetail'
                          data-nama='{$row['nama']}'
                          data-nip='{$row['nip']}'
                          data-jabatan='{$row['jabatan']}'
                          data-jenis='{$jenis_absen_map[$row['jenis_absen']]}'
                          data-tahun='{$row['thn']}'
                          data-bulan='{$bulan_map[$row['bln']]}'
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
            echo "<tr><td colspan='6' class='text-center'>Tidak ada data absensi</td></tr>";
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
                <th>Peta</th>
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


    <!-- Script untuk Mengisi Modal dengan Data dari Tombol -->
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
        $('#modalDetail').on('show.bs.modal', function(event) {
          var button = $(event.relatedTarget); // Tombol yang diklik
          $('#modalNama').text(button.data('nama'));
          $('#modalNip').text(button.data('nip'));
          $('#modalJabatan').text(button.data('jabatan'));
          $('#modalJenis').text(button.data('jenis'));
          $('#modalTahun').text(button.data('tahun'));
          $('#modalBulan').text(button.data('bulan'));
          $('#modalTanggal').text(button.data('tanggal'));
          $('#modalJam').text(button.data('jam'));
          $('#modalLokasi').text(button.data('lokasi'));
          $('#modalKeterangan').text(button.data('keterangan'));


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