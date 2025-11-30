<?php
session_start();
include '../config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge-ipk-danger { background-color: #dc3545; color: white; } /* Merah */
        .badge-ipk-warning { background-color: #ffc107; color: black; } /* Kuning */
        .badge-ipk-success { background-color: #198754; color: white; } /* Hijau */
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">🎓 SIPRESMA Admin</a>
            <div class="d-flex gap-2">
                <a href="../dashboard/index.php" class="btn btn-light btn-sm fw-bold text-primary">Kembali ke Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <h3 class="mb-4">Daftar Mahasiswa</h3>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <a href="create.php" class="btn btn-primary mb-3">+ Tambah Mahasiswa</a>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama Lengkap</th>
                                <th>Prodi</th>
                                <th>Dosen Wali</th>
                                <th>IPK</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // QUERY DENGAN PENGURUTAN (ORDER BY)
                            // Kita gabungkan (JOIN) tabel mahasiswa, prodi, dan dosen biar namanya muncul
                            $query = "SELECT m.*, p.nama_prodi, p.jenjang, d.nama_lengkap as nama_doswal 
                                      FROM mahasiswa m
                                      JOIN prodi p ON m.id_prodi = p.id_prodi
                                      JOIN dosen d ON m.dosen_wali = d.nidn
                                      ORDER BY m.nim ASC"; // <--- INI OBAT PUSINGNYA (Urutkan by NIM)

                            $result = mysqli_query($koneksi, $query);
                            $no = 1;

                            while ($row = mysqli_fetch_assoc($result)) {
                                // Logic Warna IPK
                                $ipk = $row['ipk_terakhir'];
                                $badge_class = 'badge-ipk-success'; // Default Hijau
                                if ($ipk < 2.00) {
                                    $badge_class = 'badge-ipk-danger'; // Merah (Bahaya)
                                } elseif ($ipk < 3.00) {
                                    $badge_class = 'badge-ipk-warning'; // Kuning (Waspada)
                                }
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><strong><?php echo $row['nim']; ?></strong></td>
                                    <td><?php echo $row['nama_lengkap']; ?></td>
                                    <td><?php echo $row['nama_prodi']; ?></td> <td><?php echo $row['nama_doswal']; ?></td> <td>
                                        <span class="badge <?php echo $badge_class; ?> rounded-pill px-3">
                                            <?php echo number_format($ipk, 2); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="update.php?nim=<?php echo $row['nim']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="delete.php?nim=<?php echo $row['nim']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus mahasiswa ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>