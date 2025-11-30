<?php
include '../config/koneksi.php';
$id = $_GET['id'];

// Hapus data
mysqli_query($koneksi, "DELETE FROM semester WHERE id_semester='$id'");

// INI YANG TADI HILANG 👇 (Perintah balik ke index)
header("Location: index.php");
exit();
?>