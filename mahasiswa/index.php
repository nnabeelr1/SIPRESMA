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
    <title>Data Mahasiswa</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">Daftar Mahasiswa</h3>
        <a href="create.php" class="btn btn-primary mb-3">+ Tambah Mahasiswa</a>
        <a href="../dashboard/index.php" class="btn btn-secondary mb-3">Kembali</a>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Lengkap</th>
                            <th>Prodi</th> <th>Dosen Wali</th> <th>IPK</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // QUERY JOIN 3 TABEL (Mahasiswa + Prodi + Dosen)
                        $query = mysqli_query($koneksi, "
                            SELECT m.*, p.nama_prodi, d.nama_lengkap AS nama_dosen
                            FROM mahasiswa m
                            LEFT JOIN prodi p ON m.id_prodi = p.id_prodi
                            LEFT JOIN dosen d ON m.dosen_wali = d.nidn
                            ORDER BY m.angkatan DESC
                        ");
                        
                        $no = 1;
                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $data['nim']; ?></td>
                                <td><?php echo $data['nama_lengkap']; ?></td>
                                
                                <td><?php echo $data['nama_prodi']; ?></td>
                                
                                <td><?php echo $data['nama_dosen']; ?></td>

                                <td>
                                    <?php 
                                    $bg = ($data['ipk_terakhir'] < 2.50) ? 'bg-danger' : 'bg-success';
                                    echo "<span class='badge $bg'>{$data['ipk_terakhir']}</span>";
                                    ?>
                                </td>
                                <td>
                                    <a href="update.php?nim=<?php echo $data['nim']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete.php?nim=<?php echo $data['nim']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
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