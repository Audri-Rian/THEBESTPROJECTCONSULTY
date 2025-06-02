<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;

class ListFinancialController extends Controller
{
    public function index()
    {
        try {
            // Carrega receitas com relacionamento + ordenação
            $incomes = Income::with('category')
                ->orderBy('date', 'DESC')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'tipo' => 'Receita',
                        'nome' => $item->name,
                        'descricao' => $item->description,
                        'valor' => (float) $item->amount,
                        'data' => $item->date,
                        'categoria' => $item->category?->name ?? 'Sem categoria'
                    ];
                });

            // Carrega despesas com relacionamento + ordenação
            $expenses = Expense::with('expenseType')
                ->orderBy('date', 'DESC')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'tipo' => 'Despesa',
                        'nome' => $item->name,
                        'descricao' => $item->description,
                        'valor' => (float) $item->amount * -1,
                        'data' => $item->date,
                        'tipoDespesa' => $item->expenseType?->name ?? 'Sem tipo'
                    ];
                });

            // Combina e mantém a ordenação correta
            $lancamentos = $incomes->merge($expenses)
                ->sortByDesc(function ($item) {
                    return strtotime($item['data']); // Ordena por timestamp
                })
                ->values();

            return response()->json($lancamentos);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }
    
}