<?php

namespace App\Filament\Resources\SubscriptionResource\Widgets;

use App\Models\User;
use App\Services\PlanService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $clients = User::where('role', 'client');

        $starterCount = (clone $clients)->where('plan', 'starter')->count();
        $proCount = (clone $clients)->where('plan', 'pro')->count();
        $enterpriseCount = (clone $clients)->where('plan', 'enterprise')->count();
        $totalClients = $starterCount + $proCount + $enterpriseCount;

        // MRR (Monthly Recurring Revenue)
        $mrr = ($proCount * PlanService::PLANS['pro']['price'])
             + ($enterpriseCount * PlanService::PLANS['enterprise']['price']);

        $trialExpiring = (clone $clients)
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', now()->addDays(7))
            ->where('trial_ends_at', '>', now())
            ->count();

        return [
            Stat::make('Total abonnés', $totalClients)
                ->description("$starterCount Starter / $proCount Pro / $enterpriseCount Enterprise")
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('Revenu Mensuel (MRR)', number_format($mrr, 0, ',', ' ') . ' XOF')
                ->description('Basé sur les plans actifs')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Essais expirant', $trialExpiring)
                ->description('Dans les 7 prochains jours')
                ->icon('heroicon-o-clock')
                ->color($trialExpiring > 0 ? 'warning' : 'success'),
        ];
    }
}
