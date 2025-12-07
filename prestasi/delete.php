<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['status'])) { header("Location: ../index.php"); exit(); }

$id = $_GET['id'];

// Ambil info file dulu untuk dihapus dari folder
$q = mysqli_query($koneksi, "SELECT file_bukti FROM prestasi WHERE id_prestasi='$id'");
$data = mysqli_fetch_assoc($q);
$file = $data['file_bukti'];

if ($file && file_exists("../uploads/$file")) {
    unlink("../uploads/$file"); // Hapus file fisik
}

// Hapus data dari DB
$delete = mysqli_query($koneksi, "DELETE FROM prestasi WHERE id_prestasi='$id'");

if ($delete) {
    echo "<script>alert('Data berhasil dihapus'); window.location='index.php';</script>";
} else {
    echo "Gagal menghapus data";
}
?>