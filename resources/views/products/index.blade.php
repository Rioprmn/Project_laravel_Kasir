@extends('layouts.main')

@section('content')
{{-- 1. HEADER HALAMAN --}}
<div class="product-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 20px; flex-wrap: wrap;">
    <div>
        <h1 style="font-weight: 800; color: var(--sidebar-dark); margin: 0; font-size: 1.8rem;">📦 Inventaris Produk</h1>
        <p style="color: #64748b; margin-top: 5px; font-size: 0.95rem;">Manajemen stok barang yang efisien.</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary" style="display: flex; align-items: center; gap: 10px; padding: 12px 25px; border-radius: 12px; white-space: nowrap; text-decoration: none;">
        <span style="font-size: 1.2rem; font-weight: bold;">+</span> Tambah Produk
    </a>
</div>

{{-- 2. FORM FILTER & PENCARIAN (RESPONSIVE GRID) --}}
<div class="card" style="margin-bottom: 25px; padding: 20px; border-radius: 20px; background: #f8fafc; border: 1px solid #e2e8f0;">
    <form action="{{ route('products.index') }}" method="GET" class="filter-form">
        <style>
            .filter-form {
                display: grid;
                grid-template-columns: 2fr 1fr auto auto;
                gap: 15px;
                align-items: flex-end;
            }
            @media (max-width: 768px) {
                .filter-form { grid-template-columns: 1fr; }
            }
        </style>
        
        {{-- Input Nama Produk --}}
        <div>
            <label style="font-weight: 700; color: #475569; font-size: 0.8rem; margin-bottom: 8px; display: block; text-transform: uppercase;">
                🔎 Cari Nama Produk
            </label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama barang..." 
                   class="form-control" style="background: white; height: 45px; border-radius: 10px; border: 1px solid #cbd5e1;">
        </div>

        {{-- Dropdown Kategori --}}
        <div>
            <label style="font-weight: 700; color: #475569; font-size: 0.8rem; margin-bottom: 8px; display: block; text-transform: uppercase;">
                📁 Kategori
            </label>
            <select name="category_id" class="form-control" style="background: white; height: 45px; border-radius: 10px; border: 1px solid #cbd5e1;">
                <option value="">Semua</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary" style="height: 45px; padding: 0 25px; border-radius: 10px; font-weight: bold;">
            Cari
        </button>
        
        @if(request('search') || request('category_id'))
            <a href="{{ route('products.index') }}" class="btn btn-secondary" 
               style="height: 45px; display: flex; align-items: center; padding: 0 20px; background: #64748b; color: white; text-decoration: none; border-radius: 10px; font-weight: bold;">
               Reset
            </a>
        @endif
    </form>
</div>

{{-- 3. TABEL DATA PRODUK (WITH RESPONSIVE WRAPPER) --}}
<div class="card" style="border-radius: 24px; padding: 10px; overflow: hidden;">
    <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <table class="table-custom" style="width: 100%; min-width: 600px;">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div class="product-avatar" style="width: 35px; height: 35px; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-weight: bold;">
                                {{ strtoupper(substr($product->name, 0, 1)) }}
                            </div>
                            <div style="font-weight: 700;">{{ $product->name }}</div>
                        </div>
                    </td>
                    <td>
                        <span style="color: #64748b; font-weight: 600;">{{ $product->category->name }}</span>
                    </td>
                    <td style="font-weight: 800; color: var(--primary-dark);">
                        Rp {{ number_format($product->price) }}
                    </td>
                    <td>
                        @if($product->stock <= 5)
                            <div class="badge-stock low" style="background: #fff1f2; color: #e11d48; padding: 5px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 700;">
                                ⚠️ {{ $product->stock }} Kritis
                            </div>
                        @else
                            <div class="badge-stock safe" style="background: #ecfdf5; color: #059669; padding: 5px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 700;">
                                ✅ {{ $product->stock }} Aman
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; justify-content: center; gap: 10px; align-items: center;">
                            <a href="{{ route('products.edit', $product->id) }}" 
                               class="btn-action edit" 
                               style="text-decoration: none; display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; background: #f1f5f9; transition: 0.3s;">
                               ✏️
                            </a>

                            <form action="{{ route('products.destroy', $product->id) }}" 
                                  method="POST" 
                                  style="margin: 0;" 
                                  onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="btn-action delete" 
                                        style="border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; background: #fef2f2; transition: 0.3s; color: #ef4444;">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection