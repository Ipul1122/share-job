<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Fungsi kecil untuk menentukan menu aktif
function isActive($page, $current) {
    return $page === $current ? 'bg-blue-600 text-white font-semibold shadow-md translate-x-1' : 'hover:bg-blue-700 hover:translate-x-1';
}
?>

<aside class="bg-blue-800 text-blue-100 w-64 min-h-screen flex-col hidden md:flex shadow-2xl relative z-10">
    <div class="p-6 flex items-center justify-center border-b border-blue-700 bg-blue-900/50">
        <div class="flex items-center gap-3">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
            <h1 class="text-2xl font-extrabold text-white tracking-wider">ShareDoc</h1>
        </div>
    </div>
    
    <nav class="flex-1 p-4 space-y-2 mt-4">
        <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2 px-4">Menu Utama</p>
        <a href="dashboard.php" class="block py-3 px-4 rounded-xl transition-all duration-300 <?php echo isActive('dashboard.php', $current_page); ?>">
            <span class="flex items-center gap-3"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg> Dashboard</span>
        </a>
        <a href="shareDoc.php" class="block py-3 px-4 rounded-xl transition-all duration-300 <?php echo isActive('shareDoc.php', $current_page); ?>">
            <span class="flex items-center gap-3"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg> Kirim Dokumen</span>
        </a>
        <a href="receiveDoc.php" class="block py-3 px-4 rounded-xl transition-all duration-300 <?php echo isActive('receiveDoc.php', $current_page); ?>">
            <span class="flex items-center gap-3"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg> Dokumen Diterima</span>
        </a>
        <a href="trackShare.php" class="block py-3 px-4 rounded-xl transition-all duration-300 <?php echo isActive('trackShare.php', $current_page); ?>">
            <span class="flex items-center gap-3"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Riwayat Kiriman</span>
        </a>
    </nav>
    
    <div class="p-4 border-t border-blue-700 bg-blue-900/30">
        <a href="logout.php" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-600 hover:text-white hover:shadow-lg transition-all duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span class="font-bold">Logout</span>
        </a>
    </div>
</aside>

<div class="md:hidden bg-blue-800 text-white p-4 flex justify-between items-center shadow-md relative z-10">
    <div class="flex items-center gap-2">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
        <h1 class="text-xl font-bold">ShareDoc</h1>
    </div>
    <button id="mobileMenuBtn" class="focus:outline-none hover:text-blue-300 transition-colors">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</div>

<div id="mobileMenuOverlay" class="fixed inset-0 bg-blue-900/90 backdrop-blur-md z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col md:hidden">
    
    <div class="flex justify-end p-6">
        <button id="closeMenuBtn" class="text-white hover:text-red-400 focus:outline-none transition-colors">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <div class="flex flex-col items-center justify-center flex-1 space-y-8 pb-20">
        <a href="dashboard.php" class="text-2xl font-bold transition-all duration-300 <?php echo $current_page == 'dashboard.php' ? 'text-blue-300 scale-110' : 'text-white hover:text-blue-200 hover:scale-110'; ?>">Dashboard</a>
        <a href="shareDoc.php" class="text-2xl font-bold transition-all duration-300 <?php echo $current_page == 'shareDoc.php' ? 'text-blue-300 scale-110' : 'text-white hover:text-blue-200 hover:scale-110'; ?>">Kirim Dokumen</a>
        <a href="receiveDoc.php" class="text-2xl font-bold transition-all duration-300 <?php echo $current_page == 'receiveDoc.php' ? 'text-blue-300 scale-110' : 'text-white hover:text-blue-200 hover:scale-110'; ?>">Dokumen Diterima</a>
        <a href="trackShare.php" class="text-2xl font-bold transition-all duration-300 <?php echo $current_page == 'trackShare.php' ? 'text-blue-300 scale-110' : 'text-white hover:text-blue-200 hover:scale-110'; ?>">Riwayat Kiriman</a>
        
        <div class="w-24 border-t-2 border-blue-500/50 my-4"></div>
        
        <a href="logout.php" class="text-xl font-bold text-red-400 hover:text-red-300 hover:scale-110 transition-all duration-300">Logout</a>
    </div>
</div>

<script>
    // Logika untuk menampilkan dan menyembunyikan Fullscreen Mobile Menu
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const closeMenuBtn = document.getElementById('closeMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    mobileMenuBtn.addEventListener('click', () => {
        mobileMenuOverlay.classList.remove('translate-x-full');
        mobileMenuOverlay.classList.add('translate-x-0');
        document.body.style.overflow = 'hidden'; // Mencegah scrolling di latar belakang
    });

    closeMenuBtn.addEventListener('click', () => {
        mobileMenuOverlay.classList.remove('translate-x-0');
        mobileMenuOverlay.classList.add('translate-x-full');
        document.body.style.overflow = 'auto'; // Mengembalikan scrolling
    });
</script>