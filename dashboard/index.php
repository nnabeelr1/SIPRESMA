<?php
session_start();
include '../config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// HITUNG STATISTIK
$jml_mhs = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mahasiswa"));
$jml_prestasi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM prestasi"));
$jml_rawan = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE ipk_terakhir < 2.50 AND ipk_terakhir > 0"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Smart Dashboard SIPRESMA</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS UPDATE: Tinggi tabel dipadatkan biar cuma muat +/- 5 baris */
        .table-scroll-area {
            max-height: 250px; /* <--- INI KUNCINYA (Dikecilkan dr 400 ke 250) */
            overflow-y: auto;
        }
        /* Sticky Header */
        .sticky-header th {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">🧠 SIPRESMA Intelligence</a>
            <div class="d-flex gap-2">
                <a href="../mahasiswa/index.php" class="btn btn-light btn-sm text-primary fw-bold">Data Mahasiswa</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-white border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <h1 class="display-4 fw-bold text-primary"><?php echo $jml_mhs; ?></h1>
                        <p class="text-muted">Total Mahasiswa</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <h1 class="display-4 fw-bold"><?php echo $jml_rawan; ?></h1>
                        <p>Mahasiswa Berisiko (IPK < 2.50)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <h1 class="display-4 fw-bold"><?php echo $jml_prestasi; ?></h1>
                        <p>Total Prestasi Tercatat</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            
            <div class="col-md-6 mb-4">
                <div class="card border-danger shadow-sm h-100">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">🚨 EWS: Mahasiswa Berisiko</h5>
                        <div class="d-flex gap-2">
                            <a href="auto_match.php" class="btn btn-light btn-sm fw-bold text-danger" onclick="return confirm('Jalankan Auto-Matchmaking?')">⚡ Auto Match</a>
                            <a href="../peersupport/index.php" class="btn btn-outline-light btn-sm fw-bold">📂 Lihat Data</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="alert alert-light small mb-0 border-bottom rounded-0">
                            Sistem mendeteksi mahasiswa dengan <strong>IPK < 2.50</strong>.
                        </div>
                        <div class="table-scroll-area">
                            <table class="table table-striped mb-0 small">
                                <thead class="sticky-header">
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Prodi</th>
                                        <th>IPK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q_ews = mysqli_query($koneksi, "
                                        SELECT m.*, p.nama_prodi 
                                        FROM mahasiswa m 
                                        JOIN prodi p ON m.id_prodi = p.id_prodi
                                        WHERE m.ipk_terakhir < 2.50 AND m.ipk_terakhir > 0
                                        ORDER BY m.ipk_terakhir ASC
                                    ");
                                    if(mysqli_num_rows($q_ews) == 0){ echo "<tr><td colspan='4' class='text-center py-4'>Aman.</td></tr>"; }
                                    while($row = mysqli_fetch_assoc($q_ews)){
                                        echo "<tr>
                                            <td>{$row['nim']}</td>
                                            <td>{$row['nama_lengkap']}</td>
                                            <td>{$row['nama_prodi']}</td>
                                            <td class='fw-bold text-danger'>{$row['ipk_terakhir']}</td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">🏆 Kandidat Mawapres</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="alert alert-light small mb-0 border-bottom rounded-0">
                            Rekomendasi: <strong>IPK > 3.50</strong> + Punya <strong>Prestasi</strong>.
                        </div>
                        <div class="table-scroll-area">
                            <table class="table table-striped mb-0 small">
                                <thead class="sticky-header">
                                    <tr>
                                        <th>Nama</th>
                                        <th>IPK</th>
                                        <th>Prestasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q_mawapres = mysqli_query($koneksi, "
                                        SELECT m.nama_lengkap, m.nim, m.ipk_terakhir, 
                                        COUNT(pr.id_prestasi) as jumlah_prestasi
                                        FROM mahasiswa m
                                        JOIN prestasi pr ON m.nim = pr.nim
                                        WHERE m.ipk_terakhir >= 3.50
                                        GROUP BY m.nim
                                        ORDER BY m.ipk_terakhir DESC
                                    ");
                                    if(mysqli_num_rows($q_mawapres) == 0){ echo "<tr><td colspan='4' class='text-center py-4'>Belum ada kandidat.</td></tr>"; }
                                    while($row = mysqli_fetch_assoc($q_mawapres)){
                                        echo "<tr>
                                            <td><strong>{$row['nama_lengkap']}</strong><br><span class='text-muted'>{$row['nim']}</span></td>
                                            <td class='fw-bold text-success'>{$row['ipk_terakhir']}</td>
                                            <td><span class='badge bg-primary'>{$row['jumlah_prestasi']} Lomba</span></td>
                                            <td><a href='../prestasi/index.php' class='btn btn-xs btn-outline-dark btn-sm'>Cek</a></td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <h6 class="text-secondary mt-2">Akses Modul Cepat</h6>
        <div class="row">
            <div class="col-md-3 mb-2"><a href="../mahasiswa/index.php" class="btn btn-outline-primary w-100">👨‍🎓 Mahasiswa</a></div>
            <div class="col-md-3 mb-2"><a href="../dosen/index.php" class="btn btn-outline-success w-100">👩‍🏫 Dosen</a></div>
            <div class="col-md-3 mb-2"><a href="../semester/index.php" class="btn btn-outline-danger w-100">🗓️ Semester</a></div>
            <div class="col-md-3 mb-2"><a href="../prestasi/index.php" class="btn btn-outline-warning text-dark w-100">🏆 Prestasi</a></div>
        </div>

    </div>
</body>
</html>