<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use App\Domain\Client\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;
        
        // Check if using tenant system or user-based
        $tenantId = $user->tenant_id ?? null;
        
        // Build base queries
        $invoiceQuery = Invoice::query();
        $paymentQuery = Payment::query();
        $clientQuery = Client::query();
        
        if ($tenantId) {
            $invoiceQuery->where('tenant_id', $tenantId);
            $paymentQuery->where('tenant_id', $tenantId);
            $clientQuery->where('tenant_id', $tenantId);
        } else {
            $invoiceQuery->where('user_id', $userId);
            $paymentQuery->whereHas('invoice', fn($q) => $q->where('user_id', $userId));
            $clientQuery->where('user_id', $userId);
        }

        // Invoices this month
        $invoicesThisMonth = (clone $invoiceQuery)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Statistiques
        $stats = [
            'invoices_count' => (clone $invoiceQuery)->count(),
            'invoices_this_month' => $invoicesThisMonth,
            'pending_count' => (clone $invoiceQuery)
                ->whereIn('status', ['draft', 'sent', 'pending', 'viewed'])
                ->count(),
            'overdue_count' => (clone $invoiceQuery)
                ->where('status', 'overdue')
                ->count(),
            'paid_count' => (clone $invoiceQuery)
                ->where('status', 'paid')
                ->count(),
            'total_amount' => (clone $invoiceQuery)
                ->where('status', 'paid')
                ->sum('total'),
            'total_clients' => (clone $clientQuery)->count(),
            'payment_rate' => $this->calculatePaymentRate($invoiceQuery),
            'avg_payment_days' => $this->calculateAvgPaymentDays($invoiceQuery),
        ];

        // Factures récentes
        $recentInvoices = (clone $invoiceQuery)
            ->with('client')
            ->latest()
            ->take(5)
            ->get();

        // Paiements récents
        $recentPayments = (clone $paymentQuery)
            ->with('invoice')
            ->where('status', 'completed')
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentInvoices', 'recentPayments'));
    }
    
    /**
     * Calculate payment rate percentage
     */
    private function calculatePaymentRate($query)
    {
        $total = (clone $query)->count();
        if ($total === 0) return 0;
        
        $paid = (clone $query)->where('status', 'paid')->count();
        return round(($paid / $total) * 100);
    }
    
    /**
     * Calculate average payment days
     */
    private function calculateAvgPaymentDays($query)
    {
        $avgDays = (clone $query)
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->get()
            ->avg(function ($invoice) {
                $created = Carbon::parse($invoice->created_at);
                $paid = Carbon::parse($invoice->paid_at);
                return $created->diffInDays($paid);
            });
        
        return round($avgDays ?? 0);
    }
}
