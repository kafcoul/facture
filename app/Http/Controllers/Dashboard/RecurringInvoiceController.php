<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Domain\Invoice\Models\RecurringInvoice;
use App\Domain\Client\Models\Client;
use Illuminate\Http\Request;

class RecurringInvoiceController extends Controller
{
    /**
     * Display a listing of recurring invoices.
     */
    public function index(Request $request)
    {
        $recurringInvoices = RecurringInvoice::query()
            ->with('client')
            ->withCount('invoices')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('client', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.recurring-invoices.index', compact('recurringInvoices'));
    }

    /**
     * Show the form for creating a new recurring invoice.
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $frequencies = RecurringInvoice::FREQUENCIES;

        return view('dashboard.recurring-invoices.create', compact('clients', 'frequencies'));
    }

    /**
     * Store a newly created recurring invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'frequency' => 'required|in:' . implode(',', array_keys(RecurringInvoice::FREQUENCIES)),
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'occurrences_limit' => 'nullable|integer|min:1',
            'auto_send' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:1000',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'frequency.required' => 'Veuillez sélectionner une fréquence.',
            'start_date.required' => 'La date de début est requise.',
            'items.required' => 'Ajoutez au moins une ligne.',
        ]);

        $items = collect($validated['items'])->map(function ($item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            return $item;
        })->toArray();

        $subtotal = collect($items)->sum('total');
        $tax = 0;
        $total = $subtotal + $tax;

        $recurringInvoice = RecurringInvoice::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'client_id' => $validated['client_id'],
            'frequency' => $validated['frequency'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'next_due_date' => $validated['start_date'],
            'occurrences_limit' => $validated['occurrences_limit'] ?? null,
            'occurrences_count' => 0,
            'auto_send' => $request->boolean('auto_send'),
            'is_active' => true,
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'currency' => 'XOF',
            'notes' => $validated['notes'] ?? null,
            'terms' => $validated['terms'] ?? null,
        ]);

        return redirect()
            ->route('client.recurring-invoices.show', $recurringInvoice)
            ->with('success', 'Facture récurrente créée avec succès.');
    }

    /**
     * Display the specified recurring invoice.
     */
    public function show(RecurringInvoice $recurringInvoice)
    {
        $this->authorize('view', $recurringInvoice);

        $recurringInvoice->load(['client', 'user', 'invoices' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(10);
        }]);

        return view('dashboard.recurring-invoices.show', compact('recurringInvoice'));
    }

    /**
     * Show the form for editing the specified recurring invoice.
     */
    public function edit(RecurringInvoice $recurringInvoice)
    {
        $this->authorize('update', $recurringInvoice);

        $clients = Client::orderBy('name')->get();
        $frequencies = RecurringInvoice::FREQUENCIES;

        return view('dashboard.recurring-invoices.edit', compact('recurringInvoice', 'clients', 'frequencies'));
    }

    /**
     * Update the specified recurring invoice.
     */
    public function update(Request $request, RecurringInvoice $recurringInvoice)
    {
        $this->authorize('update', $recurringInvoice);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'frequency' => 'required|in:' . implode(',', array_keys(RecurringInvoice::FREQUENCIES)),
            'end_date' => 'nullable|date|after:start_date',
            'occurrences_limit' => 'nullable|integer|min:1',
            'auto_send' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:1000',
        ]);

        $items = collect($validated['items'])->map(function ($item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            return $item;
        })->toArray();

        $subtotal = collect($items)->sum('total');
        $total = $subtotal;

        $recurringInvoice->update([
            'client_id' => $validated['client_id'],
            'frequency' => $validated['frequency'],
            'end_date' => $validated['end_date'] ?? null,
            'occurrences_limit' => $validated['occurrences_limit'] ?? null,
            'auto_send' => $request->boolean('auto_send'),
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $total,
            'notes' => $validated['notes'] ?? null,
            'terms' => $validated['terms'] ?? null,
        ]);

        return redirect()
            ->route('client.recurring-invoices.show', $recurringInvoice)
            ->with('success', 'Facture récurrente mise à jour avec succès.');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(RecurringInvoice $recurringInvoice)
    {
        $this->authorize('update', $recurringInvoice);

        $recurringInvoice->update(['is_active' => !$recurringInvoice->is_active]);

        $status = $recurringInvoice->is_active ? 'activée' : 'désactivée';

        return redirect()
            ->route('client.recurring-invoices.show', $recurringInvoice)
            ->with('success', "Facturation récurrente {$status}.");
    }

    /**
     * Remove the specified recurring invoice.
     */
    public function destroy(RecurringInvoice $recurringInvoice)
    {
        $this->authorize('delete', $recurringInvoice);

        $recurringInvoice->delete();

        return redirect()
            ->route('client.recurring-invoices.index')
            ->with('success', 'Facture récurrente supprimée avec succès.');
    }
}
