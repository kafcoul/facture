<?php

namespace App\Filament\Widgets;

use App\Domain\Invoice\Models\Invoice;
use Filament\Widgets\ChartWidget;

class InvoiceStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition des factures';

    protected static ?int $sort = 3;

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $statuses = [
            'draft' => ['label' => 'Brouillon', 'color' => 'rgb(156, 163, 175)'],
            'sent' => ['label' => 'Envoyée', 'color' => 'rgb(59, 130, 246)'],
            'viewed' => ['label' => 'Vue', 'color' => 'rgb(139, 92, 246)'],
            'paid' => ['label' => 'Payée', 'color' => 'rgb(34, 197, 94)'],
            'overdue' => ['label' => 'En retard', 'color' => 'rgb(239, 68, 68)'],
            'cancelled' => ['label' => 'Annulée', 'color' => 'rgb(107, 114, 128)'],
            'partially_paid' => ['label' => 'Partiellement payée', 'color' => 'rgb(245, 158, 11)'],
        ];

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($statuses as $status => $config) {
            $count = Invoice::where('status', $status)->count();
            if ($count > 0) {
                $labels[] = $config['label'];
                $data[] = $count;
                $colors[] = $config['color'];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }
}
