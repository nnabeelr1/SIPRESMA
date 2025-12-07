<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }
include '../config/koneksi.php';

$kode = $_GET['kode'];
$query = mysqli_query($koneksi, "SELECT * FROM matakuliah WHERE kode_mk = '$kode'");
$data = mysqli_fetch_array($query);

if (isset($_POST['update'])) {
    $nama  = $_POST['nama_mk'];
    $sks   = $_POST['sks'];
    $smt   = $_POST['semester_paket'];
    $prodi = $_POST['id_prodi'];

    $update = mysqli_query($koneksi, "UPDATE matakuliah SET nama_mk='$nama', sks='$sks', semester_paket='$smt', id_prodi='$prodi' WHERE kode_mk='$kode'");

    if ($update) { echo "<script>alert('✅ Berhasil diupdate!'); window.location='index.php';</script>"; } 
    else { echo "Gagal: " . mysqli_error($koneksi); }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Matkul - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root { --warning: #f59e0b; --bg-body: #f8fafc; --text-main: #1e293b; --text-muted: #64748b; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); }
        
        /* Navbar */
        .navbar-clean { background: white; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 0.8rem 0; }
        .logo-box { background: rgba(16, 185, 129, 0.1); color: #10b981; width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .brand-text { color: var(--text-main); letter-spacing: -0.5px; }

        /* Card Form */
        .card-modern { background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.03); box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 2.5rem; max-width: 700px; margin: 3rem auto; }
        .form-label { font-weight: 700; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.3rem; }
        .form-control, .form-select { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.7rem 1rem; color: var(--text-main); }
        .form-control:focus, .form-select:focus { border-color: var(--warning); outline: none; background: white; }
        
        .btn-warning-soft { background: var(--warning); color: white; border: none; font-weight: 600; padding: 0.8rem; border-radius: 10px; width: 100%; transition: 0.2s; margin-top: 2rem; }
        .btn-warning-soft:hover { background: #d97706; }
        .btn-back { display: block; text-align: center; margin-top: 1rem; color: var(--text-muted); text-decoration: none; font-size: 0.9rem; }
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
                <div style="width: 50px; height: 50px; background: #fffbeb; color: var(--warning); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <iconify-icon icon="solar:pen-new-square-bold" style="font-size: 1.5rem;"></iconify-icon>
                </div>
                <h4 class="fw-bold">Edit Mata Kuliah</h4>
                <p class="text-muted small">Perbarui data kurikulum.</p>
            </div>

            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kode MK</label>
                        <input type="text" name="kode_mk" class="form-control" value="<?php echo $data['kode_mk']; ?>" readonly style="background: #f1f5f9; cursor: not-allowed;">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Nama Mata Kuliah</label>
                        <input type="text" name="nama_mk" class="form-control" value="<?php echo $data['nama_mk']; ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SKS</label>
                        <input type="number" name="sks" class="form-control" min="1" max="6" value="<?php echo $data['sks']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Semester Paket</label>
                        <input type="number" name="semester_paket" class="form-control" min="1" max="8" value="<?php echo $data['semester_paket']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Prodi Pemilik</label>
                    <select name="id_prodi" class="form-select">
                        <?php
                        $q_prodi = mysqli_query($koneksi, "SELECT * FROM prodi");
                        while($p = mysqli_fetch_array($q_prodi)){
                            $pilih = ($data['id_prodi'] == $p['id_prodi']) ? 'selected' : '';
                            echo "<option value='$p[id_prodi]' $pilih>$p[nama_prodi]</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" name="update" class="btn-warning-soft">Update Data</button>
                <a href="index.php" class="btn-back">Batal & Kembali</a>
            </form>
        </div>
    </div>

</body>
</html>