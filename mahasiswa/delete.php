<?php
include '../config/koneksi.php';

// 1. Ambil NIM dari URL
$nim = $_GET['nim'];

// 2. Cari ID User milik mahasiswa ini
// Kita butuh ID User karena kalau User dihapus, Mahasiswa otomatis terhapus (Cascade)
$query_cari = mysqli_query($koneksi, "SELECT id_user FROM mahasiswa WHERE nim = '$nim'");
$data = mysqli_fetch_assoc($query_cari);
$id_user = $data['id_user'];

// 3. Hapus data di tabel USER
$hapus = mysqli_query($koneksi, "DELETE FROM user WHERE id_user = '$id_user'");

if ($hapus) {
    echo "<script>alert('Data berhasil dihapus!'); window.location='index.php';</script>";
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>