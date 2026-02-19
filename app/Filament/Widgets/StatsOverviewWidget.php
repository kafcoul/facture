<?php

namespace App\Filament\Widgets;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Chiffre d'affaires total (factures payées)
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        $lastMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('total');
        $thisMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->sum('total');
        $revenueChange = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // Factures
        $totalInvoices = Invoice::count();
        $pendingInvoices = Invoice::whereIn('status', ['draft', 'sent', 'viewed'])->count();
        $overdueInvoices = Invoice::where('status', 'overdue')->count();

        // Clients
        $totalClients = Client::count();
        $newClientsThisMonth = Client::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Paiements ce mois
        $paymentsThisMonth = Payment::where('status', 'success')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        // Taux de paiement
        $paidCount = Invoice::where('status', 'paid')->count();
        $paymentRate = $totalInvoices > 0 ? round(($paidCount / $totalInvoices) * 100, 1) : 0;

        // Tendance CA sur 6 mois (pour le sparkline)
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenueChart[] = (float) Invoice::where('status', 'paid')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('total');
        }

        return [
            Stat::make('Chiffre d\'affaires', number_format($totalRevenue, 0, ',', ' ') . ' XOF')
                ->description($revenueChange >= 0 ? "+{$revenueChange}% vs mois dernier" : "{$revenueChange}% vs mois dernier")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart($revenueChart),

            Stat::make('Factures', $totalInvoices)
                ->description("{$pendingInvoices} en attente · {$overdueInvoices} en retard")
                ->descriptionIcon('heroicon-m-document-text')
                ->color($overdueInvoices > 0 ? 'warning' : 'primary'),

            Stat::make('Clients', $totalClients)
                ->description("+{$newClientsThisMonth} ce mois")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),

            Stat::make('Taux de paiement', $paymentRate . '%')
                ->description(number_format($paymentsThisMonth, 0, ',', ' ') . ' XOF ce mois')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($paymentRate >= 80 ? 'success' : ($paymentRate >= 50 ? 'warning' : 'danger')),
        ];
    }
}
