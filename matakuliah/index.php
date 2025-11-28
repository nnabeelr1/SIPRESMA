<?php
session_start();
// Security Check
if (!isset($_SESSION['status'])) { header("Location: ../index.php"); exit(); }
if ($_SESSION['role'] != 'admin') { header("Location: ../dashboard/welcome_mhs.php"); exit(); }

include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mata Kuliah</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">Data Mata Kuliah</h3>
        <a href="create.php" class="btn btn-primary mb-3">+ Tambah Matkul</a>
        <a href="../dashboard/index.php" class="btn btn-secondary mb-3">Kembali</a>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Kode MK</th>
                            <th>Nama Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Smt Paket</th>
                            <th>Prodi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Join ke Prodi
                        $query = mysqli_query($koneksi, "SELECT * FROM matakuliah 
                                                         JOIN prodi ON matakuliah.id_prodi = prodi.id_prodi
                                                         ORDER BY semester_paket ASC");
                        $no = 1;
                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $data['kode_mk']; ?></td>
                                <td><?php echo $data['nama_mk']; ?></td>
                                <td><?php echo $data['sks']; ?></td>
                                <td><?php echo $data['semester_paket']; ?></td>
                                <td><?php echo $data['nama_prodi']; ?></td>
                                <td>
                                    <a href="update.php?kode=<?php echo $data['kode_mk']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete.php?kode=<?php echo $data['kode_mk']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus MK ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>