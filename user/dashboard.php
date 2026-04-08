<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Menentukan salam sapaan dinamis berdasarkan jam server
date_default_timezone_set('Asia/Jakarta');
$jam = date('H');
if ($jam >= 5 && $jam < 11) {
    $salam = "Selamat Pagi";
    $icon = "🌅";
} elseif ($jam >= 11 && $jam < 15) {
    $salam = "Selamat Siang";
    $icon = "☀️";
} elseif ($jam >= 15 && $jam < 18) {
    $salam = "Selamat Sore";
    $icon = "🌇";
} else {
    $salam = "Selamat Malam";
    $icon = "🌙";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - ShareDoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Efek pattern background halus */
        .bg-pattern {
            background-color: #f3f4f6;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%231e40af' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800 bg-pattern">
    
    <div class="flex flex-col md:flex-row min-h-screen">
        
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 w-full p-4 sm:p-8 lg:p-12 relative overflow-hidden">
            
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-blue-500 opacity-10 blur-3xl pointer-events-none"></div>

            <div class="max-w-6xl mx-auto space-y-8 relative z-0">
                
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 transform transition duration-500 hover:shadow-2xl">
                    <div class="bg-gradient-to-r from-blue-800 to-blue-600 p-8 sm:p-10 text-white flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div>
                            <div class="inline-block px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-sm text-sm font-semibold mb-4 border border-white/30">
                                Area Pengguna
                            </div>
                            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">
                                <?= $salam; ?> <?= $icon; ?>
                            </h2>
                            <p class="text-blue-100 text-lg">
                                Selamat datang kembali! Anda login sebagai <strong class="text-white border-b border-dashed border-white pb-1"><?php echo htmlspecialchars($_SESSION['email']); ?></strong>
                            </p>
                        </div>
                        <div class="hidden sm:block">
                            <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center border-4 border-white/20 shadow-inner">
                                <svg class="w-12 h-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Aksi Cepat
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <a href="shareDoc.php" class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2 flex flex-col h-full relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-150 duration-500 ease-out z-0"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2">Kirim Dokumen</h4>
                            <p class="text-gray-500 text-sm leading-relaxed mb-6">Unggah file tanpa batas ukuran dan kirim ke banyak pengguna sekaligus.</p>
                            <span class="mt-auto text-blue-600 font-bold text-sm flex items-center gap-1 group-hover:text-blue-800">
                                Mulai Berbagi <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </span>
                        </div>
                    </a>

                    <a href="receiveDoc.php" class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2 flex flex-col h-full relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-150 duration-500 ease-out z-0"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2">Inbox Diterima</h4>
                            <p class="text-gray-500 text-sm leading-relaxed mb-6">Cek dan unduh semua dokumen yang telah dikirimkan oleh pengguna lain kepada Anda.</p>
                            <span class="mt-auto text-indigo-600 font-bold text-sm flex items-center gap-1 group-hover:text-indigo-800">
                                Cek Inbox <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </span>
                        </div>
                    </a>

                    <a href="trackShare.php" class="group bg-white rounded-2xl p-6 shadow-md hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2 flex flex-col h-full relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-cyan-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-150 duration-500 ease-out z-0"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 bg-cyan-600 text-white rounded-xl flex items-center justify-center shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2">Pantau Riwayat</h4>
                            <p class="text-gray-500 text-sm leading-relaxed mb-6">Lacak status file Anda, kirim ulang, atau hapus dokumen yang pernah Anda bagikan.</p>
                            <span class="mt-auto text-cyan-600 font-bold text-sm flex items-center gap-1 group-hover:text-cyan-800">
                                Lihat Riwayat <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </span>
                        </div>
                    </a>

                </div>
            </div>

        </main>
    </div>

</body>
</html>