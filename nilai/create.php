<?php
session_start();
include '../config/koneksi.php';

// 1. Cek Login Dosen
if ($_SESSION['role'] != 'dosen') {
    header("Location: ../index.php");
    exit();
}

// 2. Ambil Data Dosen (Untuk Navbar)
$id_user = $_SESSION['id_user'];
$dosen_nav = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_lengkap FROM dosen WHERE id_user='$id_user'"));
$nama_depan = explode(' ', trim($dosen_nav['nama_lengkap']))[0];

// 3. Ambil ID Kelas dari URL
if (!isset($_GET['id_kelas'])) {
    header("Location: index.php");
    exit();
}
$id_kelas = $_GET['id_kelas'];

// 4. Ambil Semester Aktif (Manual, tanpa JOIN)
$q_smt = mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'");
$smt_aktif = mysqli_fetch_assoc($q_smt);
$nama_semester_aktif = $smt_aktif ? $smt_aktif['nama_semester'] : '-';

// 5. Ambil Detail Kelas & Matkul
$q_kelas = mysqli_query($koneksi, "
    SELECT k.*, m.nama_mk, m.sks, d.nama_lengkap as nama_dosen
    FROM kelas k
    JOIN matakuliah m ON k.kode_mk = m.kode_mk
    JOIN dosen d ON k.nidn = d.nidn
    WHERE k.id_kelas = '$id_kelas'
");
$kelas = mysqli_fetch_assoc($q_kelas);

// 6. Proses Simpan Nilai
if (isset($_POST['simpan_nilai'])) {
    $id_krs_array = $_POST['id_krs'];
    $tugas_array  = $_POST['tugas']; // Ini array inputan form
    $uts_array    = $_POST['uts'];
    $uas_array    = $_POST['uas'];
    
    for ($i = 0; $i < count($id_krs_array); $i++) {
        $id_krs = $id_krs_array[$i];
        $tugas  = floatval($tugas_array[$i]);
        $uts    = floatval($uts_array[$i]);
        $uas    = floatval($uas_array[$i]);

        // Hitung Nilai Akhir
        $nilai_akhir = ($tugas * 0.30) + ($uts * 0.30) + ($uas * 0.40);

        // Tentukan Grade
        if ($nilai_akhir >= 85) $grade = "A";
        elseif ($nilai_akhir >= 80) $grade = "AB";
        elseif ($nilai_akhir >= 70) $grade = "B";
        elseif ($nilai_akhir >= 65) $grade = "BC";
        elseif ($nilai_akhir >= 60) $grade = "C";
        elseif ($nilai_akhir >= 50) $grade = "CD";
        elseif ($nilai_akhir >= 40) $grade = "D";
        else $grade = "E";

        // Cek & Update/Insert (Pakai nama kolom yang benar: nilai_tugas, dll)
        $cek = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_krs='$id_krs'");
        if (mysqli_num_rows($cek) > 0) {
            $query = "UPDATE nilai SET nilai_tugas='$tugas', nilai_uts='$uts', nilai_uas='$uas', nilai_akhir='$nilai_akhir', grade='$grade' WHERE id_krs='$id_krs'";
        } else {
            $query = "INSERT INTO nilai (id_krs, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir, grade) VALUES ('$id_krs', '$tugas', '$uts', '$uas', '$nilai_akhir', '$grade')";
        }
        mysqli_query($koneksi, $query);
    }

    echo "<script>alert('✅ Berhasil! Semua nilai telah disimpan.'); window.location.href='index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Nilai - <?php echo $kelas['nama_mk']; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
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
            padding-bottom: 5rem;
        }

        .navbar-clean {
            background: white; border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 0.8rem 0; position: sticky; top: 0; z-index: 100;
        }
        .logo-box {
            background: rgba(16, 185, 129, 0.1); color: #10b981;
            width: 42px; height: 42px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .page-header {
            margin-top: 2rem; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: end;
        }
        .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        .info-box {
            background: white; border-radius: 16px; padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.03); box-shadow: var(--card-shadow);
            margin-bottom: 2rem; display: flex; gap: 3rem; align-items: center;
        }
        .info-item label { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 0.2rem; }
        .info-item div { font-size: 1.1rem; font-weight: 700; color: var(--text-main); }

        .card-modern {
            background: white; border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.03); box-shadow: var(--card-shadow);
            overflow: hidden; padding: 0;
        }
        
        .table-custom thead th {
            background-color: #f8fafc; color: var(--text-muted);
            font-weight: 700; font-size: 0.75rem; text-transform: uppercase;
            letter-spacing: 0.05em; padding: 1rem; border-bottom: 1px solid #e2e8f0;
            vertical-align: middle; text-align: center;
        }
        .table-custom tbody td {
            padding: 1rem; vertical-align: middle;
            border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;
        }

        .form-control-clean {
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 8px; text-align: center; font-weight: 600;
            color: var(--text-main); padding: 0.5rem;
        }
        .form-control-clean:focus {
            background: white; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); outline: none;
        }

        .nilai-akhir { font-weight: 800; color: var(--text-main); font-size: 1rem; }
        .grade-box {
            display: inline-block; width: 36px; height: 36px; line-height: 36px;
            border-radius: 8px; font-weight: 800; text-align: center;
            background: #f1f5f9; color: var(--text-muted);
        }
        
        .floating-action {
            position: fixed; bottom: 30px; right: 30px; z-index: 99;
            background: white; padding: 10px; border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid rgba(0,0,0,0.05);
        }
        .btn-save-float {
            background: var(--primary); color: white; border: none;
            padding: 0.8rem 2rem; border-radius: 50px; font-weight: 700;
            display: flex; align-items: center; gap: 8px; transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        .btn-save-float:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5); }

        .btn-clean {
            background: white; border: 1px solid #e2e8f0; color: var(--text-main);
            font-weight: 600; padding: 0.6rem 1.2rem; border-radius: 12px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            text-decoration: none;
        }
        .btn-clean:hover { background: #f8fafc; border-color: #cbd5e1; }
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
                        LECTURER PORTAL
                    </small>
                </div>
            </a>
            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-block text-end" style="line-height: 1.2;">
                    <span class="fw-bold d-block text-dark" style="font-size: 0.9rem;">
                        <?php echo $nama_depan; ?>
                    </span>
                    <small class="text-muted" style="font-size: 0.75rem;">Lecturer</small>
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
                <h2 class="page-title">Input Nilai Mahasiswa</h2>
                <p class="page-subtitle">Silakan isi nilai Tugas, UTS, dan UAS.</p>
            </div>
            <div>
                <a href="index.php" class="btn-clean">
                    <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Kembali
                </a>
            </div>
        </div>

        <div class="info-box">
            <div class="info-item">
                <label>Mata Kuliah</label>
                <div class="text-primary"><?php echo $kelas['nama_mk']; ?></div>
            </div>
            <div class="info-item">
                <label>Kelas</label>
                <div><?php echo $kelas['nama_kelas']; ?></div>
            </div>
            <div class="info-item">
                <label>SKS</label>
                <div><?php echo $kelas['sks']; ?></div>
            </div>
            <div class="info-item">
                <label>Semester</label>
                <div><?php echo $nama_semester_aktif; ?></div>
            </div>
        </div>

        <form method="POST" id="formNilai">
            <div class="card-modern">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIM</th>
                                <th width="25%" style="text-align: left; padding-left: 1rem;">Nama Mahasiswa</th>
                                <th width="12%">Tugas (30%)</th>
                                <th width="12%">UTS (30%)</th>
                                <th width="12%">UAS (40%)</th>
                                <th width="10%">Akhir</th>
                                <th width="9%">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ambil nama kolom yang BENAR: nilai_tugas, nilai_uts, nilai_uas
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
                                // Gunakan nama kolom yang benar dari DB
                                $tugas = $row['nilai_tugas'] ?? 0;
                                $uts   = $row['nilai_uts'] ?? 0;
                                $uas   = $row['nilai_uas'] ?? 0;
                                $akhir = $row['nilai_akhir'] ?? 0;
                                $grade = $row['grade'] ?? '-';
                            ?>
                                <tr>
                                    <td class="text-center text-muted fw-bold"><?php echo $no++; ?></td>
                                    <td class="text-center font-monospace"><?php echo $row['nim']; ?></td>
                                    <td style="padding-left: 1rem;">
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
            <div class="floating-action">
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
                    if (akhir >= 85) grade = 'A';
                    else if (akhir >= 80) grade = 'AB';
                    else if (akhir >= 70) grade = 'B';
                    else if (akhir >= 65) grade = 'BC';
                    else if (akhir >= 60) grade = 'C';
                    else if (akhir >= 50) grade = 'CD';
                    else if (akhir >= 40) grade = 'D';

                    const gradeBox = row.querySelector('.grade-box');
                    gradeBox.innerText = grade;
                    
                    gradeBox.style.background = (grade === 'A' || grade === 'AB') ? '#dcfce7' : 
                                                (grade === 'E' || grade === 'D') ? '#fee2e2' : '#f1f5f9';
                    gradeBox.style.color = (grade === 'A' || grade === 'AB') ? '#166534' : 
                                           (grade === 'E' || grade === 'D') ? '#b91c1c' : '#64748b';
                });
            });
        });
    </script>

</body>
</html>