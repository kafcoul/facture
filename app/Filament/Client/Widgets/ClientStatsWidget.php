<?php

namespace App\Filament\Client\Widgets;

use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenantId = auth()->user()?->tenant_id;

        $totalInvoices = Invoice::where('tenant_id', $tenantId)->count();
        $unpaidInvoices = Invoice::where('tenant_id', $tenantId)
            ->whereIn('status', ['sent', 'viewed', 'overdue'])
            ->count();
        $totalPaid = Payment::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->sum('amount');
        $overdueInvoices = Invoice::where('tenant_id', $tenantId)
            ->where('status', 'overdue')
            ->count();

        return [
            Stat::make('Total Factures', $totalInvoices)
                ->description('Toutes vos factures')
                ->icon('heroicon-o-document-text')
                ->color('primary'),
            Stat::make('Factures Impayées', $unpaidInvoices)
                ->description('En attente de paiement')
                ->icon('heroicon-o-clock')
                ->color($unpaidInvoices > 0 ? 'warning' : 'success'),
            Stat::make('Total Payé', number_format($totalPaid, 0, ',', ' ') . ' XOF')
                ->description('Montant total réglé')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Factures en Retard', $overdueInvoices)
                ->description('À régler rapidement')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($overdueInvoices > 0 ? 'danger' : 'success'),
        ];
    }
}
