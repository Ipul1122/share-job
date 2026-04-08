<?php
session_start();
require '../config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Pastikan user berasal dari halaman registrasi
if (!isset($_SESSION['temp_email'])) {
    header("Location: registrasi.php");
    exit();
}

$email = $_SESSION['temp_email'];
$msg = '';
$msg_type = '';

// Logika Validasi OTP
if (isset($_POST['verify_otp'])) {
    $otp_input = mysqli_real_escape_string($conn, $_POST['otp']);
    
    $query = mysqli_query($conn, "SELECT otp, otp_expiry FROM users WHERE email = '$email'");
    $row = mysqli_fetch_assoc($query);

    if ($row['otp'] == $otp_input) {
        $current_time = date("Y-m-d H:i:s");
        if ($current_time <= $row['otp_expiry']) {
            // OTP Valid
            mysqli_query($conn, "UPDATE users SET is_verified = 1, otp = NULL, otp_expiry = NULL WHERE email = '$email'");
            unset($_SESSION['temp_email']); // Bersihkan session sementara
            
            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='login.php';</script>";
            exit();
        } else {
            $msg = "Kode OTP sudah kedaluwarsa. Silakan minta kode baru.";
            $msg_type = "red";
        }
    } else {
        $msg = "Kode OTP salah!";
        $msg_type = "red";
    }
}

// Logika Resend OTP
if (isset($_POST['resend_otp'])) {
    $new_otp = rand(100000, 999999);
    $new_expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));
    
    mysqli_query($conn, "UPDATE users SET otp='$new_otp', otp_expiry='$new_expiry' WHERE email='$email'");

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
        $mail->Subject = 'Kirim Ulang: Kode OTP Registrasi - share doc';
        $mail->Body    = "Ini adalah kode OTP baru kamu: <b>$new_otp</b>.<br>Berlaku selama 15 menit.";

        $mail->send();
        $msg = "OTP baru telah dikirim ke email kamu.";
        $msg_type = "green";
    } catch (Exception $e) {
        $msg = "Gagal mengirim ulang OTP.";
        $msg_type = "red";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Verifikasi OTP</title></head>
<body>
    <h2>Verifikasi Email</h2>
    <p>Kode OTP telah dikirim ke: <strong><?php echo htmlspecialchars($email); ?></strong></p>
    
    <?php if($msg) echo "<p style='color:$msg_type;'>$msg</p>"; ?>

    <form action="" method="POST">
        <label>Masukkan 6 Digit OTP:</label><br>
        <input type="text" name="otp" maxlength="6" required><br><br>
        <button type="submit" name="verify_otp">Verifikasi</button>
    </form>
    
    <br><hr><br>

    <p>Tidak menerima email?</p>
    <form action="" method="POST">
        <button type="submit" name="resend_otp">Kirim Ulang OTP</button>
    </form>
</body>
</html>