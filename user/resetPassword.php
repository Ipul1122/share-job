<?php
require '../config/config.php';

$msg = '';
$token_invalid = false;
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
    $token_invalid = true; // Flag untuk menampilkan alert lalu redirect
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$token_invalid) {
    $new_pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
        $hashed_password = mysqli_real_escape_string($conn, $hashed_password);
        
        mysqli_query($conn, "UPDATE users SET password = '$hashed_password', reset_token = NULL, reset_expiry = NULL WHERE id = " . $user['id']);
        
        $success = true;
    } else {
        $msg = "Konfirmasi password tidak cocok!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ShareDoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen relative font-sans antialiased p-4">

    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-800 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>

    <?php if($token_invalid): ?>
        <script>
            Swal.fire({
                icon: 'error', title: 'Akses Ditolak', text: 'Token tidak valid atau sudah kedaluwarsa. Silakan minta link baru.',
                confirmButtonColor: '#1e40af', confirmButtonText: 'Kembali ke Login'
            }).then(() => { window.location.href = 'login.php'; });
        </script>
    <?php elseif(isset($success)): ?>
        <script>
            Swal.fire({
                icon: 'success', title: 'Berhasil!', text: 'Password berhasil diubah! Silakan login dengan password baru Anda.',
                confirmButtonColor: '#1e40af', confirmButtonText: 'Ke Halaman Login'
            }).then(() => { window.location.href = 'login.php'; });
        </script>
    <?php endif; ?>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 relative z-10 border border-gray-100">
        <div class="text-center mb-8">
            <div class="mx-auto bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-4 text-blue-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-800">Buat Password Baru</h2>
            <p class="text-gray-500 text-sm mt-2">Pastikan password baru Anda kuat dan mudah diingat.</p>
        </div>

        <?php if($msg): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded-lg text-sm text-center font-medium mb-4"><?= $msg ?></div>
        <?php endif; ?>

        <?php if(!$token_invalid): ?>
        <form action="" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-600 outline-none transition text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" required placeholder="••••••••" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-600 outline-none transition text-sm">
            </div>
            <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-3 px-4 rounded-xl shadow-lg transform active:scale-95 transition duration-200 mt-2">
                Simpan Password Baru
            </button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>