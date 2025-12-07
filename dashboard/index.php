<?php
session_start();
include '../config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// 1. STATISTIK RINGKAS
$jml_mhs = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mahasiswa"));
$jml_dosen = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM dosen"));
$jml_matkul = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM matakuliah"));
$jml_rawan = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE ipk_terakhir < 2.50 AND ipk_terakhir > 0"));

// 2. DATA UNTUK GRAFIK DONUT
$q_grafik_prodi = mysqli_query($koneksi, "SELECT p.nama_prodi, COUNT(m.nim) as jumlah FROM mahasiswa m JOIN prodi p ON m.id_prodi = p.id_prodi GROUP BY p.nama_prodi");
$label_prodi = []; $data_prodi = [];
while($row = mysqli_fetch_assoc($q_grafik_prodi)){ $label_prodi[] = $row['nama_prodi']; $data_prodi[] = $row['jumlah']; }

// 3. DATA UNTUK GRAFIK BAR
$q_grafik_ipk = mysqli_query($koneksi, "SELECT p.kode_prodi, AVG(m.ipk_terakhir) as rata_ipk FROM mahasiswa m JOIN prodi p ON m.id_prodi = p.id_prodi WHERE m.ipk_terakhir > 0 GROUP BY p.kode_prodi");
$label_ipk = []; $data_ipk = [];
while($row = mysqli_fetch_assoc($q_grafik_ipk)){ $label_ipk[] = $row['kode_prodi']; $data_ipk[] = number_format($row['rata_ipk'], 2); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIPRESMA</title>
    
    <!-- Bootstrap 5 & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #10b981;       
            --primary-dark: #059669;
            --secondary: #64748b;     
            --bg-body: #f8fafc;       
            
            --text-main: #0f172a;     
            --text-muted: #64748b;    
            
            /* Warna Aksen Ringan */
            --accent-blue: #3b82f6;
            --accent-green: #10b981;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            
            /* BACKGROUND: Motif Dot Matrix lebih jelas tapi nyaman */
            background-color: #f1f5f9; 
            
            /* Layer 1: Dot grid lebih besar dikit (1.2px) warnanya slate halus */
            /* Layer 2: Vignette putih di tengah biar konten tetap fokus */
            background-image: 
                radial-gradient(#cbd5e1 1.2px, transparent 1.2px),
                radial-gradient(circle at center, rgba(255,255,255,0.8) 0%, rgba(241,245,249,0.5) 100%);
                
            background-size: 20px 20px, 100% 100%; /* Jarak antar titik 20px */
            background-attachment: fixed;
            
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* --- NAVBAR CLEAN --- */
        .navbar-clean {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .brand-text { color: var(--text-main); letter-spacing: -0.5px; }

        /* --- CARDS Colorful Gradients --- */
        .card-modern {
            background: white;
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: 20px;
            box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05);
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .card-modern:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08); 
        }

        /* Card Specific Backgrounds (Gradient Top) */
        .card-blue {
            background: linear-gradient(to bottom, #eff6ff 0%, #ffffff 50%);
            border-top: 4px solid var(--accent-blue);
        }
        .card-green {
            background: linear-gradient(to bottom, #ecfdf5 0%, #ffffff 50%);
            border-top: 4px solid var(--accent-green);
        }
        .card-amber {
            background: linear-gradient(to bottom, #fffbeb 0%, #ffffff 50%);
            border-top: 4px solid var(--accent-amber);
        }
        .card-red {
            background: linear-gradient(to bottom, #fef2f2 0%, #ffffff 50%);
            border-top: 4px solid var(--accent-rose);
        }

        /* Stats Styles */
        .stat-value { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; color: var(--text-main); margin-top: 0.5rem; }
        .stat-label { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted); opacity: 0.8; }
        
        /* Icon Boxes - Lebih solid dikit biar pop out dari background gradasi */
        .icon-box {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; margin-bottom: 0.5rem;
            background: white; /* Background putih biar ikon jelas */
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .icon-box.blue { color: var(--accent-blue); }
        .icon-box.green { color: var(--accent-green); }
        .icon-box.amber { color: var(--accent-amber); }
        .icon-box.rose { color: var(--accent-rose); }

        /* --- SCROLLABLE TABLE AREA --- */
        .table-scroll-area {
            max-height: 400px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .table-scroll-area::-webkit-scrollbar { width: 6px; }
        .table-scroll-area::-webkit-scrollbar-track { background: transparent; }
        .table-scroll-area::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }

        .table-custom thead th {
            background: #f8fafc; position: sticky; top: 0; z-index: 10;
            color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            padding: 1rem; border: none; box-shadow: 0 1px 0 #e2e8f0;
        }
        .table-custom tbody td { padding: 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 0.9rem; }
        
        /* Badges */
        .badge-status { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        .badge-risk { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-achievement { background: #eff6ff; color: #1e40af; border: 1px solid #dbeafe; }

        /* --- Welcome Pill --- */
        .welcome-pill {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 1);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
            padding: 0.8rem 1.5rem;
            border-radius: 100px;
            display: inline-flex; align-items: center; gap: 15px;
            margin-bottom: 2rem;
        }

        /* --- Menu Grid Item --- */
        .menu-grid-item {
            background: white; 
            border: 1px solid rgba(226, 232, 240, 1);
            border-radius: 20px; padding: 1.5rem; text-align: center; text-decoration: none;
            display: block; transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .menu-grid-item:hover {
            border-color: var(--primary); transform: translateY(-5px);
            box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.15);
        }
        .menu-icon { font-size: 2.5rem; margin-bottom: 0.8rem; color: var(--text-muted); transition: 0.2s; }
        .menu-grid-item:hover .menu-icon { color: var(--primary); transform: scale(1.1); }
        .menu-label { font-weight: 600; color: var(--text-main); margin: 0; font-size: 0.9rem; }

    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
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
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Administrator</p>
                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">System Admin</p>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-light rounded-circle border p-1">
                             <iconify-icon icon="solar:user-circle-bold" width="36" class="text-secondary"></iconify-icon>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                        <li><a class="dropdown-item rounded-3" href="../logout.php">
                            <iconify-icon icon="solar:logout-2-bold" class="me-2 text-danger"></iconify-icon>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5 pb-5">
        
        <!-- Welcome Section -->
        <div class="row align-items-end mb-4">
            <div class="col-md-8">
                <div class="welcome-pill">
                    <span style="font-size: 1.8rem;">👋</span>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Selamat Datang Kembali!</h5>
                        <small class="text-muted">Pantau kesehatan akademik dan prestasi mahasiswa hari ini.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mb-4">
                <div class="d-inline-flex align-items-center bg-white px-4 py-2 rounded-pill shadow-sm text-secondary border fw-medium">
                    <iconify-icon icon="solar:calendar-bold-duotone" class="me-2 text-primary fs-5"></iconify-icon>
                    <?php echo date('d F Y'); ?>
                </div>
            </div>
        </div>

        <!-- STAT CARDS (Sekarang Semua Berwarna) -->
        <div class="row g-4 mb-5">
            <!-- Mahasiswa (Blue) -->
            <div class="col-xl-3 col-md-6">
                <div class="card-modern card-blue">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label text-primary">Total Mahasiswa</div>
                            <div class="stat-value"><?php echo number_format($jml_mhs); ?></div>
                        </div>
                        <div class="icon-box blue">
                            <iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dosen (Green) -->
            <div class="col-xl-3 col-md-6">
                <div class="card-modern card-green">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label text-success">Total Dosen</div>
                            <div class="stat-value"><?php echo number_format($jml_dosen); ?></div>
                        </div>
                        <div class="icon-box green">
                            <iconify-icon icon="solar:user-id-bold-duotone"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Matkul (Amber) -->
            <div class="col-xl-3 col-md-6">
                <div class="card-modern card-amber">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label text-warning">Mata Kuliah</div>
                            <div class="stat-value"><?php echo number_format($jml_matkul); ?></div>
                        </div>
                        <div class="icon-box amber">
                            <iconify-icon icon="solar:book-bookmark-bold-duotone"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EWS (Red) -->
            <div class="col-xl-3 col-md-6">
                <div class="card-modern card-red">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label text-danger">Perlu Perhatian</div>
                            <div class="stat-value text-danger"><?php echo number_format($jml_rawan); ?></div>
                        </div>
                        <div class="icon-box rose">
                            <iconify-icon icon="solar:bell-bing-bold-duotone"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHARTS SECTION -->
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0">Rata-rata IPK per Prodi</h6>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="chartIPK"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-modern">
                    <h6 class="fw-bold mb-4">Sebaran Mahasiswa</h6>
                    <div style="height: 250px;">
                        <canvas id="chartProdi"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLES SECTION (Scrollable) -->
        <div class="row g-4 mb-5">
            <!-- EWS Table -->
            <div class="col-lg-6">
                <div class="card-modern h-100 p-0 overflow-hidden card-red border-0">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white bg-opacity-75">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box rose mb-0" style="width: 42px; height: 42px; font-size: 20px;">
                                <iconify-icon icon="solar:shield-warning-bold-duotone"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Early Warning System</h6>
                                <div class="text-secondary small">Mahasiswa berisiko </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                             <a href="../peersupport/index.php" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold px-3">
                                Lihat Data
                            </a>
                            <a href="auto_match.php" class="btn btn-danger btn-sm rounded-pill fw-bold px-3 d-flex align-items-center gap-1" onclick="return confirm('Jalankan Auto-Match?')">
                                <iconify-icon icon="solar:bolt-bold"></iconify-icon> Auto-Match
                            </a>
                        </div>
                    </div>
                    <!-- WRAPPER SCROLLABLE -->
                    <div class="table-scroll-area bg-white">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th class="text-center">IPK</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q_ews = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE ipk_terakhir < 2.50 AND ipk_terakhir > 0 ORDER BY ipk_terakhir ASC");
                                if(mysqli_num_rows($q_ews)==0){ 
                                    echo "<tr><td colspan='3' class='text-center py-5 text-muted'>Semua mahasiswa aman!</td></tr>"; 
                                }
                                while($r=mysqli_fetch_assoc($q_ews)){ 
                                    echo "<tr>
                                        <td>
                                            <div class='fw-bold text-dark'>{$r['nama_lengkap']}</div>
                                            <div class='small text-secondary'>{$r['nim']}</div>
                                        </td>
                                        <td class='text-center'><span class='fw-bold text-danger'>{$r['ipk_terakhir']}</span></td>
                                        <td class='text-end'>
                                            <span class='badge-status badge-risk'>Risiko Tinggi</span>
                                        </td>
                                    </tr>"; 
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Prestasi Table -->
            <div class="col-lg-6">
                <div class="card-modern h-100 p-0 overflow-hidden card-amber border-0">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white bg-opacity-75">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box amber mb-0" style="width: 42px; height: 42px; font-size: 20px;">
                                <iconify-icon icon="solar:cup-star-bold-duotone"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Top Students</h6>
                                <div class="text-secondary small">Mahasiswa berprestasi (Valid)</div>
                            </div>
                        </div>
                        <a href="../prestasi/index.php" class="btn btn-outline-warning btn-sm rounded-pill fw-bold px-3 text-dark">
                            Lihat Semua
                        </a>
                    </div>
                    <!-- WRAPPER SCROLLABLE -->
                    <div class="table-scroll-area bg-white">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th class="text-center">IPK</th>
                                    <th class="text-end">Capaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q_mw = mysqli_query($koneksi, "
                                    SELECT m.nama_lengkap, m.ipk_terakhir, 
                                    (SELECT COUNT(*) FROM prestasi p WHERE p.nim = m.nim AND p.status_validasi = 'valid') as jum 
                                    FROM mahasiswa m 
                                    WHERE m.ipk_terakhir >= 3.5 
                                    HAVING jum > 0
                                    ORDER BY m.ipk_terakhir DESC
                                ");

                                if(mysqli_num_rows($q_mw)==0){ 
                                    echo "<tr><td colspan='3' class='text-center py-5 text-muted'>Belum ada data.</td></tr>"; 
                                }
                                while($r=mysqli_fetch_assoc($q_mw)){ 
                                    echo "<tr>
                                        <td><div class='fw-bold text-dark'>{$r['nama_lengkap']}</div></td>
                                        <td class='text-center'><span class='fw-bold text-success'>{$r['ipk_terakhir']}</span></td>
                                        <td class='text-end'>
                                            <span class='badge-status badge-achievement'>{$r['jum']} Prestasi</span>
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

        <!-- QUICK MENU GRID -->
        <h6 class="fw-bold mb-4 ps-1 text-secondary text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Akses Menu Utama</h6>
        <div class="row g-3">
            <div class="col-6 col-md-4 col-lg-3">
                <a href="../user/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:user-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">User System</p>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="../prodi/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:buildings-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Program Studi</p>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="../dosen/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Data Dosen</p>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="../mahasiswa/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:square-academic-cap-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Data Mahasiswa</p>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="../matakuliah/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:notebook-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Mata Kuliah</p>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="../kelas/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:black-hole-line-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Kelas Kuliah</p>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="../semester/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:calendar-date-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Semester</p>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="../prestasi/index.php" class="menu-grid-item">
                    <iconify-icon icon="solar:medal-star-bold-duotone" class="menu-icon"></iconify-icon>
                    <p class="menu-label">Validasi Prestasi</p>
                </a>
            </div>
        </div>

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Chart Defaults
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#64748b';

        // 1. Chart Prodi (Doughnut)
        new Chart(document.getElementById('chartProdi'), {
            type: 'doughnut',
            data: { 
                labels: <?php echo json_encode($label_prodi); ?>, 
                datasets: [{ 
                    data: <?php echo json_encode($data_prodi); ?>, 
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 0,
                }] 
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } }
            }
        });
        
        // 2. Chart IPK (Bar)
        const ctxIPK = document.getElementById('chartIPK').getContext('2d');
        const gradientIPK = ctxIPK.createLinearGradient(0, 0, 0, 400);
        gradientIPK.addColorStop(0, '#10b981'); 
        gradientIPK.addColorStop(1, '#3b82f6');

        new Chart(ctxIPK, {
            type: 'bar',
            data: { 
                labels: <?php echo json_encode($label_ipk); ?>, 
                datasets: [{ 
                    label: 'Rata-rata IPK', 
                    data: <?php echo json_encode($data_ipk); ?>, 
                    backgroundColor: gradientIPK,
                    borderRadius: 6,
                    barThickness: 30
                }] 
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { beginAtZero: true, max: 4.0, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>