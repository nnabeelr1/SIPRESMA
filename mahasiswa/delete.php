<?php
include '../config/koneksi.php';

$nim = $_GET['nim'];

// 1. Cari dulu ID User-nya (Biar akun loginnya juga ikut terhapus nanti)
$cari_user = mysqli_query($koneksi, "SELECT id_user FROM mahasiswa WHERE nim='$nim'");
$data_user = mysqli_fetch_assoc($cari_user);
$id_user   = $data_user['id_user'];

// --- PROSES PEMBERSIHAN DATA TERKAIT (Hapus Anak-anaknya dulu) ---

// A. Hapus Prestasi
mysqli_query($koneksi, "DELETE FROM prestasi WHERE nim='$nim'");

// B. Hapus Peer Support (Baik dia sebagai Mentee maupun Mentor)
mysqli_query($koneksi, "DELETE FROM peer_support WHERE mentee_nim='$nim' OR mentor_nim='$nim'");

// C. Hapus Nilai (Lewat KRS)
// Kita harus cari dulu ID KRS milik mahasiswa ini, baru hapus nilainya
$krs_mhs = mysqli_query($koneksi, "SELECT id_krs FROM krs WHERE nim='$nim'");
while($kRow = mysqli_fetch_assoc($krs_mhs)){
    $id_krs_nya = $kRow['id_krs'];
    mysqli_query($koneksi, "DELETE FROM nilai WHERE id_krs='$id_krs_nya'");
}

// D. Hapus KRS
mysqli_query($koneksi, "DELETE FROM krs WHERE nim='$nim'");

// --- AKHIR PEMBERSIHAN ---


// 2. BARU HAPUS DATA MAHASISWA (Induk)
$hapus_mhs = mysqli_query($koneksi, "DELETE FROM mahasiswa WHERE nim='$nim'");

// 3. TERAKHIR HAPUS AKUN LOGIN (User)
if($hapus_mhs) {
    mysqli_query($koneksi, "DELETE FROM user WHERE id_user='$id_user'");
}

// Balik ke index
header("Location: index.php");
?>