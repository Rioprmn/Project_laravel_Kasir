<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
    $query = Product::with('category');

    // Filter berdasarkan Nama Produk (Search)
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Filter berdasarkan Kategori
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    $products = $query->get();
    $categories = Category::all();

    return view('products.index', compact('products', 'categories'));
    }
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:100',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        Product::create($validated);
        return redirect('/products')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:100',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $product->update($validated);
        return redirect('/products')->with('success', 'Barang berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect('/products')->with('success', 'Barang berhasil dihapus');
    }

    
}