<?php
session_start();
require '../config/config.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Cari user berdasarkan email
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        
        // CEK APAKAH AKUN SUDAH TERVERIFIKASI
        if ($row['is_verified'] == 0) {
            $_SESSION['temp_email'] = $email;
            header("Location: newOtp.php"); // Lempar ke halaman OTP
            exit();
        }

        // Verifikasi kecocokan hash password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login System</title>
</head>
<body>
    <h2>Halaman Login</h2>
    
    <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>

    <form action="" method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
    
    <p>Belum punya akun? <a href="registrasi.php">Daftar di sini</a></p>
</body>
</html>