@extends('layouts.main')

@section('content')
<div style="background-color: #f8fafc; min-height: 100vh; padding: 20px; border-radius: 20px;">
    
    {{-- Search Bar --}}
    <div style="margin-bottom: 25px;">
        <input type="text" id="searchProduct" placeholder="🔎 Cari nama produk atau kode..." 
            style="width: 100%; padding: 15px 25px; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); outline: none;">
    </div>

    {{-- Grid Produk --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
        @foreach($products as $product)
        <div class="card-product" style="background: white; border-radius: 15px; padding: 20px; text-align: center; border: 1px solid #e2e8f0;">
            <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 1.5rem;">📦</div>
            <h4 style="margin: 0; color: #1e293b; font-weight: 800; min-height: 45px; font-size: 1rem;">{{ $product->name }}</h4>
            <p style="color: #94a3b8; font-size: 0.75rem; margin: 5px 0;">{{ $product->code ?? 'P-'.$product->id }}</p>
            <h3 style="color: #2563eb; margin: 10px 0; font-weight: 800; font-size: 1.2rem;">Rp {{ number_format($product->price, 0, ',', '.') }}</h3>
            
            <div style="margin-bottom: 15px;">
                @if($product->stock <= 0)
                    <span style="font-size: 0.75rem; color: #ef4444; background: #fef2f2; padding: 4px 8px; border-radius: 6px; font-weight: 700;">Stok Habis</span>
                @else
                    <span style="font-size: 0.75rem; color: #64748b;">Stok: <b>{{ $product->stock }}</b></span>
                @endif
            </div>
            
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="qty" value="1">
                <button type="submit" {{ $product->stock <= 0 ? 'disabled' : '' }} 
                    style="background: {{ $product->stock <= 0 ? '#cbd5e1' : '#2563eb' }}; color: white; border: none; padding: 10px 15px; border-radius: 10px; width: 100%; cursor: pointer; font-weight: 700;">
                    + Tambah
                </button>
            </form>
        </div>
        @endforeach
    </div>

    {{-- Area Keranjang --}}
    <div style="background: white; border-radius: 20px; padding: 25px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-bottom: 25px; color: #1e293b; font-weight: 800;">🛒 Detail Pesanan</h3>
        
        @if(session('cart') && count(session('cart')) > 0)
            <div style="max-height: 400px; overflow-y: auto; margin-bottom: 20px;">
                @php $total = 0; @endphp
                @foreach(session('cart') as $id => $item)
                    @php 
                        $subtotal = $item['price'] * $item['qty']; 
                        $total += $subtotal;
                    @endphp
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #f1f5f9;">
                        <div style="flex: 2;">
                            <div style="font-weight: 700; color: #1e293b;">{{ $item['name'] }}</div>
                            <div style="color: #94a3b8; font-size: 0.8rem;">Rp {{ number_format($item['price']) }}</div>
                        </div>

                        {{-- EDIT QUANTITY --}}
                        <div style="flex: 1; display: flex; align-items: center; gap: 10px; justify-content: center;">
                            <form action="{{ route('cart.update') }}" method="POST" style="margin: 0; display: flex; align-items: center; background: #f1f5f9; padding: 5px; border-radius: 8px;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $id }}">
                                
                                {{-- Tombol Kurang --}}
                                <button type="submit" name="action" value="decrease" 
                                    style="border: none; background: white; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; font-weight: bold; color: #64748b; transition: 0.2s;"
                                    onmouseover="this.style.background='#fee2e2'; this.style.color='#ef4444'" 
                                    onmouseout="this.style.background='white'; this.style.color='#64748b'">-</button>
                                
                                {{-- Angka Qty dan Satuan --}}
                                <div style="margin: 0 15px; display: flex; flex-direction: column; align-items: center; min-width: 45px;">
                                    <span style="font-weight: 800; color: #1e293b; line-height: 1;">{{ $item['qty'] }}</span>
                                    <small style="font-size: 0.65rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px;">
                                        {{ $item['unit'] ?? 'Pcs' }}
                                    </small>
                                </div>
                                
                                {{-- Tombol Tambah --}}
                                <button type="submit" name="action" value="increase" 
                                    style="border: none; background: white; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; font-weight: bold; color: #64748b; transition: 0.2s;"
                                    onmouseover="this.style.background='#dcfce7'; this.style.color='#16a34a'" 
                                    onmouseout="this.style.background='white'; this.style.color='#64748b'">+</button>
                            </form>
                        </div>

                        <div style="flex: 1; text-align: right; display: flex; align-items: center; justify-content: flex-end; gap: 15px;">
                            <span style="font-weight: 800;">Rp {{ number_format($subtotal) }}</span>
                            <form action="{{ route('cart.remove') }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $id }}">
                                <button type="submit" style="background: #fee2e2; border: none; color: #ef4444; width: 30px; height: 30px; border-radius: 6px; cursor: pointer;">✕</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="background: #f1f5f9; padding: 20px; border-radius: 15px; margin-bottom: 25px;">
                <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 800;">
                    <span>Total Belanja</span>
                    <span style="color: #2563eb;">Rp {{ number_format($total) }}</span>
                </div>
                
                <div style="margin-top: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; display: block;">NOMINAL UANG BAYAR</label>
                    <input type="number" id="input_bayar" class="form-control" placeholder="0" 
                        style="font-size: 1.8rem; font-weight: 800; text-align: right; border: none; width: 100%; outline: none;">
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                    <span style="font-weight: 700; color: #64748b;">Kembalian</span>
                    <span id="display_kembalian" style="font-size: 1.2rem; font-weight: 800; color: #64748b;">Rp 0</span>
                </div>
            </div>

            <input type="hidden" id="total_belanja" value="{{ $total }}">

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="total" value="{{ $total }}">
                    <input type="hidden" name="bayar" id="hidden_bayar" value="0">
                    <button type="submit" id="btn-checkout" disabled 
                        style="background: #cbd5e1; color: white; border: none; padding: 18px; border-radius: 12px; width: 100%; cursor: not-allowed; font-weight: 800;">
                        ⚠️ MASUKKAN PEMBAYARAN
                    </button>
                </form>
                
                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan keranjang?')">
                    @csrf
                    <button type="submit" style="background: #fff; color: #94a3b8; border: 1px solid #e2e8f0; padding: 18px; border-radius: 12px; width: 100%; font-weight: 700;">
                        KOSONGKAN
                    </button>
                </form>
            </div>
        @else
            <div style="text-align: center; padding: 60px; color: #94a3b8;">
                <p>Keranjang kosong</p>
            </div>
        @endif
    </div>
</div>

{{-- PANGGIL JS DI SINI --}}
@push('scripts')
    <script src="{{ asset('js/kasir.js') }}"></script>
@endpush

@endsection