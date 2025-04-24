<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::with('supplier')->get();
        $suppliers = Supplier::all();

        return Inertia::render('ProductsManager' , compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'price_for_sale' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'price_for_sale' => $request->price_for_sale,
            'quantity' => $request->quantity,
            'supplier_id' => $request->supplier_id ?? null,
        ]);

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function getProduct(Product $product)
    {
        return $product;
    }

    public function update(Request $request, Product $product){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'price_for_sale' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $product->update($validatedData);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function search(Request $request){
        $search = $request->input('search');

        $products = Product::with('supplier')
        ->when($search, function($query, $search){
            $query->where('name', 'like', '%' . $search . '%');
        })->get();

        $suppliers = Supplier::all();

        return Inertia::render('ProductsManager', compact('products', 'suppliers'));
    }

}
