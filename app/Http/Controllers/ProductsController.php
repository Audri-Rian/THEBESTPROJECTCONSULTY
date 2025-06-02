<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\ProductsHistory;
use App\Models\StockHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'price' => 'nullable|numeric|min:0',
                'price_for_sale' => 'nullable|numeric|min:0',
                'quantity' => 'nullable|integer|min:0',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if (Product::where('name', $request->name)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product already exists!'
                ]);
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                try {
                    $imagePath = $request->file('image')->store('products', 'public');
                } catch (\Exception $e) {
                    Log::error('Error uploading image: ' . $e->getMessage());
                    return response()->json([
                        'status' => false,
                        'message' => 'Error uploading image: ' . $e->getMessage()
                    ], 500);
                }
            }

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price ?: 0,
                'price_for_sale' => $request->price_for_sale ?: 0,
                'quantity' => $request->quantity ?: 0,
                'supplier_id' => $request->supplier_id ?? null,
                'image_path' => $imagePath,
                'status_id' => 1
            ]);

            $product = Product::with('supplier')->find($product->id);

            // Create stock history with default values
            StockHistory::create([
                'product_id' => $product->id,
                'quantity' => $request->quantity ?: 0,
                'price' => $request->price ?: 0,
                'price_for_sale' => $request->price_for_sale ?: 0
            ]);

            return response()->json([
                'message' => 'Product created successfully!',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            Log::info('Update request received', [
                'product_id' => $product->id,
                'all_data' => $request->all()
            ]);

            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string|max:500',
                'price' => 'nullable|numeric|min:0',
                'price_for_sale' => 'nullable|numeric|min:0',
                'quantity' => 'nullable|integer|min:0',
                'supplier_id' => 'nullable|exists:suppliers,id'
            ]);

            if (isset($validatedData['quantity']) && $validatedData['quantity'] != $product->quantity) {
                $change = $validatedData['quantity'] - $product->quantity;
                StockHistory::create([
                    'product_id' => $product->id,
                    'quantity' => $change ?: 0,
                    'price' => $product->price ?: 0,
                    'price_for_sale' => $product->price_for_sale ?: 0
                ]);
            }

            // Update other fields
            $filteredData = array_filter($validatedData, function ($value) {
                return !is_null($value);
            });

            Log::info('Updating product with data', [
                'filtered_data' => $filteredData
            ]);

            if (!empty($filteredData)) {
                $product->fill($filteredData)->save();
            }

            $updatedProduct = Product::with('supplier')->find($product->id);
            
            Log::info('Product updated successfully', [
                'product_id' => $product->id,
                'updated_fields' => array_keys($filteredData)
            ]);

            return response()->json([
                'message' => 'Product updated successfully!',
                'product' => $updatedProduct
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        $product->update(['status_id' => 2]);
        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully!'
        ]);
    }

    public function uploadImage(Request $request, Product $product)
    {
        try {
            Log::info('Image upload request received', [
                'product_id' => $product->id,
                'has_image' => $request->hasFile('image'),
                'all_data' => $request->all(),
                'files' => $request->allFiles()
            ]);

            if (!$request->hasFile('image')) {
                throw new \Exception('No image file was uploaded');
            }

            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Delete old image if exists
            if ($product->image_path && $product->image_path !== '0') {
                Storage::disk('public')->delete($product->image_path);
            }
            
            // Store new image
            $imagePath = $request->file('image')->store('products', 'public');
            if (!$imagePath) {
                throw new \Exception('Failed to store image');
            }
            
            // Update product with new image path
            $product->image_path = $imagePath;
            $product->save();

            Log::info('Image uploaded successfully', [
                'product_id' => $product->id,
                'new_image_path' => $imagePath
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Image uploaded successfully!',
                'image_url' => Storage::url($imagePath)
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading image: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'files' => $request->allFiles()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }
}
