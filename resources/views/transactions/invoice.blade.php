<!DOCTYPE html>
<html>
<head>
    <title>Nota Transaksi #{{ $transaction->id }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 13px; color: #000; }
        .invoice-box { max-width: 300px; margin: auto; padding: 10px; border: 1px solid #eee; }
        .text-center { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { border-bottom: 1px dashed #000; text-align: left; padding: 5px 0; }
        td { padding: 5px 0; vertical-align: top; }
        .line { border-top: 1px dashed #000; margin-top: 5px; }
        .total-section td { font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #2563eb; color: white; border: none; border-radius: 5px;">🖨️ Cetak Nota</button>
        <a href="{{ route('transactions.create') }}" style="padding: 10px; text-decoration: none; color: #64748b; font-size: 12px;">Kembali ke Kasir</a>
    </div>

    <div class="invoice-box">
        <div class="text-center">
            <h3 style="margin-bottom: 5px; text-transform: uppercase;">NAMA TOKO KASIR</h3>
            <p style="margin: 0; font-size: 11px;">Jl. Raya Alamat Toko No. 123</p>
            <div class="line"></div>
            <p style="margin: 10px 0; text-align: left;">
                No    : #TRX-{{ $transaction->id }}<br>
                Tgl   : {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
                Kasir : {{ $transaction->user->name ?? 'Admin' }}
            </p>
            <div class="line"></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td style="width: 50%;">{{ $item->product->name }}</td>
                    <td style="text-align: center;">{{ $item->qty }}</td>
                    <td style="text-align: right;">{{ number_format($item->subtotal) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="line"></div>
        
        <table class="total-section">
            <tr>
                <td colspan="2">TOTAL</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->total) }}</td>
            </tr>
            <tr>
                <td colspan="2">BAYAR</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->bayar) }}</td>
            </tr>
            <tr>
                <td colspan="2">KEMBALI</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->kembali) }}</td>
            </tr>
        </table>
        
        <div class="line"></div>
        <p class="text-center" style="margin-top: 15px;">-- Terima Kasih --<br>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
    </div>
</body>
</html>