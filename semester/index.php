<?php
session_start();
// Security Check
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') { 
    header("Location: ../index.php"); 
    exit(); 
}
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Semester - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            --primary: #10b981; --bg-body: #f8fafc; --text-main: #1e293b; --text-muted: #64748b;
            --card-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); padding-bottom: 3rem; }

        /* Navbar Clean (Sama Persis Dashboard) */
        .navbar-clean { background: white; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 0.8rem 0; position: sticky; top: 0; z-index: 100; }
        .logo-box { background: rgba(16, 185, 129, 0.1); color: #10b981; width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .brand-text { color: var(--text-main); letter-spacing: -0.5px; }

        /* Header */
        .page-header { margin-top: 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: end; }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* Card Modern */
        .card-modern { background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.03); box-shadow: var(--card-shadow); overflow: hidden; padding: 1.5rem; }
        .card-title-small { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main); display: flex; align-items: center; gap: 8px; }

        /* Form */
        .form-label { font-weight: 700; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.4rem; text-transform: uppercase; }
        .form-control { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.7rem 1rem; color: var(--text-main); }
        .form-control:focus { border-color: var(--primary); outline: none; background: white; }

        /* Table */
        .table-custom thead th { background-color: #f8fafc; color: var(--text-muted); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
        .table-custom tbody td { padding: 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: var(--text-main); }
        
        /* Active Row Highlight */
        .row-active { background-color: #ecfdf5 !important; }
        .row-active td { border-bottom: 1px solid #d1fae5 !important; }

        /* Badges */
        .badge-pill { padding: 0.35em 0.8em; border-radius: 6px; font-weight: 600; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 4px; }
        .badge-active { background: #10b981; color: white; }
        .badge-inactive { background: #f1f5f9; color: #94a3b8; }

        /* Alert */
        .alert-soft-warning { background: #fffbeb; border: 1px solid #fef3c7; color: #b45309; border-radius: 12px; padding: 1rem; font-size: 0.85rem; display: flex; gap: 10px; margin-bottom: 1.5rem; line-height: 1.5; }

        /* Buttons */
        .btn-clean { background: white; border: 1px solid #e2e8f0; color: var(--text-main); font-weight: 600; padding: 0.6rem 1.2rem; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s; }
        .btn-clean:hover { background: #f8fafc; border-color: #cbd5e1; }
        
        .btn-emerald { background: var(--primary); color: white; border: none; font-weight: 600; padding: 0.8rem; border-radius: 10px; width: 100%; transition: 0.2s; }
        .btn-emerald:hover { background: #059669; }

        .btn-action-soft { padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 600; font-size: 0.8rem; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; gap: 4px; }
        .btn-activate { background: #eff6ff; color: #2563eb; }
        .btn-activate:hover { background: #dbeafe; }
        .btn-delete { background: #fef2f2; color: #ef4444; }
        .btn-delete:hover { background: #fee2e2; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <div class="logo-box"><iconify-icon icon="solar:infinity-bold" style="font-size: 1.5rem;"></iconify-icon></div>
                <div><h5 class="fw-bold mb-0 brand-text">SIPRESMA</h5><p class="mb-0 text-muted" style="font-size: 10px; font-weight: 600; letter-spacing: 1px;">ACADEMIC DASHBOARD</p></div>
            </a>
            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-block text-end"><p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Administrator</p><p class="mb-0 text-muted" style="font-size: 0.75rem;">System Admin</p></div>
                <div class="bg-light rounded-circle border p-1 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <iconify-icon icon="solar:user-circle-bold" class="text-secondary" style="font-size: 1.8rem;"></iconify-icon>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <div>
                <h2 class="page-title">Pengaturan Semester</h2>
                <p class="page-subtitle">Kelola tahun ajaran dan status aktif KRS.</p>
            </div>
            <div>
                <a href="../dashboard/index.php" class="btn-clean"><iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Dashboard</a>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-md-4">
                <div class="card-modern sticky-top" style="top: 100px;">
                    <h5 class="card-title-small">
                        <iconify-icon icon="solar:add-folder-bold" class="text-primary"></iconify-icon> Buat Semester Baru
                    </h5>
                    <form action="create.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Semester</label>
                            <input type="text" name="nama" class="form-control" placeholder="Cth: 2025 Ganjil" required>
                        </div>
                        <button type="submit" name="simpan" class="btn-emerald">Simpan Data</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card-modern">
                    <div class="alert-soft-warning">
                        <iconify-icon icon="solar:info-circle-bold" class="fs-5 mt-1"></iconify-icon>
                        <div>
                            <strong>Penting:</strong> Hanya satu semester yang boleh <strong>AKTIF</strong>. 
                            Mengaktifkan semester baru akan otomatis menutup semester sebelumnya.
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>Nama Semester</th>
                                    <th width="20%">Status</th>
                                    <th width="25%" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q = mysqli_query($koneksi, "SELECT * FROM semester ORDER BY nama_semester DESC");
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($q)) {
                                    $status = strtolower($row['status']);
                                    $isActive = ($status == 'aktif');
                                    $rowClass = $isActive ? 'row-active' : '';
                                ?>
                                    <tr class="<?php echo $rowClass; ?>">
                                        <td class="text-center fw-semibold text-muted"><?php echo $no++; ?></td>
                                        <td class="fw-bold text-dark"><?php echo $row['nama_semester']; ?></td>
                                        <td>
                                            <?php if($isActive){ ?>
                                                <span class="badge-pill badge-active"><iconify-icon icon="solar:check-circle-bold"></iconify-icon> AKTIF</span>
                                            <?php } else { ?>
                                                <span class="badge-pill badge-inactive">Tidak Aktif</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-end">
                                            <?php if(!$isActive) { ?>
                                                <a href="activate.php?id=<?php echo $row['id_semester']; ?>" class="btn-action-soft btn-activate" title="Aktifkan">
                                                    <iconify-icon icon="solar:bolt-bold"></iconify-icon> Aktifkan
                                                </a>
                                                <a href="delete.php?id=<?php echo $row['id_semester']; ?>" class="btn-action-soft btn-delete" onclick="return confirm('Hapus semester ini?')" title="Hapus">
                                                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                                </a>
                                            <?php } else { ?>
                                                <span class="text-success small fw-bold"><iconify-icon icon="solar:clock-circle-bold"></iconify-icon> Sedang Berjalan</span>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>