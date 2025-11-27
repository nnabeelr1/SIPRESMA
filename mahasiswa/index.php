<?php
session_start();

// Cek 1: Apakah user sudah login?
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    header("Location: ../index.php?pesan=belum_login");
    exit();
}

// Cek 2: Apakah user adalah ADMIN?
if ($_SESSION['role'] != 'admin') {
    // Kalau bukan admin (misal mahasiswa), tendang ke halaman mahasiswa
    header("Location: ../dashboard/welcome_mhs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa - SIPRESMA</title>
    
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h2 class="mb-4">Daftar Mahasiswa</h2>
        
        <a href="create.php" class="btn btn-primary mb-3">+ Tambah Mahasiswa</a>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Lengkap</th>
                            <th>Angkatan</th>
                            <th>IPK</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 1. Panggil koneksi database (Mundur satu folder ke config)
                        include '../config/koneksi.php';

                        // 2. Query data
                        $query = mysqli_query($koneksi, "SELECT * FROM mahasiswa");
                        $no = 1;

                        // 3. Looping data
                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $data['nim']; ?></td>
                                <td><?php echo $data['nama_lengkap']; ?></td>
                                <td><?php echo $data['angkatan']; ?></td>
                                <td>
                                    <?php 
                                    if ($data['ipk_terakhir'] < 2.50) {
                                        echo '<span class="badge bg-danger">'.$data['ipk_terakhir'].'</span>';
                                    } else {
                                        echo '<span class="badge bg-success">'.$data['ipk_terakhir'].'</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo $data['status_akademik']; ?></td>
                                <td>
                                    <a href="update.php?nim=<?php echo $data['nim']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    
                                    <a href="delete.php?nim=<?php echo $data['nim']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin mau hapus data ini?');">Hapus</a>
                                </td>
                            </tr>
                        <?php
                        } // Akhir looping
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>