<?php
session_start();
include '../config/koneksi.php';

// Cek Dosen
if ($_SESSION['role'] != 'dosen') { echo "Anda bukan Dosen!"; exit(); }

// 1. Cari NIDN Dosen yang login
$id_user = $_SESSION['id_user'];
$q_dosen = mysqli_query($koneksi, "SELECT nidn, nama_lengkap FROM dosen WHERE id_user='$id_user'");
$data_dosen = mysqli_fetch_assoc($q_dosen);
$nidn_saya = $data_dosen['nidn'];

// 2. Logic Tombol Terima/Tolak
if (isset($_POST['aksi'])) {
    $id_match = $_POST['id_match'];
    $peran    = $_POST['peran_saya']; // Saya sebagai doswal mentee atau mentor?
    $aksi     = $_POST['aksi'];

    if ($aksi == 'terima') {
        // A. Update kolom ACC sesuai peran saya
        if ($peran == 'doswal_mentee') {
            mysqli_query($koneksi, "UPDATE peer_support SET acc_doswal_mentee=1 WHERE id_match='$id_match'");
        } else {
            mysqli_query($koneksi, "UPDATE peer_support SET acc_doswal_mentor=1 WHERE id_match='$id_match'");
        }

        // B. Cek apakah SEKARANG sudah setuju dua-duanya?
        $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM peer_support WHERE id_match='$id_match'"));
        
        if ($cek['acc_doswal_mentee'] == 1 && $cek['acc_doswal_mentor'] == 1) {
            // Kalau dua-duanya 1, baru SAH jadi AKTIF
            mysqli_query($koneksi, "UPDATE peer_support SET status='aktif' WHERE id_match='$id_match'");
            echo "<script>alert('SAH! Persetujuan lengkap. Status sekarang AKTIF.');</script>";
        } else {
            // Kalau baru satu pihak
            echo "<script>alert('Anda menyetujui. Menunggu persetujuan Dosen Wali pasangannya...');</script>";
        }

    } else {
        // [LOGIKA BARU] JANGAN DELETE, TAPI UPDATE STATUS JADI 'DITOLAK'
        // Supaya admin tau kalau pasangan ini gak cocok, dan gak disaranin lagi
        mysqli_query($koneksi, "UPDATE peer_support SET status='ditolak' WHERE id_match='$id_match'");
        echo "<script>alert('Usulan Ditolak. Sistem akan mencarikan pasangan lain untuk mahasiswa tersebut.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Dosen Wali</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">🎓 SIPRESMA Dosen</a>
            <div class="text-white">
                Halo, <?php echo $data_dosen['nama_lengkap']; ?>
                <a href="../logout.php" class="btn btn-light btn-sm ms-3 text-success fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-success">📋 Persetujuan Peer Support</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>Aturan Baru:</strong> Program Peer Support baru akan <strong>AKTIF</strong> jika 
                    Dosen Wali MENTEE <strong>DAN</strong> Dosen Wali MENTOR sama-sama menyetujui.
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Detail Pasangan</th>
                            <th>Posisi Anda</th>
                            <th>Status Persetujuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query: Cari match yang melibatkan mahasiswa bimbingan saya
                        $query = mysqli_query($koneksi, "
                            SELECT ps.*, 
                                   m1.nama_lengkap as nama_mentee, m1.dosen_wali as doswal_mentee,
                                   m2.nama_lengkap as nama_mentor, m2.dosen_wali as doswal_mentor
                            FROM peer_support ps
                            JOIN mahasiswa m1 ON ps.mentee_nim = m1.nim
                            JOIN mahasiswa m2 ON ps.mentor_nim = m2.nim
                            WHERE (m1.dosen_wali = '$nidn_saya' OR m2.dosen_wali = '$nidn_saya') 
                            AND ps.status = 'menunggu_dosen'
                        ");

                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='4' class='text-center text-muted'>Tidak ada usulan baru.</td></tr>";
                        }

                        while ($row = mysqli_fetch_assoc($query)) {
                            // Cek peran saya di baris ini
                            if ($row['doswal_mentee'] == $nidn_saya) {
                                $peran_saya = 'doswal_mentee';
                                $label_peran = "Dosen Wali Mentee";
                                $sudah_acc = $row['acc_doswal_mentee'];
                            } else {
                                $peran_saya = 'doswal_mentor';
                                $label_peran = "Dosen Wali Mentor";
                                $sudah_acc = $row['acc_doswal_mentor'];
                            }
                        ?>
                            <tr>
                                <td>
                                    Mentee: <strong><?php echo $row['nama_mentee']; ?></strong> (Butuh)<br>
                                    Mentor: <strong><?php echo $row['nama_mentor']; ?></strong> (Pintar)
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark"><?php echo $label_peran; ?></span>
                                </td>
                                <td>
                                    Doswal Mentee: 
                                    <?php echo ($row['acc_doswal_mentee'] == 1) ? '✅ Oke' : '⏳ Menunggu'; ?> <br>
                                    
                                    Doswal Mentor: 
                                    <?php echo ($row['acc_doswal_mentor'] == 1) ? '✅ Oke' : '⏳ Menunggu'; ?>
                                </td>
                                <td>
                                    <?php if ($sudah_acc == 0) { ?>
                                        <form method="POST">
                                            <input type="hidden" name="id_match" value="<?php echo $row['id_match']; ?>">
                                            <input type="hidden" name="peran_saya" value="<?php echo $peran_saya; ?>">
                                            
                                            <button type="submit" name="aksi" value="terima" class="btn btn-success btn-sm">✅ Setujui</button>
                                            <button type="submit" name="aksi" value="tolak" class="btn btn-danger btn-sm">❌ Tolak</button>
                                        </form>
                                    <?php } else { ?>
                                        <span class="text-muted small"><i>Anda sudah menyetujui.<br>Menunggu rekan dosen.</i></span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>