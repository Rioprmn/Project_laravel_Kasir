@extends('layouts.main')

@section('content')
<div class="header-page" style="margin-bottom: 30px;">
    <h1 style="font-weight: 800; color: var(--sidebar-dark); margin: 0;">Halo, {{ Auth::user()->name }}! 👋</h1>
    <p style="color: #64748b; margin-top: 5px;">Cek performa tokomu hari ini yuk.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
    <div class="card" style="background: var(--primary-grad); color: white; position: relative; overflow: hidden; border: none;">
        <div style="position: absolute; right: -20px; top: -20px; font-size: 8rem; opacity: 0.1;">💰</div>
        <small style="text-transform: uppercase; letter-spacing: 1px; font-weight: 800;">Pendapatan Hari Ini</small>
        <h2 style="font-size: 2.5rem; margin: 15px 0;">Rp {{ number_format($incomeToday ?? 0) }}</h2>
        <div style="background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 50px; display: inline-block; font-size: 0.85rem;">
            🔥 Performa Bagus
        </div>
    </div>
    
    <div class="card" style="border-top: 5px solid var(--primary);">
        <small style="color: #64748b; font-weight: 800;">TOTAL TRANSAKSI</small>
        <h2 style="font-size: 2.5rem; margin: 15px 0; color: var(--sidebar-dark);">{{ $transactionCount ?? 0 }}</h2>
        <p style="color: #10b981; font-weight: 600; margin: 0;">✅ Terverifikasi sistem</p>
    </div>

    <div class="card" style="border-top: 5px solid #ef4444;">
        <small style="color: #64748b; font-weight: 800;">PRODUK HAMPIR HABIS</small>
        <h2 style="font-size: 2.5rem; margin: 15px 0; color: #ef4444;">{{ $lowStockCount ?? 0 }}</h2>
        <a href="/products" style="color: #ef4444; text-decoration: none; font-weight: bold; font-size: 0.9rem;">Kelola Stok &rarr;</a>
    </div>
</div>

<div style="margin-top: 30px; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
    <h3 style="margin-bottom: 20px; color: #1e293b; font-weight: 800;">📈 Tren Penjualan Seminggu Terakhir</h3>
    <div style="height: 350px;">
        <canvas id="salesChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($totals),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Total: Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>


<div style="margin-top: 40px;">
    <h3 style="color: var(--sidebar-dark);">Aksi Cepat</h3>
    <div style="display: flex; gap: 15px; margin-top: 15px;">
        <a href="{{ route('transactions.create') }}" class="btn btn-primary" style="text-decoration: none;">🛒 Buka Kasir</a>
        <a href="/products/create" class="btn btn-secondary" style="text-decoration: none; background: white; color: var(--sidebar-dark); border: 1px solid #e2e8f0;">📦 Tambah Produk</a>
    </div>
</div>


@endsection