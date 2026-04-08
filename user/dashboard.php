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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - ShareDoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">
    
    <div class="flex flex-col md:flex-row min-h-screen">
        
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 w-full p-6 lg:p-10">
            
            <div class="bg-white rounded-xl shadow-md p-6 lg:p-8 border-t-4 border-blue-600">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-2">Selamat Datang di Dashboard!</h2>
                <p class="text-gray-600 text-lg mb-8">
                    Anda berhasil login menggunakan email: <strong class="text-blue-600"><?php echo htmlspecialchars($_SESSION['email']); ?></strong>
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <div class="bg-blue-50 hover:bg-blue-100 transition duration-300 border border-blue-200 rounded-lg p-5 flex items-center shadow-sm">
                        <div class="bg-blue-600 p-3 rounded-full text-white mr-4 shadow">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Kirim File</h3>
                            <a href="shareDoc.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Mulai berbagi dokumen &rarr;</a>
                        </div>
                    </div>

                    </div>
            </div>

        </main>
    </div>

</body>
</html>