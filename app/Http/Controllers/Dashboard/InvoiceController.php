<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Invoice\Models\InvoiceItem;
use App\Domain\Client\Models\Client;
use App\Models\Product;
use App\Http\Requests\StoreInvoiceRequest;
use App\Services\InvoiceNumberService;
use App\Services\InvoiceCalculatorService;
use App\Services\PdfService;
use App\Domain\Invoice\Events\InvoiceCreated;
use App\Jobs\SendInvoiceEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        $user = auth()->user();
        $query = Invoice::where('tenant_id', $user->tenant_id)
            ->with('client');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->latest()->paginate(15);

        return view('dashboard.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['client', 'items', 'payments']);

        return view('dashboard.invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['client', 'items.product', 'user', 'payments']);

        try {
            $pdfService = new PdfService();
            $pdf = $pdfService->generateInvoicePdf($invoice);

            $filename = 'facture-' . ($invoice->number ?? $invoice->id) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('create', Invoice::class);

        $user = auth()->user();
        
        $clients = Client::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'company']);

        $products = Product::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'unit_price', 'tax_rate']);

        return view('dashboard.invoices.create', compact('clients', 'products'));
    }

    public function store(StoreInvoiceRequest $request, InvoiceNumberService $numberService, InvoiceCalculatorService $calculator)
    {
        $this->authorize('create', Invoice::class);

        $user = auth()->user();

        try {
            DB::beginTransaction();

            $invoiceNumber = $numberService->generate($user->tenant_id);

            $invoice = Invoice::create([
                'tenant_id' => $user->tenant_id,
                'user_id' => $user->id,
                'client_id' => $request->client_id,
                'number' => $invoiceNumber,
                'issued_at' => $request->issue_date,
                'due_date' => $request->due_date,
                'status' => 'draft',
                'notes' => $request->notes,
                'terms' => $request->terms,
                'discount_percentage' => $request->discount_percentage ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
            ]);

            foreach ($request->items as $item) {
                $description = $item['description'] ?? '';
                if (empty($description) && !empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    $description = $product ? $product->name : 'Article';
                }
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $description ?: 'Article',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            $this->recalculateTotals($invoice);

            DB::commit();

            return redirect()
                ->route('client.invoices.show', $invoice)
                ->with('success', 'La facture a été créée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la facture : ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $user = auth()->user();

        $invoice->load(['client', 'items']);

        $clients = Client::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'company']);

        $products = Product::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'unit_price', 'tax_rate']);

        return view('dashboard.invoices.edit', compact('invoice', 'clients', 'products'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(StoreInvoiceRequest $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        try {
            DB::beginTransaction();

            $invoice->update([
                'client_id' => $request->client_id,
                'issued_at' => $request->issue_date,
                'due_date' => $request->due_date,
                'notes' => $request->notes,
                'terms' => $request->terms,
                'discount_percentage' => $request->discount_percentage ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
            ]);

            // Supprimer les anciens items et recréer
            $invoice->items()->delete();

            foreach ($request->items as $item) {
                $description = $item['description'] ?? '';
                if (empty($description) && !empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    $description = $product ? $product->name : 'Article';
                }
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $description ?: 'Article',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            $this->recalculateTotals($invoice);

            DB::commit();

            return redirect()
                ->route('client.invoices.show', $invoice)
                ->with('success', 'La facture a été mise à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified invoice (draft only).
     */
    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);

        $invoice->items()->delete();
        $invoice->delete();

        return redirect()
            ->route('client.invoices.index')
            ->with('success', 'La facture a été supprimée.');
    }

    /**
     * Send the invoice by email to the client.
     */
    public function send(Invoice $invoice)
    {
        $this->authorize('send', $invoice);

        $invoice->load('client');

        if (!$invoice->client || !$invoice->client->email) {
            return back()->with('error', 'Le client n\'a pas d\'adresse email.');
        }

        // Mettre à jour le statut
        if ($invoice->status === 'draft') {
            $invoice->update(['status' => 'sent', 'issued_at' => $invoice->issued_at ?? now()]);
        }

        // Dispatcher le job d'envoi d'email
        SendInvoiceEmailJob::dispatch($invoice);

        return back()->with('success', 'La facture a été envoyée à ' . $invoice->client->email);
    }

    /**
     * Duplicate an existing invoice.
     */
    public function duplicate(Invoice $invoice, InvoiceNumberService $numberService)
    {
        $this->authorize('duplicate', $invoice);

        $user = auth()->user();

        try {
            DB::beginTransaction();

            $newInvoice = $invoice->replicate([
                'uuid', 'public_hash', 'number', 'status', 'paid_at', 'pdf_path',
            ]);
            $newInvoice->number = $numberService->generate($user->tenant_id);
            $newInvoice->status = 'draft';
            $newInvoice->issued_at = now();
            $newInvoice->due_date = now()->addDays(30);
            $newInvoice->paid_at = null;
            $newInvoice->pdf_path = null;
            $newInvoice->save();

            // Dupliquer les items
            foreach ($invoice->items as $item) {
                $newItem = $item->replicate();
                $newItem->invoice_id = $newInvoice->id;
                $newItem->save();
            }

            DB::commit();

            return redirect()
                ->route('client.invoices.edit', $newInvoice)
                ->with('success', 'La facture a été dupliquée. Vous pouvez la modifier.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la duplication : ' . $e->getMessage());
        }
    }

    /**
     * Change the status of an invoice.
     */
    public function changeStatus(Request $request, Invoice $invoice)
    {
        $this->authorize('changeStatus', $invoice);

        $validated = $request->validate([
            'status' => 'required|in:draft,sent,viewed,paid,overdue,cancelled',
        ]);

        $oldStatus = $invoice->status;
        $newStatus = $validated['status'];

        // Règles de transition
        $allowedTransitions = [
            'draft' => ['sent', 'cancelled'],
            'sent' => ['viewed', 'paid', 'overdue', 'cancelled'],
            'viewed' => ['paid', 'overdue', 'cancelled'],
            'overdue' => ['paid', 'sent', 'cancelled'],
            'cancelled' => ['draft'],
            'paid' => [], // On ne change pas une facture payée facilement
        ];

        if (!in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
            return back()->with('error', "Transition de statut invalide : {$oldStatus} → {$newStatus}");
        }

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'paid') {
            $updateData['paid_at'] = now();
        }

        $invoice->update($updateData);

        $statusLabels = [
            'draft' => 'Brouillon',
            'sent' => 'Envoyée',
            'viewed' => 'Vue',
            'paid' => 'Payée',
            'overdue' => 'En retard',
            'cancelled' => 'Annulée',
        ];

        return back()->with('success', 'Statut mis à jour : ' . ($statusLabels[$newStatus] ?? $newStatus));
    }

    /**
     * Recalculate invoice totals.
     */
    private function recalculateTotals(Invoice $invoice): void
    {
        $invoice->load('items');

        $subtotal = 0;
        $taxAmount = 0;

        foreach ($invoice->items as $item) {
            $itemTotal = $item->quantity * $item->unit_price;
            $subtotal += $itemTotal;
            $taxAmount += $itemTotal * ($item->tax_rate / 100);
        }

        $discountAmount = $invoice->discount_amount ?? 0;
        if ($invoice->discount_percentage) {
            $discountAmount = $subtotal * ($invoice->discount_percentage / 100);
        }

        $total = $subtotal + $taxAmount - $discountAmount;

        $invoice->update([
            'subtotal' => $subtotal,
            'tax' => $taxAmount,
            'discount' => $discountAmount,
            'total' => $total,
        ]);
    }

    /**
     * API pour autocomplete des clients
     */
    public function searchClients(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('q', '');

        $clients = Client::where('tenant_id', $user->tenant_id)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'company', 'address', 'city', 'country']);

        return response()->json($clients);
    }

    /**
     * API pour autocomplete des produits
     */
    public function searchProducts(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('q', '');

        $products = Product::where('tenant_id', $user->tenant_id)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'description', 'unit_price', 'tax_rate']);

        return response()->json($products);
    }
}
