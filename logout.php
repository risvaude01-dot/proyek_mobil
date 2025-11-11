<?php
session_start();

// Hancurkan semua session
session_destroy();

// Redirect ke halaman login dengan pesan logout
header("location:login.php?pesan=logout");
exit();
?>