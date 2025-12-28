<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
   public function index() {
    // Ambil data pertama, jika tidak ada buat objek kosong
    $setting = \App\Models\Setting::first();
    return view('settings.index', compact('setting'));
}

public function update(Request $request) {
    $request->validate([
        'shop_name' => 'required',
        'shop_address' => 'required',
        'shop_phone' => 'required',
    ]);

    // Update data pertama atau buat jika belum ada
    \App\Models\Setting::updateOrCreate(['id' => 1], $request->all());

    return back()->with('success', 'Pengaturan toko berhasil diperbarui!');
}
}
