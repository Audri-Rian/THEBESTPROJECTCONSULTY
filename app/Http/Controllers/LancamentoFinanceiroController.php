<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Income;
use App\Models\Cashbox;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class LancamentoFinanceiroController extends Controller
{
    public function index()
    {
        return Inertia::render('LancamentoFinanceiro');
    }

    public function storeRevenue(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'categories_id' => 'required|exists:categories,id'
        ]);
    
        Income::create($validated);
    
        return back()->with('success', 'Receita adicionada com sucesso!');
    }

    public function CategoryList()
{
    $categories = Category::all();
    \Log::info('Categorias retornadas:', ['count' => count($categories)]);
    return $categories;
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        Category::create($validated);

        return back()->with('success', 'Categoria criada com sucesso!');
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Categoria excluída com sucesso']);
    }

    
}