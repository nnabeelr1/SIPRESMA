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
        /* --- THEME CONFIGURATION --- */
        :root {
            --primary: #10b981;       
            --primary-dark: #047857;
            --text-main: #0f172a;     
            --text-muted: #64748b;    
            --radius-xl: 24px;
            --nav-height: 80px;
        }

        /* --- GLOBAL & ANIMATION --- */
        html { overflow-y: scroll; } 
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            
            /* BACKGROUND: Mint Green + Dot Matrix (KONSISTEN) */
            background-color: #f0fdf4; 
            background-image: 
                radial-gradient(#86efac 1.2px, transparent 1.2px), 
                radial-gradient(circle at top center, rgba(16, 185, 129, 0.1) 0%, rgba(240, 253, 244, 0) 70%); 
            background-size: 24px 24px, 100% 100%;
            background-attachment: fixed;
        }

        /* Keyframes */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-entry {
            animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0; 
        }

        /* --- NAVBAR (GLASS) --- */
        .navbar-clean {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(16, 185, 129, 0.1); 
            border-top: 3px solid var(--primary); 
            height: var(--nav-height);
            position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 4px 20px -10px rgba(16, 185, 129, 0.15);
        }
        .logo-box {
            background: linear-gradient(135deg, #10b981, #047857);
            color: white; padding: 8px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }
        .brand-text { letter-spacing: -0.5px; color: var(--text-main); }
        .brand-sub { letter-spacing: 1.5px; font-weight: 700; color: var(--text-muted); font-size: 10px; }

        /* --- HERO SECTION --- */
        .welcome-pill {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius-xl);
            padding: 2.5rem; 
            text-align: center;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }
        .welcome-pill:hover { transform: translateY(-3px); }

        /* --- ALERT STATUS --- */
        .alert-glass {
            border-radius: 16px; padding: 1.5rem;
            display: flex; align-items: flex-start; gap: 1rem;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        
        /* Pending */
        .alert-pending { background: rgba(255, 251, 235, 0.8); border: 1px solid #fcd34d; }
        .icon-box-pending { background: #fef3c7; color: #b45309; width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        
        /* Active */
        .alert-active { background: rgba(236, 253, 245, 0.8); border: 1px solid #6ee7b7; }
        .icon-box-active { background: #d1fae5; color: #047857; width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        
        /* Empty */
        .alert-empty { 
            background: rgba(255, 255, 255, 0.5); border: 2px dashed #cbd5e1; 
            text-align: center; padding: 2rem; display: block;
        }

        /* --- MENU GRID --- */
        .menu-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: 20px;
            padding: 2rem 1.5rem;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-decoration: none; color: var(--text-main);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }
        .menu-card:hover { 
            transform: translateY(-8px); 
            background: white; 
            box-shadow: 0 15px 30px -5px rgba(16, 185, 129, 0.15); 
            border-color: var(--primary); 
        }
        
        .menu-icon { font-size: 3rem; margin-bottom: 1rem; transition: transform 0.3s; }
        .menu-card:hover .menu-icon { transform: scale(1.15) rotate(5deg); }
        
        .menu-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 0.3rem; }
        .menu-desc { font-size: 0.85rem; color: var(--text-muted); text-align: center; }

        /* Icon Colors */
        .icon-blue { background: linear-gradient(135deg, #3b82f6, #2563eb); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .icon-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .icon-orange { background: linear-gradient(135deg, #f59e0b, #d97706); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        /* --- DATE BADGE --- */
        .date-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.5rem 1rem; background: white; border-radius: 50px;
            font-weight: 600; color: var(--text-muted); font-size: 0.85rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 1rem;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="#">
                <div class="logo-box">
                    <iconify-icon icon="solar:infinity-bold" width="24"></iconify-icon>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 brand-text">SIPRESMA</h5>
                    <p class="mb-0 brand-sub">STUDENT PORTAL</p>
                </div>
            </a>

            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-block text-end">
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><?php echo $nama_depan; ?></p>
                    <p class="mb-0 text-success d-flex align-items-center justify-content-end gap-1" style="font-size: 0.75rem; font-weight: 600;">
                        <iconify-icon icon="solar:record-circle-bold" style="font-size: 8px;"></iconify-icon> <?php echo $nim_saya; ?>
                    </p>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-white rounded-circle border p-1 shadow-sm transition-hover">
                             <iconify-icon icon="solar:user-circle-bold-duotone" width="40" class="text-primary"></iconify-icon>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-2">
                        <li><a class="dropdown-item rounded-3 text-danger fw-bold" href="../logout.php">
                            <iconify-icon icon="solar:logout-2-bold-duotone" class="me-2"></iconify-icon>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5 pb-5">
        
        <div class="welcome-pill animate-entry" style="animation-delay: 0.1s;">
            <h2 class="fw-bold mb-2 text-dark">Halo, <?php echo $nama_saya; ?>! 👋</h2>
            <p class="text-muted mb-0">Selamat datang di Portal Akademik Mahasiswa.</p>
            <div class="date-badge">
                <iconify-icon icon="solar:calendar-bold-duotone" class="text-primary"></iconify-icon>
                <?php echo date('d F Y'); ?>
            </div>
        </div>

        <div class="row justify-content-center mb-4 animate-entry" style="animation-delay: 0.2s;">
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
                        <div class="alert-glass alert-pending">
                            <div class="icon-box-pending">
                                <iconify-icon icon="solar:hourglass-line-bold" class="fs-4"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Menunggu Verifikasi Dosen</h6>
                                <p class="text-muted small mb-0">
                                    Admin merekomendasikanmu untuk program <strong>Peer Support</strong>. 
                                    Menunggu persetujuan Dosen Wali.
                                </p>
                            </div>
                        </div>';
                    } else if ($data_ps['status'] == 'aktif') {
                        // STATUS: AKTIF
                        echo '
                        <div class="alert-glass alert-active">
                            <div class="icon-box-active">
                                <iconify-icon icon="solar:check-circle-bold" class="fs-4"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Peer Support Aktif! 🎉</h6>
                                <p class="text-muted small mb-0">
                                    Selamat! Program mentoringmu telah disetujui. Silakan mulai kegiatan belajar bersama partner kamu.
                                </p>
                            </div>
                        </div>';
                    }
                } else {
                    // STATUS: KOSONG
                    echo '
                    <div class="alert-empty text-muted">
                        <iconify-icon icon="solar:book-bookmark-linear" class="fs-2 mb-2 opacity-50"></iconify-icon>
                        <p class="small mb-0">Belum ada kegiatan Peer Support aktif.</p>
                    </div>';
                }
                ?>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center mb-4 animate-entry" style="animation-delay: 0.3s;">
            <h6 class="fw-bold text-muted text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Layanan Akademik</h6>
        </div>
        
        <div class="row g-4 justify-content-center animate-entry" style="animation-delay: 0.4s;">
            
            <div class="col-md-4 col-lg-3">
                <a href="../krs/index.php" class="menu-card">
                    <iconify-icon icon="solar:cart-large-2-bold-duotone" class="menu-icon icon-blue"></iconify-icon>
                    <span class="menu-title">KRS Online</span>
                    <span class="menu-desc">Rencana Studi Semester</span>
                </a>
            </div>

            <div class="col-md-4 col-lg-3">
                <a href="../khs/index.php" class="menu-card">
                    <iconify-icon icon="solar:chart-square-bold-duotone" class="menu-icon icon-purple"></iconify-icon>
                    <span class="menu-title">Lihat Nilai (KHS)</span>
                    <span class="menu-desc">Hasil Studi & Transkrip</span>
                </a>
            </div>

            <div class="col-md-4 col-lg-3">
                <a href="../prestasi/index.php" class="menu-card">
                    <iconify-icon icon="solar:cup-star-bold-duotone" class="menu-icon icon-orange"></iconify-icon>
                    <span class="menu-title">Prestasi Mahasiswa</span>
                    <span class="menu-desc">Input Sertifikat Lomba</span>
                </a>
            </div>

        </div>

        <footer class="text-center mt-5 mb-3 text-muted small animate-entry" style="animation-delay: 0.5s;">
            &copy; 2025 SIPRESMA Student Portal
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>