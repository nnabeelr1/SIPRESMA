<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">Manajemen Pengguna (User)</h3>
        
        <div class="alert alert-info small">
            <strong>Info:</strong> Halaman ini untuk mengelola akun login. <br>
            Untuk menambah Mahasiswa/Dosen, sebaiknya lewat menu <strong>Mahasiswa</strong> atau <strong>Dosen</strong> agar datanya lengkap.
            Di sini khusus untuk menambah <strong>Admin Baru</strong> atau <strong>Reset Password</strong>.
        </div>

        <a href="create.php" class="btn btn-primary mb-3">+ Tambah Admin Baru</a>
        <a href="../dashboard/index.php" class="btn btn-secondary mb-3">Kembali</a>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Role (Peran)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM user ORDER BY id_user DESC");
                        $no = 1;
                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $data['username']; ?></td>
                                <td>
                                    <?php 
                                    if($data['role']=='admin') echo '<span class="badge bg-danger">Admin</span>';
                                    elseif($data['role']=='dosen') echo '<span class="badge bg-success">Dosen</span>';
                                    else echo '<span class="badge bg-info">Mahasiswa</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="update.php?id=<?php echo $data['id_user']; ?>" class="btn btn-sm btn-warning">Edit / Reset Pass</a>
                                    
                                    <?php if($data['username'] != $_SESSION['username']) { ?>
                                        <a href="delete.php?id=<?php echo $data['id_user']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')">Hapus</a>
                                    <?php } ?>
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