<?php
namespace App\Services;

use App\Models\Cashbox;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Sale;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Builder;

class CashboxServices{
    protected $cashbox;

    public function __construct(Cashbox $cashbox, Sale $sale, Expense $expense, ProductPrice $productPrice){
        $this->cashbox = $cashbox;
        $this->sale = $sale;
        $this->expense = $expense;
        $this->productPrice = $productPrice;
    }

    //esperar audri terminar a migration de tipo de despesas para organizar tudo

    //a funcao calculateGrossProfit é para calcular o lucro bruto, ou seja, venda - custos.
    public function calculateGrossProfit(){
        $sales = Sale::sum('total_amount');
        $expenses = Expense::where('expense_type_id', 2)
        ->sum('amount'); // assumindo que o id 2 é para custos variáveis
        $grossProfit = $sales - $expenses;
        return $grossProfit;
    }

    //a funcao calculateNetProfit é para calcular o lucro liquido, ou seja, lucro bruto - despesas.
    public function calculateNetProfit(){
        $grossProfit = $this->calculateGrossProfit();
        $expenses = Expense::where('expense_type_id', 1)->sum('amount'); // assumindo que o id 1 é para despesas fixas
        $netProfit = $grossProfit - $expenses;
        return $netProfit;
    }

    public function calculateBreakEvenPoint($productid){
        $fixedCosts = Expense::where('expense_type_id', 1)
        ->sum('amount'); // assumindo que o id 1 é para custos fixos
        $unitPrice = ProductPrice::where('product_id', $productid)
        ->value('price');
        $variableCosts = Expense::where('expense_type_id', 2)
        ->where('product_id', $productid)
        ->sum('amount'); // assumindo que o id 2 é para custos variáveis

        if($unitPrice <= $variableCosts){
            return "Erro: O preço de venda deve ser maior que o custo variável.";
        }

        $breakEvenPoint = $fixedCosts / ($unitPrice - $variableCosts);
        return $breakEvenPoint;
    }

    public function calculateROI(){
        $netProfit = $this->calculateNetProfit();
        $investment = Expense::where('expense_type_id', 3) // assumindo que o id 3 é para investimentos
        ->sum('amount');

        if(!$investment || $investment == 0){
            return "Erro: O investimento não pode ser zero.";
        }

        $roi = ($netProfit / $investment) * 100;
        return $roi;
    }

    public function calculateAverageTicket(){
        $query = Sale::last30Days();

        $sales = $query->sum('total_amount');
        $allSales = $query()->count();

        if($allSales == 0){
            return "Erro: Não há vendas registradas.";
        }

        $avgTicket = $sales / $allSales;
        return $avgTicket;
    }
}