<?php
session_start();
include '../config/koneksi.php';

// 1. Cek Login & Role
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// 2. Data Mahasiswa
$username = $_SESSION['username'];
$q_mhs = mysqli_query($koneksi, "
    SELECT m.*, u.username, p.nama_prodi 
    FROM mahasiswa m 
    JOIN user u ON m.id_user = u.id_user 
    JOIN prodi p ON m.id_prodi = p.id_prodi
    WHERE u.username='$username'
");
$data_mhs = mysqli_fetch_assoc($q_mhs);
$nim_saya = $data_mhs['nim'];
$nama_saya = $data_mhs['nama_lengkap'];
$id_prodi = $data_mhs['id_prodi'];
$nama_depan = explode(' ', trim($nama_saya))[0];

// 3. Cek Semester Aktif
$q_smt = mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'");
$smt_aktif = mysqli_fetch_assoc($q_smt);

if (!$smt_aktif) {
    echo "<script>alert('❌ Tidak ada semester aktif saat ini!'); window.location='../dashboard/welcome_mhs.php';</script>";
    exit();
}
$id_smt = $smt_aktif['id_semester'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRS Online - SIPRESMA</title>
    
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

        /* --- HEADER PAGE --- */
        .page-header {
            margin-top: 2rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; letter-spacing: -1px; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* --- INFO GLASS --- */
        .info-glass {
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .info-item label { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
        .info-item div { font-size: 1.1rem; font-weight: 700; color: var(--text-main); }

        /* --- CARD & TABLE --- */
        .card-modern {
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius-xl);
            padding: 0; 
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03);
            overflow: hidden; 
            height: 100%;
            display: flex; flex-direction: column;
        }
        
        .card-header-clean {
            padding: 1.2rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05);
            background: rgba(255,255,255,0.5); display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-weight: 700; font-size: 1rem; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px; }

        .table-responsive { flex-grow: 1; overflow-y: auto; max-height: 600px; }
        .table-responsive::-webkit-scrollbar { width: 6px; }
        .table-responsive::-webkit-scrollbar-track { background: transparent; }
        .table-responsive::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }

        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-custom thead th {
            position: sticky; top: 0; z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            color: var(--text-muted); font-size: 0.75rem; font-weight: 800; 
            text-transform: uppercase; letter-spacing: 0.1em;
            padding: 1rem 1.2rem; border-bottom: 1px solid #e2e8f0;
        }
        .table-custom tbody td { 
            padding: 1rem 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.03); 
            vertical-align: middle; font-size: 0.9rem; font-weight: 500;
        }
        .table-custom tr:hover { background-color: rgba(255,255,255,0.9); }

        /* --- BUTTONS --- */
        a { text-decoration: none !important; }

        .btn-glass-back {
            background: white; border: 1px solid #e2e8f0; color: var(--text-main);
            font-weight: 700; padding: 0.6rem 1.2rem; border-radius: 50px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03); font-size: 0.9rem;
        }
        .btn-glass-back:hover { transform: translateY(-2px); color: var(--primary); }

        .btn-glass-print {
            background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;
            font-weight: 700; padding: 0.6rem 1.2rem; border-radius: 50px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            font-size: 0.9rem;
        }
        .btn-glass-print:hover { background: #dbeafe; transform: translateY(-2px); }

        /* Action Buttons */
        .btn-action-add {
            background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;
            padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 0.8rem;
            display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; cursor: pointer;
        }
        .btn-action-add:hover { background: #15803d; color: white; transform: translateY(-2px); }

        .btn-action-del {
            width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
            background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; transition: 0.2s;
        }
        .btn-action-del:hover { background: #b91c1c; color: white; transform: translateY(-2px); }

        /* --- BADGES --- */
        .badge-pill { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .badge-gray { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .badge-smt { background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; }

        /* --- EMPTY STATE --- */
        .empty-state { padding: 4rem 2rem; text-align: center; }
        .empty-icon { font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="../dashboard/welcome_mhs.php">
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
        
        <div class="page-header animate-entry" style="animation-delay: 0.1s;">
            <div>
                <h2 class="page-title">Kartu Rencana Studi</h2>
                <p class="page-subtitle">Penyusunan mata kuliah semester ini.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard/welcome_mhs.php" class="btn-glass-back">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Dashboard
                </a>
                <a href="cetak.php" target="_blank" class="btn-glass-print">
                    <iconify-icon icon="solar:printer-bold-duotone"></iconify-icon> Cetak KRS
                </a>
            </div>
        </div>

        <div class="info-glass animate-entry" style="animation-delay: 0.2s;">
            <div class="d-flex gap-5">
                <div class="info-item">
                    <label>Nama Mahasiswa</label>
                    <div><?php echo $data_mhs['nama_lengkap']; ?></div>
                </div>
                <div class="info-item">
                    <label>NIM</label>
                    <div class="font-monospace"><?php echo $nim_saya; ?></div>
                </div>
                <div class="info-item">
                    <label>Program Studi</label>
                    <div><?php echo $data_mhs['nama_prodi']; ?></div>
                </div>
            </div>
            <div class="info-item text-end">
                <label>Semester Aktif</label>
                <div class="text-primary"><?php echo $smt_aktif['nama_semester']; ?></div>
            </div>
        </div>

        <div class="row g-4 animate-entry" style="animation-delay: 0.3s;">
            
            <div class="col-lg-7">
                <div class="card-modern">
                    <div class="card-header-clean">
                        <h6 class="card-title text-primary"><iconify-icon icon="solar:library-bold-duotone" class="fs-5"></iconify-icon> Mata Kuliah Tersedia</h6>
                        <small class="text-muted">Prodi: <?php echo $data_mhs['nama_prodi']; ?></small>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th width="45%">Mata Kuliah</th>
                                    <th width="10%" class="text-center">Smt</th>
                                    <th width="10%" class="text-center">Kls</th>
                                    <th width="20%">Jadwal</th>
                                    <th width="15%" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_tersedia = mysqli_query($koneksi, "
                                    SELECT k.*, m.nama_mk, m.sks, m.semester_paket
                                    FROM kelas k
                                    JOIN matakuliah m ON k.kode_mk = m.kode_mk
                                    WHERE m.id_prodi = '$id_prodi' 
                                    AND k.id_kelas NOT IN (SELECT id_kelas FROM krs WHERE nim='$nim_saya' AND id_semester='$id_smt')
                                    ORDER BY m.semester_paket ASC, m.nama_mk ASC
                                ");

                                if(mysqli_num_rows($query_tersedia) == 0) {
                                    echo "<tr><td colspan='5'>
                                        <div class='empty-state'>
                                            <iconify-icon icon='solar:box-minimalistic-linear' class='empty-icon'></iconify-icon>
                                            <p class='text-muted small mb-0'>Tidak ada mata kuliah tersedia.</p>
                                        </div>
                                    </td></tr>";
                                }

                                while($row = mysqli_fetch_assoc($query_tersedia)) {
                                ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark d-block"><?php echo $row['nama_mk']; ?></span>
                                            <small class="text-muted"><?php echo $row['sks']; ?> SKS</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-pill badge-smt"><?php echo $row['semester_paket']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-pill badge-gray"><?php echo $row['nama_kelas']; ?></span>
                                        </td>
                                        <td>
                                            <span class="d-block text-dark small fw-bold"><?php echo $row['hari']; ?></span>
                                            <small class="text-muted"><?php echo $row['jam_mulai']; ?></small>
                                        </td>
                                        <td class="text-end">
                                            <form action="create.php" method="POST" class="m-0">
                                                <input type="hidden" name="id_kelas" value="<?php echo $row['id_kelas']; ?>">
                                                <input type="hidden" name="id_semester" value="<?php echo $id_smt; ?>">
                                                <input type="hidden" name="nim" value="<?php echo $nim_saya; ?>">
                                                <button type="submit" name="ambil" class="btn-action-add border-0">
                                                    <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Ambil
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-modern">
                    <div class="card-header-clean bg-light">
                        <h6 class="card-title text-success"><iconify-icon icon="solar:cart-large-2-bold-duotone" class="fs-5"></iconify-icon> KRS Saya</h6>
                        <small class="text-muted">Semester ini</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th width="50%">Mata Kuliah</th>
                                    <th width="15%" class="text-center">Kls</th>
                                    <th width="15%" class="text-center">SKS</th>
                                    <th width="20%" class="text-end">Batal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_sks = 0;
                                $query_krs = mysqli_query($koneksi, "
                                    SELECT krs.id_krs, m.nama_mk, m.sks, k.nama_kelas
                                    FROM krs
                                    JOIN kelas k ON krs.id_kelas = k.id_kelas
                                    JOIN matakuliah m ON k.kode_mk = m.kode_mk
                                    WHERE krs.nim='$nim_saya' AND krs.id_semester='$id_smt'
                                ");

                                if(mysqli_num_rows($query_krs) == 0) {
                                    echo "<tr><td colspan='4'>
                                        <div class='empty-state py-5'>
                                            <iconify-icon icon='solar:notebook-linear' class='empty-icon fs-1'></iconify-icon>
                                            <p class='text-muted small mb-0'>Belum mengambil mata kuliah.</p>
                                        </div>
                                    </td></tr>";
                                }

                                while($krs = mysqli_fetch_assoc($query_krs)) {
                                    $total_sks += $krs['sks'];
                                ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark d-block"><?php echo $krs['nama_mk']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-pill badge-gray"><?php echo $krs['nama_kelas']; ?></span>
                                        </td>
                                        <td class="text-center fw-bold text-dark">
                                            <?php echo $krs['sks']; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="delete.php?id=<?php echo $krs['id_krs']; ?>" 
                                               class="btn-action-del ms-auto"
                                               onclick="return confirm('Batalkan mata kuliah ini?')">
                                                <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            
                            <?php if(mysqli_num_rows($query_krs) > 0) { ?>
                            <tfoot>
                                <tr style="background-color: #ecfdf5;">
                                    <td colspan="2" class="text-end fw-bold text-success text-uppercase small">Total SKS Diambil</td>
                                    <td class="text-center fw-bold text-success fs-6"><?php echo $total_sks; ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>