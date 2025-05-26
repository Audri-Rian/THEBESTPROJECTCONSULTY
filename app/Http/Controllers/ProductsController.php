<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\ProductsHistory;
use App\Models\StockHistory;

class ProductsController extends Controller
{
    public function index()
    {
        return Inertia::render('products/ProductsManager');
    }

    public function history()
    {
        return Inertia::render('products/ProductsHistory');
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

    public function getProductsHistory()
    {
        $history = new ProductsHistory();
        $productSales = $history->getProductsHistory();
        return response()->json($productSales);
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

        if (Product::where('name', $request->name)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Product already exists!'
            ]);
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'price_for_sale' => $request->price_for_sale,
            'quantity' => $request->quantity,
            'supplier_id' => $request->supplier_id ?? null,
        ]);

        $product = Product::with('supplier')->find($product->id);

        $stockHistory = StockHistory::create([
            'product_id' => $product->id,
            'quantity' => $product->quantity,
        ]);

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

        if (isset($validatedData['quantity']) && $validatedData['quantity'] != $product->quantity) {
            $change = $validatedData['quantity'] - $product->quantity;
            StockHistory::create([
                'product_id' => $product->id,
                'quantity' => $change,
            ]);
        }

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
