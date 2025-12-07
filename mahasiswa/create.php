<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $username     = $_POST['username'];
    $password     = $_POST['password']; 
    $nim          = $_POST['nim'];
    $nama         = $_POST['nama'];
    $angkatan     = $_POST['angkatan'];
    $id_prodi     = $_POST['id_prodi'];
    $dosen_wali   = $_POST['dosen_wali'];
    $ipk          = $_POST['ipk'];

    // Validasi Duplikat
    $cek_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
    $cek_nim = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE nim='$nim'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Username sudah terpakai!');</script>";
    } elseif (mysqli_num_rows($cek_nim) > 0) {
        echo "<script>alert('NIM sudah terdaftar!');</script>";
    } else {
        // Insert User
        $insert_user = mysqli_query($koneksi, "INSERT INTO user (username, password, role) VALUES ('$username', '$password', 'mahasiswa')");
        if ($insert_user) {
            $id_user_baru = mysqli_insert_id($koneksi);
            // Insert Mahasiswa
            $insert_mhs = mysqli_query($koneksi, "INSERT INTO mahasiswa (nim, id_user, nama_lengkap, id_prodi, angkatan, dosen_wali, ipk_terakhir) 
                                                  VALUES ('$nim', '$id_user_baru', '$nama', '$id_prodi', '$angkatan', '$dosen_wali', '$ipk')");
            if ($insert_mhs) {
                echo "<script>alert('✅ Berhasil!'); window.location='index.php';</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root { --primary: #10b981; --bg-body: #f8fafc; --text-main: #1e293b; --text-muted: #64748b; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); padding-bottom: 3rem; }
        
        /* Navbar */
        .navbar-clean { background: white; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 0.8rem 0; position: sticky; top: 0; z-index: 100; }
        .logo-box { background: rgba(16, 185, 129, 0.1); color: #10b981; width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .brand-text { color: var(--text-main); letter-spacing: -0.5px; }

        /* Card Form */
        .card-modern { background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.03); box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 2.5rem; max-width: 800px; margin: 3rem auto; }
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
                <h4 class="fw-bold">Tambah Data Mahasiswa</h4>
                <p class="text-muted small">Input data mahasiswa dan akun login sekaligus.</p>
            </div>

            <form method="POST">
                
                <div class="form-section-title">1. Akun Login</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username (NIM)</label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: 24001" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="text" name="password" class="form-control" value="123" required>
                    </div>
                </div>

                <div class="form-section-title">2. Biodata Mahasiswa</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIM</label>
                        <input type="text" name="nim" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Angkatan</label>
                        <input type="number" name="angkatan" class="form-control" value="<?php echo date('Y'); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">IPK Terakhir</label>
                        <input type="number" name="ipk" class="form-control" step="0.01" min="0" max="4.00" value="0.00" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program Studi</label>
                        <select name="id_prodi" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <?php
                            $q_prodi = mysqli_query($koneksi, "SELECT * FROM prodi ORDER BY nama_prodi ASC");
                            while ($p = mysqli_fetch_assoc($q_prodi)) {
                                echo "<option value='".$p['id_prodi']."'>".$p['nama_prodi']." (".$p['jenjang'].")</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dosen Wali</label>
                        <select name="dosen_wali" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <?php
                            $q_dosen = mysqli_query($koneksi, "SELECT * FROM dosen ORDER BY nama_lengkap ASC");
                            while ($d = mysqli_fetch_assoc($q_dosen)) {
                                echo "<option value='".$d['nidn']."'>".$d['nama_lengkap']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <button type="submit" name="simpan" class="btn-emerald">Simpan Data</button>
                <a href="index.php" class="btn-back">Batal & Kembali</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>