<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Income;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class LancamentoFinanceiroController extends Controller
{
    public function index()
    {
        return Inertia::render('LancamentoFinanceiro');
    }

    // Métodos para Receitas
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

    // Métodos para Categorias
    public function CategoryList()
    {
        return Category::all();
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        Category::create($validated);

        return back()->with('success', 'Categoria criada com sucesso!');
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Categoria excluída com sucesso']);
    }

    // Métodos para Despesas
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'categories_id' => 'required|exists:categories,id',
            'expense_types_id' => 'required|exists:expense_types,id'
        ]);

        Expense::create($validated);

        return back()->with('success', 'Despesa adicionada com sucesso!');
    }

    // Métodos para Tipos de Despesa
    public function expenseTypeList()
    {
        return ExpenseType::all();
    }

    public function storeExpenseType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:100',
        ]);

        ExpenseType::create($validated);

        return back()->with('success', 'Tipo de despesa criado com sucesso!');
    }

    public function destroyExpenseType($id)
    {
        $type = ExpenseType::findOrFail($id);
        $type->delete();

        return response()->json(['message' => 'Tipo de despesa excluído com sucesso']);
    }
}