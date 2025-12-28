@extends('layouts.main')

@section('content')
<div style="display: flex; flex-direction: column; gap: 30px;">
    {{-- Header --}}
    <div>
        <h1 style="font-weight: 800; color: var(--sidebar-dark); margin: 0; font-size: 1.8rem;">📁 Manajemen Kategori</h1>
        <p style="color: #64748b; margin-top: 5px;">Kelola kategori produk untuk memudahkan pencarian.</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px;">
        {{-- Form Tambah Kategori --}}
        <div class="card" style="padding: 25px; border-radius: 20px; background: white; border: 1px solid #e2e8f0; height: fit-content;">
            <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 1.1rem;">➕ Tambah Baru</h3>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 700; color: #475569; font-size: 0.85rem;">NAMA KATEGORI</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Alat Tulis" required 
                           style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #cbd5e1;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; border-radius: 10px; font-weight: bold;">
                    Simpan Kategori
                </button>
            </form>
        </div>

        {{-- Tabel Daftar Kategori --}}
        <div class="card" style="padding: 10px; border-radius: 24px; background: white; border: 1px solid #e2e8f0; overflow: hidden;">
            <table class="table-custom" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 15px;">NAMA KATEGORI</th>
                        <th style="padding: 15px; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                    <tr>
                        <td style="padding: 15px; font-weight: 700; color: #1e293b;">{{ $cat->name }}</td>
                        <td style="padding: 15px;">
                            <div style="display: flex; justify-content: center;">
                                <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Produk dengan kategori ini mungkin akan bermasalah.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: #fef2f2; border: none; padding: 8px; border-radius: 8px; color: #ef4444; cursor: pointer;">
                                        🗑️ Hapus
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
</div>
@endsection