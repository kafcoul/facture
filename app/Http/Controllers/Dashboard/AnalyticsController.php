<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use App\Domain\Client\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', '30'); // Default 30 days
        
        $startDate = match($period) {
            '7' => now()->subDays(7),
            '30' => now()->subDays(30),
            '90' => now()->subDays(90),
            '365' => now()->subYear(),
            'all' => null,
            default => now()->subDays(30),
        };

        // Revenue Statistics
        $revenueStats = $this->getRevenueStats($user, $startDate);
        
        // Invoice Statistics
        $invoiceStats = $this->getInvoiceStats($user, $startDate);
        
        // Payment Statistics
        $paymentStats = $this->getPaymentStats($user, $startDate);
        
        // Client Statistics
        $clientStats = $this->getClientStats($user, $startDate);
        
        // Revenue Chart Data (monthly)
        $revenueChartData = $this->getRevenueChartData($user);
        
        // Top Clients
        $topClients = $this->getTopClients($user, $startDate);
        
        // Invoice Status Distribution
        $invoiceStatusData = $this->getInvoiceStatusDistribution($user, $startDate);

        return view('dashboard.analytics.index', compact(
            'period',
            'revenueStats',
            'invoiceStats',
            'paymentStats',
            'clientStats',
            'revenueChartData',
            'topClients',
            'invoiceStatusData'
        ));
    }

    /**
     * Get revenue statistics.
     */
    private function getRevenueStats($user, $startDate)
    {
        $query = Invoice::where('status', 'paid');
        
        if ($startDate) {
            $query->where('paid_at', '>=', $startDate);
        }

        $totalRevenue = (clone $query)->sum('total');
        
        // Previous period comparison
        $previousPeriodRevenue = 0;
        if ($startDate) {
            $daysDiff = now()->diffInDays($startDate);
            $previousStartDate = $startDate->copy()->subDays($daysDiff);
            $previousEndDate = $startDate->copy();
            
            $previousPeriodRevenue = Invoice::where('status', 'paid')
                ->whereBetween('paid_at', [$previousStartDate, $previousEndDate])
                ->sum('total');
        }
        
        $percentageChange = $previousPeriodRevenue > 0 
            ? (($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100 
            : 0;

        return [
            'total' => $totalRevenue,
            'previous' => $previousPeriodRevenue,
            'percentage_change' => round($percentageChange, 1),
            'average_invoice' => $query->count() > 0 ? $totalRevenue / $query->count() : 0,
        ];
    }

    /**
     * Get invoice statistics.
     */
    private function getInvoiceStats($user, $startDate)
    {
        $query = Invoice::query();
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $total = (clone $query)->count();
        $paid = (clone $query)->where('status', 'paid')->count();
        $pending = (clone $query)->whereIn('status', ['pending', 'sent', 'draft'])->count();
        $overdue = (clone $query)->where('status', 'overdue')->count();
        
        $paymentRate = $total > 0 ? ($paid / $total) * 100 : 0;
        
        // Average payment time
        $avgPaymentDays = Invoice::where('status', 'paid')
            ->whereNotNull('paid_at')
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->selectRaw('AVG(DATEDIFF(paid_at, created_at)) as avg_days')
            ->first()
            ->avg_days ?? 0;

        return [
            'total' => $total,
            'paid' => $paid,
            'pending' => $pending,
            'overdue' => $overdue,
            'payment_rate' => round($paymentRate, 1),
            'avg_payment_days' => round($avgPaymentDays),
        ];
    }

    /**
     * Get payment statistics.
     */
    private function getPaymentStats($user, $startDate)
    {
        $query = Payment::query();
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $totalPayments = (clone $query)->sum('amount');
        $paymentCount = (clone $query)->count();
        
        // Payment methods distribution
        $paymentMethods = (clone $query)
            ->select('gateway', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('gateway')
            ->get()
            ->mapWithKeys(fn($item) => [$item->gateway => [
                'total' => $item->total,
                'count' => $item->count
            ]]);

        return [
            'total' => $totalPayments,
            'count' => $paymentCount,
            'methods' => $paymentMethods,
        ];
    }

    /**
     * Get client statistics.
     */
    private function getClientStats($user, $startDate)
    {
        $query = Client::query();
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $totalClients = Client::count();
        $newClients = (clone $query)->count();
        
        // Active clients (with invoices in period)
        $activeClients = Client::whereHas('invoices', function ($q) use ($startDate) {
                if ($startDate) {
                    $q->where('created_at', '>=', $startDate);
                }
            })
            ->count();

        return [
            'total' => $totalClients,
            'new' => $newClients,
            'active' => $activeClients,
        ];
    }

    /**
     * Get revenue chart data for the last 12 months.
     */
    private function getRevenueChartData($user)
    {
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $revenue = Invoice::where('status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('total');
            
            $data[] = [
                'month' => $date->translatedFormat('M Y'),
                'revenue' => $revenue,
            ];
        }

        return $data;
    }

    /**
     * Get top clients by revenue.
     */
    private function getTopClients($user, $startDate, $limit = 5)
    {
        return Client::withSum(['invoices' => function ($query) use ($startDate) {
                $query->where('status', 'paid');
                if ($startDate) {
                    $query->where('paid_at', '>=', $startDate);
                }
            }], 'total')
            ->having('invoices_sum_total', '>', 0)
            ->orderByDesc('invoices_sum_total')
            ->take($limit)
            ->get();
    }

    /**
     * Get invoice status distribution.
     */
    private function getInvoiceStatusDistribution($user, $startDate)
    {
        $query = Invoice::query();
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return $query
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => [
                'count' => $item->count,
                'total' => $item->total
            ]]);
    }
}
