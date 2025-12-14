<?php
session_start();
include '../config/koneksi.php';

// Cek Dosen
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../index.php");
    exit();
}

// 1. Cari Data Dosen
$id_user = $_SESSION['id_user'];
$q_dosen = mysqli_query($koneksi, "SELECT nidn, nama_lengkap FROM dosen WHERE id_user='$id_user'");
$data_dosen = mysqli_fetch_assoc($q_dosen);
$nidn_saya = $data_dosen['nidn'];
$nama_depan = explode(' ', trim($data_dosen['nama_lengkap']))[0];

// Logic Peer Support
if (isset($_POST['aksi'])) {
    $id_match = $_POST['id_match'];
    $peran    = $_POST['peran_saya']; 
    $aksi     = $_POST['aksi'];

    if ($aksi == 'terima') {
        if ($peran == 'doswal_mentee') {
            mysqli_query($koneksi, "UPDATE peer_support SET acc_doswal_mentee=1 WHERE id_match='$id_match'");
        } else {
            mysqli_query($koneksi, "UPDATE peer_support SET acc_doswal_mentor=1 WHERE id_match='$id_match'");
        }

        // Cek apakah dua-duanya sudah setuju
        $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM peer_support WHERE id_match='$id_match'"));
        
        if ($cek['acc_doswal_mentee'] == 1 && $cek['acc_doswal_mentor'] == 1) {
            mysqli_query($koneksi, "UPDATE peer_support SET status='aktif' WHERE id_match='$id_match'");
            echo "<script>alert('✅ Program Aktif! Kedua dosen wali telah menyetujui.'); window.location='welcome_dosen.php';</script>";
        } else {
            echo "<script>alert('✅ Disetujui. Menunggu persetujuan partner dosen wali.'); window.location='welcome_dosen.php';</script>";
        }

    } else {
        mysqli_query($koneksi, "UPDATE peer_support SET status='ditolak' WHERE id_match='$id_match'");
        echo "<script>alert('❌ Usulan Ditolak.'); window.location='welcome_dosen.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
        html { overflow-y: scroll; } /* Anti-Geser */
        
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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(16, 185, 129, 0.3);
            box-shadow: 0 8px 20px -5px rgba(16, 185, 129, 0.15);
            padding: 0.8rem 1.5rem; 
            border-radius: 50px;
            display: inline-flex; align-items: center; gap: 15px;
            color: var(--text-main);
            transition: all 0.3s ease;
        }
        .welcome-pill:hover { transform: translateY(-2px); border-color: var(--primary); box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.25); }
        
        .date-badge {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(16, 185, 129, 0.3);
            box-shadow: 0 8px 20px -5px rgba(16, 185, 129, 0.15);
            color: var(--text-main); font-weight: 700; font-size: 0.95rem;
            padding: 0.8rem 1.5rem; border-radius: 50px;
            display: inline-flex; align-items: center; gap: 10px;
            transition: all 0.3s ease;
        }
        .date-badge:hover { transform: translateY(-2px); border-color: var(--primary); }
        .date-badge iconify-icon { color: var(--primary); font-size: 1.2rem; }

        /* --- MENU CARDS --- */
        .menu-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            text-decoration: none; color: var(--text-main);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }
        .menu-card:hover { transform: translateY(-5px); background: white; box-shadow: 0 15px 30px rgba(16, 185, 129, 0.15); border-color: var(--primary); }
        
        .menu-icon-box {
            width: 56px; height: 56px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-right: 1.2rem;
        }
        .bg-blue-soft { background: #eff6ff; color: #3b82f6; }
        .bg-orange-soft { background: #fff7ed; color: #f97316; }

        /* --- CARD & TABLE --- */
        .card-modern {
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius-xl);
            padding: 0; 
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03);
            overflow: hidden; 
            transition: transform 0.3s ease;
        }

        .table-wrapper { max-height: 500px; overflow-y: auto; overflow-x: hidden; }
        .table-wrapper::-webkit-scrollbar { width: 6px; }
        .table-wrapper::-webkit-scrollbar-track { background: transparent; }
        .table-wrapper::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }

        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; table-layout: fixed; }
        
        .table-custom thead th {
            position: sticky; top: 0; z-index: 20;
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            box-shadow: 0 1px 0 rgba(0,0,0,0.05);
            color: var(--text-muted); font-size: 0.75rem; font-weight: 800; 
            text-transform: uppercase; letter-spacing: 0.1em;
            padding: 1.5rem 1.2rem; border-bottom: 1px solid #e2e8f0;
        }
        .table-custom tbody td { 
            padding: 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.03); 
            vertical-align: middle; font-size: 0.95rem; font-weight: 500;
            background: transparent;
        }
        .table-custom tr:last-child td { border-bottom: none; }
        .table-custom tbody tr:hover { background-color: rgba(255,255,255,0.9); }

        /* --- BUTTONS & BADGES --- */
        .btn-action-soft {
            width: 38px; height: 38px; border-radius: 10px; border: none;
            display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.2s; cursor: pointer;
        }
        .btn-soft-success { background: #dcfce7; color: #15803d; }
        .btn-soft-success:hover { background: #15803d; color: white; transform: translateY(-2px); }
        .btn-soft-danger { background: #fee2e2; color: #b91c1c; }
        .btn-soft-danger:hover { background: #b91c1c; color: white; transform: translateY(-2px); }

        .badge-pill { padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
        .badge-role { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        
        /* Steps */
        .step-indicator { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; margin-bottom: 4px; color: var(--text-muted); }
        .step-dot { width: 10px; height: 10px; border-radius: 50%; background: #e2e8f0; }
        .step-active { background: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2); }
        .step-waiting { background: #fbbf24; }

        .alert-info-glass {
            background: rgba(239, 246, 255, 0.7); border: 1px solid #dbeafe; color: #1e40af;
            border-radius: 16px; padding: 1rem; display: flex; gap: 10px; align-items: center;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.05); margin-bottom: 2rem;
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
                    <p class="mb-0 brand-sub">LECTURER PORTAL</p>
                </div>
            </a>
            
            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-block text-end">
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><?php echo $nama_depan; ?></p>
                    <p class="mb-0 text-success d-flex align-items-center justify-content-end gap-1" style="font-size: 0.75rem; font-weight: 600;">
                        <iconify-icon icon="solar:record-circle-bold" style="font-size: 8px;"></iconify-icon> Dosen
                    </p>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-white rounded-circle border p-1 shadow-sm transition-hover">
                             <iconify-icon icon="solar:user-circle-bold-duotone" width="40" class="text-primary"></iconify-icon>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-2">
                        <li><h6 class="dropdown-header text-uppercase small fw-bold">Account</h6></li>
                        <li><a class="dropdown-item rounded-3 mb-1" href="#"><iconify-icon icon="solar:settings-bold-duotone" class="me-2 text-primary"></iconify-icon>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item rounded-3 text-danger fw-bold" href="../logout.php"><iconify-icon icon="solar:logout-2-bold-duotone" class="me-2"></iconify-icon>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5 pb-5">
        
        <div class="row align-items-end mb-5 animate-entry" style="animation-delay: 0.1s;">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="welcome-pill">
                    <span style="font-size: 2rem;">🎓</span>
                    <div>
                        <h5 class="mb-0" style="font-weight: 700; color: var(--text-main);">Selamat Datang, <?php echo $nama_depan; ?>!</h5>
                        <small style="color: var(--text-muted); font-weight: 500;">Dashboard Akademik & Bimbingan Mahasiswa.</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="date-badge">
                    <iconify-icon icon="solar:calendar-bold-duotone"></iconify-icon>
                    <span><?php echo date('d F Y'); ?></span>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5 animate-entry" style="animation-delay: 0.2s;">
            <div class="col-md-6">
                <a href="../nilai/index.php" class="menu-card">
                    <div class="d-flex align-items-center">
                        <div class="menu-icon-box bg-blue-soft">
                            <iconify-icon icon="solar:notebook-bold-duotone"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Input Nilai</h6>
                            <small class="text-muted">Kelola nilai perkuliahan semester ini.</small>
                        </div>
                    </div>
                    <iconify-icon icon="solar:alt-arrow-right-linear" class="fs-4 text-muted"></iconify-icon>
                </a>
            </div>
            <div class="col-md-6">
                <a href="../prestasi/index.php" class="menu-card">
                    <div class="d-flex align-items-center">
                        <div class="menu-icon-box bg-orange-soft">
                            <iconify-icon icon="solar:medal-star-bold-duotone"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Validasi Prestasi</h6>
                            <small class="text-muted">Verifikasi sertifikat prestasi mahasiswa.</small>
                        </div>
                    </div>
                    <iconify-icon icon="solar:alt-arrow-right-linear" class="fs-4 text-muted"></iconify-icon>
                </a>
            </div>
        </div>

        <div class="d-flex align-items-center mb-3 animate-entry" style="animation-delay: 0.3s;">
            <h5 class="fw-bold mb-0 text-dark">Persetujuan Peer Support</h5>
        </div>

        <div class="card-modern animate-entry" style="animation-delay: 0.4s;">
            <div class="p-4 border-bottom">
                <div class="alert-info-glass mb-0">
                    <iconify-icon icon="solar:info-circle-bold" class="fs-4"></iconify-icon>
                    <div>
                        <strong>Konfirmasi Dibutuhkan:</strong> Program mentoring akan aktif jika <strong>Kedua Dosen Wali</strong> (wali mentee & wali mentor) telah memberikan persetujuan.
                    </div>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 35%;">Pasangan Mahasiswa</th>
                            <th style="width: 20%;">Peran Anda</th>
                            <th style="width: 25%;">Status Persetujuan</th>
                            <th style="width: 20%;" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "
                            SELECT ps.*, m1.nama_lengkap as nama_mentee, m1.dosen_wali as doswal_mentee,
                                   m2.nama_lengkap as nama_mentor, m2.dosen_wali as doswal_mentor
                            FROM peer_support ps
                            JOIN mahasiswa m1 ON ps.mentee_nim = m1.nim
                            JOIN mahasiswa m2 ON ps.mentor_nim = m2.nim
                            WHERE (m1.dosen_wali = '$nidn_saya' OR m2.dosen_wali = '$nidn_saya') 
                            AND ps.status = 'menunggu_dosen'
                        ");

                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='4' class='text-center py-5 text-muted'>
                                <iconify-icon icon='solar:clipboard-check-linear' class='fs-1 mb-2'></iconify-icon><br>
                                Tidak ada usulan mentoring yang perlu disetujui.
                            </td></tr>";
                        }

                        while ($row = mysqli_fetch_assoc($query)) {
                            if ($row['doswal_mentee'] == $nidn_saya) {
                                $peran_saya = 'doswal_mentee'; $label_peran = "Wali Mentee"; $sudah_acc = $row['acc_doswal_mentee'];
                            } else {
                                $peran_saya = 'doswal_mentor'; $label_peran = "Wali Mentor"; $sudah_acc = $row['acc_doswal_mentor'];
                            }
                        ?>
                            <tr>
                                <td>
                                    <div class="mb-3">
                                        <small class="fw-bold text-danger d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">MENTEE (PEMBELAJAR)</small>
                                        <div class="fw-bold text-dark fs-6"><?php echo $row['nama_mentee']; ?></div>
                                    </div>
                                    <div>
                                        <small class="fw-bold text-success d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">MENTOR (PENGAJAR)</small>
                                        <div class="fw-bold text-dark fs-6"><?php echo $row['nama_mentor']; ?></div>
                                    </div>
                                </td>
                                
                                <td>
                                    <span class="badge-pill badge-role">
                                        <iconify-icon icon="solar:user-id-bold"></iconify-icon> <?php echo $label_peran; ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <div class="step-indicator">
                                        <div class="step-dot <?php echo ($row['acc_doswal_mentee'])?'step-active':'step-waiting';?>"></div>
                                        <span>Wali Mentee <?php echo ($row['acc_doswal_mentee'])?'(Setuju)':'(Menunggu)';?></span>
                                    </div>
                                    <div class="step-indicator">
                                        <div class="step-dot <?php echo ($row['acc_doswal_mentor'])?'step-active':'step-waiting';?>"></div>
                                        <span>Wali Mentor <?php echo ($row['acc_doswal_mentor'])?'(Setuju)':'(Menunggu)';?></span>
                                    </div>
                                </td>
                                
                                <td class="text-end">
                                    <?php if ($sudah_acc == 0) { ?>
                                        <form method="POST" class="d-flex gap-2 justify-content-end">
                                            <input type="hidden" name="id_match" value="<?php echo $row['id_match']; ?>">
                                            <input type="hidden" name="peran_saya" value="<?php echo $peran_saya; ?>">
                                            
                                            <button type="submit" name="aksi" value="terima" class="btn-action-soft btn-soft-success" title="Setujui">
                                                <iconify-icon icon="solar:check-circle-bold" class="fs-5"></iconify-icon>
                                            </button>
                                            <button type="submit" name="aksi" value="tolak" class="btn-action-soft btn-soft-danger" onclick="return confirm('Tolak usulan ini?')" title="Tolak">
                                                <iconify-icon icon="solar:close-circle-bold" class="fs-5"></iconify-icon>
                                            </button>
                                        </form>
                                    <?php } else { ?>
                                        <span class="badge-pill bg-success-subtle text-success border border-success-subtle">
                                            <iconify-icon icon="solar:check-read-bold"></iconify-icon> Anda Menyetujui
                                        </span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>