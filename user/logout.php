<?php
session_start();
session_unset(); 
session_destroy(); 

// Arahkan kembali ke login
header("Location: login.php");
exit();
?>