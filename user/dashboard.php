<?php
session_start();

// Cek apakah user sudah login, jika belum lempar kembali ke login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
</head>
<body>
    <h2>Selamat Datang di Dashboard!</h2>
    <p>Anda berhasil login menggunakan email: <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong></p>
    
    <br>
    <a href="logout.php"><button>Logout</button></a>
</body>
</html>