<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Product;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class Analytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Analytique';

    protected static ?string $navigationGroup = 'Analytique';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Analytique & Rapports';

    protected static ?string $slug = 'analytics';

    protected static string $view = 'filament.pages.analytics';

    public string $period = '12';

    public function getAnalyticsData(): array
    {
        $months = (int) $this->period;
        $startDate = now()->subMonths($months)->startOfMonth();

        // --- Chiffre d'Affaires mensuel ---
        $revenueByMonth = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month, SUM(total) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // --- Paiements par passerelle ---
        $paymentsByGateway = Payment::where('status', 'completed')
            ->where('completed_at', '>=', $startDate)
            ->selectRaw('gateway, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('gateway')
            ->get()
            ->toArray();

        // --- Top 10 Clients par CA ---
        $topClients = Client::withSum(['invoices' => function ($q) use ($startDate) {
            $q->where('status', 'paid')->where('paid_at', '>=', $startDate);
        }], 'total')
            ->orderByDesc('invoices_sum_total')
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'name' => $c->name,
                'total' => (float) ($c->invoices_sum_total ?? 0),
            ])
            ->toArray();

        // --- Top 10 Produits les plus facturés ---
        $topProducts = Product::withSum(['invoiceItems' => function ($q) use ($startDate) {
            $q->whereHas('invoice', fn ($iq) => $iq->where('created_at', '>=', $startDate));
        }], 'total')
            ->withCount(['invoiceItems' => function ($q) use ($startDate) {
                $q->whereHas('invoice', fn ($iq) => $iq->where('created_at', '>=', $startDate));
            }])
            ->orderByDesc('invoice_items_sum_total')
            ->limit(10)
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'total' => (float) ($p->invoice_items_sum_total ?? 0),
                'count' => $p->invoice_items_count,
            ])
            ->toArray();

        // --- KPIs ---
        $totalRevenue = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $startDate)
            ->sum('total');

        $totalInvoices = Invoice::where('created_at', '>=', $startDate)->count();
        $paidInvoices = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $startDate)->count();
        $overdueInvoices = Invoice::where('status', 'overdue')->count();
        $avgInvoice = $paidInvoices > 0 ? $totalRevenue / $paidInvoices : 0;

        $prevStartDate = $startDate->copy()->subMonths($months);
        $prevRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$prevStartDate, $startDate])
            ->sum('total');
        $revenueGrowth = $prevRevenue > 0 ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        $paymentRate = $totalInvoices > 0 ? ($paidInvoices / $totalInvoices) * 100 : 0;

        // --- Délai moyen de paiement ---
        $avgPaymentDays = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $startDate)
            ->whereNotNull('issued_at')
            ->whereNotNull('paid_at')
            ->selectRaw('AVG(DATEDIFF(paid_at, issued_at)) as avg_days')
            ->value('avg_days') ?? 0;

        return [
            'revenueByMonth' => $revenueByMonth,
            'paymentsByGateway' => $paymentsByGateway,
            'topClients' => $topClients,
            'topProducts' => $topProducts,
            'kpis' => [
                'totalRevenue' => $totalRevenue,
                'totalInvoices' => $totalInvoices,
                'paidInvoices' => $paidInvoices,
                'overdueInvoices' => $overdueInvoices,
                'avgInvoice' => $avgInvoice,
                'revenueGrowth' => round($revenueGrowth, 1),
                'paymentRate' => round($paymentRate, 1),
                'avgPaymentDays' => round($avgPaymentDays, 0),
            ],
        ];
    }
}
