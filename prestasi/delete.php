<?php
include '../config/koneksi.php';

$id = $_GET['id'];

// Hapus data berdasarkan ID
mysqli_query($koneksi, "DELETE FROM prestasi WHERE id_prestasi='$id'");

// Balik ke halaman index
header("Location: index.php");
?>