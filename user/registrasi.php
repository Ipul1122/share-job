<?php
session_start();
require '../config/config.php';

// Load PHPMailer (Sesuaikan path jika manual tanpa composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php'; 

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        $cek_email = mysqli_query($conn, "SELECT id, is_verified FROM users WHERE email = '$email'");
        
        if (mysqli_num_rows($cek_email) > 0) {
            $row = mysqli_fetch_assoc($cek_email);
            if ($row['is_verified'] == 1) {
                $error = "Email sudah terdaftar dan terverifikasi!";
            } else {
                // Email ada tapi belum verifikasi, kita update OTP-nya nanti
                $user_exists_unverified = true;
            }
        } else {
            $user_exists_unverified = false;
        }

        if (empty($error)) {
            $otp = rand(100000, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            if ($user_exists_unverified) {
                $query = "UPDATE users SET password='$hashed_password', otp='$otp', otp_expiry='$expiry' WHERE email='$email'";
            } else {
                $query = "INSERT INTO users (email, password, otp, otp_expiry) VALUES ('$email', '$hashed_password', '$otp', '$expiry')";
            }

            if (mysqli_query($conn, $query)) {
                // Konfigurasi Kirim Email dengan Gmail
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'msyaifulloh2024@gmail.com'; 
                    $mail->Password   = 'uivv mxdz tuyt esgt'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('msyaifulloh2024@gmail.com', 'share doc'); 
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Kode OTP Registrasi - ';
                    $mail->Body    = "Terima kasih telah mendaftar. Kode OTP kamu adalah: <b>$otp</b>.<br>Kode ini berlaku selama 15 menit.";

                    $mail->send();
                    
                    // Simpan email di session untuk halaman verifikasi
                    $_SESSION['temp_email'] = $email;
                    header("Location: newOtp.php");
                    exit();
                } catch (Exception $e) {
                    $error = "Pendaftaran berhasil, tetapi gagal mengirim email OTP: {$mail->ErrorInfo}";
                }
            } else {
                $error = "Terjadi kesalahan database.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Registrasi Akun</title></head>
<body>
    <h2>Halaman Registrasi</h2>
    <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
    <form action="" method="POST">
        <label>Email:</label><br><input type="email" name="email" required><br><br>
        <label>Password:</label><br><input type="password" name="password" required><br><br>
        <label>Konfirmasi Password:</label><br><input type="password" name="confirm_password" required><br><br>
        <button type="submit">Daftar & Kirim OTP</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
</body>
</html>