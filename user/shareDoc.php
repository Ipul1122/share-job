<?php
session_start();
require '../config/config.php'; //

// Proteksi Halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];
$msg = '';
$msg_type = '';

// Ambil daftar user lain
$users_query = mysqli_query($conn, "SELECT id, email FROM users WHERE id != '$sender_id' AND is_verified = 1");

// Logika Upload & Share
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_users = $_POST['target_users'] ?? [];
    
    if (empty($target_users)) {
        $msg = "Pilih minimal satu user tujuan!";
        $msg_type = "red";
    } elseif (!isset($_FILES['dokumen']['name'][0]) || empty($_FILES['dokumen']['name'][0])) {
        $msg = "Pilih file yang ingin dibagikan!";
        $msg_type = "red";
    } else {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $success_count = 0;
        $file_count = count($_FILES['dokumen']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            $file_name = $_FILES['dokumen']['name'][$i];
            $tmp_name  = $_FILES['dokumen']['tmp_name'][$i];
            
            if ($_FILES['dokumen']['error'][$i] === 0) {
                $unique_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
                $destination = $upload_dir . $unique_name;

                if (move_uploaded_file($tmp_name, $destination)) {
                    foreach ($target_users as $receiver_id) {
                        $rid = mysqli_real_escape_string($conn, $receiver_id);
                        $fn = mysqli_real_escape_string($conn, $file_name);
                        $dp = mysqli_real_escape_string($conn, $destination);
                        mysqli_query($conn, "INSERT INTO shared_files (sender_id, receiver_id, file_name, file_path) VALUES ('$sender_id', '$rid', '$fn', '$dp')");
                    }
                    $success_count++;
                }
            }
        }
        $msg = $success_count > 0 ? "$success_count file berhasil dibagikan!" : "Gagal mengupload file.";
        $msg_type = $success_count > 0 ? "green" : "red";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Document - ShareDoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">
    
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 p-4 lg:p-10">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-blue-600">
                    <div class="p-6 bg-blue-50 border-b border-blue-100">
                        <h2 class="text-2xl font-bold text-blue-800">📤 Bagikan Dokumen</h2>
                        <p class="text-blue-600 text-sm">Kirim file ke banyak user sekaligus dengan mudah.</p>
                    </div>

                    <?php if($msg): ?>
                        <div class="m-6 p-4 rounded-lg text-center font-medium <?php echo $msg_type == 'green' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                            <?php echo $msg; ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
                        
                        <div>
                            <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">
                                <label class="block text-lg font-semibold text-gray-700">1. Pilih Penerima:</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="userSearch" placeholder="Cari email..." class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                    <button type="button" id="selectAll" class="text-sm font-medium text-blue-600 hover:text-blue-800 underline">Pilih Semua</button>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto p-2 border rounded-lg bg-gray-50" id="userList">
                                <?php while($user = mysqli_fetch_assoc($users_query)): ?>
                                <label class="user-card flex items-center p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition duration-200 shadow-sm">
                                    <input type="checkbox" name="target_users[]" value="<?= $user['id'] ?>" class="user-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700 truncate"><?= htmlspecialchars($user['email']) ?></span>
                                </label>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-4">2. Unggah File:</label>
                            <div id="dropZone" class="group border-2 border-dashed border-blue-400 rounded-xl p-10 text-center bg-blue-50 hover:bg-blue-100 hover:border-blue-600 transition duration-300 cursor-pointer relative">
                                <input type="file" name="dokumen[]" id="fileInput" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <div class="space-y-4">
                                    <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto shadow-md group-hover:scale-110 transition duration-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                    <p class="text-blue-800 font-medium">Tarik file ke sini atau klik untuk mencari</p>
                                    <p class="text-gray-500 text-xs text-uppercase italic">Semua format file didukung (Tanpa Batas Ukuran)</p>
                                </div>
                                <div id="selectedFiles" class="mt-6 text-left space-y-2 hidden">
                                    <p class="text-sm font-bold text-gray-700 border-b pb-1">File Terpilih:</p>
                                    <ul id="fileListNames" class="text-xs text-gray-600 list-disc list-inside"></ul>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-4 px-6 rounded-xl shadow-lg transform active:scale-95 transition duration-200">
                            🚀 Bagikan Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Pencarian User
        document.getElementById('userSearch').addEventListener('input', function(e) {
            const filter = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.user-card');
            cards.forEach(card => {
                const email = card.querySelector('span').textContent.toLowerCase();
                card.style.display = email.includes(filter) ? 'flex' : 'none';
            });
        });

        // Pilih Semua
        document.getElementById('selectAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            this.textContent = !allChecked ? 'Batal Pilih Semua' : 'Pilih Semua';
        });

        // Preview Nama File
        const fileInput = document.getElementById('fileInput');
        const fileListNames = document.getElementById('fileListNames');
        const selectedFilesDiv = document.getElementById('selectedFiles');

        fileInput.addEventListener('change', function() {
            fileListNames.innerHTML = '';
            if (this.files.length > 0) {
                selectedFilesDiv.classList.remove('hidden');
                Array.from(this.files).forEach(file => {
                    const li = document.createElement('li');
                    li.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                    fileListNames.appendChild(li);
                });
            } else {
                selectedFilesDiv.classList.add('hidden');
            }
        });

        // Dropzone Highlight
        const dropZone = document.getElementById('dropZone');
        ['dragenter', 'dragover'].forEach(name => {
            dropZone.addEventListener(name, () => dropZone.classList.add('bg-blue-200', 'border-blue-700'), false);
        });
        ['dragleave', 'drop'].forEach(name => {
            dropZone.addEventListener(name, () => dropZone.classList.remove('bg-blue-200', 'border-blue-700'), false);
        });
    </script>
</body>
</html>