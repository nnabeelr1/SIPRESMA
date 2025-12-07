<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'mahasiswa') { header("Location: ../index.php"); exit(); }

$username = $_SESSION['username'];
$mhs = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nim, nama_lengkap FROM mahasiswa JOIN user ON mahasiswa.id_user = user.id_user WHERE user.username='$username'"));
$nim_saya = $mhs['nim'];
$nama_user = explode(' ', trim($mhs['nama_lengkap']))[0];

if(isset($_POST['simpan'])) {
    $kegiatan = $_POST['kegiatan'];
    $juara    = $_POST['juara'];
    $tingkat  = $_POST['tingkat'];
    $tahun    = $_POST['tahun'];
    $ket      = $_POST['keterangan'];

    $filename = $_FILES['bukti']['name'];
    $tmp_name = $_FILES['bukti']['tmp_name'];
    $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $new_name = $nim_saya . "_" . time() . "." . $ext;
    $target_dir = "../uploads/";
    
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
    if(!in_array($ext, $allowed)) {
        echo "<script>alert('❌ Format file harus PDF, JPG, atau PNG!');</script>";
    } else {
        if(move_uploaded_file($tmp_name, $target_dir . $new_name)) {
            $insert = mysqli_query($koneksi, "INSERT INTO prestasi (nim, nama_kegiatan, jenis_juara, tingkat, tahun, keterangan, file_bukti, status_validasi) VALUES ('$nim_saya', '$kegiatan', '$juara', '$tingkat', '$tahun', '$ket', '$new_name', 'menunggu')");
            if($insert) { echo "<script>alert('✅ Berhasil diajukan!'); window.location='index.php';</script>"; }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Prestasi - SIPRESMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            --primary: #10b981; --secondary: #64748b; --bg-body: #f8fafc; --card-bg: #ffffff;
            --text-main: #1e293b; --text-muted: #94a3b8;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); padding-bottom: 3rem; }
        
        .navbar-clean { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0,0,0,0.05); padding: 1rem 0; position: sticky; top: 0; z-index: 100; }
        .brand-text { color: var(--text-main); letter-spacing: -0.5px; }

        .card-modern { background: var(--card-bg); border: 1px solid rgba(0,0,0,0.03); border-radius: 16px; box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05); padding: 2.5rem; max-width: 700px; margin: 0 auto; }
        .form-label { font-weight: 700; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.4rem; }
        .form-control, .form-select { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.7rem 1rem; color: var(--text-main); }
        .form-control:focus, .form-select:focus { border-color: var(--primary); outline: none; background: white; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
        
        .btn-emerald { background: var(--primary); color: white; border: none; font-weight: 600; padding: 0.8rem; border-radius: 10px; width: 100%; margin-top: 1rem; }
        .btn-emerald:hover { background: #059669; }
        .bg-decoration { position: fixed; width: 400px; height: 400px; background: var(--primary); filter: blur(120px); opacity: 0.05; z-index: -1; border-radius: 50%; }
        .blob-1 { top: -100px; right: -100px; } .blob-2 { bottom: -100px; left: -100px; }
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
                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">Mahasiswa</p>
                </div>
                <div class="bg-light rounded-circle border p-1"><iconify-icon icon="solar:user-circle-bold" width="36" class="text-secondary"></iconify-icon></div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4 align-items-end justify-content-center">
            <div class="col-md-8 text-center">
                <h2 class="fw-bold mb-1">Input Prestasi</h2>
                <p class="text-muted mb-0">Laporkan prestasi untuk validasi akademik.</p>
            </div>
        </div>

        <div class="card-modern">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama Kegiatan / Lomba</label>
                    <input type="text" name="kegiatan" class="form-control" placeholder="Contoh: Lomba Web Design Nasional" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pencapaian (Juara)</label>
                        <select name="juara" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option>Juara 1</option><option>Juara 2</option><option>Juara 3</option>
                            <option>Juara Harapan</option><option>Finalis</option><option>Peserta</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tingkat</label>
                        <select name="tingkat" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option>Jurusan</option><option>Fakultas</option><option>Universitas</option>
                            <option>Regional</option><option>Nasional</option><option>Internasional</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Perolehan</label>
                        <input type="number" name="tahun" class="form-control" value="<?php echo date('Y'); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Upload Bukti (Sertifikat)</label>
                        <input type="file" name="bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan Tambahan</label>
                    <textarea name="keterangan" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" name="simpan" class="btn-emerald">Simpan Prestasi</button>
                <a href="index.php" class="d-block text-center mt-3 text-decoration-none text-muted small fw-bold">Batal & Kembali</a>
            </form>
        </div>
    </div>
</body>
</html>