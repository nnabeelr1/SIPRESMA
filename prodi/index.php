<?php
session_start();
// Security Check
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    header("Location: ../index.php?pesan=belum_login");
    exit();
}
if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard/welcome_mhs.php");
    exit();
}

include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Program Studi - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            --primary: #10b981; --bg-body: #f8fafc; --text-main: #1e293b; --text-muted: #64748b;
            --card-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); padding-bottom: 3rem; }

        /* Navbar Clean */
        .navbar-clean { background: white; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 0.8rem 0; position: sticky; top: 0; z-index: 100; }
        .logo-box { background: rgba(16, 185, 129, 0.1); color: #10b981; width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .brand-text { color: var(--text-main); letter-spacing: -0.5px; }

        /* Header */
        .page-header { margin-top: 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: end; }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* Card & Table */
        .card-modern { background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.03); box-shadow: var(--card-shadow); overflow: hidden; }
        .table-custom thead th { background-color: #f8fafc; color: var(--text-muted); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
        .table-custom tbody td { padding: 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: var(--text-main); }
        
        /* Badges */
        .badge-pill { padding: 0.35em 0.8em; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
        .badge-d3 { background: #e0f2fe; color: #0369a1; }
        .badge-s1 { background: #dcfce7; color: #15803d; }
        .badge-s2 { background: #f3e8ff; color: #7e22ce; }

        /* Buttons */
        .btn-clean { background: white; border: 1px solid #e2e8f0; color: var(--text-main); font-weight: 600; padding: 0.6rem 1.2rem; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s; }
        .btn-clean:hover { background: #f8fafc; border-color: #cbd5e1; }
        .btn-primary-soft { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-weight: 600; padding: 0.6rem 1.2rem; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s; }
        .btn-primary-soft:hover { background: #d1fae5; color: #047857; }
        
        .btn-icon-soft { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border: none; text-decoration: none; }
        .btn-edit { background: #fef3c7; color: #b45309; }
        .btn-edit:hover { background: #fde68a; }
        .btn-delete { background: #fee2e2; color: #b91c1c; }
        .btn-delete:hover { background: #fecaca; }
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
                <h2 class="page-title">Program Studi</h2>
                <p class="page-subtitle">Manajemen data jurusan dan jenjang pendidikan.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard/index.php" class="btn-clean"><iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Dashboard</a>
                <a href="create.php" class="btn-primary-soft"><iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Prodi</a>
            </div>
        </div>

        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">Kode Prodi</th>
                            <th width="45%">Nama Program Studi</th>
                            <th width="15%">Jenjang</th>
                            <th width="20%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM prodi ORDER BY jenjang ASC, nama_prodi ASC");
                        $no = 1;
                        while ($data = mysqli_fetch_array($query)) {
                            $jenjang = $data['jenjang'];
                            $badge = ($jenjang == 'S1') ? 'badge-s1' : (($jenjang == 'D3') ? 'badge-d3' : 'badge-s2');
                        ?>
                            <tr>
                                <td class="text-center fw-semibold text-muted"><?php echo $no++; ?></td>
                                <td class="font-monospace fw-bold text-dark"><?php echo $data['kode_prodi']; ?></td>
                                <td class="fw-bold text-dark"><?php echo $data['nama_prodi']; ?></td>
                                <td><span class="badge-pill <?php echo $badge; ?>"><?php echo $jenjang; ?></span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="update.php?id=<?php echo $data['id_prodi']; ?>" class="btn-icon-soft btn-edit"><iconify-icon icon="solar:pen-bold"></iconify-icon></a>
                                        <a href="delete.php?id=<?php echo $data['id_prodi']; ?>" class="btn-icon-soft btn-delete" onclick="return confirm('Hapus prodi ini?')"><iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon></a>
                                    </div>
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