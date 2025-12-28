<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Kasir Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/stayle.css') }}">
    <style>
    /* Tombol Toggle Menu (Hanya muncul di HP) */
    .mobile-nav-toggle {
        display: none;
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px;
        border-radius: 10px;
        cursor: pointer;
        box-shadow: var(--shadow-md);
    }

    @media (max-width: 992px) {
        .mobile-nav-toggle { display: block; }
        
        .sidebar {
            left: -300px; /* Sembunyikan sidebar ke kiri */
            transition: 0.4s;
            height: calc(100vh - 40px);
        }

        .sidebar.active {
            left: 20px; /* Munculkan saat tombol diklik */
        }

        .main-content {
            margin-left: 0 !important;
            padding: 80px 20px 20px 20px !important;
        }

        /* Perbaikan tabel di HP agar bisa di-scroll */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Grid Filter agar jadi 1 kolom di HP */
        form[style*="display: grid"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<button class="mobile-nav-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
    ☰ Menu
</button>
    @stack('styles')
</head>
<body>
    <div class="sidebar" style="display: flex; flex-direction: column; height: 100vh;">
    {{-- Atas: Brand --}}
    <div class="sidebar-brand">KASIR-KU</div>
    
    {{-- Tengah: Menu (Mengisi sisa ruang) --}}
    <ul class="sidebar-menu" style="flex-grow: 1; list-style: none; padding: 0;">
    {{-- Dashboard --}}
    <li>
        <a href="{{ route('dashboard') }}" class="{{ Request::is('dashboard') ? 'active' : '' }}">📊 Dashboard</a>
    </li>

    {{-- Menu Group Produk --}}
    <li style="margin-bottom: 5px;">
        {{-- Menu Utama Produk --}}
        <a href="/products" class="{{ Request::is('products*') && !Request::is('categories*') ? 'active' : '' }}">📦 Produk</a>
        
        {{-- Sub-menu Kategori --}}
        <ul style="list-style: none; padding-left: 20px; margin-top: 5px;">
            <li>
                <a href="{{ route('categories.index') }}" 
                   class="{{ Request::is('categories*') ? 'active' : '' }}" 
                   style="font-size: 0.9rem; opacity: 0.9; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 0.8rem;">📁</span> Kategori
                </a>
            </li>
        </ul>
    </li>

    {{-- Kasir --}}
    <li>
        <a href="{{ route('transactions.create') }}" class="{{ Request::is('transactions/create') ? 'active' : '' }}">🛒 Kasir</a>
    </li>

    {{-- Laporan --}}
    <li>
        <a href="/transactions" class="{{ Request::is('transactions') ? 'active' : '' }}">📜 Laporan</a>
    </li>
    {{-- Pengaturan --}}
    <li>
    <a href="{{ route('settings.index') }}" class="{{ Request::is('settings*') ? 'active' : '' }}">⚙️ Pengaturan</a>
    </li>
</ul>
    
    {{-- Bawah: Logout Area (Lebih rapi & tidak maksa mepet) --}}
    <div class="sidebar-footer" style="padding: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <span>🚪</span> Keluar Aplikasi
            </button>
        </form>
    </div>
</div>
    <div class="main-content">
        @yield('content')
    </div>

    @stack('scripts')
    
    @push('scripts')
    <script src="{{ asset('js/product-batch.js') }}"></script>
    @endpush
</body>
</html>