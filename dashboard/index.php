<?php
session_start();
include '../config/koneksi.php';

// --- SECURITY & ACCESS CONTROL ---
// Memastikan sesi aktif dan role adalah admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// --- DATA FETCHING (REAL-TIME) ---

// 1. STATISTIK UTAMA (Card Atas)
$jml_mhs    = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mahasiswa"));
$jml_dosen  = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM dosen"));
$jml_matkul = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM matakuliah"));
// EWS: Mahasiswa dengan IPK < 2.50 (Perlu Bantuan)
$jml_rawan  = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE ipk_terakhir < 2.50 AND ipk_terakhir > 0"));

// 2. DATA UNTUK CHART DONUT (Sebaran Prodi)
$q_grafik_prodi = mysqli_query($koneksi, "
    SELECT p.nama_prodi, COUNT(m.nim) as jumlah 
    FROM mahasiswa m 
    JOIN prodi p ON m.id_prodi = p.id_prodi 
    GROUP BY p.nama_prodi
");
$label_prodi = []; 
$data_prodi = [];
while($row = mysqli_fetch_assoc($q_grafik_prodi)){ 
    $label_prodi[] = $row['nama_prodi']; 
    $data_prodi[]  = $row['jumlah']; 
}

// 3. DATA UNTUK CHART BAR (Rata-rata IPK per Prodi)
$q_grafik_ipk = mysqli_query($koneksi, "
    SELECT p.kode_prodi, AVG(m.ipk_terakhir) as rata_ipk 
    FROM mahasiswa m 
    JOIN prodi p ON m.id_prodi = p.id_prodi 
    WHERE m.ipk_terakhir > 0 
    GROUP BY p.kode_prodi
");
$label_ipk = []; 
$data_ipk = [];
while($row = mysqli_fetch_assoc($q_grafik_ipk)){ 
    $label_ipk[] = $row['kode_prodi']; 
    $data_ipk[]  = number_format($row['rata_ipk'], 2); 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* --- CONFIGURATION & VARIABLES --- */
        :root {
            /* Palette Hijau UNNES (Disamakan dengan Login Page) */
            --primary: #10b981;       
            --primary-dark: #047857;
            --primary-soft: #d1fae5;
            
            /* Text Colors */
            --text-main: #0f172a;     
            --text-muted: #64748b;    
            
            /* Status Indicators */
            --accent-blue: #3b82f6;   /* Info */
            --accent-green: #10b981;  /* Success */
            --accent-amber: #f59e0b;  /* Warning */
            --accent-rose: #f43f5e;   /* Danger / EWS */
            
            /* UI Dimensions */
            --radius-xl: 24px;
            --radius-lg: 16px;
            --radius-md: 12px;
            --nav-height: 80px;
        }

        /* --- BODY & BACKGROUND --- */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;

            /* BACKGROUND: Mint Green Lembut + Pola Dot Matrix */
            background-color: #f0fdf4; 
            background-image: 
                radial-gradient(#86efac 1.2px, transparent 1.2px), 
                radial-gradient(circle at top center, rgba(16, 185, 129, 0.1) 0%, rgba(240, 253, 244, 0) 70%); 
            background-size: 24px 24px, 100% 100%;
            background-attachment: fixed;
        }

        /* --- NAVBAR GLASSMORPHISM --- */
        .navbar-clean {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(16, 185, 129, 0.1); 
            border-top: 3px solid var(--primary); 
            height: var(--nav-height);
            position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 4px 20px -10px rgba(16, 185, 129, 0.15);
            transition: all 0.3s ease;
        }
        
        /* Logo Styling */
        .logo-box {
            background: linear-gradient(135deg, #10b981, #047857);
            color: white;
            padding: 8px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }
        .brand-text { letter-spacing: -0.5px; color: var(--text-main); }
        .brand-sub { letter-spacing: 1.5px; font-weight: 700; color: var(--text-muted); font-size: 10px; }

        /* --- WELCOME PILL (Revisi: Lebih Slim & Clean) --- */
        .welcome-pill {
            /* Ganti background jadi Putih Transparan (Kaca) */
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            
            /* Kasih Border Hijau Tipis biar tetep ada branding UNNES */
            border: 1px solid rgba(16, 185, 129, 0.3);
            
            /* Shadow hijau pudar biar 'pop-up' dikit */
            box-shadow: 0 8px 20px -5px rgba(16, 185, 129, 0.15);
            
            /* Ukuran dikecilin (Padding dikurangi) biar ga kegedean */
            padding: 0.8rem 1.5rem; 
            border-radius: 50px;
            display: inline-flex; align-items: center; gap: 15px;
            
            /* Text jadi gelap, bukan putih lagi */
            color: var(--text-main);
            transition: all 0.3s ease;
        }

        .welcome-pill:hover {
            transform: translateY(-2px);
            border-color: var(--primary); /* Pas di-hover border jadi hijau jelas */
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.25);
        }

        /* Pastikan warna teks di dalamnya bener (Override style sebelumnya) */
        .welcome-pill h5 { 
            color: var(--text-main) !important; 
            font-size: 1.1rem; /* Font judul dikecilin dikit */
            font-weight: 700; 
            margin: 0; 
        }

        .welcome-pill small { 
            color: var(--text-muted) !important; 
            font-weight: 500; 
        }

        /* --- DATE BADGE (BARU: Biar seragam sama Welcome Pill) --- */
        .date-badge {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(16, 185, 129, 0.3);
            box-shadow: 0 8px 20px -5px rgba(16, 185, 129, 0.15);
            
            /* Typography & Size */
            color: var(--text-main);
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.8rem 1.5rem; /* Ukuran disamakan dgn welcome-pill */
            border-radius: 50px;
            
            display: inline-flex; align-items: center; gap: 10px;
            transition: all 0.3s ease;
        }

        .date-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.25);
            border-color: var(--primary);
        }
        
        .date-badge iconify-icon {
            color: var(--primary); 
            font-size: 1.2rem;
        }

        /* --- CARDS DESIGN (GLASS) --- */
        .card-modern {
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            height: 100%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; overflow: hidden;
            box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
        }
        .card-modern:hover { 
            transform: translateY(-8px); 
            background: white;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08); 
            border-color: white;
        }

        /* Card Color Accents (Top Border) */
        .card-blue  { border-top: 4px solid var(--accent-blue); background: linear-gradient(to bottom, #eff6ff 0%, rgba(255,255,255,0.9) 40%); }
        .card-green { border-top: 4px solid var(--accent-green); background: linear-gradient(to bottom, #ecfdf5 0%, rgba(255,255,255,0.9) 40%); }
        .card-amber { border-top: 4px solid var(--accent-amber); background: linear-gradient(to bottom, #fffbeb 0%, rgba(255,255,255,0.9) 40%); }
        .card-red   { border-top: 4px solid var(--accent-rose); background: linear-gradient(to bottom, #fef2f2 0%, rgba(255,255,255,0.9) 40%); }

        /* Typography dalam Card */
        .stat-value { font-size: 2.5rem; font-weight: 800; letter-spacing: -1.5px; color: var(--text-main); margin-top: 0.5rem; line-height: 1; }
        .stat-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; opacity: 0.9; }

        /* Floating Icons */
        .icon-box {
            width: 56px; height: 56px; border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            background: white;
            box-shadow: 0 8px 16px -4px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .card-modern:hover .icon-box { transform: scale(1.1) rotate(10deg); }
        .icon-box.blue { color: var(--accent-blue); }
        .icon-box.green { color: var(--accent-green); }
        .icon-box.amber { color: var(--accent-amber); }
        .icon-box.rose { color: var(--accent-rose); }

        /* --- CHART & TABLE AREA --- */
        .chart-container { position: relative; height: 320px; width: 100%; }
        
        .table-wrapper {
            max-height: 400px; overflow-y: auto;
            border-radius: 0 0 var(--radius-xl) var(--radius-xl);
            scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;
        }
        .table-custom thead th {
            background: #f8fafc; position: sticky; top: 0; z-index: 10;
            color: var(--text-muted); font-size: 0.7rem; font-weight: 800; 
            text-transform: uppercase; letter-spacing: 0.1em;
            padding: 1.2rem; border-bottom: 1px solid #e2e8f0;
        }
        .table-custom tbody td { 
            padding: 1.2rem; border-bottom: 1px solid #f1f5f9; 
            vertical-align: middle; font-size: 0.9rem; font-weight: 500;
        }
        .table-custom tr:last-child td { border-bottom: none; }
        .table-custom tr:hover td { background-color: #f8fafc; }

        /* Badges */
        .badge-pill { padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; }
        .badge-risk { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

        /* --- MENU GRID (Quick Access) --- */
        .menu-grid-item {
            background: white; border: 1px solid #e2e8f0;
            border-radius: var(--radius-lg); padding: 1.8rem 1rem;
            text-align: center; text-decoration: none; display: block;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); 
        }
        .menu-grid-item:hover {
            border-color: var(--primary);
            transform: translateY(-8px);
            box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.2); /* Green glow shadow */
        }
        .menu-icon { 
            font-size: 2.8rem; margin-bottom: 1rem; 
            color: var(--text-muted); transition: 0.3s; 
            /* Gradient Icon Effect */
            background: linear-gradient(to bottom right, #cbd5e1, #64748b);
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent;
        }
        .menu-grid-item:hover .menu-icon { 
            background: linear-gradient(to bottom right, #10b981, #059669);
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent;
            transform: scale(1.15); 
        }
        .menu-label { font-weight: 600; color: var(--text-main); font-size: 0.95rem; }

        /* Custom Button Styles for EWS */
        .btn-custom-outline {
            border: 1px solid #e2e8f0; background: white; color: var(--text-muted); font-weight: 600; font-size: 0.85rem;
            padding: 8px 16px; border-radius: 50px; transition: all 0.2s;
        }
        .btn-custom-outline:hover { border-color: var(--text-muted); color: var(--text-main); background: #f8fafc; }
        
        .btn-custom-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626); border: none; color: white; font-weight: 700; font-size: 0.85rem;
            padding: 8px 18px; border-radius: 50px; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); transition: all 0.2s;
        }
        .btn-custom-danger:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(239, 68, 68, 0.4); color: white; }

    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="#">
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
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Administrator</p>
                    <p class="mb-0 text-success d-flex align-items-center justify-content-end gap-1" style="font-size: 0.75rem; font-weight: 600;">
                        <iconify-icon icon="solar:record-circle-bold" style="font-size: 8px;"></iconify-icon> System Admin
                    </p>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-white rounded-circle border p-1 shadow-sm" style="transition: 0.2s;">
                             <iconify-icon icon="solar:user-circle-bold-duotone" width="40" class="text-primary"></iconify-icon>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-2">
                        <li><h6 class="dropdown-header text-uppercase small fw-bold text-muted">Account</h6></li>
                        <li><a class="dropdown-item rounded-3 mb-1 fw-medium" href="#"><iconify-icon icon="solar:settings-bold-duotone" class="me-2 text-primary"></iconify-icon>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item rounded-3 text-danger fw-bold" href="../logout.php">
                            <iconify-icon icon="solar:logout-2-bold-duotone" class="me-2"></iconify-icon>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mb-5 pb-5">
        
        <div class="row align-items-end mb-5">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="welcome-pill">
                    <span style="font-size: 2.5rem;">👋</span>
                    <div>
                        <h5 class="mb-1">Selamat Datang Kembali!</h5>
                        <small>Pantau kesehatan akademik dan prestasi mahasiswa hari ini.</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="date-badge">
                    <iconify-icon icon="solar:calendar-bold-duotone"></iconify-icon>
                    <span><?php echo date('d F Y'); ?></span>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card-modern card-blue">
                    <div class="d-flex justify-content-between align-items-start h-100">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div class="stat-label text-primary">Total Mahasiswa</div>
                            <div class="stat-value"><?php echo number_format($jml_mhs); ?></div>
                            <div class="mt-2 text-muted small"><i class="text-primary fw-bold">+5%</i> bulan ini</div>
                        </div>
                        <div class="icon-box blue">
                            <iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-modern card-green">
                    <div class="d-flex justify-content-between align-items-start h-100">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div class="stat-label text-success">Total Dosen</div>
                            <div class="stat-value"><?php echo number_format($jml_dosen); ?></div>
                            <div class="mt-2 text-muted small">Aktif mengajar</div>
                        </div>
                        <div class="icon-box green">
                            <iconify-icon icon="solar:user-id-bold-duotone"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-modern card-amber">
                    <div class="d-flex justify-content-between align-items-start h-100">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div class="stat-label text-warning">Mata Kuliah</div>
                            <div class="stat-value"><?php echo number_format($jml_matkul); ?></div>
                            <div class="mt-2 text-muted small">Kurikulum 2024</div>
                        </div>
                        <div class="icon-box amber">
                            <iconify-icon icon="solar:book-bookmark-bold-duotone"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-modern card-red">
                    <div class="d-flex justify-content-between align-items-start h-100">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div class="stat-label text-danger">Perlu Perhatian</div>
                            <div class="stat-value text-danger"><?php echo number_format($jml_rawan); ?></div>
                            <div class="mt-2 text-danger small fw-bold">IPK < 2.50</div>
                        </div>
                        <div class="icon-box rose">
                            <iconify-icon icon="solar:bell-bing-bold-duotone"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="d-flex justify-content-between align-items-center mb-4 ps-2">
                        <div>
                            <h6 class="fw-bold mb-1 fs-5">Rata-rata IPK per Prodi</h6>
                            <small class="text-muted">Analisis performa akademik per jurusan</small>
                        </div>
                        <button class="btn btn-sm btn-outline-light text-muted border px-3 rounded-pill">
                            <iconify-icon icon="solar:menu-dots-bold"></iconify-icon> Option
                        </button>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartIPK"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card-modern">
                    <h6 class="fw-bold mb-1 fs-5 ps-2">Sebaran Mahasiswa</h6>
                    <small class="text-muted ps-2 mb-4 d-block">Proporsi mahasiswa aktif</small>
                    <div class="chart-container" style="height: 260px;">
                        <canvas id="chartProdi"></canvas>
                    </div>
                    <div class="text-center mt-3 p-3 bg-light rounded-4 border border-light">
                        <small class="text-muted fst-italic"><iconify-icon icon="solar:info-circle-bold" class="me-1"></iconify-icon> Data diperbarui secara real-time</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            
            <div class="col-lg-6">
                <div class="card-modern p-0 overflow-hidden border-0 shadow-sm h-100">
                    <div class="p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(to right, #fef2f2, #fff);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white p-2 rounded-circle shadow-sm text-danger d-flex">
                                <iconify-icon icon="solar:shield-warning-bold-duotone" width="28"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Early Warning System</h6>
                                <div class="text-secondary small">Mahasiswa Berisiko (IPK < 2.5)</div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="../peersupport/index.php" class="btn-custom-outline d-flex align-items-center gap-2 text-decoration-none">
                                <iconify-icon icon="solar:eye-bold"></iconify-icon> Lihat Data
                            </a>
                            <a href="auto_match.php" class="btn-custom-danger d-flex align-items-center gap-2 text-decoration-none" onclick="return confirm('Jalankan sistem Auto-Match untuk memasangkan mentor?')">
                                <iconify-icon icon="solar:bolt-bold"></iconify-icon> Auto-Match
                            </a>
                        </div>
                    </div>
                    
                    <div class="table-wrapper bg-white">
                        <table class="table table-custom mb-0 w-100">
                            <thead>
                                <tr>
                                    <th>Identitas Mahasiswa</th>
                                    <th class="text-center">IPK Saat Ini</th>
                                    <th class="text-end">Status Risiko</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q_ews = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE ipk_terakhir < 2.50 AND ipk_terakhir > 0 ORDER BY ipk_terakhir ASC LIMIT 10");
                                
                                // Handling jika data kosong
                                if(mysqli_num_rows($q_ews)==0){ 
                                    echo "<tr><td colspan='3' class='text-center py-5 text-muted'>
                                        <iconify-icon icon='solar:check-circle-bold' class='text-success fs-1 mb-2'></iconify-icon>
                                        <br>Semua mahasiswa aman!
                                    </td></tr>"; 
                                }
                                
                                // Loop Data
                                while($r=mysqli_fetch_assoc($q_ews)){ 
                                    echo "<tr>
                                        <td>
                                            <div class='fw-bold text-dark'>{$r['nama_lengkap']}</div>
                                            <div class='small text-muted'>NIM: {$r['nim']}</div>
                                        </td>
                                        <td class='text-center'>
                                            <span class='fw-bold text-danger fs-6'>{$r['ipk_terakhir']}</span>
                                        </td>
                                        <td class='text-end'>
                                            <span class='badge-pill badge-risk'>High Risk</span>
                                        </td>
                                    </tr>"; 
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-modern p-0 overflow-hidden border-0 shadow-sm h-100">
                    <div class="p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(to right, #fffbeb, #fff);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white p-2 rounded-circle shadow-sm text-warning d-flex">
                                <iconify-icon icon="solar:cup-star-bold-duotone" width="28"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Top Achievers</h6>
                                <div class="text-secondary small">Prestasi Terverifikasi</div>
                            </div>
                        </div>
                        <a href="../prestasi/index.php" class="btn-custom-outline text-warning border-warning d-flex align-items-center gap-2 text-decoration-none">
                            Lihat Semua <iconify-icon icon="solar:arrow-right-broken"></iconify-icon>
                        </a>
                    </div>

                    <div class="table-wrapper bg-white">
                        <table class="table table-custom mb-0 w-100">
                            <thead>
                                <tr>
                                    <th>Nama Mahasiswa</th>
                                    <th class="text-center">IPK</th>
                                    <th class="text-end">Total Prestasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query untuk mengambil mahasiswa berprestasi (IPK >= 3.5 dan punya minimal 1 prestasi valid)
                                $q_mw = mysqli_query($koneksi, "
                                    SELECT m.nama_lengkap, m.ipk_terakhir, 
                                    (SELECT COUNT(*) FROM prestasi p WHERE p.nim = m.nim AND p.status_validasi = 'valid') as jum 
                                    FROM mahasiswa m 
                                    WHERE m.ipk_terakhir >= 3.5 
                                    HAVING jum > 0 
                                    ORDER BY m.ipk_terakhir DESC 
                                    LIMIT 10
                                ");

                                if(mysqli_num_rows($q_mw)==0){ 
                                    echo "<tr><td colspan='3' class='text-center py-5 text-muted'>Belum ada data prestasi.</td></tr>"; 
                                }
                                while($r=mysqli_fetch_assoc($q_mw)){ 
                                    echo "<tr>
                                        <td><div class='fw-bold text-dark'>{$r['nama_lengkap']}</div></td>
                                        <td class='text-center'><span class='fw-bold text-success'>{$r['ipk_terakhir']}</span></td>
                                        <td class='text-end'>
                                            <span class='badge-pill badge-success'>{$r['jum']} Valid</span>
                                        </td>
                                    </tr>"; 
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-4 gap-3">
            <h6 class="fw-bold mb-0 ps-1 text-secondary text-uppercase" style="letter-spacing: 1.5px; font-size: 0.8rem;">Master Data Management</h6>
            <div class="flex-grow-1 border-bottom"></div>
        </div>
        
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <a href="../user/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:shield-user-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">User System</p>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="../prodi/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:buildings-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Program Studi</p>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="../dosen/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Data Dosen</p>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="../mahasiswa/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:square-academic-cap-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Data Mahasiswa</p>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="../matakuliah/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:notebook-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Mata Kuliah</p>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="../kelas/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:black-hole-line-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Kelas Perkuliahan</p>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="../semester/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:calendar-date-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Semester</p>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="../prestasi/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:medal-star-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Validasi Prestasi</p>
                </a>
            </div>
        </div>

    </div>
    
    <footer class="text-center py-5 text-muted small">
        <div class="mb-2">
            <iconify-icon icon="solar:infinity-bold" class="text-success"></iconify-icon>
        </div>
        <p class="mb-0 fw-medium">&copy; <?php echo date('Y'); ?> SIPRESMA UNNES. All rights reserved.</p>
        <p class="mb-0" style="opacity: 0.6;">Developed for Academic Excellence</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // --- GLOBAL CHART DEFAULTS ---
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.scale.grid.display = false; // Bersih tanpa grid

        // 1. CHART PRODI (Doughnut Modern)
        new Chart(document.getElementById('chartProdi'), {
            type: 'doughnut',
            data: { 
                labels: <?php echo json_encode($label_prodi); ?>, 
                datasets: [{ 
                    data: <?php echo json_encode($data_prodi); ?>, 
                    // Palette Warna Modern (Sesuai tema)
                    backgroundColor: [
                        '#10b981', // Primary Green
                        '#3b82f6', // Blue
                        '#f59e0b', // Amber
                        '#ef4444', // Red
                        '#8b5cf6', // Purple
                        '#ec4899'  // Pink
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 15
                }] 
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%', // Bolong tengah
                plugins: { 
                    legend: { 
                        position: 'right', 
                        labels: { 
                            usePointStyle: true, 
                            pointStyle: 'circle',
                            boxWidth: 8, 
                            padding: 20, 
                            font: {size: 11} 
                        } 
                    } 
                },
                animation: { 
                    animateScale: true, 
                    animateRotate: true 
                }
            }
        });
        
        // 2. CHART IPK (Bar Gradient)
        const ctxIPK = document.getElementById('chartIPK').getContext('2d');
        // Buat Gradient Vertikal untuk batang chart
        const gradientIPK = ctxIPK.createLinearGradient(0, 0, 0, 400);
        gradientIPK.addColorStop(0, '#10b981'); // Hijau Atas (Pekat)
        gradientIPK.addColorStop(1, 'rgba(16, 185, 129, 0.2)'); // Hijau Bawah (Pudar)

        new Chart(ctxIPK, {
            type: 'bar',
            data: { 
                labels: <?php echo json_encode($label_ipk); ?>, 
                datasets: [{ 
                    label: 'Rata-rata IPK', 
                    data: <?php echo json_encode($data_ipk); ?>, 
                    backgroundColor: gradientIPK,
                    borderRadius: 8, // Ujung batang tumpul
                    barThickness: 40,
                    maxBarThickness: 50,
                    hoverBackgroundColor: '#059669' // Warna saat di-hover
                }] 
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        max: 4.0, 
                        grid: { 
                            display: true,
                            borderDash: [5, 5], 
                            color: '#f1f5f9',
                            drawBorder: false 
                        },
                        ticks: { font: {size: 11} }
                    },
                    x: { 
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: {size: 11} }
                    }
                },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: {size: 13},
                        bodyFont: {size: 13}
                    }
                }
            }
        });
    </script>
</body>
</html>