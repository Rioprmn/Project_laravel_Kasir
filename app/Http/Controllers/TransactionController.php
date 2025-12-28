<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::all();
        $totalCart = 0;
        return view('transactions.create', compact('products'));
    }

    public function store(Request $request)
    {
    // 1. Validasi input
    $request->validate([
        'bayar' => 'required|numeric|min:0',
        'total' => 'required|numeric|min:0',
    ]);

    // 2. Ambil data keranjang
    $cart = session()->get('cart');

    if (!$cart || count($cart) == 0) {
        return redirect()->back()->with('error', 'Keranjang masih kosong!');
    }

    // 3. Cek apakah uang cukup
    if ($request->bayar < $request->total) {
        return redirect()->back()->with('error', 'Uang bayar tidak cukup!');
    }

    try {
        // 4. Proses Simpan dengan Database Transaction
        DB::transaction(function () use ($request, $cart) {
            
            // Simpan Header Transaksi
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'total'   => $request->total,
                'bayar'   => $request->bayar,
                'kembali' => $request->bayar - $request->total,
                'tanggal' => now(), // Pastikan kolom ini ada di database atau biarkan default
            ]);

            foreach ($cart as $id => $details) {
                // Ambil data produk terbaru untuk cek stok
                $product = Product::findOrFail($id);

                // Tambahan: Validasi stok sebelum potong (mencegah stok minus)
                if ($product->stock < $details['qty']) {
                    throw new \Exception("Stok untuk produk {$product->name} tidak mencukupi!");
                }

                // Simpan Detail Transaksi
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $id,
                    'price'          => $details['price'],
                    'qty'            => $details['qty'],
                    'subtotal'       => $details['price'] * $details['qty']
                ]);

                // Potong Stok Produk secara otomatis
                $product->decrement('stock', $details['qty']);
            }
        });

        // 5. Hapus Keranjang setelah berhasil
        session()->forget('cart');

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil! Kembalian: Rp ' . number_format($request->bayar - $request->total, 0, ',', '.'));

    } catch (\Exception $e) {
        // Jika ada error (seperti stok habis di tengah proses), transaksi di-rollback otomatis
        return redirect()->back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
    }
    }

    public function addToCart(Request $request)
    {
    $product = Product::findOrFail($request->product_id);
    $cart = session()->get('cart', []);

    $qtyInCart = $cart[$product->id]['qty'] ?? 0;
    $totalQty = $qtyInCart + $request->qty;

    if ($totalQty > $product->stock) {
        return back()->with('error', 'Stok tidak mencukupi');
    }

    $cart[$product->id] = [
        'name' => $product->name,
        'price' => $product->price,
        'qty' => $totalQty,
        'unit' => $product->unit, // Tambahkan ini agar satuan tersimpan di keranjang
    ];

    session()->put('cart', $cart);
    return back();
    }

    public function updateCart(Request $request)
    {
    // 1. Validasi awal
    $request->validate([
        'product_id' => 'required',
        'action' => 'required|in:increase,decrease' // Menerima increase atau decrease
    ]);

    $cart = session()->get('cart', []);

    if (!isset($cart[$request->product_id])) {
        return back()->with('error', 'Produk tidak ditemukan di cart');
    }

    $product = Product::findOrFail($request->product_id);
    $currentQty = $cart[$request->product_id]['qty'];

    // 2. Logika Tambah atau Kurang
    if ($request->action === 'increase') {
        // Cek stok sebelum nambah
        if ($currentQty + 1 > $product->stock) {
            return back()->with('error', 'Stok tidak mencukupi!');
        }
        $cart[$request->product_id]['qty']++;
    } else {
        // Logika Kurang
        if ($currentQty > 1) {
            $cart[$request->product_id]['qty']--;
        } else {
            // Kalau sisa 1 dikurang lagi, hapus dari keranjang
            unset($cart[$request->product_id]);
        }
    }

    // 3. Simpan balik ke session
    session()->put('cart', $cart);

    return back(); // Refresh halaman dengan data terbaru
    }


    public function removeFromCart(Request $request)
    {
    $cart = session()->get('cart', []);
    unset($cart[$request->product_id]);
    session()->put('cart', $cart);

    return back();
    }

    // Ubah bagian ini di TransactionController.php

public function checkout(Request $request)
{
    $cart = session()->get('cart');

    if (!$cart || count($cart) === 0) {
        return back()->with('error', 'Keranjang belanja masih kosong!');
    }

    // 1. Hitung Total belanja dari session
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['qty'];
    }

    // 2. Validasi: Apakah uang yang diinput cukup?
    if ($request->bayar < $total) {
        return back()->with('error', 'Uang bayar tidak mencukupi! Kurang Rp ' . number_format($total - $request->bayar));
    }

    $transactionId = DB::transaction(function () use ($cart, $total, $request) {
        // 3. Simpan transaksi utama
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'total'   => $total,
            'bayar'   => $request->bayar,
            'kembali' => $request->bayar - $total, // Hitung otomatis kembalian
        ]);

        // 4. Simpan detail item & kurangi stok
        foreach ($cart as $productId => $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id'     => $productId,
                'price'          => $item['price'],
                'qty'            => $item['qty'],
                'subtotal'       => $item['price'] * $item['qty']
            ]);

            Product::find($productId)->decrement('stock', $item['qty']);
        }
        
        return $transaction->id;
    });

    session()->forget('cart');

    return redirect()->route('transactions.show', $transactionId)
                     ->with('success', 'Pembayaran Berhasil!');
}

    public function index()
    {
    // Mengambil 10 transaksi per halaman
    $transactions = Transaction::with(['user', 'items.product'])
                    ->latest()
                    ->paginate(10); 

    return view('transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = Transaction::with('items.product', 'user')->findOrFail($id);
        return view('transactions.invoice', compact('transaction'));
    }


    public function export()
    {
    $transactions = Transaction::with('user')->orderBy('created_at', 'desc')->get();
    
    $fileName = 'laporan-transaksi-' . date('Y-m-d') . '.csv';
    
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['No. Nota', 'Tanggal', 'Waktu', 'Kasir', 'Total Belanja'];

    $callback = function() use($transactions, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($transactions as $trx) {
            fputcsv($file, [
                '#TRX-' . $trx->id,
                $trx->created_at->format('d M Y'),
                $trx->created_at->format('H:i') . ' WIB',
                $trx->user->name ?? 'Admin',
                $trx->total
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
    }

    public function clearCart()
    {
    session()->forget('cart');
    return redirect()->back()->with('success', 'Keranjang belanja dikosongkan.');
    }


    public function reset()
    {
    try {
        DB::transaction(function () {
            // Hapus semua detail transaksi dulu (jika tidak pakai cascade delete di DB)
            DB::table('transaction_items')->delete();
            // Hapus semua header transaksi
            DB::table('transactions')->delete();
        });

        return redirect()->back()->with('success', 'Semua riwayat laporan berhasil dibersihkan!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal mereset laporan: ' . $e->getMessage());
    }
    }

}
