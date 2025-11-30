<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != 'admin') { header("Location: index.php"); exit(); }

// 1. Cari Mahasiswa Berisiko (Mentee)
// UPDATE LOGIC: Sekarang cari yang IPK < 2.50
$q_mentee = mysqli_query($koneksi, "
    SELECT * FROM mahasiswa 
    WHERE ipk_terakhir < 2.50 AND ipk_terakhir > 0
    AND nim NOT IN (SELECT mentee_nim FROM peer_support)
");

$jumlah_pasangan = 0;

while ($mentee = mysqli_fetch_assoc($q_mentee)) {
    $prodi_mentee    = $mentee['id_prodi'];
    $nim_mentee      = $mentee['nim'];
    $angkatan_mentee = $mentee['angkatan']; 

    // 2. Cari Mentor (IPK Tinggi)
    // SYARAT: IPK > 3.50, Prodi SAMA, Angkatan LEBIH TUA/SAMA
    $q_mentor = mysqli_query($koneksi, "
        SELECT * FROM mahasiswa 
        WHERE ipk_terakhir > 3.50 
        AND id_prodi = '$prodi_mentee'
        AND nim != '$nim_mentee'
        AND angkatan <= '$angkatan_mentee' 
        ORDER BY RAND() LIMIT 1
    ");

    if (mysqli_num_rows($q_mentor) > 0) {
        $mentor = mysqli_fetch_assoc($q_mentor);
        $nim_mentor = $mentor['nim'];

        // 3. Jodohkan!
        $tgl = date('Y-m-d');
        $insert = mysqli_query($koneksi, "
            INSERT INTO peer_support (mentee_nim, mentor_nim, tanggal_mulai, status, acc_doswal_mentee, acc_doswal_mentor)
            VALUES ('$nim_mentee', '$nim_mentor', '$tgl', 'menunggu_dosen', 0, 0)
        ");

        if($insert) $jumlah_pasangan++;
    }
}

// Balik ke dashboard
echo "<script>
    alert('Sistem berhasil memasangkan $jumlah_pasangan pasang Peer Support (Senior/Sebaya)!'); 
    window.location='index.php';
</script>";
?>