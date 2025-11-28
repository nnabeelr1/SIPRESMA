<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }

include '../config/koneksi.php';
$id = $_GET['id'];

$hapus = mysqli_query($koneksi, "DELETE FROM prodi WHERE id_prodi='$id'");

if ($hapus) {
    header("Location: index.php");
} else {
    echo "Gagal menghapus (Mungkin data sedang dipakai oleh Mahasiswa/Dosen): " . mysqli_error($koneksi);
}
?>