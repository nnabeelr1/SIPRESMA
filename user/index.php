<?php
session_start();
include '../config/koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            --primary: #10b981;       
            --bg-body: #f8fafc;       
            --text-main: #1e293b;     
            --text-muted: #94a3b8;    
            --card-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 3rem;
        }

        /* --- Navbar Clean (PERSIS DASHBOARD) --- */
        .navbar-clean {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .brand-text {
            color: var(--text-main);
            letter-spacing: -0.5px;
        }

        /* --- Header & Layout --- */
        .page-header {
            margin-top: 2rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* --- Alert --- */
        .alert-soft-info {
            background: #eff6ff; border: 1px solid #dbeafe; color: #1e40af;
            border-radius: 12px; padding: 1rem; font-size: 0.9rem;
            display: flex; gap: 10px; align-items: flex-start; margin-bottom: 2rem;
        }

        /* --- Card & Table --- */
        .card-modern {
            background: white; border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        .table-custom thead th {
            background-color: #f8fafc; color: var(--text-muted);
            font-weight: 700; font-size: 0.75rem; text-transform: uppercase;
            letter-spacing: 0.05em; padding: 1rem; border-bottom: 1px solid #e2e8f0;
        }
        .table-custom tbody td {
            padding: 1rem; vertical-align: middle;
            border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: var(--text-main);
        }

        /* --- Badges & Buttons --- */
        .badge-pill { padding: 0.35em 0.8em; border-radius: 6px; font-weight: 600; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px; }
        .badge-admin { background: #fee2e2; color: #b91c1c; }
        .badge-dosen { background: #dcfce7; color: #166534; }
        .badge-mhs   { background: #dbeafe; color: #1e40af; }

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
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Administrator</p>
                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">System Admin</p>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-light rounded-circle border p-1">
                             <iconify-icon icon="solar:user-circle-bold" width="36" class="text-secondary"></iconify-icon>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                        <li><a class="dropdown-item rounded-3" href="../logout.php">
                            <iconify-icon icon="solar:logout-2-bold" class="me-2"></iconify-icon>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <div>
                <h2 class="page-title">Manajemen User</h2>
                <p class="page-subtitle">Kelola akun login untuk Admin, Dosen, dan Mahasiswa.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard/index.php" class="btn-clean">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Dashboard
                </a>
                <a href="create.php" class="btn-primary-soft">
                    <iconify-icon icon="solar:user-plus-bold"></iconify-icon> Tambah Admin
                </a>
            </div>
        </div>

        <div class="alert-soft-info">
            <iconify-icon icon="solar:info-circle-bold" class="fs-5 mt-1"></iconify-icon>
            <div>
                <strong>Informasi Sistem:</strong><br>
                Halaman ini khusus untuk mengelola akun (Username/Password). 
                Untuk menambah data Mahasiswa atau Dosen secara lengkap, silakan gunakan menu <strong>Data Master</strong> masing-masing.
            </div>
        </div>

        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="35%">Username</th>
                            <th width="20%">Role (Peran)</th>
                            <th width="15%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM user ORDER BY id_user DESC");
                        $no = 1;
                        while ($data = mysqli_fetch_array($query)) {
                            if($data['role']=='admin') { $badge='badge-admin'; $icon='solar:shield-user-bold'; $role='Administrator'; } 
                            elseif($data['role']=='dosen') { $badge='badge-dosen'; $icon='solar:user-id-bold'; $role='Dosen Pengajar'; } 
                            else { $badge='badge-mhs'; $icon='solar:square-academic-cap-bold'; $role='Mahasiswa'; }
                        ?>
                            <tr>
                                <td class="text-center fw-semibold text-muted"><?php echo $no++; ?></td>
                                <td><span class="fw-bold text-dark"><?php echo $data['username']; ?></span></td>
                                <td><span class="badge-pill <?php echo $badge; ?>"><iconify-icon icon="<?php echo $icon; ?>"></iconify-icon> <?php echo $role; ?></span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="update.php?id=<?php echo $data['id_user']; ?>" class="btn-icon-soft btn-edit"><iconify-icon icon="solar:pen-bold"></iconify-icon></a>
                                        <?php if($data['username'] != $_SESSION['username']) { ?>
                                            <a href="delete.php?id=<?php echo $data['id_user']; ?>" class="btn-icon-soft btn-delete" onclick="return confirm('Hapus user?')"><iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon></a>
                                        <?php } ?>
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