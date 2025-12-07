<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }

if (isset($_POST['simpan'])) {
    include '../config/koneksi.php';
    $kode = $_POST['kode_prodi'];
    $nama = $_POST['nama_prodi'];
    $jenjang = $_POST['jenjang'];
    $simpan = mysqli_query($koneksi, "INSERT INTO prodi (kode_prodi, nama_prodi, jenjang) VALUES ('$kode', '$nama', '$jenjang')");
    if ($simpan) { echo "<script>alert('✅ Berhasil!'); window.location='index.php';</script>"; } 
    else { echo "<script>alert('❌ Gagal!');</script>"; }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Prodi - SIPRESMA</title>
    
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
        .card-modern { background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.03); box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 2.5rem; max-width: 500px; margin: 3rem auto; }
        .form-label { font-weight: 700; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; }
        .form-control, .form-select { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.7rem 1rem; color: var(--text-main); }
        .form-control:focus, .form-select:focus { border-color: var(--primary); outline: none; background: white; }
        
        .btn-emerald { background: var(--primary); color: white; border: none; font-weight: 600; padding: 0.8rem; border-radius: 10px; width: 100%; transition: 0.2s; }
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
                    <iconify-icon icon="solar:buildings-bold" style="font-size: 1.5rem;"></iconify-icon>
                </div>
                <h4 class="fw-bold">Tambah Prodi</h4>
                <p class="text-muted small">Input data program studi baru.</p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Kode Prodi</label>
                    <input type="text" name="kode_prodi" class="form-control" placeholder="Contoh: A11" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Prodi</label>
                    <input type="text" name="nama_prodi" class="form-control" placeholder="Contoh: Teknik Informatika" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Jenjang</label>
                    <select name="jenjang" class="form-select">
                        <option value="D3">D3 - Diploma 3</option>
                        <option value="S1" selected>S1 - Sarjana</option>
                        <option value="S2">S2 - Magister</option>
                    </select>
                </div>
                <button type="submit" name="simpan" class="btn-emerald">Simpan Data</button>
                <a href="index.php" class="btn-back">Batal & Kembali</a>
            </form>
        </div>
    </div>

</body>
</html>