<?php

namespace App\Filament\Widgets;

use App\Domain\Invoice\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Chiffre d\'affaires mensuel';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    public ?string $filter = '12';

    protected function getFilters(): ?array
    {
        return [
            '6' => '6 derniers mois',
            '12' => '12 derniers mois',
            '24' => '24 derniers mois',
        ];
    }

    protected function getData(): array
    {
        $months = (int) $this->filter;

        $labels = [];
        $revenueData = [];
        $invoiceCountData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');

            $revenueData[] = (float) Invoice::where('status', 'paid')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('total');

            $invoiceCountData[] = Invoice::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'CA (XOF)',
                    'data' => $revenueData,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Factures créées',
                    'data' => $invoiceCountData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
