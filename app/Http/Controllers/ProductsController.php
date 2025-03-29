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
            'quantity' => 'nullable|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'supplier_id' => $request->supplier_id ?? null,
        ]);

        $product->save();

        return redirect()->route('products')->with('success', 'Product created successfully!');
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
            'quantity' => 'nullable|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $product->update($validatedData);

        return redirect()->route('products')->with('success', 'Product updated successfully!');
    }

}
