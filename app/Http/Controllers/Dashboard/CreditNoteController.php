<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Domain\Invoice\Models\CreditNote;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Client\Models\Client;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    /**
     * Display a listing of credit notes.
     */
    public function index(Request $request)
    {
        $creditNotes = CreditNote::query()
            ->with(['client', 'invoice'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('number', 'like', "%{$search}%")
                      ->orWhereHas('client', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.credit-notes.index', compact('creditNotes'));
    }

    /**
     * Show the form for creating a new credit note.
     */
    public function create(Request $request)
    {
        $clients = Client::orderBy('name')->get();
        $invoices = Invoice::whereIn('status', ['sent', 'paid', 'overdue', 'viewed'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Pré-remplir si créé depuis une facture
        $selectedInvoice = null;
        if ($request->invoice_id) {
            $selectedInvoice = Invoice::with('items')->find($request->invoice_id);
        }

        return view('dashboard.credit-notes.create', compact('clients', 'invoices', 'selectedInvoice'));
    }

    /**
     * Store a newly created credit note.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'reason' => 'required|string|in:' . implode(',', array_keys(CreditNote::REASONS)),
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'reason.required' => 'Veuillez indiquer le motif.',
            'items.required' => 'Ajoutez au moins une ligne.',
            'items.min' => 'Ajoutez au moins une ligne.',
        ]);

        $items = collect($validated['items'])->map(function ($item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            return $item;
        })->toArray();

        $subtotal = collect($items)->sum('total');
        $tax = 0; // Les avoirs sont généralement hors taxe ou le tax est inclus
        $total = $subtotal + $tax;

        // Générer le numéro
        $lastNumber = CreditNote::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->value('number');
        $nextSeq = 1;
        if ($lastNumber && preg_match('/(\d+)$/', $lastNumber, $m)) {
            $nextSeq = (int)$m[1] + 1;
        }
        $number = 'AV-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);

        $creditNote = CreditNote::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'client_id' => $validated['client_id'],
            'invoice_id' => $validated['invoice_id'] ?? null,
            'number' => $number,
            'status' => 'draft',
            'reason' => $validated['reason'],
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'currency' => 'XOF',
            'issued_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('client.credit-notes.show', $creditNote)
            ->with('success', 'Avoir créé avec succès.');
    }

    /**
     * Display the specified credit note.
     */
    public function show(CreditNote $creditNote)
    {
        $this->authorize('view', $creditNote);

        $creditNote->load(['client', 'invoice', 'user']);

        return view('dashboard.credit-notes.show', compact('creditNote'));
    }

    /**
     * Show the form for editing the specified credit note.
     */
    public function edit(CreditNote $creditNote)
    {
        $this->authorize('update', $creditNote);

        $clients = Client::orderBy('name')->get();
        $invoices = Invoice::whereIn('status', ['sent', 'paid', 'overdue', 'viewed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.credit-notes.edit', compact('creditNote', 'clients', 'invoices'));
    }

    /**
     * Update the specified credit note.
     */
    public function update(Request $request, CreditNote $creditNote)
    {
        $this->authorize('update', $creditNote);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'reason' => 'required|string|in:' . implode(',', array_keys(CreditNote::REASONS)),
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $items = collect($validated['items'])->map(function ($item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            return $item;
        })->toArray();

        $subtotal = collect($items)->sum('total');
        $total = $subtotal;

        $creditNote->update([
            'client_id' => $validated['client_id'],
            'invoice_id' => $validated['invoice_id'] ?? null,
            'reason' => $validated['reason'],
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $total,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('client.credit-notes.show', $creditNote)
            ->with('success', 'Avoir mis à jour avec succès.');
    }

    /**
     * Change the status of the credit note.
     */
    public function changeStatus(Request $request, CreditNote $creditNote)
    {
        $this->authorize('view', $creditNote);

        $validated = $request->validate([
            'status' => 'required|in:draft,issued,applied,cancelled',
        ]);

        $creditNote->update(['status' => $validated['status']]);

        $statusLabels = CreditNote::STATUSES;

        return redirect()
            ->route('client.credit-notes.show', $creditNote)
            ->with('success', "Statut modifié en \"{$statusLabels[$validated['status']]}\".");
    }

    /**
     * Remove the specified credit note.
     */
    public function destroy(CreditNote $creditNote)
    {
        $this->authorize('delete', $creditNote);

        $creditNote->delete();

        return redirect()
            ->route('client.credit-notes.index')
            ->with('success', 'Avoir supprimé avec succès.');
    }
}
