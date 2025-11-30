<?php
// --- 1. KODE SATPAM (Security) ---
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    header("Location: ../index.php?pesan=belum_login");
    exit();
}
if ($_SESSION['role'] != 'admin') {
    header("Location: welcome_mhs.php");
    exit();
}

include '../config/koneksi.php';

// --- 2. QUERY HITUNG DATA (Statistik) ---
$total_mhs    = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mahasiswa"));
$total_dosen  = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM dosen"));
$total_prodi  = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM prodi"));
$total_matkul = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM matakuliah"));
$total_kelas  = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM kelas"));
// Hitung semester aktif
$q_smt = mysqli_query($koneksi, "SELECT nama_semester FROM semester WHERE status='aktif'");
$smt_aktif = mysqli_fetch_assoc($q_smt);
$nama_smt = $smt_aktif ? $smt_aktif['nama_semester'] : 'Tidak Ada';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - SIPRESMA</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-stat { transition: all 0.3s; cursor: pointer; }
        .card-stat:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">🎓 SIPRESMA Admin</a>
            <div class="d-flex gap-2">
                <a href="../user/index.php" class="btn btn-light btn-sm fw-bold text-primary">⚙️ Kelola User</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="alert alert-info border-0 shadow-sm mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="alert-heading">Selamat Datang, Admin! 👋</h4>
                <p class="mb-0">Semester Aktif Saat Ini: <strong><?php echo $nama_smt; ?></strong></p>
            </div>
            <a href="../semester/index.php" class="btn btn-primary">⚙️ Ganti Semester</a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card card-stat bg-primary text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title opacity-75">Mahasiswa</h5>
                        <h1 class="display-4 fw-bold mb-0"><?php echo $total_mhs; ?></h1>
                    </div>
                    <div class="card-footer bg-primary border-0">
                        <a href="../mahasiswa/index.php" class="text-white text-decoration-none small">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-stat bg-success text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title opacity-75">Dosen</h5>
                        <h1 class="display-4 fw-bold mb-0"><?php echo $total_dosen; ?></h1>
                    </div>
                    <div class="card-footer bg-success border-0">
                        <a href="../dosen/index.php" class="text-white text-decoration-none small">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-stat bg-warning text-dark h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title opacity-75">Prodi</h5>
                        <h1 class="display-4 fw-bold mb-0"><?php echo $total_prodi; ?></h1>
                    </div>
                    <div class="card-footer bg-warning border-0">
                        <a href="../prodi/index.php" class="text-dark text-decoration-none small">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>

             <div class="col-md-3">
                <div class="card card-stat bg-danger text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title opacity-75">Mata Kuliah</h5>
                        <h1 class="display-4 fw-bold mb-0"><?php echo $total_matkul; ?></h1>
                    </div>
                    <div class="card-footer bg-danger border-0">
                        <a href="../matakuliah/index.php" class="text-white text-decoration-none small">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="mb-3 text-secondary">Akses Modul Cepat</h5>
        
        <div class="row mb-2">
            <div class="col-md-4 mb-2">
                <a href="../mahasiswa/index.php" class="btn btn-outline-primary w-100 py-3 fw-bold">👨‍🎓 Mahasiswa</a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="../dosen/index.php" class="btn btn-outline-secondary w-100 py-3 fw-bold">👩‍🏫 Dosen</a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="../kelas/index.php" class="btn btn-outline-warning text-dark w-100 py-3 fw-bold">
                    🏫 Data Kelas <span class="badge bg-danger rounded-pill"><?php echo $total_kelas; ?></span>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <a href="../semester/index.php" class="btn btn-outline-danger w-100 py-3 fw-bold">
                    🗓️ Atur Semester (KRS)
                </a>
            </div>

            <div class="col-md-6 mb-2">
                <a href="../peersupport/index.php" class="btn btn-outline-info w-100 py-3 fw-bold">
                    🤝 Peer Support
                </a>
            </div>
        </div>

    </div>

    <footer class="text-center mt-5 py-4 text-muted border-top">
        &copy; 2025 SIPRESMA Group 4
    </footer>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>