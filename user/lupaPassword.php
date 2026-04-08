<?php
session_start();
require '../config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

$msg = '';
$msg_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $query = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND is_verified = 1");
    
    if (mysqli_num_rows($query) === 1) {
        $token = bin2hex(random_bytes(32)); // Generate token unik
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Berlaku 1 jam
        
        mysqli_query($conn, "UPDATE users SET reset_token = '$token', reset_expiry = '$expiry' WHERE email = '$email'");

        // Link Reset Password (Sesuaikan domain jika sudah online)
        $reset_link = "http://localhost/share-doc/user/resetPassword.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'msyaifulloh2024@gmail.com'; // GANTI dengan App Password
            $mail->Password   = 'uivv mxdz tuyt esgt'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('msyaifulloh2024@gmail.com', 'share doc');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password - Ipul TopUp';
            $mail->Body    = "Klik link berikut untuk mereset password kamu: <br><a href='$reset_link'>$reset_link</a><br>Link ini berlaku selama 1 jam.";

            $mail->send();
            $msg = "Link reset password telah dikirim ke email kamu.";
            $msg_type = "green";
        } catch (Exception $e) {
            $msg = "Gagal mengirim email.";
            $msg_type = "red";
        }
    } else {
        $msg = "Email tidak terdaftar atau belum verifikasi.";
        $msg_type = "red";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Lupa Password</title></head>
<body>
    <h2>Lupa Password</h2>
    <?php if($msg) echo "<p style='color:$msg_type;'>$msg</p>"; ?>
    <form action="" method="POST">
        <label>Masukkan Email Akun:</label><br>
        <input type="email" name="email" required><br><br>
        <button type="submit">Kirim Link Reset</button>
    </form>
    <p><a href="login.php">Kembali ke Login</a></p>
</body>
</html>