<?php
session_start();
include '../config/koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// 2. Ambil Data Mahasiswa & Semester Aktif
$username = $_SESSION['username'];
$q_mhs = mysqli_query($koneksi, "
    SELECT m.*, d.nama_lengkap as nama_doswal, p.nama_prodi 
    FROM mahasiswa m 
    JOIN user u ON m.id_user = u.id_user 
    LEFT JOIN dosen d ON m.dosen_wali = d.nidn
    LEFT JOIN prodi p ON m.id_prodi = p.id_prodi
    WHERE u.username='$username'
");
$mhs = mysqli_fetch_assoc($q_mhs);
$nim_saya = $mhs['nim'];

$q_smt = mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'");
$smt = mysqli_fetch_assoc($q_smt);
$id_smt = $smt['id_semester'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak KRS - <?php echo $nim_saya; ?></title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS Khusus Cetak: Hilangkan tombol saat di-print */
        @media print {
            .no-print { display: none; }
        }
        body { font-family: "Times New Roman", Times, serif; }
    </style>
</head>
<body onload="window.print()">

    <div class="container mt-4">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold">UNIVERSITAS SIPRESMA INDONESIA</h3>
            <h5 class="fw-bold">KARTU RENCANA STUDI (KRS)</h5>
            <p>Semester: <?php echo $smt['nama_semester']; ?></p>
            <hr style="border: 2px solid black;">
        </div>

        <table class="table table-borderless table-sm w-75 mb-3">
            <tr>
                <td width="20%">Nama Mahasiswa</td>
                <td width="2%">:</td>
                <td><strong><?php echo $mhs['nama_lengkap']; ?></strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td><?php echo $mhs['nim']; ?></td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td><?php echo $mhs['nama_prodi']; ?></td>
            </tr>
            <tr>
                <td>Dosen Wali</td>
                <td>:</td>
                <td><?php echo $mhs['nama_doswal']; ?></td>
            </tr>
        </table>

        <table class="table table-bordered border-dark">
            <thead class="text-center table-secondary border-dark">
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Kode MK</th>
                    <th>Mata Kuliah</th>
                    <th width="10%">Kelas</th>
                    <th width="10%">SKS</th>
                    <th>Jadwal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q_krs = mysqli_query($koneksi, "
                    SELECT krs.*, k.nama_kelas, k.hari, k.jam_mulai, k.jam_selesai, 
                           m.kode_mk, m.nama_mk, m.sks
                    FROM krs
                    JOIN kelas k ON krs.id_kelas = k.id_kelas
                    JOIN matakuliah m ON k.kode_mk = m.kode_mk
                    WHERE krs.nim='$nim_saya' AND krs.id_semester='$id_smt'
                ");

                $no = 1;
                $total_sks = 0;
                while($row = mysqli_fetch_assoc($q_krs)) {
                    $total_sks += $row['sks'];
                ?>
                    <tr>
                        <td class="text-center"><?php echo $no++; ?></td>
                        <td class="text-center"><?php echo $row['kode_mk']; ?></td>
                        <td><?php echo $row['nama_mk']; ?></td>
                        <td class="text-center"><?php echo $row['nama_kelas']; ?></td>
                        <td class="text-center"><?php echo $row['sks']; ?></td>
                        <td><?php echo $row['hari'] . ', ' . date('H:i', strtotime($row['jam_mulai'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-bold">Total SKS Yang Diambil:</td>
                    <td class="text-center fw-bold"><?php echo $total_sks; ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="row mt-5">
            <div class="col-4 text-center">
                <br>
                Mengetahui,<br>
                Dosen Wali
                <br><br><br><br>
                ( <strong><?php echo $mhs['nama_doswal']; ?></strong> )
            </div>
            <div class="col-4 offset-4 text-center">
                Semarang, <?php echo date('d F Y'); ?> <br>
                Mahasiswa Yang Bersangkutan
                <br><br><br><br>
                ( <strong><?php echo $mhs['nama_lengkap']; ?></strong> )
            </div>
        </div>

    </div>

</body>
</html>