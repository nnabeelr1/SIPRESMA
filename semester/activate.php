<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }
include '../config/koneksi.php';

$id = $_GET['id'];

// 1. Matikan semua semester dulu
mysqli_query($koneksi, "UPDATE semester SET status='tidak'");

// 2. Aktifkan semester yang dipilih
mysqli_query($koneksi, "UPDATE semester SET status='aktif' WHERE id_semester='$id'");

header("Location: index.php");
?>