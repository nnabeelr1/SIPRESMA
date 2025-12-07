<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }

if (isset($_POST['simpan'])) {
    include '../config/koneksi.php';
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['role'];
    $simpan = mysqli_query($koneksi, "INSERT INTO user (username, password, role) VALUES ('$user', '$pass', '$role')");
    if ($simpan) { echo "<script>alert('✅ Berhasil!'); window.location='index.php';</script>"; }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - SIPRESMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root { --primary: #10b981; --bg-body: #f8fafc; --text-main: #1e293b; --text-muted: #94a3b8; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); }
        
        /* Navbar Dashboard Style */
        .navbar-clean { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0,0,0,0.05); padding: 1rem 0; position: sticky; top: 0; z-index: 100; }
        .brand-text { color: var(--text-main); letter-spacing: -0.5px; }

        /* Card Form */
        .card-modern { background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.03); box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 2.5rem; max-width: 500px; margin: 3rem auto; }
        .form-label { font-weight: 700; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; }
        .form-control, .form-select { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.7rem 1rem; color: var(--text-main); }
        .form-control:focus, .form-select:focus { border-color: var(--primary); outline: none; background: white; }
        
        .btn-emerald { background: var(--primary); color: white; border: none; font-weight: 600; padding: 0.8rem; border-radius: 10px; width: 100%; }
        .btn-emerald:hover { background: #059669; }
        .btn-back { display: block; text-align: center; margin-top: 1rem; color: var(--text-muted); text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success d-flex"><iconify-icon icon="solar:infinity-bold" width="24"></iconify-icon></div>
                <div><h5 class="fw-bold mb-0 brand-text">SIPRESMA</h5><p class="mb-0 text-muted" style="font-size: 10px; font-weight: 600; letter-spacing: 1px;">ACADEMIC DASHBOARD</p></div>
            </a>
            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-block text-end"><p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Administrator</p><p class="mb-0 text-muted" style="font-size: 0.75rem;">System Admin</p></div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-light rounded-circle border p-1"><iconify-icon icon="solar:user-circle-bold" width="36" class="text-secondary"></iconify-icon></div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                        <li><a class="dropdown-item rounded-3" href="../logout.php"><iconify-icon icon="solar:logout-2-bold" class="me-2"></iconify-icon>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card-modern">
            <div class="text-center mb-4">
                <div style="width: 50px; height: 50px; background: #ecfdf5; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <iconify-icon icon="solar:user-plus-bold" style="font-size: 1.5rem;"></iconify-icon>
                </div>
                <h4 class="fw-bold">Tambah Admin Baru</h4>
                <p class="text-muted small">Buat akun untuk pengelola sistem.</p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Contoh: admin2" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Role Akses</label>
                    <select name="role" class="form-select">
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <button type="submit" name="simpan" class="btn-emerald">Simpan Data</button>
                <a href="index.php" class="btn-back">Batal & Kembali</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>