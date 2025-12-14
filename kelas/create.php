<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

include '../config/koneksi.php';

// Cek Semester Aktif (PENTING!)
$smt = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'"));
if(!$smt) { 
    echo "<script>alert('❌ Tidak ada semester aktif! Harap aktifkan semester terlebih dahulu.'); window.location='index.php';</script>"; 
    exit(); 
}

if (isset($_POST['simpan'])) {
    $mk      = $_POST['kode_mk'];
    $dosen   = $_POST['nidn'];
    $nama    = $_POST['nama_kelas'];
    $hari    = $_POST['hari'];
    $mulai   = $_POST['jam_mulai'];
    $selesai = $_POST['jam_selesai'];
    $id_smt  = $smt['id_semester'];

    $simpan = mysqli_query($koneksi, "INSERT INTO kelas (kode_mk, nidn, id_semester, nama_kelas, hari, jam_mulai, jam_selesai) 
                                      VALUES ('$mk', '$dosen', '$id_smt', '$nama', '$hari', '$mulai', '$selesai')");

    if ($simpan) { 
        echo "<script>alert('✅ Berhasil membuka kelas!'); window.location='index.php';</script>"; 
    } else { 
        echo "<script>alert('❌ Gagal: " . mysqli_error($koneksi) . "');</script>"; 
    }
}

$nama_user = "Administrator"; 
$role_label = "System Admin";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buka Kelas - SIPRESMA</title>
    
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

        /* --- GLOBAL --- */
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

        /* --- CARD FORM --- */
        .card-modern {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius-xl);
            padding: 2.5rem; 
            box-shadow: 0 10px 40px -5px rgba(0,0,0,0.05);
            max-width: 800px; /* Lebar untuk form jadwal */
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
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            background: white;
        }

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

        .icon-header {
            width: 60px; height: 60px; 
            background: #ecfdf5; color: var(--primary);
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 0 8px rgba(236, 253, 245, 0.5);
        }
        
        .alert-info-glass {
            background: rgba(239, 246, 255, 0.7); border: 1px solid #dbeafe; color: #1e40af;
            border-radius: 12px; padding: 0.8rem; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; margin-bottom: 2rem;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="../dashboard/index.php">
                <div class="logo-box">
                    <iconify-icon icon="solar:infinity-bold" width="24"></iconify-icon>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 brand-text">SIPRESMA</h5>
                    <p class="mb-0 brand-sub">ACADEMIC DASHBOARD</p>
                </div>
            </a>
            
            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-block text-end">
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><?php echo $nama_user; ?></p>
                    <p class="mb-0 text-success d-flex align-items-center justify-content-end gap-1" style="font-size: 0.75rem; font-weight: 600;">
                        <iconify-icon icon="solar:record-circle-bold" style="font-size: 8px;"></iconify-icon> <?php echo $role_label; ?>
                    </p>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-white rounded-circle border p-1 shadow-sm transition-hover">
                             <iconify-icon icon="solar:user-circle-bold-duotone" width="40" class="text-primary"></iconify-icon>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-2">
                        <li><h6 class="dropdown-header text-uppercase small fw-bold">Account</h6></li>
                        <li><a class="dropdown-item rounded-3 mb-1" href="#"><iconify-icon icon="solar:settings-bold-duotone" class="me-2 text-primary"></iconify-icon>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item rounded-3 text-danger fw-bold" href="../logout.php"><iconify-icon icon="solar:logout-2-bold-duotone" class="me-2"></iconify-icon>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="card-modern animate-entry">
            <div class="text-center mb-4">
                <div class="icon-header">
                    <iconify-icon icon="solar:black-hole-bold-duotone" style="font-size: 1.8rem;"></iconify-icon>
                </div>
                <h4 class="fw-bold mb-1">Buka Kelas Baru</h4>
                <p class="text-muted small">Atur jadwal dan dosen pengampu.</p>
            </div>
            
            <div class="alert-info-glass">
                <iconify-icon icon="solar:calendar-bold"></iconify-icon>
                <div>Semester Aktif: <strong><?php echo $smt['nama_semester']; ?></strong></div>
            </div>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mata Kuliah</label>
                        <select name="kode_mk" class="form-select" required>
                            <option value="">-- Pilih MK --</option>
                            <?php
                            $q_mk = mysqli_query($koneksi, "SELECT * FROM matakuliah ORDER BY nama_mk ASC");
                            while($m = mysqli_fetch_array($q_mk)){
                                echo "<option value='$m[kode_mk]'>$m[nama_mk] ($m[sks] SKS)</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dosen Pengajar</label>
                        <select name="nidn" class="form-select" required>
                            <option value="">-- Pilih Dosen --</option>
                            <?php
                            $q_d = mysqli_query($koneksi, "SELECT * FROM dosen ORDER BY nama_lengkap ASC");
                            while($d = mysqli_fetch_array($q_d)){
                                echo "<option value='$d[nidn]'>$d[nama_lengkap]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" placeholder="Cth: A, B, atau Pagi" required>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Hari</label>
                        <select name="hari" class="form-select" required>
                            <option>Senin</option><option>Selasa</option><option>Rabu</option>
                            <option>Kamis</option><option>Jumat</option><option>Sabtu</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" required>
                    </div>
                </div>

                <button type="submit" name="simpan" class="btn-gradient-primary">
                    Simpan Jadwal Kelas
                </button>
                
                <a href="index.php" class="btn-back">
                    <iconify-icon icon="solar:arrow-left-linear" style="position: relative; top: 1px;"></iconify-icon> Batal & Kembali
                </a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>