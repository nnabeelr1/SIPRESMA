<?php
session_start();
include '../config/koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    header("Location: ../index.php?pesan=belum_login");
    exit();
}

// 2. Cek Role Mahasiswa
if ($_SESSION['role'] != 'mahasiswa') {
    echo "Akses Ditolak! Anda bukan mahasiswa.";
    exit();
}

// 3. Ambil Identitas Mahasiswa
$username = $_SESSION['username'];
$query_mhs = mysqli_query($koneksi, "SELECT nim, nama_lengkap FROM mahasiswa 
                                     JOIN user ON mahasiswa.id_user = user.id_user 
                                     WHERE user.username = '$username'");
$data_mhs = mysqli_fetch_assoc($query_mhs);

if ($data_mhs) {
    $nim_saya = $data_mhs['nim'];
    $nama_saya = $data_mhs['nama_lengkap'];
} else {
    echo "Data mahasiswa tidak ditemukan. Hubungi Admin.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-info mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">🎓 SIPRESMA Student</a>
            <a href="../logout.php" class="btn btn-light btn-sm text-info fw-bold">Logout</a>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <h1 class="display-4 text-info">Halo, <?php echo $nama_saya; ?>! 👋</h1>
                <p class="lead mt-3">Selamat datang di Portal Mahasiswa SIPRESMA.</p>
                <hr class="my-4">

                <div class="row justify-content-center mb-5">
                    <div class="col-md-8">
                        <?php
                        // Cek status Peer Support
                        $q_cek = mysqli_query($koneksi, "SELECT * FROM peer_support 
                                                         WHERE (mentee_nim = '$nim_saya' OR mentor_nim = '$nim_saya') 
                                                         AND status != 'ditolak'");
                        $data_ps = mysqli_fetch_assoc($q_cek);

                        if ($data_ps) {
                            if ($data_ps['status'] == 'menunggu_dosen') {
                                echo '
                                <div class="alert alert-warning shadow-sm text-start border-warning">
                                    <h4 class="alert-heading">⏳ Sedang Diproses Dosen Wali</h4>
                                    <p class="mb-0">
                                        Admin telah merekomendasikan kamu untuk program Peer Support.<br>
                                        Saat ini usulan tersebut sedang menunggu persetujuan dari <strong>Dosen Wali</strong>.
                                    </p>
                                </div>';
                            } else if ($data_ps['status'] == 'aktif') {
                                echo '
                                <div class="alert alert-success shadow-sm text-start border-success">
                                    <h4 class="alert-heading">🎉 SURAT PERINTAH MENTORING</h4>
                                    <p>Selamat! Dosen Walimu telah menyetujui program ini.</p>
                                    <hr>
                                    <p class="mb-0">
                                        Status: <strong>AKTIF</strong> <br>
                                        Silakan segera temui pasangan mentoringmu di kampus untuk mulai belajar bersama.
                                    </p>
                                </div>';
                            }
                        } else {
                            echo '
                            <div class="alert alert-light border shadow-sm">
                                <p class="text-muted mb-0">
                                    Belum ada kegiatan Peer Support yang aktif untukmu saat ini. <br>
                                    Tetap semangat belajar! 🚀
                                </p>
                            </div>';
                        }
                        ?>
                    </div>
                </div>

                <h5 class="mb-4 text-secondary">Menu Akademik</h5>
                <div class="row justify-content-center">
                    
                    <div class="col-md-4 mb-3">
                        <a href="../krs/index.php" class="btn btn-primary btn-lg w-100 shadow-sm py-4">
                            🛒<br><strong>Isi KRS Online</strong>
                        </a>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <a href="../khs/index.php" class="btn btn-secondary btn-lg w-100 shadow-sm py-4">
                            📊<br><strong>Lihat KHS / Nilai</strong>
                        </a>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <a href="../prestasi/index.php" class="btn btn-outline-warning text-dark btn-lg w-100 shadow-sm py-4">
                            🏆<br><strong>Prestasi</strong>
                        </a>
                    </div>

                </div>

                <div class="mt-5 text-muted small">
                    &copy; 2025 SIPRESMA Student Portal
                </div>
            </div>
        </div>
    </div>

</body>
</html>