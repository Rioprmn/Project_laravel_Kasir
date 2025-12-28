<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
    $categories = \App\Models\Category::all();
    return view('categories.index', compact('categories'));
}

public function store(Request $request) {
    // 1. Validasi input
    $request->validate([
        'name' => 'required|unique:categories,name|max:255'
    ]);

    // 2. Gunakan $request->only atau tentukan kolomnya secara manual
    // Ini menghindari error MassAssignment karena kita tidak menyertakan '_token'
    \App\Models\Category::create([
        'name' => $request->name
    ]);

    return back()->with('success', 'Kategori baru berhasil ditambahkan!');
}

public function destroy($id) {
    \App\Models\Category::findOrFail($id)->delete();
    return back()->with('success', 'Kategori dihapus!');
}
}
