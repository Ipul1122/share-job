<?php
require '../config/config.php';

$msg = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: login.php");
    exit();
}

// Cek validitas token
$token = mysqli_real_escape_string($conn, $token);
$query = mysqli_query($conn, "SELECT * FROM users WHERE reset_token = '$token' AND reset_expiry > NOW()");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    die("Token tidak valid atau sudah kedaluwarsa. Silakan minta link baru.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
        $hashed_password = mysqli_real_escape_string($conn, $hashed_password);
        
        // Update password dan hapus token agar tidak bisa dipakai lagi
        mysqli_query($conn, "UPDATE users SET password = '$hashed_password', reset_token = NULL, reset_expiry = NULL WHERE id = " . $user['id']);
        
        echo "<script>alert('Password berhasil diubah! Silakan login.'); window.location.href='login.php';</script>";
        exit();
    } else {
        $msg = "Konfirmasi password tidak cocok!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Reset Password Baru</title></head>
<body>
    <h2>Buat Password Baru</h2>
    <?php if($msg) echo "<p style='color:red;'>$msg</p>"; ?>
    <form action="" method="POST">
        <label>Password Baru:</label><br>
        <input type="password" name="password" required><br><br>
        <label>Konfirmasi Password Baru:</label><br>
        <input type="password" name="confirm_password" required><br><br>
        <button type="submit">Simpan Password Baru</button>
    </form>
</body>
</html>