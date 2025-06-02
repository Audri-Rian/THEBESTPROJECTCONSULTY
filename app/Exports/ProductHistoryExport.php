<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductHistoryExport implements FromCollection, WithHeadings, WithTitle
{
    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function title(): string
    {
        return 'Products History Report';
    }

    public function headings(): array
    {
        return [
            ['Relatório: Histórico de Produtos'],
            ['Gerado em: ' . $this->reportData['metadata']['generated_at']],
            [],
            ['ID', 'Nome do Produto', 'Quantidade', 'Preço', 'Tipo', 'Data']
        ];
    }

    public function collection()
    {
        return collect($this->reportData['data'])->map(function ($item) {
            return [
                $item['id'],
                $item['product_name'],
                $item['quantity'],
                $item['price'],
                $item['type'],
                $item['date']
            ];
        });
    }
}