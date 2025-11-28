<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }
include '../config/koneksi.php';

$kode = $_GET['kode'];
$hapus = mysqli_query($koneksi, "DELETE FROM matakuliah WHERE kode_mk='$kode'");

if ($hapus) {
    header("Location: index.php");
} else {
    echo "Gagal: " . mysqli_error($koneksi);
}
?>