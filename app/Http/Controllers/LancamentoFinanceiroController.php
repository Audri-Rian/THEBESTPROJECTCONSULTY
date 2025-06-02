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

    public function getIncomes()
    {
        $incomes = Income::with('category')
            ->select(
                'id',
                'name',
                'description',
                'amount',
                'date',
                'categories_id'
            )->get()
            ->map(function ($income) {
                $income->type = 'income';
                return $income;
            });

        return response()->json($incomes);
    }

    public function getExpenses()
    {
        $expenses = Expense::with(['category', 'expenseType'])
            ->select(
                'id',
                'name',
                'description',
                'amount',
                'date',
                'categories_id',
                'expense_types_id'
            )->get()
            ->map(function ($expense) {
                $expense->type = 'expense';
                return $expense;
            });

        return response()->json($expenses);
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
    public function updateIncome(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'categories_id' => 'required|exists:categories,id'
        ]);

        $income = Income::findOrFail($id);
        $income->update($validated);

        return back()->with('success', 'Receita atualizada com sucesso!');
    }

    public function updateExpense(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'expense_types_id' => 'required|exists:expense_types,id'
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update($validated);

        return back()->with('success', 'Despesa atualizada com sucesso!');
    }

    public function destroyIncome($id)
    {
        try {
            Income::findOrFail($id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyExpense($id)
    {
        try {
            Expense::findOrFail($id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir: ' . $e->getMessage()
            ], 500);
        }
    }

public function searchEntries(Request $request)
{
    try {
        $search = $request->input('search', '');

        $incomes = Income::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->get()
            ->map(function ($income) {
                return [
                    'id' => $income->id,
                    'nome' => $income->name,
                    'descricao' => $income->description,
                    'valor' => (float) ($income->amount ?? 0),
                    'data' => $income->date,
                    'categoria_id' => $income->categories_id,
                    'categoria' => optional($income->category)->name,
                    'tipo' => 'Receita',
                ];
            });

        $expenses = Expense::with('expenseType')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->get()
            ->map(function($expense){
                return [    
                    'id' => $expense->id,
                    'nome' => $expense->name,
                    'descricao' => $expense->description,
                    'valor' => (float) ($expense->amount ?? 0) * -1,
                    'data' => $expense->date,
                    'tipoDespesa_id' => $expense->expense_types_id,
                    'tipoDespesa' => optional($expense->expenseType)->name,
                    'tipo' => 'Despesa',
                ];
            });
        
        $results = collect($incomes)
            ->merge($expenses)
            ->sortByDesc('data')
            ->values();

        return response()->json($results);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erro interno',
            'message' => $e->getMessage()
        ], 500);
    }
}
}