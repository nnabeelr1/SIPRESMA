<?php
session_start();
include '../config/koneksi.php';

// Cek Login Dosen
if ($_SESSION['role'] != 'dosen') {
    header("Location: ../index.php");
    exit();
}

// Ambil Data Dosen
$id_user = $_SESSION['id_user'];
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nidn, nama_lengkap FROM dosen WHERE id_user='$id_user'"));
$nidn = $dosen['nidn'];
$nama_dosen = $dosen['nama_lengkap'];
// Ambil nama depan
$nama_depan = explode(' ', trim($nama_dosen))[0];

// Ambil Semester Aktif
$smt = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Nilai - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            /* Palette Konsisten */
            --primary: #10b981;       /* Emerald */
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

        /* --- Navbar Clean (Sama Persis Dashboard Dosen) --- */
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

        /* --- Info Box (Layout Modern) --- */
        .info-box {
            background: white; border-radius: 16px; padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .info-item label { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 0.2rem; }
        .info-item div { font-size: 1.1rem; font-weight: 700; color: var(--text-main); }

        /* --- Card Styles --- */
        .card-modern {
            background: white; border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        .card-header-clean {
            padding: 1.5rem; border-bottom: 1px solid #f1f5f9; background: white;
            display: flex; align-items: center; gap: 10px;
        }
        .card-title { font-weight: 700; font-size: 1rem; color: var(--text-main); margin: 0; }

        /* --- Table Styling --- */
        .table-custom thead th {
            background-color: #f8fafc; color: var(--text-muted);
            font-weight: 700; font-size: 0.75rem; text-transform: uppercase;
            letter-spacing: 0.05em; padding: 1rem; border-bottom: 1px solid #e2e8f0; border-top: none;
        }
        .table-custom tbody td {
            padding: 1.2rem 1rem; vertical-align: middle;
            border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: var(--text-main);
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
            font-weight: 600; padding: 0.5rem 1.2rem; border-radius: 10px;
            display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
            text-decoration: none; font-size: 0.85rem;
        }
        .btn-primary-soft:hover { background: #d1fae5; color: #047857; transform: translateY(-2px); }

        /* --- Badges --- */
        .badge-pill {
            padding: 0.3em 0.7em; border-radius: 6px;
            font-weight: 600; font-size: 0.75rem;
        }
        .badge-gray { background: #f1f5f9; color: #475569; }
        .badge-blue { background: #e0f2fe; color: #0369a1; }
        .badge-purple { background: #f3e8ff; color: #7e22ce; }

        /* --- Empty State --- */
        .empty-state { padding: 4rem 1rem; text-align: center; color: var(--text-muted); }
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
                        LECTURER PORTAL
                    </small>
                </div>
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-block text-end" style="line-height: 1.2;">
                    <span class="fw-bold d-block text-dark" style="font-size: 0.9rem;">
                        <?php echo $nama_depan; ?>
                    </span>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Lecturer
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
                <h2 class="page-title">Input Nilai Mahasiswa</h2>
                <p class="page-subtitle">Kelola nilai ujian, tugas, dan evaluasi kelas Anda.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard/welcome_dosen.php" class="btn-clean">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Dashboard
                </a>
            </div>
        </div>

        <div class="info-box">
            <div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="logo-box" style="width: 48px; height: 48px; background: #f1f5f9; color: var(--text-muted);">
                        <iconify-icon icon="solar:user-id-bold-duotone" style="font-size: 1.5rem;"></iconify-icon>
                    </div>
                    <div>
                        <label>Dosen Pengampu</label>
                        <div><?php echo $nama_dosen; ?></div>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <label>Semester Aktif</label>
                <?php if($smt) { ?>
                    <span class="badge-pill badge-purple" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                        <?php echo $smt['nama_semester']; ?>
                    </span>
                <?php } else { ?>
                    <span class="text-danger fw-bold">Tidak Aktif</span>
                <?php } ?>
            </div>
        </div>

        <div class="card-modern">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="text-primary fs-5"></iconify-icon>
                    <h6 class="card-title">Daftar Kelas Perkuliahan Anda</h6>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="35%">Mata Kuliah</th>
                            <th width="10%" class="text-center">Kelas</th>
                            <th width="20%">Jadwal</th>
                            <th width="15%" class="text-center">Jumlah Mhs</th>
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
                            echo "<tr><td colspan='6' class='empty-state'>
                                <iconify-icon icon='solar:folder-with-files-linear' class='fs-1 mb-2'></iconify-icon><br>
                                Anda belum memiliki kelas perkuliahan
                            </td></tr>";
                        }

                        while($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td class="text-center fw-semibold text-muted"><?php echo $no++; ?></td>
                                <td>
                                    <span class="fw-bold text-dark d-block"><?php echo $row['nama_mk']; ?></span>
                                    <small class="text-muted"><?php echo $row['sks']; ?> SKS</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge-pill badge-gray"><?php echo $row['nama_kelas']; ?></span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;"><?php echo $row['hari']; ?></span>
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <?php echo date('H:i', strtotime($row['jam_mulai'])); ?> - <?php echo date('H:i', strtotime($row['jam_selesai'])); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-pill badge-blue"><?php echo $row['jumlah_mhs']; ?> Orang</span>
                                </td>
                                <td class="text-end">
                                    <a href="create.php?id_kelas=<?php echo $row['id_kelas']; ?>" class="btn-primary-soft">
                                        <iconify-icon icon="solar:pen-new-square-bold"></iconify-icon> Input Nilai
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if(mysqli_num_rows($query) > 0) { ?>
            <div class="mt-4 text-center text-muted small">
                Menampilkan <strong><?php echo mysqli_num_rows($query); ?></strong> kelas perkuliahan
            </div>
        <?php } ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>