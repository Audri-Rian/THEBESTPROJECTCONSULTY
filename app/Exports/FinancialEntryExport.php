<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialEntryExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function title(): string
    {
        return 'Relatório Financeiro';
    }

    public function headings(): array
    {
        return [
            ['Relatório: ' . $this->reportData['metadata']['report_name']],
            ['Gerado em: ' . $this->reportData['metadata']['generated_at']],
            ['Total de registros: ' . $this->reportData['metadata']['total_records']],
            ['Total de receitas: R$ ' . number_format($this->reportData['metadata']['total_receitas'], 2, ',', '.')],
            ['Total de despesas: R$ ' . number_format($this->reportData['metadata']['total_despesas'], 2, ',', '.')],
            ['Saldo: R$ ' . number_format($this->reportData['metadata']['saldo'], 2, ',', '.')],
            [],
            ['ID', 'Nome', 'Descrição', 'Valor', 'Data', 'Categoria/Tipo', 'Tipo de Lançamento']
        ];
    }

    public function collection()
    {
        return collect($this->reportData['data'])->map(function ($item) {
            return [
                $item['id'],
                $item['nome'],
                $item['descricao'],
                'R$ ' . number_format($item['valor'], 2, ',', '.'),
                $item['data'],
                $item['tipo'] === 'Receita' ? ($item['categoria'] ?? 'N/A') : ($item['tipoDespesa'] ?? 'N/A'),
                $item['tipo']
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para o cabeçalho do relatório
        $sheet->getStyle('A1:A6')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ]);

        // Estilo para os cabeçalhos das colunas
        $sheet->getStyle('A8:G8')->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2E8F0'
                ]
            ]
        ]);

        // Estilo para os valores
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A9:G' . $lastRow)->applyFromArray([
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);

        // Ajusta largura das colunas
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [
            8 => ['font' => ['bold' => true]],
        ];
    }
} 