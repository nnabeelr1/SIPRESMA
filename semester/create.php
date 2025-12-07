<?php
session_start();
// Security Check
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') { 
    header("Location: ../index.php"); 
    exit(); 
}

include '../config/koneksi.php';

if(isset($_POST['simpan'])){
    $nama = $_POST['nama'];
    
    // Default status 'tidak' saat dibuat
    $insert = mysqli_query($koneksi, "INSERT INTO semester (nama_semester, status) VALUES ('$nama', 'tidak')");
    
    if($insert) {
        header("Location: index.php?msg=success");
    } else {
        echo "<script>alert('Gagal menambah semester!'); window.location='index.php';</script>";
    }
}
?>