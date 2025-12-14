<?php
session_start();
include '../config/koneksi.php';

// 1. Cek Login & Role
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// 2. Ambil Data Mahasiswa
$username = $_SESSION['username'];
$q_mhs = mysqli_query($koneksi, "
    SELECT m.*, u.username 
    FROM mahasiswa m 
    JOIN user u ON m.id_user = u.id_user 
    WHERE u.username='$username'
");
$data_mhs = mysqli_fetch_assoc($q_mhs);
$nim_saya = $data_mhs['nim'];
$nama_saya = $data_mhs['nama_lengkap'];
$nama_depan = explode(' ', trim($nama_saya))[0];

// 3. Logic Simpan Data
if(isset($_POST['simpan'])) {
    $kegiatan = $_POST['kegiatan'];
    $juara    = $_POST['juara'];
    $tingkat  = $_POST['tingkat'];
    $tahun    = $_POST['tahun'];
    $ket      = $_POST['keterangan'];

    // Upload File
    $filename = $_FILES['bukti']['name'];
    $tmp_name = $_FILES['bukti']['tmp_name'];
    $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $new_name = $nim_saya . "_" . time() . "." . $ext;
    $target_dir = "../uploads/";
    
    // Buat folder jika belum ada
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
    if(!in_array($ext, $allowed)) {
        echo "<script>alert('❌ Format file harus PDF, JPG, atau PNG!');</script>";
    } else {
        if(move_uploaded_file($tmp_name, $target_dir . $new_name)) {
            $insert = mysqli_query($koneksi, "INSERT INTO prestasi (nim, nama_kegiatan, jenis_juara, tingkat, tahun, keterangan, file_bukti, status_validasi) VALUES ('$nim_saya', '$kegiatan', '$juara', '$tingkat', '$tahun', '$ket', '$new_name', 'menunggu')");
            
            if($insert) { 
                echo "<script>alert('✅ Prestasi berhasil diajukan! Menunggu validasi dosen.'); window.location='index.php';</script>"; 
            } else {
                echo "<script>alert('❌ Gagal menyimpan data database.');</script>";
            }
        } else {
            echo "<script>alert('❌ Gagal upload file.');</script>";
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        /* --- THEME CONFIGURATION --- */
        :root {
            --primary: #10b981;       
            --primary-dark: #047857;
            --text-main: #0f172a;     
            --text-muted: #64748b;    
            --radius-xl: 24px;
            --nav-height: 80px;
        }

        /* --- GLOBAL & ANIMATION --- */
        html { overflow-y: scroll; } 
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            background-color: #f0fdf4; 
            background-image: 
                radial-gradient(#86efac 1.2px, transparent 1.2px), 
                radial-gradient(circle at top center, rgba(16, 185, 129, 0.1) 0%, rgba(240, 253, 244, 0) 70%); 
            background-size: 24px 24px, 100% 100%;
            background-attachment: fixed;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-entry {
            animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0; 
        }

        /* --- NAVBAR --- */
        .navbar-clean {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(16, 185, 129, 0.1); 
            border-top: 3px solid var(--primary); 
            height: var(--nav-height);
            position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 4px 20px -10px rgba(16, 185, 129, 0.15);
        }
        .logo-box {
            background: linear-gradient(135deg, #10b981, #047857);
            color: white; padding: 8px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }
        .brand-text { letter-spacing: -0.5px; color: var(--text-main); }
        .brand-sub { letter-spacing: 1.5px; font-weight: 700; color: var(--text-muted); font-size: 10px; }

        /* --- HEADER PAGE --- */
        .page-header {
            margin-top: 2rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; letter-spacing: -1px; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* --- CARD FORM --- */
        .card-modern {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius-xl);
            padding: 2.5rem; 
            box-shadow: 0 10px 40px -5px rgba(0,0,0,0.05);
            max-width: 700px;
            margin: 2rem auto;
            transition: transform 0.3s ease;
        }

        /* --- FORM ELEMENTS --- */
        .form-label { font-weight: 700; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; }
        
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.8rem 1rem;
            color: var(--text-main);
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); background: white;
        }
        
        /* File Input Custom */
        input[type=file]::file-selector-button {
            margin-right: 15px; border: none; background: #e2e8f0;
            padding: 8px 16px; border-radius: 8px; color: var(--text-main); cursor: pointer; font-weight: 600; font-size: 0.8rem;
            transition: .2s;
        }
        input[type=file]::file-selector-button:hover { background: #cbd5e1; }

        /* --- BUTTONS --- */
        .btn-gradient-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 50px;
            font-weight: 700; width: 100%; margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); transition: all 0.2s;
        }
        .btn-gradient-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4); color: white; }

        .btn-back {
            display: block; text-align: center; margin-top: 1.5rem;
            color: var(--text-muted); text-decoration: none !important;
            font-weight: 600; font-size: 0.9rem; transition: 0.2s;
        }
        .btn-back:hover { color: var(--primary); }

        .btn-glass-back {
            background: white; border: 1px solid #e2e8f0; color: var(--text-main);
            font-weight: 700; padding: 0.6rem 1.2rem; border-radius: 50px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03); font-size: 0.9rem; text-decoration: none;
        }
        .btn-glass-back:hover { transform: translateY(-2px); color: var(--primary); }

        /* Icon Header Form */
        .icon-header {
            width: 60px; height: 60px; 
            background: #fff7ed; color: #f97316; /* Orange Theme for Achievement */
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 0 8px rgba(255, 247, 237, 0.5);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="../dashboard/welcome_mhs.php">
                <div class="logo-box">
                    <iconify-icon icon="solar:infinity-bold" width="24"></iconify-icon>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 brand-text">SIPRESMA</h5>
                    <p class="mb-0 brand-sub">STUDENT PORTAL</p>
                </div>
            </a>
            
            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-block text-end">
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><?php echo $nama_depan; ?></p>
                    <p class="mb-0 text-success d-flex align-items-center justify-content-end gap-1" style="font-size: 0.75rem; font-weight: 600;">
                        <iconify-icon icon="solar:record-circle-bold" style="font-size: 8px;"></iconify-icon> <?php echo $nim_saya; ?>
                    </p>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-white rounded-circle border p-1 shadow-sm transition-hover">
                             <iconify-icon icon="solar:user-circle-bold-duotone" width="40" class="text-primary"></iconify-icon>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-2">
                        <li><a class="dropdown-item rounded-3 text-danger fw-bold" href="../logout.php">
                            <iconify-icon icon="solar:logout-2-bold-duotone" class="me-2"></iconify-icon>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5 pb-5">
        
        <div class="page-header animate-entry" style="animation-delay: 0.1s;">
            <div>
                <h2 class="page-title">Input Prestasi</h2>
                <p class="page-subtitle">Laporkan pencapaian non-akademik untuk divalidasi.</p>
            </div>
            <div>
                <a href="index.php" class="btn-glass-back">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Kembali
                </a>
            </div>
        </div>

        <div class="card-modern animate-entry" style="animation-delay: 0.2s;">
            <div class="text-center mb-4">
                <div class="icon-header">
                    <iconify-icon icon="solar:cup-star-bold-duotone" style="font-size: 1.8rem;"></iconify-icon>
                </div>
                <h4 class="fw-bold mb-1">Form Pengajuan Prestasi</h4>
                <p class="text-muted small">Pastikan data yang diinput sesuai dengan sertifikat asli.</p>
            </div>

            <form method="POST" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label class="form-label">Nama Kegiatan / Lomba</label>
                    <input type="text" name="kegiatan" class="form-control" placeholder="Contoh: Lomba Web Design Nasional 2025" required autofocus>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pencapaian (Juara)</label>
                        <select name="juara" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option>Juara 1</option>
                            <option>Juara 2</option>
                            <option>Juara 3</option>
                            <option>Juara Harapan</option>
                            <option>Finalis</option>
                            <option>Peserta</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tingkat</label>
                        <select name="tingkat" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option>Jurusan</option>
                            <option>Fakultas</option>
                            <option>Universitas</option>
                            <option>Regional</option>
                            <option>Nasional</option>
                            <option>Internasional</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Perolehan</label>
                        <input type="number" name="tahun" class="form-control" value="<?php echo date('Y'); ?>" min="2000" max="<?php echo date('Y'); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Upload Bukti (Sertifikat)</label>
                        <input type="file" name="bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="form-text small mt-1 text-muted">
                            <iconify-icon icon="solar:info-circle-linear" style="position: relative; top: 1px;"></iconify-icon> Format: PDF, JPG, PNG (Max 2MB)
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Keterangan Tambahan (Opsional)</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan detail lain jika diperlukan..."></textarea>
                </div>

                <button type="submit" name="simpan" class="btn-gradient-primary">
                    <iconify-icon icon="solar:upload-track-bold" class="me-2"></iconify-icon> Ajukan Prestasi
                </button>
                
                <a href="index.php" class="btn-back">
                    Batal & Kembali
                </a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>