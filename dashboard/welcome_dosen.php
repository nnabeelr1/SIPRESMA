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

// Logic Peer Support (TETAP ADA)
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

        $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM peer_support WHERE id_match='$id_match'"));
        
        if ($cek['acc_doswal_mentee'] == 1 && $cek['acc_doswal_mentor'] == 1) {
            mysqli_query($koneksi, "UPDATE peer_support SET status='aktif' WHERE id_match='$id_match'");
            echo "<script>alert('✅ Program Aktif!'); window.location='welcome_dosen.php';</script>";
        } else {
            echo "<script>alert('✅ Disetujui. Menunggu partner.'); window.location='welcome_dosen.php';</script>";
        }

    } else {
        mysqli_query($koneksi, "UPDATE peer_support SET status='ditolak' WHERE id_match='$id_match'");
        echo "<script>alert('❌ Ditolak.'); window.location='welcome_dosen.php';</script>";
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            --primary: #10b981; --bg-body: #f8fafc; --text-main: #1e293b; --text-muted: #64748b;
            --card-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); padding-bottom: 3rem; }

        /* Navbar */
        .navbar-clean { background: white; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 0.8rem 0; position: sticky; top: 0; z-index: 100; }
        .logo-box { background: rgba(16, 185, 129, 0.1); color: #10b981; width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        
        /* Welcome Card */
        .welcome-card { background: white; border-radius: 20px; padding: 2.5rem; text-align: center; box-shadow: var(--card-shadow); margin-bottom: 2rem; border: 1px solid rgba(0,0,0,0.03); }

        /* --- MENU GRID SYSTEM (BARU) --- */
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
        
        .menu-card {
            background: white; border-radius: 16px; padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.03); box-shadow: var(--card-shadow);
            display: flex; align-items: center; justify-content: space-between;
            text-decoration: none; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .menu-card:hover { transform: translateY(-5px); border-color: var(--primary); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        
        .menu-icon-box {
            width: 56px; height: 56px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-right: 1rem;
        }
        .bg-icon-blue { background: #eff6ff; color: #3b82f6; }
        .bg-icon-orange { background: #fff7ed; color: #f97316; }

        /* Table Section */
        .section-title { font-weight: 800; font-size: 1.1rem; color: var(--text-main); margin-bottom: 1rem; }
        .card-modern { background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.03); box-shadow: var(--card-shadow); overflow: hidden; }
        .card-header-clean { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; background: white; display: flex; align-items: center; gap: 10px; }
        
        /* Table Style */
        .table-custom thead th { background-color: #f8fafc; color: var(--text-muted); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
        .table-custom tbody td { padding: 1.25rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: var(--text-main); }
        
        /* Components */
        .step-indicator { display: flex; align-items: center; gap: 0.6rem; font-size: 0.8rem; margin-bottom: 0.4rem; }
        .step-dot { width: 8px; height: 8px; border-radius: 50%; background: #e2e8f0; }
        .step-active { background: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2); }
        .step-waiting { background: #fbbf24; }
        
        .badge-pill { padding: 0.3em 0.7em; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
        .badge-blue-soft { background: #e0f2fe; color: #0369a1; }
        
        .btn-action { padding: 0.4rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; border: none; transition: 0.2s; }
        .btn-approve { background: #10b981; color: white; }
        .btn-approve:hover { background: #059669; }
        .btn-reject { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
        .btn-reject:hover { background: #fee2e2; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="#">
                <div class="logo-box"><iconify-icon icon="solar:infinity-bold" style="font-size: 1.5rem;"></iconify-icon></div>
                <div><h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">SIPRESMA</h5><small class="text-muted fw-bold" style="font-size: 0.65rem;">LECTURER PORTAL</small></div>
            </a>
            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-block text-end"><span class="fw-bold d-block text-dark"><?php echo $nama_depan; ?></span><small class="text-muted">Lecturer</small></div>
                <div class="bg-light rounded-circle border p-1 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><iconify-icon icon="solar:user-circle-bold" class="text-secondary fs-4"></iconify-icon></div>
                <a href="../logout.php" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-dark shadow-sm border ms-2">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <div class="welcome-card">
            <h2 class="fw-bold mb-2">Selamat Datang, <?php echo $data_dosen['nama_lengkap']; ?>!</h2>
            <p class="text-muted mb-0">Portal Manajemen Akademik & Bimbingan Mahasiswa</p>
        </div>

        <div class="menu-grid">
            
            <a href="../nilai/index.php" class="menu-card">
                <div class="d-flex align-items-center">
                    <div class="menu-icon-box bg-icon-blue">
                        <iconify-icon icon="solar:notebook-bold-duotone"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Input Nilai</h6>
                        <small class="text-muted">Kelola nilai perkuliahan</small>
                    </div>
                </div>
                <iconify-icon icon="solar:alt-arrow-right-linear" class="fs-5 text-muted"></iconify-icon>
            </a>

            <a href="../prestasi/index.php" class="menu-card">
                <div class="d-flex align-items-center">
                    <div class="menu-icon-box bg-icon-orange">
                        <iconify-icon icon="solar:medal-star-bold-duotone"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Validasi Prestasi</h6>
                        <small class="text-muted">Cek & Acc Sertifikat</small>
                    </div>
                </div>
                <iconify-icon icon="solar:alt-arrow-right-linear" class="fs-5 text-muted"></iconify-icon>
            </a>

        </div>

        <h5 class="section-title">Persetujuan Peer Support</h5>
        
        <div class="card-modern">
            <div class="card-header-clean">
                <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="text-primary fs-5"></iconify-icon>
                <h6 class="fw-bold mb-0 text-dark">Menunggu Konfirmasi</h6>
            </div>
            
            <div class="p-4">
                <div class="alert alert-warning d-flex align-items-center gap-2 mb-4" style="background: #fffbeb; border-color: #fef3c7; color: #b45309; border-radius: 12px;">
                    <iconify-icon icon="solar:info-circle-bold" class="fs-5"></iconify-icon>
                    <div style="font-size: 0.9rem;"><strong>Penting:</strong> Program aktif jika <strong>Kedua Dosen Wali</strong> menyetujui.</div>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Pasangan Mahasiswa</th>
                                <th style="width: 20%;">Peran Anda</th>
                                <th style="width: 25%;">Status</th>
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
                                echo "<tr><td colspan='4' class='text-center py-5 text-muted'>Tidak ada usulan.</td></tr>";
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
                                        <div class="mb-2"><small class="text-danger fw-bold d-block" style="font-size: 0.65rem;">MENTEE</small><span class="fw-bold text-dark"><?php echo $row['nama_mentee']; ?></span></div>
                                        <div><small class="text-success fw-bold d-block" style="font-size: 0.65rem;">MENTOR</small><span class="fw-bold text-dark"><?php echo $row['nama_mentor']; ?></span></div>
                                    </td>
                                    <td><span class="badge-pill badge-blue-soft"><iconify-icon icon="solar:user-id-bold"></iconify-icon> <?php echo $label_peran; ?></span></td>
                                    <td>
                                        <div class="step-indicator"><div class="step-dot <?php echo ($row['acc_doswal_mentee'])?'step-active':'step-waiting';?>"></div><span>Wali Mentee</span></div>
                                        <div class="step-indicator"><div class="step-dot <?php echo ($row['acc_doswal_mentor'])?'step-active':'step-waiting';?>"></div><span>Wali Mentor</span></div>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($sudah_acc == 0) { ?>
                                            <form method="POST" class="d-flex gap-2 justify-content-end">
                                                <input type="hidden" name="id_match" value="<?php echo $row['id_match']; ?>">
                                                <input type="hidden" name="peran_saya" value="<?php echo $peran_saya; ?>">
                                                <button type="submit" name="aksi" value="tolak" class="btn-action btn-reject" onclick="return confirm('Tolak?')">Tolak</button>
                                                <button type="submit" name="aksi" value="terima" class="btn-action btn-approve" onclick="return confirm('Setujui?')">Setujui</button>
                                            </form>
                                        <?php } else { ?>
                                            <span class="text-success fw-bold small"><iconify-icon icon="solar:check-circle-bold"></iconify-icon> Disetujui</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</body>
</html>