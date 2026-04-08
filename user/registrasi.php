<?php
session_start();
require '../config/config.php'; //

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
        //
        $cek_email = mysqli_query($conn, "SELECT id, is_verified FROM users WHERE email = '$email'");
        
        if (mysqli_num_rows($cek_email) > 0) {
            $row = mysqli_fetch_assoc($cek_email);
            if ($row['is_verified'] == 1) {
                $error = "Email sudah terdaftar dan terverifikasi!";
            } else {
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
                    $mail->Subject = '[ShareDoc] Kode OTP Verifikasi Akun Anda';
                    
                    // Body Email HTML Formal untuk Registrasi
                    $mail->Body = "
                    <div style='font-family: Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f3f4f6; border-radius: 10px;'>
                        <div style='text-align: center; margin-bottom: 25px; margin-top: 10px;'>
                            <h2 style='color: #1e40af; margin: 0; font-size: 28px; font-weight: 800; letter-spacing: 1px;'>ShareDoc</h2>
                        </div>
                        <div style='background-color: #ffffff; padding: 40px 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 5px solid #1e40af;'>
                            <h3 style='color: #1f2937; font-size: 20px; margin-top: 0; margin-bottom: 20px; text-align: center;'>Verifikasi Pendaftaran Akun</h3>
                            <p style='color: #4b5563; font-size: 15px; line-height: 1.6; margin-bottom: 25px;'>
                                Yth. Pengguna,<br><br>
                                Terima kasih telah mendaftar di <strong>ShareDoc</strong>. Untuk menyelesaikan proses registrasi dan memverifikasi alamat email Anda, silakan gunakan Kode OTP (<i>One-Time Password</i>) berikut:
                            </p>
                            <div style='text-align: center; margin: 35px 0;'>
                                <span style='display: inline-block; padding: 15px 40px; font-size: 32px; font-weight: bold; color: #1e40af; background-color: #eff6ff; border: 2px dashed #93c5fd; border-radius: 8px; letter-spacing: 8px;'>
                                    $otp
                                </span>
                            </div>
                            <p style='color: #dc2626; font-size: 13px; text-align: center; margin-bottom: 30px;'>
                                <em>Penting: Kode OTP ini bersifat rahasia dan hanya berlaku selama <strong>15 menit</strong>.</em>
                            </p>
                            <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                            <p style='color: #6b7280; font-size: 12px; line-height: 1.5; text-align: center;'>
                                Jika Anda tidak merasa melakukan pendaftaran akun di ShareDoc, mohon abaikan email ini.<br>
                                <strong>Jangan pernah membagikan kode ini kepada siapapun</strong>, termasuk pihak admin kami.
                            </p>
                        </div>
                        <div style='text-align: center; margin-top: 20px; color: #9ca3af; font-size: 12px;'>
                            &copy; " . date('Y') . " ShareDoc. All rights reserved.
                        </div>
                    </div>
                    ";

                    $mail->send();
                    
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - ShareDoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen relative font-sans antialiased p-4">

    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-800 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>

    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row relative z-10 border border-gray-100">
        
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-800 to-blue-600 p-12 flex-col justify-center text-white relative overflow-hidden">
            <h1 class="text-4xl font-bold mb-4 z-10">Bergabunglah!</h1>
            <p class="text-blue-100 text-lg mb-8 leading-relaxed z-10">
                Buat akun baru untuk mulai membagikan dokumen dengan ukuran tak terbatas ke siapa saja, kapan saja.
            </p>
            <div class="z-10 bg-white/10 p-6 rounded-xl border border-white/20 backdrop-blur-sm">
                <p class="italic text-sm text-blue-50">"ShareDoc mempermudah pekerjaan tim kami dalam bertukar file raksasa tanpa ribet."</p>
            </div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 border-4 border-white/10 rounded-full"></div>
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        <div class="w-full md:w-1/2 p-8 sm:p-12">
            <div class="text-center md:text-left mb-8">
                <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Daftar Akun 🚀</h2>
                <p class="text-gray-500">Lengkapi data di bawah ini untuk mendaftar.</p>
            </div>

            <?php if(!empty($error)): ?>
                <script>
                    Swal.fire({ icon: 'error', title: 'Oops...', text: '<?= $error ?>', confirmButtonColor: '#1e40af' });
                </script>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="email" name="email" required placeholder="nama@email.com" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition shadow-sm text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" name="password" id="reg-password" required placeholder="••••••••" class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-600 outline-none transition text-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-blue-600" onclick="toggleVisibility('reg-password', 'eye-icon-1')">
                            <svg id="eye-icon-1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <input type="password" name="confirm_password" id="reg-confirm" required placeholder="••••••••" class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-600 outline-none transition text-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-blue-600" onclick="toggleVisibility('reg-confirm', 'eye-icon-2')">
                            <svg id="eye-icon-2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-3 px-4 rounded-xl shadow-lg transform active:scale-95 transition duration-200 mt-4">
                    Daftar & Kirim OTP
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-gray-600">
                Sudah punya akun? 
                <a href="login.php" class="font-bold text-blue-600 hover:text-blue-800 hover:underline transition">Masuk di sini</a>
            </div>
        </div>
    </div>

    <script>
        function toggleVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === "password") {
                input.type = "text";
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>`;
            } else {
                input.type = "password";
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`;
            }
        }
    </script>
</body>
</html>