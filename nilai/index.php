<?php
session_start();
include '../config/koneksi.php';

// Cek apakah yang login itu Dosen?
if ($_SESSION['role'] != 'dosen') {
    header("Location: ../index.php");
    exit();
}

// Ambil NIDN Dosen yang sedang login
$id_user = $_SESSION['id_user'];
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nidn, nama_lengkap FROM dosen WHERE id_user='$id_user'"));
$nidn = $dosen['nidn'];

// Ambil Semester Aktif
$smt = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Nilai Mahasiswa</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">📝 Input Nilai</a>
            <a href="../dashboard/welcome_dosen.php" class="btn btn-light btn-sm text-success fw-bold">Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="alert alert-success">
            <strong>Dosen:</strong> <?php echo $dosen['nama_lengkap']; ?> <br>
            <strong>Semester Aktif:</strong> <?php echo $smt ? $smt['nama_semester'] : 'Tidak Ada'; ?>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-success">Pilih Kelas Ajar Anda</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Jadwal</th>
                            <th>Jml Mhs</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Cari kelas yang diajar dosen ini
                        $q_kelas = mysqli_query($koneksi, "
                            SELECT k.*, m.nama_mk, m.sks,
                            (SELECT COUNT(*) FROM krs WHERE id_kelas = k.id_kelas) as jumlah_mhs
                            FROM kelas k
                            JOIN matakuliah m ON k.kode_mk = m.kode_mk
                            WHERE k.nidn = '$nidn'
                        ");

                        $no = 1;
                        if(mysqli_num_rows($q_kelas) == 0) {
                            echo "<tr><td colspan='6' class='text-center'>Anda belum memiliki kelas ajar.</td></tr>";
                        }

                        while ($row = mysqli_fetch_assoc($q_kelas)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo $row['nama_mk']; ?></strong><br>
                                    <small><?php echo $row['sks']; ?> SKS</small>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo $row['nama_kelas']; ?></span></td>
                                <td><?php echo $row['hari'] . ', ' . date('H:i', strtotime($row['jam_mulai'])); ?></td>
                                <td><?php echo $row['jumlah_mhs']; ?> Orang</td>
                                <td>
                                    <a href="create.php?id_kelas=<?php echo $row['id_kelas']; ?>" class="btn btn-success btn-sm">
                                        📝 Input Nilai
                                    </a>
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