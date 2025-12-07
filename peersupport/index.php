<?php
session_start();
include '../config/koneksi.php';

// Cek Admin
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    
    <style>
        :root {
            /* Palette Professional (Emerald & Slate) - COPIED FROM DASHBOARD */
            --primary: #10b981;       /* Emerald 500 */
            --primary-dark: #059669;  /* Emerald 600 */
            --secondary: #64748b;     /* Slate 500 */
            --bg-body: #f8fafc;       /* Slate 50 */
            --card-bg: #ffffff;
            --text-main: #1e293b;     /* Slate 800 */
            --text-muted: #94a3b8;    /* Slate 400 */
            
            /* Status Colors */
            --soft-green: #d1fae5; --text-green: #065f46;
            --soft-red: #fee2e2;   --text-red: #991b1b;
            --soft-blue: #e0f2fe;  --text-blue: #075985;
            --soft-amber: #fef3c7; --text-amber: #92400e;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* --- Custom Navbar (PERSIS DASHBOARD) --- */
        .navbar-clean {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .brand-text {
            color: var(--text-main);
            letter-spacing: -0.5px;
        }
        
        /* --- Card Styling --- */
        .card-modern {
            background: var(--card-bg);
            border: 1px solid rgba(0,0,0,0.03);
            border-radius: 16px;
            box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        /* --- Header Section --- */
        .page-header {
            margin-top: 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* --- Table Styling --- */
        .table-modern thead th {
            background-color: #f1f5f9;
            color: var(--secondary);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem;
            border: none;
        }
        .table-modern tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 0.9rem;
        }
        .table-modern tbody tr:last-child td { border-bottom: none; }
        
        /* --- Badges --- */
        .badge-pill {
            padding: 0.35em 0.8em;
            border-radius: 50rem;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .badge-soft-success { background: var(--soft-green); color: var(--text-green); }
        .badge-soft-warning { background: var(--soft-amber); color: var(--text-amber); }
        .badge-soft-danger { background: var(--soft-red); color: var(--text-red); }
        .badge-soft-gray { background: #f1f5f9; color: #475569; }

        /* --- Buttons --- */
        .btn-back {
            background: white; border: 1px solid #e2e8f0; color: var(--text-main);
            font-weight: 600; padding: 0.6rem 1.2rem; border-radius: 12px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
        }
        .btn-back:hover { background: #f8fafc; border-color: #cbd5e1; }

        .btn-danger-soft {
            background: #fef2f2; color: #ef4444; border: 1px solid #fecaca;
            font-weight: 600; padding: 0.6rem 1.2rem; border-radius: 12px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
        }
        .btn-danger-soft:hover { background: #fee2e2; color: #dc2626; }

        /* --- Student Info --- */
        .student-name { font-weight: 700; color: var(--text-main); display: block; font-size: 0.95rem; }
        .student-nim { font-size: 0.8rem; color: var(--text-muted); font-family: monospace; }

        /* --- Empty State --- */
        .empty-state { padding: 4rem 2rem; text-align: center; }
        .empty-icon { font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; }
    </style>
</head>
<body>

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
                            <iconify-icon icon="solar:logout-2-bold" class="me-2"></iconify-icon>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5 pb-5">
        
        <div class="page-header">
            <div>
                <h2 class="page-title">Data Peer Support</h2>
                <p class="page-subtitle">Kelola daftar pasangan mentoring antara Mentee dan Mentor.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard/index.php" class="btn-back">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Dashboard
                </a>
                <a href="reset.php" class="btn-danger-soft" onclick="return confirm('⚠️ PERINGATAN:\n\nHapus SEMUA data pasangan mentoring?')">
                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon> Reset Data
                </a>
            </div>
        </div>

        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th width="30%">Mentee (Butuh Bimbingan)</th>
                            <th width="30%">Mentor (Berprestasi)</th>
                            <th width="15%">Prodi</th>
                            <th width="15%">Status</th>
                            <th width="10%" class="text-end">Aksi</th>
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
                        if(mysqli_num_rows($query) == 0) {
                            echo "
                            <tr>
                                <td colspan='6'>
                                    <div class='empty-state'>
                                        <iconify-icon icon='solar:folder-with-files-linear' class='empty-icon'></iconify-icon>
                                        <h6 class='fw-bold text-dark'>Belum Ada Data</h6>
                                        <p class='text-muted small mb-0'>Jalankan Auto Match di dashboard.</p>
                                    </div>
                                </td>
                            </tr>";
                        }

                        while($row = mysqli_fetch_assoc($query)) {
                            // Logic Status
                            if($row['status'] == 'aktif') {
                                $badge_cls = 'badge-soft-success'; $icon = 'solar:check-circle-bold'; $txt = 'Aktif';
                            } elseif($row['status'] == 'ditolak') {
                                $badge_cls = 'badge-soft-danger'; $icon = 'solar:close-circle-bold'; $txt = 'Ditolak';
                            } else {
                                $badge_cls = 'badge-soft-warning'; $icon = 'solar:hourglass-line-bold'; $txt = 'Menunggu';
                            }
                        ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?php echo $no++; ?></td>
                                
                                <td>
                                    <span class="student-name"><?php echo $row['nama_mentee']; ?></span>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="student-nim"><?php echo $row['nim_mentee']; ?></span>
                                        <span class="badge-pill badge-soft-danger py-1 px-2" style="font-size: 0.65rem;">
                                            IPK <?php echo $row['ipk_mentee']; ?>
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <span class="student-name"><?php echo $row['nama_mentor']; ?></span>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="student-nim"><?php echo $row['nim_mentor']; ?></span>
                                        <span class="badge-pill badge-soft-success py-1 px-2" style="font-size: 0.65rem;">
                                            IPK <?php echo $row['ipk_mentor']; ?>
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge-pill badge-soft-gray fw-normal">
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
                                       class="btn btn-sm btn-light text-danger border"
                                       style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px;"
                                       onclick="return confirm('Hapus pasangan ini?')">
                                        <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
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