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
    <title>Data Dosen</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">Data Dosen Pengajar</h3>
        <a href="create.php" class="btn btn-primary mb-3">+ Tambah Dosen</a>
        <a href="../dashboard/index.php" class="btn btn-secondary mb-3">Kembali</a>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>NIDN</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Homebase Prodi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // JOIN tabel dosen dengan prodi biar muncul nama prodinya
                        $query = mysqli_query($koneksi, "SELECT * FROM dosen 
                                                         JOIN prodi ON dosen.id_prodi = prodi.id_prodi");
                        $no = 1;
                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $data['nidn']; ?></td>
                                <td><?php echo $data['nama_lengkap']; ?></td>
                                <td><?php echo $data['email']; ?></td>
                                <td><?php echo $data['nama_prodi']; ?> (<?php echo $data['jenjang']; ?>)</td>
                                <td>
                                    <a href="update.php?nidn=<?php echo $data['nidn']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete.php?nidn=<?php echo $data['nidn']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus dosen ini?')">Hapus</a>
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