@extends('layouts.main')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto; animation: fadeIn 0.5s ease;">
    <div class="card-header" style="margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
        <h2 style="color: var(--sidebar-dark); margin: 0;">✏️ Edit Produk</h2>
        <p style="color: #64748b; font-size: 0.9rem;">Mengubah data: <strong>{{ $product->name }}</strong></p>
    </div>

    <form method="POST" action="{{ route('products.update', $product->id) }}">
        @csrf
        @method('PUT') {{-- WAJIB ADA UNTUK EDIT --}}
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 8px;">Kategori</label>
            <select name="category_id" class="form-control" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 8px;">Nama Barang</label>
            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
        </div>

        <div style="margin-bottom: 15px;">
        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Satuan Barang</label>
        <select name="unit" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
            <option value="Pcs">Pcs (Biji/Buah)</option>
            <option value="Kg">Kg (Kilogram)</option>
            <option value="Gr">Gr (Gram)</option>
            <option value="Botol">Botol</option>
            <option value="Liter">Liter</option>
            <option value="Pack">Pack/Box</option>
        </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 8px;">Harga (Rp)</label>
                <input type="number" name="price" class="form-control" value="{{ $product->price }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 8px;">Stok</label>
                <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
            </div>
        </div>

        <div class="btn-group" style="margin-top: 30px; display: flex; gap: 15px;">
            <button type="submit" class="btn btn-primary" style="flex: 2;">
                💾 Simpan Perubahan
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary" style="flex: 1; text-decoration: none; background: #64748b; color: white; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                ❌ Batal
            </a>
        </div>
    </form>
</div>
@endsection