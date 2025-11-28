<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }
include '../config/koneksi.php';

$id = $_GET['id'];
$hapus = mysqli_query($koneksi, "DELETE FROM user WHERE id_user='$id'");

if ($hapus) {
    header("Location: index.php");
} else {
    echo "Gagal: " . mysqli_error($koneksi);
}
?>