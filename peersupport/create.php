<?php
session_start();
include '../config/koneksi.php';

if (isset($_POST['match'])) {
    $mentee = $_POST['mentee_nim'];
    $mentor = $_POST['mentor_nim'];
    $tgl    = date('Y-m-d');

    // INSERT: Status 'menunggu_dosen', dua-duanya masih 0
    $query = "INSERT INTO peer_support (mentee_nim, mentor_nim, tanggal_mulai, status, acc_doswal_mentee, acc_doswal_mentor) 
              VALUES ('$mentee', '$mentor', '$tgl', 'menunggu_dosen', 0, 0)";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>