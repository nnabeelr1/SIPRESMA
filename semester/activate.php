<?php
include '../config/koneksi.php';
$id = $_GET['id'];

// 1. Matikan SEMUA semester dulu (Reset)
mysqli_query($koneksi, "UPDATE semester SET status='tidak'");

// 2. Aktifkan HANYA semester yang dipilih
mysqli_query($koneksi, "UPDATE semester SET status='aktif' WHERE id_semester='$id'");

header("Location: index.php");
?>