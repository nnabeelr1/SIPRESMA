<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kelas & Jadwal</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">Data Kelas & Jadwal Kuliah</h3>
        <a href="create.php" class="btn btn-primary mb-3">+ Buka Kelas Baru</a>
        <a href="../dashboard/index.php" class="btn btn-secondary mb-3">Kembali</a>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Matakuliah</th>
                            <th>Kelas</th>
                            <th>Dosen</th>
                            <th>Jadwal</th>
                            <th>Kuota</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "
                            SELECT k.*, m.nama_mk, m.sks, d.nama_lengkap 
                            FROM kelas k
                            JOIN matakuliah m ON k.kode_mk = m.kode_mk
                            JOIN dosen d ON k.nidn = d.nidn
                            ORDER BY m.nama_mk ASC, k.nama_kelas ASC
                        ");
                        $no = 1;
                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo $data['nama_mk']; ?></strong><br>
                                    <small class="text-muted"><?php echo $data['kode_mk']; ?> (<?php echo $data['sks']; ?> SKS)</small>
                                </td>
                                <td><span class="badge bg-info text-dark"><?php echo $data['nama_kelas']; ?></span></td>
                                <td><?php echo $data['nama_lengkap']; ?></td>
                                <td>
                                    <?php echo $data['hari']; ?><br>
                                    <small><?php echo date('H:i', strtotime($data['jam_mulai'])) . ' - ' . date('H:i', strtotime($data['jam_selesai'])); ?></small>
                                </td>
                                <td><?php echo $data['kuota']; ?></td>
                                <td>
                                    <a href="update.php?id=<?php echo $data['id_kelas']; ?>" class="btn btn-sm btn-warning">Edit</a>
    
                                    <a href="delete.php?id=<?php echo $data['id_kelas']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kelas ini?')">Hapus</a>
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