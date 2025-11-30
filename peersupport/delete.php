<?php
include '../config/koneksi.php';
$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM peer_support WHERE id_match='$id'");
header("Location: index.php");
?>