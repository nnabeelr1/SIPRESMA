<?php
session_start();
include '../config/koneksi.php';

// Pastikan tombol 'ambil' ditekan
if (isset($_POST['ambil'])) {
    $nim      = $_POST['nim'];
    $kelas    = $_POST['id_kelas'];
    $semester = $_POST['id_semester'];
    $tgl      = date('Y-m-d');

    // Cek: Apakah matkul ini sudah diambil sebelumnya? (Biar gak dobel)
    $cek = mysqli_query($koneksi, "SELECT * FROM krs WHERE nim='$nim' AND id_kelas='$kelas'");
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('Matkul ini sudah kamu ambil!'); window.location='index.php';</script>";
        exit();
    }

    // Insert ke database
    $query = "INSERT INTO krs (nim, id_kelas, id_semester, tanggal_krs) 
              VALUES ('$nim', '$kelas', '$semester', '$tgl')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php");
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>