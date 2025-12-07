<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }
include '../config/koneksi.php';

$id = $_GET['id'];
$data = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM kelas WHERE id_kelas='$id'"));

if (isset($_POST['update'])) {
    $mk    = $_POST['kode_mk'];
    $dosen = $_POST['nidn'];
    $nama  = $_POST['nama_kelas'];
    $hari  = $_POST['hari'];
    $mulai = $_POST['jam_mulai'];
    $selesai = $_POST['jam_selesai'];

    $update = mysqli_query($koneksi, "UPDATE kelas SET kode_mk='$mk', nidn='$dosen', nama_kelas='$nama', hari='$hari', jam_mulai='$mulai', jam_selesai='$selesai' WHERE id_kelas='$id'");

    if ($update) { echo "<script>alert('✅ Berhasil!'); window.location='index.php';</script>"; } 
    else { echo "Error: " . mysqli_error($koneksi); }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kelas - SIPRESMA</title>
    
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
        .card-modern { background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.03); box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 2.5rem; max-width: 800px; margin: 3rem auto; }
        .form-label { font-weight: 700; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.3rem; }
        .form-control, .form-select { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.7rem 1rem; color: var(--text-main); }
        .form-control:focus, .form-select:focus { border-color: var(--warning); outline: none; background: white; }
        
        .btn-warning-soft { background: var(--warning); color: white; border: none; font-weight: 600; padding: 0.8rem; border-radius: 10px; width: 100%; transition: 0.2s; margin-top: 2rem; }
        .btn-warning-soft:hover { background: #d97706; }
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
                <div style="width: 50px; height: 50px; background: #fffbeb; color: var(--warning); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <iconify-icon icon="solar:pen-new-square-bold" style="font-size: 1.5rem;"></iconify-icon>
                </div>
                <h4 class="fw-bold">Edit Kelas</h4>
                <p class="text-muted small">Ubah detail jadwal atau pengajar.</p>
            </div>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mata Kuliah</label>
                        <select name="kode_mk" class="form-select" required>
                            <?php
                            $q_mk = mysqli_query($koneksi, "SELECT * FROM matakuliah ORDER BY nama_mk ASC");
                            while($m = mysqli_fetch_array($q_mk)){
                                $sel = ($data['kode_mk'] == $m['kode_mk']) ? 'selected' : '';
                                echo "<option value='$m[kode_mk]' $sel>$m[nama_mk]</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dosen Pengajar</label>
                        <select name="nidn" class="form-select" required>
                            <?php
                            $q_d = mysqli_query($koneksi, "SELECT * FROM dosen ORDER BY nama_lengkap ASC");
                            while($d = mysqli_fetch_array($q_d)){
                                $sel = ($data['nidn'] == $d['nidn']) ? 'selected' : '';
                                echo "<option value='$d[nidn]' $sel>$d[nama_lengkap]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" value="<?php echo $data['nama_kelas']; ?>" required>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Hari</label>
                        <select name="hari" class="form-select" required>
                            <option <?php if($data['hari']=='Senin') echo 'selected'; ?>>Senin</option>
                            <option <?php if($data['hari']=='Selasa') echo 'selected'; ?>>Selasa</option>
                            <option <?php if($data['hari']=='Rabu') echo 'selected'; ?>>Rabu</option>
                            <option <?php if($data['hari']=='Kamis') echo 'selected'; ?>>Kamis</option>
                            <option <?php if($data['hari']=='Jumat') echo 'selected'; ?>>Jumat</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" value="<?php echo $data['jam_mulai']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" value="<?php echo $data['jam_selesai']; ?>" required>
                    </div>
                </div>

                <button type="submit" name="update" class="btn-warning-soft">Update Data</button>
                <a href="index.php" class="btn-back">Batal & Kembali</a>
            </form>
        </div>
    </div>

</body>
</html>