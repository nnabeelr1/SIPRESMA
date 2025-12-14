<?php
session_start();
include '../config/koneksi.php';

// Cek Login Dosen
if ($_SESSION['role'] != 'dosen') {
    header("Location: ../index.php");
    exit();
}

// Data Dosen (Navbar)
$id_user = $_SESSION['id_user'];
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_lengkap FROM dosen WHERE id_user='$id_user'"));
$nama_depan = explode(' ', trim($dosen['nama_lengkap']))[0];

// Ambil ID Kelas
$id_kelas = $_GET['id_kelas'];

// Data Kelas
$q_kelas = mysqli_query($koneksi, "
    SELECT k.*, m.nama_mk, m.sks 
    FROM kelas k JOIN matakuliah m ON k.kode_mk = m.kode_mk 
    WHERE k.id_kelas = '$id_kelas'
");
$kelas = mysqli_fetch_assoc($q_kelas);

// Logic Simpan
if (isset($_POST['simpan_nilai'])) {
    $id_krs_arr = $_POST['id_krs'];
    $tugas_arr  = $_POST['tugas'];
    $uts_arr    = $_POST['uts'];
    $uas_arr    = $_POST['uas'];
    
    for ($i = 0; $i < count($id_krs_arr); $i++) {
        $id_krs = $id_krs_arr[$i];
        $tugas  = floatval($tugas_arr[$i]);
        $uts    = floatval($uts_arr[$i]);
        $uas    = floatval($uas_arr[$i]);

        // Rumus: Tugas 30%, UTS 30%, UAS 40%
        $akhir = ($tugas * 0.30) + ($uts * 0.30) + ($uas * 0.40);

        if ($akhir >= 85) $grade = "A";
        elseif ($akhir >= 80) $grade = "AB";
        elseif ($akhir >= 70) $grade = "B";
        elseif ($akhir >= 65) $grade = "BC";
        elseif ($akhir >= 60) $grade = "C";
        elseif ($akhir >= 50) $grade = "CD";
        elseif ($akhir >= 40) $grade = "D";
        else $grade = "E";

        // Cek update/insert
        $cek = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_krs='$id_krs'");
        if (mysqli_num_rows($cek) > 0) {
            $q = "UPDATE nilai SET nilai_tugas='$tugas', nilai_uts='$uts', nilai_uas='$uas', nilai_akhir='$akhir', grade='$grade' WHERE id_krs='$id_krs'";
        } else {
            $q = "INSERT INTO nilai (id_krs, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir, grade) VALUES ('$id_krs', '$tugas', '$uts', '$uas', '$akhir', '$grade')";
        }
        mysqli_query($koneksi, $q);
    }
    echo "<script>alert('✅ Nilai berhasil disimpan!'); window.location='index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Nilai - <?php echo $kelas['nama_mk']; ?></title>
    
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
            padding-bottom: 5rem; /* Space for floating button */
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

        /* --- HEADER --- */
        .page-header {
            margin-top: 2rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; letter-spacing: -1px; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        /* --- INFO GLASS --- */
        .info-glass {
            background: rgba(255, 255, 255, 0.6); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem;
            display: flex; gap: 3rem; align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .info-item label { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
        .info-item div { font-size: 1.1rem; font-weight: 700; color: var(--text-main); }

        /* --- TABLE --- */
        .card-modern {
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: var(--radius-xl);
            padding: 0; 
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03);
            overflow: hidden; 
        }

        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-custom thead th {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            color: var(--text-muted); font-size: 0.75rem; font-weight: 800; 
            text-transform: uppercase; letter-spacing: 0.1em;
            padding: 1.2rem; border-bottom: 1px solid #e2e8f0;
            text-align: center; vertical-align: middle;
        }
        .table-custom tbody td { 
            padding: 1rem; border-bottom: 1px solid rgba(0,0,0,0.03); 
            vertical-align: middle;
        }

        /* --- INPUTS --- */
        .form-control-clean {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid #e2e8f0;
            border-radius: 8px; text-align: center; font-weight: 600;
            color: var(--text-main); padding: 0.5rem;
            transition: all 0.2s;
        }
        .form-control-clean:focus {
            background: white; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); outline: none;
        }

        /* --- GRADES --- */
        .nilai-akhir { font-weight: 800; color: var(--text-main); font-size: 1rem; }
        .grade-box {
            display: inline-flex; width: 36px; height: 36px; 
            align-items: center; justify-content: center;
            border-radius: 10px; font-weight: 800; 
            background: #f1f5f9; color: var(--text-muted);
        }

        /* --- FLOATING BUTTON --- */
        .floating-action {
            position: fixed; bottom: 30px; right: 30px; z-index: 99;
            background: white; padding: 8px; border-radius: 50px;
            box-shadow: 0 10px 40px rgba(16, 185, 129, 0.2);
        }
        .btn-save-float {
            background: linear-gradient(135deg, #10b981, #059669); 
            color: white; border: none;
            padding: 0.8rem 2rem; border-radius: 50px; font-weight: 700;
            display: flex; align-items: center; gap: 8px; transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        .btn-save-float:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5); }

        .btn-glass-back {
            background: white; border: 1px solid #e2e8f0; color: var(--text-main);
            font-weight: 700; padding: 0.7rem 1.4rem; border-radius: 50px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }
        .btn-glass-back:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); color: var(--primary); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-clean mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="../dashboard/welcome_dosen.php">
                <div class="logo-box">
                    <iconify-icon icon="solar:infinity-bold" width="24"></iconify-icon>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 brand-text">SIPRESMA</h5>
                    <p class="mb-0 brand-sub">LECTURER PORTAL</p>
                </div>
            </a>
            
            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-block text-end">
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><?php echo $nama_depan; ?></p>
                    <p class="mb-0 text-success d-flex align-items-center justify-content-end gap-1" style="font-size: 0.75rem; font-weight: 600;">
                        <iconify-icon icon="solar:record-circle-bold" style="font-size: 8px;"></iconify-icon> Lecturer
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

    <div class="container">
        
        <div class="page-header animate-entry" style="animation-delay: 0.1s;">
            <div>
                <h2 class="page-title">Input Nilai: <?php echo $kelas['nama_mk']; ?></h2>
                <p class="page-subtitle">Isi nilai tugas, UTS, dan UAS untuk setiap mahasiswa.</p>
            </div>
            <div>
                <a href="index.php" class="btn-glass-back">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Kembali
                </a>
            </div>
        </div>

        <div class="info-glass animate-entry" style="animation-delay: 0.2s;">
            <div class="info-item">
                <label>Kelas</label>
                <div class="text-primary"><?php echo $kelas['nama_kelas']; ?></div>
            </div>
            <div class="info-item">
                <label>SKS</label>
                <div><?php echo $kelas['sks']; ?></div>
            </div>
            <div class="info-item">
                <label>Jadwal</label>
                <div><?php echo $kelas['hari'] . ', ' . date('H:i', strtotime($kelas['jam_mulai'])); ?></div>
            </div>
        </div>

        <form method="POST" id="formNilai">
            <div class="card-modern animate-entry" style="animation-delay: 0.3s;">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIM</th>
                                <th width="25%" style="text-align: left;">Nama Mahasiswa</th>
                                <th width="12%">Tugas (30%)</th>
                                <th width="12%">UTS (30%)</th>
                                <th width="12%">UAS (40%)</th>
                                <th width="10%">Akhir</th>
                                <th width="9%">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_mhs = mysqli_query($koneksi, "
                                SELECT krs.id_krs, m.nim, m.nama_lengkap, 
                                       n.nilai_tugas, n.nilai_uts, n.nilai_uas, n.nilai_akhir, n.grade
                                FROM krs
                                JOIN mahasiswa m ON krs.nim = m.nim
                                LEFT JOIN nilai n ON krs.id_krs = n.id_krs
                                WHERE krs.id_kelas = '$id_kelas'
                                ORDER BY m.nim ASC
                            ");

                            $no = 1;
                            if(mysqli_num_rows($q_mhs) == 0) {
                                echo "<tr><td colspan='8' class='text-center py-5 text-muted'>Belum ada mahasiswa di kelas ini.</td></tr>";
                            }

                            while($row = mysqli_fetch_assoc($q_mhs)){
                                $tugas = $row['nilai_tugas'] ?? 0;
                                $uts   = $row['nilai_uts'] ?? 0;
                                $uas   = $row['nilai_uas'] ?? 0;
                                $akhir = $row['nilai_akhir'] ?? 0;
                                $grade = $row['grade'] ?? '-';
                            ?>
                                <tr>
                                    <td class="text-center text-muted fw-bold"><?php echo $no++; ?></td>
                                    <td class="text-center font-monospace small"><?php echo $row['nim']; ?></td>
                                    <td style="text-align: left;">
                                        <span class="fw-bold text-dark"><?php echo $row['nama_lengkap']; ?></span>
                                        <input type="hidden" name="id_krs[]" value="<?php echo $row['id_krs']; ?>">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="tugas[]" class="form-control-clean w-100 inp-tugas" value="<?php echo $tugas; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="uts[]" class="form-control-clean w-100 inp-uts" value="<?php echo $uts; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="uas[]" class="form-control-clean w-100 inp-uas" value="<?php echo $uas; ?>" required>
                                    </td>
                                    <td class="text-center">
                                        <span class="nilai-akhir"><?php echo number_format($akhir, 2); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="grade-box"><?php echo $grade; ?></span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if(mysqli_num_rows($q_mhs) > 0) { ?>
            <div class="floating-action animate-entry" style="animation-delay: 0.5s;">
                <button type="submit" name="simpan_nilai" class="btn-save-float">
                    <iconify-icon icon="solar:diskette-bold" style="font-size: 1.2rem;"></iconify-icon>
                    Simpan Semua Nilai
                </button>
            </div>
            <?php } ?>
        </form>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll('input[type="number"]');
            
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('tr');
                    
                    const tugas = parseFloat(row.querySelector('.inp-tugas').value) || 0;
                    const uts   = parseFloat(row.querySelector('.inp-uts').value) || 0;
                    const uas   = parseFloat(row.querySelector('.inp-uas').value) || 0;

                    const akhir = (tugas * 0.30) + (uts * 0.30) + (uas * 0.40);
                    row.querySelector('.nilai-akhir').innerText = akhir.toFixed(2);

                    let grade = 'E';
                    if (akhir >= 86) grade = 'A';
                    else if (akhir >= 81) grade = 'AB';
                    else if (akhir >= 71) grade = 'B';
                    else if (akhir >= 66) grade = 'BC';
                    else if (akhir >= 61) grade = 'C';
                    else if (akhir >= 56) grade = 'CD';
                    else if (akhir >= 51) grade = 'D';

                    const gradeBox = row.querySelector('.grade-box');
                    gradeBox.innerText = grade;
                    
                    if(grade === 'A' || grade === 'AB') {
                        gradeBox.style.background = '#dcfce7'; gradeBox.style.color = '#15803d';
                    } else if(grade === 'E' || grade === 'D') {
                        gradeBox.style.background = '#fee2e2'; gradeBox.style.color = '#b91c1c';
                    } else {
                        gradeBox.style.background = '#f1f5f9'; gradeBox.style.color = '#64748b';
                    }
                });
            });
        });
    </script>

</body>
</html>