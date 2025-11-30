<?php
session_start();
include '../config/koneksi.php';

$id_krs = $_GET['id'];

// Hapus data KRS
$hapus = mysqli_query($koneksi, "DELETE FROM krs WHERE id_krs='$id_krs'");

if ($hapus) {
    header("Location: index.php");
} else {
    echo "Gagal: " . mysqli_error($koneksi);
}
?>