<?php
session_start();
// Security Check
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    header("Location: ../index.php?pesan=belum_login");
    exit();
}
if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard/welcome_mhs.php");
    exit();
}

include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Program Studi</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">Data Program Studi</h3>
        <a href="create.php" class="btn btn-primary mb-3">+ Tambah Prodi</a>
        <a href="../dashboard/index.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Kode Prodi</th>
                            <th>Nama Prodi</th>
                            <th>Jenjang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM prodi");
                        $no = 1;
                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $data['kode_prodi']; ?></td>
                                <td><?php echo $data['nama_prodi']; ?></td>
                                <td><span class="badge bg-info"><?php echo $data['jenjang']; ?></span></td>
                                <td>
                                    <a href="update.php?id=<?php echo $data['id_prodi']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete.php?id=<?php echo $data['id_prodi']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus prodi ini?')">Hapus</a>
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