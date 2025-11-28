<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }

include '../config/koneksi.php';
$nidn = $_GET['nidn'];

// Cari ID User dulu
$cek = mysqli_query($koneksi, "SELECT id_user FROM dosen WHERE nidn='$nidn'");
$data = mysqli_fetch_assoc($cek);
$id_user = $data['id_user'];

// Hapus User (Otomatis Dosen terhapus)
$hapus = mysqli_query($koneksi, "DELETE FROM user WHERE id_user='$id_user'");

if ($hapus) {
    echo "<script>alert('Dosen dihapus!'); window.location='index.php';</script>";
}
?>