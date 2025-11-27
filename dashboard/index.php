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

<?php
include '../config/koneksi.php';

// --- QUERY HITUNG DATA (REAL-TIME) ---
// 1. Hitung Mahasiswa
$q_mhs = mysqli_query($koneksi, "SELECT * FROM mahasiswa");
$total_mhs = mysqli_num_rows($q_mhs);

// 2. Hitung Dosen
$q_dosen = mysqli_query($koneksi, "SELECT * FROM dosen");
$total_dosen = mysqli_num_rows($q_dosen);

// 3. Hitung Prodi
$q_prodi = mysqli_query($koneksi, "SELECT * FROM prodi");
$total_prodi = mysqli_num_rows($q_prodi);

// 4. Hitung Mata Kuliah
$q_matkul = mysqli_query($koneksi, "SELECT * FROM matakuliah");
$total_matkul = mysqli_num_rows($q_matkul);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <div class="d-flex">
                <a href="../login.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <h4 class="alert-heading">Selamat Datang, Admin! 👋</h4>
            <p class="mb-0">Berikut adalah ringkasan data akademik terkini di sistem SIPRESMA.</p>
        </div>

        <div class="row g-4 mb-5">
            
            <div class="col-md-3">
                <div class="card card-stat bg-primary text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title opacity-75">Mahasiswa</h5>
                        <h1 class="display-4 fw-bold mb-0"><?php echo $total_mhs; ?></h1>
                        <p class="mb-0 small">Orang terdaftar</p>
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
                        <p class="mb-0 small">Pengajar aktif</p>
                    </div>
                    <div class="card-footer bg-success border-0">
                        <a href="../dosen/index.php" class="text-white text-decoration-none small">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-stat bg-warning text-dark h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title opacity-75">Program Studi</h5>
                        <h1 class="display-4 fw-bold mb-0"><?php echo $total_prodi; ?></h1>
                        <p class="mb-0 small">Jurusan tersedia</p>
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
                        <p class="mb-0 small">Kurikulum aktif</p>
                    </div>
                    <div class="card-footer bg-danger border-0">
                        <a href="../matakuliah/index.php" class="text-white text-decoration-none small">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>

        </div>

        <h5 class="mb-3 text-secondary">Akses Modul Cepat</h5>
        <div class="row">
            <div class="col-md-2 mb-2">
                <a href="../mahasiswa/index.php" class="btn btn-outline-primary w-100 py-3 fw-bold">
                    👨‍🎓 Mahasiswa
                </a>
            </div>
            <div class="col-md-2 mb-2">
                <a href="../dosen/index.php" class="btn btn-outline-secondary w-100 py-3 fw-bold">
                    👩‍🏫 Dosen
                </a>
            </div>
            <div class="col-md-2 mb-2">
                <a href="../nilai/index.php" class="btn btn-outline-success w-100 py-3 fw-bold">
                    📊 Nilai
                </a>
            </div>
            <div class="col-md-2 mb-2">
                <a href="../peersupport/index.php" class="btn btn-outline-info w-100 py-3 fw-bold">
                    🤝 Peer Support
                </a>
            </div>
        </div>

    </div>

    <footer class="text-center mt-5 py-4 text-muted border-top">
        &copy; 2025 SIPRESMA - Sistem Informasi Prestasi & Risiko Akademik
    </footer>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>