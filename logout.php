<?php
session_start();
session_destroy();
// GANTI INI: Jangan ke login.php, tapi ke index.php
header("Location: index.php"); 
?>