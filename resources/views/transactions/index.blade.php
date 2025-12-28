@extends('layouts.main')

@section('content')
<div class="header-page" style="margin-bottom: 25px;">
    <h2 style="margin: 0; color: #1e293b;">📜 Laporan Transaksi</h2>
    <p style="color: #64748b; margin-top: 5px;">Pantau semua riwayat penjualan toko Anda di sini.</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div style="display: flex; gap: 10px; align-items: center;">
        <span style="background: #f1f5f9; padding: 8px 15px; border-radius: 20px; font-size: 0.85rem; color: #475569; font-weight: 600;">
            Total: {{ $transactions->count() }} Transaksi
        </span>
        
        {{-- TOMBOL DOWNLOAD --}}
        <a href="{{ route('transactions.export') }}" class="btn" style="background: #0ee995; color: white; text-decoration: none; font-size: 0.85rem; padding: 8px 15px; border-radius: 20px; display: flex; align-items: center; gap: 8px;">
            📥 Download CSV
        </a>
    </div>

    {{-- TOMBOL RESET (BARU) --}}
        @if($transactions->count() > 0)
        <form action="{{ route('transactions.reset') }}" method="POST" onsubmit="return confirm('⚠️ PERINGATAN: Semua data laporan akan dihapus permanen. Anda yakin?')">
            @csrf
            @method('DELETE')
            <button type="submit" style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; font-size: 0.85rem; padding: 8px 15px; border-radius: 20px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                🗑️ Reset Laporan
            </button>
        </form>
        @endif
    </div>

    <a href="{{ route('transactions.create') }}" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px;">
        <span>+</span> Transaksi Baru
    </a>
    </div>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>NO. NOTA</th>
                    <th>TANGGAL & WAKTU</th>
                    <th>KASIR</th>
                    <th>TOTAL BELANJA</th>
                    <th style="text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td>
                        <span class="badge-trx">#TRX-{{ $trx->id }}</span>
                    </td>
                    <td style="color: #475569;">
                        <div>{{ $trx->created_at->format('d M Y') }}</div>
                        <small style="color: #94a3b8;">{{ $trx->created_at->format('H:i') }} WIB</small>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 30px; height: 30px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold; color: #475569;">
                                {{ strtoupper(substr($trx->user->name ?? 'A', 0, 1)) }}
                            </div>
                            {{ $trx->user->name ?? 'Admin' }}
                        </div>
                    </td>
                    <td style="font-weight: 700; color: #1e293b;">
                        Rp {{ number_format($trx->total) }}
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('transactions.show', $trx->id) }}" class="btn-detail-modern">
                            Lihat Nota
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div style="text-align: center; padding: 50px 0;">
                            <div style="font-size: 3rem; margin-bottom: 10px;">🔍</div>
                            <h4 style="margin: 0; color: #475569;">Belum ada transaksi tercata</h4>
                            <p style="color: #94a3b8;">Mulai transaksi pertama Anda hari ini!</p>
                            <a href="{{ route('transactions.create') }}" style="color: #2563eb; text-decoration: none; font-weight: bold;">Buat Transaksi Baru &rarr;</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Navigasi Halaman --}}
    @if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div style="margin-top: 20px;">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection