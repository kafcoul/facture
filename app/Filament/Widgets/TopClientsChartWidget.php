<?php

namespace App\Filament\Widgets;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopClientsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Top 10 clients (CA)';

    protected static ?int $sort = 5;

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $topClients = Invoice::where('status', 'paid')
            ->select('client_id', DB::raw('SUM(total) as total_revenue'))
            ->groupBy('client_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->with('client')
            ->get();

        $labels = $topClients->map(fn ($item) => $item->client?->name ?? 'Inconnu')->toArray();
        $data = $topClients->map(fn ($item) => (float) $item->total_revenue)->toArray();

        $colors = [
            'rgb(245, 158, 11)',
            'rgb(59, 130, 246)',
            'rgb(34, 197, 94)',
            'rgb(239, 68, 68)',
            'rgb(139, 92, 246)',
            'rgb(236, 72, 153)',
            'rgb(14, 165, 233)',
            'rgb(249, 115, 22)',
            'rgb(168, 85, 247)',
            'rgb(20, 184, 166)',
        ];

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
