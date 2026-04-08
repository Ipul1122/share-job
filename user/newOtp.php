<?php
session_start();
require '../config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

if (!isset($_SESSION['temp_email'])) {
    header("Location: registrasi.php");
    exit();
}

$email = $_SESSION['temp_email'];
$msg = '';
$msg_type = '';
$success = false;

// Logika Validasi OTP
if (isset($_POST['verify_otp'])) {
    $otp_input = mysqli_real_escape_string($conn, $_POST['otp']);
    
    $query = mysqli_query($conn, "SELECT otp, otp_expiry FROM users WHERE email = '$email'");
    $row = mysqli_fetch_assoc($query);

    if ($row['otp'] == $otp_input) {
        $current_time = date("Y-m-d H:i:s");
        if ($current_time <= $row['otp_expiry']) {
            mysqli_query($conn, "UPDATE users SET is_verified = 1, otp = NULL, otp_expiry = NULL WHERE email = '$email'");
            unset($_SESSION['temp_email']);
            $success = true;
        } else {
            $msg = "Kode OTP sudah kedaluwarsa. Silakan minta kode baru.";
            $msg_type = "error";
        }
    } else {
        $msg = "Kode OTP salah!";
        $msg_type = "error";
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

        $mail->setFrom('msyaifulloh2024@gmail.com', 'ShareDoc Security');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = '[ShareDoc] Permintaan Kirim Ulang Kode OTP';
        
        // Body Email HTML Formal untuk Resend OTP
        $mail->Body = "
        <div style='font-family: Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f3f4f6; border-radius: 10px;'>
            <div style='text-align: center; margin-bottom: 25px; margin-top: 10px;'>
                <h2 style='color: #1e40af; margin: 0; font-size: 28px; font-weight: 800; letter-spacing: 1px;'>ShareDoc</h2>
            </div>
            <div style='background-color: #ffffff; padding: 40px 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 5px solid #1e40af;'>
                <h3 style='color: #1f2937; font-size: 20px; margin-top: 0; margin-bottom: 20px; text-align: center;'>Kode OTP Baru Anda</h3>
                <p style='color: #4b5563; font-size: 15px; line-height: 1.6; margin-bottom: 25px;'>
                    Yth. Pengguna,<br><br>
                    Sesuai dengan permintaan Anda, kami telah membuatkan Kode OTP (<i>One-Time Password</i>) yang baru. Silakan masukkan kode di bawah ini pada halaman verifikasi:
                </p>
                <div style='text-align: center; margin: 35px 0;'>
                    <span style='display: inline-block; padding: 15px 40px; font-size: 32px; font-weight: bold; color: #1e40af; background-color: #eff6ff; border: 2px dashed #93c5fd; border-radius: 8px; letter-spacing: 8px;'>
                        $new_otp
                    </span>
                </div>
                <p style='color: #dc2626; font-size: 13px; text-align: center; margin-bottom: 30px;'>
                    <em>Penting: Kode OTP ini bersifat rahasia dan hanya berlaku selama <strong>15 menit</strong> sejak email ini dikirim.</em>
                </p>
                <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                <p style='color: #6b7280; font-size: 12px; line-height: 1.5; text-align: center;'>
                    Jika Anda tidak merasa meminta pengiriman ulang kode ini, mohon abaikan email ini atau pastikan akun Anda aman.<br>
                    <strong>Jangan pernah membagikan kode ini kepada siapapun</strong>.
                </p>
            </div>
            <div style='text-align: center; margin-top: 20px; color: #9ca3af; font-size: 12px;'>
                &copy; " . date('Y') . " ShareDoc. All rights reserved.
            </div>
        </div>
        ";

        $mail->send();
        $msg = "OTP baru telah dikirim ke email kamu.";
        $msg_type = "success";
    } catch (Exception $e) {
        $msg = "Gagal mengirim ulang OTP.";
        $msg_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - ShareDoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen relative font-sans antialiased p-4">

    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-800 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>

    <?php if($success): ?>
        <script>
            Swal.fire({
                icon: 'success', title: 'Registrasi Berhasil!', text: 'Akun Anda telah terverifikasi. Silakan login.',
                confirmButtonColor: '#1e40af'
            }).then(() => { window.location.href = 'login.php'; });
        </script>
    <?php elseif($msg): ?>
        <script>
            Swal.fire({
                icon: '<?= $msg_type ?>', title: '<?= $msg_type == "success" ? "Berhasil" : "Oops!" ?>', text: '<?= $msg ?>',
                confirmButtonColor: '#1e40af'
            });
        </script>
    <?php endif; ?>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 relative z-10 border border-gray-100 text-center">
        <div class="mx-auto bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-6 text-blue-600">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </div>
        
        <h2 class="text-2xl font-extrabold text-gray-800 mb-2">Verifikasi Email</h2>
        <p class="text-gray-500 text-sm mb-6">
            Kami telah mengirimkan 6-digit kode OTP ke:<br>
            <strong class="text-blue-800"><?php echo htmlspecialchars($email); ?></strong>
        </p>

        <form action="" method="POST" class="space-y-6">
            <div>
                <input type="text" name="otp" maxlength="6" required placeholder="••••••" autocomplete="off"
                       class="w-full text-center text-3xl font-bold tracking-[1em] py-4 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-600 outline-none transition text-gray-800">
            </div>
            
            <button type="submit" name="verify_otp" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-4 px-4 rounded-xl shadow-lg transform active:scale-95 transition duration-200">
                Verifikasi OTP
            </button>
        </form>
        
        <div class="mt-8 border-t border-gray-200 pt-6">
            <p class="text-sm text-gray-500 mb-2">Tidak menerima email?</p>
            <form action="" method="POST">
                <button type="submit" name="resend_otp" class="text-blue-600 font-semibold hover:text-blue-800 hover:underline transition">
                    Kirim Ulang Kode OTP
                </button>
            </form>
        </div>
    </div>

</body>
</html>