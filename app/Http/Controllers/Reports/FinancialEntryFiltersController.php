<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Exports\FinancialEntryExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class FinancialEntryFiltersController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->input('search');

        if (!$search) {
            return response()->json([]);
        }

        $incomes = Income::with('category')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->get()
            ->map(function ($income) {
                return [
                    'id' => $income->id,
                    'nome' => $income->name,
                    'descricao' => $income->description,
                    'valor' => (float) $income->amount,
                    'data' => Carbon::parse($income->date)->format('d/m/Y'),
                    'categoria' => optional($income->category)->name,
                    'tipo' => 'Receita'
                ];
            });

        $expenses = Expense::with('expenseType')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'nome' => $expense->name,
                    'descricao' => $expense->description,
                    'valor' => (float) ($expense->amount * -1),
                    'data' => Carbon::parse($expense->date)->format('d/m/Y'),
                    'tipoDespesa' => optional($expense->expenseType)->name,
                    'tipo' => 'Despesa'
                ];
            });

        $results = $incomes->concat($expenses)
            ->sortByDesc('data')
            ->values()
            ->take(10); // Limita a 10 resultados para o dropdown

        return response()->json($results);
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf,json',
            'entryId' => 'nullable|integer'
        ]);

        $query = collect();

        if ($request->entryId) {
            // Busca específica por ID
            $income = Income::with('category')->find($request->entryId);
            $expense = Expense::with('expenseType')->find($request->entryId);

            if ($income) {
                $query->push($this->formatIncome($income));
            } elseif ($expense) {
                $query->push($this->formatExpense($expense));
            }
        } else {
            // Busca todos os lançamentos
            $incomes = Income::with('category')->get()->map(function ($income) {
                return $this->formatIncome($income);
            });

            $expenses = Expense::with('expenseType')->get()->map(function ($expense) {
                return $this->formatExpense($expense);
            });

            $query = $incomes->concat($expenses)->sortByDesc('data');
        }

        $reportData = [
            'metadata' => [
                'report_name' => 'Relatório Financeiro',
                'generated_at' => now()->format('d/m/Y H:i:s'),
                'filters' => [
                    'entry_id' => $request->entryId ?? 'Todos'
                ],
                'total_records' => $query->count(),
                'total_receitas' => $query->where('tipo', 'Receita')->sum('valor'),
                'total_despesas' => abs($query->where('tipo', 'Despesa')->sum('valor')),
                'saldo' => $query->sum('valor')
            ],
            'data' => $query->values()
        ];

        switch ($request->format) {
            case 'xlsx':
                return Excel::download(
                    new FinancialEntryExport($reportData),
                    'lancamentos-financeiros.xlsx'
                );

            case 'csv':
                return $this->exportCsv($reportData);

            case 'pdf':
                return $this->exportPdf($reportData);

            case 'json':
                return response()->json($reportData)
                    ->header('Content-Disposition', 'attachment; filename="lancamentos-financeiros.json"');
        }
    }

    private function formatIncome($income)
    {
        return [
            'id' => $income->id,
            'nome' => $income->name,
            'descricao' => $income->description,
            'valor' => (float) $income->amount,
            'data' => Carbon::parse($income->date)->format('d/m/Y'),
            'categoria' => optional($income->category)->name,
            'tipo' => 'Receita'
        ];
    }

    private function formatExpense($expense)
    {
        return [
            'id' => $expense->id,
            'nome' => $expense->name,
            'descricao' => $expense->description,
            'valor' => (float) ($expense->amount * -1),
            'data' => Carbon::parse($expense->date)->format('d/m/Y'),
            'tipoDespesa' => optional($expense->expenseType)->name,
            'tipo' => 'Despesa'
        ];
    }

    private function exportCsv($reportData)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="lancamentos-financeiros.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Relatório: ' . $reportData['metadata']['report_name']]);
            fputcsv($file, ['Gerado em: ' . $reportData['metadata']['generated_at']]);
            fputcsv($file, ['Total de registros: ' . $reportData['metadata']['total_records']]);
            fputcsv($file, ['Total de receitas: R$ ' . number_format($reportData['metadata']['total_receitas'], 2, ',', '.')]);
            fputcsv($file, ['Total de despesas: R$ ' . number_format($reportData['metadata']['total_despesas'], 2, ',', '.')]);
            fputcsv($file, ['Saldo: R$ ' . number_format($reportData['metadata']['saldo'], 2, ',', '.')]);
            fputcsv($file, []); // Linha em branco
            
            // Cabeçalhos
            fputcsv($file, ['ID', 'Nome', 'Descrição', 'Valor', 'Data', 'Categoria/Tipo', 'Tipo de Lançamento']);
            
            // Dados
            foreach ($reportData['data'] as $row) {
                fputcsv($file, [
                    $row['id'],
                    $row['nome'],
                    $row['descricao'],
                    'R$ ' . number_format($row['valor'], 2, ',', '.'),
                    $row['data'],
                    $row['tipo'] === 'Receita' ? ($row['categoria'] ?? 'N/A') : ($row['tipoDespesa'] ?? 'N/A'),
                    $row['tipo']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPdf($reportData)
    {
        $pdf = PDF::loadView('reports.financial-entries', $reportData);
        return $pdf->download('lancamentos-financeiros.pdf');
    }
} 