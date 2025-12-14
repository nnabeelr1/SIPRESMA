<?php
session_start();
// --- SECURITY CHECK ---
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

include '../config/koneksi.php';
$nama_user = "Administrator"; 
$role_label = "System Admin";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen - SIPRESMA</title>
    
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

        /* --- HEADER PAGE --- */
        .page-header {
            margin-top: 2rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; letter-spacing: -1px; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* --- CARD STYLE (GLASS CONTAINER) --- */
        .card-modern {
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius-xl);
            padding: 0; 
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03);
            overflow: hidden; 
            transition: transform 0.3s ease;
        }

        /* --- TABLE STYLING --- */
        .table-wrapper {
            max-height: 550px;
            overflow-y: auto; overflow-x: hidden;
        }
        .table-wrapper::-webkit-scrollbar { width: 6px; }
        .table-wrapper::-webkit-scrollbar-track { background: transparent; }
        .table-wrapper::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }

        .table-custom {
            width: 100%; border-collapse: separate; border-spacing: 0; table-layout: fixed;
        }

        /* Sticky Header */
        .table-custom thead th {
            position: sticky; top: 0; z-index: 20;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 0 rgba(0,0,0,0.05);
            color: var(--text-muted); font-size: 0.75rem; font-weight: 800; 
            text-transform: uppercase; letter-spacing: 0.1em;
            padding: 1.5rem 1.2rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-custom tbody td { 
            padding: 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.03); 
            vertical-align: middle; font-size: 0.95rem; font-weight: 500;
            background: transparent; word-wrap: break-word;
        }
        .table-custom tr:last-child td { border-bottom: none; }
        .table-custom tbody tr { transition: all 0.2s ease; }
        .table-custom tbody tr:hover { 
            background-color: rgba(255,255,255,0.9);
            transform: scale(1.002);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); z-index: 10; position: relative;
        }

        /* --- BUTTONS --- */
        a { text-decoration: none !important; }

        /* Tombol Tambah (Gradient Green) */
        .btn-gradient-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white; border: none; padding: 0.7rem 1.5rem; border-radius: 50px;
            font-weight: 700; display: inline-flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); transition: all 0.2s;
        }
        .btn-gradient-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4); color: white; }

        /* Action Buttons (Soft) */
        .btn-action-soft {
            width: 38px; height: 38px; border-radius: 10px; border: none;
            display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.2s; cursor: pointer;
        }
        .btn-soft-warning { background: #fef3c7; color: #b45309; }
        .btn-soft-warning:hover { background: #b45309; color: white; transform: translateY(-2px); }
        .btn-soft-danger { background: #fee2e2; color: #b91c1c; }
        .btn-soft-danger:hover { background: #b91c1c; color: white; transform: translateY(-2px); }

        /* --- BADGES & TEXT --- */
        .badge-pill { padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 6px; }
        .badge-prodi { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        
        .text-mono { font-family: monospace; font-size: 0.85rem; color: var(--text-muted); background: #f8fafc; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0; }

        /* --- EMPTY STATE --- */
        .empty-state { padding: 5rem 2rem; text-align: center; }
        .empty-icon { font-size: 3.5rem; color: #cbd5e1; margin-bottom: 1rem; }
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
    <div class="container mb-5 pb-5">
        
        <div class="page-header animate-entry" style="animation-delay: 0.1s;">
            <div>
                <h2 class="page-title">Data Dosen</h2>
                <p class="page-subtitle">Manajemen data dosen pengajar dan wali.</p>
            </div>
            
            <div>
                <a href="create.php" class="btn-gradient-primary">
                    <iconify-icon icon="solar:user-plus-bold" class="fs-5"></iconify-icon> Tambah Dosen
                </a>
            </div>
        </div>

        <div class="card-modern animate-entry" style="animation-delay: 0.3s;">
            <div class="table-wrapper">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">NIDN</th>
                            <th width="30%">Nama Lengkap</th>
                            <th width="25%">Email</th>
                            <th width="15%">Prodi</th>
                            <th width="10%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM dosen JOIN prodi ON dosen.id_prodi = prodi.id_prodi ORDER BY dosen.nama_lengkap ASC");
                        $no = 1;
                        
                        if(mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='6'>
                                <div class='empty-state'>
                                    <iconify-icon icon='solar:user-id-linear' class='empty-icon'></iconify-icon>
                                    <h6 class='fw-bold text-dark'>Belum Ada Data Dosen</h6>
                                    <p class='text-muted small'>Silakan tambah data dosen baru.</p>
                                </div>
                            </td></tr>";
                        }
                        
                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?php echo $no++; ?></td>
                                
                                <td>
                                    <span class="text-mono"><?php echo $data['nidn']; ?></span>
                                </td>
                                
                                <td>
                                    <span class="fw-bold text-dark"><?php echo $data['nama_lengkap']; ?></span>
                                </td>
                                
                                <td>
                                    <span class="text-muted small"><?php echo $data['email']; ?></span>
                                </td>
                                
                                <td>
                                    <span class="badge-pill badge-prodi">
                                        <?php echo $data['jenjang']; ?> <?php echo $data['nama_prodi']; ?>
                                    </span>
                                </td>
                                
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="update.php?nidn=<?php echo $data['nidn']; ?>" class="btn-action-soft btn-soft-warning" title="Edit">
                                            <iconify-icon icon="solar:pen-bold" class="fs-5"></iconify-icon>
                                        </a>
                                        <a href="delete.php?nidn=<?php echo $data['nidn']; ?>" class="btn-action-soft btn-soft-danger" onclick="return confirm('Hapus dosen <?php echo $data['nama_lengkap']; ?>?')" title="Hapus">
                                            <iconify-icon icon="solar:trash-bin-trash-bold" class="fs-5"></iconify-icon>
                                        </a>
                                    </div>
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