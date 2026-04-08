<?php
session_start();
require '../config/config.php'; //
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

$msg = '';
$msg_type = ''; // Akan diisi 'success' atau 'error' untuk SweetAlert

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    //
    $query = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND is_verified = 1");
    
    if (mysqli_num_rows($query) === 1) {
        $token = bin2hex(random_bytes(32)); 
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); 
        
        mysqli_query($conn, "UPDATE users SET reset_token = '$token', reset_expiry = '$expiry' WHERE email = '$email'");

        $reset_link = "http://localhost/share-doc/user/resetPassword.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'msyaifulloh2024@gmail.com'; 
            $mail->Password   = 'uivv mxdz tuyt esgt'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('msyaifulloh2024@gmail.com', 'ShareDoc');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password - ShareDoc';
            $mail->Body    = "Halo, <br><br>Kami menerima permintaan untuk mereset password akun ShareDoc Anda. Klik tombol di bawah ini untuk membuat password baru:<br><br>
                              <a href='$reset_link' style='background-color:#1e40af; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>Reset Password</a><br><br>
                              Atau salin tautan berikut: <br><a href='$reset_link'>$reset_link</a><br><br>
                              <i>Link ini berlaku selama 1 jam.</i>";

            $mail->send();
            $msg = "Link untuk mereset password telah dikirim ke email Anda. Silakan cek Inbox atau folder Spam.";
            $msg_type = "success";
        } catch (Exception $e) {
            $msg = "Gagal mengirim email. Sistem sedang mengalami gangguan.";
            $msg_type = "error";
        }
    } else {
        $msg = "Email tidak terdaftar atau akun Anda belum diverifikasi.";
        $msg_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - ShareDoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen relative font-sans antialiased p-4">

    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-800 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>

    <?php if($msg): ?>
        <script>
            Swal.fire({
                icon: '<?= $msg_type ?>',
                title: '<?= $msg_type == "success" ? "Berhasil Terkirim!" : "Gagal!" ?>',
                text: '<?= $msg ?>',
                confirmButtonColor: '#1e40af' // blue-800
            });
        </script>
    <?php endif; ?>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 sm:p-10 relative z-10 border border-gray-100 text-center">
        
        <div class="mx-auto bg-blue-50 w-20 h-20 rounded-full flex items-center justify-center mb-6 shadow-sm border border-blue-100 text-blue-600">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
        
        <h2 class="text-2xl font-extrabold text-gray-800 mb-2">Lupa Password?</h2>
        <p class="text-gray-500 text-sm mb-8 px-2">
            Jangan khawatir! Masukkan email yang terdaftar, dan kami akan mengirimkan instruksi untuk mereset password Anda.
        </p>

        <form action="" method="POST" class="space-y-6 text-left">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2" for="email">Alamat Email Terdaftar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input type="email" name="email" id="email" required placeholder="nama@email.com" 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition shadow-sm text-sm">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-3 px-4 rounded-xl shadow-lg transform active:scale-95 transition duration-200">
                Kirim Link Reset
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-center gap-2 text-sm text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <a href="login.php" class="font-bold text-blue-600 hover:text-blue-800 hover:underline transition">Kembali ke Halaman Login</a>
        </div>
    </div>

</body>
</html>