@extends('layouts.main')

@section('content')
{{-- 1. HEADER HALAMAN --}}
<div class="product-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 20px; flex-wrap: wrap;">
    <div>
        <h1 style="font-weight: 800; color: var(--sidebar-dark); margin: 0; font-size: 1.8rem;">📦 Inventaris Produk</h1>
        <p style="color: #64748b; margin-top: 5px; font-size: 0.95rem;">Manajemen stok barang yang efisien.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        {{-- TOMBOL HAPUS MASSAL (Tersembunyi secara default) --}}
        <button type="button" id="btn-batch-delete" class="btn" style="display: none; background: #ef4444; color: white; border-radius: 12px; padding: 12px 20px; border: none; font-weight: bold; cursor: pointer;">
            🗑️ Hapus Terpilih (<span id="count-selected">0</span>)
        </button>
        
        <a href="{{ route('products.create') }}" class="btn btn-primary" style="display: flex; align-items: center; gap: 10px; padding: 12px 25px; border-radius: 12px; white-space: nowrap; text-decoration: none;">
            <span style="font-size: 1.2rem; font-weight: bold;">+</span> Tambah Produk
        </a>
    </div>
</div>

{{-- 2. FORM FILTER & PENCARIAN --}}
{{-- ... kode form filter kamu tetap sama ... --}}

{{-- 3. TABEL DATA PRODUK DENGAN BATCH DELETE --}}
<div class="card" style="border-radius: 24px; padding: 10px; overflow: hidden;">
    {{-- Form pembungkus untuk Batch Delete --}}
    <form id="form-batch-delete" action="{{ route('products.batchDelete') }}" method="POST">
        @csrf
        @method('DELETE')
        
        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="table-custom" style="width: 100%; min-width: 600px;">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">
                            <input type="checkbox" id="select-all" style="width: 18px; height: 18px; cursor: pointer;">
                        </th>
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
                        <td style="text-align: center;">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="product-checkbox" style="width: 18px; height: 18px; cursor: pointer;">
                        </td>
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
                                <a href="{{ route('products.edit', $product->id) }}" class="btn-action edit" style="text-decoration: none; display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; background: #f1f5f9; transition: 0.3s;">✏️</a>
                                {{-- Tombol hapus satuan tetap berfungsi --}}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>

{{-- SCRIPT INTERAKSI --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const batchDeleteBtn = document.getElementById('btn-batch-delete');
        const countSpan = document.getElementById('count-selected');
        const form = document.getElementById('form-batch-delete');

        function toggleBatchButton() {
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            if (checkedCount > 0) {
                batchDeleteBtn.style.display = 'block';
                countSpan.textContent = checkedCount;
            } else {
                batchDeleteBtn.style.display = 'none';
            }
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleBatchButton();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', toggleBatchButton);
        });

        batchDeleteBtn.addEventListener('click', function() {
            if (confirm('Yakin ingin menghapus semua produk yang dipilih?')) {
                form.submit();
            }
        });
    });
</script>
@endsection