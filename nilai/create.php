<?php
session_start();
include '../config/koneksi.php';

// Cek Dosen
if ($_SESSION['role'] != 'dosen') {
    header("Location: ../index.php");
    exit();
}

$id_kelas = $_GET['id_kelas'];

// --- BAGIAN 1: LOGIC SIMPAN NILAI (PROCESS) ---
if (isset($_POST['simpan_nilai'])) {
    
    // Ambil array inputan dari Form
    $tugas_arr = $_POST['tugas']; // Array [id_krs => nilai]
    $uts_arr   = $_POST['uts'];
    $uas_arr   = $_POST['uas'];

    // Looping setiap mahasiswa
    foreach ($tugas_arr as $id_krs => $nilai_tugas) {
        $nilai_uts = $uts_arr[$id_krs];
        $nilai_uas = $uas_arr[$id_krs];

        // A. HITUNG NILAI AKHIR (Rumus: 30% + 30% + 40%)
        // Bobot ini biasanya standar, tapi kalau beda bisa diubah angkanya di sini
        $akhir = ($nilai_tugas * 0.30) + ($nilai_uts * 0.30) + ($nilai_uas * 0.40);

        // B. TENTUKAN GRADE SESUAI TABEL UNNES 2024
        // Logic Waterfall (Cek dari yang paling besar dulu)
        if ($akhir >= 86) {
            $grade = 'A';   // 86 - 100
        } elseif ($akhir >= 81) {
            $grade = 'AB';  // 81 - 85
        } elseif ($akhir >= 71) {
            $grade = 'B';   // 71 - 80
        } elseif ($akhir >= 66) {
            $grade = 'BC';  // 66 - 70
        } elseif ($akhir >= 61) {
            $grade = 'C';   // 61 - 65
        } elseif ($akhir >= 56) {
            $grade = 'CD';  // 56 - 60
        } elseif ($akhir >= 51) {
            $grade = 'D';   // 51 - 55
        } else {
            $grade = 'E';   // < 51
        }

        // C. CEK & SIMPAN KE DATABASE
        $cek = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_krs='$id_krs'");
        
        if (mysqli_num_rows($cek) > 0) {
            // KALAU SUDAH ADA -> UPDATE (Edit)
            $query = "UPDATE nilai SET 
                      nilai_tugas='$nilai_tugas', nilai_uts='$nilai_uts', 
                      nilai_uas='$nilai_uas', nilai_akhir='$akhir', grade='$grade' 
                      WHERE id_krs='$id_krs'";
        } else {
            // KALAU BELUM ADA -> INSERT (Baru)
            $query = "INSERT INTO nilai (id_krs, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir, grade) 
                      VALUES ('$id_krs', '$nilai_tugas', '$nilai_uts', '$nilai_uas', '$akhir', '$grade')";
        }

        mysqli_query($koneksi, $query);
    }

    // Refresh halaman biar nilai terupdate
    echo "<script>alert('Nilai berhasil disimpan! Grade diperbarui sesuai standar.'); window.location='create.php?id_kelas=$id_kelas';</script>";
}
// --- END LOGIC ---

// Ambil Info Kelas buat Judul
$q_info = mysqli_query($koneksi, "
    SELECT k.*, m.nama_mk, m.sks 
    FROM kelas k
    JOIN matakuliah m ON k.kode_mk = m.kode_mk
    WHERE k.id_kelas = '$id_kelas'
");
$info = mysqli_fetch_assoc($q_info);

// Ambil Daftar Mahasiswa + Nilai
$q_mhs = mysqli_query($koneksi, "
    SELECT krs.id_krs, mhs.nim, mhs.nama_lengkap, 
           n.nilai_tugas, n.nilai_uts, n.nilai_uas, n.nilai_akhir, n.grade
    FROM krs
    JOIN mahasiswa mhs ON krs.nim = mhs.nim
    LEFT JOIN nilai n ON krs.id_krs = n.id_krs
    WHERE krs.id_kelas = '$id_kelas'
    ORDER BY mhs.nim ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Nilai - <?php echo $info['nama_mk']; ?></title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5 mb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Input Nilai Mahasiswa</h3>
                <p class="text-muted mb-0">
                    Matkul: <strong><?php echo $info['nama_mk']; ?> (Kelas <?php echo $info['nama_kelas']; ?>)</strong>
                </p>
            </div>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST">
                    
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-success">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th width="10%">Tugas (30%)</th>
                                <th width="10%">UTS (30%)</th>
                                <th width="10%">UAS (40%)</th>
                                <th width="10%">Akhir</th>
                                <th width="5%">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if(mysqli_num_rows($q_mhs) == 0) {
                                echo "<tr><td colspan='8' class='text-center text-danger fw-bold'>Belum ada mahasiswa di kelas ini.</td></tr>";
                            }
                            
                            while($row = mysqli_fetch_assoc($q_mhs)) { 
                                $id = $row['id_krs'];
                                
                                // Logic warna-warni grade biar cantik
                                $bg_grade = 'bg-light';
                                if($row['grade'] == 'A') $bg_grade = 'bg-success text-white';
                                elseif($row['grade'] == 'E') $bg_grade = 'bg-danger text-white';
                                elseif(strpos($row['grade'], 'B') !== false) $bg_grade = 'bg-info text-dark';
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['nim']; ?></td>
                                    <td><?php echo $row['nama_lengkap']; ?></td>
                                    
                                    <td>
                                        <input type="number" name="tugas[<?php echo $id; ?>]" class="form-control text-center" value="<?php echo $row['nilai_tugas']; ?>" step="0.01" min="0" max="100" required>
                                    </td>
                                    <td>
                                        <input type="number" name="uts[<?php echo $id; ?>]" class="form-control text-center" value="<?php echo $row['nilai_uts']; ?>" step="0.01" min="0" max="100" required>
                                    </td>
                                    <td>
                                        <input type="number" name="uas[<?php echo $id; ?>]" class="form-control text-center" value="<?php echo $row['nilai_uas']; ?>" step="0.01" min="0" max="100" required>
                                    </td>
                                    
                                    <td class="text-center fw-bold bg-light"><?php echo $row['nilai_akhir']; ?></td>
                                    <td class="text-center fw-bold <?php echo $bg_grade; ?>"><?php echo $row['grade']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <?php if(mysqli_num_rows($q_mhs) > 0) { ?>
                        <div class="text-end mt-3">
                            <button type="submit" name="simpan_nilai" class="btn btn-primary btn-lg px-5">
                                💾 SIMPAN NILAI
                            </button>
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>

</body>
</html>