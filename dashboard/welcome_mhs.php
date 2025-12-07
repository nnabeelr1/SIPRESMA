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
    // Ambil nama depan untuk navbar
    $nama_depan = explode(' ', trim($nama_saya))[0];
} else {
    echo "Data mahasiswa tidak ditemukan. Hubungi Admin.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            /* Palette Konsisten (Emerald & Slate) */
            --primary: #10b981;       /* Emerald 500 */
            --bg-body: #f8fafc;       /* Slate 50 */
            --text-main: #1e293b;     /* Slate 800 */
            --text-muted: #64748b;    /* Slate 400 */
            --card-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            padding-bottom: 3rem;
            min-height: 100vh;
        }

        /* --- Navbar Clean (Sama Persis Admin) --- */
        .navbar-clean {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 0.8rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo-box {
            background: rgba(16, 185, 129, 0.1); /* Soft Emerald */
            color: #10b981;
            width: 42px; height: 42px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        /* --- Welcome Section --- */
        .welcome-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            border: 1px solid rgba(0,0,0,0.03);
        }

        /* --- Status Alert (Peer Support) --- */
        .alert-custom {
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
        }
        .alert-pending { background: #fffbeb; border-left: 5px solid #f59e0b; }
        .alert-active { background: #ecfdf5; border-left: 5px solid #10b981; }
        .alert-empty { background: white; text-align: center; display: block; border: 1px dashed #cbd5e1; }
        
        .alert-icon-box {
            width: 42px; height: 42px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; flex-shrink: 0;
        }
        .bg-icon-pending { background: rgba(245, 158, 11, 0.15); color: #b45309; }
        .bg-icon-active { background: rgba(16, 185, 129, 0.15); color: #047857; }

        /* --- Menu Grid Buttons (Clean Style) --- */
        .menu-grid-btn {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            background: white; padding: 2.5rem 1.5rem;
            border-radius: 20px;
            text-decoration: none; color: var(--text-main);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            box-shadow: var(--card-shadow);
        }
        .menu-grid-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.08);
            border-color: var(--primary);
        }
        .menu-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            transition: transform 0.3s;
        }
        .menu-grid-btn:hover .menu-icon { transform: scale(1.1); }
        
        .menu-label { font-weight: 700; font-size: 1.1rem; margin-bottom: 0.2rem; }
        .menu-desc { font-size: 0.85rem; color: var(--text-muted); }

        /* Icon Colors (Soft Pastel) */
        .icon-krs { color: #3b82f6; }
        .icon-khs { color: #8b5cf6; }
        .icon-prestasi { color: #f59e0b; }

    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="#">
                <div class="logo-box">
                    <iconify-icon icon="solar:infinity-bold" style="font-size: 1.5rem;"></iconify-icon>
                </div>
                <div style="line-height: 1.2;">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">SIPRESMA</h5>
                    <small class="text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 1px; display: block;">
                        STUDENT PORTAL
                    </small>
                </div>
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-block text-end" style="line-height: 1.2;">
                    <span class="fw-bold d-block text-dark" style="font-size: 0.9rem;">
                        <?php echo $nama_depan; ?>
                    </span>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        <?php echo $nim_saya; ?>
                    </small>
                </div>
                
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-light rounded-circle border p-1 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <iconify-icon icon="solar:user-circle-bold" class="text-secondary" style="font-size: 1.8rem;"></iconify-icon>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                        <li><a class="dropdown-item rounded-3" href="../logout.php">
                            <iconify-icon icon="solar:logout-2-bold" class="me-2"></iconify-icon>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <div class="welcome-card">
            <div class="welcome-content">
                <h2 class="fw-bold mb-2">Halo, <?php echo $nama_saya; ?>! 👋</h2>
                <p class="text-muted mb-0">Selamat datang di Portal Akademik Mahasiswa.</p>
            </div>
        </div>

        <div class="row justify-content-center mb-5">
            <div class="col-lg-10">
                <?php
                // Cek status Peer Support
                $q_cek = mysqli_query($koneksi, "SELECT * FROM peer_support 
                                                 WHERE (mentee_nim = '$nim_saya' OR mentor_nim = '$nim_saya') 
                                                 AND status != 'ditolak'");
                $data_ps = mysqli_fetch_assoc($q_cek);

                if ($data_ps) {
                    if ($data_ps['status'] == 'menunggu_dosen') {
                        // STATUS: PENDING
                        echo '
                        <div class="alert-custom alert-pending">
                            <div class="alert-icon-box bg-icon-pending">
                                <iconify-icon icon="solar:hourglass-line-bold"></iconify-icon>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Menunggu Verifikasi Dosen</h5>
                                <p class="text-muted small mb-0">
                                    Admin telah merekomendasikanmu untuk program <strong>Peer Support</strong>. 
                                    Saat ini menunggu persetujuan Dosen Wali.
                                </p>
                            </div>
                        </div>';
                    } else if ($data_ps['status'] == 'aktif') {
                        // STATUS: AKTIF
                        echo '
                        <div class="alert-custom alert-active">
                            <div class="alert-icon-box bg-icon-active">
                                <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Peer Support Aktif! 🎉</h5>
                                <p class="text-success small mb-0 fw-semibold">
                                    Selamat! Program mentoringmu telah disetujui. 
                                </p>
                                <p class="text-muted small mb-0 mt-1">
                                    Silakan hubungi partner belajarmu dan mulai kegiatan mentoring di kampus.
                                </p>
                            </div>
                        </div>';
                    }
                } else {
                    // STATUS: KOSONG (Default)
                    echo '
                    <div class="alert-custom alert-empty">
                        <div class="py-3">
                            <iconify-icon icon="solar:book-bookmark-linear" class="fs-1 text-muted mb-2"></iconify-icon>
                            <p class="text-muted small mb-0">Belum ada kegiatan Peer Support aktif.</p>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>

        <h5 class="fw-bold mb-4 text-center text-dark">Layanan Akademik</h5>
        
        <div class="row g-4 justify-content-center">
            
            <div class="col-md-4 col-lg-3">
                <a href="../krs/index.php" class="menu-grid-btn">
                    <iconify-icon icon="solar:cart-large-2-bold-duotone" class="menu-icon icon-krs"></iconify-icon>
                    <span class="menu-label">KRS Online</span>
                    <span class="menu-desc">Rencana Studi Semester</span>
                </a>
            </div>

            <div class="col-md-4 col-lg-3">
                <a href="../khs/index.php" class="menu-grid-btn">
                    <iconify-icon icon="solar:chart-square-bold-duotone" class="menu-icon icon-khs"></iconify-icon>
                    <span class="menu-label">Lihat Nilai (KHS)</span>
                    <span class="menu-desc">Hasil Studi & Transkrip</span>
                </a>
            </div>

            <div class="col-md-4 col-lg-3">
                <a href="../prestasi/index.php" class="menu-grid-btn">
                    <iconify-icon icon="solar:cup-star-bold-duotone" class="menu-icon icon-prestasi"></iconify-icon>
                    <span class="menu-label">Prestasi Mahasiswa</span>
                    <span class="menu-desc">Input Sertifikat Lomba</span>
                </a>
            </div>

        </div>

        <div class="text-center mt-5 mb-5">
            <small class="text-muted">© 2025 SIPRESMA Student Portal</small>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>