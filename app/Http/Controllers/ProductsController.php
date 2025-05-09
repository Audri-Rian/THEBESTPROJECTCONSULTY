<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductsController extends Controller
{
    public function index()
    {
        return Inertia::render('ProductsManager');
    }

    public function getAllProducts()
    {
        $products = Product::with('supplier')->get();
        return response()->json($products);
    }

    public function getProduct(Product $product)
    {
        return Product::with('supplier')->findOrFail($product->id);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            $products = Product::with('supplier')->get();
            return response()->json(['products' => $products]);
        }

        $products = Product::with('supplier')
            ->where('name', 'like', '%' . $query . '%')
            ->orWhere('id', 'like', '%' . $query . '%')
            ->get();

        return response()->json(['products' => $products]);
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

        $product = Product::with('supplier')->find($product->id);

        return response()->json([
            'message' => 'Product created successfully!',
            'product' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'price_for_sale' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $filteredData = array_filter($validatedData, function ($value) {
            return !is_null($value);
        });

        $product->fill($filteredData)->save();

        $updatedProduct = Product::with('supplier')->find($product->id);
        return response()->json([
            'message' => 'Product updated successfully!',
            'product' => $updatedProduct
        ]);
    }

    public function destroy(Product $product)
    {
        $product->update(['status_id' => 2]);
        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully!'
        ]);
    }
}
