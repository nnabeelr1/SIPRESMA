<?php
include '../config/koneksi.php';

if(isset($_POST['simpan'])){
    $nama = $_POST['nama'];
    // Default status 'tidak' saat dibuat
    mysqli_query($koneksi, "INSERT INTO semester (nama_semester, status) VALUES ('$nama', 'tidak')");
    header("Location: index.php");
}
?>