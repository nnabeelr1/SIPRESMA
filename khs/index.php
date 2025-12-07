<?php
session_start();
include '../config/koneksi.php';

// 1. Cek Login Mahasiswa
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// 2. Ambil Data Mahasiswa
$username = $_SESSION['username'];
$mhs = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT m.*, d.nama_lengkap as nama_doswal, p.nama_prodi 
    FROM mahasiswa m 
    JOIN user u ON m.id_user = u.id_user 
    LEFT JOIN dosen d ON m.dosen_wali = d.nidn
    LEFT JOIN prodi p ON m.id_prodi = p.id_prodi
    WHERE u.username='$username'
"));
$nim_saya = $mhs['nim'];
$nama_saya = $mhs['nama_lengkap'];
// Ambil nama depan
$nama_depan = explode(' ', trim($nama_saya))[0];

// 3. Ambil Semester Aktif
$smt = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'"));
if(!$smt) {
    echo "Belum ada semester aktif."; exit();
}
$id_smt = $smt['id_semester'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Hasil Studi - SIPRESMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            /* Palette Konsisten */
            --primary: #10b981;       
            --bg-body: #f8fafc;       
            --text-main: #1e293b;     
            --text-muted: #64748b;    
            --card-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 3rem;
        }

        /* --- Navbar Clean (Sama Persis Dashboard) --- */
        .navbar-clean {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 0.8rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo-box {
            background: rgba(16, 185, 129, 0.1); 
            color: #10b981;
            width: 42px; height: 42px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        /* --- Page Header --- */
        .page-header {
            margin-top: 2rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* --- Info Box Mahasiswa --- */
        .info-box {
            background: white; border-radius: 16px; padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;
        }
        .info-item { margin-bottom: 0.8rem; display: flex; align-items: center; }
        .info-label { width: 140px; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 1rem; font-weight: 700; color: var(--text-main); }

        /* --- Card Styles --- */
        .card-modern {
            background: white; border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        .card-header-clean {
            padding: 1.5rem; border-bottom: 1px solid #f1f5f9; background: white;
            display: flex; align-items: center; gap: 10px;
        }
        .card-title { font-weight: 700; font-size: 1rem; color: var(--text-main); margin: 0; }

        /* --- Table Styling --- */
        .table-custom thead th {
            background-color: #f8fafc; color: var(--text-muted);
            font-weight: 700; font-size: 0.75rem; text-transform: uppercase;
            letter-spacing: 0.05em; padding: 1rem; border-bottom: 1px solid #e2e8f0; border-top: none;
        }
        .table-custom tbody td {
            padding: 1rem; vertical-align: middle;
            border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: var(--text-main);
        }
        .table-custom tfoot td {
            background-color: #f0fdfa; /* Hijau sangat muda */
            color: var(--primary);
            font-weight: 700; padding: 1rem;
        }

        /* --- IPS Card Modern --- */
        .ips-card {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); /* Emerald Gradient */
            color: white; border-radius: 16px; padding: 1.5rem;
            text-align: center; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .ips-label { font-size: 0.85rem; font-weight: 500; opacity: 0.9; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 1px; }
        .ips-value { font-size: 3rem; font-weight: 800; line-height: 1; }

        /* --- Grade Badges --- */
        .grade-badge {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.9rem;
        }
        .grade-a { background: #dcfce7; color: #166534; }
        .grade-b { background: #dbeafe; color: #1e40af; }
        .grade-c { background: #fef3c7; color: #92400e; }
        .grade-d { background: #fee2e2; color: #b91c1c; }
        .grade-e { background: #f1f5f9; color: #64748b; }

        /* --- Buttons --- */
        .btn-clean {
            background: white; border: 1px solid #e2e8f0; color: var(--text-main);
            font-weight: 600; padding: 0.6rem 1.2rem; border-radius: 12px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            text-decoration: none;
        }
        .btn-clean:hover { background: #f8fafc; border-color: #cbd5e1; }

        .btn-print {
            background: white; border: 1px solid var(--primary); color: var(--primary);
            font-weight: 600; padding: 0.6rem 1.5rem; border-radius: 12px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            text-decoration: none;
        }
        .btn-print:hover { background: #ecfdf5; }

        /* --- Empty State --- */
        .empty-state { padding: 4rem 1rem; text-align: center; color: var(--text-muted); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="#">
                <div class="logo-box">
                    <iconify-icon icon="solar:infinity-bold" style="font-size: 1.5rem;"></iconify-icon>
                </div>
                <div style="line-height: 1.2;">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">SIPRESMA</h5>
                    <small class="text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 1px; display: block;">
                        STUDENT PORTAL
                    </small>
                </div>
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-block text-end" style="line-height: 1.2;">
                    <span class="fw-bold d-block text-dark" style="font-size: 0.9rem;">
                        <?php echo $nama_depan; ?>
                    </span>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        <?php echo $nim_saya; ?>
                    </small>
                </div>
                <div class="bg-light rounded-circle border p-1 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <iconify-icon icon="solar:user-circle-bold" class="text-secondary" style="font-size: 1.8rem;"></iconify-icon>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <div class="page-header">
            <div>
                <h2 class="page-title">Kartu Hasil Studi</h2>
                <p class="page-subtitle">Transkrip nilai sementara untuk semester aktif.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard/welcome_mhs.php" class="btn-clean">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Dashboard
                </a>
            </div>
        </div>

        <div class="info-box">
            <div>
                <div class="info-item">
                    <div class="info-label">Mahasiswa</div>
                    <div class="info-value"><?php echo $nama_saya; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIM</div>
                    <div class="info-value"><?php echo $nim_saya; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Program Studi</div>
                    <div class="info-value"><?php echo $mhs['nama_prodi']; ?></div>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <div class="info-label">Semester</div>
                    <div class="info-value text-primary"><?php echo $smt['nama_semester']; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Dosen Wali</div>
                    <div class="info-value"><?php echo $mhs['nama_doswal'] ?: '-'; ?></div>
                </div>
            </div>
        </div>

        <div class="card-modern mb-4">
            <div class="card-header-clean">
                <iconify-icon icon="solar:clipboard-list-bold-duotone" class="text-primary fs-4"></iconify-icon>
                <h6 class="card-title">Rincian Hasil Studi</h6>
            </div>
            
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="40%">Mata Kuliah</th>
                            <th width="10%" class="text-center">SKS (K)</th>
                            <th width="10%" class="text-center">Nilai</th>
                            <th width="15%" class="text-center">Bobot (N)</th>
                            <th width="20%" class="text-center">Mutu (K×N)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "
                            SELECT krs.*, m.nama_mk, m.sks, n.grade 
                            FROM krs
                            JOIN kelas k ON krs.id_kelas = k.id_kelas
                            JOIN matakuliah m ON k.kode_mk = m.kode_mk
                            LEFT JOIN nilai n ON krs.id_krs = n.id_krs
                            WHERE krs.nim='$nim_saya' AND krs.id_semester='$id_smt'
                        ");

                        $no = 1;
                        $total_sks = 0;
                        $total_mutu = 0;

                        if(mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='6' class='empty-state'>
                                <iconify-icon icon='solar:box-minimalistic-linear' class='fs-1 mb-2'></iconify-icon><br>
                                Belum ada mata kuliah yang diambil
                            </td></tr>";
                        }

                        while($row = mysqli_fetch_assoc($query)) {
                            $grade = $row['grade'] ? $row['grade'] : 'E';
                            $sks   = $row['sks'];

                            // Konversi Bobot
                            switch ($grade) {
                                case 'A':  $bobot = 4.00; break;
                                case 'AB': $bobot = 3.50; break;
                                case 'B':  $bobot = 3.00; break;
                                case 'BC': $bobot = 2.50; break;
                                case 'C':  $bobot = 2.00; break;
                                case 'CD': $bobot = 1.50; break;
                                case 'D':  $bobot = 1.00; break;
                                default:   $bobot = 0.00;
                            }

                            $mutu = $sks * $bobot;
                            $total_sks += $sks;
                            $total_mutu += $mutu;
                            
                            // Grade Style
                            $g = strtolower(substr($grade, 0, 1)); // Ambil huruf depan aja buat class css
                            $grade_class = 'grade-e';
                            if($g == 'a') $grade_class = 'grade-a';
                            elseif($g == 'b') $grade_class = 'grade-b';
                            elseif($g == 'c') $grade_class = 'grade-c';
                            elseif($g == 'd') $grade_class = 'grade-d';
                        ?>
                            <tr>
                                <td class="text-center fw-semibold"><?php echo $no++; ?></td>
                                <td>
                                    <span class="fw-bold d-block"><?php echo $row['nama_mk']; ?></span>
                                </td>
                                <td class="text-center"><?php echo $sks; ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <span class="grade-badge <?php echo $grade_class; ?>"><?php echo $grade; ?></span>
                                    </div>
                                </td>
                                <td class="text-center"><?php echo number_format($bobot, 2); ?></td>
                                <td class="text-center fw-bold"><?php echo number_format($mutu, 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <?php if(mysqli_num_rows($query) > 0) { ?>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end">TOTAL</td>
                            <td class="text-center"><?php echo $total_sks; ?></td>
                            <td></td>
                            <td></td>
                            <td class="text-center"><?php echo number_format($total_mutu, 2); ?></td>
                        </tr>
                    </tfoot>
                    <?php } ?>
                </table>
            </div>
        </div>

        <?php if(mysqli_num_rows($query) > 0) { 
            $ips = ($total_sks > 0) ? ($total_mutu / $total_sks) : 0;
        ?>
        <div class="row align-items-center">
            <div class="col-md-6">
                <a href="cetak.php" target="_blank" class="btn-print">
                    <iconify-icon icon="solar:printer-bold"></iconify-icon> Cetak Transkrip
                </a>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <div class="ips-card" style="min-width: 300px;">
                        <div class="ips-label">Indeks Prestasi Semester</div>
                        <div class="ips-value"><?php echo number_format($ips, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>