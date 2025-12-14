<?php
session_start();
// --- SECURITY CHECK ---
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

include '../config/koneksi.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id'"));

// LOGIC UPDATE
if (isset($_POST['update'])) {
    $user = $_POST['username'];
    $pass = $_POST['password']; 
    $role = $_POST['role'];
    
    // Cek apakah password diisi atau kosong
    if (!empty($pass)) { 
        // Update dengan password baru
        $query = "UPDATE user SET username='$user', password='$pass', role='$role' WHERE id_user='$id'"; 
    } else { 
        // Update tanpa ganti password
        $query = "UPDATE user SET username='$user', role='$role' WHERE id_user='$id'"; 
    }
    
    if (mysqli_query($koneksi, $query)) { 
        echo "<script>alert('✅ Data user berhasil diperbarui!'); window.location='index.php';</script>"; 
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
    <title>Edit User - SIPRESMA</title>
    
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
            --warning: #f59e0b;       
            --radius-xl: 24px;
            --nav-height: 80px;
        }

        /* --- GLOBAL & ANIMATION --- */
        /* FIX GESER: Paksa scrollbar selalu ada */
        html { overflow-y: scroll; } 
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            
            /* BACKGROUND: Mint Green + Dot Matrix (KONSISTEN) */
            background-color: #f0fdf4; 
            background-image: 
                radial-gradient(#86efac 1.2px, transparent 1.2px), 
                radial-gradient(circle at top center, rgba(16, 185, 129, 0.1) 0%, rgba(240, 253, 244, 0) 70%); 
            background-size: 24px 24px, 100% 100%;
            background-attachment: fixed;
        }

        /* Keyframes: Animasi Entry */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-entry {
            animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0; 
        }

        /* --- NAVBAR (GLASSMORPHISM) --- */
        .navbar-clean {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
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

        /* --- CARD FORM (CENTERED GLASS) --- */
        .card-modern {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius-xl);
            padding: 2.5rem; 
            box-shadow: 0 10px 40px -5px rgba(0,0,0,0.05);
            max-width: 500px;
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
            border-color: var(--warning); /* Fokus warna Amber */
            outline: none;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
            background: white;
        }

        /* --- SPECIAL ALERT (GLASS) --- */
        .alert-reset { 
            background: rgba(255, 251, 235, 0.6); 
            border: 1px solid #fcd34d; 
            color: #b45309; 
            padding: 0.8rem; 
            border-radius: 12px; 
            font-size: 0.85rem; 
            display: flex; gap: 10px; align-items: center; 
            margin-bottom: 5px; 
            backdrop-filter: blur(5px);
        }

        /* --- BUTTONS --- */
        /* Tombol Update (Gradient Amber - Biar beda dari Create) */
        .btn-gradient-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 50px;
            font-weight: 700; width: 100%;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3); transition: all 0.2s;
        }
        .btn-gradient-warning:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4); color: white; }

        .btn-back {
            display: block; text-align: center; margin-top: 1.5rem;
            color: var(--text-muted); text-decoration: none !important;
            font-weight: 600; font-size: 0.9rem; transition: 0.2s;
        }
        .btn-back:hover { color: var(--primary); }

        /* Icon Header Form */
        .icon-header {
            width: 60px; height: 60px; 
            background: #fffbeb; color: var(--warning);
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 0 8px rgba(255, 251, 235, 0.5);
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
                        <li><a class="dropdown-item rounded-3 mb-1" href="#">
                            <iconify-icon icon="solar:settings-bold-duotone" class="me-2 text-primary"></iconify-icon>Settings
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item rounded-3 text-danger fw-bold" href="../logout.php">
                            <iconify-icon icon="solar:logout-2-bold-duotone" class="me-2"></iconify-icon>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mb-5">
        
        <div class="card-modern animate-entry">
            <div class="text-center mb-4">
                <div class="icon-header">
                    <iconify-icon icon="solar:pen-new-square-bold-duotone" style="font-size: 1.8rem;"></iconify-icon>
                </div>
                <h4 class="fw-bold mb-1">Edit Akun User</h4>
                <p class="text-muted small">Update data login atau reset password.</p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $data['username']; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-warning">Password Baru (Opsional)</label>
                    <div class="alert-reset">
                        <iconify-icon icon="solar:key-minimalistic-bold" class="fs-5"></iconify-icon>
                        <div>Biarkan kosong jika tidak ingin mengganti password lama.</div>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password baru...">
                </div>

                <div class="mb-4">
                    <label class="form-label">Role Akses</label>
                    <select name="role" class="form-select">
                        <option value="admin" <?php if($data['role']=='admin') echo 'selected'; ?>>Administrator</option>
                        <option value="mahasiswa" <?php if($data['role']=='mahasiswa') echo 'selected'; ?>>Mahasiswa</option>
                        <option value="dosen" <?php if($data['role']=='dosen') echo 'selected'; ?>>Dosen</option>
                    </select>
                </div>
                
                <button type="submit" name="update" class="btn-gradient-warning">
                    <iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon> Simpan Perubahan
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