<?php
session_start();
include '../config/koneksi.php';

// 1. Cek Login & Role Mahasiswa
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// 2. Ambil Data Mahasiswa yang Login (Termasuk ID PRODI)
$username = $_SESSION['username'];
$q_mhs = mysqli_query($koneksi, "
    SELECT m.*, u.username 
    FROM mahasiswa m 
    JOIN user u ON m.id_user = u.id_user 
    WHERE u.username='$username'
");
$data_mhs = mysqli_fetch_assoc($q_mhs);
$nim_saya = $data_mhs['nim'];
$id_prodi_saya = $data_mhs['id_prodi']; // <--- PENTING: Kita ambil ID Prodinya

// 3. Cek Semester Aktif
$q_smt = mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'");
$smt_aktif = mysqli_fetch_assoc($q_smt);

if (!$smt_aktif) {
    echo "<script>alert('Tidak ada semester aktif!'); window.location='../dashboard/welcome_mhs.php';</script>";
    exit();
}
$id_smt_aktif = $smt_aktif['id_semester'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KRS Online</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-info mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">🎓 KRS Online</a>
            <a href="../dashboard/welcome_mhs.php" class="btn btn-light btn-sm text-info fw-bold">Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container mb-5">
        
        <div class="alert alert-primary d-flex justify-content-between align-items-center">
            <div>
                <strong>Mahasiswa:</strong> <?php echo $data_mhs['nama_lengkap']; ?> (<?php echo $nim_saya; ?>)<br>
                <strong>Semester Aktif:</strong> <?php echo $smt_aktif['nama_semester']; ?>
            </div>
            <a href="cetak.php" target="_blank" class="btn btn-light btn-sm fw-bold">🖨️ Cetak KRS (KSM)</a>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">1. Pilih Mata Kuliah (Sesuai Prodi)</div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Matkul</th>
                                    <th class="text-center">Smt</th>
                                    <th class="text-center">Kls</th>
                                    <th>Jadwal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // LOGIKA PERBAIKAN:
                                // Hanya tampilkan matkul yang ID_PRODI-nya sama dengan mahasiswa
                                $query_tersedia = mysqli_query($koneksi, "
                                    SELECT k.*, m.nama_mk, m.sks, m.semester_paket, m.id_prodi
                                    FROM kelas k
                                    JOIN matakuliah m ON k.kode_mk = m.kode_mk
                                    WHERE 
                                    -- FILTER 1: HANYA MATKUL PRODI SAYA
                                    m.id_prodi = '$id_prodi_saya' 
                                    AND
                                    -- FILTER 2: YANG BELUM SAYA AMBIL
                                    k.id_kelas NOT IN (
                                        SELECT id_kelas FROM krs WHERE nim='$nim_saya' AND id_semester='$id_smt_aktif'
                                    )
                                    ORDER BY m.semester_paket ASC, m.nama_mk ASC
                                ");

                                if(mysqli_num_rows($query_tersedia) == 0) {
                                    echo "<tr><td colspan='5' class='text-center text-muted py-4'>Tidak ada mata kuliah tersedia untuk Prodi Anda.</td></tr>";
                                }

                                while($row = mysqli_fetch_assoc($query_tersedia)) {
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $row['nama_mk']; ?></strong> <br>
                                            <small class="text-muted"><?php echo $row['sks']; ?> SKS</small>
                                        </td>
                                        <td class="text-center fw-bold text-secondary"><?php echo $row['semester_paket']; ?></td>
                                        <td class="text-center"><span class="badge bg-secondary"><?php echo $row['nama_kelas']; ?></span></td>
                                        <td><small><?php echo $row['hari'] . ', ' . $row['jam_mulai']; ?></small></td>
                                        <td class="text-center">
                                            <form action="create.php" method="POST">
                                                <input type="hidden" name="id_kelas" value="<?php echo $row['id_kelas']; ?>">
                                                <input type="hidden" name="id_semester" value="<?php echo $id_smt_aktif; ?>">
                                                <input type="hidden" name="nim" value="<?php echo $nim_saya; ?>">
                                                <button type="submit" name="ambil" class="btn btn-sm btn-success">Ambil</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">2. KRS Anda (Sudah Diambil)</div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Matkul</th>
                                    <th class="text-center">Kls</th>
                                    <th class="text-center">SKS</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_sks = 0;
                                $query_krs = mysqli_query($koneksi, "
                                    SELECT krs.id_krs, m.nama_mk, m.sks, k.nama_kelas
                                    FROM krs
                                    JOIN kelas k ON krs.id_kelas = k.id_kelas
                                    JOIN matakuliah m ON k.kode_mk = m.kode_mk
                                    WHERE krs.nim='$nim_saya' AND krs.id_semester='$id_smt_aktif'
                                ");

                                if(mysqli_num_rows($query_krs) == 0) {
                                    echo "<tr><td colspan='4' class='text-center text-muted py-3'>Belum ada matkul yang diambil.</td></tr>";
                                }

                                while($krs = mysqli_fetch_assoc($query_krs)) {
                                    $total_sks += $krs['sks'];
                                ?>
                                    <tr>
                                        <td><?php echo $krs['nama_mk']; ?></td>
                                        <td class="text-center"><?php echo $krs['nama_kelas']; ?></td>
                                        <td class="text-center"><?php echo $krs['sks']; ?></td>
                                        <td class="text-center">
                                            <a href="delete.php?id=<?php echo $krs['id_krs']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Batalkan matkul ini?')">X</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                
                                <tr class="table-info fw-bold">
                                    <td colspan="2" class="text-end">Total SKS:</td>
                                    <td class="text-center"><?php echo $total_sks; ?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>