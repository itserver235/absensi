<?php
//  Path: dashboard.php
session_start();

//filter user
$only_admin = true; // Hanya admin yang boleh akses halaman ini
include '../service/cek_user.php'; // Pastikan file ini benar-benar ada dan berisi cek session


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
  <title>Data Karyawan</title>
</head>

<body class="bg-light d-flex flex-column min-vh-100">

  <!-- Header -->
  <header class="bg-primary text-white text-center py-3">
    <h1>Data Karyawan</h1>
  </header>
  <!-- Header -->

  <!-- Main Content -->
  <main class="container my-5 flex-grow-1">
    <div class="row">
      <div class="col">
        <a href="../admin/dashboard_admin.php" class="btn btn-secondary" ;>Kembali</a>
        <a href="../service/created.php" class="btn btn-primary">Tambah Karyawan</a>
      </div>
    </div>
    <div class="row mt-3">
      <div class="col">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">NIP</th>
              <th scope="col">Nama</th>
              <th scope="col">Jabatan</th>
              <th scope="col">Dibuat</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>

            <?php
            include "../service/koneksi.php";

            //baca tabel database
            $query = "SELECT * FROM user";
            $result = $conn->query($query);

            //untuk memeriksa apakah query diatas di eksekusi dengan benar
            if (!$result) {
              die("Invalid query: " . $conn->error);
            }

            //membaca data (looping per baris)
            while ($row = $result->fetch_assoc()) {
              echo "
              <tr>
                <td>$row[id]</td>
                <td>$row[nip]</td>
                <td>$row[nama]</td>
				<td>$row[jabatan]</td>
                <td>$row[created_at]</td>
                <td>
                  <a class='btn btn-primary btn-sm' href='../service/edit.php?id=$row[id]'>Edit</a>
                  <a class='btn btn-danger btn-sm' href='#' onclick='confirmDelete($row[id])'>Delete</a>
                </td>
              </tr>
            ";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
  <!-- Main Content -->

  <!-- JS delete -->
  <script>
    function confirmDelete(id) {
      if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        window.location.href = '../service/delete.php?id=' + id;
      }
    }
  </script>

</body>

</html>