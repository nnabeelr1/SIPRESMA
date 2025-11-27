<?php
session_start();

// 1. Cek apakah user sudah login?
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    header("Location: ../index.php?pesan=belum_login");
    exit();
}

// 2. Cek apakah yang akses beneran mahasiswa?
if ($_SESSION['role'] != 'mahasiswa') {
    echo "Akses Ditolak! Anda bukan mahasiswa.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
...

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-info mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">🎓 SIPRESMA Student</a>
            <a href="../logout.php" class="btn btn-light btn-sm text-info fw-bold">Logout</a>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <h1 class="display-4 text-info">Halo, <?php echo $_SESSION['nama_lengkap']; ?>! 👋</h1>
                <p class="lead mt-3">Selamat datang di Portal Mahasiswa SIPRESMA.</p>
                <hr class="my-4">
                
                <p class="text-muted">
                    Fitur <strong>KRS Online</strong>, <strong>Lihat Nilai</strong>, dan <strong>Peer Support</strong> 
                    sedang dikerjakan oleh teman-teman kelompokmu. <br>
                    Harap bersabar ya! 🛠️
                </p>

                <div class="alert alert-warning d-inline-block mt-3">
                    Status Akademik Kamu: <strong>Aktif</strong>
                </div>
            </div>
        </div>
    </div>

</body>
</html>