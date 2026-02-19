<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\WebhookLog;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use App\Domain\Client\Models\Client;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActivityLogWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();

        // Nouveaux utilisateurs cette semaine
        $newUsersWeek = User::where('created_at', '>=', $thisWeek)->count();
        $newUsersToday = User::where('created_at', '>=', $today)->count();

        // Factures créées cette semaine
        $newInvoicesWeek = Invoice::where('created_at', '>=', $thisWeek)->count();
        $paidInvoicesWeek = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $thisWeek)
            ->count();

        // Paiements cette semaine
        $paymentsWeek = Payment::where('created_at', '>=', $thisWeek)->count();
        $successPaymentsWeek = Payment::where('status', 'success')
            ->where('created_at', '>=', $thisWeek)
            ->count();

        // Webhooks récents
        $webhooksToday = WebhookLog::where('created_at', '>=', $today)->count();
        $failedWebhooks = WebhookLog::where('processed', false)
            ->where('created_at', '>=', $thisWeek)
            ->count();

        // Nouveaux clients
        $newClientsWeek = Client::where('created_at', '>=', $thisWeek)->count();

        return [
            Stat::make('Inscriptions semaine', $newUsersWeek)
                ->description("$newUsersToday aujourd'hui")
                ->icon('heroicon-o-user-plus')
                ->color('info')
                ->chart(self::getWeeklyData(User::class)),
            Stat::make('Factures semaine', $newInvoicesWeek)
                ->description("$paidInvoicesWeek payées")
                ->icon('heroicon-o-document-check')
                ->color('success')
                ->chart(self::getWeeklyData(Invoice::class)),
            Stat::make('Nouveaux clients', $newClientsWeek)
                ->description('Cette semaine')
                ->icon('heroicon-o-building-storefront')
                ->color('primary'),
            Stat::make('Webhooks aujourd\'hui', $webhooksToday)
                ->description($failedWebhooks > 0 ? "$failedWebhooks échoués cette semaine" : 'Tous traités ✓')
                ->icon('heroicon-o-globe-alt')
                ->color($failedWebhooks > 0 ? 'danger' : 'success'),
        ];
    }

    /**
     * Obtenir les données hebdomadaires pour le mini-graphique
     */
    private static function getWeeklyData(string $modelClass): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = $modelClass::whereDate('created_at', $date->toDateString())->count();
        }
        return $data;
    }
}
