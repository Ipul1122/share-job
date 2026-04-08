<?php
session_start();
require '../config/config.php';

// Proteksi Halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$receiver_id = $_SESSION['user_id'];

// Ambil data file yang diterima user ini, gabungkan dengan tabel users untuk mendapat email pengirim
$query = "SELECT sf.id, sf.file_name, sf.file_path, sf.shared_at, u.email AS sender_email 
          FROM shared_files sf 
          JOIN users u ON sf.sender_id = u.id 
          WHERE sf.receiver_id = '$receiver_id' 
          ORDER BY sf.shared_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Diterima - ShareDoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">
    
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 p-4 lg:p-10">
            <div class="max-w-6xl mx-auto">
                
                <div class="bg-white rounded-t-xl shadow-md p-6 border-t-4 border-blue-600 flex justify-between items-center mb-1">
                    <div>
                        <h2 class="text-2xl font-bold text-blue-800">📥 Dokumen Diterima</h2>
                        <p class="text-blue-600 text-sm mt-1">Daftar file yang dikirimkan oleh user lain kepada Anda.</p>
                    </div>
                    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold shadow-sm">
                        Total: <?= mysqli_num_rows($result) ?> File
                    </div>
                </div>

                <div class="bg-white shadow-md rounded-b-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-blue-800 text-white text-sm uppercase tracking-wider">
                                    <th class="py-4 px-6 font-semibold">Nama File</th>
                                    <th class="py-4 px-6 font-semibold">Pengirim</th>
                                    <th class="py-4 px-6 font-semibold">Waktu Diterima</th>
                                    <th class="py-4 px-6 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php if(mysqli_num_rows($result) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr class="hover:bg-blue-50 transition duration-200">
                                        <td class="py-4 px-6 flex items-center gap-3">
                                            <div class="bg-blue-100 text-blue-600 p-2 rounded">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <span class="font-medium text-gray-800 truncate max-w-xs" title="<?= htmlspecialchars($row['file_name']) ?>">
                                                <?= htmlspecialchars($row['file_name']) ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-600">
                                            <?= htmlspecialchars($row['sender_email']) ?>
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-500">
                                            <?= date('d M Y, H:i', strtotime($row['shared_at'])) ?> WIB
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <a href="<?= htmlspecialchars($row['file_path']) ?>" download="<?= htmlspecialchars($row['file_name']) ?>" 
                                               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg shadow transition duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Download
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-gray-500">
                                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            <p>Belum ada dokumen yang diterima.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>