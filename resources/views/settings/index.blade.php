@extends('layouts.main')

@section('content')
<div style="max-width: 600px;">
    <h1 style="font-weight: 800; color: var(--sidebar-dark); margin-bottom: 20px;">⚙️ Pengaturan Toko</h1>

    <div class="card" style="padding: 30px; border-radius: 20px; background: white; border: 1px solid #e2e8f0;">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Nama Toko</label>
                <input type="text" name="shop_name" class="form-control" value="{{ $setting->shop_name ?? '' }}" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #cbd5e1;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Alamat Toko</label>
                <textarea name="shop_address" class="form-control" rows="3" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #cbd5e1;">{{ $setting->shop_address ?? '' }}</textarea>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Nomor Telepon/WhatsApp</label>
                <input type="text" name="shop_phone" class="form-control" value="{{ $setting->shop_phone ?? '' }}" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #cbd5e1;">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; border-radius: 12px; font-weight: bold; font-size: 1rem;">
                💾 Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection