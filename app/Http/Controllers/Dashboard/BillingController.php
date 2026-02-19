<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    /**
     * Page de facturation / gestion du plan
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;
        $currentPlan = $user->plan ?? 'starter';

        // Statistiques d'utilisation courante
        $usage = $this->getCurrentUsage($user, $tenant);

        // Limites du plan
        $limits = PlanService::getPlan($currentPlan)['limits'] ?? [];

        // Trial info
        $isOnTrial = $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture();
        $trialDaysRemaining = PlanService::trialDaysRemaining($tenant);
        $isTrialExpired = PlanService::isTrialExpired($tenant);

        // Tous les plans
        $plans = PlanService::getAllPlans();

        return view('dashboard.billing.index', compact(
            'user',
            'tenant',
            'currentPlan',
            'usage',
            'limits',
            'isOnTrial',
            'trialDaysRemaining',
            'isTrialExpired',
            'plans'
        ));
    }

    /**
     * Mettre à niveau le plan
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:pro,enterprise',
        ]);

        $user = $request->user();
        $tenant = $user->tenant;
        $currentPlan = $user->plan ?? 'starter';
        $newPlan = $request->input('plan');

        // Vérifier que c'est bien un upgrade
        if (!PlanService::isUpgrade($currentPlan, $newPlan)) {
            return back()->with('error', 'Ce changement de plan n\'est pas un upgrade valide.');
        }

        try {
            DB::transaction(function () use ($user, $tenant, $newPlan) {
                // Mettre à jour le plan de l'utilisateur
                $user->update(['plan' => $newPlan]);

                // Mettre à jour le plan du tenant
                $tenant->update([
                    'plan' => $newPlan,
                    'trial_ends_at' => $tenant->trial_ends_at ?? now()->addDays(30),
                ]);
            });

            $planName = PlanService::getPlan($newPlan)['name'];

            Log::info('Plan upgraded', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'from' => $currentPlan,
                'to' => $newPlan,
            ]);

            return redirect()
                ->route('client.billing')
                ->with('success', "Votre plan a été mis à niveau vers {$planName} ! Profitez de toutes les nouvelles fonctionnalités.");

        } catch (\Exception $e) {
            Log::error('Plan upgrade failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la mise à niveau. Veuillez réessayer.');
        }
    }

    /**
     * Rétrograder le plan
     */
    public function downgrade(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:starter,pro',
        ]);

        $user = $request->user();
        $tenant = $user->tenant;
        $currentPlan = $user->plan ?? 'starter';
        $newPlan = $request->input('plan');

        // Vérifier que c'est bien un downgrade
        if (!PlanService::isDowngrade($currentPlan, $newPlan)) {
            return back()->with('error', 'Ce changement de plan n\'est pas un downgrade valide.');
        }

        // Vérifier les limites avant downgrade
        $usage = $this->getCurrentUsage($user, $tenant);
        $newLimits = PlanService::getPlan($newPlan)['limits'];
        $violations = [];

        if ($newLimits['clients'] !== -1 && ($usage['clients'] ?? 0) > $newLimits['clients']) {
            $violations[] = "Vous avez {$usage['clients']} clients, la limite du plan " . ucfirst($newPlan) . " est de {$newLimits['clients']}.";
        }

        if ($newLimits['products'] !== -1 && ($usage['products'] ?? 0) > $newLimits['products']) {
            $violations[] = "Vous avez {$usage['products']} produits, la limite du plan " . ucfirst($newPlan) . " est de {$newLimits['products']}.";
        }

        if (!empty($violations)) {
            return back()->with('error', 'Impossible de rétrograder : ' . implode(' ', $violations) . ' Veuillez d\'abord réduire votre utilisation.');
        }

        try {
            DB::transaction(function () use ($user, $tenant, $newPlan) {
                $user->update(['plan' => $newPlan]);
                $tenant->update(['plan' => $newPlan]);
            });

            $planName = PlanService::getPlan($newPlan)['name'];

            Log::info('Plan downgraded', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'from' => $currentPlan,
                'to' => $newPlan,
            ]);

            return redirect()
                ->route('client.billing')
                ->with('success', "Votre plan a été rétrogradé vers {$planName}. Le changement prendra effet à la fin de la période de facturation en cours.");

        } catch (\Exception $e) {
            Log::error('Plan downgrade failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors du changement de plan. Veuillez réessayer.');
        }
    }

    /**
     * Annuler l'abonnement (revenir au gratuit)
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;
        $currentPlan = $user->plan ?? 'starter';

        if ($currentPlan === 'starter') {
            return back()->with('error', 'Vous êtes déjà sur le plan gratuit.');
        }

        try {
            DB::transaction(function () use ($user, $tenant) {
                $user->update(['plan' => 'starter']);
                $tenant->update([
                    'plan' => 'starter',
                    'trial_ends_at' => null,
                ]);
            });

            Log::info('Subscription cancelled', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'from' => $currentPlan,
            ]);

            return redirect()
                ->route('client.billing')
                ->with('success', 'Votre abonnement a été annulé. Vous avez été rétrogradé au plan Starter gratuit.');

        } catch (\Exception $e) {
            Log::error('Subscription cancellation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de l\'annulation. Veuillez réessayer.');
        }
    }

    /**
     * Calculer l'utilisation courante du tenant
     */
    private function getCurrentUsage($user, $tenant): array
    {
        $tenantId = $tenant->id;

        $invoicesThisMonth = \App\Domain\Invoice\Models\Invoice::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalClients = \App\Domain\Client\Models\Client::where('tenant_id', $tenantId)->count();
        $totalProducts = \App\Models\Product::where('tenant_id', $tenantId)->count();

        return [
            'invoices_this_month' => $invoicesThisMonth,
            'clients' => $totalClients,
            'products' => $totalProducts,
        ];
    }
}
