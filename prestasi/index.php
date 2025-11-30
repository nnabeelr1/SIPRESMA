<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status'])) {
    header("Location: ../index.php");
    exit();
}

$role = $_SESSION['role'];
$nim_saya = "";

// Kalau Mahasiswa, cari NIM-nya dulu
if ($role == 'mahasiswa') {
    $username = $_SESSION['username'];
    $mhs = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT nim FROM mahasiswa JOIN user ON mahasiswa.id_user = user.id_user 
        WHERE user.username='$username'
    "));
    $nim_saya = $mhs['nim'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Prestasi Non-Akademik</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-warning mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="#">🏆 Prestasi Mahasiswa</a>
            <?php if($role == 'mahasiswa') { ?>
                <a href="../dashboard/welcome_mhs.php" class="btn btn-light btn-sm fw-bold">Kembali ke Dashboard</a>
            <?php } else { ?>
                <a href="../dashboard/index.php" class="btn btn-light btn-sm fw-bold">Kembali ke Dashboard</a>
            <?php } ?>
        </div>
    </nav>

    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Daftar Prestasi</h3>
            <?php if($role == 'mahasiswa') { ?>
                <a href="create.php" class="btn btn-primary">➕ Tambah Prestasi</a>
            <?php } ?>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <?php if($role == 'admin') { echo "<th>Mahasiswa</th>"; } ?>
                            <th>Nama Kegiatan</th>
                            <th>Juara</th>
                            <th>Tingkat</th>
                            <th>Tahun</th>
                            <th>Ket / Link</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // LOGIC QUERY: Admin lihat semua, Mahasiswa lihat punya sendiri
                        if ($role == 'admin') {
                            $query = mysqli_query($koneksi, "
                                SELECT p.*, m.nama_lengkap, m.nim 
                                FROM prestasi p
                                JOIN mahasiswa m ON p.nim = m.nim
                                ORDER BY p.id_prestasi DESC
                            ");
                        } else {
                            $query = mysqli_query($koneksi, "
                                SELECT * FROM prestasi 
                                WHERE nim = '$nim_saya'
                                ORDER BY id_prestasi DESC
                            ");
                        }

                        $no = 1;
                        if(mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='8' class='text-center text-muted'>Belum ada data prestasi.</td></tr>";
                        }

                        while($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                
                                <?php if($role == 'admin') { ?>
                                    <td>
                                        <strong><?php echo $row['nama_lengkap']; ?></strong><br>
                                        <small class="text-muted"><?php echo $row['nim']; ?></small>
                                    </td>
                                <?php } ?>

                                <td><?php echo $row['nama_kegiatan']; ?></td>
                                <td><span class="badge bg-success"><?php echo $row['jenis_juara']; ?></span></td>
                                <td><?php echo $row['tingkat']; ?></td>
                                <td><?php echo $row['tahun']; ?></td>
                                <td>
                                    <?php if(!empty($row['keterangan'])) { ?>
                                        <a href="#" class="text-decoration-none" onclick="alert('<?php echo $row['keterangan']; ?>')">📄 Info</a>
                                    <?php } else { echo "-"; } ?>
                                </td>
                                <td>
                                    <a href="delete.php?id=<?php echo $row['id_prestasi']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus prestasi ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>