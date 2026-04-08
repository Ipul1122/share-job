<?php
session_start();
require '../config/config.php'; 

// Proteksi Halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];
$msg = '';
$msg_type = '';

// --- LOGIKA UNTUK AKSI (RESEND, DELETE, BULK DELETE) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // Fungsi bantu untuk menghapus data & file secara aman
    function safeDelete($conn, $id, $sender_id) {
        $id = mysqli_real_escape_string($conn, $id);
        
        // Cari path file dari ID yang mau dihapus
        $q = mysqli_query($conn, "SELECT file_path FROM shared_files WHERE id = '$id' AND sender_id = '$sender_id'");
        if ($row = mysqli_fetch_assoc($q)) {
            $fp = $row['file_path'];
            
            // Cek apakah file ini masih dipakai oleh riwayat share yang lain
            $check = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM shared_files WHERE file_path = '" . mysqli_real_escape_string($conn, $fp) . "'");
            $cnt = mysqli_fetch_assoc($check)['cnt'];
            
            // Hapus record dari database
            mysqli_query($conn, "DELETE FROM shared_files WHERE id = '$id'");
            
            // Jika ini adalah satu-satunya record yang memakai file tersebut, hapus file fisiknya dari server
            if ($cnt <= 1 && file_exists($fp)) {
                unlink($fp);
            }
            return true;
        }
        return false;
    }

    // Eksekusi berdasarkan tipe aksi
    if ($action === 'delete' && isset($_POST['id'])) {
        if (safeDelete($conn, $_POST['id'], $sender_id)) {
            $msg = "1 riwayat berhasil dihapus.";
            $msg_type = "green";
        }
    } elseif ($action === 'bulk_delete' && !empty($_POST['ids'])) {
        $deleted = 0;
        foreach ($_POST['ids'] as $id) {
            if (safeDelete($conn, $id, $sender_id)) {
                $deleted++;
            }
        }
        $msg = "$deleted riwayat berhasil dihapus.";
        $msg_type = "green";
    } elseif ($action === 'resend' && isset($_POST['id'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        
        // Copy data lama dan insert sebagai data baru
        $q = mysqli_query($conn, "SELECT receiver_id, file_name, file_path FROM shared_files WHERE id = '$id' AND sender_id = '$sender_id'");
        if ($row = mysqli_fetch_assoc($q)) {
            $rid = $row['receiver_id'];
            $fn = mysqli_real_escape_string($conn, $row['file_name']);
            $fp = mysqli_real_escape_string($conn, $row['file_path']);
            
            mysqli_query($conn, "INSERT INTO shared_files (sender_id, receiver_id, file_name, file_path) VALUES ('$sender_id', '$rid', '$fn', '$fp')");
            $msg = "File berhasil dikirim ulang.";
            $msg_type = "green";
        }
    }
}

// Ambil riwayat pengiriman
$query = "SELECT sf.id, sf.file_name, sf.file_path, sf.shared_at, u.email AS receiver_email 
          FROM shared_files sf 
          JOIN users u ON sf.receiver_id = u.id 
          WHERE sf.sender_id = '$sender_id' 
          ORDER BY sf.shared_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Kiriman - ShareDoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">
    
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 p-4 lg:p-10">
            <div class="max-w-6xl mx-auto">
                
                <div class="bg-white rounded-t-xl shadow-md p-6 border-t-4 border-blue-600 flex justify-between items-center mb-1">
                    <div>
                        <h2 class="text-2xl font-bold text-blue-800">📤 Riwayat Kiriman</h2>
                        <p class="text-blue-600 text-sm mt-1">Pantau, kirim ulang, atau hapus dokumen yang telah dibagikan.</p>
                    </div>
                    <div class="flex gap-3 items-center">
                        <button type="button" onclick="confirmBulkDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow transition duration-200 text-sm flex items-center gap-2 hidden" id="btnBulkDelete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Terpilih
                        </button>
                        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold shadow-sm text-sm">
                            Total: <?= mysqli_num_rows($result) ?> File
                        </div>
                    </div>
                </div>

                <?php if($msg): ?>
                    <script>
                        Swal.fire({
                            icon: '<?= $msg_type == "green" ? "success" : "error" ?>',
                            title: 'Berhasil',
                            text: '<?= $msg ?>',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    </script>
                <?php endif; ?>

                <form id="bulkForm" method="POST">
                    <input type="hidden" name="action" value="bulk_delete">
                    
                    <div class="bg-white shadow-md rounded-b-xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-blue-800 text-white text-sm uppercase tracking-wider">
                                        <th class="py-4 px-6 font-semibold w-12 text-center">
                                            <input type="checkbox" id="checkAll" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 cursor-pointer">
                                        </th>
                                        <th class="py-4 px-6 font-semibold">Nama File</th>
                                        <th class="py-4 px-6 font-semibold">Dikirim Kepada</th>
                                        <th class="py-4 px-6 font-semibold">Waktu Pengiriman</th>
                                        <th class="py-4 px-6 font-semibold text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php if(mysqli_num_rows($result) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="hover:bg-blue-50 transition duration-200">
                                            <td class="py-4 px-6 text-center">
                                                <input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="checkItem w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer" onchange="toggleBulkButton()">
                                            </td>
                                            <td class="py-4 px-6 flex items-center gap-3">
                                                <div class="bg-green-100 text-green-600 p-2 rounded">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                                </div>
                                                <span class="font-medium text-gray-800 truncate w-32 md:w-48 lg:w-64" title="<?= htmlspecialchars($row['file_name']) ?>">
                                                    <?= htmlspecialchars($row['file_name']) ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-600 font-semibold">
                                                <?= htmlspecialchars($row['receiver_email']) ?>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-500">
                                                <?= date('d M Y, H:i', strtotime($row['shared_at'])) ?> WIB
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button type="button" onclick="actionResend(<?= $row['id'] ?>)" class="bg-blue-100 hover:bg-blue-200 text-blue-700 p-2 rounded transition" title="Kirim Ulang">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    </button>
                                                    <button type="button" onclick="actionDelete(<?= $row['id'] ?>)" class="bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded transition" title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="py-10 text-center text-gray-500">
                                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                <p>Anda belum pernah mengirim dokumen apa pun.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>

            </div>
        </main>
    </div>

    <form id="actionForm" method="POST" class="hidden">
        <input type="hidden" name="action" id="actionType">
        <input type="hidden" name="id" id="actionId">
    </form>

    <script>
        // Logika Pilih Semua Checkbox
        const checkAll = document.getElementById('checkAll');
        const checkItems = document.querySelectorAll('.checkItem');
        const btnBulkDelete = document.getElementById('btnBulkDelete');

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkItems.forEach(item => item.checked = this.checked);
                toggleBulkButton();
            });
        }

        // Tampilkan/Sembunyikan tombol Bulk Delete jika ada yang dicentang
        function toggleBulkButton() {
            const isChecked = document.querySelectorAll('.checkItem:checked').length > 0;
            if(isChecked) {
                btnBulkDelete.classList.remove('hidden');
            } else {
                btnBulkDelete.classList.add('hidden');
                checkAll.checked = false;
            }
        }

        // Fungsi Bulk Delete
        function confirmBulkDelete() {
            let count = document.querySelectorAll('.checkItem:checked').length;
            Swal.fire({
                title: 'Hapus ' + count + ' data?',
                text: "Data akan dihapus secara permanen dari riwayat Anda!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulkForm').submit();
                }
            });
        }

        // Fungsi Single Delete
        function actionDelete(id) {
            Swal.fire({
                title: 'Hapus Riwayat Ini?',
                text: "Data tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('actionType').value = 'delete';
                    document.getElementById('actionId').value = id;
                    document.getElementById('actionForm').submit();
                }
            });
        }

        // Fungsi Resend
        function actionResend(id) {
            Swal.fire({
                title: 'Kirim Ulang Dokumen?',
                text: "Dokumen ini akan dikirim kembali ke penerima dengan waktu saat ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1e40af', // blue-800
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Kirim Ulang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('actionType').value = 'resend';
                    document.getElementById('actionId').value = id;
                    document.getElementById('actionForm').submit();
                }
            });
        }
    </script>
</body>
</html>