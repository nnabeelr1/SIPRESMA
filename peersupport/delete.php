<?php
// 1. Panggil Koneksi
include '../config/koneksi.php';

// 2. Ambil ID yang mau dihapus
$id = $_GET['id'];

// 3. Proses Hapus Data
$hapus = mysqli_query($koneksi, "DELETE FROM peer_support WHERE id_match='$id'");

// 4. PENTING: Redirect (Tendang balik ke halaman index)
// Kalau baris ini hilang, layarnya bakal putih doang
header("Location: index.php");
exit();
?>