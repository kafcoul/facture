<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Services\CsvExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(
        private CsvExportService $csvService
    ) {}

    /**
     * Export clients to CSV
     */
    public function clients(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $query = Client::where('tenant_id', $user->tenant_id)
            ->with('invoices')
            ->orderBy('name');

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $columns = [
            'name' => 'Nom',
            'company' => 'Entreprise',
            'email' => 'Email',
            'phone' => 'Téléphone',
            'city' => 'Ville',
            'country' => 'Pays',
            'tax_id' => 'Numéro fiscal',
            'currency' => 'Devise',
            'is_active' => 'Actif',
            'created_at' => 'Date de création',
        ];

        return $this->csvService->export($query, $columns, 'clients_' . date('Y-m-d') . '.csv', function ($client) {
            return [
                $client->name,
                $client->company,
                $client->email,
                $client->phone,
                $client->city,
                $client->country,
                $client->tax_id,
                $client->currency ?? 'XOF',
                $client->is_active ? 'Oui' : 'Non',
                $client->created_at?->format('d/m/Y'),
            ];
        });
    }

    /**
     * Export invoices to CSV
     */
    public function invoices(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $query = Invoice::where('tenant_id', $user->tenant_id)
            ->with(['client', 'items'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->where('issued_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('issued_at', '<=', $request->to);
        }

        $columns = [
            'number' => 'Numéro',
            'client.name' => 'Client',
            'status' => 'Statut',
            'issued_at' => 'Date émission',
            'due_date' => 'Date échéance',
            'subtotal' => 'Sous-total HT',
            'tax' => 'TVA',
            'discount' => 'Remise',
            'total' => 'Total TTC',
            'currency' => 'Devise',
            'paid_at' => 'Date paiement',
            'items_count' => 'Nb lignes',
        ];

        $statusLabels = [
            'draft' => 'Brouillon',
            'sent' => 'Envoyée',
            'viewed' => 'Vue',
            'paid' => 'Payée',
            'overdue' => 'En retard',
            'cancelled' => 'Annulée',
            'partially_paid' => 'Partiel',
        ];

        return $this->csvService->export($query, $columns, 'factures_' . date('Y-m-d') . '.csv', function ($invoice) use ($statusLabels) {
            return [
                $invoice->number,
                $invoice->client?->name ?? '—',
                $statusLabels[$invoice->status] ?? $invoice->status,
                $invoice->issued_at?->format('d/m/Y'),
                $invoice->due_date?->format('d/m/Y'),
                number_format((float) $invoice->subtotal, 0, ',', ' '),
                number_format((float) $invoice->tax, 0, ',', ' '),
                number_format((float) $invoice->discount, 0, ',', ' '),
                number_format((float) $invoice->total, 0, ',', ' '),
                $invoice->currency ?? 'XOF',
                $invoice->paid_at?->format('d/m/Y'),
                $invoice->items->count(),
            ];
        });
    }

    /**
     * Export products to CSV
     */
    public function products(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $query = Product::where('tenant_id', $user->tenant_id)
            ->orderBy('name');

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $columns = [
            'name' => 'Nom',
            'sku' => 'SKU',
            'description' => 'Description',
            'unit_price' => 'Prix unitaire',
            'price' => 'Prix catalogue',
            'tax_rate' => 'TVA (%)',
            'unit' => 'Unité',
            'is_active' => 'Actif',
            'created_at' => 'Date de création',
        ];

        return $this->csvService->export($query, $columns, 'produits_' . date('Y-m-d') . '.csv', function ($product) {
            return [
                $product->name,
                $product->sku,
                $product->description,
                number_format((float) $product->unit_price, 0, ',', ' '),
                $product->price ? number_format((float) $product->price, 0, ',', ' ') : '',
                $product->tax_rate,
                $product->unit ?? 'unité',
                $product->is_active ? 'Oui' : 'Non',
                $product->created_at?->format('d/m/Y'),
            ];
        });
    }

    /**
     * Export payments to CSV
     */
    public function payments(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $query = Payment::where('tenant_id', $user->tenant_id)
            ->with(['invoice.client'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        $columns = [
            'invoice.number' => 'Facture',
            'invoice.client.name' => 'Client',
            'amount' => 'Montant',
            'currency' => 'Devise',
            'gateway' => 'Passerelle',
            'payment_method' => 'Méthode',
            'transaction_id' => 'ID Transaction',
            'status' => 'Statut',
            'completed_at' => 'Date paiement',
            'created_at' => 'Date création',
        ];

        $gatewayLabels = [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'orange_money' => 'Orange Money',
            'mtn_money' => 'MTN Money',
            'wave' => 'Wave',
            'bank_transfer' => 'Virement',
            'cash' => 'Espèces',
            'check' => 'Chèque',
        ];

        $statusLabels = [
            'pending' => 'En attente',
            'completed' => 'Complété',
            'failed' => 'Échoué',
        ];

        return $this->csvService->export($query, $columns, 'paiements_' . date('Y-m-d') . '.csv', function ($payment) use ($gatewayLabels, $statusLabels) {
            return [
                $payment->invoice?->number ?? '—',
                $payment->invoice?->client?->name ?? '—',
                number_format((float) $payment->amount, 0, ',', ' '),
                $payment->currency ?? 'XOF',
                $gatewayLabels[$payment->gateway] ?? $payment->gateway ?? '—',
                $payment->payment_method ?? '—',
                $payment->transaction_id ?? '—',
                $statusLabels[$payment->status] ?? $payment->status,
                $payment->completed_at?->format('d/m/Y H:i'),
                $payment->created_at?->format('d/m/Y'),
            ];
        });
    }
}
