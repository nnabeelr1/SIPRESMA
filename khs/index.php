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

// 3. Ambil Semester Aktif
$smt = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'"));
// Kalau tidak ada semester aktif, cegah error
if(!$smt) {
    echo "Belum ada semester aktif."; exit();
}
$id_smt = $smt['id_semester'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Hasil Studi (KHS)</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sembunyikan tombol saat dicetak */
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 no-print">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">📊 KHS Online</a>
            <a href="../dashboard/welcome_mhs.php" class="btn btn-light btn-sm text-primary fw-bold">Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container mb-5">
        
        <div class="text-center d-none d-print-block">
            <h3>KARTU HASIL STUDI (KHS)</h3>
            <p>Semester: <?php echo $smt['nama_semester']; ?></p>
            <hr>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td width="30%">Nama</td><td>: <strong><?php echo $mhs['nama_lengkap']; ?></strong></td></tr>
                            <tr><td>NIM</td><td>: <?php echo $mhs['nim']; ?></td></tr>
                            <tr><td>Prodi</td><td>: <?php echo $mhs['nama_prodi']; ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td width="30%">Semester</td><td>: <?php echo $smt['nama_semester']; ?></td></tr>
                            <tr><td>Dosen Wali</td><td>: <?php echo $mhs['nama_doswal']; ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">Hasil Studi Mahasiswa</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Mata Kuliah</th>
                            <th width="10%" class="text-center">SKS (K)</th>
                            <th width="10%" class="text-center">Nilai</th>
                            <th width="10%" class="text-center">Bobot (N)</th>
                            <th width="10%" class="text-center">Mutu (K x N)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query Gabungan: KRS + Kelas + Matkul + Nilai
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
                            echo "<tr><td colspan='6' class='text-center text-muted p-4'>Belum ada KRS yang diambil semester ini.</td></tr>";
                        }

                        while($row = mysqli_fetch_assoc($query)) {
                            // Jika belum dinilai dosen, anggap nilainya E (atau strip -)
                            $grade = $row['grade'] ? $row['grade'] : 'E';
                            $sks   = $row['sks'];

                            // LOGIC KONVERSI BOBOT (SESUAI GAMBAR UNNES 2024)
                            switch ($grade) {
                                case 'A':  $bobot = 4.00; break;
                                case 'AB': $bobot = 3.50; break;
                                case 'B':  $bobot = 3.00; break;
                                case 'BC': $bobot = 2.50; break;
                                case 'C':  $bobot = 2.00; break;
                                case 'CD': $bobot = 1.50; break;
                                case 'D':  $bobot = 1.00; break;
                                default:   $bobot = 0.00; // E atau kosong
                            }

                            $mutu = $sks * $bobot;
                            
                            // Hitung Total
                            $total_sks += $sks;
                            $total_mutu += $mutu;
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo $row['nama_mk']; ?></td>
                                <td class="text-center"><?php echo $sks; ?></td>
                                <td class="text-center fw-bold text-primary"><?php echo $grade; ?></td>
                                <td class="text-center"><?php echo number_format($bobot, 2); ?></td>
                                <td class="text-center fw-bold"><?php echo number_format($mutu, 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">TOTAL:</td>
                            <td class="text-center"><?php echo $total_sks; ?></td>
                            <td></td>
                            <td></td>
                            <td class="text-center"><?php echo number_format($total_mutu, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php
            // Hitung IPS (Indeks Prestasi Semester)
            // Rumus: Total Mutu / Total SKS
            $ips = ($total_sks > 0) ? ($total_mutu / $total_sks) : 0;
        ?>
        <div class="row mt-4">
            <div class="col-md-4 offset-md-8">
                <div class="card bg-info text-white text-center shadow">
                    <div class="card-body">
                        <h5>Indeks Prestasi Semester (IPS)</h5>
                        <h1 class="display-3 fw-bold mb-0"><?php echo number_format($ips, 2); ?></h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 mb-5 no-print">
            <button onclick="window.print()" class="btn btn-warning btn-lg fw-bold shadow-sm">🖨️ Cetak Transkrip / KHS</button>
        </div>

    </div>
</body>
</html>