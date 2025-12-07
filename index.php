<?php
session_start();
include 'config/koneksi.php';

// --- LOGIKA LOGIN (TIDAK DIUBAH DARI VERSI LAMA) ---
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        $_SESSION['id_user']  = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";

        $_SESSION['nama_lengkap'] = $data['username'];

        if ($data['role'] == 'mahasiswa') {
            $id = $data['id_user'];
            $q_mhs = mysqli_query($koneksi, "SELECT nama_lengkap FROM mahasiswa WHERE id_user='$id'");
            if ($row = mysqli_fetch_assoc($q_mhs)) {
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            }
        }

        // Arahkan sesuai Role
        if ($data['role'] == 'admin') {
            header("Location: dashboard/index.php");
        } else if ($data['role'] == 'mahasiswa') {
            header("Location: dashboard/welcome_mhs.php");
        } else if ($data['role'] == 'dosen') {
            header("Location: dashboard/welcome_dosen.php");
        }
        
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPRESMA UNNES</title>
    
    <!-- Bootstrap & Fonts -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            --primary: #10b981;       /* Emerald Green SIPRESMA */
            --primary-dark: #047857;  /* Darker Emerald */
            --unnes-yellow: #f59e0b;  /* Accent for Awards */
            --bg-body: #ffffff;       
            --text-main: #0f172a;     
            --text-muted: #64748b;    
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            overflow-x: hidden;
            background-color: var(--bg-body);
        }

        /* --- Layout Split Screen --- */
        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* --- LEFT SIDE (Brand & Features) --- */
        .left-panel {
            flex: 1.3; /* Lebih lebar sedikit biar gambar lega */
            position: relative;
            background-image: url('https://cloud.jpnn.com/photo/jatim/news/normal/2022/06/28/universitas-negeri-semarang-unnes-foto-humas-unnes-fbo8p-uyqz.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            color: white;
            overflow: hidden;
        }

        /* Overlay Gradient Hijau Unnes agar text terbaca */
        .left-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.92) 0%, rgba(16, 185, 129, 0.85) 100%);
            z-index: 1;
        }

        .left-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 50px;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1rem;
            letter-spacing: -1px;
        }

        .hero-desc {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 3rem;
            font-weight: 300;
            line-height: 1.6;
        }

        /* Feature List Styling */
        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .feature-icon-box {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #fff;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .feature-text h5 { margin: 0; font-weight: 700; font-size: 1.1rem; }
        .feature-text p { margin: 0; font-size: 0.9rem; opacity: 0.8; }


        /* --- RIGHT SIDE (Login Form) --- */
        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            padding: 2rem;
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }

        .login-header { margin-bottom: 2.5rem; }
        .login-title { font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; }
        .login-subtitle { color: var(--text-muted); }

        /* Form Controls */
        .form-group { margin-bottom: 1.5rem; }
        .form-label { font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 0.5rem; display: block; }
        
        .form-control-custom {
            width: 100%;
            padding: 1rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .btn-login:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        }

        .alert-custom {
            background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c;
            padding: 1rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 10px;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 991px) {
            .login-container { flex-direction: column; }
            .left-panel { flex: none; height: 320px; padding: 2rem; justify-content: flex-end; }
            .hero-title { font-size: 2rem; }
            .hero-desc, .feature-list, .brand-badge { display: none; } /* Sembunyikan detail di mobile biar rapi */
            .right-panel { flex: 1; padding: 3rem 1.5rem; }
        }
    </style>
</head>
<body>

<div class="login-container">
    
    <!-- LEFT PANEL: Branding & Value Proposition -->
    <div class="left-panel">
        <div class="left-content">
            <div class="brand-badge">
                <iconify-icon icon="mdi:school-outline"></iconify-icon>
                UNIVERSITAS NEGERI SEMARANG
            </div>

            <h1 class="hero-title">Wujudkan Prestasi<br>Tanpa Batas.</h1>
            <p class="hero-desc">
                Selamat datang di <strong>SIPRESMA</strong> (Sistem Informasi Prestasi Mahasiswa). 
                Platform cerdas untuk monitoring akademik dan pengembangan potensi mahasiswa.
            </p>

            <div class="feature-list">
                <!-- Fitur 1: EWS -->
                <div class="feature-item">
                    <div class="feature-icon-box">
                        <iconify-icon icon="solar:shield-warning-bold-duotone"></iconify-icon>
                    </div>
                    <div class="feature-text">
                        <h5>Early Warning System</h5>
                        <p>Deteksi dini risiko akademik mahasiswa secara real-time.</p>
                    </div>
                </div>

                <!-- Fitur 2: Matchmaking -->
                <div class="feature-item">
                    <div class="feature-icon-box">
                        <iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon>
                    </div>
                    <div class="feature-text">
                        <h5>Peer Matchmaking</h5>
                        <p>Kolaborasi belajar antar mahasiswa berprestasi.</p>
                    </div>
                </div>

                <!-- Fitur 3: Prestasi -->
                <div class="feature-item">
                    <div class="feature-icon-box" style="color: var(--unnes-yellow);">
                        <iconify-icon icon="solar:cup-star-bold-duotone"></iconify-icon>
                    </div>
                    <div class="feature-text">
                        <h5>Manajemen Prestasi</h5>
                        <p>Rekapitulasi dan validasi pencapaian tingkat nasional.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Login Form -->
    <div class="right-panel">
        <div class="login-wrapper">
            <div class="login-header">
                <div style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 28px; margin-bottom: 1rem;">
                    <iconify-icon icon="solar:infinity-bold"></iconify-icon>
                </div>
                <h2 class="login-title">Login Akun</h2>
                <p class="login-subtitle">Masuk untuk mengakses dashboard SIPRESMA.</p>
            </div>

            <?php if(isset($error)) { ?>
                <div class="alert-custom">
                    <iconify-icon icon="solar:danger-circle-bold" class="fs-5"></iconify-icon>
                    <div><?php echo $error; ?></div>
                </div>
            <?php } ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label">Username / NIM / NIP</label>
                    <input type="text" name="username" class="form-control-custom" placeholder="Masukkan ID Pengguna" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control-custom" placeholder="••••••••" required>
                </div>

                <button type="submit" name="login" class="btn-login">
                    MASUK SEKARANG <iconify-icon icon="solar:arrow-right-linear" style="vertical-align: middle; margin-left: 5px;"></iconify-icon>
                </button>
            </form>

            <div class="text-center mt-4" style="color: #94a3b8; font-size: 0.85rem;">
                &copy; 2025 SIPRESMA Team - Unnes
            </div>
        </div>
    </div>

</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>