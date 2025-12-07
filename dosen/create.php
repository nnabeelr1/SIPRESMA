<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }

if (isset($_POST['simpan'])) {
    include '../config/koneksi.php';
    
    // Data Akun
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Data Dosen
    $nidn     = $_POST['nidn'];
    $nama     = $_POST['nama_lengkap'];
    $email    = $_POST['email'];
    $id_prodi = $_POST['id_prodi'];

    // 1. Insert User
    $insert_user = mysqli_query($koneksi, "INSERT INTO user (username, password, role) VALUES ('$username', '$password', 'dosen')");
    
    if ($insert_user) {
        $id_user = mysqli_insert_id($koneksi);
        
        // 2. Insert Dosen
        $insert_dosen = mysqli_query($koneksi, "INSERT INTO dosen (nidn, id_user, id_prodi, nama_lengkap, email) 
                                                VALUES ('$nidn', '$id_user', '$id_prodi', '$nama', '$email')");
        
        if ($insert_dosen) {
            echo "<script>alert('✅ Dosen berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('❌ Gagal Dosen: " . mysqli_error($koneksi) . "');</script>";
        }
    } else {
        echo "<script>alert('❌ Gagal User: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Dosen - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root { --primary: #10b981; --bg-body: #f8fafc; --text-main: #1e293b; --text-muted: #64748b; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); }
        
        /* Navbar */
        .navbar-clean { background: white; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 0.8rem 0; }
        .logo-box { background: rgba(16, 185, 129, 0.1); color: #10b981; width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .brand-text { color: var(--text-main); letter-spacing: -0.5px; }

        /* Card Form */
        .card-modern { background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.03); box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 2.5rem; max-width: 600px; margin: 3rem auto; }
        .form-section-title { font-size: 0.9rem; font-weight: 700; color: var(--text-main); border-bottom: 2px solid #f1f5f9; padding-bottom: 0.5rem; margin-bottom: 1rem; margin-top: 1.5rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-label { font-weight: 600; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.3rem; }
        .form-control, .form-select { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.7rem 1rem; color: var(--text-main); }
        .form-control:focus, .form-select:focus { border-color: var(--primary); outline: none; background: white; }
        
        .btn-emerald { background: var(--primary); color: white; border: none; font-weight: 600; padding: 0.8rem; border-radius: 10px; width: 100%; transition: 0.2s; margin-top: 2rem; }
        .btn-emerald:hover { background: #059669; }
        .btn-back { display: block; text-align: center; margin-top: 1rem; color: var(--text-muted); text-decoration: none; font-size: 0.9rem; }
        .btn-back:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <div class="logo-box"><iconify-icon icon="solar:infinity-bold" style="font-size: 1.5rem;"></iconify-icon></div>
                <div><h5 class="fw-bold mb-0 brand-text">SIPRESMA</h5><p class="mb-0 text-muted" style="font-size: 10px; font-weight: 600; letter-spacing: 1px;">ACADEMIC DASHBOARD</p></div>
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="card-modern">
            <div class="text-center mb-4">
                <div style="width: 50px; height: 50px; background: #ecfdf5; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <iconify-icon icon="solar:user-plus-bold" style="font-size: 1.5rem;"></iconify-icon>
                </div>
                <h4 class="fw-bold">Tambah Data Dosen</h4>
                <p class="text-muted small">Lengkapi formulir di bawah ini.</p>
            </div>

            <form method="POST">
                
                <div class="form-section-title">1. Akun Login</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>

                <div class="form-section-title">2. Biodata Dosen</div>
                <div class="mb-3">
                    <label class="form-label">NIDN</label>
                    <input type="text" name="nidn" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap (+Gelar)</label>
                    <input type="text" name="nama_lengkap" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Homebase Prodi</label>
                    <select name="id_prodi" class="form-select">
                        <?php
                        include '../config/koneksi.php';
                        $prodi = mysqli_query($koneksi, "SELECT * FROM prodi");
                        while($p = mysqli_fetch_array($prodi)){
                            echo "<option value='$p[id_prodi]'>$p[nama_prodi] ($p[jenjang])</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" name="simpan" class="btn-emerald">Simpan Data</button>
                <a href="index.php" class="btn-back">Batal & Kembali</a>
            </form>
        </div>
    </div>

</body>
</html>