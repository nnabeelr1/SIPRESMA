<?php
session_start();
include '../config/koneksi.php';

// 1. Cek Login & Role Mahasiswa
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// 2. Ambil Data Mahasiswa yang Login (Termasuk ID PRODI)
$username = $_SESSION['username'];
$q_mhs = mysqli_query($koneksi, "
    SELECT m.*, u.username 
    FROM mahasiswa m 
    JOIN user u ON m.id_user = u.id_user 
    WHERE u.username='$username'
");
$data_mhs = mysqli_fetch_assoc($q_mhs);
$nim_saya = $data_mhs['nim'];
$nama_saya = $data_mhs['nama_lengkap'];
$id_prodi_saya = $data_mhs['id_prodi'];

// Ambil nama depan untuk navbar
$nama_depan = explode(' ', trim($nama_saya))[0];

// 3. Cek Semester Aktif
$q_smt = mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'");
$smt_aktif = mysqli_fetch_assoc($q_smt);

if (!$smt_aktif) {
    echo "<script>alert('Tidak ada semester aktif!'); window.location='../dashboard/welcome_mhs.php';</script>";
    exit();
}
$id_smt_aktif = $smt_aktif['id_semester'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRS Online - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            /* Palette Konsisten */
            --primary: #10b981;       
            --bg-body: #f8fafc;       
            --text-main: #1e293b;     
            --text-muted: #64748b;    
            --card-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 3rem;
        }

        /* --- Navbar Clean (Sama Persis Dashboard MHS) --- */
        .navbar-clean {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 0.8rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo-box {
            background: rgba(16, 185, 129, 0.1); 
            color: #10b981;
            width: 42px; height: 42px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        /* --- Page Header --- */
        .page-header {
            margin-top: 2rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* --- Card Styles --- */
        .card-modern {
            background: white; border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            height: 100%;
        }
        .card-header-clean {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            background: white;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-weight: 700; font-size: 1rem; color: var(--text-main); margin: 0; }

        /* --- Info Box Mahasiswa --- */
        .info-box {
            background: white; border-radius: 16px; padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .info-item label { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-item div { font-size: 1rem; font-weight: 700; color: var(--text-main); }

        /* --- Table Styling --- */
        .table-custom thead th {
            background-color: #f8fafc; color: var(--text-muted);
            font-weight: 700; font-size: 0.75rem; text-transform: uppercase;
            letter-spacing: 0.05em; padding: 1rem; border-bottom: 1px solid #e2e8f0; border-top: none;
        }
        .table-custom tbody td {
            padding: 1rem; vertical-align: middle;
            border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;
        }
        
        /* --- Buttons --- */
        .btn-clean {
            background: white; border: 1px solid #e2e8f0; color: var(--text-main);
            font-weight: 600; padding: 0.6rem 1.2rem; border-radius: 12px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            text-decoration: none;
        }
        .btn-clean:hover { background: #f8fafc; border-color: #cbd5e1; }

        .btn-primary-soft {
            background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;
            font-weight: 600; padding: 0.4rem 1rem; border-radius: 10px;
            display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
            font-size: 0.85rem;
        }
        .btn-primary-soft:hover { background: #d1fae5; color: #047857; }

        .btn-delete-icon {
            width: 30px; height: 30px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #ef4444; background: #fef2f2; border: 1px solid #fecaca;
            transition: all 0.2s;
        }
        .btn-delete-icon:hover { background: #fee2e2; transform: scale(1.1); }

        /* --- Badges --- */
        .badge-pill {
            padding: 0.3em 0.7em; border-radius: 6px;
            font-weight: 600; font-size: 0.75rem;
        }
        .badge-gray { background: #f1f5f9; color: #475569; }
        .badge-purple { background: #f3e8ff; color: #7e22ce; }

        /* --- Empty State --- */
        .empty-state { padding: 3rem 1rem; text-align: center; color: var(--text-muted); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean">
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
                <div class="bg-light rounded-circle border p-1 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <iconify-icon icon="solar:user-circle-bold" class="text-secondary" style="font-size: 1.8rem;"></iconify-icon>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <div class="page-header">
            <div>
                <h2 class="page-title">Kartu Rencana Studi</h2>
                <p class="page-subtitle">Pilih mata kuliah yang akan diambil semester ini.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard/welcome_mhs.php" class="btn-clean">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Dashboard
                </a>
                <a href="cetak.php" target="_blank" class="btn-clean text-primary">
                    <iconify-icon icon="solar:printer-bold"></iconify-icon> Cetak KRS
                </a>
            </div>
        </div>

        <div class="info-box">
            <div class="d-flex gap-5">
                <div class="info-item">
                    <label>Mahasiswa</label>
                    <div><?php echo $data_mhs['nama_lengkap']; ?></div>
                </div>
                <div class="info-item">
                    <label>NIM</label>
                    <div><?php echo $nim_saya; ?></div>
                </div>
            </div>
            <div class="info-item text-end">
                <label>Semester Aktif</label>
                <div class="text-primary"><?php echo $smt_aktif['nama_semester']; ?></div>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-lg-7">
                <div class="card-modern">
                    <div class="card-header-clean">
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:library-bold-duotone" class="text-primary fs-5"></iconify-icon>
                            <h6 class="card-title">Mata Kuliah Tersedia</h6>
                        </div>
                    </div>
                    
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-custom mb-0">
                            <thead class="sticky-top bg-white" style="z-index: 5;">
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
                                    SELECT k.*, m.nama_mk, m.sks, m.semester_paket, m.id_prodi
                                    FROM kelas k
                                    JOIN matakuliah m ON k.kode_mk = m.kode_mk
                                    WHERE 
                                    m.id_prodi = '$id_prodi_saya' 
                                    AND
                                    k.id_kelas NOT IN (
                                        SELECT id_kelas FROM krs WHERE nim='$nim_saya' AND id_semester='$id_smt_aktif'
                                    )
                                    ORDER BY m.semester_paket ASC, m.nama_mk ASC
                                ");

                                if(mysqli_num_rows($query_tersedia) == 0) {
                                    echo "<tr><td colspan='5' class='empty-state'>
                                        <iconify-icon icon='solar:box-minimalistic-linear' class='fs-1 mb-2'></iconify-icon><br>
                                        Tidak ada mata kuliah tersedia
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
                                            <span class="badge-pill badge-purple"><?php echo $row['semester_paket']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-pill badge-gray"><?php echo $row['nama_kelas']; ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted fw-semibold d-block"><?php echo $row['hari']; ?></small>
                                            <small class="text-muted"><?php echo $row['jam_mulai']; ?></small>
                                        </td>
                                        <td class="text-end">
                                            <form action="create.php" method="POST" class="m-0">
                                                <input type="hidden" name="id_kelas" value="<?php echo $row['id_kelas']; ?>">
                                                <input type="hidden" name="id_semester" value="<?php echo $id_smt_aktif; ?>">
                                                <input type="hidden" name="nim" value="<?php echo $nim_saya; ?>">
                                                <button type="submit" name="ambil" class="btn-primary-soft border-0">
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
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:cart-large-2-bold-duotone" class="text-success fs-5"></iconify-icon>
                            <h6 class="card-title">KRS Saya</h6>
                        </div>
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
                                    WHERE krs.nim='$nim_saya' AND krs.id_semester='$id_smt_aktif'
                                ");

                                if(mysqli_num_rows($query_krs) == 0) {
                                    echo "<tr><td colspan='4' class='empty-state'>
                                        Belum mengambil mata kuliah
                                    </td></tr>";
                                }

                                while($krs = mysqli_fetch_assoc($query_krs)) {
                                    $total_sks += $krs['sks'];
                                ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark"><?php echo $krs['nama_mk']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-pill badge-gray"><?php echo $krs['nama_kelas']; ?></span>
                                        </td>
                                        <td class="text-center fw-bold text-dark">
                                            <?php echo $krs['sks']; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="delete.php?id=<?php echo $krs['id_krs']; ?>" 
                                               class="btn-delete-icon ms-auto"
                                               onclick="return confirm('Batalkan mata kuliah ini?')">
                                                <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            
                            <?php if(mysqli_num_rows($query_krs) > 0) { ?>
                            <tfoot>
                                <tr style="background-color: #ecfdf5;">
                                    <td colspan="2" class="text-end fw-bold text-success">Total SKS Diambil:</td>
                                    <td class="text-center fw-bold text-success" style="font-size: 1.1rem;"><?php echo $total_sks; ?></td>
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