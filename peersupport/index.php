<?php
session_start();
include '../config/koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Peer Support</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-purple mb-4" style="background-color: #6f42c1;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">🤝 Peer Support Data</a>
            <a href="../dashboard/index.php" class="btn btn-light btn-sm text-purple fw-bold">Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Daftar Pasangan Mentoring</h3>
            <a href="reset.php" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus SEMUA pasangan?')">⚠️ Reset Semua Data</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Mentee (Mahasiswa Berisiko)</th>
                            <th>Mentor (Mahasiswa Berprestasi)</th>
                            <th>Prodi</th>
                            <th>Status ACC Dosen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // QUERY CANGGIH: Join ke Mahasiswa 2x (satu buat Mentee, satu buat Mentor)
                        $query = mysqli_query($koneksi, "
                            SELECT ps.*, 
                                   m1.nama_lengkap as nama_mentee, m1.nim as nim_mentee, m1.ipk_terakhir as ipk_mentee,
                                   m2.nama_lengkap as nama_mentor, m2.nim as nim_mentor, m2.ipk_terakhir as ipk_mentor,
                                   p.nama_prodi
                            FROM peer_support ps
                            JOIN mahasiswa m1 ON ps.mentee_nim = m1.nim
                            JOIN mahasiswa m2 ON ps.mentor_nim = m2.nim
                            JOIN prodi p ON m1.id_prodi = p.id_prodi
                            ORDER BY ps.id_match DESC
                        ");

                        $no = 1;
                        if(mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='6' class='text-center py-5 text-muted'>Belum ada pasangan terbentuk. Silakan jalankan 'Auto Matchmaking' di Dashboard.</td></tr>";
                        }

                        while($row = mysqli_fetch_assoc($query)) {
                            // Cek Status
                            $status_badge = ($row['status'] == 'aktif') ? 'bg-success' : 'bg-warning text-dark';
                            $status_text  = ($row['status'] == 'aktif') ? 'AKTIF (Disetujui)' : 'Menunggu Dosen';
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                
                                <td>
                                    <strong><?php echo $row['nama_mentee']; ?></strong><br>
                                    <small class="text-danger">IPK: <?php echo $row['ipk_mentee']; ?></small>
                                </td>

                                <td>
                                    <strong><?php echo $row['nama_mentor']; ?></strong><br>
                                    <small class="text-success">IPK: <?php echo $row['ipk_mentor']; ?></small>
                                </td>

                                <td><?php echo $row['nama_prodi']; ?></td>

                                <td>
                                    <span class="badge <?php echo $status_badge; ?>"><?php echo $status_text; ?></span>
                                </td>

                                <td>
                                    <a href="delete.php?id=<?php echo $row['id_match']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Batalkan pasangan ini?')">Hapus</a>
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