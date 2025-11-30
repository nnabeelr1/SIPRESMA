<?php
include '../config/koneksi.php';
mysqli_query($koneksi, "TRUNCATE TABLE peer_support");
header("Location: index.php");
?>