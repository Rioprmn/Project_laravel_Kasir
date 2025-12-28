<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Ringkasan (Sudah benar)
        $incomeToday = Transaction::whereDate('created_at', Carbon::today())->sum('total');
        $transactionCount = Transaction::whereDate('created_at', Carbon::today())->count();
        $lowStockCount = Product::where('stock', '<=', 5)->count();

        // 2. LOGIKA GRAFIK: Ambil data 7 hari terakhir
        $salesData = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // 3. Format data untuk Chart.js
        $labels = [];
        $totals = [];
        
        // Loop untuk memastikan setiap hari dalam 7 hari terakhir ada (meskipun 0)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $displayDate = Carbon::now()->subDays($i)->format('d M');
            
            $labels[] = $displayDate;
            
            // Cari data yang tanggalnya cocok
            $row = $salesData->firstWhere('date', $date);
            $totals[] = $row ? $row->total : 0;
        }

        return view('dashboard', compact(
            'incomeToday', 
            'transactionCount', 
            'lowStockCount', 
            'labels', 
            'totals'
        ));
    }
}