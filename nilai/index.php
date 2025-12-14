<?php
session_start();
include '../config/koneksi.php';

// Cek Login Dosen
if ($_SESSION['role'] != 'dosen') {
    header("Location: ../index.php");
    exit();
}

// Data Dosen
$id_user = $_SESSION['id_user'];
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nidn, nama_lengkap FROM dosen WHERE id_user='$id_user'"));
$nidn = $dosen['nidn'];
$nama_depan = explode(' ', trim($dosen['nama_lengkap']))[0];

// Semester Aktif
$smt = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Nilai - SIPRESMA</title>
    
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
        html { overflow-y: scroll; } 
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            
            /* BACKGROUND: Mint Green + Dot Matrix */
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

        /* --- NAVBAR (GLASS - LECTURER STYLE) --- */
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

        /* --- HEADER PAGE --- */
        .page-header {
            margin-top: 2rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; letter-spacing: -1px; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* --- INFO BOX GLASS --- */
        .info-glass {
            background: rgba(255, 255, 255, 0.6); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 16px; padding: 1.2rem; margin-bottom: 2rem;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .info-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-value { font-size: 1.1rem; font-weight: 700; color: var(--text-main); }

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

        .table-wrapper {
            max-height: 550px;
            overflow-y: auto; overflow-x: hidden;
        }
        .table-wrapper::-webkit-scrollbar { width: 6px; }
        .table-wrapper::-webkit-scrollbar-track { background: transparent; }
        .table-wrapper::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }

        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; table-layout: fixed; }

        .table-custom thead th {
            position: sticky; top: 0; z-index: 20;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 0 rgba(0,0,0,0.05);
            color: var(--text-muted); font-size: 0.75rem; font-weight: 800; 
            text-transform: uppercase; letter-spacing: 0.1em;
            padding: 1.5rem 1.2rem; border-bottom: 1px solid #e2e8f0;
        }

        .table-custom tbody td { 
            padding: 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.03); 
            vertical-align: middle; font-size: 0.95rem; font-weight: 500;
            background: transparent; word-wrap: break-word;
        }
        .table-custom tr:last-child td { border-bottom: none; }
        .table-custom tbody tr:hover { background-color: rgba(255,255,255,0.9); }

        /* --- BUTTONS & BADGES --- */
        a { text-decoration: none !important; }

        .btn-glass-back {
            background: white; border: 1px solid #e2e8f0; color: var(--text-main);
            font-weight: 700; padding: 0.7rem 1.4rem; border-radius: 50px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }
        .btn-glass-back:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); color: var(--primary); }

        .btn-action-soft {
            background: #e0f2fe; color: #0369a1; border: none;
            padding: 0.6rem 1.2rem; border-radius: 10px; font-weight: 700; font-size: 0.85rem;
            display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;
        }
        .btn-action-soft:hover { background: #0369a1; color: white; transform: translateY(-2px); }

        .badge-pill { padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
        .badge-gray { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .badge-blue { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-purple { background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; }

        /* --- EMPTY STATE --- */
        .empty-state { padding: 5rem 2rem; text-align: center; }
        .empty-icon { font-size: 3.5rem; color: #cbd5e1; margin-bottom: 1rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="../dashboard/welcome_dosen.php">
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
                        <iconify-icon icon="solar:record-circle-bold" style="font-size: 8px;"></iconify-icon> Lecturer
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
        
        <div class="page-header animate-entry" style="animation-delay: 0.1s;">
            <div>
                <h2 class="page-title">Input Nilai Mahasiswa</h2>
                <p class="page-subtitle">Pilih kelas perkuliahan untuk mengelola nilai.</p>
            </div>
            <div>
                <a href="../dashboard/welcome_dosen.php" class="btn-glass-back">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Dashboard
                </a>
            </div>
        </div>

        <div class="info-glass animate-entry" style="animation-delay: 0.2s;">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-box" style="background: #f1f5f9; color: var(--text-muted);">
                    <iconify-icon icon="solar:user-id-bold-duotone" style="font-size: 1.5rem;"></iconify-icon>
                </div>
                <div>
                    <div class="info-label">Dosen Pengampu</div>
                    <div class="info-value"><?php echo $dosen['nama_lengkap']; ?></div>
                </div>
            </div>
            <div class="text-end">
                <div class="info-label">Semester Aktif</div>
                <?php if($smt) { ?>
                    <span class="badge-pill badge-purple">
                        <iconify-icon icon="solar:calendar-bold" class="me-1"></iconify-icon>
                        <?php echo $smt['nama_semester']; ?>
                    </span>
                <?php } else { ?>
                    <span class="badge-pill bg-danger-subtle text-danger">Tidak Aktif</span>
                <?php } ?>
            </div>
        </div>

        <div class="card-modern animate-entry" style="animation-delay: 0.3s;">
            <div class="table-wrapper">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="35%">Mata Kuliah</th>
                            <th width="15%" class="text-center">Kelas</th>
                            <th width="20%">Jadwal</th>
                            <th width="10%" class="text-center">Mahasiswa</th>
                            <th width="15%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "
                            SELECT k.*, m.nama_mk, m.sks,
                            (SELECT COUNT(*) FROM krs WHERE id_kelas = k.id_kelas) as jumlah_mhs
                            FROM kelas k
                            JOIN matakuliah m ON k.kode_mk = m.kode_mk
                            WHERE k.nidn = '$nidn'
                        ");

                        $no = 1;
                        if(mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='6'>
                                <div class='empty-state'>
                                    <iconify-icon icon='solar:notebook-minimalistic-linear' class='empty-icon'></iconify-icon>
                                    <h6 class='fw-bold text-dark'>Belum Ada Kelas</h6>
                                    <p class='text-muted small'>Anda belum memiliki jadwal mengajar semester ini.</p>
                                </div>
                            </td></tr>";
                        }

                        while($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?php echo $no++; ?></td>
                                
                                <td>
                                    <span class="fw-bold text-dark d-block"><?php echo $row['nama_mk']; ?></span>
                                    <small class="text-muted"><?php echo $row['sks']; ?> SKS</small>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge-pill badge-gray"><?php echo $row['nama_kelas']; ?></span>
                                </td>
                                
                                <td>
                                    <span class="fw-bold text-dark small d-block"><?php echo $row['hari']; ?></span>
                                    <span class="text-muted small">
                                        <iconify-icon icon="solar:clock-circle-linear" style="position: relative; top: 1px;"></iconify-icon>
                                        <?php echo date('H:i', strtotime($row['jam_mulai'])) . ' - ' . date('H:i', strtotime($row['jam_selesai'])); ?>
                                    </span>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge-pill badge-blue"><?php echo $row['jumlah_mhs']; ?> Org</span>
                                </td>
                                
                                <td class="text-end">
                                    <a href="create.php?id_kelas=<?php echo $row['id_kelas']; ?>" class="btn-action-soft">
                                        <iconify-icon icon="solar:pen-new-square-bold"></iconify-icon> Kelola Nilai
                                    </a>
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