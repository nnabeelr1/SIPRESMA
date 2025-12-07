<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['status'])) { header("Location: ../index.php"); exit(); }

$role = $_SESSION['role'];
$username = $_SESSION['username'];
$nim_saya = "";
$nidn_saya = ""; 

// Identifikasi User
if ($role == 'mahasiswa') {
    $q = mysqli_query($koneksi, "SELECT nim, nama_lengkap FROM mahasiswa JOIN user ON mahasiswa.id_user = user.id_user WHERE user.username='$username'");
    $d = mysqli_fetch_assoc($q);
    $nim_saya = $d['nim'];
    $nama_user = explode(' ', trim($d['nama_lengkap']))[0];
    $role_label = "Mahasiswa";
} elseif ($role == 'dosen') {
    $q = mysqli_query($koneksi, "SELECT nidn, nama_lengkap FROM dosen JOIN user ON dosen.id_user = user.id_user WHERE user.username='$username'");
    $d = mysqli_fetch_assoc($q);
    $nidn_saya = $d['nidn']; 
    $nama_user = explode(' ', trim($d['nama_lengkap']))[0];
    $role_label = "Dosen Wali";
} else {
    $nama_user = "Administrator";
    $role_label = "System Admin";
}

// LOGIC VALIDASI (DOSEN)
if (isset($_POST['validasi']) && $role == 'dosen') {
    $id_prestasi = $_POST['id_prestasi'];
    $status_baru = $_POST['status_baru'];
    $update = mysqli_query($koneksi, "UPDATE prestasi SET status_validasi='$status_baru' WHERE id_prestasi='$id_prestasi'");
    if($update) { header("Location: index.php"); exit(); }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Prestasi - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    
    <style>
        :root {
            --primary: #10b981;
            --secondary: #64748b;
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #94a3b8;
            /* Status Colors */
            --soft-green: #d1fae5; --text-green: #065f46;
            --soft-red: #fee2e2;   --text-red: #991b1b;
            --soft-blue: #e0f2fe;  --text-blue: #075985;
            --soft-amber: #fef3c7; --text-amber: #92400e;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
            padding-bottom: 3rem;
        }

        /* Navbar Clean (SAMA PERSIS index.php) */
        .navbar-clean {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .brand-text { color: var(--text-main); letter-spacing: -0.5px; }

        /* Card Modern */
        .card-modern {
            background: var(--card-bg);
            border: 1px solid rgba(0,0,0,0.03);
            border-radius: 16px;
            box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        /* Table Modern */
        .table-modern thead th {
            background-color: #f1f5f9; color: var(--secondary); font-weight: 700;
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem; border: none;
        }
        .table-modern tbody td { padding: 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 0.9rem; }

        /* Badge & Buttons */
        .badge-pill { padding: 0.35em 0.8em; border-radius: 50rem; font-weight: 600; font-size: 0.75rem; }
        .btn-emerald { background: var(--primary); color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 50rem; font-weight: 600; font-size: 0.9rem; transition: 0.2s; }
        .btn-emerald:hover { background: #059669; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
        .btn-back { border: 1px solid #cbd5e1; background: white; color: var(--text-main); padding: 0.6rem 1.2rem; border-radius: 50rem; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-back:hover { background: #f1f5f9; }

        /* Background Decoration */
        .bg-decoration { position: fixed; width: 400px; height: 400px; background: var(--primary); filter: blur(120px); opacity: 0.05; z-index: -1; border-radius: 50%; }
        .blob-1 { top: -100px; right: -100px; }
        .blob-2 { bottom: -100px; left: -100px; }
        
        /* Status Badges */
        .status-wait { background: var(--soft-amber); color: var(--text-amber); }
        .status-valid { background: var(--soft-green); color: var(--text-green); }
        .status-reject { background: var(--soft-red); color: var(--text-red); }

        .btn-icon-soft { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; text-decoration: none; transition: 0.2s; }
    </style>
</head>
<body>

    <div class="bg-decoration blob-1"></div>
    <div class="bg-decoration blob-2"></div>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
                <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success d-flex">
                    <iconify-icon icon="solar:infinity-bold" width="24"></iconify-icon>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 brand-text">SIPRESMA</h5>
                    <p class="mb-0 text-muted" style="font-size: 10px; font-weight: 600; letter-spacing: 1px;">ACADEMIC DASHBOARD</p>
                </div>
            </a>
            
            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-block text-end">
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><?php echo $nama_user; ?></p>
                    <p class="mb-0 text-muted" style="font-size: 0.75rem;"><?php echo $role_label; ?></p>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-light rounded-circle border p-1">
                             <iconify-icon icon="solar:user-circle-bold" width="36" class="text-secondary"></iconify-icon>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5 pb-5">

        <div class="row mb-4 align-items-end">
            <div class="col-md-8">
                <h2 class="fw-bold mb-1">Validasi Prestasi</h2>
                <p class="text-muted mb-0">Kelola dan pantau pencapaian non-akademik.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
                <?php $back = ($role == 'mahasiswa') ? '../dashboard/welcome_mhs.php' : '../index.php'; ?>
                <a href="<?php echo $back; ?>" class="btn-back">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Dashboard
                </a>
                <?php if($role == 'mahasiswa') { ?>
                    <a href="create.php" class="btn-emerald">
                        <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> Tambah Baru
                    </a>
                <?php } ?>
            </div>
        </div>

        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-modern table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="20%">Mahasiswa</th>
                            <th width="25%">Kegiatan</th>
                            <th width="15%">Pencapaian</th>
                            <th width="10%">Bukti</th>
                            <th class="text-center" width="10%">Status</th>
                            <th class="text-end" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($role == 'mahasiswa') {
                            $query = mysqli_query($koneksi, "SELECT * FROM prestasi WHERE nim = '$nim_saya' ORDER BY id_prestasi DESC");
                        } elseif ($role == 'dosen') {
                            $query = mysqli_query($koneksi, "SELECT p.*, m.nama_lengkap, m.nim FROM prestasi p JOIN mahasiswa m ON p.nim = m.nim WHERE m.dosen_wali = '$nidn_saya' ORDER BY p.id_prestasi DESC");
                        } else {
                            $query = mysqli_query($koneksi, "SELECT p.*, m.nama_lengkap, m.nim FROM prestasi p JOIN mahasiswa m ON p.nim = m.nim ORDER BY p.id_prestasi DESC");
                        }

                        $no = 1;
                        if(mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>Belum ada data prestasi.</td></tr>";
                        }

                        while ($row = mysqli_fetch_assoc($query)) {
                            $st = $row['status_validasi'];
                            $badge = ($st == 'valid') ? 'status-valid' : (($st == 'ditolak') ? 'status-reject' : 'status-wait');
                            $icon  = ($st == 'valid') ? 'check-circle-bold' : (($st == 'ditolak') ? 'close-circle-bold' : 'hourglass-line-bold');
                            $mhs_nama = ($role == 'mahasiswa') ? 'Saya sendiri' : $row['nama_lengkap'];
                            $mhs_nim = ($role == 'mahasiswa') ? $nim_saya : $row['nim'];
                        ?>
                            <tr>
                                <td class="text-center fw-semibold text-muted"><?php echo $no++; ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $mhs_nama; ?></div>
                                    <small class="text-muted"><?php echo $mhs_nim; ?></small>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?php echo $row['nama_kegiatan']; ?></div>
                                    <small class="text-muted"><?php echo $row['tahun']; ?></small>
                                </td>
                                <td><span class="badge badge-pill bg-light text-secondary border"><?php echo $row['jenis_juara']; ?></span></td>
                                <td>
                                    <?php if($row['file_bukti']) { ?>
                                        <a href="../uploads/<?php echo $row['file_bukti']; ?>" target="_blank" class="btn btn-sm btn-light border fw-semibold text-primary" style="font-size: 0.7rem;">File</a>
                                    <?php } else { echo "-"; } ?>
                                </td>
                                <td class="text-center"><span class="badge badge-pill <?php echo $badge; ?> d-inline-flex align-items-center gap-1"><iconify-icon icon="solar:<?php echo $icon; ?>"></iconify-icon> <?php echo ucfirst($st); ?></span></td>
                                <td class="text-end">
                                    
                                    <?php if($role == 'dosen') { ?>
                                        <form method="POST" class="d-flex gap-2 justify-content-end">
                                            <input type="hidden" name="id_prestasi" value="<?php echo $row['id_prestasi']; ?>">
                                            <input type="hidden" name="validasi" value="go">
                                            <button type="submit" name="status_baru" value="valid" class="btn-icon-soft bg-success bg-opacity-10 text-success" title="Validasi"><iconify-icon icon="solar:check-read-bold"></iconify-icon></button>
                                            <button type="submit" name="status_baru" value="ditolak" class="btn-icon-soft bg-danger bg-opacity-10 text-danger" title="Tolak"><iconify-icon icon="solar:close-circle-bold"></iconify-icon></button>
                                        </form>
                                    <?php } elseif ($role == 'mahasiswa' && $st == 'menunggu') { ?>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="update.php?id=<?php echo $row['id_prestasi']; ?>" class="btn-icon-soft bg-warning bg-opacity-10 text-warning" title="Edit"><iconify-icon icon="solar:pen-bold"></iconify-icon></a>
                                            <a href="delete.php?id=<?php echo $row['id_prestasi']; ?>" class="btn-icon-soft bg-danger bg-opacity-10 text-danger" onclick="return confirm('Batalkan pengajuan ini?')" title="Hapus"><iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon></a>
                                        </div>
                                    <?php } else { ?>
                                        <small class="text-muted fst-italic">No action</small>
                                    <?php } ?>

                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>