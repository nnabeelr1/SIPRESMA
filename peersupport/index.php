<?php
session_start();
include '../config/koneksi.php';

// --- SECURITY CHECK ---
// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peer Support - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    
    <style>
        /* --- THEME CONFIGURATION --- */
        :root {
            /* Warna Utama (Green UNNES) */
            --primary: #10b981;       
            --primary-dark: #047857;
            
            /* Warna Text */
            --text-main: #0f172a;     
            --text-muted: #64748b;    
            
            /* UI Dimensions */
            --radius-xl: 24px;
            --nav-height: 80px;
        }

        /* --- GLOBAL & ANIMATION --- */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            
            /* BACKGROUND: Mint Green + Dot Matrix (KONSISTEN DENGAN DASHBOARD) */
            background-color: #f0fdf4; 
            background-image: 
                radial-gradient(#86efac 1.2px, transparent 1.2px), 
                radial-gradient(circle at top center, rgba(16, 185, 129, 0.1) 0%, rgba(240, 253, 244, 0) 70%); 
            background-size: 24px 24px, 100% 100%;
            background-attachment: fixed;
        }

        /* Keyframes: Animasi Entry Halus */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-entry {
            animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0; /* Default hidden sebelum animasi jalan */
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

        /* --- HEADER PAGE --- */
        .page-header {
            margin-top: 2rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; letter-spacing: -1px; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* --- CARD STYLE (GLASS CONTAINER) --- */
        .card-modern {
            background: rgba(255, 255, 255, 0.7); /* Transparan biar background dot kelihatan */
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius-xl);
            padding: 0; 
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03);
            overflow: hidden; /* Round corner container */
            transition: transform 0.3s ease;
        }

        /* --- TABLE STYLING --- */
        /* Table Wrapper: Scroll hanya di area tabel, tidak page */
        .table-wrapper {
            max-height: 550px; /* Tinggi tabel maksimal */
            overflow-y: auto;  /* Scroll Vertikal Aktif */
            overflow-x: hidden; /* Scroll Horizontal Mati */
        }
        
        /* Custom Scrollbar */
        .table-wrapper::-webkit-scrollbar { width: 6px; }
        .table-wrapper::-webkit-scrollbar-track { background: transparent; }
        .table-wrapper::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }

        .table-custom {
            width: 100%;
            border-collapse: separate; 
            border-spacing: 0;
            table-layout: fixed; /* PENTING: Biar lebar kolom KONSISTEN walaupun kosong */
        }

        /* Sticky Header (Nempel di atas saat scroll) */
        .table-custom thead th {
            position: sticky; top: 0; z-index: 20;
            background: rgba(255, 255, 255, 0.95); /* Putih Solid dikit biar teks ga tabrakan */
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 0 rgba(0,0,0,0.05);
            
            color: var(--text-muted); 
            font-size: 0.75rem; font-weight: 800; 
            text-transform: uppercase; letter-spacing: 0.1em;
            padding: 1.5rem 1.2rem;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Body Row */
        .table-custom tbody td { 
            padding: 1.2rem; 
            border-bottom: 1px solid rgba(0,0,0,0.03); 
            vertical-align: middle; 
            font-size: 0.95rem; font-weight: 500;
            background: transparent;
            word-wrap: break-word; /* Biar teks panjang ga ngerusak layout */
        }
        .table-custom tr:last-child td { border-bottom: none; }
        
        /* Hover Effect Row */
        .table-custom tbody tr { transition: all 0.2s ease; }
        .table-custom tbody tr:hover { 
            background-color: rgba(255,255,255,0.9);
            transform: scale(1.002);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            z-index: 10; position: relative;
        }

        /* --- BUTTONS --- */
        a { text-decoration: none !important; }

        /* Tombol Soft Action (Hapus) */
        .btn-action-soft {
            width: 40px; height: 40px; border-radius: 12px;
            display: inline-flex; align-items: center; justify-content: center;
            border: none; background-color: #fef2f2; color: #ef4444;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-action-soft:hover {
            background-color: #ef4444; color: white;
            transform: translateY(-3px) rotate(5deg);
            box-shadow: 0 8px 15px -3px rgba(239, 68, 68, 0.3);
        }

        /* Tombol Reset Data (Header) */
        .btn-glass-danger {
            background: #fef2f2; color: #ef4444; border: 1px solid #fecaca;
            font-weight: 700; padding: 0.7rem 1.4rem; border-radius: 50px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
        }
        .btn-glass-danger:hover { 
            background: #ef4444; color: white; 
            transform: translateY(-2px); 
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3); 
        }

        /* --- BADGES --- */
        .badge-pill { padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 6px; }
        .badge-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-danger  { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-prodi   { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-weight: 600; }
        .badge-ipk     { font-family: monospace; letter-spacing: 0; padding: 4px 8px; border-radius: 8px; font-size: 0.7rem; }

        .student-name { font-weight: 700; color: var(--text-main); display: block; font-size: 0.95rem; }
        .student-nim { font-size: 0.8rem; color: var(--text-muted); font-family: monospace; letter-spacing: 0.5px; }

        /* --- EMPTY STATE --- */
        .empty-state { padding: 5rem 2rem; text-align: center; }
        .empty-icon { font-size: 3.5rem; color: #cbd5e1; margin-bottom: 1rem; }

    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-4">
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
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Administrator</p>
                    <p class="mb-0 text-success d-flex align-items-center justify-content-end gap-1" style="font-size: 0.75rem; font-weight: 600;">
                        <iconify-icon icon="solar:record-circle-bold" style="font-size: 8px;"></iconify-icon> System Admin
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
    <div class="container mb-5 pb-5">
        
        <div class="page-header animate-entry" style="animation-delay: 0.1s;">
            <div>
                <h2 class="page-title">Data Peer Support</h2>
                <p class="page-subtitle">Kelola daftar pasangan mentoring antara Mentee dan Mentor.</p>
            </div>
            <div>
                <a href="reset.php" class="btn-glass-danger" onclick="return confirm('⚠️ PERINGATAN:\n\nHapus SEMUA data pasangan mentoring? Data tidak bisa dikembalikan.')">
                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon> Reset Data
                </a>
            </div>
        </div>

        <div class="card-modern animate-entry" style="animation-delay: 0.3s;">
            <div class="table-wrapper">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 30%;">Mentee (Butuh Bimbingan)</th>
                            <th style="width: 30%;">Mentor (Berprestasi)</th>
                            <th style="width: 15%;">Prodi</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%;" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "
                            SELECT ps.*, 
                                   m1.nama_lengkap as nama_mentee, m1.nim as nim_mentee, m1.ipk_terakhir as ipk_mentee,
                                   m2.nama_lengkap as nama_mentor, m2.nim as nim_mentor, m2.ipk_terakhir as ipk_mentor,
                                   p.nama_prodi
                            FROM peer_support ps
                            JOIN mahasiswa m1 ON ps.mentee_nim = m1.nim
                            JOIN mahasiswa m2 ON ps.mentor_nim = m2.nim
                            JOIN prodi p ON m1.id_prodi = p.id_prodi
                            ORDER BY ps.id_match DESC
                        ");

                        $no = 1;
                        // Handling EMPTY STATE
                        if(mysqli_num_rows($query) == 0) {
                            echo "
                            <tr>
                                <td colspan='6'>
                                    <div class='empty-state'>
                                        <iconify-icon icon='solar:folder-with-files-linear' class='empty-icon'></iconify-icon>
                                        <h6 class='fw-bold text-dark fs-5'>Belum Ada Data Pasangan</h6>
                                        <p class='text-muted small mb-3'>Silakan jalankan fitur 'Auto-Match' di dashboard.</p>
                                        <a href='../dashboard/auto_match.php' class='btn btn-success rounded-pill fw-bold px-4 shadow-sm text-decoration-none'>
                                            <iconify-icon icon='solar:bolt-bold' class='me-2'></iconify-icon> Auto Match Sekarang
                                        </a>
                                    </div>
                                </td>
                            </tr>";
                        }

                        // Loop Data
                        while($row = mysqli_fetch_assoc($query)) {
                            // Logic Styling Status
                            if($row['status'] == 'aktif') {
                                $badge_cls = 'badge-success'; $icon = 'solar:check-circle-bold'; $txt = 'Aktif';
                            } elseif($row['status'] == 'ditolak') {
                                $badge_cls = 'badge-danger'; $icon = 'solar:close-circle-bold'; $txt = 'Ditolak';
                            } else {
                                $badge_cls = 'badge-warning'; $icon = 'solar:hourglass-line-bold'; $txt = 'Menunggu';
                            }
                        ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?php echo $no++; ?></td>
                                
                                <td>
                                    <span class="student-name"><?php echo $row['nama_mentee']; ?></span>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="student-nim"><?php echo $row['nim_mentee']; ?></span>
                                        <span class="badge-ipk bg-danger-subtle text-danger">
                                            IPK <?php echo $row['ipk_mentee']; ?>
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <span class="student-name"><?php echo $row['nama_mentor']; ?></span>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="student-nim"><?php echo $row['nim_mentor']; ?></span>
                                        <span class="badge-ipk bg-success-subtle text-success">
                                            IPK <?php echo $row['ipk_mentor']; ?>
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge-pill badge-prodi">
                                        <?php echo $row['nama_prodi']; ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge-pill <?php echo $badge_cls; ?>">
                                        <iconify-icon icon="<?php echo $icon; ?>"></iconify-icon> <?php echo $txt; ?>
                                    </span>
                                </td>

                                <td class="text-end">
                                    <a href="delete.php?id=<?php echo $row['id_match']; ?>" 
                                       class="btn-action-soft"
                                       title="Hapus Data"
                                       onclick="return confirm('Hapus pasangan ini?')">
                                        <iconify-icon icon="solar:trash-bin-trash-bold" class="fs-5"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>