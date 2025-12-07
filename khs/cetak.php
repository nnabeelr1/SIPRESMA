<?php
session_start();
include '../config/koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// 2. Ambil Data Mahasiswa
$username = $_SESSION['username'];
$mhs = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT m.*, d.nama_lengkap as nama_doswal, d.nidn, p.nama_prodi, p.jenjang 
    FROM mahasiswa m 
    JOIN user u ON m.id_user = u.id_user 
    LEFT JOIN dosen d ON m.dosen_wali = d.nidn
    LEFT JOIN prodi p ON m.id_prodi = p.id_prodi
    WHERE u.username='$username'
"));

// 3. Ambil Semester Aktif
$smt = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM semester WHERE status='aktif'"));
if(!$smt) {
    echo "Belum ada semester aktif."; exit();
}
$id_smt = $smt['id_semester'];

// Locale Tanggal
setlocale(LC_TIME, 'id_ID');
$tanggal_cetak = date('d F Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KHS_<?php echo $mhs['nim']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* SETUP KERTAS A4 */
        @page { size: A4; margin: 2cm; }
        
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            color: #000;
            background: white;
        }

        /* KOP SURAT (SAMA PERSIS KRS) */
        .header-kop {
            border-bottom: 3px double black;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 20px;
        }
        .header-text h2 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .header-text h3 { margin: 0; font-size: 14pt; font-weight: bold; }
        .header-text p { margin: 0; font-size: 10pt; font-style: italic; }

        /* TABEL BIODATA */
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px; vertical-align: top; }
        .label-col { width: 150px; font-weight: bold; }
        .colon-col { width: 10px; }

        /* TABEL NILAI (HITAM PEKAT) */
        .khs-table {
            width: 100%; border-collapse: collapse; margin-bottom: 20px;
        }
        .khs-table th, .khs-table td {
            border: 1px solid black; padding: 6px 8px; text-align: center;
        }
        .khs-table th {
            background-color: #f0f0f0 !important; font-weight: bold;
            -webkit-print-color-adjust: exact;
        }
        .text-left { text-align: left !important; }

        /* IPS BOX */
        .ips-container {
            border: 1px solid black;
            padding: 10px;
            width: 300px;
            margin-left: auto;
            text-align: center;
            font-weight: bold;
            margin-bottom: 40px;
        }

        /* TANDA TANGAN */
        .signature-section {
            display: flex; justify-content: space-between; margin-top: 30px;
            page-break-inside: avoid;
        }
        .sign-box { width: 40%; text-align: center; }
        .sign-space { height: 80px; }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print position-fixed top-0 start-0 p-3">
        <a href="index.php" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>

    <div class="header-kop">
        <div style="font-size: 3rem; font-weight: bold; border: 2px solid black; padding: 0 15px; border-radius: 50%;">S</div>
        
        <div class="header-text">
            <h2>UNIVERSITAS SIPRESMA INDONESIA</h2>
            <h3>FAKULTAS ILMU KOMPUTER & TEKNOLOGI</h3>
            <p>Jl. Jendral Sudirman No. 123, Semarang, Jawa Tengah | Telp: (024) 1234567</p>
            <p>Website: www.sipresma.ac.id | Email: info@sipresma.ac.id</p>
        </div>
    </div>

    <div class="text-center mb-4">
        <h4 style="text-decoration: underline; font-weight: bold; margin-bottom: 5px;">KARTU HASIL STUDI (KHS)</h4>
        <span>Semester: <?php echo $smt['nama_semester']; ?></span>
    </div>

    <table class="info-table">
        <tr>
            <td class="label-col">Nama Mahasiswa</td>
            <td class="colon-col">:</td>
            <td><?php echo strtoupper($mhs['nama_lengkap']); ?></td>
            
            <td class="label-col">Program Studi</td>
            <td class="colon-col">:</td>
            <td><?php echo $mhs['nama_prodi']; ?></td>
        </tr>
        <tr>
            <td class="label-col">NIM</td>
            <td class="colon-col">:</td>
            <td><?php echo $mhs['nim']; ?></td>

            <td class="label-col">Tahun Ajaran</td>
            <td class="colon-col">:</td>
            <td><?php echo date('Y'); ?></td>
        </tr>
        <tr>
            <td class="label-col">Dosen Wali</td>
            <td class="colon-col">:</td>
            <td colspan="4"><?php echo $mhs['nama_doswal']; ?></td>
        </tr>
    </table>

    <table class="khs-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Kode MK</th>
                <th>Mata Kuliah</th>
                <th width="8%">SKS</th>
                <th width="8%">Nilai</th>
                <th width="8%">Bobot</th>
                <th width="10%">Mutu (KxN)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($koneksi, "
                SELECT krs.*, m.nama_mk, m.kode_mk, m.sks, n.grade 
                FROM krs
                JOIN kelas k ON krs.id_kelas = k.id_kelas
                JOIN matakuliah m ON k.kode_mk = m.kode_mk
                LEFT JOIN nilai n ON krs.id_krs = n.id_krs
                WHERE krs.nim='{$mhs['nim']}' AND krs.id_semester='{$id_smt}'
                ORDER BY m.nama_mk ASC
            ");

            $no = 1;
            $total_sks = 0;
            $total_mutu = 0;

            while($row = mysqli_fetch_assoc($query)) {
                $grade = $row['grade'] ? $row['grade'] : 'E';
                $sks = $row['sks'];

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
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['kode_mk']; ?></td>
                    <td class="text-left"><?php echo $row['nama_mk']; ?></td>
                    <td><?php echo $sks; ?></td>
                    <td><?php echo $grade; ?></td>
                    <td><?php echo number_format($bobot, 2); ?></td>
                    <td><?php echo number_format($mutu, 2); ?></td>
                </tr>
            <?php } ?>

            <?php for($i=0; $i<(8-$no); $i++) { ?>
                <tr style="height: 25px;">
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold; padding-right: 15px;">TOTAL</td>
                <td style="font-weight: bold;"><?php echo $total_sks; ?></td>
                <td colspan="2"></td>
                <td style="font-weight: bold;"><?php echo number_format($total_mutu, 2); ?></td>
            </tr>
        </tfoot>
    </table>

    <?php
        $ips = ($total_sks > 0) ? ($total_mutu / $total_sks) : 0;
    ?>
    <div class="ips-container">
        INDEKS PRESTASI SEMESTER (IPS) : <?php echo number_format($ips, 2); ?>
    </div>

    <div class="signature-section">
        <div class="sign-box">
            Mengetahui,<br>
            Dosen Wali
            <div class="sign-space"></div>
            <strong><u><?php echo $mhs['nama_doswal']; ?></u></strong><br>
            NIDN. <?php echo $mhs['nidn'] ?? '......................'; ?>
        </div>

        <div class="sign-box">
            Semarang, <?php echo $tanggal_cetak; ?><br>
            Mahasiswa
            <div class="sign-space"></div>
            <strong><u><?php echo $mhs['nama_lengkap']; ?></u></strong><br>
            NIM. <?php echo $mhs['nim']; ?>
        </div>
    </div>

</body>
</html> 