<?php
session_start();
session_destroy();
header("Location: index.php"); // Kembali ke halaman login
?>