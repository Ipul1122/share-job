<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="bg-blue-800 text-white w-64 min-h-screen flex-col hidden md:flex shadow-xl">
    <div class="p-6 flex items-center justify-center border-b border-blue-700">
        <h1 class="text-2xl font-bold tracking-wider">ShareDoc</h1>
    </div>
    <nav class="flex-1 p-4 space-y-2">
        <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 <?php echo $current_page == 'dashboard.php' ? 'bg-blue-600 font-semibold shadow' : ''; ?>">
            🏠 Dashboard
        </a>
        <a href="shareDoc.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 <?php echo $current_page == 'shareDoc.php' ? 'bg-blue-600 font-semibold shadow' : ''; ?>">
            📤 Kirim Dokumen
        </a>
        <a href="receiveDoc.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 <?php echo $current_page == 'receiveDoc.php' ? 'bg-blue-600 font-semibold shadow' : ''; ?>">
            📥 Dokumen Diterima
        </a>
        <a href="trackShare.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-600 <?php echo $current_page == 'trackShare.php' ? 'bg-blue-600 font-semibold shadow' : ''; ?>">
            ⏱️ Riwayat Kiriman
        </a>
    </nav>
    <div class="p-4 border-t border-blue-700">
        <a href="logout.php" class="block py-2 px-4 rounded bg-red-600 hover:bg-red-700 text-center transition duration-200 shadow">
            Logout
        </a>
    </div>
</aside>

<div class="md:hidden bg-blue-800 text-white p-4 flex justify-between items-center shadow-md">
    <h1 class="text-xl font-bold">ShareDoc</h1>
    <button id="mobileMenuBtn" class="focus:outline-none hover:text-blue-300">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</div>

<div id="mobileMenu" class="md:hidden hidden bg-blue-700 text-white flex-col shadow-lg">
    <a href="dashboard.php" class="block py-3 px-5 border-b border-blue-600 hover:bg-blue-500 <?php echo $current_page == 'dashboard.php' ? 'bg-blue-600' : ''; ?>">🏠 Dashboard</a>
    <a href="shareDoc.php" class="block py-3 px-5 border-b border-blue-600 hover:bg-blue-500 <?php echo $current_page == 'shareDoc.php' ? 'bg-blue-600' : ''; ?>">📤 Kirim Dokumen</a>
    <a href="receiveDoc.php" class="block py-3 px-5 border-b border-blue-600 hover:bg-blue-500 <?php echo $current_page == 'receiveDoc.php' ? 'bg-blue-600' : ''; ?>">📥 Dokumen Diterima</a>
    <a href="trackShare.php" class="block py-3 px-5 border-b border-blue-600 hover:bg-blue-500 <?php echo $current_page == 'trackShare.php' ? 'bg-blue-600' : ''; ?>">⏱️ Riwayat Kiriman</a>
    <a href="logout.php" class="block py-3 px-5 bg-red-600 hover:bg-red-700">Logout</a>
</div>

<script>
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
        menu.classList.toggle('flex');
    });
</script>